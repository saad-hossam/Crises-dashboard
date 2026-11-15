
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_batches', function (Blueprint $table) {
            $table->id();

            // Original file information
            $table->string('original_filename');
            $table->string('stored_filename');
            $table->string('stored_path');
            $table->string('file_extension', 10);
            $table->unsignedBigInteger('file_size')->default(0);
            $table->string('file_hash', 64)->index();

            // Import destination
            $table->string('target_table');
            $table->string('selected_sheet')->nullable();
            $table->string('organizational_unit')->nullable();

            // Import statistics
            $table->unsignedInteger('total_rows')->default(0);
            $table->unsignedInteger('imported_rows')->default(0);
            $table->unsignedInteger('failed_rows')->default(0);

            // Import status
            $table->string('status', 30)->default('pending');
            $table->text('error_message')->nullable();

            // Execution timestamps
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            // Prevent registering the same file hash more than once.
            $table->unique('file_hash');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_batches');
    }
};
