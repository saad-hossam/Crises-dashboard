<?php

namespace App\Services;

use Throwable;
use RuntimeException;
use App\Models\ImportBatch;
use App\Models\IndustrialFacility;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\IndustrialFacilityColumns;
use App\Imports\IndustrialFacilitiesImport;
use App\Imports\IndustrialFacilitiesPreviewImport;

class IndustrialFacilitiesImportService
{
    /**
     * Validate Excel file before importing.
     */
    public function validate(string $filePath): array
    {
        if (!is_file($filePath)) {
            throw new RuntimeException(
                "Excel file not found: {$filePath}"
            );
        }

        $preview = new IndustrialFacilitiesPreviewImport();

        Excel::import($preview, $filePath);

        $rows = $preview->rows;

        if (empty($rows)) {
            throw new RuntimeException(
                'The main worksheet is empty or missing.'
            );
        }

        $actualHeaders = array_map(
            fn ($value) => trim((string) $value),
            $rows[0]
        );

        $expectedHeaders = IndustrialFacilityColumns::expectedHeaders();

        if ($actualHeaders !== $expectedHeaders) {
            $differences = [];

            foreach ($expectedHeaders as $index => $expected) {
                $actual = $actualHeaders[$index] ?? '[MISSING]';

                if ($expected !== $actual) {
                    $differences[] = [
                        'column' => $index + 1,
                        'expected' => $expected,
                        'actual' => $actual,
                    ];
                }
            }

            throw new RuntimeException(
                'Excel headers do not match the expected template: ' .
                json_encode($differences, JSON_UNESCAPED_UNICODE)
            );
        }

        $dataRows = array_slice($rows, 1);

        $validRows = array_filter(
            $dataRows,
            function ($row) {
                foreach ($row as $value) {
                    if (
                        $value !== null &&
                        trim((string) $value) !== ''
                    ) {
                        return true;
                    }
                }

                return false;
            }
        );

        return [
            'valid' => true,
            'headers_count' => count($actualHeaders),
            'data_rows_count' => count($validRows),
        ];
    }

    /**
     * Import Excel file and record the operation in import_batches.
     */
    public function import(
        string $filePath,
        ?string $organizationalUnit = null
    ): array {
        // 1. Validate file.
        $validation = $this->validate($filePath);

        // 2. Calculate file metadata.
        $fileHash = hash_file('sha256', $filePath);

        if ($fileHash === false) {
            throw new RuntimeException(
                'Unable to calculate the file SHA-256 hash.'
            );
        }

        // 3. Prevent duplicate file registration.
        if (ImportBatch::where('file_hash', $fileHash)->exists()) {
            throw new RuntimeException(
                'This Excel file has already been registered in import_batches.'
            );
        }

        // 4. Check target table.
        if (IndustrialFacility::query()->exists()) {
            throw new RuntimeException(
                'Import cancelled: industrial_facilities already contains records.'
            );
        }

        // 5. Create import batch.
        $batch = ImportBatch::create([
            'original_filename' => basename($filePath),
            'stored_filename' => basename($filePath),
            'stored_path' => $filePath,
            'file_extension' => strtolower(
                pathinfo($filePath, PATHINFO_EXTENSION)
            ),
            'file_size' => filesize($filePath),
            'file_hash' => $fileHash,

            'target_table' => 'industrial_facilities',
            'selected_sheet' => 'البيانات',
            'organizational_unit' => $organizationalUnit,

            'total_rows' => $validation['data_rows_count'],
            'imported_rows' => 0,
            'failed_rows' => 0,

            'status' => 'processing',
            'started_at' => now(),
        ]);

        try {
            // 6. Import records inside a transaction.
            $importedCount = DB::transaction(
                function () use ($filePath, $organizationalUnit) {

                    // Recheck before inserting.
                    if (IndustrialFacility::query()->exists()) {
                        throw new RuntimeException(
                            'Target table is no longer empty.'
                        );
                    }

                    Excel::import(
                        new IndustrialFacilitiesImport(
                            $organizationalUnit
                        ),
                        $filePath
                    );

                    return IndustrialFacility::query()->count();
                }
            );

            // 7. Verify imported count.
            if ($importedCount !== $validation['data_rows_count']) {
                throw new RuntimeException(
                    "Import count mismatch. Expected: " .
                    $validation['data_rows_count'] .
                    ", Imported: {$importedCount}"
                );
            }

            // 8. Mark batch as completed.
            $batch->update([
                'status' => 'completed',
                'imported_rows' => $importedCount,
                'failed_rows' => 0,
                'error_message' => null,
                'completed_at' => now(),
            ]);

            return [
                'success' => true,
                'batch_id' => $batch->id,
                'validation' => $validation,
                'imported_rows' => $importedCount,
            ];

        } catch (Throwable $exception) {

            // 9. Record failure without hiding the original error.
            $batch->update([
                'status' => 'failed',
                'imported_rows' => 0,
                'failed_rows' => $validation['data_rows_count'],
                'error_message' => $exception->getMessage(),
                'completed_at' => now(),
            ]);

            throw $exception;
        }
    }
}
