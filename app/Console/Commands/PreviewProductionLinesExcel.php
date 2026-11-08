<?php

namespace App\Console\Commands;

use App\Models\Unit;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Throwable;

class PreviewProductionLinesExcel extends Command
{
    protected $signature = 'excel:preview-production-lines';

    protected $description = 'Validate extracted production lines without database insertion';

    public function handle(): int
    {
        $inputPath = storage_path('app/extracted/production_lines.json');

        if (!File::exists($inputPath)) {
            $this->error('Extracted JSON file not found.');
            return self::FAILURE;
        }

        try {
            $payload = json_decode(
                File::get($inputPath),
                true,
                512,
                JSON_THROW_ON_ERROR
            );

            if (!isset($payload['records']) || !is_array($payload['records'])) {
                $this->error('Invalid extraction JSON structure.');
                return self::FAILURE;
            }

            // Read existing unit IDs without modifying the database.
            $unitIds = Unit::query()
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $validatedRecords = [];

            $summary = [
                'total_records' => count($payload['records']),
                'valid_records' => 0,
                'warning_records' => 0,
                'blocked_records' => 0,
                'total_errors' => 0,
                'total_warnings' => 0,
                'ready_to_import' => 0,
            ];

            foreach ($payload['records'] as $index => $record) {
                $excelRow = $index + 2;

                if (!is_array($record)) {
                    $errors = ['بنية السجل غير صحيحة.'];

                    $summary['blocked_records']++;
                    $summary['total_errors']++;

                    $validatedRecords[] = [
                        'excel_row' => $excelRow,
                        'status' => 'blocked',
                        'errors' => $errors,
                        'warnings' => [],
                        'data' => $record,
                    ];

                    continue;
                }

                $excelRow = $record['_excel_row'] ?? $excelRow;

                $errors = [];
                $warnings = [];

                /*
                 * 1. Validate unit_id.
                 * It is the only required business field.
                 */
                $unitId = $record['unit_id'] ?? null;

                if ($unitId === null || $unitId === '') {
                    $errors[] = 'كود الوحدة مفقود.';
                } elseif (
                    !is_numeric($unitId)
                    || (float) $unitId <= 0
                    || (float) $unitId != (int) $unitId
                ) {
                    $errors[] = 'كود الوحدة يجب أن يكون رقمًا صحيحًا موجبًا.';
                } elseif (!in_array((int) $unitId, $unitIds, true)) {
                    $errors[] = 'كود الوحدة غير موجود في جدول units.';
                }

                /*
                 * 2. Validate name.
                 * Empty names are allowed.
                 */
                $name = $record['name'] ?? null;

                if ($name === null || $name === '') {
                    $warnings[] = 'اسم خط الإنتاج فارغ، والاستيراد مسموح.';
                } elseif (is_string($name) && trim($name) === '') {
                    $warnings[] = 'اسم خط الإنتاج فارغ، والاستيراد مسموح.';
                } elseif (!is_string($name) && !is_numeric($name)) {
                    $warnings[] = 'اسم خط الإنتاج يحتوي على قيمة غير نصية، يلزم مراجعتها.';
                }

                /*
                 * 3. Validate coordinates.
                 * Original values remain unchanged.
                 *
                 * Approximate expected bounds for Egypt:
                 * X / Longitude: 24 to 38
                 * Y / Latitude: 21 to 32
                 */
                $x = $record['x_coordinate'] ?? null;
                $y = $record['y_coordinate'] ?? null;

                $hasX = $x !== null && $x !== '';
                $hasY = $y !== null && $y !== '';

                if ($hasX xor $hasY) {
                    $warnings[] = 'الإحداثيات غير مكتملة.';
                } elseif ($hasX && $hasY) {
                    if (!is_numeric($x) || !is_numeric($y)) {
                        $warnings[] = 'الإحداثيات تحتوي على قيمة غير رقمية.';
                    } else {
                        $longitude = (float) $x;
                        $latitude = (float) $y;

                        if (
                            !is_finite($longitude)
                            || !is_finite($latitude)
                        ) {
                            $warnings[] = 'الإحداثيات تحتوي على قيمة غير صالحة.';
                        } elseif (
                            $longitude < 24
                            || $longitude > 38
                            || $latitude < 21
                            || $latitude > 32
                        ) {
                            $warnings[] = 'الإحداثيات خارج النطاق الجغرافي المتوقع؛ يلزم مراجعتها.';
                        }
                    }
                }

                /*
                 * 4. Determine status.
                 */
                if (count($errors) > 0) {
                    $status = 'blocked';
                    $summary['blocked_records']++;
                } elseif (count($warnings) > 0) {
                    $status = 'warning';
                    $summary['warning_records']++;
                    $summary['ready_to_import']++;
                } else {
                    $status = 'valid';
                    $summary['valid_records']++;
                    $summary['ready_to_import']++;
                }

                $summary['total_errors'] += count($errors);
                $summary['total_warnings'] += count($warnings);

                /*
                 * 5. Preserve the original extracted values.
                 */
                $data = $record;
                unset($data['_excel_row']);

                $validatedRecords[] = [
                    'excel_row' => $excelRow,
                    'status' => $status,
                    'errors' => $errors,
                    'warnings' => $warnings,
                    'data' => $data,
                ];
            }

            /*
             * 6. Build preview report.
             */
            $preview = [
                'metadata' => [
                    'source' => $payload['metadata'] ?? [],
                    'validated_at' => now()->toIso8601String(),
                    'database_insertion_performed' => false,
                ],
                'summary' => $summary,
                'records' => $validatedRecords,
            ];

            $json = json_encode(
                $preview,
                JSON_PRETTY_PRINT
                | JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
                | JSON_INVALID_UTF8_SUBSTITUTE
            );

            if ($json === false) {
                $this->error('Failed to generate preview JSON.');
                return self::FAILURE;
            }

            /*
             * 7. Save preview report.
             */
            $outputDirectory = storage_path('app/previews');

            File::ensureDirectoryExists($outputDirectory);

            $outputPath = $outputDirectory . '/production_lines_preview.json';

            File::put($outputPath, $json);

            /*
             * 8. Display summary.
             */
            $this->newLine();

            $this->info('Production Lines Preview completed.');

            $this->table(
                ['Validation result', 'Count'],
                [
                    ['Total records', $summary['total_records']],
                    ['Valid records', $summary['valid_records']],
                    ['Records with warnings', $summary['warning_records']],
                    ['Blocked records', $summary['blocked_records']],
                    ['Total errors', $summary['total_errors']],
                    ['Total warnings', $summary['total_warnings']],
                    ['Ready to import', $summary['ready_to_import']],
                ]
            );

            /*
             * 9. Display records requiring review.
             */
            $reviewRecords = array_filter(
                $validatedRecords,
                fn ($item) => $item['status'] !== 'valid'
            );

            if (!empty($reviewRecords)) {
                $this->newLine();
                $this->warn('Records requiring review:');

                $rows = [];

                foreach (
                    array_slice(array_values($reviewRecords), 0, 30)
                    as $item
                ) {
                    $messages = array_merge(
                        $item['errors'],
                        $item['warnings']
                    );

                    $rows[] = [
                        $item['excel_row'],
                        $item['status'],
                        implode(' | ', $messages),
                    ];
                }

                $this->table(
                    ['Excel row', 'Status', 'Details'],
                    $rows
                );
            }

            $this->newLine();

            $this->info("Preview saved to: {$outputPath}");

            $this->comment(
                'No records were inserted or modified in the database.'
            );

            return self::SUCCESS;

        } catch (Throwable $exception) {
            $this->error('Preview failed: ' . $exception->getMessage());

            $this->error(
                'File: ' . $exception->getFile()
                . ' | Line: ' . $exception->getLine()
            );

            return self::FAILURE;
        }
    }
}
