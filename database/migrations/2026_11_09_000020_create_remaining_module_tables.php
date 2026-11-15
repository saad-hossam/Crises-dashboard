<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * الجداول المخصصة للموديولات الـ 18 المتبقية
 * كل ملف Excel → جدول typed للتحليل والـ charts
 */
return new class extends Migration
{
    private function common(Blueprint $table): void
    {
        $table->id();
        $table->string('owner_organization')->nullable()->index();
        $table->string('location_scope')->nullable();
        $table->string('governorate')->nullable()->index();
        $table->string('company_name')->nullable()->index();
        $table->text('address')->nullable();
        $table->string('operating_status')->nullable()->index();
        $table->string('readiness_level')->nullable()->index();
        $table->string('last_updated_date')->nullable();
        $table->string('data_entry_responsible')->nullable();
        $table->string('x_coordinate')->nullable();
        $table->string('y_coordinate')->nullable();
        $table->string('unit_name')->nullable()->index();
        $table->string('unit_code')->nullable()->index();
        $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
        $table->foreignId('module_import_batch_id')->nullable()->constrained('module_import_batches')->nullOnDelete();
        $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamps();
        $table->softDeletes();
    }

    public function up(): void
    {
        // ═══════════════════════════════════════════════════════════
        // 0. module_import_batches — يجب أن يُنشأ أولاً (parent FK)
        // ═══════════════════════════════════════════════════════════
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

        // ═══════════════════════════════════════════════════════════
        // 1. الاتصالات
        // ═══════════════════════════════════════════════════════════
        if (!Schema::hasTable('communications')) {
            Schema::create('communications', function (Blueprint $table) {
                $this->common($table);
                $table->string('communication_type')->nullable()->index();
                $table->string('service_provider')->nullable();
                $table->string('capacity_speed')->nullable();
                $table->string('unit_of_measure')->nullable();
                $table->string('lines_channels_count')->nullable();
                $table->string('coverage')->nullable();
                $table->string('has_alternative')->nullable();
            });
        }

        // 2. الأراضي
        if (!Schema::hasTable('lands')) {
            Schema::create('lands', function (Blueprint $table) {
                $this->common($table);
                $table->string('plot_number')->nullable();
                $table->string('area')->nullable();
                $table->string('area_unit')->nullable();
                $table->string('ownership_type')->nullable()->index();
                $table->string('ownership_year')->nullable();
                $table->string('current_use')->nullable();
                $table->string('planned_use')->nullable();
                $table->string('utilization_percent')->nullable();
                $table->string('land_status')->nullable();
                $table->string('estimated_value')->nullable();
                $table->text('legal_restrictions')->nullable();
                $table->string('reutilization_possibility')->nullable();
                $table->string('proposed_use')->nullable();
            });
        }

        // 3. الأصول غير المستغلة
        if (!Schema::hasTable('unused_assets')) {
            Schema::create('unused_assets', function (Blueprint $table) {
                $this->common($table);
                $table->string('asset_name')->nullable()->index();
                $table->string('estimated_value')->nullable();
                $table->string('current_use')->nullable();
                $table->string('utilization_percent')->nullable();
                $table->text('underutilization_reason')->nullable();
                $table->string('idle_months')->nullable();
                $table->text('legal_restrictions')->nullable();
                $table->text('technical_restrictions')->nullable();
                $table->string('restart_cost')->nullable();
                $table->text('alternative_uses')->nullable();
                $table->string('sale_possibility')->nullable();
                $table->string('lease_possibility')->nullable();
                $table->string('usufruct_possibility')->nullable();
                $table->string('partnership_possibility')->nullable();
                $table->string('repurpose_possibility')->nullable();
                $table->string('expected_annual_return')->nullable();
                $table->string('intervention_priority')->nullable()->index();
            });
        }

        // 4. المواقع الاستراتيجية
        if (!Schema::hasTable('strategic_sites')) {
            Schema::create('strategic_sites', function (Blueprint $table) {
                $this->common($table);
                $table->string('ownership_type')->nullable();
                $table->string('site_name')->nullable()->index();
                $table->string('site_type')->nullable()->index();
                $table->string('ministry')->nullable();
                $table->string('center')->nullable();
                $table->string('city')->nullable();
                $table->string('district')->nullable();
                $table->string('area')->nullable();
                $table->string('entrances_count')->nullable();
                $table->string('exits_count')->nullable();
                $table->text('vital_target_description')->nullable();
            });
        }

        // 5. البنية التحتية
        if (!Schema::hasTable('infrastructures')) {
            Schema::create('infrastructures', function (Blueprint $table) {
                $this->common($table);
                $table->string('infrastructure_type')->nullable()->index();
                $table->string('facility_network_name')->nullable();
                $table->string('design_capacity')->nullable();
                $table->string('available_capacity')->nullable();
                $table->string('unit_of_measure')->nullable();
                $table->string('supply_source')->nullable();
                $table->string('backup_source')->nullable();
            });
        }

        // 6. الشركات التابعة
        if (!Schema::hasTable('subsidiary_companies')) {
            Schema::create('subsidiary_companies', function (Blueprint $table) {
                $this->common($table);
                $table->string('name_ar')->nullable()->index();
                $table->string('name_en')->nullable();
                $table->string('holding_company')->nullable();
                $table->string('legal_form')->nullable();
                $table->string('main_activity')->nullable();
                $table->string('sub_activity')->nullable();
                $table->string('holding_ownership_percent')->nullable();
                $table->string('state_ownership_percent')->nullable();
                $table->string('capital')->nullable();
                $table->string('employees_count')->nullable();
                $table->string('factories_count')->nullable();
                $table->string('branches_count')->nullable();
                $table->string('sites_count')->nullable();
                $table->string('main_products_count')->nullable();
                $table->string('owns_other_companies')->nullable();
                $table->string('subsidiaries_count')->nullable();
            });
        }

        // 7. العقود والتشغيل
        if (!Schema::hasTable('contracts_operations')) {
            Schema::create('contracts_operations', function (Blueprint $table) {
                $this->common($table);
                $table->string('contract_type')->nullable()->index();
                $table->text('contract_subject')->nullable();
                $table->string('counterparty')->nullable();
                $table->string('start_date')->nullable();
                $table->string('end_date')->nullable();
                $table->string('contract_value')->nullable();
                $table->string('currency')->nullable();
                $table->string('renewal_terms')->nullable();
                $table->string('critical_obligation')->nullable();
                $table->string('supplier_alternative')->nullable();
                $table->string('contract_manager')->nullable();
            });
        }

        // 8. الفنادق
        if (!Schema::hasTable('hotels')) {
            Schema::create('hotels', function (Blueprint $table) {
                $this->common($table);
                $table->string('ownership_type')->nullable();
                $table->string('hotel_name')->nullable()->index();
                $table->string('star_rating')->nullable()->index();
                $table->string('ministry')->nullable();
                $table->string('center')->nullable();
                $table->string('city')->nullable();
                $table->string('district')->nullable();
                $table->string('phone')->nullable();
                $table->string('rooms_count')->nullable();
                $table->string('dining_halls_count')->nullable();
                $table->string('entrances_count')->nullable();
                $table->string('exits_count')->nullable();
                $table->string('manager_name')->nullable();
                $table->string('manager_mobile')->nullable();
                $table->string('license_number')->nullable();
                $table->string('license_period')->nullable();
                $table->string('license_status')->nullable();
                $table->string('individual_vehicles')->nullable();
                $table->string('group_vehicles')->nullable();
                $table->string('email')->nullable();
                $table->string('beds_count')->nullable();
                $table->string('emergency_capacity')->nullable();
                $table->string('has_clinic')->nullable();
                $table->string('has_ambulance')->nullable();
                $table->string('has_doctor')->nullable();
            });
        }

        // 9. المركبات
        if (!Schema::hasTable('vehicles')) {
            Schema::create('vehicles', function (Blueprint $table) {
                $this->common($table);
                $table->string('ownership_type')->nullable();
                $table->string('plate_number')->nullable()->index();
                $table->string('chassis_number')->nullable();
                $table->string('engine_number')->nullable();
                $table->string('license_type')->nullable();
                $table->string('color')->nullable();
                $table->string('manufacture_year')->nullable();
                $table->string('vehicle_condition')->nullable()->index();
                $table->string('current_location')->nullable();
                $table->text('fault_description')->nullable();
                $table->string('dispense_date')->nullable();
                $table->string('scrap_reason')->nullable();
                $table->string('scrap_date')->nullable();
                $table->text('notes')->nullable();
                $table->string('ministry')->nullable();
                $table->string('vehicle_type')->nullable()->index();
                $table->string('brand')->nullable();
                $table->string('model')->nullable();
                $table->string('fuel_type')->nullable();
                $table->string('odometer_km')->nullable();
                $table->string('maintenance_contract')->nullable();
                $table->string('maintenance_contract_end')->nullable();
                $table->string('fitting')->nullable();
                $table->string('fitting_entity')->nullable();
                $table->string('procurement_type')->nullable();
                $table->string('procurement_entity')->nullable();
                $table->string('procurement_year')->nullable();
                $table->string('annual_usage_plan_km')->nullable();
                $table->string('vehicle_requirement')->nullable();
                $table->string('fault_date')->nullable();
                $table->string('repair_block_reason')->nullable();
                $table->string('disposal_reason')->nullable();
                $table->string('driver_name')->nullable();
                $table->string('license_end_date')->nullable();
                $table->string('last_maintenance_date')->nullable();
                $table->string('last_maintenance_odometer')->nullable();
                $table->string('monthly_fuel_consumption')->nullable();
            });
        }

        // 10. المستشفيات
        if (!Schema::hasTable('hospitals')) {
            Schema::create('hospitals', function (Blueprint $table) {
                $this->common($table);
                $table->string('ownership_type')->nullable();
                $table->string('hospital_name')->nullable()->index();
                $table->string('hospital_type')->nullable()->index();
                $table->string('ministry')->nullable();
                $table->string('center')->nullable();
                $table->string('city')->nullable();
                $table->string('district')->nullable();
                $table->string('doctors_count')->nullable();
                $table->string('rooms_count')->nullable();
                $table->string('ac_rooms')->nullable();
                $table->string('fan_rooms')->nullable();
                $table->string('total_beds')->nullable();
                $table->string('icu_beds')->nullable();
                $table->string('reception_beds')->nullable();
                $table->string('ventilators')->nullable();
                $table->string('emergency_drugs_stock')->nullable();
                $table->string('ambulances')->nullable();
                $table->string('body_bags')->nullable();
                $table->string('morgue_fridges')->nullable();
                $table->text('notes')->nullable();
                $table->string('phone')->nullable();
                $table->string('operating_rooms')->nullable();
                $table->string('burn_care_beds')->nullable();
                $table->string('neonatal_beds')->nullable();
                $table->string('nursing_count')->nullable();
                $table->string('blood_bank')->nullable();
                $table->string('dialysis_machines')->nullable();
                $table->string('affiliation')->nullable();
                $table->string('intermediate_care_beds')->nullable();
                $table->string('recovery_beds')->nullable();
                $table->string('emergency_beds')->nullable();
                $table->string('isolation_beds')->nullable();
                $table->string('specialty')->nullable();
                $table->text('medical_equipment')->nullable();
                $table->string('available_for_operation')->nullable();
                $table->string('available_for_service')->nullable();
            });
        }

        // 11. المشروعات
        if (!Schema::hasTable('projects')) {
            Schema::create('projects', function (Blueprint $table) {
                $this->common($table);
                $table->string('project_name')->nullable()->index();
                $table->string('project_type')->nullable()->index();
                $table->text('objective')->nullable();
                $table->string('planned_start_date')->nullable();
                $table->string('planned_end_date')->nullable();
                $table->string('progress_percent')->nullable();
                $table->string('estimated_cost')->nullable();
                $table->string('actual_cost')->nullable();
                $table->string('currency')->nullable();
                $table->string('funding_source')->nullable();
                $table->string('project_status')->nullable()->index();
                $table->text('production_capacity_impact')->nullable();
            });
        }

        // 12. المعدات
        if (!Schema::hasTable('equipment')) {
            Schema::create('equipment', function (Blueprint $table) {
                $this->common($table);
                $table->string('ownership_type')->nullable();
                $table->string('equipment_name')->nullable()->index();
                $table->string('model')->nullable();
                $table->string('manufacture_year')->nullable();
                $table->string('plate_number')->nullable();
                $table->string('serial_number')->nullable();
                $table->string('engine_number')->nullable();
                $table->string('operating_hours')->nullable();
                $table->string('condition_status')->nullable()->index();
                $table->string('mechanical_fault')->nullable();
                $table->string('electrical_fault')->nullable();
                $table->text('fault_description')->nullable();
                $table->string('fault_date')->nullable();
                $table->string('repair_cost')->nullable();
                $table->string('purchase_cost')->nullable();
                $table->string('current_value')->nullable();
                $table->string('repair_decision')->nullable();
                $table->string('current_location')->nullable();
                $table->text('notes')->nullable();
                $table->string('ministry')->nullable();
                $table->string('village_district')->nullable();
                $table->string('engine_model')->nullable();
                $table->string('chassis_number')->nullable();
                $table->string('fuel_type')->nullable();
                $table->string('fitting')->nullable();
                $table->string('operating_crew')->nullable();
                $table->string('production_capacity')->nullable();
                $table->string('transportability')->nullable();
                $table->string('maintenance_contract')->nullable();
                $table->string('maintenance_contract_end')->nullable();
                $table->string('last_periodic_maintenance')->nullable();
                $table->text('last_maintenance_details')->nullable();
                $table->string('fuel_consumption_rate')->nullable();
                $table->string('usage_nature')->nullable();
                $table->string('driver_name')->nullable();
                $table->string('license_type')->nullable();
                $table->string('custody_ownership')->nullable();
                $table->string('scrap_date')->nullable();
                $table->string('scrap_reason')->nullable();
                $table->string('movement_restrictions')->nullable();
                $table->text('performance_history')->nullable();
                $table->string('dimensions')->nullable();
                $table->string('weight')->nullable();
                $table->string('max_load')->nullable();
            });
        }

        // 13. الموارد البشرية
        if (!Schema::hasTable('human_resources')) {
            Schema::create('human_resources', function (Blueprint $table) {
                $this->common($table);
                $table->string('job_title')->nullable()->index();
                $table->string('job_category')->nullable()->index();
                $table->string('employees_count')->nullable();
                $table->string('emergency_available_count')->nullable();
                $table->string('shifts_count')->nullable();
                $table->string('avg_experience_years')->nullable();
                $table->text('required_certificate')->nullable();
                $table->text('critical_skill')->nullable();
            });
        }

        // 14. الحماية المدنية
        if (!Schema::hasTable('fire_stations')) {
            Schema::create('fire_stations', function (Blueprint $table) {
                $this->common($table);
                $table->string('ownership_type')->nullable();
                $table->string('station_name')->nullable()->index();
                $table->string('phone')->nullable();
                $table->string('station_type')->nullable()->index();
                $table->string('ministry')->nullable();
                $table->string('center')->nullable();
                $table->string('city')->nullable();
                $table->string('district')->nullable();
                $table->string('heavy_fire_trucks')->nullable();
                $table->string('light_fire_trucks')->nullable();
                $table->string('medium_fire_trucks')->nullable();
                $table->string('water_tanker_8t')->nullable();
                $table->string('water_tanker_15m3')->nullable();
                $table->string('water_tanker_35m3')->nullable();
                $table->text('handheld_extinguishers')->nullable();
                $table->string('rescue_vehicle')->nullable();
                $table->text('service_areas')->nullable();
                $table->text('notes')->nullable();
                $table->string('officers_count')->nullable();
                $table->string('personnel_count')->nullable();
                $table->string('conscripts_count')->nullable();
                $table->string('guards_count')->nullable();
                $table->string('civilians_count')->nullable();
                $table->string('fire_hydrants_count')->nullable();
                $table->string('hydraulic_platform_count')->nullable();
                $table->string('hydraulic_ladder_count')->nullable();
                $table->string('portable_water_pump_count')->nullable();
                $table->string('floating_water_pump_count')->nullable();
                $table->string('equipment_carrier_count')->nullable();
                $table->string('explosives_report_vehicle')->nullable();
                $table->string('crucible_count')->nullable();
                $table->string('jamming_vehicle_count')->nullable();
                $table->string('portable_jammer_count')->nullable();
                $table->string('robot_count')->nullable();
                $table->string('explosion_suit_count')->nullable();
                $table->string('water_cannon')->nullable();
                $table->string('rope_set_count')->nullable();
                $table->string('metal_detector_gates')->nullable();
                $table->string('xray_baggage_scanners')->nullable();
                $table->string('bomb_disposal_team')->nullable();
                $table->string('search_rescue_team')->nullable();
            });
        }

        // 15. نطاقات الإنتاج (خطوط الإنتاج)
        if (!Schema::hasTable('production_scopes')) {
            Schema::create('production_scopes', function (Blueprint $table) {
                $this->common($table);
                $table->text('line_name')->nullable()->index();
                $table->text('product_name')->nullable()->index();
                $table->text('manufacturer')->nullable();
                $table->string('line_case', 50)->nullable()->index();
                $table->string('operational_case', 50)->nullable()->index();
                $table->text('notes')->nullable();
            });
        }

        // 16. ماكينات
        if (!Schema::hasTable('machines')) {
            Schema::create('machines', function (Blueprint $table) {
                $this->common($table);
                $table->string('machine_name')->nullable()->index();
                $table->string('machine_type')->nullable()->index();
                $table->string('axes_count')->nullable();
                $table->string('origin_country')->nullable();
                $table->string('machine_model')->nullable();
                $table->string('purchase_year')->nullable();
                $table->string('condition_status')->nullable()->index();
                $table->string('max_length_mm')->nullable();
                $table->string('max_width_mm')->nullable();
                $table->string('max_height_mm')->nullable();
                $table->string('max_outer_diameter_mm')->nullable();
                $table->string('max_inner_diameter_mm')->nullable();
                $table->string('max_workpiece_weight_kg')->nullable();
                $table->string('machining_accuracy_micron')->nullable();
                $table->string('efficiency')->nullable();
                $table->string('annual_available_hours')->nullable();
                $table->string('annual_used_hours')->nullable();
                $table->string('load_rate_percent')->nullable();
                $table->string('workshop_name')->nullable();
                $table->string('capacity_available_for_others')->nullable();
            });
        }

        // 17. محطات الكهرباء
        if (!Schema::hasTable('power_stations')) {
            Schema::create('power_stations', function (Blueprint $table) {
                $this->common($table);
                $table->string('ownership_type')->nullable();
                $table->string('station_name')->nullable()->index();
                $table->string('phone')->nullable();
                $table->string('capacity_mva')->nullable();
                $table->text('distribution_areas')->nullable();
                $table->string('operation_type')->nullable()->index();
                $table->string('ministry')->nullable();
                $table->string('center')->nullable();
                $table->string('city')->nullable();
                $table->string('district')->nullable();
                $table->string('service_start_date')->nullable();
                $table->string('last_maintenance_date')->nullable();
                $table->string('next_maintenance_date')->nullable();
                $table->string('coverage_scope')->nullable();
                $table->string('energy_source')->nullable();
                $table->string('operator_entity')->nullable();
                $table->string('production_capacity')->nullable();
                $table->string('available_capacity')->nullable();
                $table->string('technical_status')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('power_stations');
        Schema::dropIfExists('machines');
        Schema::dropIfExists('production_scopes');
        Schema::dropIfExists('fire_stations');
        Schema::dropIfExists('human_resources');
        Schema::dropIfExists('equipment');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('hospitals');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('hotels');
        Schema::dropIfExists('contracts_operations');
        Schema::dropIfExists('subsidiary_companies');
        Schema::dropIfExists('infrastructures');
        Schema::dropIfExists('strategic_sites');
        Schema::dropIfExists('unused_assets');
        Schema::dropIfExists('lands');
        Schema::dropIfExists('communications');
        Schema::dropIfExists('module_import_batches');
    }
};