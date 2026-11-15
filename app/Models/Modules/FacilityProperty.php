<?php

namespace App\Models\Modules;

use Illuminate\Database\Eloquent\Model;

class FacilityProperty extends Model
{
    protected $table = 'facilities_properties';

    protected $fillable = [
        'owner_organization',
        'location_scope',
        'governorate',
        'facility_name',
        'facility_type',
        'usage',
        'ownership_type',
        'total_area',
        'built_area',
        'buildings_count',
        'floors_count',
        'construction_year',
        'estimated_value',
        'structural_condition',
        'capacity',
        'electricity_source',
        'water_source',
        'insurance_status',
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
