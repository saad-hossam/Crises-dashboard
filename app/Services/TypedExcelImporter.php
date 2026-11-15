<?php

namespace App\Services;

use RuntimeException;
use App\Models\ModuleImportBatch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;

class TypedExcelImporter
{
    /* =========================================================
       PREVIEW — يقرأ أول 12 صفًا فقط (سريع جدًا)
    ========================================================= */
    public function preview(string $absolutePath): array
    {
        $reader = new XlsxReader();
        $reader->open($absolutePath);

        $moduleCode   = null;
        $moduleNameAr = null;
        $headers      = [];
        $preview      = [];
        $total        = 0;
        $dataSheetName = null;

        try {
            foreach ($reader->getSheetIterator() as $sheet) {
                $sheetName = $sheet->getName();
                $n = mb_strtolower($sheetName);

                // _System
                if ($n === '_system' || $n === 'system') {
                    foreach ($sheet->getRowIterator() as $row) {
                        $cells = $row->getCells();
                        $key = trim((string) ($cells[0]->getValue() ?? ''));
                        $val = $cells[1]->getValue() ?? null;
                        if ($key === 'ModuleCode') $moduleCode = trim((string) $val);
                        if ($key === 'Module')     $moduleNameAr = trim((string) $val);
                    }
                    continue;
                }

                // البيانات (أول ورقة بيانات فقط)
                if ($dataSheetName === null
                    && (str_contains($n, 'بيان') || str_contains($n, 'data') || !str_starts_with($n, '_'))) {
                    $dataSheetName = $sheetName;

                    foreach ($sheet->getRowIterator() as $row) {
                        $cells = [];
                        foreach ($row->getCells() as $cell) {
                            $cells[] = $cell->getValue();
                        }

                        // أول صف غير فارغ = الهيدر
                        if (empty($headers)) {
                            $headers = array_map(fn ($c) => trim((string) $c), $cells);
                            continue;
                        }

                        // تجاهل الفارغ
                        if (array_filter($cells, fn ($c) => $c !== null && $c !== '') === []) {
                            continue;
                        }

                        $total++;
                        if (count($preview) < 12) {
                            $preview[] = $cells;
                        }
                    }
                }
            }
        } finally {
            $reader->close();
        }

        if (!$moduleCode) {
            throw new RuntimeException('لم يتم العثور على ModuleCode في شيت _System');
        }

        $config = config('excel_modules.' . $moduleCode);
        if (!$config) {
            throw new RuntimeException("ModuleCode غير معروف: {$moduleCode}");
        }

        return [
            'module_code'      => $moduleCode,
            'module_name_ar'   => $moduleNameAr ?: ($config['name_ar'] ?? $moduleCode),
            'template_version' => null,
            'table'            => $config['table'],
            'headers'          => $headers,
            'total_rows'       => $total,
            'preview_rows'     => $preview,
            'mapped_columns'   => array_values($config['columns'] ?? []),
        ];
    }

    /* =========================================================
       IMPORT — streaming، ذاكرة ثابتة (~10MB لأي حجم)
    ========================================================= */
    public function import(
        string $storedPath,
        string $originalFilename,
        ?int $userId = null,
        bool $replaceExisting = true
    ): ModuleImportBatch {
        $absolute = \Storage::disk('local')->path($storedPath);
        if (!is_file($absolute)) {
            throw new RuntimeException('الملف غير موجود: ' . $storedPath);
        }

        $reader = new XlsxReader();
        $reader->open($absolute);

        $moduleCode   = null;
        $moduleNameAr = null;
        $headers      = null;
        $headerIndex  = [];
        $batch        = null;
        $table        = null;
        $map          = null;
        $imported     = 0;
        $skipped      = 0;
        $total        = 0;
        $buffer       = [];

        try {
            DB::beginTransaction();

            foreach ($reader->getSheetIterator() as $sheet) {
                $n = mb_strtolower($sheet->getName());

                /* ---- _System ---- */
                if ($n === '_system' || $n === 'system') {
                    foreach ($sheet->getRowIterator() as $row) {
                        $cells = $row->getCells();
                        $key = trim((string) ($cells[0]->getValue() ?? ''));
                        $val = $cells[1]->getValue() ?? null;
                        if ($key === 'ModuleCode') $moduleCode = trim((string) $val);
                        if ($key === 'Module')     $moduleNameAr = trim((string) $val);
                    }
                    continue;
                }

                /* ---- البيانات ---- */
                if ($headers !== null) continue;   // عالجناها بالفعل

                foreach ($sheet->getRowIterator() as $row) {
                    $cells = [];
                    foreach ($row->getCells() as $cell) {
                        $cells[] = $cell->getValue();
                    }

                    /* أول صف = الهيدر */
                    if ($headers === null) {
                        $headers = array_map(fn ($c) => trim((string) $c), $cells);

                        // حل ModuleCode إن لم يكن من _System
                        if (!$moduleCode) {
                            $moduleCode = $this->inferModuleFromHeaders($headers);
                        }

                        $config = config('excel_modules.' . $moduleCode);
                        if (!$config) {
                            throw new RuntimeException("ModuleCode غير معروف: {$moduleCode}");
                        }

                        $table = $config['table'];
                        $map   = $config['columns'];

                        foreach ($headers as $i => $h) {
                            $k = $this->normalizeHeader($h);
                            if ($k !== '' && !isset($headerIndex[$k])) {
                                $headerIndex[$k] = $i;
                            }
                        }

                        if ($replaceExisting && Schema::hasTable($table)) {
                            DB::table($table)->delete();
                        }

                        $batch = ModuleImportBatch::create([
                            'module_code'       => $moduleCode,
                            'module_name_ar'    => $moduleNameAr ?: $config['name_ar'],
                            'original_filename' => $originalFilename,
                            'file_hash'         => hash_file('sha256', $absolute),
                            'status'            => 'processing',
                            'user_id'           => $userId,
                            'replaced_existing' => $replaceExisting,
                        ]);

                        continue;
                    }

                    /* تجاهل الصفوف الفارغة */
                    if (array_filter($cells, fn ($c) => $c !== null && $c !== '') === []) {
                        continue;
                    }

                    $total++;
                    $attrs = $this->mapRow($cells, $headerIndex, $map);
                    if (empty($attrs)) { $skipped++; continue; }

                    $attrs['module_import_batch_id'] = $batch->id;
                    if ($table !== 'production_lines') {
                        $attrs['import_batch_id'] = $batch->id;
                    }

                    foreach (['x_coordinate', 'y_coordinate'] as $coord) {
                        if (array_key_exists($coord, $attrs)) {
                            $attrs[$coord] = $this->normalizeCoordinate($attrs[$coord]);
                        }
                    }

                    $attrs = $this->filterExistingColumns($table, $attrs);
                    $attrs = $this->fitToColumns($table, $attrs);

                    if (empty($attrs)) { $skipped++; continue; }

                    $buffer[] = $attrs + ['created_at' => now(), 'updated_at' => now()];

                    if (count($buffer) >= 500) {
                        DB::table($table)->insert($buffer);
                        $imported += count($buffer);
                        $buffer = [];
                    }
                }
            }

            if (!empty($buffer)) {
                DB::table($table)->insert($buffer);
                $imported += count($buffer);
            }

            if (!$batch) {
                throw new RuntimeException('لم يتم العثور على ورقة بيانات في الملف');
            }

            $batch->update([
                'total_rows'    => $total,
                'imported_rows' => $imported,
                'skipped_rows'  => $skipped,
                'status'        => 'completed',
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            if ($batch) {
                $batch->update([
                    'status'        => 'failed',
                    'error_message' => mb_substr($e->getMessage(), 0, 500),
                ]);
            }
            throw $e;
        } finally {
            $reader->close();
        }

        return $batch->fresh();
    }

    /* =========================================================
       HELPERS
    ========================================================= */

    private function inferModuleFromHeaders(array $headers): string
    {
        $all = config('excel_modules', []);
        $best = null;
        $bestScore = 0;

        foreach ($all as $code => $cfg) {
            $wanted = [];
            foreach (array_keys($cfg['columns'] ?? []) as $ar) {
                $wanted[$this->normalizeHeader($ar)] = true;
            }
            $score = 0;
            foreach ($headers as $h) {
                $k = $this->normalizeHeader($h);
                if ($k !== '' && isset($wanted[$k])) $score++;
            }
            if ($score > $bestScore && $score >= 3) {
                $bestScore = $score;
                $best = $code;
            }
        }

        if (!$best) {
            throw new RuntimeException('لم أستطع تحديد الموديول من أسماء الأعمدة');
        }
        return $best;
    }

    private function normalizeHeader(mixed $value): string
    {
        $v = trim((string) $value);
        if ($v === '') return '';
        $v = preg_replace('/[\x{064B}-\x{065F}\x{0640}]/u', '', $v);
        $v = str_replace(['أ', 'إ', 'آ'], 'ا', $v);
        $v = str_replace(['ى', 'ئ'], 'ي', $v);
        $v = str_replace('ة', 'ه', $v);
        $v = str_replace(['(', ')', '%', '/', '-', '_', '.'], ' ', $v);
        $v = preg_replace('/\s+/u', ' ', $v);
        return mb_strtolower(trim($v));
    }

    private function mapRow(array $cells, array $headerIndex, array $map): array
    {
        $attrs = [];
        foreach ($map as $arHeader => $enCol) {
            $key = $this->normalizeHeader($arHeader);
            if (!isset($headerIndex[$key])) continue;
            $idx = $headerIndex[$key];
            $val = $cells[$idx] ?? null;
            if (is_string($val)) $val = trim($val);
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
        return array_filter(
            $attrs,
            fn ($v, $k) => in_array($k, $cache[$table], true),
            ARRAY_FILTER_USE_BOTH
        );
    }

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
            } catch (\Throwable $e) {}
        }
        foreach ($attrs as $k => $v) {
            if (is_string($v) && isset($limits[$table][$k]) && mb_strlen($v) > $limits[$table][$k]) {
                $attrs[$k] = mb_substr($v, 0, $limits[$table][$k]);
            } elseif ($v instanceof \DateTimeInterface) {
                $attrs[$k] = $v->format('Y-m-d');
            }
        }
        return $attrs;
    }

    private function normalizeCoordinate(mixed $value): ?float
    {
        if ($value === null || $value === '') return null;
        if (is_string($value)) $value = str_replace([',', ' ', '°'], ['.', '', ''], trim($value));
        if (!is_numeric($value)) return null;
        $f = (float) $value;
        if ($f < -180 || $f > 180) return null;
        return $f;
    }
}