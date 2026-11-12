<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // عمود "حالة المحطة الفنية" موجود في config وفي ملف Excel لكن مش في جدول power_stations
        if (Schema::hasTable('power_stations') && !Schema::hasColumn('power_stations', 'station_status')) {
            Schema::table('power_stations', function (Blueprint $table) {
                $table->string('station_status')->nullable()->index();
            });
        }

        // غرف العمليات (الموديول رقم 26)
        if (!Schema::hasTable('operations_rooms')) {
            Schema::create('operations_rooms', function (Blueprint $table) {
                $table->id();
                $table->string('owner_organization')->nullable()->index();
                $table->string('location_scope')->nullable();
                $table->string('governorate')->nullable()->index();
                $table->string('room_name')->nullable()->index();
                $table->string('room_type')->nullable()->index();
                $table->text('work_scope')->nullable();
                $table->string('working_hours')->nullable();
                $table->string('seats_count')->nullable();
                $table->string('screens_count')->nullable();
                $table->string('central_link')->nullable();
                $table->string('backup_power')->nullable();
                $table->string('backup_duration_hours')->nullable();
                $table->text('alt_communication')->nullable();
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
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('operations_rooms');
        if (Schema::hasTable('power_stations') && Schema::hasColumn('power_stations', 'station_status')) {
            Schema::table('power_stations', function (Blueprint $table) {
                $table->dropColumn('station_status');
            });
        }
    }
};
