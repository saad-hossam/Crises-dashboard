<?php

namespace App\Models\Modules;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $table = 'sites';

    protected $fillable = [
        'owner_organization',
        'location_scope',
        'governorate',
        'site_name',
        'site_type',
        'owner_company',
        'operator_company',
        'area',
        'area_unit',
        'employees_count',
        'daily_operating_hours',
        'shifts_count',
        'site_manager',
        'site_manager_phone',
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
