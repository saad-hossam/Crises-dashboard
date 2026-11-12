<?php
// app/Services/Analytics/Modules/MachinesAnalytics.php

namespace App\Services\Analytics\Modules;

use Illuminate\Support\Facades\DB;
use App\Services\Analytics\ValueNormalizer;
use App\Services\Analytics\ChartBuilders\BarBuilder;
use App\Services\Analytics\ChartBuilders\KpiBuilder;
use App\Services\Analytics\ChartBuilders\DoughnutBuilder;

class MachinesAnalytics
{
    public function build(string $moduleCode, array $module): array
    {
        $rows = DB::table('machines')->get();

        $kpis = [
            KpiBuilder::card('total', 'إجمالي الماكينات', $rows->count()),
            KpiBuilder::card('workshops', 'عدد الورش', $rows->pluck('workshop_name')->filter()->unique()->count()),
            KpiBuilder::card('types', 'عدد الأنواع', $rows->pluck('machine_type')->filter()->unique()->count()),
            KpiBuilder::card('owners', 'عدد الجهات المالكة', $rows->pluck('owner_organization')->filter()->unique()->count()),
        ];

        // 1) bar: الماكينات لكل جهة مالكة
        $ownerRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->owner_organization ?? '')),
            'value' => 1,
        ])->all();
        $charts[] = BarBuilder::build('owner_organization', 'الماكينات لكل جهة مالكة', $ownerRows, true, 15);

        // 2) bar: أعلى 10 ورش
        $wsRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->workshop_name ?? '')),
            'value' => 1,
        ])->all();
        $charts[] = BarBuilder::build('workshop_name', 'أعلى 10 ورش', $wsRows, true, 10);

        // 3) doughnut: الصلاحية
        $condRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->condition_status ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('condition_status', 'حالة الصلاحية', $condRows);

        // 4) bar: أعلى 10 أنواع ماكينات
        $typeRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->machine_type ?? '')),
            'value' => 1,
        ])->all();
        $charts[] = BarBuilder::build('machine_type', 'أعلى 10 أنواع ماكينات', $typeRows, true, 10);

        // 5) bar: أعلى 10 بلاد منشأ
        $originRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->origin_country ?? '')),
            'value' => 1,
        ])->all();
        $charts[] = BarBuilder::build('origin_country', 'أعلى 10 بلاد منشأ', $originRows, false, 10);

        return [
            'module' => ['code' => $moduleCode, 'name_ar' => $module['name_ar']],
            'kpis'   => $kpis,
            'charts' => $charts,
            'map'    => ['points' => []],
        ];
    }
}