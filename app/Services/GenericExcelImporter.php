<?php

namespace App\Services;

use App\Models\ExcelImportBatch;
use App\Models\ExcelModule;
use App\Models\ExcelRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use RuntimeException;
use Throwable;

class GenericExcelImporter
{
    private const DATA_SHEET = 'البيانات';
    private const SYSTEM_SHEET = '_System';
    private const CHUNK_SIZE = 300;

    /**
     * Common columns we try to extract specially (for indexing & filters).
     * Keys are possible Arabic header names.
     */
    private const SPECIAL_COLUMNS = [
        'unit_code' => ['كود الوحدة', 'كود الوحده', 'كود وحدة'],
        'unit_name' => ['الوحدة', 'اسم الوحدة', 'الوحده'],
        'x' => ['X', 'x', 'إحداثي X', 'الاحداثي السيني'],
        'y' => ['Y', 'y', 'إحداثي Y', 'الاحداثي الصادي'],
    ];

    /**
     * Preview only – returns structured data without saving.
     */
    public function preview(string $absolutePath): array
    {
        $spreadsheet = IOFactory::load($absolutePath);

        $system = $this->readSystemSheet($spreadsheet);
        $moduleCode = $system['ModuleCode'] ?? null;
        $moduleName = $system['Module'] ?? 'Unknown';

        if (!$moduleCode) {
            throw new RuntimeException('ملف Excel لا يحتوي على ModuleCode في ورقة _System');
        }

        $dataSheet = $spreadsheet->getSheetByName(self::DATA_SHEET);
        if (!$dataSheet) {
            throw new RuntimeException('ورقة "البيانات" غير موجودة في الملف');
        }

        $rows = $dataSheet->toArray(null, true, true, false);
        if (count($rows) < 2) {
            throw new RuntimeException('ورقة البيانات فارغة أو تحتوي على عناوين فقط');
        }

        $headers = array_map(fn ($h) => trim((string) $h), $rows[0]);
        $dataRows = array_slice($rows, 1);

        // Clean empty trailing rows
        $dataRows = array_values(array_filter($dataRows, function ($row) {
            return collect($row)->filter(fn ($v) => $v !== null && $v !== '')->isNotEmpty();
        }));

        $previewRows = array_slice($dataRows, 0, 15); // first 15 for UI

        return [
            'module_code' => $moduleCode,
            'module_name_ar' => $moduleName,
            'template_version' => $system['TemplateVersion'] ?? null,
            'headers' => $headers,
            'total_rows' => count($dataRows),
            'preview_rows' => $previewRows,
            'file_hash' => hash_file('sha256', $absolutePath),
        ];
    }

    /**
     * Full import after user confirms the preview.
     */
    public function import(
        string $storedPath,          // relative path on local disk
        string $originalFilename,
        int $userId,
        bool $replaceExisting = true // if true → delete old records of this module
    ): ExcelImportBatch {
        $disk = Storage::disk('local');
        $absolutePath = $disk->path($storedPath);

        if (!file_exists($absolutePath)) {
            throw new RuntimeException("الملف غير موجود: {$storedPath}");
        }

        $fileHash = hash_file('sha256', $absolutePath);
        $preview = $this->preview($absolutePath);

        return DB::transaction(function () use (
            $preview, $storedPath, $originalFilename, $userId, $fileHash, $replaceExisting, $absolutePath
        ) {
            // 1. Upsert module
            $module = ExcelModule::updateOrCreate(
                ['code' => $preview['module_code']],
                [
                    'name_ar' => $preview['module_name_ar'],
                    'template_version' => $preview['template_version'],
                    'last_headers' => $preview['headers'],
                ]
            );

            // 2. Check duplicate file
            $existingBatch = ExcelImportBatch::where('excel_module_id', $module->id)
                ->where('file_hash', $fileHash)
                ->where('status', 'completed')
                ->first();

            if ($existingBatch) {
                throw new RuntimeException('تم استيراد نفس الملف مسبقاً (نفس الـ hash).');
            }

            // 3. Create batch
            $batch = ExcelImportBatch::create([
                'excel_module_id' => $module->id,
                'user_id' => $userId,
                'original_filename' => $originalFilename,
                'stored_path' => $storedPath,
                'file_hash' => $fileHash,
                'total_rows' => $preview['total_rows'],
                'status' => 'processing',
            ]);

            // 4. Optionally clear old data for this module
            if ($replaceExisting) {
                ExcelRecord::where('excel_module_id', $module->id)->delete();
            }

            // 5. Read full data and insert in chunks
            $spreadsheet = IOFactory::load($absolutePath);
            $dataSheet = $spreadsheet->getSheetByName(self::DATA_SHEET);
            $allRows = $dataSheet->toArray(null, true, true, false);

            $headers = array_map(fn ($h) => trim((string) $h), $allRows[0]);
            $headerKeys = $this->normalizeHeaders($headers);

            $dataRows = array_slice($allRows, 1);
            $dataRows = array_values(array_filter($dataRows, function ($row) {
                return collect($row)->filter(fn ($v) => $v !== null && $v !== '')->isNotEmpty();
            }));

            $imported = 0;
            $failed = 0;
            $chunks = array_chunk($dataRows, self::CHUNK_SIZE);

            foreach ($chunks as $chunk) {
                $toInsert = [];

                foreach ($chunk as $row) {
                    try {
                        $payload = [];
                        foreach ($headerKeys as $idx => $key) {
                            $payload[$key] = $row[$idx] ?? null;
                        }

                        $unitCode = $this->extractSpecial($payload, 'unit_code');
                        $unitName = $this->extractSpecial($payload, 'unit_name');
                        $x = $this->normalizeCoordinate($this->extractSpecial($payload, 'x'));
                        $y = $this->normalizeCoordinate($this->extractSpecial($payload, 'y'));

                        $toInsert[] = [
                            'excel_module_id' => $module->id,
                            'excel_import_batch_id' => $batch->id,
                            'unit_code' => $unitCode !== null && $unitCode !== '' ? mb_substr((string) $unitCode, 0, 50) : null,
                            'unit_name' => $unitName !== null && $unitName !== '' ? mb_substr((string) $unitName, 0, 255) : null,
                            'x_coordinate' => $x,
                            'y_coordinate' => $y,
                            'data' => json_encode($payload, JSON_UNESCAPED_UNICODE),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        $imported++;
                    } catch (Throwable $e) {
                        $failed++;
                    }
                }

                if (!empty($toInsert)) {
                    ExcelRecord::insert($toInsert);
                }
            }

            // 6. Finalize
            $batch->update([
                'imported_rows' => $imported,
                'failed_rows' => $failed,
                'status' => 'completed',
            ]);

            $module->update([
                'row_count' => ExcelRecord::where('excel_module_id', $module->id)->count(),
                'last_imported_at' => now(),
                'last_headers' => $headers,
            ]);

            return $batch->fresh();
        });
    }

    // ─────────────────────────── Helpers ───────────────────────────

    private function readSystemSheet($spreadsheet): array
    {
        $sheet = $spreadsheet->getSheetByName(self::SYSTEM_SHEET);
        if (!$sheet) {
            return [];
        }

        $result = [];
        foreach ($sheet->toArray() as $row) {
            if (!empty($row[0])) {
                $result[trim((string) $row[0])] = $row[1] ?? null;
            }
        }
        return $result;
    }

    /**
     * Convert Arabic headers to clean keys while keeping original as value.
     * Example: "اسم خط الانتاج" → "اسم_خط_الانتاج"
     */
    private function normalizeHeaders(array $headers): array
    {
        $keys = [];
        $used = [];

        foreach ($headers as $i => $header) {
            $header = trim((string) $header);
            if ($header === '') {
                $key = "col_{$i}";
            } else {
                // Keep Arabic but make it safe for JSON keys
                $key = preg_replace('/\s+/', '_', $header);
                $key = preg_replace('/[^\p{L}\p{N}_]/u', '', $key);
            }

            // uniqueness
            $original = $key;
            $counter = 1;
            while (isset($used[$key])) {
                $key = $original . '_' . $counter++;
            }
            $used[$key] = true;
            $keys[$i] = $key;
        }

        return $keys;
    }

    private function extractSpecial(array $payload, string $type): mixed
    {
        $candidates = self::SPECIAL_COLUMNS[$type] ?? [];
        foreach ($candidates as $candidate) {
            // try exact and normalized
            foreach ($payload as $k => $v) {
                if ($k === $candidate || str_contains($k, $candidate)) {
                    return $v;
                }
            }
        }
        return null;
    }

    /**
     * Parse coordinate safely. Returns null if empty / non-numeric / extreme.
     */
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

        // إحداثيات جغرافية منطقية تقريباً
        if ($f < -180 || $f > 180) {
            return null;
        }

        return $f;
    }
}
