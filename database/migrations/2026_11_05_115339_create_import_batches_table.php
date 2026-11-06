
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_batches', function (Blueprint $table) {
            $table->id();

            // Uploaded Excel file
            $table->string('file_name');
            $table->string('file_path');
            $table->char('file_hash', 64)->unique();

            // Import destination
            $table->string('destination_table', 100);

            // Import statistics
            $table->unsignedInteger('total_rows')->default(0);
            $table->unsignedInteger('imported_rows')->default(0);
            $table->unsignedInteger('skipped_rows')->default(0);
            $table->unsignedInteger('failed_rows')->default(0);

            // pending, processing, completed, failed
            $table->string('status', 30)->default('pending');

            // Importing user
            $table->unsignedBigInteger('created_by')->nullable();

            // Execution timestamps
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Additional import information
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index('destination_table');
            $table->index('status');
            $table->index('created_by');
            $table->index('created_at');
        });

        // Link production_lines records to their import batch.
        Schema::table('production_lines', function (Blueprint $table) {
            $table->foreign('import_batch_id')
                ->references('id')
                ->on('import_batches')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('production_lines', function (Blueprint $table) {
            $table->dropForeign(['import_batch_id']);
        });

        Schema::dropIfExists('import_batches');
    }
};
