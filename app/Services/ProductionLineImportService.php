<?php

namespace App\Services;

use App\Models\ImportBatch;
use App\Models\ImportError;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class ProductionLineImportService
{
    private const DESTINATION_TABLE = 'production_lines';

    private const CHUNK_SIZE = 500;

    /**
     * All Excel columns mapped to production_lines.
     */
    private const DATA_COLUMNS = [
        'owner_organization',
        'location_scope',
        'governorate',
        'name',
        'product_name',
        'manufacturer',
        'operating_year',
        'design_capacity',
        'capacity_unit',
        'current_operational_capacity',
        'annual_production_capacity',
        'actual_production',
        'utilization_rate',
        'shifts_count',
        'daily_operating_hours',
        'workers_count',
        'line_status',
        'expected_lifetime_years',
        'last_maintenance_date',
        'next_maintenance_date',
        'production_increase_possibility',
        'production_increase_lead_time_days',
        'development_type',
        'development_completion_date',
        'company_name',
        'address',
        'operating_status',
        'readiness_level',
        'last_updated_date',
        'data_entry_responsible',
        'x_coordinate',
        'y_coordinate',
        'unit_id',
    ];

    public function __construct(
        private readonly ImportBatchService $batches
    ) {
    }

    /**
     * Import the approved preview into production_lines.
     *
     * Strategy:
     * - Insert Only.
     * - SHA-256 duplicate-file prevention.
     * - Chunked insertion.
     * - No row-level duplicate restrictions.
     * - Soft Delete remains supported.
     */
    public function import(
        string $storedPath,
        int $userId
    ): ImportBatch {

        if ($userId <= 0) {
            throw new RuntimeException(
                'An authenticated importing user is required.'
            );
        }

        $storedPath = str_replace('\\', '/', $storedPath);

        /*
         * Only accept uploaded files from our private directory.
         */
        if (
            !str_starts_with(
                $storedPath,
                'production-lines/uploads/'
            )
        ) {
            throw new RuntimeException(
                'Invalid uploaded Excel file location.'
            );
        }

        $disk = Storage::disk('local');

        $sourcePath = $disk->path($storedPath);

        if (!$disk->exists($storedPath) || !is_file($sourcePath)) {
            throw new RuntimeException(
                'The uploaded Excel file no longer exists.'
            );
        }

        /*
         * Calculate the real SHA-256 hash.
         */
        $fileHash = hash_file('sha256', $sourcePath);

        if ($fileHash === false) {
            throw new RuntimeException(
                'Unable to calculate the uploaded file SHA-256.'
            );
        }

        /*
         * Verify that the file is stored under its own hash.
         */
        $storedDirectoryHash = basename(dirname($storedPath));

        if (!hash_equals($storedDirectoryHash, $fileHash)) {
            throw new RuntimeException(
                'Uploaded file integrity verification failed.'
            );
        }

        /*
         * Prevent exact duplicate file imports.
         */
        $this->batches->assertFileNotImported($fileHash);

        /*
         * Load the approved preview.
         */
        $previewPath = storage_path(
            'app/previews/production_lines_preview.json'
        );

        if (!is_file($previewPath)) {
            throw new RuntimeException(
                'Production Lines preview file was not found.'
            );
        }

        $payload = json_decode(
            file_get_contents($previewPath),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        if (
            !isset($payload['records'])
            || !is_array($payload['records'])
        ) {
            throw new RuntimeException(
                'Invalid production lines preview structure.'
            );
        }

        /*
         * Verify that preview belongs to the uploaded workbook.
         */
        $sourceMetadata = $payload['metadata']['source'] ?? [];

        $previewFileName = $sourceMetadata['file_name'] ?? null;

        if (
            $previewFileName !== null
            && $previewFileName !== basename($storedPath)
        ) {
            throw new RuntimeException(
                'The preview does not belong to the currently uploaded file.'
            );
        }

        $records = array_values($payload['records']);

        if (count($records) === 0) {
            throw new RuntimeException(
                'There are no records available for import.'
            );
        }

        /*
         * Reject blocked records.
         * Warning records are allowed.
         */
        $blockedRecords = array_filter(
            $records,
            fn ($record) =>
                !is_array($record)
                || ($record['status'] ?? null) === 'blocked'
        );

        if (count($blockedRecords) > 0) {
            throw ValidationException::withMessages([
                'excel_file' =>
                    'لا يمكن الاستيراد لوجود سجلات مرفوضة. '
                    . 'يجب مراجعة الأخطاء أولاً.',
            ]);
        }

        /*
         * Validate all unit IDs before creating the batch.
         */
        $unitIds = DB::table('units')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        foreach ($records as $index => $record) {
            $this->validateRecord(
                $record,
                $index,
                $unitIds
            );
        }

        /*
         * Create the import batch.
         */
        $batch = $this->batches->createPending([
            'file_name' => basename($sourcePath),
            'file_path' => $storedPath,
            'file_hash' => $fileHash,
            'destination_table' => self::DESTINATION_TABLE,
            'created_by' => $userId,

            'metadata' => [
                'strategy' => 'insert_only',
                'chunk_size' => self::CHUNK_SIZE,
                'preview_summary' => $payload['summary'] ?? [],
                'source_metadata' => $sourceMetadata,
            ],
        ]);

        try {
            /*
             * Start the batch.
             */
            $batch = $this->batches->start(
                $batch,
                count($records)
            );

            /*
             * Prepare and insert records in chunks.
             */
            foreach (
                array_chunk($records, self::CHUNK_SIZE)
                as $chunk
            ) {
                $entries = [];

                foreach ($chunk as $record) {
                    $data = $record['data'];

                    $entries[] = [
                        'excel_row' => (int) $record['excel_row'],
                        'original_data' => $data,
                        'database_row' => $this->prepareDatabaseRow(
                            $data,
                            $batch,
                            $userId
                        ),
                    ];
                }

                $this->insertChunk($batch, $entries);
            }

            /*
             * Ensure every Excel row has a final result.
             */
            $batch->refresh();

            $processedRows =
                (int) $batch->imported_rows
                + (int) $batch->skipped_rows
                + (int) $batch->failed_rows;

            if ($processedRows !== count($records)) {
                throw new RuntimeException(
                    'The processed row count does not match the Excel record count.'
                );
            }

            /*
             * Complete the batch.
             */
            return $this->batches->complete($batch);

        } catch (Throwable $exception) {

            /*
             * Preserve the batch and its counters for auditing.
             */
            try {
                $batch->refresh();

                if (in_array(
                    $batch->status,
                    [
                        ImportBatch::STATUS_PENDING,
                        ImportBatch::STATUS_PROCESSING,
                    ],
                    true
                )) {
                    $this->batches->fail(
                        $batch,
                        $exception->getMessage()
                    );
                }
            } catch (Throwable $statusException) {
                report($statusException);
            }

            throw $exception;
        }
    }

    /**
     * Validate an individual preview record.
     */
    private function validateRecord(
        array $record,
        int $index,
        array $unitIds
    ): void {

        $excelRow = $record['excel_row'] ?? ($index + 2);

        if (!in_array(
            $record['status'] ?? null,
            ['valid', 'warning'],
            true
        )) {
            throw ValidationException::withMessages([
                'excel_file' =>
                    "حالة السجل في صف Excel رقم {$excelRow} غير صالحة للاستيراد.",
            ]);
        }

        if (!isset($record['data']) || !is_array($record['data'])) {
            throw new RuntimeException(
                "Invalid record data at Excel row {$excelRow}."
            );
        }

        $data = $record['data'];

        $unitId = $data['unit_id'] ?? null;

        if (
            $unitId === null
            || $unitId === ''
            || !is_numeric($unitId)
            || (float) $unitId <= 0
            || (float) $unitId != (int) $unitId
        ) {
            throw ValidationException::withMessages([
                'excel_file' =>
                    "كود الوحدة غير صالح في صف Excel رقم {$excelRow}.",
            ]);
        }

        if (!in_array((int) $unitId, $unitIds, true)) {
            throw ValidationException::withMessages([
                'excel_file' =>
                    "كود الوحدة {$unitId} غير موجود في قاعدة البيانات، صف Excel رقم {$excelRow}.",
            ]);
        }

        /*
         * The production line name is allowed to be null or empty.
         */
        foreach (self::DATA_COLUMNS as $column) {
            if (!array_key_exists($column, $data)) {
                continue;
            }

            $value = $data[$column];

            if (
                $value !== null
                && !is_scalar($value)
            ) {
                throw new RuntimeException(
                    "Unsupported value in column {$column}, Excel row {$excelRow}."
                );
            }
        }
    }

    /**
     * Convert a preview record to a database row.
     */
    private function prepareDatabaseRow(
        array $data,
        ImportBatch $batch,
        int $userId
    ): array {

        $now = now()->toDateTimeString();

        $row = [];

        foreach (self::DATA_COLUMNS as $column) {
            $value = $data[$column] ?? null;

            if ($column === 'unit_id') {
                $row[$column] = (int) $value;
                continue;
            }

            /*
             * All non-FK destination columns are TEXT,
             * LONGTEXT or VARCHAR.
             *
             * Keep null as null and preserve the textual value.
             */
            $row[$column] = $value === null
                ? null
                : (string) $value;
        }

        /*
         * Internal tracking fields.
         */
        $row['created_by'] = $userId;
        $row['updated_by'] = null;
        $row['import_batch_id'] = $batch->id;

        $row['created_at'] = $now;
        $row['updated_at'] = $now;

        return $row;
    }

    /**
     * Insert one chunk.
     *
     * Successful chunk:
     * insert and batch counter update happen in the same transaction.
     *
     * Failed chunk:
     * retry records individually to identify failed Excel rows.
     */
    private function insertChunk(
        ImportBatch $batch,
        array $entries
    ): void {

        $rows = array_column($entries, 'database_row');

        try {
            DB::transaction(function () use (
                $rows,
                $batch
            ) {
                DB::table(self::DESTINATION_TABLE)
                    ->insert($rows);

                $this->batches->incrementImported(
                    $batch,
                    count($rows)
                );
            });

            return;

        } catch (Throwable $chunkException) {

            report($chunkException);

            /*
             * The chunk transaction was rolled back.
             * Retry individually so each failure can be logged.
             */
            foreach ($entries as $entry) {
                $this->insertSingleRecord(
                    $batch,
                    $entry
                );
            }
        }
    }

    /**
     * Insert one record and record its failure if necessary.
     */
    private function insertSingleRecord(
        ImportBatch $batch,
        array $entry
    ): void {

        $row = $entry['database_row'];

        try {
            DB::transaction(function () use (
                $row,
                $batch
            ) {
                DB::table(self::DESTINATION_TABLE)
                    ->insert([$row]);

                $this->batches->incrementImported(
                    $batch,
                    1
                );
            });

        } catch (Throwable $exception) {

            report($exception);

            /*
             * Record failed row and its database error.
             */
            DB::transaction(function () use (
                $batch,
                $entry,
                $exception
            ) {
                $this->batches->incrementFailed($batch, 1);

                $this->batches->recordError(
                    $batch,
                    $entry['excel_row'],
                    $exception->getMessage(),
                    null,
                    ImportError::TYPE_DATABASE,
                    $entry['original_data'],
                    [
                        'exception_class' => get_class($exception),
                        'destination_table' => self::DESTINATION_TABLE,
                    ]
                );
            });
        }
    }
}
