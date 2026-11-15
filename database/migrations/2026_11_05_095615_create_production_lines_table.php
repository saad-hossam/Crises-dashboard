
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_lines', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Basic Information
            |--------------------------------------------------------------------------
            */

            $table->text('owner_organization')->nullable();
            $table->string('location_scope', 255)->nullable();
            $table->string('governorate', 255)->nullable();
            $table->longText('name')->nullable();
            $table->longText('product_name')->nullable();
            $table->text('manufacturer')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Production Capacity
            |--------------------------------------------------------------------------
            */

            $table->string('operating_year', 255)->nullable();
            $table->text('design_capacity')->nullable();
            $table->text('capacity_unit')->nullable();
            $table->text('current_operational_capacity')->nullable();
            $table->text('annual_production_capacity')->nullable();
            $table->text('actual_production')->nullable();
            $table->text('utilization_rate')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Operation and Workforce
            |--------------------------------------------------------------------------
            */

            $table->text('shifts_count')->nullable();
            $table->text('daily_operating_hours')->nullable();
            $table->text('workers_count')->nullable();
            $table->string('line_status', 255)->nullable();
            $table->text('expected_lifetime_years')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Maintenance and Development
            |--------------------------------------------------------------------------
            */

            $table->text('last_maintenance_date')->nullable();
            $table->text('next_maintenance_date')->nullable();
            $table->longText('production_increase_possibility')->nullable();
            $table->text('production_increase_lead_time_days')->nullable();
            $table->string('development_type', 255)->nullable();
            $table->text('development_completion_date')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Company Information
            |--------------------------------------------------------------------------
            */

            $table->longText('company_name')->nullable();
            $table->longText('address')->nullable();
            $table->string('operating_status', 255)->nullable();
            $table->string('readiness_level', 255)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Data Entry Information
            |--------------------------------------------------------------------------
            */

            $table->text('last_updated_date')->nullable();
            $table->longText('data_entry_responsible')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Geographic Coordinates
            |--------------------------------------------------------------------------
            */

            $table->text('x_coordinate')->nullable();
            $table->text('y_coordinate')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Unit Relationship
            |--------------------------------------------------------------------------
            */

            $table->foreignId('unit_id')
                ->constrained('units');

            /*
            |--------------------------------------------------------------------------
            | User Tracking
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Import Tracking
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('import_batch_id')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Soft Deletes and Timestamps
            |--------------------------------------------------------------------------
            */

            $table->softDeletes();
            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('location_scope');
            $table->index('governorate');
            $table->index('operating_year');
            $table->index('line_status');
            $table->index('operating_status');
            $table->index('readiness_level');
            $table->index('development_type');
            $table->index('created_by');
            $table->index('updated_by');
            $table->index('import_batch_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_lines');
    }
};
