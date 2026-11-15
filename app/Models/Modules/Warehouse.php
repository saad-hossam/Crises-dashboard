<?php

namespace App\Models\Modules;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $table = 'warehouses';

    protected $fillable = [
        'owner_organization',
        'location_scope',
        'governorate',
        'warehouse_name',
        'warehouse_type',
        'area',
        'design_capacity',
        'used_capacity',
        'available_capacity',
        'capacity_unit',
        'materials_type',
        'refrigeration',
        'civil_defense_status',
        'company_name',
        'address',
        'operating_status',
        'readiness_level',
        'last_updated_date',
        'data_entry_responsible',
        'x_coordinate',
        'y_coordinate',
        'unit_name',
        'unit_code',
        'import_batch_id',
    ];

    protected $casts = [
        'x_coordinate' => 'float',
        'y_coordinate' => 'float',
    ];
}
