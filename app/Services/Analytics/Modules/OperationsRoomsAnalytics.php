<?php
// app/Services/Analytics/Modules/OperationsRoomsAnalytics.php

namespace App\Services\Analytics\Modules;

use Illuminate\Support\Facades\DB;
use App\Services\Analytics\ChartBuilders\BarBuilder;
use App\Services\Analytics\ChartBuilders\KpiBuilder;
use App\Services\Analytics\ChartBuilders\DoughnutBuilder;

class OperationsRoomsAnalytics
{
    public function build(string $moduleCode, array $module): array
    {
        $rows = DB::table('operations_rooms')->get();

        $kpis = [
            KpiBuilder::card('total', 'إجمالي الغرف', $rows->count()),
            KpiBuilder::card('types', 'عدد الأنواع', $rows->pluck('room_type')->filter()->unique()->count()),
            KpiBuilder::card('units', 'عدد الوحدات', $rows->pluck('unit_name')->filter()->unique()->count()),
            KpiBuilder::card('companies', 'عدد الجهات', $rows->pluck('company_name')->filter()->unique()->count()),
        ];

        // 1) doughnut: نوع الغرفة
        $typeRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->room_type ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('room_type', 'نوع الغرفة', $typeRows);

        // 2) doughnut: وجود طاقة احتياطي
        $powerRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->backup_power ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('backup_power', 'وجود طاقة احتياطي', $powerRows);

        // 3) doughnut: وجود ربط مركزي
        $linkRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->central_link ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('central_link', 'وجود ربط مركزي', $linkRows);

        // 4) bar: غرف لكل وحدة
        $unitRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->unit_name ?? '')),
            'value' => 1,
        ])->all();
        $charts[] = BarBuilder::build('unit_name', 'غرف العمليات لكل وحدة', $unitRows, true, 15);

        // 5) doughnut: حالة التشغيل
        $statusRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->operating_status ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('operating_status', 'حالة التشغيل', $statusRows);

        return [
            'module' => ['code' => $moduleCode, 'name_ar' => $module['name_ar']],
            'kpis'   => $kpis,
            'charts' => $charts,
            'map'    => ['points' => []],
        ];
    }
}