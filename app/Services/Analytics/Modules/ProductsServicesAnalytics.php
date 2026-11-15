<?php
// app/Services/Analytics/Modules/ProductsServicesAnalytics.php

namespace App\Services\Analytics\Modules;

use Illuminate\Support\Facades\DB;
use App\Services\Analytics\ValueNormalizer;
use App\Services\Analytics\ChartBuilders\BarBuilder;
use App\Services\Analytics\ChartBuilders\KpiBuilder;
use App\Services\Analytics\ChartBuilders\DoughnutBuilder;

class ProductsServicesAnalytics
{
    public function build(string $moduleCode, array $module): array
    {
        $rows = DB::table('products_services')->get();

        $kpis = [
            KpiBuilder::card('total', 'إجمالي المنتجات/الخدمات', $rows->count()),
            KpiBuilder::card('companies', 'عدد الشركات', $rows->pluck('company_name')->filter()->unique()->count()),
            KpiBuilder::card('target_markets', 'الأسواق المستهدفة', $rows->pluck('target_market')->filter()->unique()->count()),
            KpiBuilder::card('units', 'عدد الوحدات', $rows->pluck('unit_name')->filter()->unique()->count()),
        ];

        // 1) bar: المنتجات لكل شركة (أعلى 10)
        $companyRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->company_name ?? '')),
            'value' => 1,
        ])->all();
        $charts[] = BarBuilder::build('company_name', 'المنتجات لكل شركة (أعلى 10)', $companyRows, true, 10);

        // 2) doughnut: السوق المستهدف
        $marketRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->target_market ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('target_market', 'السوق المستهدف', $marketRows);

        // 3) doughnut: إمكانية زيادة الإنتاج
        $incRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->can_increase_production ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('can_increase_production', 'إمكانية زيادة الإنتاج', $incRows);

        // 4) doughnut: مستوى الجاهزية
        $readyRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->readiness_level ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('readiness_level', 'مستوى الجاهزية', $readyRows);

        // 5) bar: أعلى 10 منتجات
        $nameRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->name ?? '')),
            'value' => 1,
        ])->all();
        $charts[] = BarBuilder::build('name', 'أعلى 10 منتجات', $nameRows, true, 10);

        return [
            'module' => ['code' => $moduleCode, 'name_ar' => $module['name_ar']],
            'kpis'   => $kpis,
            'charts' => $charts,
            'map'    => ['points' => []],
        ];
    }
}