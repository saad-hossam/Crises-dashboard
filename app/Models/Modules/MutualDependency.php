<?php

namespace App\Models\Modules;

use Illuminate\Database\Eloquent\Model;

class MutualDependency extends Model
{
    protected $table = 'mutual_dependencies';

    protected $fillable = [
        'owner_organization',
        'location_scope',
        'governorate',
        'dependence_type',
        'counterpart',
        'dependence_degree',
        'has_alternative',
        'alternatives_count',
        'outage_tolerance_hours',
        'alternative_lead_hours',
        'outage_impact',
        'mitigation_action',
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
