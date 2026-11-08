<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('excel_import_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('excel_module_id')->constrained('excel_modules')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('original_filename');
            $table->string('stored_path');
            $table->string('file_hash', 64)->index();          // SHA-256
            $table->unsignedInteger('total_rows')->default(0);
            $table->unsignedInteger('imported_rows')->default(0);
            $table->unsignedInteger('failed_rows')->default(0);
            $table->string('status', 30)->default('pending'); // pending, previewed, completed, failed
            $table->json('meta')->nullable();                 // extra info
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->unique(['excel_module_id', 'file_hash']); // prevent same file twice per module
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('excel_import_batches');
    }
};
