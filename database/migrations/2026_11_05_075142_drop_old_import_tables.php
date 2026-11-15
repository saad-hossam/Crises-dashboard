<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('import_batches');

        Schema::dropIfExists('industrial_facilities');
    }

    public function down(): void
    {
        Schema::create('industrial_facilities', function (Blueprint $table) {
            $table->id();

            $table->string('owner_organization')->nullable();
            $table->string('location_scope')->nullable();
            $table->string('governorate')->nullable();
            $table->string('facility_number')->nullable();
            $table->string('industrial_sector')->nullable();
            $table->string('facility_name')->nullable();
            $table->string('trade_name')->nullable();
            $table->string('investor_code')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('industrial_register_number')->nullable();
            $table->string('register_issue_date')->nullable();
            $table->string('register_expiry_date')->nullable();
            $table->text('main_industrial_activity')->nullable();
            $table->string('main_activity_code')->nullable();
            $table->text('sub_industrial_activity')->nullable();
            $table->string('sub_activity_code')->nullable();
            $table->string('commercial_register_capital')->nullable();
            $table->string('city')->nullable();
            $table->string('industrial_region')->nullable();
            $table->string('industrial_zone_status')->nullable();
            $table->text('address')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_job_title')->nullable();
            $table->string('official_phone')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->string('email')->nullable();
            $table->string('operating_status')->nullable();
            $table->string('product_name')->nullable();
            $table->string('product_code')->nullable();
            $table->string('annual_production_quantity')->nullable();
            $table->string('production_unit')->nullable();
            $table->text('raw_materials')->nullable();
            $table->string('total_employees')->nullable();
            $table->string('power_capacity_kw')->nullable();
            $table->string('annual_electricity_consumption')->nullable();
            $table->string('annual_water_consumption')->nullable();
            $table->string('main_fuel_type')->nullable();
            $table->string('annual_fuel_consumption')->nullable();
            $table->string('license_number')->nullable();
            $table->string('license_issue_date')->nullable();
            $table->string('area_sqm')->nullable();
            $table->string('license_type')->nullable();
            $table->decimal('longitude_x', 11, 7)->nullable();
            $table->decimal('latitude_y', 11, 7)->nullable();
            $table->string('organizational_unit')->nullable();

            $table->timestamps();
        });

        Schema::create('import_batches', function (Blueprint $table) {
            $table->id();

            $table->string('original_filename');
            $table->string('stored_filename');
            $table->string('stored_path');
            $table->string('file_extension', 10);
            $table->unsignedBigInteger('file_size')->default(0);
            $table->string('file_hash', 64)->unique();
            $table->string('target_table');
            $table->string('selected_sheet')->nullable();
            $table->string('organizational_unit')->nullable();
            $table->unsignedInteger('total_rows')->default(0);
            $table->unsignedInteger('imported_rows')->default(0);
            $table->unsignedInteger('failed_rows')->default(0);
            $table->string('status', 30)->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->index('file_hash');
        });
    }
};