<?php

namespace App\Console\Commands;

use Throwable;
use App\Models\ImportBatch;
use Illuminate\Console\Command;
use App\Models\IndustrialFacility;
use Illuminate\Support\Facades\Schema;
use App\Services\IndustrialFacilitiesImportService;

class ImportIndustrialFacilities extends Command
{
    protected $signature = 'industrial:import
                            {file : Excel file path}
                            {organizational_unit? : Organizational unit assigned to all imported rows}';

    protected $description = 'Validate and import industrial facilities from Excel';

    public function handle(
        IndustrialFacilitiesImportService $importService
    ): int {
        $file = $this->argument('file');

        $organizationalUnit = $this->argument('organizational_unit');

        if ($organizationalUnit !== null) {
            $organizationalUnit = trim($organizationalUnit);

            if ($organizationalUnit === '') {
                $organizationalUnit = null;
            }
        }

        // 1. Check file existence.
        if (!is_file($file)) {
            $this->error("Excel file not found: {$file}");

            return self::FAILURE;
        }

        // 2. Check database tables.
        if (!Schema::hasTable('industrial_facilities')) {
            $this->error('Table industrial_facilities does not exist.');

            return self::FAILURE;
        }

        if (!Schema::hasTable('import_batches')) {
            $this->error('Table import_batches does not exist.');

            return self::FAILURE;
        }

        // 3. Preserve existing Insert Only protection.
        if (IndustrialFacility::query()->exists()) {
            $this->error(
                'Import cancelled: industrial_facilities already contains records.'
            );

            $this->comment(
                'The current import strategy allows importing only into an empty target table.'
            );

            return self::FAILURE;
        }

        try {
            // 4. Validate before confirmation.
            $validation = $importService->validate($file);

            $fileHash = hash_file('sha256', $file);

            if ($fileHash === false) {
                throw new \RuntimeException(
                    'Unable to calculate the file SHA-256 hash.'
                );
            }

            // 5. Check duplicate file.
            if (ImportBatch::where('file_hash', $fileHash)->exists()) {
                $this->error(
                    'This Excel file has already been registered.'
                );

                return self::FAILURE;
            }

            // 6. Display import summary.
            $this->info('Excel validation passed.');

            $this->table(
                ['Item', 'Value'],
                [
                    ['File', $file],
                    ['Worksheet', 'البيانات'],
                    ['Columns', $validation['headers_count']],
                    ['Data rows', $validation['data_rows_count']],
                    [
                        'Organizational unit',
                        $organizationalUnit ?? '[NULL]',
                    ],
                    ['Target table', 'industrial_facilities'],
                    ['Import strategy', 'Insert Only'],
                ]
            );

            if (!$this->confirm(
                'Do you want to start importing these records?'
            )) {
                $this->warn('Import cancelled by user.');

                return self::SUCCESS;
            }

            // 7. Execute import through service.
            $result = $importService->import(
                $file,
                $organizationalUnit
            );

            // 8. Display result.
            $this->newLine();

            $this->info('Import completed successfully.');

            $this->table(
                ['Result', 'Value'],
                [
                    ['Batch ID', $result['batch_id']],
                    [
                        'Expected records',
                        $result['validation']['data_rows_count'],
                    ],
                    ['Imported records', $result['imported_rows']],
                    ['Status', 'completed'],
                ]
            );

            return self::SUCCESS;

        } catch (Throwable $exception) {
            $this->newLine();

            $this->error('Import failed.');
            $this->line($exception->getMessage());

            return self::FAILURE;
        }
    }
}
