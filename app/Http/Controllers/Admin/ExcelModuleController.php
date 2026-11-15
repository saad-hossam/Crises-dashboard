<?php

namespace App\Http\Controllers\Admin;

use App\Models\ExcelModule;
use App\Models\ExcelRecord;
use App\Models\ExcelImportBatch;
use App\Models\ModuleImportBatch;
use App\Exports\ExcelModuleExport;
use App\Http\Controllers\Controller;
use App\Services\TypedExcelImporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelModuleController extends Controller
{
    public function __construct(
        private readonly TypedExcelImporter $importer
    ) {}

    /* ---------- Dashboard ---------- */
    public function dashboard()
    {
        $modules = ExcelModule::withCount('records')
            ->orderByDesc('last_imported_at')->get();
        $totalRecords = ExcelRecord::count();
        $totalModules = $modules->count();
        $lastImport   = ExcelImportBatch::where('status','completed')->latest()->first();
        $recentBatches= ExcelImportBatch::with('module')
            ->where('status','completed')->latest()->limit(8)->get();
        $chartLabels = $modules->pluck('name_ar')->toArray();
        $chartData   = $modules->pluck('records_count')->toArray();

        return view('admin.excel-modules.dashboard', compact(
            'modules','totalRecords','totalModules','lastImport',
            'recentBatches','chartLabels','chartData'
        ));
    }

    /* ---------- Index ---------- */
    public function index()
    {
        $registry = config('excel_modules', []);
        $modules  = [];

        foreach ($registry as $code => $cfg) {
            $model = $cfg['model'] ?? null;
            $count = ($model && class_exists($model)) ? $model::count() : 0;
            $lastBatch = ModuleImportBatch::where('module_code', $code)->latest()->first();

            $modules[] = [
                'code'             => $code,
                'name_ar'          => $cfg['name_ar'] ?? $code,
                'table'            => $cfg['table'] ?? '',
                'records_count'    => $count,
                'last_imported_at' => $lastBatch?->created_at,
            ];
        }
        return view('admin.excel-modules.index', compact('modules'));
    }

    /* ---------- Upload form ---------- */
    public function create()
    {
        return view('admin.excel-modules.create');
    }

    /* ---------- Preview POST ---------- */
    public function preview(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:102400',
        ], [
            'excel_file.required' => 'يرجى اختيار ملف Excel.',
            'excel_file.file'     => 'الملف المرفوع غير صالح.',
            'excel_file.uploaded' => 'فشل رفع الملف. غالباً حجمه أكبر من حد PHP (upload_max_filesize / post_max_size). ارفع الحد إلى 64M على الأقل أو قسّم الملف.',
            'excel_file.mimes'    => 'يجب أن يكون الملف بصيغة xlsx أو xls.',
            'excel_file.max'      => 'حجم الملف أكبر من المسموح (100 ميجابايت).',
        ]);

        $file = $request->file('excel_file');
        if (!$file || !$file->isValid()) {
            $err = $file ? $file->getErrorMessage() : 'لا يوجد ملف';
            return back()->withErrors([
                'excel_file' => 'فشل استلام الملف على الخادم: ' . $err
                    . ' — راجع upload_max_filesize و post_max_size في php.ini (يُفضّل 64M أو أكثر).',
            ]);
        }

        $filename = Str::uuid() . '_' . $file->getClientOriginalName();
        $path     = $file->storeAs('excel-uploads', $filename, 'local');

        try {
            $previewData = $this->importer->preview(Storage::disk('local')->path($path));
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($path);
            return back()->withErrors([
                'excel_file' => 'الملف غير صالح: ' . $e->getMessage(),
            ]);
        }

        $originalName = $file->getClientOriginalName();
        $ticket = (string) Str::uuid();

        // تذكرة استيراد مستقلة عن انقطاع الجلسة (cache + session)
        $payload = [
            'path'          => $path,
            'original_name' => $originalName,
            'user_id'       => auth()->id(),
            'created_at'    => now()->toIso8601String(),
        ];
        Cache::put('excel_import_ticket_' . $ticket, $payload, now()->addHours(2));
        Cache::put('excel_preview_' . md5($path), $previewData, now()->addHours(2));

        // payload مشفّر يُمرَّر مع الفورم — لا يعتمد على الجلسة ولا على الـ Cache
        $encryptedPayload = Crypt::encryptString(json_encode([
            'path' => $path,
            'original_name' => $originalName,
            'user_id' => auth()->id(),
            'ticket' => $ticket,
        ], JSON_UNESCAPED_UNICODE));

        session([
            'excel_stored_path'   => $path,
            'excel_original_name' => $originalName,
            'excel_import_ticket' => $ticket,
            'excel_import_payload'=> $encryptedPayload,
        ]);
        session()->save();

        // redirect نسبي عبر intended host الحالي (تجنب APP_URL الخاطئ)
        return redirect('/admin/excel-modules/preview?ticket=' . urlencode($ticket));
    }

    /* ---------- Preview GET (from cache / ticket) ---------- */
    public function previewShow(Request $request)
    {
        $ticket = $request->query('ticket') ?: session('excel_import_ticket');
        $path = null;
        $originalName = null;

        if ($ticket) {
            $payload = Cache::get('excel_import_ticket_' . $ticket);
            if (is_array($payload)) {
                $path = $payload['path'] ?? null;
                $originalName = $payload['original_name'] ?? null;
            }
        }

        if (!$path) {
            $path = session('excel_stored_path');
            $originalName = session('excel_original_name');
        }

        if (!$path || !Storage::disk('local')->exists($path)) {
            return redirect()->route('admin.excel-modules.create')
                ->withErrors(['excel_file' => 'لا توجد معاينة حالية. ارفع الملف من جديد.']);
        }

        // ثبّت الجلسة من التذكرة
        session([
            'excel_stored_path'   => $path,
            'excel_original_name' => $originalName,
            'excel_import_ticket' => $ticket,
        ]);

        $preview = Cache::get('excel_preview_' . md5($path));

        if (!$preview) {
            try {
                $preview = $this->importer->preview(Storage::disk('local')->path($path));
                Cache::put('excel_preview_' . md5($path), $preview, now()->addHours(2));
            } catch (\Throwable $e) {
                return redirect()->route('admin.excel-modules.create')
                    ->withErrors(['excel_file' => $e->getMessage()]);
            }
        }

        return view('admin.excel-modules.preview', [
            'preview'          => $preview,
            'originalName'     => $originalName ?? 'file.xlsx',
            'importTicket'     => $ticket,
            'importPayload'    => session('excel_import_payload'),
        ]);
    }

    /* ---------- Import (SYNCHRONOUS — no queue) ---------- */
    public function import(Request $request)
    {
        $ticket = $request->input('import_ticket') ?: session('excel_import_ticket');
        $path = null;
        $originalName = null;
        $payload = null;

        // 1) payload مشفّر من الفورم (الأقوى ضد ضياع الجلسة)
        $enc = $request->input('import_payload') ?: session('excel_import_payload');
        if ($enc) {
            try {
                $decoded = json_decode(Crypt::decryptString($enc), true);
                if (is_array($decoded)) {
                    $path = $decoded['path'] ?? null;
                    $originalName = $decoded['original_name'] ?? null;
                    $ticket = $ticket ?: ($decoded['ticket'] ?? null);
                    $payload = $decoded;
                }
            } catch (\Throwable $e) {
                // ignore and fall through
            }
        }

        // 2) تذكرة Cache
        if ((!$path) && $ticket) {
            $cached = Cache::get('excel_import_ticket_' . $ticket);
            if (is_array($cached)) {
                $payload = $cached;
                $path = $cached['path'] ?? null;
                $originalName = $cached['original_name'] ?? null;
            }
        }

        // 3) session
        if (!$path) {
            $path = session('excel_stored_path');
            $originalName = session('excel_original_name');
        }

        if (!$path || !Storage::disk('local')->exists($path)) {
            return redirect()->route('admin.excel-modules.create')
                ->withErrors(['error' => 'انتهت صلاحية المعاينة، ارفع الملف مرة أخرى.']);
        }

        $replace = $request->boolean('replace_existing', true);

        @set_time_limit(0);
        @ini_set('memory_limit', '1024M');

        // حافظ على هوية المستخدم قبل عمليات طويلة
        $userId = auth()->id() ?? ($payload['user_id'] ?? 1);

        try {
            $batch = $this->importer->import(
                storedPath:       $path,
                originalFilename: $originalName ?? 'upload.xlsx',
                userId:           (int) $userId,
                replaceExisting:  $replace
            );
        } catch (\Throwable $e) {
            $msg = $e->getMessage();
            if (strlen($msg) > 500) $msg = substr($msg, 0, 500) . '…';
            return redirect()
                ->route('admin.excel-modules.create')
                ->withErrors(['error' => $msg]);
        }

        session()->forget(['excel_stored_path', 'excel_original_name', 'excel_import_ticket']);
        Cache::forget('excel_preview_' . md5($path));
        if ($ticket) {
            Cache::forget('excel_import_ticket_' . $ticket);
        }

        $code = $batch->module_code ?? null;
        if (!$code || !config('excel_modules.' . $code)) {
            return redirect()
                ->route('admin.excel-modules.index')
                ->with('success', 'تم الاستيراد بنجاح (' . ($batch->imported_rows ?? 0) . ' صف).');
        }

        // رابط نسبي يحافظ على نفس الـ host/port (127.0.0.1:8000 vs localhost)
        return redirect('/admin/excel-modules/table/' . $code)
            ->with('success', "تم استيراد {$batch->imported_rows} صف إلى جدول {$code} بنجاح.");
    }

    /* ---------- Custom table ---------- */
    public function showTable(string $moduleCode, Request $request)
    {
        $config = config('excel_modules.' . $moduleCode);
        if (!$config) abort(404, 'موديول غير معروف');

        $modelClass = $config['model'] ?? null;
        if (!$modelClass || !class_exists($modelClass)) abort(404, 'موديول غير مهيأ');

        $query = $modelClass::query();

        if ($q = trim((string)$request->get('q'))) {
            $query->where(function ($qq) use ($q, $config) {
                foreach (array_values($config['columns']) as $col) {
                    $qq->orWhere($col, 'like', '%' . $q . '%');
                }
            });
        }
        if ($unitName = trim((string)$request->get('unit_name'))) {
            $query->where('unit_name', 'like', '%' . $unitName . '%');
        }

        $perPage = (int)$request->get('per_page', 25);
        $perPage = in_array($perPage, [25,50,100,200], true) ? $perPage : 25;
        $records = $query->orderBy('id')->paginate($perPage)->withQueryString();

        $columns  = array_values(array_unique(array_values($config['columns'])));
        $priority = ['unit_name','name','name_ar','site_name','warehouse_name',
                     'facility_name','product_name','governorate','operating_status','owner_organization'];
        usort($columns, function ($a,$b) use ($priority) {
            $ia = array_search($a,$priority,true); $ia = $ia===false?99:$ia;
            $ib = array_search($b,$priority,true); $ib = $ib===false?99:$ib;
            return $ia <=> $ib;
        });

        $batches = ModuleImportBatch::where('module_code', $moduleCode)->latest()->limit(5)->get();

        $unitNames = $modelClass::query()
            ->whereNotNull('unit_name')->where('unit_name','!=','')
            ->distinct()->orderBy('unit_name')->pluck('unit_name');

        $totalCount = $modelClass::count();
        $withCoords = 0; $uniqueUnits = 0;
        $moduleCharts = [];

        try {
            $tableName = (new $modelClass)->getTable();
            $group = function (string $col) use ($modelClass, $tableName) {
                if (!\Illuminate\Support\Facades\Schema::hasColumn($tableName, $col)) {
                    return ['labels'=>[], 'data'=>[]];
                }
                $rows = $modelClass::selectRaw($col.' as k, count(*) as c')
                    ->whereNotNull($col)->where($col,'!=','')
                    ->groupBy($col)->orderByDesc('c')->limit(10)->pluck('c','k');
                return [
                    'labels' => array_map('strval', array_values($rows->keys()->all())),
                    'data'   => array_map('intval', array_values($rows->all())),
                ];
            };

            $chartDefs = $config['charts'] ?? [
                ['field'=>'operating_status','type'=>'doughnut','title'=>'حالة التشغيل'],
                ['field'=>'governorate','type'=>'bar','title'=>'المحافظة'],
                ['field'=>'readiness_level','type'=>'doughnut','title'=>'الجاهزية'],
                ['field'=>'owner_organization','type'=>'bar','title'=>'الجهة المالكة'],
            ];

            foreach (array_slice($chartDefs, 0, 4) as $i => $def) {
                $field = $def['field'] ?? '';
                $payload = $group($field);
                $moduleCharts[] = [
                    'id'    => 'modChart'.$i,
                    'type'  => $def['type'] ?? 'bar',
                    'title' => $def['title'] ?? $field,
                    'field' => $field,
                    'labels'=> $payload['labels'],
                    'data'  => $payload['data'],
                ];
            }

            foreach (['unit_name','owner_organization','company_name'] as $c) {
                if (\Illuminate\Support\Facades\Schema::hasColumn($tableName, $c)) {
                    $uniqueUnits = (int)$modelClass::whereNotNull($c)->where($c,'!=','')->distinct()->count($c);
                    break;
                }
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn($tableName,'x_coordinate')) {
                $withCoords = $modelClass::whereNotNull('x_coordinate')->whereNotNull('y_coordinate')->count();
            }
        } catch (\Throwable $e) {
            if (empty($moduleCharts)) {
                for ($i=0; $i<4; $i++) {
                    $moduleCharts[] = [
                        'id'=>'modChart'.$i,'type'=>'bar','title'=>'—',
                        'field'=>'','labels'=>[],'data'=>[],
                    ];
                }
            }
        }

        return view('admin.excel-modules.table', compact(
            'moduleCode','config','records','columns','batches',
            'unitNames','moduleCharts','totalCount','withCoords','uniqueUnits'
        ));
    }

    public function showTableRecord(string $moduleCode, int $id)
    {
        $config = config('excel_modules.' . $moduleCode);
        if (!$config) abort(404);
        $modelClass = $config['model'];
        $record = $modelClass::findOrFail($id);
        $columns = array_values(array_unique(array_values($config['columns'])));
        return view('admin.excel-modules.table-record', compact('moduleCode','config','record','columns'));
    }

    public function show(ExcelModule $module, Request $request)
    {
        $query = ExcelRecord::where('excel_module_id', $module->id);
        if ($search = trim((string)$request->get('q'))) $query->where('data','like','%'.$search.'%');
        if ($unit = $request->get('unit_code')) $query->where('unit_code', $unit);
        $fc = $request->get('filter_column'); $fv = trim((string)$request->get('filter_value'));
        if ($fc && $fv !== '') $query->where("data->{$fc}", 'like', '%'.$fv.'%');

        $perPage = (int)$request->get('per_page', 50);
        $perPage = in_array($perPage,[25,50,100,200],true) ? $perPage : 50;
        $records = $query->latest('id')->paginate($perPage)->withQueryString();

        $sample = ExcelRecord::where('excel_module_id', $module->id)->limit(30)->get()->pluck('data');
        $headers = collect($sample)->flatMap(fn($d) => array_keys($d ?? []))->unique()->values()->all();

        if (!empty($module->last_headers)) {
            $normalized = collect($module->last_headers)
                ->map(fn($h) => preg_replace('/\s+/','_',trim((string)$h)))
                ->filter()->values()->all();
            $headers = array_values(array_unique(array_merge(
                array_intersect($normalized, $headers), $headers
            )));
        }
        $module->loadCount('records');
        return view('admin.excel-modules.show', compact('module','records','headers'));
    }

    public function export(ExcelModule $module, Request $request): BinaryFileResponse|StreamedResponse
    {
        $filename = $module->code . '_' . now()->format('Ymd_His') . '.xlsx';
        if (class_exists(\Maatwebsite\Excel\Excel::class)) {
            return Excel::download(new ExcelModuleExport($module, $request), $filename);
        }
        return $this->exportAsCsv($module, $request);
    }

    private function exportAsCsv(ExcelModule $module, Request $request): StreamedResponse
    {
        $query = ExcelRecord::where('excel_module_id', $module->id);
        if ($search = trim((string)$request->get('q'))) $query->where('data','like','%'.$search.'%');
        if ($unit = $request->get('unit_code')) $query->where('unit_code', $unit);
        $fc = $request->get('filter_column'); $fv = trim((string)$request->get('filter_value'));
        if ($fc && $fv !== '') $query->where("data->{$fc}", 'like', '%'.$fv.'%');

        $records = $query->orderBy('id')->get();

        $allKeys = [];
        foreach ($records as $r) {
            foreach (array_keys($r->data ?? []) as $k) $allKeys[$k] = true;
        }
        $headers = array_keys($allKeys);

        if (!empty($module->last_headers)) {
            $normalized = collect($module->last_headers)
                ->map(fn($h) => preg_replace('/\s+/','_',trim((string)$h)))
                ->filter()->values()->all();
            $headers = array_values(array_unique(array_merge(
                array_intersect($normalized, $headers), $headers
            )));
        }

        $filename = $module->code . '_' . now()->format('Ymd_His') . '.csv';
        return response()->streamDownload(function () use ($records, $headers) {
            $out = fopen('php://output','w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, array_map(fn($h) => str_replace('_',' ',$h), $headers));
            foreach ($records as $record) {
                $row = [];
                foreach ($headers as $h) {
                    $val = $record->data[$h] ?? '';
                    $row[] = is_array($val) ? json_encode($val, JSON_UNESCAPED_UNICODE) : $val;
                }
                fputcsv($out, $row);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function showRecord(ExcelModule $module, ExcelRecord $record)
    {
        if ((int)$record->excel_module_id !== (int)$module->id) abort(404);
        $data = is_array($record->data) ? $record->data : [];
        $groups = ['أساسي'=>[], 'موقع / إحداثيات'=>[], 'سعات / أرقام'=>[], 'حالة / تواريخ'=>[], 'أخرى'=>[]];
        $coordHints  = ['x','y','إحداث','coord','latitude','longitude'];
        $numHints    = ['سعة','قدرة','عدد','نسبة','كمية','إنتاج','مساحة','capacity','rate','count','percent'];
        $statusHints = ['حالة','تاريخ','status','date','سنة','year'];
        $basicHints  = ['كود','اسم','جهة','محافظة','شركة','وحدة','owner','name','code','company'];

        foreach ($data as $key => $value) {
            $k = (string)$key; $kl = mb_strtolower($k);
            $bucket = 'أخرى';
            foreach ($coordHints as $h) {
                if (str_contains($kl, mb_strtolower($h)) || $k==='X' || $k==='Y') { $bucket='موقع / إحداثيات'; break; }
            }
            if ($bucket==='أخرى') foreach ($basicHints as $h) {
                if (str_contains($kl, mb_strtolower($h))) { $bucket='أساسي'; break; }
            }
            if ($bucket==='أخرى') foreach ($numHints as $h) {
                if (str_contains($kl, mb_strtolower($h))) { $bucket='سعات / أرقام'; break; }
            }
            if ($bucket==='أخرى') foreach ($statusHints as $h) {
                if (str_contains($kl, mb_strtolower($h))) { $bucket='حالة / تواريخ'; break; }
            }
            $groups[$bucket][$k] = $value;
        }
        $groups = array_filter($groups, fn($g) => count($g) > 0);
        return view('admin.excel-modules.record', compact('module','record','data','groups'));
    }

    public function destroy(ExcelModule $module)
    {
        $module->records()->delete();
        $module->batches()->delete();
        $module->delete();
        return redirect()->route('admin.excel-modules.index')
            ->with('success', 'تم حذف الموديول وكل بياناته.');
    }
}