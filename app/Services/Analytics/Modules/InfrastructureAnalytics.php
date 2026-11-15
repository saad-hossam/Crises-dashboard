<?php
// app/Services/Analytics/Modules/InfrastructureAnalytics.php

namespace App\Services\Analytics\Modules;

use Illuminate\Support\Facades\DB;
use App\Services\Analytics\ChartBuilders\BarBuilder;
use App\Services\Analytics\ChartBuilders\KpiBuilder;
use App\Services\Analytics\ChartBuilders\DoughnutBuilder;

class InfrastructureAnalytics
{
    public function build(string $moduleCode, array $module): array
    {
        $rows = DB::table('infrastructures')->get();

        $kpis = [
            KpiBuilder::card('total', 'إجمالي السجلات', $rows->count()),
            KpiBuilder::card('types', 'عدد الأنواع', $rows->pluck('infrastructure_type')->filter()->unique()->count()),
            KpiBuilder::card('companies', 'عدد الشركات', $rows->pluck('company_name')->filter()->unique()->count()),
            KpiBuilder::card('units', 'عدد الوحدات', $rows->pluck('unit_name')->filter()->unique()->count()),
        ];

        // 1) bar: نوع البنية أعلى 10
        $typeRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->infrastructure_type ?? '')),
            'value' => 1,
        ])->all();
        $charts[] = BarBuilder::build('infrastructure_type', 'أنواع البنية التحتية (أعلى 10)', $typeRows, true, 10);

        // 2) doughnut: حالة التشغيل
        $statusRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->operating_status ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('operating_status', 'حالة التشغيل', $statusRows);

        // 3) bar: المرافق لكل وحدة
        $unitRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->unit_name ?? '')),
            'value' => 1,
        ])->all();
        $charts[] = BarBuilder::build('unit_name', 'المرافق لكل وحدة', $unitRows, true, 15);

        // 4) doughnut: الجاهزية
        $readyRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->readiness_level ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('readiness_level', 'مستوى الجاهزية', $readyRows);

        return [
            'module' => ['code' => $moduleCode, 'name_ar' => $module['name_ar']],
            'kpis'   => $kpis,
            'charts' => $charts,
            'map'    => ['points' => []],
        ];
    }
}