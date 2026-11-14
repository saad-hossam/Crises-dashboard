<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * 8 موديولات إضافية:
 * AMBULANCE_STATIONS, JOINT_STOCK_COMPANIES, STRATEGIC_INVENTORY,
 * MOSQUES, FUEL_STATIONS, SCHOOLS, YOUTH_CENTERS, OWNERSHIP_STRUCTURE, WITHDRAWAL_RATES
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
        // 1. نقاط الإسعاف
        if (!Schema::hasTable('ambulance_stations')) {
            Schema::create('ambulance_stations', function (Blueprint $table) {
                $this->common($table);
                $table->string('ownership_type')->nullable()->index();
                $table->string('station_name')->nullable()->index();
                $table->string('station_phone')->nullable();
                $table->string('paramedic_phone')->nullable();
                $table->string('station_type')->nullable()->index();
                $table->string('medical_supplies_availability')->nullable()->index();
                $table->string('ministry')->nullable();
                $table->string('center')->nullable();
                $table->string('city')->nullable();
                $table->string('district')->nullable();
                $table->string('paramedics_count')->nullable();
                $table->string('regular_ambulances_count')->nullable();
                $table->string('sterilization_ambulances_count')->nullable();
                $table->string('icu_ambulances_count')->nullable();
                $table->string('single_patient_ambulances_count')->nullable();
                $table->string('double_patient_ambulances_count')->nullable();
                $table->string('gps_equipment_count')->nullable();
                $table->string('radio_equipment')->nullable();
                $table->string('drivers_count')->nullable();
                $table->string('drivers_license_type')->nullable();
                $table->string('doctors_count')->nullable();
                $table->string('nurses_count')->nullable();
                $table->string('work_system')->nullable();
                $table->string('out_of_service_vehicles')->nullable();
                $table->string('nearest_fuel_station')->nullable();
                $table->string('station_code')->nullable();
                $table->string('rest_capacity')->nullable();
                $table->string('has_winch')->nullable();
                $table->string('medical_devices_count')->nullable();
                $table->string('last_calibration_date')->nullable();
                $table->string('vehicles_maintenance_date')->nullable();
            });
        }

        // 2. الشركات المساهمة والمشتركة
        if (!Schema::hasTable('joint_stock_companies')) {
            Schema::create('joint_stock_companies', function (Blueprint $table) {
                $this->common($table);
                $table->string('invested_company_name')->nullable()->index();
                $table->string('investment_type')->nullable()->index();
                $table->string('ownership_percentage')->nullable();
                $table->string('book_value')->nullable();
                $table->string('activity')->nullable();
                $table->string('country')->nullable();
                $table->text('partners')->nullable();
                $table->string('investment_date')->nullable();
                $table->string('investment_status')->nullable()->index();
                $table->text('strategic_purpose')->nullable();
                $table->string('investment_importance')->nullable()->index();
            });
        }

        // 3. المخزون الإستراتيجي
        if (!Schema::hasTable('strategic_inventories')) {
            Schema::create('strategic_inventories', function (Blueprint $table) {
                $this->common($table);
                $table->string('item_name')->nullable()->index();
                $table->string('available_quantity')->nullable();
                $table->string('unit_of_measure')->nullable();
                $table->string('min_limit')->nullable();
                $table->string('max_limit')->nullable();
                $table->string('daily_consumption_avg')->nullable();
                $table->string('sufficiency_days')->nullable();
                $table->string('expiry_date')->nullable();
                $table->string('redistribution_possible')->nullable();
                $table->string('extra_supply_lead_days')->nullable();
            });
        }

        // 4. المساجد
        if (!Schema::hasTable('mosques')) {
            Schema::create('mosques', function (Blueprint $table) {
                $this->common($table);
                $table->string('ownership_type')->nullable()->index();
                $table->string('mosque_name')->nullable()->index();
                $table->string('phone')->nullable();
                $table->string('mosque_type')->nullable()->index();
                $table->string('ministry')->nullable();
                $table->string('center')->nullable();
                $table->string('city')->nullable();
                $table->string('district')->nullable();
                $table->string('area')->nullable();
                $table->string('halls_count')->nullable();
                $table->string('hall_area')->nullable();
                $table->string('open_land_area')->nullable();
                $table->string('owner_entity')->nullable();
                $table->string('affiliated_admin')->nullable();
                $table->string('event_halls_count')->nullable();
                $table->string('mosque_area')->nullable();
                $table->string('capacity')->nullable();
                $table->string('outdoor_areas')->nullable();
                $table->string('water_tanks_capacity')->nullable();
                $table->string('backup_electricity')->nullable();
                $table->string('has_clinic')->nullable();
                $table->string('has_event_halls')->nullable();
                $table->string('fire_extinguishers_count')->nullable();
                $table->string('entrances_count')->nullable();
            });
        }

        // 5. محطات الوقود
        if (!Schema::hasTable('fuel_stations')) {
            Schema::create('fuel_stations', function (Blueprint $table) {
                $this->common($table);
                $table->string('ownership_type')->nullable()->index();
                $table->string('station_name')->nullable()->index();
                $table->string('station_type')->nullable()->index();
                $table->string('ministry')->nullable();
                $table->string('center')->nullable();
                $table->string('city')->nullable();
                $table->string('district')->nullable();
                $table->string('station_ownership_type')->nullable();
                $table->string('fuel_type')->nullable()->index();
                $table->text('services_provided')->nullable();
                $table->string('pumps_count')->nullable();
                $table->string('tanks_capacity')->nullable();
                $table->string('fire_extinguishers_count')->nullable();
                $table->string('fire_hydrants_count')->nullable();
                $table->string('buildings_count')->nullable();
                $table->string('brand')->nullable();
                $table->string('monthly_quota')->nullable();
                $table->string('working_hours')->nullable();
                $table->text('tanks_detail')->nullable();
                $table->string('has_cng')->nullable();
                $table->string('has_ev_chargers')->nullable();
                $table->string('nozzles_count')->nullable();
                $table->string('atg_linked')->nullable();
                $table->string('gps_tracking')->nullable();
                $table->string('pos_support')->nullable();
                $table->string('licenses_validity')->nullable();
                $table->string('geographic_scope')->nullable();
                $table->string('heavy_transport_service')->nullable();
                $table->string('distance_to_fire_hospital')->nullable();
            });
        }

        // 6. المدارس
        if (!Schema::hasTable('schools')) {
            Schema::create('schools', function (Blueprint $table) {
                $this->common($table);
                $table->string('ownership_type')->nullable()->index();
                $table->string('school_name')->nullable()->index();
                $table->string('school_type')->nullable()->index();
                $table->string('ministry')->nullable();
                $table->string('phone')->nullable();
                $table->string('center')->nullable();
                $table->string('city')->nullable();
                $table->string('district')->nullable();
                $table->string('classrooms_count')->nullable();
                $table->string('area')->nullable();
                $table->text('notes')->nullable();
                $table->string('education_level')->nullable()->index();
                $table->string('gender_type')->nullable()->index();
                $table->string('capacity')->nullable();
                $table->string('teachers_count')->nullable();
                $table->string('workers_count')->nullable();
                $table->string('admins_count')->nullable();
                $table->string('education_admin')->nullable();
                $table->string('water_tanks_capacity')->nullable();
                $table->string('backup_electricity')->nullable();
                $table->string('toilets_count')->nullable();
                $table->string('toilets_condition')->nullable();
                $table->string('playground_area')->nullable();
                $table->string('has_clinic')->nullable();
                $table->string('has_vaccine_fridge')->nullable();
                $table->string('has_kitchen')->nullable();
                $table->string('has_internal_radio')->nullable();
                $table->string('gates_count')->nullable();
                $table->string('gates_width')->nullable();
                $table->string('fire_extinguishers_count')->nullable();
                $table->string('emergency_exits_count')->nullable();
                $table->string('property_status')->nullable();
            });
        }

        // 7. مراكز الشباب
        if (!Schema::hasTable('youth_centers')) {
            Schema::create('youth_centers', function (Blueprint $table) {
                $this->common($table);
                $table->string('ownership_type')->nullable()->index();
                $table->string('center_name')->nullable()->index();
                $table->string('phone')->nullable();
                $table->string('center_area')->nullable();
                $table->string('courts_count')->nullable();
                $table->string('court_type')->nullable()->index();
                $table->string('court_area')->nullable();
                $table->string('court_rooms_count')->nullable();
                $table->string('youth_directorate')->nullable();
                $table->string('center')->nullable();
                $table->string('city')->nullable();
                $table->string('district')->nullable();
                $table->string('manager_name')->nullable();
                $table->string('manager_phone')->nullable();
                $table->string('emergency_capacity')->nullable();
                $table->string('residence_rooms_count')->nullable();
                $table->string('suitable_for_shelter')->nullable()->index();
                $table->string('medical_readiness')->nullable();
                $table->string('individual_vehicles_count')->nullable();
                $table->string('electricity_source')->nullable();
                $table->string('water_source')->nullable();
                $table->string('open_land_area')->nullable();
                $table->string('fire_equipment')->nullable();
                $table->string('gates_count')->nullable();
            });
        }

        // 8. هيكل الملكية للشركات
        if (!Schema::hasTable('ownership_structures')) {
            Schema::create('ownership_structures', function (Blueprint $table) {
                $this->common($table);
                $table->string('shareholder_name')->nullable()->index();
                $table->string('shareholder_type')->nullable()->index();
                $table->string('shareholder_sector')->nullable();
                $table->string('shares_count')->nullable();
                $table->string('ownership_percentage')->nullable();
                $table->string('share_nominal_value')->nullable();
                $table->string('contribution_value')->nullable();
                $table->string('ownership_start_date')->nullable();
            });
        }

        // 9. نسب السحب (عقود / سحب كميات)
        if (!Schema::hasTable('withdrawal_rates')) {
            Schema::create('withdrawal_rates', function (Blueprint $table) {
                $this->common($table);
                $table->string('serial_number')->nullable();
                $table->string('factory_name')->nullable()->index();
                $table->string('product_name')->nullable()->index();
                $table->string('quantity')->nullable();
                $table->string('client_name')->nullable()->index();
                $table->string('contract_date')->nullable();
                $table->string('market_type')->nullable()->index(); // محلي / خارجي
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawal_rates');
        Schema::dropIfExists('ownership_structures');
        Schema::dropIfExists('youth_centers');
        Schema::dropIfExists('schools');
        Schema::dropIfExists('fuel_stations');
        Schema::dropIfExists('mosques');
        Schema::dropIfExists('strategic_inventories');
        Schema::dropIfExists('joint_stock_companies');
        Schema::dropIfExists('ambulance_stations');
    }
};
