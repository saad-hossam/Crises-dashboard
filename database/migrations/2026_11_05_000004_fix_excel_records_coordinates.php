<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('excel_records')) {
            Schema::table('excel_records', function (Blueprint $table) {
                $table->double('x_coordinate')->nullable()->change();
                $table->double('y_coordinate')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('excel_records')) {
            Schema::table('excel_records', function (Blueprint $table) {
                $table->decimal('x_coordinate', 12, 8)->nullable()->change();
                $table->decimal('y_coordinate', 12, 8)->nullable()->change();
            });
        }
    }
};
