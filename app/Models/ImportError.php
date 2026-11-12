<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportError extends Model
{
    public const TYPE_VALIDATION = 'validation';

    public const TYPE_DATABASE = 'database';

    public const TYPE_SYSTEM = 'system';

    protected $fillable = [
        'import_batch_id',
        'excel_row',
        'column_name',
        'error_type',
        'error_message',
        'row_data',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'excel_row' => 'integer',
            'row_data' => 'array',
            'metadata' => 'array',
        ];
    }

    /**
     * Import batch associated with this error.
     */
    public function importBatch(): BelongsTo
    {
        return $this->belongsTo(ImportBatch::class);
    }
}
