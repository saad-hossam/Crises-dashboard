<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndustrialFacility extends Model
{
    protected $table = 'industrial_facilities';

    protected $fillable = [
        // Ownership and location
        'owner_organization',
        'location_scope',
        'governorate',
        'facility_number',
        'industrial_sector',

        // Facility information
        'facility_name',
        'trade_name',
        'investor_code',
        'tax_number',
        'industrial_register_number',

        // Industrial register dates
        'register_issue_date',
        'register_expiry_date',

        // Industrial activities
        'main_industrial_activity',
        'main_activity_code',
        'sub_industrial_activity',
        'sub_activity_code',

        // Commercial information
        'commercial_register_capital',

        // Address and industrial region
        'city',
        'industrial_region',
        'industrial_zone_status',
        'address',

        // Contact information
        'contact_person',
        'contact_job_title',
        'official_phone',
        'emergency_phone',
        'email',

        // Operation and production
        'operating_status',
        'product_name',
        'product_code',
        'annual_production_quantity',
        'production_unit',
        'raw_materials',

        // Workforce and consumption
        'total_employees',
        'power_capacity_kw',
        'annual_electricity_consumption',
        'annual_water_consumption',
        'main_fuel_type',
        'annual_fuel_consumption',

        // License information
        'license_number',
        'license_issue_date',
        'area_sqm',
        'license_type',

        // Geographic coordinates
        'longitude_x',
        'latitude_y',

        // Organizational information
        'organizational_unit',
    ];

    protected function casts(): array
    {
        return [
            'longitude_x' => 'decimal:7',
            'latitude_y' => 'decimal:7',
        ];
    }
}
