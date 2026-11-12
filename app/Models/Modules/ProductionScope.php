<?php

namespace App\Models\Modules;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionScope extends Model
{
    use SoftDeletes;

    protected $table = 'production_scopes';

    protected $fillable = [
        'owner_organization','location_scope','governorate','company_name','address',
        'operating_status','readiness_level','last_updated_date','data_entry_responsible',
        'x_coordinate','y_coordinate','unit_name','unit_code','unit_id',
        'module_import_batch_id','created_by','updated_by',
        'line_name','product_name','manufacturer',
        'line_case','operational_case','notes',
    ];

    protected $casts = [
        'x_coordinate' => 'float',
        'y_coordinate' => 'float',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'deleted_at'   => 'datetime',
    ];

    public function importBatch(): BelongsTo
    {
        return $this->belongsTo(ModuleImportBatch::class, 'module_import_batch_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Unit::class, 'unit_id');
    }
}