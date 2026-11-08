
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_errors', function (Blueprint $table) {
            $table->id();

            // Related import batch
            $table->foreignId('import_batch_id')
                ->constrained('import_batches')
                ->restrictOnDelete();

            // Original Excel row number
            $table->unsignedInteger('excel_row');

            // Failed Excel column, if applicable
            $table->string('column_name', 255)->nullable();

            // validation, database, system
            $table->string('error_type', 30)->default('validation');

            // Error description
            $table->longText('error_message');

            // Original row values for troubleshooting
            $table->json('row_data')->nullable();

            // Additional error context
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['import_batch_id', 'excel_row']);
            $table->index(['import_batch_id', 'error_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_errors');
    }
};
