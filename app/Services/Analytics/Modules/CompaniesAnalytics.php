<?php
// app/Services/Analytics/Modules/CompaniesAnalytics.php

namespace App\Services\Analytics\Modules;

use Illuminate\Support\Facades\DB;
use App\Services\Analytics\ValueNormalizer;
use App\Services\Analytics\ChartBuilders\BarBuilder;
use App\Services\Analytics\ChartBuilders\KpiBuilder;
use App\Services\Analytics\ChartBuilders\DoughnutBuilder;

class CompaniesAnalytics
{
    public function build(string $moduleCode, array $module): array
    {
        $rows = DB::table('companies')->get();

        $totalEmployees = $rows->sum(fn($r) => ValueNormalizer::number($r->employees_count ?? 0) ?? 0);

        $kpis = [
            KpiBuilder::card('total', 'إجمالي الشركات', $rows->count()),
            KpiBuilder::card('employees', 'إجمالي العاملين', (int) $totalEmployees),
            KpiBuilder::card('types', 'عدد الأنواع', $rows->pluck('company_type')->filter()->unique()->count()),
            KpiBuilder::card('governorates', 'عدد المحافظات', $rows->pluck('governorate')->filter()->unique()->count()),
        ];

        // 1) bar: العاملين لكل شركة
        $empRows = $rows->filter(fn($r) => !empty($r->name_ar))
            ->map(fn($r) => [
                'label' => trim((string) $r->name_ar),
                'value' => ValueNormalizer::number($r->employees_count ?? 0) ?? 0,
            ])->all();
        $charts[] = BarBuilder::build('employees_count', 'عدد العاملين لكل شركة', $empRows, true, 20);

        // 2) doughnut: نوع الشركة
        $typeRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->company_type ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('company_type', 'نوع الشركة', $typeRows);

        // 3) doughnut: الشكل القانوني
        $legalRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->legal_form ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('legal_form', 'الشكل القانوني', $legalRows);

        // 4) doughnut: موقف القيد في البورصة
        $stockRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->stock_listing_status ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('stock_listing_status', 'موقف القيد في البورصة', $stockRows);

        // 5) bar: التوزيع حسب المحافظة
        $govRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->governorate ?? '')),
            'value' => 1,
        ])->all();
        $charts[] = BarBuilder::build('governorate', 'التوزيع حسب المحافظة', $govRows);

        return [
            'module' => ['code' => $moduleCode, 'name_ar' => $module['name_ar']],
            'kpis'   => $kpis,
            'charts' => $charts,
            'map'    => ['points' => []],
        ];
    }
}