<?php

namespace App\Services;

use App\Models\ModuleImportBatch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\IOFactory;
use RuntimeException;

/**
 * يستورد ملف Excel إلى الجدول المخصص حسب ModuleCode
 * مثال: PRODUCTION_LINES → production_lines
 */
class TypedExcelImporter
{
    /** اسم الشيت اللي اتحدد تلقائياً لما الملف مفيهوش _System */
    private ?string $inferredSheet = null;

    public function preview(string $absolutePath): array
    {
        $spreadsheet = IOFactory::load($absolutePath);
        [$moduleCode, $moduleNameAr, $templateVersion] = $this->readSystem($spreadsheet);
        $sheet = $this->dataSheet($spreadsheet);
        $resolved = $this->resolveModuleCode($moduleCode, $sheet);
        if ($resolved !== $moduleCode) {
            $moduleCode = $resolved;
            $moduleNameAr = null;
        }
        $config = $this->configFor($moduleCode);

        $headerRowNum = $this->detectHeaderRow($sheet, $config['columns'] ?? []);
        $headers = $this->headerRow($sheet, $headerRowNum);
        $previewIndex = [];
        foreach ($headers as $i => $h) {
            $k = $this->normalizeHeader($h);
            if ($k !== '' && !isset($previewIndex[$k])) {
                $previewIndex[$k] = $i;
            }
        }
        $total = 0;
        $preview = [];

        foreach ($sheet->getRowIterator($headerRowNum + 1) as $row) {
            $cells = [];
            foreach ($row->getCellIterator() as $cell) {
                $cells[] = $this->cellValue($cell);
            }
            // تجاهل الصفوف الفارغة وصفوف تكرار الهيدر
            if ($this->rowEmpty($cells) || $this->looksLikeHeaderRow($cells, $previewIndex)) {
                continue;
            }
            $total++;
            if (count($preview) < 12) {
                $preview[] = $cells;
            }
        }

        return [
            'module_code'      => $moduleCode,
            'module_name_ar'   => $moduleNameAr ?: ($config['name_ar'] ?? $moduleCode),
            'template_version' => $templateVersion,
            'table'            => $config['table'],
            'headers'          => $headers,
            'total_rows'       => $total,
            'preview_rows'     => $preview,
            'mapped_columns'   => array_values($config['columns'] ?? []),
        ];
    }

    public function import(
        string $storedPath,
        string $originalFilename,
        ?int $userId = null,
        bool $replaceExisting = true
    ): ModuleImportBatch {
        $absolute = storage_path('app/' . ltrim($storedPath, '/'));
        if (!is_file($absolute)) {
            // Laravel disk local path
            $absolute = \Storage::disk('local')->path($storedPath);
        }
        if (!is_file($absolute)) {
            throw new RuntimeException('الملف غير موجود: ' . $storedPath);
        }

        $spreadsheet = IOFactory::load($absolute);
        [$moduleCode, $moduleNameAr] = $this->readSystem($spreadsheet);
        $sheet = $this->dataSheet($spreadsheet);
        $resolved = $this->resolveModuleCode($moduleCode, $sheet);
        if ($resolved !== $moduleCode) {
            $moduleCode = $resolved;
            $moduleNameAr = null;
        }
        $config = $this->configFor($moduleCode);
        $table = $config['table'];
        $map = $config['columns'];
        $modelClass = $config['model'];

        $headerRowNum = $this->detectHeaderRow($sheet, $map);
        $headers = $this->headerRow($sheet, $headerRowNum);

        // بناء فهرس: اسم عربي (بعد التطبيع) → index في الصف
        $headerIndex = [];
        foreach ($headers as $i => $h) {
            $key = $this->normalizeHeader($h);
            if ($key !== '' && !isset($headerIndex[$key])) {
                $headerIndex[$key] = $i;
            }
        }

        $fileHash = hash_file('sha256', $absolute);

        $batch = ModuleImportBatch::create([
            'module_code'        => $moduleCode,
            'module_name_ar'     => $moduleNameAr ?: ($config['name_ar'] ?? $moduleCode),
            'original_filename'  => $originalFilename,
            'file_hash'          => $fileHash,
            'status'             => 'processing',
            'user_id'            => $userId,
            'replaced_existing'  => $replaceExisting,
        ]);

        $imported = 0;
        $skipped = 0;
        $total = 0;
        $buffer = [];

        try {
            DB::beginTransaction();

            if ($replaceExisting && Schema::hasTable($table)) {
                // امسح بيانات الموديول القديمة (اختياري)
                DB::table($table)->delete();
            }

            foreach ($sheet->getRowIterator($headerRowNum + 1) as $row) {
                $cells = [];
                foreach ($row->getCellIterator() as $cell) {
                    $cells[] = $this->cellValue($cell);
                }
                if ($this->rowEmpty($cells) || $this->looksLikeHeaderRow($cells, $headerIndex)) {
                    continue;
                }
                $total++;

                $attrs = $this->mapRow($cells, $headerIndex, $map);

                // ربط دفعة الاستيراد الجديدة (بدون كسر FK القديم إن وُجد)
                $attrs['module_import_batch_id'] = $batch->id;
                // import_batch_id فقط لو العمود موجود ومش مربوط إجبارياً بجدول قديم فارغ
                // نتركه null في production_lines لتفادي CONSTRAINT على import_batches
                if ($table !== 'production_lines') {
                    $attrs['import_batch_id'] = $batch->id;
                }

                // تنظيف إحداثيات
                foreach (['x_coordinate', 'y_coordinate'] as $coord) {
                    if (array_key_exists($coord, $attrs)) {
                        $attrs[$coord] = $this->normalizeCoordinate($attrs[$coord]);
                    }
                }

                // تجاهل أعمدة غير موجودة في الجدول
                $attrs = $this->filterExistingColumns($table, $attrs);
                $attrs = $this->fitToColumns($table, $attrs);

                if (empty($attrs)) {
                    $skipped++;
                    continue;
                }

                $buffer[] = $attrs + [
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (count($buffer) >= 200) {
                    DB::table($table)->insert($buffer);
                    $imported += count($buffer);
                    $buffer = [];
                }
            }

            if ($buffer) {
                DB::table($table)->insert($buffer);
                $imported += count($buffer);
            }

            $batch->update([
                'total_rows'     => $total,
                'imported_rows'  => $imported,
                'skipped_rows'   => $skipped,
                'status'         => 'completed',
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $batch->update([
                'status'        => 'failed',
                'error_message' => mb_substr($e->getMessage(), 0, 500),
                'total_rows'    => $total,
                'imported_rows' => $imported,
            ]);
            throw $e;
        }

        return $batch->fresh();
    }

    /**
     * ملف "خطوط الإنتاج المرفقة" الـ ModuleCode بتاعه PRODUCTION_LINES لكنه قالب مختصر
     * (م / اسم الخط / المنتج / الشركة المصنعة / الجهة) المفروض يروح جدول production_scopes.
     * القالب الكامل لخطوط الإنتاج (حوالي 34 عمود) بيفضل على production_lines.
     */
    private function resolveModuleCode(string $moduleCode, $sheet): string
    {
        if ($moduleCode !== 'PRODUCTION_LINES') {
            return $moduleCode;
        }
        $all = config('excel_modules', []);
        if (!isset($all['PRODUCTION_LINES'], $all['PRODUCTION_SCOPES'])) {
            return $moduleCode;
        }

        $matched = 0;
        $rowNum = $this->detectHeaderRow($sheet, $all['PRODUCTION_LINES']['columns'] ?? []);
        $wanted = [];
        foreach (array_keys($all['PRODUCTION_LINES']['columns'] ?? []) as $ar) {
            $wanted[$this->normalizeHeader($ar)] = true;
        }
        foreach ($this->headerRow($sheet, $rowNum) as $h) {
            $k = $this->normalizeHeader($h);
            if ($k !== '' && isset($wanted[$k])) {
                $matched++;
            }
        }

        // القالب الكامل فيه أعمدة كتير؛ المختصر فيه أقل من 10 أعمدة متطابقة
        return $matched < 10 ? 'PRODUCTION_SCOPES' : $moduleCode;
    }

    /**
     * تحديد الموديول من أسماء الأعمدة لما مفيش _System.
     * يرجع [ModuleCode, اسم الشيت] أو null لو مفيش تطابق كافي.
     */
    private function inferModule($spreadsheet): ?array
    {
        $all = config('excel_modules', []);
        $best = null;
        $bestScore = 0;

        foreach ($spreadsheet->getSheetNames() as $name) {
            $n = mb_strtolower($name);
            if (str_contains($n, 'system') || str_contains($n, 'lookup') || str_starts_with($name, '_')) {
                continue;
            }
            $sheet = $spreadsheet->getSheetByName($name);
            $maxRow = min(15, max(1, (int) $sheet->getHighestDataRow()));

            // أسماء الأعمدة المطبّعة لكل صف من أول 15 صف
            $rows = [];
            for ($r = 1; $r <= $maxRow; $r++) {
                $keys = [];
                foreach ($this->headerRow($sheet, $r) as $h) {
                    $k = $this->normalizeHeader($h);
                    if ($k !== '') {
                        $keys[$k] = true;
                    }
                }
                if ($keys) {
                    $rows[] = $keys;
                }
            }

            foreach ($all as $code => $cfg) {
                $wanted = [];
                foreach (array_keys($cfg['columns'] ?? []) as $ar) {
                    $wanted[$this->normalizeHeader($ar)] = true;
                }
                foreach ($rows as $keys) {
                    $score = count(array_intersect_key($keys, $wanted));
                    // لازم 5 أعمدة على الأقل ونص أعمدة الصف يطابق الموديول
                    if ($score >= 5 && $score >= count($keys) * 0.5 && $score > $bestScore) {
                        $bestScore = $score;
                        $best = [$code, $name];
                    }
                }
            }
        }

        return $best;
    }

    /**
     * صف بيكرر أسماء الأعمدة (زي الصف 2 في ماكينات.xlsx) — بنتجاهله.
     */
    private function looksLikeHeaderRow(array $cells, array $headerIndex): bool
    {
        $filled = 0;
        $hits = 0;
        foreach ($cells as $c) {
            if ($c === null || $c === '') {
                continue;
            }
            $filled++;
            if (isset($headerIndex[$this->normalizeHeader($c)])) {
                $hits++;
            }
        }
        return $hits >= 3 && $hits >= $filled * 0.5;
    }

    private function configFor(string $moduleCode): array
    {
        $all = config('excel_modules', []);
        if (!isset($all[$moduleCode])) {
            throw new RuntimeException(
                "ModuleCode غير معروف: {$moduleCode}. أضفه في config/excel_modules.php"
            );
        }
        return $all[$moduleCode];
    }

    private function readSystem($spreadsheet): array
    {
        $code = null;
        $nameAr = null;
        $version = null;
        $this->inferredSheet = null;

        foreach ($spreadsheet->getSheetNames() as $name) {
            if (strcasecmp(trim($name), '_System') === 0 || strcasecmp(trim($name), 'System') === 0) {
                $sheet = $spreadsheet->getSheetByName($name);
                foreach ($sheet->getRowIterator(1, 30) as $row) {
                    $vals = [];
                    foreach ($row->getCellIterator() as $cell) {
                        $vals[] = $cell->getValue();
                    }
                    $key = trim((string) ($vals[0] ?? ''));
                    $val = $vals[1] ?? null;
                    if ($key === 'ModuleCode') {
                        $code = trim((string) $val);
                    }
                    if ($key === 'Module' || $key === 'ModuleNameAr') {
                        $nameAr = trim((string) $val);
                    }
                    if ($key === 'TemplateVersion') {
                        $version = trim((string) $val);
                    }
                }
                break;
            }
        }

        if (!$code) {
            // ملف من غير شيت _System (زي ماكينات.xlsx): نحدد الموديول من أسماء الأعمدة
            $inferred = $this->inferModule($spreadsheet);
            if ($inferred === null) {
                throw new RuntimeException(
                    'لم يتم العثور على ModuleCode في شيت _System، ولم أستطع تحديد نوع الملف من أسماء الأعمدة'
                );
            }
            [$code, $this->inferredSheet] = $inferred;
            $nameAr = null;
        }

        return [$code, $nameAr, $version];
    }

    private function dataSheet($spreadsheet)
    {
        if ($this->inferredSheet !== null) {
            $sheet = $spreadsheet->getSheetByName($this->inferredSheet);
            if ($sheet) {
                return $sheet;
            }
        }
        foreach ($spreadsheet->getSheetNames() as $name) {
            if (str_contains($name, 'بيان') || strcasecmp($name, 'data') === 0) {
                return $spreadsheet->getSheetByName($name);
            }
        }
        // أول شيت ليس _System أو Lookup
        foreach ($spreadsheet->getSheetNames() as $name) {
            $n = mb_strtolower($name);
            if (!str_contains($n, 'system') && !str_contains($n, 'lookup') && !str_starts_with($name, '_')) {
                return $spreadsheet->getSheetByName($name);
            }
        }
        return $spreadsheet->getSheet(0);
    }

    private function headerRow($sheet, int $rowNum = 1): array
    {
        $headers = [];
        foreach ($sheet->getRowIterator($rowNum, $rowNum) as $row) {
            foreach ($row->getCellIterator() as $cell) {
                $headers[] = trim((string) $this->cellValue($cell));
            }
        }
        return $headers;
    }

    /**
     * بعض الملفات (مثل "خطوط الإنتاج المرفقة") فيها صف عنوان في الأول والهيدر الحقيقي في صف تاني.
     * نفحص أول 15 صف ونختار الصف اللي أكتر عدد من أعمدته بيطابق أعمدة الـ config.
     * لو مفيش تطابق نرجع للصف 1 (السلوك القديم).
     */
    private function detectHeaderRow($sheet, array $map): int
    {
        $wanted = [];
        foreach (array_keys($map) as $ar) {
            $wanted[$this->normalizeHeader($ar)] = true;
        }

        $best = 1;
        $bestScore = 0;
        $maxRow = min(15, max(1, (int) $sheet->getHighestDataRow()));

        for ($r = 1; $r <= $maxRow; $r++) {
            $score = 0;
            foreach ($this->headerRow($sheet, $r) as $h) {
                $k = $this->normalizeHeader($h);
                if ($k !== '' && isset($wanted[$k])) {
                    $score++;
                }
            }
            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $r;
            }
        }

        return $best;
    }

    /**
     * توحيد شكل اسم العمود: مسافات متعددة، همزات الألف، ى/ي، ة/ه، تشكيل، حروف كبيرة/صغيرة.
     */
    private function normalizeHeader(mixed $value): string
    {
        $v = trim((string) $value);
        if ($v === '') {
            return '';
        }
        $v = preg_replace('/[\x{064B}-\x{065F}\x{0640}]/u', '', $v);       // تشكيل وتطويل
        $v = str_replace(['أ', 'إ', 'آ'], 'ا', $v);
        $v = str_replace(['ى', 'ئ'], 'ي', $v);
        $v = str_replace('ة', 'ه', $v);
        $v = str_replace(['(', ')', '%', '/', '-', '_', '.'], ' ', $v);
        $v = preg_replace('/\s+/u', ' ', $v);
        return mb_strtolower(trim($v));
    }

    /**
     * قيمة الخلية مع حماية من أخطاء الفورمولا (بدل ما الاستيراد كله يقع).
     */
    private function cellValue($cell): mixed
    {
        try {
            return $cell->getCalculatedValue();
        } catch (\Throwable $e) {
            try {
                return $cell->getOldCalculatedValue() ?? $cell->getValue();
            } catch (\Throwable $e2) {
                return null;
            }
        }
    }

    /**
     * قص القيم الأطول من حجم العمود (varchar) عشان MySQL ما يرميش "Data too long".
     */
    private function fitToColumns(string $table, array $attrs): array
    {
        static $limits = [];
        if (!isset($limits[$table])) {
            $limits[$table] = [];
            try {
                foreach (Schema::getColumns($table) as $col) {
                    if (preg_match('/^(?:var)?char\((\d+)\)/i', (string) ($col['type'] ?? ''), $m)) {
                        $limits[$table][$col['name']] = (int) $m[1];
                    }
                }
            } catch (\Throwable $e) {
                // لو فشل قراءة الـ schema نكمل بدون قص
            }
        }

        foreach ($attrs as $k => $v) {
            if (is_string($v) && isset($limits[$table][$k]) && mb_strlen($v) > $limits[$table][$k]) {
                $attrs[$k] = mb_substr($v, 0, $limits[$table][$k]);
            } elseif ($v instanceof \DateTimeInterface) {
                $attrs[$k] = $v->format('Y-m-d');
            } elseif (is_array($v) || is_object($v)) {
                $attrs[$k] = is_object($v) && method_exists($v, '__toString') ? (string) $v : null;
            }
        }

        return $attrs;
    }

    private function rowEmpty(array $cells): bool
    {
        foreach ($cells as $c) {
            if ($c !== null && $c !== '') {
                return false;
            }
        }
        return true;
    }

    private function mapRow(array $cells, array $headerIndex, array $map): array
    {
        $attrs = [];
        foreach ($map as $arHeader => $enCol) {
            $key = $this->normalizeHeader($arHeader);
            if (!isset($headerIndex[$key])) {
                continue;
            }
            $idx = $headerIndex[$key];
            $val = $cells[$idx] ?? null;
            if (is_string($val)) {
                $val = trim($val);
            }
            $attrs[$enCol] = $val === '' ? null : $val;
        }
        return $attrs;
    }

    private function filterExistingColumns(string $table, array $attrs): array
    {
        static $cache = [];
        if (!isset($cache[$table])) {
            $cache[$table] = Schema::getColumnListing($table);
        }
        $cols = $cache[$table];
        return array_filter(
            $attrs,
            fn ($v, $k) => in_array($k, $cols, true),
            ARRAY_FILTER_USE_BOTH
        );
    }

    private function normalizeCoordinate(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_string($value)) {
            $value = str_replace([',', ' ', '°'], ['.', '', ''], trim($value));
        }
        if (!is_numeric($value)) {
            return null;
        }
        $f = (float) $value;
        if ($f < -180 || $f > 180) {
            return null;
        }
        return $f;
    }
}
