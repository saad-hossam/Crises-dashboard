<?php
// app/Services/Analytics/Modules/MutualDependencyAnalytics.php

namespace App\Services\Analytics\Modules;

use Illuminate\Support\Facades\DB;
use App\Services\Analytics\ChartBuilders\BarBuilder;
use App\Services\Analytics\ChartBuilders\KpiBuilder;
use App\Services\Analytics\ChartBuilders\DoughnutBuilder;

class MutualDependencyAnalytics
{
    public function build(string $moduleCode, array $module): array
    {
        $rows = DB::table('mutual_dependencies')->get();

        $kpis = [
            KpiBuilder::card('total', 'إجمالي السجلات', $rows->count()),
            KpiBuilder::card('types', 'عدد أنواع الاعتماد', $rows->pluck('dependence_type')->filter()->unique()->count()),
            KpiBuilder::card('counterparts', 'عدد الجهات المقابلة', $rows->pluck('counterpart')->filter()->unique()->count()),
            KpiBuilder::card('units', 'عدد الوحدات', $rows->pluck('unit_name')->filter()->unique()->count()),
        ];

        // 1) doughnut: درجة الاعتماد
        $degRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->dependence_degree ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('dependence_degree', 'درجة الاعتماد', $degRows);

        // 2) doughnut: وجود بديل
        $altRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->has_alternative ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('has_alternative', 'وجود بديل', $altRows);

        // 3) bar: الاعتماد لكل وحدة
        $unitRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->unit_name ?? '')),
            'value' => 1,
        ])->all();
        $charts[] = BarBuilder::build('unit_name', 'الاعتماد لكل وحدة', $unitRows, true, 15);

        // 4) bar: أعلى 10 جهات مقابلة
        $cpRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->counterpart ?? '')),
            'value' => 1,
        ])->all();
        $charts[] = BarBuilder::build('counterpart', 'أعلى 10 جهات مقابلة', $cpRows, false, 10);

        return [
            'module' => ['code' => $moduleCode, 'name_ar' => $module['name_ar']],
            'kpis'   => $kpis,
            'charts' => $charts,
            'map'    => ['points' => []],
        ];
    }
}