<?php
// app/Services/Analytics/Modules/ContractsOperationsAnalytics.php

namespace App\Services\Analytics\Modules;

use Illuminate\Support\Facades\DB;
use App\Services\Analytics\ValueNormalizer;
use App\Services\Analytics\ChartBuilders\BarBuilder;
use App\Services\Analytics\ChartBuilders\KpiBuilder;
use App\Services\Analytics\ChartBuilders\DoughnutBuilder;

class ContractsOperationsAnalytics
{
    public function build(string $moduleCode, array $module): array
    {
        $rows = DB::table('contracts_operations')->get();

        $kpis = [
            KpiBuilder::card('total', 'إجمالي العقود', $rows->count()),
            KpiBuilder::card('types', 'عدد الأنواع', $rows->pluck('contract_type')->filter()->unique()->count()),
            KpiBuilder::card('companies', 'عدد الشركات', $rows->pluck('company_name')->filter()->unique()->count()),
            KpiBuilder::card('units', 'عدد الوحدات', $rows->pluck('unit_name')->filter()->unique()->count()),
        ];

        // 1) doughnut: حالة العقد (operating_status)
        $statusRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->operating_status ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('operating_status', 'حالة العقد', $statusRows);

        // 2) bar: نوع العقد أعلى 10
        $typeRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->contract_type ?? '')),
            'value' => 1,
        ])->all();
        $charts[] = BarBuilder::build('contract_type', 'أنواع العقود (أعلى 10)', $typeRows, true, 10);

        // 3) bar: عقود لكل وحدة
        $unitRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->unit_name ?? '')),
            'value' => 1,
        ])->all();
        $charts[] = BarBuilder::build('unit_name', 'العقود لكل وحدة', $unitRows, true, 15);

        // 4) bar: عقود تنتهي كل سنة
        $byYear = [];
        foreach ($rows as $r) {
            $date = $r->end_date ?? null;
            if (!$date) continue;
            $year = substr((string) $date, 0, 4);
            if (!ctype_digit($year)) continue;
            $byYear[$year] = ($byYear[$year] ?? 0) + 1;
        }
        ksort($byYear);
        $yearData = [];
        foreach ($byYear as $l => $v) {
            $yearData[] = ['label' => $l, 'value' => $v];
        }
        if (count($yearData) > 0) {
            $charts[] = BarBuilder::build('end_date', 'العقود المنتهية كل سنة', $yearData);
        }

        return [
            'module' => ['code' => $moduleCode, 'name_ar' => $module['name_ar']],
            'kpis'   => $kpis,
            'charts' => $charts,
            'map'    => ['points' => []],
        ];
    }
}