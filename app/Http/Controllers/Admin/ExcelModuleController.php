<?php

namespace App\Http\Controllers\Admin;

use App\Models\ExcelModule;
use App\Models\ExcelRecord;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ExcelImportBatch;
use App\Models\ModuleImportBatch;
use App\Exports\ExcelModuleExport;
use App\Http\Controllers\Controller;
use App\Services\TypedExcelImporter;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExcelModuleController extends Controller
{
    public function __construct(
        private readonly TypedExcelImporter $importer
    ) {}

    /**
     * Dashboard إحصائي بسيط
     */
    public function dashboard()
    {
        $modules = ExcelModule::withCount('records')
            ->orderByDesc('last_imported_at')
            ->get();

        $totalRecords = ExcelRecord::count();
        $totalModules = $modules->count();
        $lastImport   = ExcelImportBatch::where('status', 'completed')
            ->latest('created_at')
            ->first();

        $recentBatches = ExcelImportBatch::with('module')
            ->where('status', 'completed')
            ->latest('created_at')
            ->limit(8)
            ->get();

        // صفوف لكل موديول للرسم
        $chartLabels = $modules->pluck('name_ar')->toArray();
        $chartData   = $modules->pluck('records_count')->toArray();

        return view('admin.excel-modules.dashboard', compact(
            'modules',
            'totalRecords',
            'totalModules',
            'lastImport',
            'recentBatches',
            'chartLabels',
            'chartData'
        ));
    }

    /**
     * List all modules
     */
public function index()
    {
        $registry = config('excel_modules', []);
        $modules = [];
        foreach ($registry as $code => $cfg) {
            $model = $cfg['model'];
            $count = class_exists($model) ? $model::count() : 0;
            $lastBatch = ModuleImportBatch::where('module_code', $code)->latest()->first();
            $modules[] = [
                'code' => $code,
                'name_ar' => $cfg['name_ar'],
                'table' => $cfg['table'],
                'records_count' => $count,
                'last_imported_at' => $lastBatch?->created_at,
            ];
        }
        return view('admin.excel-modules.index', compact('modules'));
    }

    /**
     * Show upload form
     */

    /**
     * عرض بيانات جدول موديول مخصص (production_lines, companies, ...)
     */
  public function showTable(string $moduleCode, Request $request)
{
    $config = config('excel_modules.' . $moduleCode);
    if (!$config) {
        abort(404, 'موديول غير معروف: ' . $moduleCode);
    }

    $modelClass = $config['model'] ?? null;
    if (!$modelClass || !class_exists($modelClass)) {
        abort(404, 'موديول غير مهيأ: ' . $moduleCode);
    }

    $query = $modelClass::query();

    // بحث عام في كل الأعمدة
    if ($q = trim((string) $request->get('q'))) {
        $query->where(function ($qq) use ($q, $config) {
            foreach (array_values($config['columns']) as $col) {
                $qq->orWhere($col, 'like', '%' . $q . '%');
            }
        });
    }

    // فلترة باسم الوحدة
    if ($unitName = trim((string) $request->get('unit_name'))) {
        $query->where('unit_name', 'like', '%' . $unitName . '%');
    }

    $perPage = (int) $request->get('per_page', 25);
    $perPage = in_array($perPage, [25, 50, 100, 200], true) ? $perPage : 25;

    // ترتيب تصاعدي
    $records = $query->orderBy('id')->paginate($perPage)->withQueryString();

    $columns = array_values(array_unique(array_values($config['columns'])));

    // أعمدة العرض الأساسية أولاً
    $priority = [ 'unit_name', 'name', 'name_ar', 'site_name', 'warehouse_name',
        'facility_name', 'product_name', 'governorate', 'operating_status', 'owner_organization'];

    usort($columns, function ($a, $b) use ($priority) {
        $ia = array_search($a, $priority, true);
        $ib = array_search($b, $priority, true);
        $ia = $ia === false ? 99 : $ia;
        $ib = $ib === false ? 99 : $ib;
        return $ia <=> $ib;
    });

    $batches = ModuleImportBatch::where('module_code', $moduleCode)
        ->latest()->limit(5)->get();

    // أسماء الوحدات المتاحة لاقتراحات Datalist
    $unitNames = $modelClass::query()
        ->whereNotNull('unit_name')
        ->where('unit_name', '!=', '')
        ->distinct()
        ->orderBy('unit_name')
        ->pluck('unit_name');

    // 4 Charts ديناميكية حسب config الموديول
    $totalCount = $modelClass::count();
    $withCoords = 0;
    $uniqueUnits = 0;
    $moduleCharts = [];

    try {
        $tableName = (new $modelClass)->getTable();
        $group = function (string $col) use ($modelClass, $tableName) {
            if (!\Illuminate\Support\Facades\Schema::hasColumn($tableName, $col)) {
                return ['labels' => [], 'data' => []];
            }
            $rows = $modelClass::selectRaw($col.' as k, count(*) as c')
                ->whereNotNull($col)->where($col, '!=', '')
                ->groupBy($col)->orderByDesc('c')->limit(10)->pluck('c', 'k');
            return [
                'labels' => array_map('strval', array_values($rows->keys()->all())),
                'data'   => array_map('intval', array_values($rows->all())),
            ];
        };

        $chartDefs = $config['charts'] ?? [
            ['field' => 'operating_status', 'type' => 'doughnut', 'title' => 'حالة التشغيل'],
            ['field' => 'governorate', 'type' => 'bar', 'title' => 'المحافظة'],
            ['field' => 'readiness_level', 'type' => 'doughnut', 'title' => 'الجاهزية'],
            ['field' => 'owner_organization', 'type' => 'bar', 'title' => 'الجهة المالكة'],
        ];

        foreach (array_slice($chartDefs, 0, 4) as $i => $def) {
            $field = $def['field'] ?? '';
            $payload = $group($field);
            $moduleCharts[] = [
                'id'     => 'modChart'.$i,
                'type'   => $def['type'] ?? 'bar',
                'title'  => $def['title'] ?? $field,
                'field'  => $field,
                'labels' => $payload['labels'],
                'data'   => $payload['data'],
            ];
        }

        foreach (['unit_name', 'owner_organization', 'company_name'] as $c) {
            if (\Illuminate\Support\Facades\Schema::hasColumn($tableName, $c)) {
                $uniqueUnits = (int) $modelClass::whereNotNull($c)->where($c, '!=', '')->distinct()->count($c);
                break;
            }
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn($tableName, 'x_coordinate')) {
            $withCoords = $modelClass::whereNotNull('x_coordinate')->whereNotNull('y_coordinate')->count();
        }
    } catch (\Throwable $e) {
        // fallback empty charts
        if (empty($moduleCharts)) {
            for ($i = 0; $i < 4; $i++) {
                $moduleCharts[] = [
                    'id' => 'modChart'.$i, 'type' => 'bar', 'title' => '—',
                    'field' => '', 'labels' => [], 'data' => [],
                ];
            }
        }
    }

    return view('admin.excel-modules.table', [
        'moduleCode'   => $moduleCode,
        'config'       => $config,
        'records'      => $records,
        'columns'      => $columns,
        'batches'      => $batches,
        'unitNames'    => $unitNames,
        'moduleCharts' => $moduleCharts,
        'totalCount'   => $totalCount,
        'withCoords'   => $withCoords,
        'uniqueUnits'  => $uniqueUnits,
    ]);
}

    /**
     * تفاصيل صف واحد من جدول مخصص
     */
    public function showTableRecord(string $moduleCode, int $id)
    {
        $config = config('excel_modules.' . $moduleCode);
        if (!$config) {
            abort(404);
        }
        $modelClass = $config['model'];
        $record = $modelClass::findOrFail($id);
        $columns = array_values(array_unique(array_values($config['columns'])));

        return view('admin.excel-modules.table-record', [
            'moduleCode' => $moduleCode,
            'config'     => $config,
            'record'     => $record,
            'columns'    => $columns,
        ]);
    }

    public function create()
    {
        return view('admin.excel-modules.create');
    }

    /**
     * Step 1: Upload → Preview
     * POST: استلام الملف
     * GET: قراءة المعاينة من الملف المخزّن (مش من الـ session عشان حجم البيانات)
     *
     * مهم: ما نخزنش preview_rows في الـ session — تسبب max_allowed_packet
     */
    public function preview(Request $request)
    {
        // GET → أعد بناء المعاينة من الملف على القرص
        if ($request->isMethod('get')) {
            $path = session('excel_stored_path');
            $originalName = session('excel_original_name');

            if (!$path || !Storage::disk('local')->exists($path)) {
                return redirect()
                    ->route('admin.excel-modules.create')
                    ->withErrors(['excel_file' => 'لا توجد معاينة حالية. ارفع الملف من جديد.']);
            }

            try {
                $preview = $this->importer->preview(Storage::disk('local')->path($path));
            } catch (\Throwable $e) {
                return redirect()
                    ->route('admin.excel-modules.create')
                    ->withErrors(['excel_file' => $e->getMessage()]);
            }

            return view('admin.excel-modules.preview', [
                'preview' => $preview,
                'originalName' => $originalName ?? 'file.xlsx',
            ]);
        }

        // POST → رفع الملف فقط + تخزين المسار في الـ session (خفيف)
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:51200',
        ]);

        $file = $request->file('excel_file');
        $filename = Str::uuid() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('excel-uploads', $filename, 'local');

        // تحقق سريع إن الملف صالح قبل الـ redirect
        try {
            $this->importer->preview(Storage::disk('local')->path($path));
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($path);
            return back()->withErrors(['excel_file' => $e->getMessage()]);
        }

        // نخزن المسار والاسم فقط — بدون بيانات الصفوف
        session([
            'excel_stored_path' => $path,
            'excel_original_name' => $file->getClientOriginalName(),
        ]);
        // امسح أي preview قديم ضخم من الـ session إن وجد
        session()->forget('excel_preview');

        return redirect()->route('admin.excel-modules.preview');
    }

    /**
     * Step 2: Confirm import
     */
    public function import(Request $request)
    {
        $path = session('excel_stored_path');
        $originalName = session('excel_original_name');

        if (!$path || !Storage::disk('local')->exists($path)) {
            return redirect()->route('admin.excel-modules.create')
                ->withErrors(['error' => 'انتهت صلاحية المعاينة، ارفع الملف مرة أخرى.']);
        }

        $replace = $request->boolean('replace_existing', true);

        try {
            $batch = $this->importer->import(
                storedPath: $path,
                originalFilename: $originalName ?? 'upload.xlsx',
                userId: auth()->id() ?? 1,
                replaceExisting: $replace
            );
        } catch (\Throwable $e) {
            // لا نخزن رسالة SQL كاملة في الـ session (بتكسر max_allowed_packet)
            $msg = $e->getMessage();
            if (strlen($msg) > 500) {
                $msg = substr($msg, 0, 500) . '…';
            }
            return back()->withErrors(['error' => $msg]);
        }

        session()->forget(['excel_preview', 'excel_stored_path', 'excel_original_name']);

        return redirect()
            ->route('admin.excel-modules.table', $batch->module_code)
            ->with('success', "تم استيراد {$batch->imported_rows} صف إلى جدول {$batch->module_code} بنجاح.");
    }

    /**
     * View records of a module (dynamic table + advanced filters)
     */
    public function show(ExcelModule $module, Request $request)
    {
        $query = ExcelRecord::where('excel_module_id', $module->id);

        if ($search = trim((string) $request->get('q'))) {
            $query->where('data', 'like', '%' . $search . '%');
        }
        if ($unit = $request->get('unit_code')) {
            $query->where('unit_code', $unit);
        }
        $filterColumn = $request->get('filter_column');
        $filterValue  = trim((string) $request->get('filter_value'));
        if ($filterColumn && $filterValue !== '') {
            $query->where("data->{$filterColumn}", 'like', '%' . $filterValue . '%');
        }

        $perPage = (int) $request->get('per_page', 50);
        $perPage = in_array($perPage, [25, 50, 100, 200], true) ? $perPage : 50;
        $records = $query->latest('id')->paginate($perPage)->withQueryString();

        $sample = ExcelRecord::where('excel_module_id', $module->id)
            ->limit(30)
            ->get()
            ->pluck('data');

        $headers = collect($sample)
            ->flatMap(fn ($d) => array_keys($d ?? []))
            ->unique()
            ->values()
            ->all();

        if (!empty($module->last_headers)) {
            $normalized = collect($module->last_headers)
                ->map(fn ($h) => preg_replace('/\s+/', '_', trim((string) $h)))
                ->filter()
                ->values()
                ->all();

            $headers = array_values(array_unique(array_merge(
                array_intersect($normalized, $headers),
                $headers
            )));
        }

        $module->loadCount('records');

        return view('admin.excel-modules.show', compact(
            'module', 'records', 'headers'
        ));
    }

    /**
     * تصدير XLSX حقيقي (Maatwebsite) مع fallback لـ CSV
     */
    public function export(ExcelModule $module, Request $request): BinaryFileResponse|StreamedResponse
    {
        $filename = $module->code . '_' . now()->format('Ymd_His') . '.xlsx';

        // لو Maatwebsite/Excel مثبت استخدمه، وإلا CSV
        if (class_exists(\Maatwebsite\Excel\Excel::class)) {
            return Excel::download(
                new ExcelModuleExport($module, $request),
                $filename
            );
        }

        // Fallback CSV
        return $this->exportAsCsv($module, $request);
    }

    private function exportAsCsv(ExcelModule $module, Request $request): StreamedResponse
    {
        $query = ExcelRecord::where('excel_module_id', $module->id);

        if ($search = trim((string) $request->get('q'))) {
            $query->where('data', 'like', '%' . $search . '%');
        }
        if ($unit = $request->get('unit_code')) {
            $query->where('unit_code', $unit);
        }
        $filterColumn = $request->get('filter_column');
        $filterValue  = trim((string) $request->get('filter_value'));
        if ($filterColumn && $filterValue !== '') {
            $query->where("data->{$filterColumn}", 'like', '%' . $filterValue . '%');
        }

        $records = $query->orderBy('id')->get();

        $allKeys = [];
        foreach ($records as $r) {
            foreach (array_keys($r->data ?? []) as $k) {
                $allKeys[$k] = true;
            }
        }
        $headers = array_keys($allKeys);

        if (!empty($module->last_headers)) {
            $normalized = collect($module->last_headers)
                ->map(fn ($h) => preg_replace('/\s+/', '_', trim((string) $h)))
                ->filter()
                ->values()
                ->all();
            $headers = array_values(array_unique(array_merge(
                array_intersect($normalized, $headers),
                $headers
            )));
        }

        $filename = $module->code . '_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($records, $headers) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, array_map(fn ($h) => str_replace('_', ' ', $h), $headers));
            foreach ($records as $record) {
                $row = [];
                foreach ($headers as $h) {
                    $val = $record->data[$h] ?? '';
                    $row[] = is_array($val) ? json_encode($val, JSON_UNESCAPED_UNICODE) : $val;
                }
                fputcsv($out, $row);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }


    /**
     * تفاصيل سجل واحد — كل الحقول من JSON
     */
    public function showRecord(ExcelModule $module, ExcelRecord $record)
    {
        if ((int) $record->excel_module_id !== (int) $module->id) {
            abort(404);
        }

        $data = is_array($record->data) ? $record->data : [];

        $groups = [
            'أساسي' => [],
            'موقع / إحداثيات' => [],
            'سعات / أرقام' => [],
            'حالة / تواريخ' => [],
            'أخرى' => [],
        ];

        $coordHints = ['x', 'y', 'إحداث', 'coord', 'خط_العرض', 'خط_الطول', 'latitude', 'longitude'];
        $numHints = ['سعة', 'قدرة', 'عدد', 'نسبة', 'كمية', 'إنتاج', 'مساحة', 'capacity', 'rate', 'count', 'percent'];
        $statusHints = ['حالة', 'تاريخ', 'status', 'date', 'سنة', 'year'];
        $basicHints = ['كود', 'اسم', 'جهة', 'محافظة', 'شركة', 'وحدة', 'owner', 'name', 'code', 'company'];

        foreach ($data as $key => $value) {
            $k = (string) $key;
            $kl = mb_strtolower($k);
            $bucket = 'أخرى';

            foreach ($coordHints as $h) {
                if (str_contains($kl, mb_strtolower($h)) || $k === 'X' || $k === 'Y') {
                    $bucket = 'موقع / إحداثيات';
                    break;
                }
            }
            if ($bucket === 'أخرى') {
                foreach ($basicHints as $h) {
                    if (str_contains($kl, mb_strtolower($h))) {
                        $bucket = 'أساسي';
                        break;
                    }
                }
            }
            if ($bucket === 'أخرى') {
                foreach ($numHints as $h) {
                    if (str_contains($kl, mb_strtolower($h))) {
                        $bucket = 'سعات / أرقام';
                        break;
                    }
                }
            }
            if ($bucket === 'أخرى') {
                foreach ($statusHints as $h) {
                    if (str_contains($kl, mb_strtolower($h))) {
                        $bucket = 'حالة / تواريخ';
                        break;
                    }
                }
            }

            $groups[$bucket][$k] = $value;
        }

        $groups = array_filter($groups, fn ($g) => count($g) > 0);

        return view('admin.excel-modules.record', compact('module', 'record', 'data', 'groups'));
    }

    /**
     * Delete all data of a module
     */
    public function destroy(ExcelModule $module)
    {
        $module->records()->delete();
        $module->batches()->delete();
        $module->delete();

        return redirect()->route('admin.excel-modules.index')
            ->with('success', 'تم حذف الموديول وكل بياناته.');
    }
}
