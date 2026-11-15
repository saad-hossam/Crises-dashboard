<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExcelImportBatch extends Model
{
    protected $fillable = [
        'excel_module_id',
        'user_id',
        'original_filename',
        'stored_path',
        'file_hash',
        'total_rows',
        'imported_rows',
        'failed_rows',
        'status',
        'meta',
        'error_message',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(ExcelModule::class, 'excel_module_id');
    }

    public function records(): HasMany
    {
        return $this->hasMany(ExcelRecord::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
