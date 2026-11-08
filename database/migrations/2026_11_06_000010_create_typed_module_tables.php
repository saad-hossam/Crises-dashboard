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
                // فك الـ FK مؤقتاً إن وجد
                Schema::table('production_lines', function (Blueprint $table) {
                    try { $table->dropForeign(['unit_id']); } catch (\Throwable $e) {}
                });
            } catch (\Throwable $e) {}

            Schema::table('production_lines', function (Blueprint $table) {
                // nullable بدون doctrine change — نستخدم DB::statement
            });

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
            $table->string('company_name')->nullable();
            $table->text('address')->nullable();
            $table->string('operating_status')->nullable()->index();
            $table->string('readiness_level')->nullable()->index();
            $table->string('last_updated_date')->nullable();
            $table->string('data_entry_responsible')->nullable();
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
                $table->string('headquarters')->nullable();
                $table->string('website')->nullable();
                $table->string('phone')->nullable();
                $table->string('board_chairman')->nullable();
                $table->string('ceo')->nullable();
                $table->string('founding_law')->nullable();
                $table->string('affiliation')->nullable();
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
                $table->string('founded_at')->nullable();
                $table->string('commercial_register')->nullable();
                $table->string('tax_id')->nullable();
                $table->text('foreign_branches')->nullable();
                $table->string('stock_listing_status')->nullable();
            });
        }

        if (!Schema::hasTable('sites')) {
            Schema::create('sites', function (Blueprint $table) use ($common) {
                $common($table);
                $table->string('site_name')->nullable()->index();
                $table->string('site_type')->nullable()->index();
                $table->string('owner_company')->nullable();
                $table->string('operator_company')->nullable();
                $table->string('area')->nullable();
                $table->string('area_unit')->nullable();
                $table->string('employees_count')->nullable();
                $table->string('daily_operating_hours')->nullable();
                $table->string('shifts_count')->nullable();
                $table->string('site_manager')->nullable();
                $table->string('site_manager_phone')->nullable();
            });
        }

        if (!Schema::hasTable('productive_capacities')) {
            Schema::create('productive_capacities', function (Blueprint $table) use ($common) {
                $common($table);
                $table->string('product_name')->nullable()->index();
                $table->string('unit_of_measure')->nullable();
                $table->string('time_period')->nullable();
                $table->string('design_capacity')->nullable();
                $table->string('available_capacity')->nullable();
                $table->string('actual_production')->nullable();
                $table->string('required_labor')->nullable();
                $table->text('required_raw_materials')->nullable();
                $table->string('import_dependency_percent')->nullable();
                $table->string('line_convertible')->nullable();
                $table->string('alternative_product')->nullable();
                $table->string('conversion_days')->nullable();
            });
        }

        if (!Schema::hasTable('warehouses')) {
            Schema::create('warehouses', function (Blueprint $table) use ($common) {
                $common($table);
                $table->string('warehouse_name')->nullable()->index();
                $table->string('warehouse_type')->nullable()->index();
                $table->string('area')->nullable();
                $table->string('design_capacity')->nullable();
                $table->string('used_capacity')->nullable();
                $table->string('available_capacity')->nullable();
                $table->string('capacity_unit')->nullable();
                $table->string('materials_type')->nullable();
                $table->string('refrigeration')->nullable();
                $table->string('civil_defense_status')->nullable();
            });
        }

        if (!Schema::hasTable('products_services')) {
            Schema::create('products_services', function (Blueprint $table) use ($common) {
                $common($table);
                $table->string('name')->nullable()->index();
                $table->string('unit_of_measure')->nullable();
                $table->text('usage')->nullable();
                $table->string('annual_production')->nullable();
                $table->string('monthly_production')->nullable();
                $table->string('current_demand')->nullable();
                $table->string('target_market')->nullable();
                $table->text('gov_beneficiaries')->nullable();
                $table->text('main_raw_materials')->nullable();
                $table->string('local_content_percent')->nullable();
                $table->string('can_increase_production')->nullable();
                $table->string('alternative_product')->nullable();
                $table->string('production_cycle')->nullable();
                $table->string('storage_duration')->nullable();
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
                $table->string('total_area')->nullable();
                $table->string('built_area')->nullable();
                $table->string('buildings_count')->nullable();
                $table->string('floors_count')->nullable();
                $table->string('construction_year')->nullable();
                $table->string('estimated_value')->nullable();
                $table->string('structural_condition')->nullable();
                $table->string('capacity')->nullable();
                $table->string('electricity_source')->nullable();
                $table->string('water_source')->nullable();
                $table->string('insurance_status')->nullable();
            });
        }

        if (!Schema::hasTable('mutual_dependencies')) {
            Schema::create('mutual_dependencies', function (Blueprint $table) use ($common) {
                $common($table);
                $table->string('dependence_type')->nullable()->index();
                $table->string('counterpart')->nullable();
                $table->string('dependence_degree')->nullable();
                $table->string('has_alternative')->nullable();
                $table->string('alternatives_count')->nullable();
                $table->string('outage_tolerance_hours')->nullable();
                $table->string('alternative_lead_hours')->nullable();
                $table->text('outage_impact')->nullable();
                $table->text('mitigation_action')->nullable();
            });
        }

        // تتبع الاستيراد لكل موديول
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
                $table->string('status')->default('completed'); // completed|failed
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
