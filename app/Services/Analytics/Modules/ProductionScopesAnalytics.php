<?php
// app/Services/Analytics/Modules/ProductionScopesAnalytics.php

namespace App\Services\Analytics\Modules;

use Illuminate\Support\Facades\DB;
use App\Services\Analytics\ChartBuilders\BarBuilder;
use App\Services\Analytics\ChartBuilders\KpiBuilder;
use App\Services\Analytics\ChartBuilders\DoughnutBuilder;

class ProductionScopesAnalytics
{
    public function build(string $moduleCode, array $module): array
    {
        $rows = DB::table('production_scopes')->get();

        $kpis = [
            KpiBuilder::card('total', 'إجمالي السجلات', $rows->count()),
            KpiBuilder::card('companies', 'عدد الجهات', $rows->pluck('company_name')->filter()->unique()->count()),
            KpiBuilder::card('products', 'عدد المنتجات', $rows->pluck('product_name')->filter()->unique()->count()),
            KpiBuilder::card('manufacturers', 'عدد الشركات المصنعة', $rows->pluck('manufacturer')->filter()->unique()->count()),
        ];

        // 1) bar: الخطوط لكل جهة/شركة
        $companyRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->company_name ?? '')),
            'value' => 1,
        ])->all();
        $charts[] = BarBuilder::build('company_name', 'الخطوط لكل جهة/شركة', $companyRows, true, 15);

        // 2) bar: أعلى 10 شركات مصنعة للخط
        $mfgRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->manufacturer ?? '')),
            'value' => 1,
        ])->all();
        $charts[] = BarBuilder::build('manufacturer', 'أعلى 10 شركات مصنعة', $mfgRows, false, 10);

        // 3) bar: أعلى 10 منتجات
        $prodRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->product_name ?? '')),
            'value' => 1,
        ])->all();
        $charts[] = BarBuilder::build('product_name', 'أعلى 10 منتجات', $prodRows, true, 10);

        return [
            'module' => ['code' => $moduleCode, 'name_ar' => $module['name_ar']],
            'kpis'   => $kpis,
            'charts' => $charts,
            'map'    => ['points' => []],
        ];
    }
}