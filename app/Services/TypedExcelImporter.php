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
    public function preview(string $absolutePath): array
    {
        $spreadsheet = IOFactory::load($absolutePath);
        [$moduleCode, $moduleNameAr, $templateVersion] = $this->readSystem($spreadsheet);
        $config = $this->configFor($moduleCode);

        $sheet = $this->dataSheet($spreadsheet);
        $headers = $this->headerRow($sheet);
        $total = 0;
        $preview = [];

        foreach ($sheet->getRowIterator(2) as $row) {
            $cells = [];
            foreach ($row->getCellIterator() as $cell) {
                $cells[] = $cell->getCalculatedValue();
            }
            // تجاهل الصفوف الفارغة
            if ($this->rowEmpty($cells)) {
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
        $config = $this->configFor($moduleCode);
        $table = $config['table'];
        $map = $config['columns'];
        $modelClass = $config['model'];

        $sheet = $this->dataSheet($spreadsheet);
        $headers = $this->headerRow($sheet);

        // بناء فهرس: اسم عربي → index في الصف
        $headerIndex = [];
        foreach ($headers as $i => $h) {
            $h = trim((string) $h);
            if ($h !== '') {
                $headerIndex[$h] = $i;
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

            foreach ($sheet->getRowIterator(2) as $row) {
                $cells = [];
                foreach ($row->getCellIterator() as $cell) {
                    $cells[] = $cell->getCalculatedValue();
                }
                if ($this->rowEmpty($cells)) {
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
            throw new RuntimeException('لم يتم العثور على ModuleCode في شيت _System');
        }

        return [$code, $nameAr, $version];
    }

    private function dataSheet($spreadsheet)
    {
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

    private function headerRow($sheet): array
    {
        $headers = [];
        foreach ($sheet->getRowIterator(1, 1) as $row) {
            foreach ($row->getCellIterator() as $cell) {
                $headers[] = trim((string) $cell->getValue());
            }
        }
        return $headers;
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
            if (!isset($headerIndex[$arHeader])) {
                continue;
            }
            $idx = $headerIndex[$arHeader];
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
