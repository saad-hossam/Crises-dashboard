<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImportBatch extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'file_name',
        'file_path',
        'file_hash',
        'destination_table',
        'total_rows',
        'imported_rows',
        'skipped_rows',
        'failed_rows',
        'status',
        'created_by',
        'started_at',
        'completed_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'total_rows' => 'integer',
            'imported_rows' => 'integer',
            'skipped_rows' => 'integer',
            'failed_rows' => 'integer',
            'metadata' => 'array',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * User who initiated the import.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Production lines imported through this batch.
     */
    public function productionLines(): HasMany
    {
        return $this->hasMany(ProductionLine::class, 'import_batch_id');
    }

    /**
     * Check whether the batch has completed successfully.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check whether the batch failed.
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Check whether the batch is currently processing.
     */
    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }
    /**
 * Errors recorded during this import batch.
 */
public function errors(): HasMany
{
    return $this->hasMany(ImportError::class);
}
}
