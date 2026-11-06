<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionLine extends Model
{
    use SoftDeletes;

    protected $fillable = [
        // Basic Information
        'owner_organization',
        'location_scope',
        'governorate',
        'name',
        'product_name',
        'manufacturer',

        // Production Capacity
        'operating_year',
        'design_capacity',
        'capacity_unit',
        'current_operational_capacity',
        'annual_production_capacity',
        'actual_production',
        'utilization_rate',

        // Operation and Workforce
        'shifts_count',
        'daily_operating_hours',
        'workers_count',
        'line_status',
        'expected_lifetime_years',

        // Maintenance and Development
        'last_maintenance_date',
        'next_maintenance_date',
        'production_increase_possibility',
        'production_increase_lead_time_days',
        'development_type',
        'development_completion_date',

        // Company Information
        'company_name',
        'address',
        'operating_status',
        'readiness_level',

        // Data Entry Information
        'last_updated_date',
        'data_entry_responsible',

        // Geographic Coordinates
        'x_coordinate',
        'y_coordinate',

        // Relationships and Tracking
        'unit_id',
        'created_by',
        'updated_by',
        'import_batch_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
