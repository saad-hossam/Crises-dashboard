<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModuleImportBatch extends Model
{
    protected $fillable = [
        'module_code', 'module_name_ar', 'original_filename', 'file_hash',
        'total_rows', 'imported_rows', 'skipped_rows', 'status', 'error_message',
        'user_id', 'replaced_existing',
    ];

    protected $casts = [
        'replaced_existing' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
