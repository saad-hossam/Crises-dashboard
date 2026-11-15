
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('industrial_facilities', function (Blueprint $table) {
            $table->id();

            // Ownership and location
            $table->string('owner_organization')->nullable();
            $table->string('location_scope')->nullable();
            $table->string('governorate')->nullable();
            $table->string('facility_number')->nullable();
            $table->string('industrial_sector')->nullable();

            // Facility information
            $table->string('facility_name')->nullable();
            $table->string('trade_name')->nullable();
            $table->string('investor_code')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('industrial_register_number')->nullable();

            // Industrial register dates
            $table->string('register_issue_date')->nullable();
            $table->string('register_expiry_date')->nullable();

            // Industrial activities
            $table->text('main_industrial_activity')->nullable();
            $table->string('main_activity_code')->nullable();
            $table->text('sub_industrial_activity')->nullable();
            $table->string('sub_activity_code')->nullable();

            // Commercial information
            $table->string('commercial_register_capital')->nullable();

            // Address and industrial region
            $table->string('city')->nullable();
            $table->string('industrial_region')->nullable();
            $table->string('industrial_zone_status')->nullable();
            $table->text('address')->nullable();

            // Contact information
            $table->string('contact_person')->nullable();
            $table->string('contact_job_title')->nullable();
            $table->string('official_phone')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->string('email')->nullable();

            // Operation and production
            $table->string('operating_status')->nullable();
            $table->string('product_name')->nullable();
            $table->string('product_code')->nullable();
            $table->string('annual_production_quantity')->nullable();
            $table->string('production_unit')->nullable();
            $table->text('raw_materials')->nullable();

            // Workforce and consumption
            $table->string('total_employees')->nullable();
            $table->string('power_capacity_kw')->nullable();
            $table->string('annual_electricity_consumption')->nullable();
            $table->string('annual_water_consumption')->nullable();
            $table->string('main_fuel_type')->nullable();
            $table->string('annual_fuel_consumption')->nullable();

            // License information
            $table->string('license_number')->nullable();
            $table->string('license_issue_date')->nullable();
            $table->string('area_sqm')->nullable();
            $table->string('license_type')->nullable();

            // Geographic coordinates
            $table->decimal('longitude_x', 11, 7)->nullable();
            $table->decimal('latitude_y', 11, 7)->nullable();

            // Organizational information
            $table->string('organizational_unit')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('industrial_facilities');
    }
};
