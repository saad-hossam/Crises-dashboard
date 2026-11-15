<?php

namespace App\Models\Modules;

use Illuminate\Database\Eloquent\Model;

class ProductiveCapacity extends Model
{
    protected $table = 'productive_capacities';

    protected $fillable = [
        'owner_organization',
        'location_scope',
        'governorate',
        'product_name',
        'unit_of_measure',
        'time_period',
        'design_capacity',
        'available_capacity',
        'actual_production',
        'required_labor',
        'required_raw_materials',
        'import_dependency_percent',
        'line_convertible',
        'alternative_product',
        'conversion_days',
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
