<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * جداول مخصصة لكل موديول Excel — للتحليل والـ charts
 * production_lines موجود مسبقاً
 */
return new class extends Migration
{
    public function up(): void
    {
        // جعل unit_id اختياري + إضافة unit_code في production_lines
        if (Schema::hasTable('production_lines')) {
            try {
                Schema::table('production_lines', function (Blueprint $table) {
                    try { $table->dropForeign(['unit_id']); } catch (\Throwable $e) {}
                });
            } catch (\Throwable $e) {}

            \Illuminate\Support\Facades\DB::statement('ALTER TABLE production_lines MODIFY unit_id BIGINT UNSIGNED NULL');

            Schema::table('production_lines', function (Blueprint $table) {
                if (!Schema::hasColumn('production_lines', 'unit_code')) {
                    $table->string('unit_code', 50)->nullable()->index();
                }
                if (!Schema::hasColumn('production_lines', 'unit_name')) {
                    $table->string('unit_name')->nullable();
                }
            });
        }

        $common = function (Blueprint $table) {
            $table->id();
            $table->string('owner_organization')->nullable()->index();
            $table->string('location_scope')->nullable()->index();
            $table->string('governorate')->nullable()->index();
            $table->text('company_name')->nullable();            // ← text
            $table->text('address')->nullable();
            $table->string('operating_status')->nullable()->index();
            $table->string('readiness_level')->nullable()->index();
            $table->string('last_updated_date')->nullable();
            $table->text('data_entry_responsible')->nullable();   // ← text
            $table->double('x_coordinate')->nullable();
            $table->double('y_coordinate')->nullable();
            $table->string('unit_name')->nullable();
            $table->string('unit_code', 50)->nullable()->index();
            $table->unsignedBigInteger('import_batch_id')->nullable()->index();
            $table->timestamps();
        };

        if (!Schema::hasTable('companies')) {
            Schema::create('companies', function (Blueprint $table) use ($common) {
                $common($table);
                $table->string('name_ar')->nullable()->index();
                $table->string('name_en')->nullable();
                $table->string('trade_name')->nullable();
                $table->string('abbreviation')->nullable();
                $table->string('company_type')->nullable()->index();
                $table->string('legal_form')->nullable();
                $table->text('headquarters')->nullable();            // ← text
                $table->text('website')->nullable();                 // ← text
                $table->string('phone')->nullable();
                $table->text('board_chairman')->nullable();          // ← text
                $table->text('ceo')->nullable();                     // ← text
                $table->text('founding_law')->nullable();            // ← text
                $table->text('affiliation')->nullable();             // ← text
                $table->string('state_ownership_percent')->nullable();
                $table->string('authorized_capital')->nullable();
                $table->string('email')->nullable();
                $table->string('issued_capital')->nullable();
                $table->string('employees_count')->nullable();
                $table->string('subsidiaries_count')->nullable();
                $table->string('sites_count')->nullable();
                $table->string('paid_capital')->nullable();
                $table->text('main_activity')->nullable();
                $table->text('sub_activities')->nullable();
                $table->text('founded_at')->nullable();
                $table->text('commercial_register')->nullable();     // ← text
                $table->string('tax_id')->nullable();
                $table->text('foreign_branches')->nullable();
                $table->text('stock_listing_status')->nullable();    // ← text
            });
        }

        if (!Schema::hasTable('sites')) {
            Schema::create('sites', function (Blueprint $table) use ($common) {
                $common($table);
                $table->string('site_name')->nullable()->index();
                $table->string('site_type')->nullable()->index();
                $table->text('owner_company')->nullable();           // ← text
                $table->text('operator_company')->nullable();        // ← text
                $table->text('area')->nullable();                    // ← text
                $table->string('area_unit')->nullable();
                $table->string('employees_count')->nullable();
                $table->text('daily_operating_hours')->nullable();   // ← text
                $table->string('shifts_count')->nullable();
                $table->text('site_manager')->nullable();            // ← text
                $table->string('site_manager_phone')->nullable();
            });
        }

        if (!Schema::hasTable('productive_capacities')) {
            Schema::create('productive_capacities', function (Blueprint $table) use ($common) {
                $common($table);
                $table->string('product_name')->nullable()->index();
                $table->text('unit_of_measure')->nullable();         // ← text
                $table->text('time_period')->nullable();             // ← text
                $table->text('design_capacity')->nullable();         // ← text
                $table->text('available_capacity')->nullable();      // ← text
                $table->text('actual_production')->nullable();       // ← text
                $table->text('required_labor')->nullable();          // ← text
                $table->text('required_raw_materials')->nullable();
                $table->text('import_dependency_percent')->nullable(); // ← text
                $table->text('line_convertible')->nullable();        // ← text
                $table->text('alternative_product')->nullable();     // ← text
                $table->text('conversion_days')->nullable();         // ← text
            });
        }

        if (!Schema::hasTable('warehouses')) {
            Schema::create('warehouses', function (Blueprint $table) use ($common) {
                $common($table);
                $table->string('warehouse_name')->nullable()->index();
                $table->string('warehouse_type')->nullable()->index();
                $table->text('area')->nullable();                    // ← text
                $table->text('design_capacity')->nullable();         // ← text
                $table->text('used_capacity')->nullable();           // ← text
                $table->text('available_capacity')->nullable();      // ← text
                $table->string('capacity_unit')->nullable();
                $table->text('materials_type')->nullable();          // ← text
                $table->text('refrigeration')->nullable();           // ← text
                $table->text('civil_defense_status')->nullable();    // ← text
            });
        }

        if (!Schema::hasTable('products_services')) {
            Schema::create('products_services', function (Blueprint $table) use ($common) {
                $common($table);
                $table->string('name')->nullable()->index();
                $table->text('unit_of_measure')->nullable();             // ← text
                $table->text('usage')->nullable();
                $table->text('annual_production')->nullable();           // ← text
                $table->text('monthly_production')->nullable();          // ← text
                $table->text('current_demand')->nullable();              // ← text
                $table->text('target_market')->nullable();               // ← text
                $table->text('gov_beneficiaries')->nullable();
                $table->text('main_raw_materials')->nullable();
                $table->text('local_content_percent')->nullable();       // ← text (سبب الخطأ)
                $table->text('can_increase_production')->nullable();     // ← text
                $table->text('alternative_product')->nullable();         // ← text
                $table->text('production_cycle')->nullable();            // ← text
                $table->text('storage_duration')->nullable();            // ← text
                $table->text('storage_conditions')->nullable();
            });
        }

        if (!Schema::hasTable('facilities_properties')) {
            Schema::create('facilities_properties', function (Blueprint $table) use ($common) {
                $common($table);
                $table->string('facility_name')->nullable()->index();
                $table->string('facility_type')->nullable()->index();
                $table->text('usage')->nullable();
                $table->string('ownership_type')->nullable();
                $table->text('total_area')->nullable();              // ← text
                $table->text('built_area')->nullable();              // ← text
                $table->string('buildings_count')->nullable();
                $table->string('floors_count')->nullable();
                $table->text('construction_year')->nullable();       // ← text
                $table->text('estimated_value')->nullable();         // ← text
                $table->text('structural_condition')->nullable();    // ← text
                $table->text('capacity')->nullable();                // ← text
                $table->text('electricity_source')->nullable();      // ← text
                $table->text('water_source')->nullable();            // ← text
                $table->text('insurance_status')->nullable();        // ← text
            });
        }

        if (!Schema::hasTable('mutual_dependencies')) {
            Schema::create('mutual_dependencies', function (Blueprint $table) use ($common) {
                $common($table);
                $table->string('dependence_type')->nullable()->index();
                $table->text('counterpart')->nullable();             // ← text
                $table->text('dependence_degree')->nullable();       // ← text
                $table->text('has_alternative')->nullable();         // ← text
                $table->string('alternatives_count')->nullable();
                $table->text('outage_tolerance_hours')->nullable();  // ← text
                $table->text('alternative_lead_hours')->nullable();  // ← text
                $table->text('outage_impact')->nullable();
                $table->text('mitigation_action')->nullable();
            });
        }

        if (!Schema::hasTable('module_import_batches')) {
            Schema::create('module_import_batches', function (Blueprint $table) {
                $table->id();
                $table->string('module_code')->index();
                $table->string('module_name_ar')->nullable();
                $table->string('original_filename')->nullable();
                $table->string('file_hash', 64)->nullable()->index();
                $table->unsignedInteger('total_rows')->default(0);
                $table->unsignedInteger('imported_rows')->default(0);
                $table->unsignedInteger('skipped_rows')->default(0);
                $table->string('status')->default('completed');
                $table->text('error_message')->nullable();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->boolean('replaced_existing')->default(false);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('module_import_batches');
        Schema::dropIfExists('mutual_dependencies');
        Schema::dropIfExists('facilities_properties');
        Schema::dropIfExists('products_services');
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('productive_capacities');
        Schema::dropIfExists('sites');
        Schema::dropIfExists('companies');
    }
};