<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExcelRecord extends Model
{
    protected $fillable = [
        'excel_module_id',
        'excel_import_batch_id',
        'unit_code',
        'unit_name',
        'x_coordinate',
        'y_coordinate',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
        'x_coordinate' => 'float',
        'y_coordinate' => 'float',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(ExcelModule::class, 'excel_module_id');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(ExcelImportBatch::class, 'excel_import_batch_id');
    }

    /**
     * Get a value from the JSON data safely.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return data_get($this->data, $key, $default);
    }
}
