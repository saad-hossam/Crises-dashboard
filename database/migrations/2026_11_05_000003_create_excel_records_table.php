<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('excel_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('excel_module_id')->constrained('excel_modules')->cascadeOnDelete();
            $table->foreignId('excel_import_batch_id')->constrained('excel_import_batches')->cascadeOnDelete();
            $table->string('unit_code', 50)->nullable()->index();
            $table->string('unit_name')->nullable();
            // double أدق وأوسع من decimal(12,8) — يمنع Out of range على الإحداثيات
            $table->double('x_coordinate')->nullable();
            $table->double('y_coordinate')->nullable();
            $table->json('data');
            $table->timestamps();

            $table->index(['excel_module_id', 'unit_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('excel_records');
    }
};
