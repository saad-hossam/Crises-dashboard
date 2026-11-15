<?php

namespace App\Services;

use App\Models\ModuleImportBatch;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Reader\XLSX\Options as XLSXOptions;

class TypedExcelImporter
{
    private const CHUNK_SIZE = 400;
    private const COMMIT_EVERY = 5000;

    /** أقصى صفوف بيانات تُحسب في المعاينة — 0 = لا حد (اقرأ كل الصفوف) */
    private const PREVIEW_MAX_DATA_ROWS = 0;

    /** أقصى صف للبحث عن الهيدر */
    private const HEADER_SCAN_MAX = 20;

    public function preview(string $filePath): array
    {
        @set_time_limit(120);
        @ini_set('memory_limit', '512M');

        $resolved = $this->resolveModule($filePath, basename($filePath));
        $moduleCode = $resolved['code'];
        $config = config("excel_modules.{$moduleCode}");
        if (!$config) {
            throw new \RuntimeException("الموديول [{$moduleCode}] غير مسجّل.");
        }

        $scan = $this->scanDataSheet(
            $filePath,
            $config['columns'] ?? [],
            limitDataRows: self::PREVIEW_MAX_DATA_ROWS
        );

        // ملفات بدون صف عنوان
        if (($scan['matched_count'] ?? 0) < 1 && !empty($resolved['system']['ModuleCode'])) {
            $scan['headers'] = array_keys($config['columns'] ?? []);
            $scan['matched_count'] = count($scan['headers']);
            $scan['header_row_index'] = 0;
            $scan['_positional'] = true;
        }

        return [
            'module_code'       => $moduleCode,
            'module_name_ar'    => $config['name_ar'] ?? $moduleCode,
            'template_version'  => $resolved['system']['TemplateVersion'] ?? null,
            'headers'           => $scan['headers'],
            'total_rows'        => $scan['total_rows'],
            'total_rows_capped' => $scan['capped'],
            'preview_rows'      => $scan['preview_rows'],
            '_inferred'         => $resolved['inferred'],
            'system'            => $resolved['system'],
            'header_row_index'  => $scan['header_row_index'],
            'matched_columns'   => $scan['matched_count'],
        ];
    }

    public function import(
        string $storedPath,
        string $originalFilename,
        int $userId,
        bool $replaceExisting = true
    ): ModuleImportBatch {
        @set_time_limit(0);
        @ini_set('memory_limit', '1024M');

        $absolutePath = Storage::disk('local')->path($storedPath);
        $resolved = $this->resolveModule($absolutePath, $originalFilename);
        $moduleCode = $resolved['code'];

        $config = config("excel_modules.{$moduleCode}");
        if (!$config) {
            throw new \RuntimeException("الموديول [{$moduleCode}] غير موجود في السجل.");
        }

        $tableName = $this->resolveTableName($config, $moduleCode);
        if (!Schema::hasTable($tableName)) {
            throw new \RuntimeException("الجدول [{$tableName}] غير موجود. نفّذ php artisan migrate");
        }

        $columnsMap = $config['columns'] ?? [];
        $normalizedColumnsMap = [];
        foreach ($columnsMap as $ar => $en) {
            $normalizedColumnsMap[$this->normalizeArabic((string) $ar)] = $en;
            $normalizedColumnsMap[trim((string) $ar)] = $en;
        }

        $tableColumns = array_flip(Schema::getColumnListing($tableName));

        // مسح الهيدر فقط
        $scan = $this->scanDataSheet($absolutePath, $columnsMap, limitDataRows: 0);
        $hasSystemCode = !empty($resolved['system']['ModuleCode']);

        if ($scan['matched_count'] < 1 && !$hasSystemCode) {
            throw new \RuntimeException(
                'لم أستطع تحديد الموديول من أسماء الأعمدة. '
                . 'المحاول: ' . $moduleCode . ' — الرؤوس: ['
                . implode(' | ', array_slice($scan['headers'], 0, 10)) . ']'
            );
        }

        $usePositional = false;
        if ($scan['matched_count'] < 1 && $hasSystemCode) {
            $usePositional = true;
            $scan['header_row_index'] = 0;
        }

        $reader = new Reader(new XLSXOptions());
        $reader->open($absolutePath);

        $imported = 0;
        $skipped = 0;
        $chunkBuffer = [];
        $now = now();
        $batch = null;

        // ✅ اكتشف نوع قاعدة البيانات
        $driver = DB::connection()->getDriverName();
        $isMysql = $driver === 'mysql';

        DB::connection()->disableQueryLog();

        try {
            // ✅ هذه الأوامر مدعومة في MySQL فقط
            if ($isMysql) {
                DB::statement('SET FOREIGN_KEY_CHECKS=0');
                DB::statement('SET UNIQUE_CHECKS=0');
            }

            if ($replaceExisting) {
                DB::table($tableName)->truncate();
            }

            $batch = ModuleImportBatch::create([
                'module_code'       => $moduleCode,
                'module_name_ar'    => $config['name_ar'] ?? $moduleCode,
                'original_filename' => $originalFilename,
                'file_hash'         => @hash_file('sha256', $absolutePath) ?: null,
                'total_rows'        => 0,
                'imported_rows'     => 0,
                'skipped_rows'      => 0,
                'status'            => 'processing',
                'user_id'           => $userId,
                'replaced_existing' => $replaceExisting,
            ]);

            DB::beginTransaction();

            $targetHeaderIndex = (int) $scan['header_row_index'];
            if (!$usePositional) {
                $targetHeaderIndex = max(1, $targetHeaderIndex);
            }

            foreach ($reader->getSheetIterator() as $sheet) {
                if ($this->isMetaSheet($sheet->getName())) {
                    continue;
                }

                $rowIndex = 0;
                $headerMap = [];
                $headerFound = false;

                if ($usePositional) {
                    $englishOrdered = [];
                    foreach ($columnsMap as $ar => $en) {
                        if (!in_array($en, $englishOrdered, true)) {
                            $englishOrdered[] = $en;
                        }
                    }
                    foreach ($englishOrdered as $idx => $englishCol) {
                        if (isset($tableColumns[$englishCol])) {
                            $headerMap[$idx] = $englishCol;
                        }
                    }
                    $headerFound = true;
                }

                foreach ($sheet->getRowIterator() as $row) {
                    $rowIndex++;
                    $cells = $this->rowToArray($row);

                    if (!$headerFound) {
                        if ($rowIndex < $targetHeaderIndex) {
                            continue;
                        }
                        foreach ($cells as $idx => $arabicHeader) {
                            $arabic = trim((string) $arabicHeader);
                            if ($arabic === '') {
                                continue;
                            }
                            $n = $this->normalizeArabic($arabic);
                            $english = $columnsMap[$arabic]
                                ?? $normalizedColumnsMap[$arabic]
                                ?? $normalizedColumnsMap[$n]
                                ?? null;
                            if ($english && isset($tableColumns[$english])) {
                                $headerMap[$idx] = $english;
                            }
                        }
                        $headerFound = true;
                        if (count($headerMap) < 1) {
                            throw new \RuntimeException(
                                'لا توجد أعمدة مطابقة للموديول [' . $moduleCode . '] في صف ' . $rowIndex
                            );
                        }
                        continue;
                    }

                    if ($this->isEmptyRow($cells)) {
                        $skipped++;
                        continue;
                    }

                    $record = [
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    if (isset($tableColumns['module_import_batch_id'])) {
                        $record['module_import_batch_id'] = $batch->id;
                    }
                    if (isset($tableColumns['import_batch_id'])) {
                        $record['import_batch_id'] = $batch->id;
                    }
                    if (isset($tableColumns['created_by'])) {
                        $record['created_by'] = $userId;
                    }
                    if (isset($tableColumns['updated_by'])) {
                        $record['updated_by'] = $userId;
                    }

                    $hasData = false;
                    foreach ($headerMap as $idx => $englishCol) {
                        $val = $this->normalizeValue($cells[$idx] ?? null);
                        $record[$englishCol] = $val;
                        if ($val !== null && $val !== '') {
                            $hasData = true;
                        }
                    }
                    if (!$hasData) {
                        $skipped++;
                        continue;
                    }

                    $chunkBuffer[] = $record;
                    $imported++;

                    if (count($chunkBuffer) >= self::CHUNK_SIZE) {
                        DB::table($tableName)->insert($chunkBuffer);
                        $chunkBuffer = [];
                        if ($imported % self::COMMIT_EVERY === 0) {
                            DB::commit();
                            DB::beginTransaction();
                            $batch->update([
                                'imported_rows' => $imported,
                                'skipped_rows'  => $skipped,
                                'total_rows'    => $imported + $skipped,
                            ]);
                        }
                    }
                }
                break;
            }

            if (!empty($chunkBuffer)) {
                DB::table($tableName)->insert($chunkBuffer);
            }
            $reader->close();

            $batch->update([
                'total_rows'    => $imported + $skipped,
                'imported_rows' => $imported,
                'skipped_rows'  => $skipped,
                'status'        => 'completed',
            ]);
            DB::commit();
            Cache::forget('excel_preview_' . md5($storedPath));

            return $batch->fresh();
        } catch (\Throwable $e) {
            try { DB::rollBack(); } catch (\Throwable $ignore) {}
            try { $reader->close(); } catch (\Throwable $ignore) {}
            if ($batch) {
                try {
                    $batch->update([
                        'status'        => 'failed',
                        'imported_rows' => $imported,
                        'skipped_rows'  => $skipped,
                        'total_rows'    => $imported + $skipped,
                    ]);
                } catch (\Throwable $ignore) {}
            }
            throw $e;
        } finally {
            if ($isMysql) {
                try {
                    DB::statement('SET FOREIGN_KEY_CHECKS=1');
                    DB::statement('SET UNIQUE_CHECKS=1');
                } catch (\Throwable $ignore) {}
            }
        }
    }

    private function resolveModule(string $filePath, string $originalName): array
    {
        $system = $this->readSystemSheetRaw($filePath);
        $systemCode = $system['ModuleCode'] ?? '';

        if ($systemCode === 'PRODUCTION_SCOPES') {
            $systemCode = '';
        }

        if ($systemCode !== '' && config("excel_modules.{$systemCode}")) {
            return [
                'code' => $systemCode,
                'inferred' => false,
                'system' => $system,
            ];
        }

        $fromFile = $this->inferModuleCodeFromFilename($originalName)
            ?? $this->inferModuleCodeFromFilename($filePath);
        if ($fromFile && $fromFile !== 'PRODUCTION_SCOPES' && config("excel_modules.{$fromFile}")) {
            return [
                'code' => $fromFile,
                'inferred' => true,
                'system' => array_merge($system, [
                    'ModuleCode' => $fromFile,
                    'Module' => config("excel_modules.{$fromFile}.name_ar", $fromFile),
                    '_inferred' => true,
                ]),
            ];
        }

        $fromHeaders = $this->inferModuleCodeFromHeadersFast($filePath);
        if ($fromHeaders && config("excel_modules.{$fromHeaders}")) {
            return [
                'code' => $fromHeaders,
                'inferred' => true,
                'system' => array_merge($system, [
                    'ModuleCode' => $fromHeaders,
                    'Module' => config("excel_modules.{$fromHeaders}.name_ar", $fromHeaders),
                    '_inferred' => true,
                ]),
            ];
        }

        throw new \RuntimeException(
            'لم أستطع تحديد الموديول من أسماء الأعمدة ولا من اسم الملف ['
            . basename($originalName) . ']. '
            . 'أضف شيت _System فيه ModuleCode أو سمِّ الملف باسم الموديول (مثال: نسب السحب.xlsx).'
        );
    }

    /**
     * @param int $limitDataRows 0 = لا حد (اقرأ كل الصفوف)، >0 = حد أقصى
     */
    private function scanDataSheet(string $filePath, array $columnsMap, int $limitDataRows = 0): array
    {
        $normMap = [];
        foreach ($columnsMap as $ar => $en) {
            $normMap[$this->normalizeArabic((string) $ar)] = $en;
        }

        $reader = new Reader(new XLSXOptions());
        $reader->open($filePath);

        $result = [
            'headers' => [],
            'header_row_index' => 1,
            'matched_count' => 0,
            'total_rows' => 0,
            'preview_rows' => [],
            'capped' => false,
        ];

        // ✅ 0 = لا حد
        $limit = $limitDataRows > 0 ? $limitDataRows : PHP_INT_MAX;

        try {
            foreach ($reader->getSheetIterator() as $sheet) {
                if ($this->isMetaSheet($sheet->getName())) {
                    continue;
                }

                $rowIndex = 0;
                $candidates = [];
                $headerRowIndex = null;
                $headers = [];
                $matched = 0;
                $total = 0;
                $preview = [];
                $pastHeader = false;

                foreach ($sheet->getRowIterator() as $row) {
                    $rowIndex++;
                    $cells = $this->rowToArray($row);
                    $labels = array_map(fn ($h) => trim((string) $h), $cells);

                    if (!$pastHeader) {
                        if ($rowIndex <= self::HEADER_SCAN_MAX) {
                            $hits = 0;
                            foreach ($labels as $lab) {
                                if ($lab === '') {
                                    continue;
                                }
                                $n = $this->normalizeArabic($lab);
                                if (isset($normMap[$n]) || isset($columnsMap[$lab])) {
                                    $hits++;
                                }
                            }
                            $candidates[] = [
                                'index' => $rowIndex,
                                'hits' => $hits,
                                'labels' => array_values(array_filter($labels, fn ($x) => $x !== '')),
                            ];
                        }

                        if ($rowIndex === self::HEADER_SCAN_MAX || ($rowIndex >= 3 && !empty($candidates))) {
                            usort($candidates, fn ($a, $b) => $b['hits'] <=> $a['hits']);
                            if (!empty($candidates) && $candidates[0]['hits'] >= 1) {
                                $headerRowIndex = $candidates[0]['index'];
                                $headers = $candidates[0]['labels'];
                                $matched = $candidates[0]['hits'];
                                $pastHeader = true;
                                if ($rowIndex === $headerRowIndex) {
                                    continue;
                                }
                            }
                        }

                        if (!$pastHeader) {
                            if ($rowIndex > self::HEADER_SCAN_MAX) {
                                if (!empty($candidates)) {
                                    usort($candidates, fn ($a, $b) => $b['hits'] <=> $a['hits']);
                                    $headerRowIndex = $candidates[0]['index'];
                                    $headers = $candidates[0]['labels'];
                                    $matched = $candidates[0]['hits'];
                                    $pastHeader = true;
                                } else {
                                    break;
                                }
                            } else {
                                continue;
                            }
                        }

                        if ($rowIndex <= $headerRowIndex) {
                            if ($rowIndex === $headerRowIndex) {
                                continue;
                            }
                            continue;
                        }
                    }

                    if ($this->isEmptyRow($cells)) {
                        continue;
                    }

                    // ✅ الآن: 0 يعني لا حد — نعدّ كل الصفوف
                    $total++;
                    if (count($preview) < 15) {
                        $preview[] = $cells;
                    }
                    if ($total >= $limit) {
                        $result['capped'] = true;
                        break;
                    }
                }

                if ($headerRowIndex === null && !empty($candidates)) {
                    usort($candidates, fn ($a, $b) => $b['hits'] <=> $a['hits']);
                    $headerRowIndex = $candidates[0]['index'];
                    $headers = $candidates[0]['labels'];
                    $matched = $candidates[0]['hits'];
                }

                $result = [
                    'headers' => $headers,
                    'header_row_index' => $headerRowIndex ?? 1,
                    'matched_count' => $matched,
                    'total_rows' => $total,
                    'preview_rows' => $preview,
                    'capped' => $result['capped'],
                ];
                break;
            }
        } finally {
            $reader->close();
        }

        return $result;
    }

    private function inferModuleCodeFromHeadersFast(string $filePath): ?string
    {
        $headerLabels = $this->readCandidateHeaderLabels($filePath);
        if (empty($headerLabels)) {
            return null;
        }
        $normHeaders = array_map(fn ($h) => $this->normalizeArabic($h), $headerLabels);

        $bestCode = null;
        $bestScore = 0;

        foreach (config('excel_modules', []) as $code => $cfg) {
            if ($code === 'PRODUCTION_SCOPES') {
                continue;
            }
            $cols = $cfg['columns'] ?? [];
            if (count($cols) < 2) {
                continue;
            }
            $keys = [];
            foreach (array_keys($cols) as $k) {
                $keys[$this->normalizeArabic((string) $k)] = true;
            }
            $hits = 0;
            foreach ($normHeaders as $h) {
                if ($h !== '' && isset($keys[$h])) {
                    $hits++;
                }
            }
            if ($hits > $bestScore) {
                $bestScore = $hits;
                $bestCode = $code;
            }
        }

        return $bestScore >= 2 ? $bestCode : null;
    }

    private function readCandidateHeaderLabels(string $filePath): array
    {
        $reader = new Reader(new XLSXOptions());
        $reader->open($filePath);
        $best = [];
        $bestHitsGeneric = 0;
        try {
            foreach ($reader->getSheetIterator() as $sheet) {
                if ($this->isMetaSheet($sheet->getName())) {
                    continue;
                }
                $rowIndex = 0;
                foreach ($sheet->getRowIterator() as $row) {
                    $rowIndex++;
                    if ($rowIndex > self::HEADER_SCAN_MAX) {
                        break;
                    }
                    $labels = [];
                    foreach ($this->rowToArray($row) as $c) {
                        $t = trim((string) $c);
                        if ($t !== '') {
                            $labels[] = $t;
                        }
                    }
                    if (count($labels) > $bestHitsGeneric) {
                        $bestHitsGeneric = count($labels);
                        $best = $labels;
                    }
                }
                break;
            }
        } finally {
            $reader->close();
        }

        return $best;
    }

    private function readSystemSheetRaw(string $filePath): array
    {
        $reader = new Reader(new XLSXOptions());
        $reader->open($filePath);
        $system = [];
        try {
            foreach ($reader->getSheetIterator() as $sheet) {
                if ($sheet->getName() !== '_System') {
                    continue;
                }
                foreach ($sheet->getRowIterator() as $rowIndex => $row) {
                    if ($rowIndex === 1) {
                        continue;
                    }
                    $cells = $this->rowToArray($row);
                    if (count($cells) < 2) {
                        continue;
                    }
                    $key = trim((string) ($cells[0] ?? ''));
                    $value = $cells[1] ?? null;
                    if ($key === '') {
                        continue;
                    }
                    if ($value instanceof \DateTimeInterface) {
                        $value = $value->format('Y-m-d H:i:s');
                    }
                    $system[$key] = is_string($value) ? trim($value) : (string) ($value ?? '');
                }
                break;
            }
        } finally {
            $reader->close();
        }

        return $system;
    }

    private function normalizeArabic(string $text): string
    {
        $text = mb_strtolower(trim($text), 'UTF-8');
        $text = preg_replace('/[\x{064B}-\x{065F}\x{0670}]/u', '', $text) ?? $text;
        $text = strtr($text, [
            'أ' => 'ا', 'إ' => 'ا', 'آ' => 'ا', 'ٱ' => 'ا',
            'ة' => 'ه', 'ى' => 'ي', 'ؤ' => 'و', 'ئ' => 'ي',
        ]);
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;
        $text = str_replace(['.xlsx', '.xls'], '', $text);
        $text = preg_replace('/[\-_]?\d+$/u', '', $text) ?? $text;

        return trim($text);
    }

    private function inferModuleCodeFromFilename(string $filePath): ?string
    {
        $filename = $this->normalizeArabic(basename($filePath));

        $map = [
            'نسب السحب'               => 'WITHDRAWAL_RATES',
            'withdrawal'              => 'WITHDRAWAL_RATES',
            'المخزون الاستراتيجي'     => 'STRATEGIC_INVENTORY',
            'المخزون الاستراتيجى'     => 'STRATEGIC_INVENTORY',
            'strategic inventory'     => 'STRATEGIC_INVENTORY',
            'المعدات'                 => 'EQUIPMENT',
            'equipment'               => 'EQUIPMENT',
            'خطوط الانتاج'            => 'PRODUCTION_LINES',
            'production lines'        => 'PRODUCTION_LINES',
            'ماكينات'                 => 'MACHINES',
            'machines'                => 'MACHINES',
            'المخازن'                 => 'WAREHOUSES',
            'المركبات'                => 'VEHICLES',
            'الشركات'                 => 'COMPANY',
            'الموارد البشريه'         => 'HUMAN_RESOURCES',
            'المنشات والعقارات'       => 'FACILITIES_AND_PROPERTIES',
        ];
        uksort($map, fn ($a, $b) => mb_strlen($b) <=> mb_strlen($a));
        foreach ($map as $needle => $code) {
            if (mb_strpos($filename, $this->normalizeArabic($needle)) !== false) {
                return $code;
            }
        }

        return null;
    }

    private function isMetaSheet(string $sheetName): bool
    {
        return $sheetName === '_System'
            || str_starts_with($sheetName, 'Lookup_')
            || str_starts_with($sheetName, '_');
    }

    private function rowToArray($row): array
    {
        $cells = [];
        foreach ($row->getCells() as $cell) {
            $val = $cell->getValue();
            if ($val instanceof \DateTimeInterface) {
                $val = $val->format('Y-m-d H:i:s');
            }
            $cells[] = $val;
        }

        return $cells;
    }

    private function isEmptyRow(array $cells): bool
    {
        foreach ($cells as $v) {
            if ($v === null) {
                continue;
            }
            if (is_string($v)) {
                if (trim($v) !== '') {
                    return false;
                }
            } elseif ($v !== 0 && $v !== '0') {
                return false;
            }
        }

        return true;
    }

    private function normalizeValue($value)
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }
        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }
        if (is_string($value)) {
            $t = trim($value);
            if ($t === '') {
                return null;
            }
            $t = preg_replace('/<br\s*\/?>/i', ' ', $t) ?? $t;
            $t = strip_tags($t);
            $t = preg_replace('/\s+/u', ' ', $t) ?? $t;

            return trim($t) === '' ? null : trim($t);
        }
        if (is_numeric($value)) {
            return $value;
        }

        return $value === null ? null : (string) $value;
    }

    private function resolveTableName(array $config, string $moduleCode): string
    {
        if (!empty($config['table'])) {
            return $config['table'];
        }
        $model = $config['model'] ?? null;
        if ($model && class_exists($model)) {
            return (new $model)->getTable();
        }
        throw new \RuntimeException("لا يمكن تحديد اسم الجدول للموديول [{$moduleCode}]");
    }
}