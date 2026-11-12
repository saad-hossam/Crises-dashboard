<?php
// app/Services/Analytics/Modules/WarehousesAnalytics.php

namespace App\Services\Analytics\Modules;

use Illuminate\Support\Facades\DB;
use App\Services\Analytics\ValueNormalizer;
use App\Services\Analytics\ChartBuilders\BarBuilder;
use App\Services\Analytics\ChartBuilders\KpiBuilder;
use App\Services\Analytics\ChartBuilders\DoughnutBuilder;

class WarehousesAnalytics
{
    public function build(string $moduleCode, array $module): array
    {
        $rows = DB::table('warehouses')->get();

        $totalArea = $rows->sum(fn($r) => ValueNormalizer::areaToSqm($r->area ?? null) ?? 0);

        $kpis = [
            KpiBuilder::card('total', 'إجمالي المخازن', $rows->count()),
            KpiBuilder::card('area', 'إجمالي المساحة', (int) $totalArea, 'م²'),
            KpiBuilder::card('types', 'عدد الأنواع', $rows->pluck('warehouse_type')->filter()->unique()->count()),
            KpiBuilder::card('companies', 'عدد الشركات', $rows->pluck('company_name')->filter()->unique()->count()),
        ];

        // 1) bar: نوع المخزن أعلى 10
        $typeRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->warehouse_type ?? '')),
            'value' => 1,
        ])->all();
        $charts[] = BarBuilder::build('warehouse_type', 'أنواع المخازن (أعلى 10)', $typeRows, false, 10);

        // 2) bar: مجموع المساحة لكل شركة
        $areaByCompany = [];
        foreach ($rows as $r) {
            $label = trim((string) ($r->company_name ?? ''));
            if ($label === '') continue;
            $area = ValueNormalizer::areaToSqm($r->area ?? null) ?? 0;
            $areaByCompany[$label] = ($areaByCompany[$label] ?? 0) + $area;
        }
        $areaData = [];
        foreach ($areaByCompany as $l => $v) {
            $areaData[] = ['label' => $l, 'value' => $v];
        }
        $charts[] = BarBuilder::build('area', 'إجمالي المساحة لكل شركة (م²)', $areaData, true, 15);

        // 3) doughnut: التبريد
        $refRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->refrigeration ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('refrigeration', 'تبريد/تجميد', $refRows);

        // 4) doughnut: الحماية المدنية
        $civilRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->civil_defense_status ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('civil_defense_status', 'حالة الحماية المدنية', $civilRows);

        return [
            'module' => ['code' => $moduleCode, 'name_ar' => $module['name_ar']],
            'kpis'   => $kpis,
            'charts' => $charts,
            'map'    => ['points' => []],
        ];
    }
}