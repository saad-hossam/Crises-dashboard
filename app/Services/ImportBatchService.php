<?php

namespace App\Services;

use App\Models\ImportBatch;
use App\Models\ImportError;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use LogicException;

class ImportBatchService
{
    /**
     * Find an existing import batch using SHA-256.
     */
    public function findByHash(string $fileHash): ?ImportBatch
    {
        $this->validateHash($fileHash);

        return ImportBatch::query()
            ->where('file_hash', strtolower($fileHash))
            ->first();
    }

    /**
     * Prevent importing the same exact file more than once.
     *
     * Any existing batch blocks re-import, including failed batches,
     * because a failed batch may have partially inserted records.
     */
    public function assertFileNotImported(string $fileHash): void
    {
        $existingBatch = $this->findByHash($fileHash);

        if ($existingBatch) {
            throw ValidationException::withMessages([
                'excel_file' => sprintf(
                    'هذا الملف سبق تسجيله في عملية استيراد رقم %d. حالة العملية: %s.',
                    $existingBatch->id,
                    $existingBatch->status
                ),
            ]);
        }
    }

    /**
     * Create a pending import batch.
     *
     * Required attributes:
     * file_name, file_path, file_hash, destination_table.
     */
    public function createPending(array $data): ImportBatch
    {
        $fileHash = strtolower(
            trim((string) ($data['file_hash'] ?? ''))
        );

        $this->validateHash($fileHash);

        $requiredFields = [
            'file_name',
            'file_path',
            'destination_table',
        ];

        foreach ($requiredFields as $field) {
            if (
                !isset($data[$field])
                || trim((string) $data[$field]) === ''
            ) {
                throw ValidationException::withMessages([
                    $field => "الحقل {$field} مطلوب لإنشاء دفعة الاستيراد.",
                ]);
            }
        }

        $destinationTable = (string) $data['destination_table'];

        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $destinationTable)) {
            throw new InvalidArgumentException(
                'Invalid destination table name.'
            );
        }

        try {
            return DB::transaction(function () use (
                $data,
                $fileHash,
                $destinationTable
            ) {
                $this->assertFileNotImported($fileHash);

                return ImportBatch::create([
                    'file_name' => basename($data['file_name']),
                    'file_path' => $data['file_path'],
                    'file_hash' => $fileHash,
                    'destination_table' => $destinationTable,

                    'total_rows' => 0,
                    'imported_rows' => 0,
                    'skipped_rows' => 0,
                    'failed_rows' => 0,

                    'status' => ImportBatch::STATUS_PENDING,

                    'created_by' => $data['created_by'] ?? null,

                    'started_at' => null,
                    'completed_at' => null,

                    'metadata' => $data['metadata'] ?? null,
                ]);
            });

        } catch (QueryException $exception) {
            // Handle concurrent duplicate-file requests.
            if ($this->findByHash($fileHash)) {
                throw ValidationException::withMessages([
                    'excel_file' => 'تم تسجيل هذا الملف بالفعل ولا يمكن استيراده مرة أخرى.',
                ]);
            }

            throw $exception;
        }
    }

    /**
     * Start processing a pending batch.
     */
    public function start(
        ImportBatch $batch,
        int $totalRows
    ): ImportBatch {
        if ($totalRows < 0) {
            throw new InvalidArgumentException(
                'Total rows cannot be negative.'
            );
        }

        return DB::transaction(function () use ($batch, $totalRows) {
            $lockedBatch = ImportBatch::query()
                ->lockForUpdate()
                ->findOrFail($batch->id);

            if ($lockedBatch->status !== ImportBatch::STATUS_PENDING) {
                throw new LogicException(
                    'Only pending import batches can be started.'
                );
            }

            $lockedBatch->update([
                'status' => ImportBatch::STATUS_PROCESSING,
                'total_rows' => $totalRows,
                'started_at' => now(),
                'completed_at' => null,
            ]);

            return $lockedBatch->fresh();
        });
    }

    /**
     * Increase successfully imported rows.
     */
    public function incrementImported(
        ImportBatch $batch,
        int $count = 1
    ): ImportBatch {
        return $this->incrementCounter(
            $batch,
            'imported_rows',
            $count
        );
    }

    /**
     * Increase skipped rows.
     */
    public function incrementSkipped(
        ImportBatch $batch,
        int $count = 1
    ): ImportBatch {
        return $this->incrementCounter(
            $batch,
            'skipped_rows',
            $count
        );
    }

    /**
     * Increase failed rows.
     *
     * Call once per failed Excel row, not once per error message.
     */
    public function incrementFailed(
        ImportBatch $batch,
        int $count = 1
    ): ImportBatch {
        return $this->incrementCounter(
            $batch,
            'failed_rows',
            $count
        );
    }

    /**
     * Record one or more errors for an Excel row.
     *
     * This method does not increment failed_rows automatically,
     * because one row may contain multiple error messages.
     */
    public function recordError(
        ImportBatch $batch,
        int $excelRow,
        string $errorMessage,
        ?string $columnName = null,
        string $errorType = ImportError::TYPE_VALIDATION,
        ?array $rowData = null,
        ?array $metadata = null
    ): ImportError {
        if ($excelRow < 1) {
            throw new InvalidArgumentException(
                'Excel row number must be positive.'
            );
        }

        if (trim($errorMessage) === '') {
            throw new InvalidArgumentException(
                'Error message cannot be empty.'
            );
        }

        $allowedTypes = [
            ImportError::TYPE_VALIDATION,
            ImportError::TYPE_DATABASE,
            ImportError::TYPE_SYSTEM,
        ];

        if (!in_array($errorType, $allowedTypes, true)) {
            throw new InvalidArgumentException(
                'Invalid import error type.'
            );
        }

        return DB::transaction(function () use (
            $batch,
            $excelRow,
            $errorMessage,
            $columnName,
            $errorType,
            $rowData,
            $metadata
        ) {
            $this->getLockedProcessingBatch($batch);

            return ImportError::create([
                'import_batch_id' => $batch->id,
                'excel_row' => $excelRow,
                'column_name' => $columnName,
                'error_type' => $errorType,
                'error_message' => $errorMessage,
                'row_data' => $rowData,
                'metadata' => $metadata,
            ]);
        });
    }

    /**
     * Complete an import batch successfully.
     */
    public function complete(ImportBatch $batch): ImportBatch
    {
        return DB::transaction(function () use ($batch) {
            $lockedBatch = $this->getLockedProcessingBatch($batch);

            $processedRows =
                (int) $lockedBatch->imported_rows
                + (int) $lockedBatch->skipped_rows
                + (int) $lockedBatch->failed_rows;

            if ($processedRows > (int) $lockedBatch->total_rows) {
                throw new LogicException(
                    'Processed rows exceed the total Excel rows.'
                );
            }

            $lockedBatch->update([
                'status' => ImportBatch::STATUS_COMPLETED,
                'completed_at' => now(),
            ]);

            return $lockedBatch->fresh();
        });
    }

    /**
     * Mark a batch as failed.
     *
     * Existing counters and error records are preserved.
     */
    public function fail(
        ImportBatch $batch,
        ?string $failureMessage = null
    ): ImportBatch {
        return DB::transaction(function () use (
            $batch,
            $failureMessage
        ) {
            $lockedBatch = ImportBatch::query()
                ->lockForUpdate()
                ->findOrFail($batch->id);

            if (
                !in_array(
                    $lockedBatch->status,
                    [
                        ImportBatch::STATUS_PENDING,
                        ImportBatch::STATUS_PROCESSING,
                    ],
                    true
                )
            ) {
                throw new LogicException(
                    'This import batch cannot be marked as failed.'
                );
            }

            $metadata = $lockedBatch->metadata ?? [];

            if ($failureMessage !== null) {
                $metadata['failure_message'] = $failureMessage;
            }

            $lockedBatch->update([
                'status' => ImportBatch::STATUS_FAILED,
                'completed_at' => now(),
                'metadata' => $metadata,
            ]);

            return $lockedBatch->fresh();
        });
    }

    /**
     * Return import batch statistics.
     */
    public function getSummary(ImportBatch $batch): array
    {
        $batch->refresh();

        return [
            'batch_id' => $batch->id,
            'file_name' => $batch->file_name,
            'file_hash' => $batch->file_hash,
            'destination_table' => $batch->destination_table,
            'status' => $batch->status,
            'total_rows' => (int) $batch->total_rows,
            'imported_rows' => (int) $batch->imported_rows,
            'skipped_rows' => (int) $batch->skipped_rows,
            'failed_rows' => (int) $batch->failed_rows,
            'started_at' => $batch->started_at,
            'completed_at' => $batch->completed_at,
        ];
    }

    /**
     * Atomically increment a batch counter.
     */
    private function incrementCounter(
        ImportBatch $batch,
        string $column,
        int $count
    ): ImportBatch {
        if ($count <= 0) {
            throw new InvalidArgumentException(
                'Counter increment must be greater than zero.'
            );
        }

        $allowedCounters = [
            'imported_rows',
            'skipped_rows',
            'failed_rows',
        ];

        if (!in_array($column, $allowedCounters, true)) {
            throw new InvalidArgumentException(
                'Invalid import batch counter.'
            );
        }

        return DB::transaction(function () use (
            $batch,
            $column,
            $count
        ) {
            $lockedBatch = $this->getLockedProcessingBatch($batch);

            $newTotal =
                (int) $lockedBatch->imported_rows
                + (int) $lockedBatch->skipped_rows
                + (int) $lockedBatch->failed_rows
                + $count;

            if ($newTotal > (int) $lockedBatch->total_rows) {
                throw new LogicException(
                    'Cannot process more rows than the batch total.'
                );
            }

            $lockedBatch->increment($column, $count);

            return $lockedBatch->fresh();
        });
    }

    /**
     * Lock and validate a processing batch.
     */
    private function getLockedProcessingBatch(
        ImportBatch $batch
    ): ImportBatch {
        $lockedBatch = ImportBatch::query()
            ->lockForUpdate()
            ->findOrFail($batch->id);

        if ($lockedBatch->status !== ImportBatch::STATUS_PROCESSING) {
            throw new LogicException(
                'The import batch is not currently processing.'
            );
        }

        return $lockedBatch;
    }

    /**
     * Validate SHA-256 format.
     */
    private function validateHash(string $fileHash): void
    {
        if (!preg_match('/\A[a-f0-9]{64}\z/i', trim($fileHash))) {
            throw new InvalidArgumentException(
                'Invalid SHA-256 file hash.'
            );
        }
    }
}
