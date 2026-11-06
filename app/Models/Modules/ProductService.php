<?php

namespace App\Models\Modules;

use Illuminate\Database\Eloquent\Model;

class ProductService extends Model
{
    protected $table = 'products_services';

    protected $fillable = [
        'owner_organization',
        'location_scope',
        'governorate',
        'name',
        'unit_of_measure',
        'usage',
        'annual_production',
        'monthly_production',
        'current_demand',
        'target_market',
        'gov_beneficiaries',
        'main_raw_materials',
        'local_content_percent',
        'can_increase_production',
        'alternative_product',
        'production_cycle',
        'storage_duration',
        'storage_conditions',
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
