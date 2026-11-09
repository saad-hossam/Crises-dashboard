<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * جداول مخصصة لكل موديول Excel (26 ملف)
 * أعمدة مشتركة + payload JSON لباقي الحقول للتحليل والـ charts
 */
return new class extends Migration
{
    private array $modules = [
        'communications' => 'الاتصالات',
        'lands' => 'الأراضي',
        'unused_assets' => 'الأصول غير المستغلة',
        'mutual_dependencies' => 'الاعتماد المتبادل',
        'strategic_sites' => 'الأماكن الاستراتيجية',
        'infrastructures' => 'البنية التحتية',
        'subsidiaries' => 'الشركات التابعة',
        'companies' => 'الشركات',
        'contracts' => 'العقود والتشغيل',
        'sites_companies' => 'الفروع والمواقع',
        'hotels' => 'الفنادق',
        'productive_capacities' => 'القدرات الإنتاجية',
        'warehouses' => 'المخازن',
        'vehicles' => 'المركبات',
        'hospitals' => 'المستشفيات',
        'projects' => 'المشروعات',
        'equipment' => 'المعدات',
        'products_services' => 'المنتجات والخدمات',
        'facilities_properties' => 'المنشآت والعقارات',
        'human_resources' => 'الموارد البشرية',
        'fire_stations' => 'الحماية المدنية',
        'production_lines' => 'خطوط الإنتاج',
        'operations_rooms' => 'غرف العمليات',
        'machines' => 'ماكينات',
        'power_stations' => 'محطات الكهرباء',
    ];

    public function up(): void
    {
        foreach ($this->modules as $table => $label) {
            if (Schema::hasTable($table)) {
                // ensure flexible columns exist
                Schema::table($table, function (Blueprint $t) use ($table) {
                    if (!Schema::hasColumn($table, 'payload')) {
                        $t->json('payload')->nullable()->after('id');
                    }
                    if (!Schema::hasColumn($table, 'import_batch_id')) {
                        $t->unsignedBigInteger('import_batch_id')->nullable()->index();
                    }
                    if (!Schema::hasColumn($table, 'module_import_batch_id')) {
                        $t->unsignedBigInteger('module_import_batch_id')->nullable()->index();
                    }
                });
                continue;
            }

            Schema::create($table, function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('import_batch_id')->nullable()->index();
                $t->unsignedBigInteger('module_import_batch_id')->nullable()->index();

                // Common AOI fields
                $t->string('owner_organization', 500)->nullable();
                $t->string('location_scope', 255)->nullable();
                $t->string('governorate', 255)->nullable();
                $t->string('company_name', 500)->nullable();
                $t->string('address', 1000)->nullable();
                $t->string('operating_status', 255)->nullable()->index();
                $t->string('readiness_level', 255)->nullable()->index();
                $t->string('unit_name', 255)->nullable()->index();
                $t->string('unit_code', 50)->nullable()->index();
                $t->double('x_coordinate')->nullable();
                $t->double('y_coordinate')->nullable();
                $t->string('data_entry_responsible', 500)->nullable();
                $t->string('last_updated_date', 100)->nullable();

                // Full row as JSON for analysis / charts / details
                $t->json('payload')->nullable();

                $t->timestamps();
            });
        }
    }

    public function down(): void
    {
        // لا نحذف الجداول الموجودة مسبقاً حتى لا نفقد بيانات
        foreach (array_keys($this->modules) as $table) {
            // Schema::dropIfExists($table);
        }
    }
};
