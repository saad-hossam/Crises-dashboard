<?php
// app/Services/Analytics/Modules/ProductiveCapacitiesAnalytics.php

namespace App\Services\Analytics\Modules;

use Illuminate\Support\Facades\DB;
use App\Services\Analytics\ChartBuilders\BarBuilder;
use App\Services\Analytics\ChartBuilders\KpiBuilder;
use App\Services\Analytics\ChartBuilders\DoughnutBuilder;

class ProductiveCapacitiesAnalytics
{
    public function build(string $moduleCode, array $module): array
    {
        $rows = DB::table('productive_capacities')->get();

        $kpis = [
            KpiBuilder::card('total', 'إجمالي السجلات', $rows->count()),
            KpiBuilder::card('products', 'عدد المنتجات', $rows->pluck('product_name')->filter()->unique()->count()),
            KpiBuilder::card('companies', 'عدد الشركات', $rows->pluck('company_name')->filter()->unique()->count()),
            KpiBuilder::card('units', 'عدد الوحدات', $rows->pluck('unit_name')->filter()->unique()->count()),
        ];

        // 1) bar: المنتجات لكل شركة
        $companyRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->company_name ?? '')),
            'value' => 1,
        ])->all();
        $charts[] = BarBuilder::build('company_name', 'المنتجات لكل شركة', $companyRows, true, 15);

        // 2) doughnut: حالة التشغيل
        $statusRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->operating_status ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('operating_status', 'حالة التشغيل', $statusRows);

        // 3) doughnut: إمكانية تحويل الخط
        $convRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->line_convertible ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('line_convertible', 'إمكانية تحويل الخط', $convRows);

        // 4) bar: أعلى 10 منتجات اعتماداً على الاستيراد
        $importRows = $rows->filter(fn($r) => !empty($r->product_name))
            ->map(fn($r) => [
                'label' => trim((string) $r->product_name),
                'value' => (float) (preg_replace('/[^0-9.]/', '', (string) ($r->import_dependency_percent ?? '0')) ?: 0),
            ])->all();
        $charts[] = BarBuilder::build('import_dependency_percent', 'أعلى 10 منتجات اعتماداً على الاستيراد (%)', $importRows, true, 10);

        return [
            'module' => ['code' => $moduleCode, 'name_ar' => $module['name_ar']],
            'kpis'   => $kpis,
            'charts' => $charts,
            'map'    => ['points' => []],
        ];
    }
}