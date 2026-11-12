<?php
// app/Services/Analytics/Modules/CommunicationsAnalytics.php

namespace App\Services\Analytics\Modules;

use Illuminate\Support\Facades\DB;
use App\Services\Analytics\ChartBuilders\BarBuilder;
use App\Services\Analytics\ChartBuilders\KpiBuilder;
use App\Services\Analytics\ChartBuilders\DoughnutBuilder;

class CommunicationsAnalytics
{
    public function build(string $moduleCode, array $module): array
    {
        $rows = DB::table('communications')->get();

        $kpis = [
            KpiBuilder::card('total', 'إجمالي السجلات', $rows->count()),
            KpiBuilder::card('types', 'عدد الأنواع', $rows->pluck('communication_type')->filter()->unique()->count()),
            KpiBuilder::card('providers', 'عدد المزودين', $rows->pluck('service_provider')->filter()->unique()->count()),
            KpiBuilder::card('units', 'عدد الوحدات', $rows->pluck('unit_name')->filter()->unique()->count()),
        ];

        // 1) bar: نوع وسيلة الاتصال أعلى 10
        $typeRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->communication_type ?? '')),
            'value' => 1,
        ])->all();
        $charts[] = BarBuilder::build('communication_type', 'نوع وسيلة الاتصال (أعلى 10)', $typeRows, true, 10);

        // 2) doughnut: وجود بديل
        $altRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->has_alternative ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('has_alternative', 'وجود بديل', $altRows);

        // 3) doughnut: حالة التشغيل
        $statusRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->operating_status ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('operating_status', 'حالة التشغيل', $statusRows);

        // 4) bar: الوسائل لكل وحدة
        $unitRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->unit_name ?? '')),
            'value' => 1,
        ])->all();
        $charts[] = BarBuilder::build('unit_name', 'الوسائل لكل وحدة', $unitRows, true, 15);

        return [
            'module' => ['code' => $moduleCode, 'name_ar' => $module['name_ar']],
            'kpis'   => $kpis,
            'charts' => $charts,
            'map'    => ['points' => []],
        ];
    }
}