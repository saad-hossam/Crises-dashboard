<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExcelModule extends Model
{
    protected $fillable = [
        'code',
        'name_ar',
        'name_en',
        'template_version',
        'row_count',
        'last_imported_at',
        'last_headers',
    ];

    protected $casts = [
        'last_headers' => 'array',
        'last_imported_at' => 'datetime',
    ];

    public function batches(): HasMany
    {
        return $this->hasMany(ExcelImportBatch::class);
    }

    public function records(): HasMany
    {
        return $this->hasMany(ExcelRecord::class);
    }
}
