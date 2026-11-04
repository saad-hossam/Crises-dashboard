<?php

namespace App\Console\Commands;

use Throwable;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\IndustrialFacilityColumns;
use App\Imports\IndustrialFacilitiesSheetImport;
use App\Imports\IndustrialFacilitiesPreviewImport;

class InspectIndustrialFacilitiesImport extends Command
{
    protected $signature = 'industrial:inspect {file}';

    protected $description = 'Inspect all Excel data mappings without inserting records';

    public function handle(): int
    {
        $file = $this->argument('file');

        if (!is_file($file)) {
            $this->error("File not found: {$file}");

            return self::FAILURE;
        }

        try {
            // Read Excel file.
            $preview = new IndustrialFacilitiesPreviewImport();

            Excel::import($preview, $file);

            $rows = $preview->rows;

            if (empty($rows)) {
                $this->error('Worksheet is empty or missing.');

                return self::FAILURE;
            }

            // Validate headers.
            $actualHeaders = array_map(
                fn ($value) => trim((string) $value),
                $rows[0]
            );

            $expectedHeaders = IndustrialFacilityColumns::expectedHeaders();

            if (
                count($actualHeaders) !== 44 ||
                $actualHeaders !== $expectedHeaders
            ) {
                $this->error('Header validation failed.');
                $this->line('Run industrial:preview to see differences.');

                return self::FAILURE;
            }

            // Get non-empty data rows.
            $dataRows = array_slice($rows, 1);

            $validRows = array_filter($dataRows, function ($row) {
                return count(array_filter(
                    $row,
                    fn ($value) => $value !== null && $value !== ''
                )) > 0;
            });

            if (empty($validRows)) {
                $this->error('No data rows found.');

                return self::FAILURE;
            }

            $this->info('Header validation passed.');
            $this->line('Columns: ' . count($actualHeaders));
            $this->line('Data rows: ' . count($validRows));

            $mappedCount = 0;

            $this->newLine();
            $this->info('Inspecting all data rows...');

            foreach ($validRows as $index => $row) {

                // Map row without saving it.
                $model = (new IndustrialFacilitiesSheetImport())->model($row);

                if (!$model) {
                    $this->error(
                        'Failed to map Excel row: ' . ($index + 2)
                    );

                    return self::FAILURE;
                }

                $mappedCount++;

                $this->newLine();
                $this->info('Excel Row: ' . ($index + 2));

                $this->line(
                    'Facility: ' . ($model->facility_name ?? '[NULL]')
                );

                $this->line(
                    'Facility Number: ' . ($model->facility_number ?? '[NULL]')
                );

                $this->line(
                    'Employees: ' . ($model->total_employees ?? '[NULL]')
                );

                $this->line(
                    'Longitude: ' . ($model->longitude_x ?? '[NULL]')
                );

                $this->line(
                    'Latitude: ' . ($model->latitude_y ?? '[NULL]')
                );

                // Inspect raw Excel date values.
                $this->line('Date fields raw values:');

                $dateColumns = [
                    'register_issue_date' => 10,
                    'register_expiry_date' => 11,
                    'license_issue_date' => 39,
                ];

                foreach ($dateColumns as $field => $columnIndex) {
                    $value = $row[$columnIndex] ?? null;

                    $this->line(
                        $field . ': ' .
                        var_export($value, true) .
                        ' | PHP Type: ' . get_debug_type($value)
                    );
                }

                // Inspect coordinate values.
                $this->line('Coordinate raw values:');

                foreach ([
                    'longitude_x' => 42,
                    'latitude_y' => 43,
                ] as $field => $columnIndex) {
                    $value = $row[$columnIndex] ?? null;

                    $this->line(
                        $field . ': ' .
                        var_export($value, true) .
                        ' | PHP Type: ' . get_debug_type($value)
                    );
                }
            }

            $this->newLine();
            $this->info("Successfully mapped {$mappedCount} rows.");

            $this->comment(
                'Inspection completed. No database records were inserted.'
            );

            return self::SUCCESS;

        } catch (Throwable $exception) {
            $this->error('Inspection failed.');
            $this->line($exception->getMessage());

            return self::FAILURE;
        }
    }
}
