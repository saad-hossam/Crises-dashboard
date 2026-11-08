<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('excel_modules', function (Blueprint $table) {
            $table->id();
            $table->string('code', 80)->unique();          // e.g. PRODUCTION_LINES
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->string('template_version')->nullable();
            $table->unsignedInteger('row_count')->default(0);
            $table->timestamp('last_imported_at')->nullable();
            $table->json('last_headers')->nullable();      // last known columns
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('excel_modules');
    }
};
