<?php
// app/Services/Analytics/Modules/SubsidiaryCompaniesAnalytics.php

namespace App\Services\Analytics\Modules;

use Illuminate\Support\Facades\DB;
use App\Services\Analytics\ValueNormalizer;
use App\Services\Analytics\ChartBuilders\KpiBuilder;

class SubsidiaryCompaniesAnalytics
{
    public function build(string $moduleCode, array $module): array
    {
        $rows = DB::table('subsidiary_companies')->get();

        $kpis = [
            KpiBuilder::card('total', 'إجمالي الشركات التابعة', $rows->count()),
            KpiBuilder::card('employees', 'إجمالي العاملين', (int) $rows->sum(fn($r) => ValueNormalizer::number($r->employees_count ?? 0) ?? 0)),
            KpiBuilder::card('factories', 'إجمالي المصانع', (int) $rows->sum(fn($r) => ValueNormalizer::number($r->factories_count ?? 0) ?? 0)),
            KpiBuilder::card('sites', 'إجمالي المواقع', (int) $rows->sum(fn($r) => ValueNormalizer::number($r->sites_count ?? 0) ?? 0)),
        ];

        return [
            'module' => ['code' => $moduleCode, 'name_ar' => $module['name_ar']],
            'kpis'   => $kpis,
            'charts' => [],
            'map'    => ['points' => []],
        ];
    }
}