<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('production_lines')) {
            return;
        }

        // إسقاط FK القديم على import_batches
        try {
            Schema::table('production_lines', function (Blueprint $table) {
                $table->dropForeign(['import_batch_id']);
            });
        } catch (\Throwable $e) {
            // قد يكون الاسم مختلفاً
            try {
                DB::statement('ALTER TABLE production_lines DROP FOREIGN KEY production_lines_import_batch_id_foreign');
            } catch (\Throwable $e2) {
                // تجاهل إن لم يوجد
            }
        }

        Schema::table('production_lines', function (Blueprint $table) {
            if (Schema::hasColumn('production_lines', 'import_batch_id')) {
                // يبقى العمود بدون FK — يخزّن id من module_import_batches
                DB::statement('ALTER TABLE production_lines MODIFY import_batch_id BIGINT UNSIGNED NULL');
            }
            if (!Schema::hasColumn('production_lines', 'module_import_batch_id')) {
                $table->unsignedBigInteger('module_import_batch_id')->nullable()->index();
            }
            if (!Schema::hasColumn('production_lines', 'unit_code')) {
                $table->string('unit_code', 50)->nullable()->index();
            }
            if (!Schema::hasColumn('production_lines', 'unit_name')) {
                $table->string('unit_name')->nullable();
            }
        });
    }

    public function down(): void
    {
        // لا نعيد الـ FK تلقائياً
    }
};
