<?php
// app/Services/Analytics/Modules/UnusedAssetsAnalytics.php

namespace App\Services\Analytics\Modules;

use Illuminate\Support\Facades\DB;
use App\Services\Analytics\ValueNormalizer;
use App\Services\Analytics\ChartBuilders\BarBuilder;
use App\Services\Analytics\ChartBuilders\KpiBuilder;
use App\Services\Analytics\ChartBuilders\DoughnutBuilder;

class UnusedAssetsAnalytics
{
    public function build(string $moduleCode, array $module): array
    {
        $rows = DB::table('unused_assets')->get();

        $totalValue = $rows->sum(fn($r) => ValueNormalizer::number($r->estimated_value ?? 0) ?? 0);

        $kpis = [
            KpiBuilder::card('total', 'إجمالي الأصول', $rows->count()),
            KpiBuilder::card('value', 'إجمالي القيمة التقديرية', (int) $totalValue),
            KpiBuilder::card('priorities', 'أولويات التدخل', $rows->pluck('intervention_priority')->filter()->unique()->count()),
            KpiBuilder::card('companies', 'عدد الشركات', $rows->pluck('company_name')->filter()->unique()->count()),
        ];

        // 1) doughnut: سبب عدم الاستغلال
        $reasonRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->underutilization_reason ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('underutilization_reason', 'سبب عدم الاستغلال', $reasonRows);

        // 2) doughnut: أولوية التدخل
        $prioRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->intervention_priority ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('intervention_priority', 'أولوية التدخل', $prioRows);

        // 3) bar: مجموع القيمة التقديرية لكل شركة
        $valByCompany = [];
        foreach ($rows as $r) {
            $label = trim((string) ($r->company_name ?? ''));
            if ($label === '') continue;
            $val = ValueNormalizer::number($r->estimated_value ?? 0) ?? 0;
            $valByCompany[$label] = ($valByCompany[$label] ?? 0) + $val;
        }
        $valData = [];
        foreach ($valByCompany as $l => $v) {
            $valData[] = ['label' => $l, 'value' => $v];
        }
        $charts[] = BarBuilder::build('estimated_value', 'القيمة التقديرية لكل شركة', $valData, true, 15);

        // 4) bar: مدة عدم الاستغلال بالأشهر
        $idleRows = $rows->filter(fn($r) => !empty($r->asset_name))
            ->map(fn($r) => [
                'label' => trim((string) $r->asset_name),
                'value' => ValueNormalizer::number($r->idle_months ?? 0) ?? 0,
            ])->all();
        $charts[] = BarBuilder::build('idle_months', 'مدة عدم الاستغلال (شهور)', $idleRows, true, 10);

        return [
            'module' => ['code' => $moduleCode, 'name_ar' => $module['name_ar']],
            'kpis'   => $kpis,
            'charts' => $charts,
            'map'    => ['points' => []],
        ];
    }
}