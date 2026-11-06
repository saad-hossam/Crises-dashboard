<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ExcelModuleExport;
use App\Http\Controllers\Controller;
use App\Models\ExcelImportBatch;
use App\Models\ExcelModule;
use App\Models\ExcelRecord;
use App\Services\GenericExcelImporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelModuleController extends Controller
{
    public function __construct(
        private readonly GenericExcelImporter $importer
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
        $modules = ExcelModule::withCount(['records', 'batches'])->orderBy('name_ar')->get();
        return view('admin.excel-modules.index', compact('modules'));
    }

    /**
     * Show upload form
     */
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
            ->route('admin.excel-modules.show', $batch->excel_module_id)
            ->with('success', "تم استيراد {$batch->imported_rows} صف بنجاح.");
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
