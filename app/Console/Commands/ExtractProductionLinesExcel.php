<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use RuntimeException;
use Throwable;

class ExtractProductionLinesExcel extends Command
{
    protected $signature = 'excel:extract-production-lines
                            {file? : Excel file full path}';

    protected $description = 'Extract production lines from Excel to JSON without database insertion';

    public function handle(): int
    {
        $filePath = $this->argument('file')
            ?: 'C:/Users/pc/Downloads/Production lines.xlsx';

        if (!is_file($filePath)) {
            $this->error("Excel file not found: {$filePath}");

            return self::FAILURE;
        }

        try {
            $spreadsheet = IOFactory::load($filePath);

            $sheet = $spreadsheet->getSheetByName('البيانات');

            if (!$sheet) {
                $this->error('Worksheet البيانات was not found.');

                return self::FAILURE;
            }

            /*
             * Excel headers => production_lines columns.
             *
             * الوحدة is intentionally ignored.
             * كود الوحدة maps directly to unit_id.
             */
            $mapping = [
                'الجهة المالكة' => 'owner_organization',
                'نطاق الموقع' => 'location_scope',
                'المحافظة' => 'governorate',
                'اسم خط الانتاج' => 'name',
                'اسم المنتج' => 'product_name',
                'الشركة المصنعة للخط' => 'manufacturer',
                'سنة التشغيل' => 'operating_year',
                'الطاقة التصميمية' => 'design_capacity',
                'وحدة الطاقة' => 'capacity_unit',
                'الطاقة التشغيلية الحالية' => 'current_operational_capacity',
                'طاقة الانتاج السنوية' => 'annual_production_capacity',
                'الانتاج الفعلي' => 'actual_production',
                'نسبة الاستغلال' => 'utilization_rate',
                'عدد الوديات' => 'shifts_count',
                'ساعات التشغيل اليومية' => 'daily_operating_hours',
                'عدد العمال' => 'workers_count',
                'حالة الخط' => 'line_status',
                'العمر الافتراضي بالسنوات' => 'expected_lifetime_years',
                'تاريخ اخر صيانة' => 'last_maintenance_date',
                'تاريخ الصيانة القادمة' => 'next_maintenance_date',
                'إمكانية زيادة الانتاج' => 'production_increase_possibility',
                'زمن زيادة الإنتاج بالأيام' => 'production_increase_lead_time_days',
                'اسم الجهة / الشركة' => 'company_name',
                'العنوان' => 'address',
                'حالة التشغيل' => 'operating_status',
                'مستوى الجاهزية' => 'readiness_level',
                'تاريخ أخر تحديث' => 'last_updated_date',
                'مسئول الإدخال' => 'data_entry_responsible',
                'نوع التطوير' => 'development_type',
                'تاريخ إنتهاء التطوير' => 'development_completion_date',
                'X' => 'x_coordinate',
                'Y' => 'y_coordinate',
                'كود الوحدة' => 'unit_id',
            ];

            /*
             * Read Excel headers.
             */
            $highestColumn = $sheet->getHighestDataColumn();

            $highestColumnIndex = Coordinate::columnIndexFromString(
                $highestColumn
            );

            $headers = [];

            for ($columnIndex = 1; $columnIndex <= $highestColumnIndex; $columnIndex++) {
                $columnLetter = Coordinate::stringFromColumnIndex(
                    $columnIndex
                );

                $header = $sheet->getCell($columnLetter . '1')->getValue();

                if ($header !== null && $header !== '') {
                    $headers[trim((string) $header)] = $columnLetter;
                }
            }

            /*
             * Check required headers.
             */
            $missingHeaders = array_diff(
                array_keys($mapping),
                array_keys($headers)
            );

            if (!empty($missingHeaders)) {
                $this->error('Missing Excel headers:');

                foreach ($missingHeaders as $header) {
                    $this->line('- ' . $header);
                }

                return self::FAILURE;
            }

            /*
             * Extract records.
             */
            $records = [];

            $formulaCount = 0;
            $missingFormulaCacheCount = 0;
            $emptyCount = 0;

            $highestRow = $sheet->getHighestDataRow();

            for ($row = 2; $row <= $highestRow; $row++) {
                $record = [
                    '_excel_row' => $row,
                ];

                foreach ($mapping as $excelHeader => $databaseField) {
                    $column = $headers[$excelHeader];

                    $cell = $sheet->getCell($column . $row);

                    $value = $this->readCellValue(
                        $cell,
                        $excelHeader,
                        $row,
                        $formulaCount,
                        $missingFormulaCacheCount
                    );

                    if ($value === null || $value === '') {
                        $emptyCount++;
                    }

                    $record[$databaseField] = $value;
                }

                // Keep records even when the line name is blank.
                $records[] = $record;
            }

            /*
             * Build extraction output.
             */
            $output = [
                'metadata' => [
                    'file_name' => basename($filePath),
                    'worksheet' => 'البيانات',
                    'header_row' => 1,
                    'first_data_row' => 2,
                    'last_data_row' => $highestRow,
                    'total_records' => count($records),
                    'total_mapped_columns' => count($mapping),
                    'formula_cells' => $formulaCount,
                    'formula_cells_without_cached_value' => $missingFormulaCacheCount,
                    'empty_cells' => $emptyCount,
                    'ignored_excel_column' => 'الوحدة',
                    'unit_id_source' => 'كود الوحدة',
                    'database_insertion_performed' => false,
                    'extracted_at' => now()->toIso8601String(),
                ],

                'records' => $records,
            ];

            /*
             * Convert to JSON.
             */
            $json = json_encode(
                $output,
                JSON_PRETTY_PRINT
                | JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
                | JSON_INVALID_UTF8_SUBSTITUTE
            );

            if ($json === false) {
                $this->error('Failed to encode extraction data into JSON.');

                return self::FAILURE;
            }

            /*
             * Save JSON.
             */
            $outputDirectory = storage_path('app/extracted');

            File::ensureDirectoryExists($outputDirectory);

            $outputPath = $outputDirectory . '/production_lines.json';

            if (File::put($outputPath, $json) === false) {
                $this->error('Failed to write extraction JSON file.');

                return self::FAILURE;
            }

            /*
             * Display results.
             */
            $this->newLine();

            $this->info('Excel extraction completed successfully.');

            $this->table(
                ['Item', 'Result'],
                [
                    ['Records', count($records)],
                    ['Mapped columns', count($mapping)],
                    ['Formula cells', $formulaCount],
                    ['Formulas without cached values', $missingFormulaCacheCount],
                    ['Empty cells', $emptyCount],
                    ['Database insertions', '0'],
                ]
            );

            $this->info("Output file: {$outputPath}");

            return self::SUCCESS;

        } catch (Throwable $exception) {
            $this->error(
                'Extraction failed: ' . $exception->getMessage()
            );

            $this->error(
                'File: ' . $exception->getFile()
                . ' | Line: ' . $exception->getLine()
            );

            return self::FAILURE;
        }
    }

    /**
     * Read the original Excel cell value.
     *
     * Formula cells:
     * Use cached calculated values.
     *
     * Rich Text cells:
     * Convert to their actual visible text.
     *
     * No database insertion.
     */
    private function readCellValue(
        Cell $cell,
        string $excelHeader,
        int $row,
        int &$formulaCount,
        int &$missingFormulaCacheCount
    ): mixed {

        /*
         * 1. Read formula or regular cell.
         */
        if ($cell->isFormula()) {
            $formulaCount++;

            $value = $cell->getOldCalculatedValue();

            if ($value === null) {
                $missingFormulaCacheCount++;
            }
        } else {
            $value = $cell->getValue();
        }

        /*
         * 2. Handle Rich Text objects.
         */
        if ($value instanceof RichText) {
            $value = $value->getPlainText();
        } elseif (
            is_object($value)
            && method_exists($value, 'getPlainText')
        ) {
            $value = $value->getPlainText();
        }

        /*
         * 3. Special handling for production line names.
         *
         * Names are textual fields, so if the original value
         * remains complex, use the displayed cell text.
         */
        if ($excelHeader === 'اسم خط الانتاج') {

            if (is_array($value) || is_object($value)) {
                $value = $cell->getFormattedValue();
            }

            if ($value === null || $value === '') {
                return null;
            }

            if (is_array($value) || is_object($value)) {
                throw new RuntimeException(
                    "Cannot extract production line name as plain text. "
                    . "Excel row: {$row}"
                );
            }

            if (!is_scalar($value)) {
                throw new RuntimeException(
                    "Unsupported production line name value. "
                    . "Excel row: {$row}"
                );
            }

            return (string) $value;
        }

        /*
         * 4. Never save unexpected complex values.
         */
        if (is_array($value) || is_object($value)) {
            throw new RuntimeException(
                "Unsupported value in Excel column '{$excelHeader}' "
                . "at row {$row}. Value type: " . get_debug_type($value)
            );
        }

        return $value;
    }
}
