<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\IndustrialFacilityColumns;
use App\Imports\IndustrialFacilitiesPreviewImport;

class PreviewIndustrialFacilitiesImport extends Command
{
    protected $signature = 'industrial:preview {file}';

    protected $description = 'Preview and validate the industrial facilities Excel file';

    public function handle(): int
    {
        $file = $this->argument('file');

        if (!is_file($file)) {
            $this->error("File not found: {$file}");

            return self::FAILURE;
        }

        $import = new IndustrialFacilitiesPreviewImport();

        try {
            Excel::import($import, $file);
        } catch (\Throwable $exception) {
            $this->error('Unable to read Excel file.');
            $this->line($exception->getMessage());

            return self::FAILURE;
        }

        $rows = $import->rows;

        if (empty($rows)) {
            $this->error('The main sheet is empty or was not found.');

            return self::FAILURE;
        }

        $actualHeaders = array_map(
            fn ($value) => trim((string) $value),
            $rows[0]
        );

        $expectedHeaders = IndustrialFacilityColumns::expectedHeaders();

        $this->info('Excel file successfully read.');
        $this->line('Expected columns: ' . count($expectedHeaders));
        $this->line('Actual columns: ' . count($actualHeaders));

        $headersMatch = $actualHeaders === $expectedHeaders;

        if (!$headersMatch) {
            $this->error('Header validation failed.');

            foreach ($expectedHeaders as $index => $expected) {
                $actual = $actualHeaders[$index] ?? '[MISSING]';

                if ($expected !== $actual) {
                    $this->line(
                        'Column ' . ($index + 1) .
                        ' | Expected: ' . $expected .
                        ' | Actual: ' . $actual
                    );
                }
            }

            return self::FAILURE;
        }

        $dataRows = array_slice($rows, 1);

        $validRows = array_filter($dataRows, function ($row) {
            return count(array_filter(
                $row,
                fn ($value) => $value !== null && $value !== ''
            )) > 0;
        });

        $this->newLine();
        $this->info('Header validation passed.');
        $this->line('Data rows: ' . count($validRows));

        $this->newLine();
        $this->info('First data row preview:');

        if (!empty($validRows)) {
            $firstRow = reset($validRows);

            foreach ($expectedHeaders as $index => $header) {
                $this->line(
                    $header . ': ' . ($firstRow[$index] ?? '')
                );
            }
        }

        return self::SUCCESS;
    }
}
