<?php

namespace App\Console\Commands;

use App\Models\ExcelImportBatch;
use App\Models\ExcelModule;
use App\Models\ExcelRecord;
use App\Models\ProductionLine;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * ينقل البيانات الموجودة في جدول production_lines القديم
 * إلى النظام الديناميكي (excel_modules + excel_records)
 *
 * الاستخدام:
 *   php artisan excel:migrate-production-lines
 *   php artisan excel:migrate-production-lines --fresh   # يمسح البيانات القديمة أولاً
 */
class MigrateProductionLinesToExcelRecords extends Command
{
    protected $signature = 'excel:migrate-production-lines
                            {--fresh : حذف سجلات PRODUCTION_LINES القديمة قبل النقل}';

    protected $description = 'نقل بيانات خطوط الإنتاج من الجدول القديم إلى النظام الديناميكي';

    /**
     * خريطة أعمدة الجدول القديم → مفاتيح الـ JSON
     * (بنفس أسماء الـ headers العربية تقريباً)
     */
    private const COLUMN_MAP = [
        'owner_organization'              => 'الجهة_المالكة',
        'location_scope'                  => 'نطاق_الموقع',
        'governorate'                     => 'المحافظة',
        'name'                            => 'اسم_خط_الانتاج',
        'product_name'                    => 'اسم_المنتج',
        'manufacturer'                    => 'الشركة_المصنعة_للخط',
        'operating_year'                  => 'سنة_التشغيل',
        'design_capacity'                 => 'الطاقة_التصميمية',
        'capacity_unit'                   => 'وحدة_الطاقة',
        'current_operational_capacity'    => 'الطاقة_التشغيلية_الحالية',
        'annual_production_capacity'      => 'طاقة_الانتاج_السنوية',
        'actual_production'               => 'الانتاج_الفعلي',
        'utilization_rate'                => 'نسبة_الاستغلال',
        'shifts_count'                    => 'عدد_الورديات',
        'daily_operating_hours'           => 'ساعات_التشغيل_اليومية',
        'workers_count'                   => 'عدد_العمال',
        'line_status'                     => 'حالة_الخط',
        'expected_lifetime_years'         => 'العمر_الافتراضي_بالسنوات',
        'last_maintenance_date'           => 'تاريخ_اخر_صيانة',
        'next_maintenance_date'           => 'تاريخ_الصيانة_القادمة',
        'production_increase_possibility' => 'إمكانية_زيادة_الانتاج',
        'production_increase_lead_time_days' => 'زمن_زيادة_الانتاج_بالايام',
        'development_type'                => 'نوع_التطوير',
        'development_completion_date'     => 'تاريخ_إنتهاء_التطوير',
        'company_name'                    => 'اسم_الجهة_الشركة',
        'address'                         => 'العنوان',
        'operating_status'                => 'حالة_التشغيل',
        'readiness_level'                 => 'مستوى_الجاهزية',
        'last_updated_date'               => 'تاريخ_اخر_تحديث',
        'data_entry_responsible'          => 'مسؤول_الادخال',
        'x_coordinate'                    => 'X',
        'y_coordinate'                    => 'Y',
        'unit_id'                         => 'كود_الوحدة',
    ];

    public function handle(): int
    {
        if (!class_exists(ProductionLine::class)) {
            $this->error('Model ProductionLine غير موجود. تأكد إن النظام القديم متثبت.');
            return self::FAILURE;
        }

        $total = ProductionLine::count();
        if ($total === 0) {
            $this->warn('لا توجد بيانات في جدول production_lines.');
            return self::SUCCESS;
        }

        $this->info("عدد السجلات في production_lines: {$total}");

        if ($this->option('fresh')) {
            $this->warn('سيتم حذف سجلات PRODUCTION_LINES من النظام الديناميكي أولاً...');
            if (!$this->confirm('هل أنت متأكد؟', true)) {
                $this->info('تم الإلغاء.');
                return self::SUCCESS;
            }
        }

        try {
            DB::transaction(function () use ($total) {
                // 1. Upsert module
                $module = ExcelModule::updateOrCreate(
                    ['code' => 'PRODUCTION_LINES'],
                    [
                        'name_ar' => 'خطوط الإنتاج',
                        'name_en' => 'Production Lines',
                        'template_version' => 'migrated-from-legacy',
                    ]
                );

                if ($this->option('fresh')) {
                    ExcelRecord::where('excel_module_id', $module->id)->delete();
                    ExcelImportBatch::where('excel_module_id', $module->id)->delete();
                }

                // 2. Create a synthetic batch
                $batch = ExcelImportBatch::create([
                    'excel_module_id'   => $module->id,
                    'user_id'           => null,
                    'original_filename' => 'migrated_from_production_lines_table',
                    'stored_path'       => 'migrated',
                    'file_hash'         => hash('sha256', 'migrated-' . now()->timestamp),
                    'total_rows'        => $total,
                    'status'            => 'processing',
                ]);

                // 3. Chunked transfer
                $imported = 0;
                $bar = $this->output->createProgressBar($total);
                $bar->start();

                ProductionLine::query()
                    ->orderBy('id')
                    ->chunkById(200, function ($lines) use ($module, $batch, &$imported, $bar) {
                        $rows = [];
                        foreach ($lines as $line) {
                            $payload = [];
                            foreach (self::COLUMN_MAP as $dbCol => $jsonKey) {
                                $payload[$jsonKey] = $line->{$dbCol} ?? null;
                            }

                            $rows[] = [
                                'excel_module_id'       => $module->id,
                                'excel_import_batch_id' => $batch->id,
                                'unit_code'             => $line->unit_id ? (string) $line->unit_id : null,
                                'unit_name'             => null,
                                'x_coordinate'          => is_numeric($line->x_coordinate) ? (float) $line->x_coordinate : null,
                                'y_coordinate'          => is_numeric($line->y_coordinate) ? (float) $line->y_coordinate : null,
                                'data'                  => json_encode($payload, JSON_UNESCAPED_UNICODE),
                                'created_at'            => $line->created_at ?? now(),
                                'updated_at'            => now(),
                            ];
                            $imported++;
                            $bar->advance();
                        }
                        if (!empty($rows)) {
                            ExcelRecord::insert($rows);
                        }
                    });

                $bar->finish();
                $this->newLine(2);

                // 4. Finalize
                $batch->update([
                    'imported_rows' => $imported,
                    'failed_rows'   => 0,
                    'status'        => 'completed',
                ]);

                $module->update([
                    'row_count'        => ExcelRecord::where('excel_module_id', $module->id)->count(),
                    'last_imported_at' => now(),
                    'last_headers'     => array_values(self::COLUMN_MAP),
                ]);

                $this->info("تم نقل {$imported} سجل بنجاح إلى الموديول PRODUCTION_LINES.");
            });
        } catch (Throwable $e) {
            $this->error('فشل النقل: ' . $e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
