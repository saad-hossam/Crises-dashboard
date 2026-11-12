<?php
// app/Services/Analytics/Modules/LandsAnalytics.php

namespace App\Services\Analytics\Modules;

use Illuminate\Support\Facades\DB;
use App\Services\Analytics\ValueNormalizer;
use App\Services\Analytics\ChartBuilders\BarBuilder;
use App\Services\Analytics\ChartBuilders\KpiBuilder;
use App\Services\Analytics\ChartBuilders\DoughnutBuilder;

class LandsAnalytics
{
    public function build(string $moduleCode, array $module): array
    {
        $rows = DB::table('lands')->get();

        $totalArea = $rows->sum(fn($r) => ValueNormalizer::areaToSqm($r->area ?? null) ?? 0);

        $kpis = [
            KpiBuilder::card('total', 'إجمالي قطع الأراضي', $rows->count()),
            KpiBuilder::card('area', 'إجمالي المساحة', (int) $totalArea, 'م²'),
            KpiBuilder::card('types', 'عدد الأنواع', $rows->pluck('ownership_type')->filter()->unique()->count()),
            KpiBuilder::card('governorates', 'عدد المحافظات', $rows->pluck('governorate')->filter()->unique()->count()),
        ];

        // 1) bar: مساحة كل قطعة
        $areaRows = $rows->filter(fn($r) => !empty($r->plot_number))
            ->map(fn($r) => [
                'label' => trim((string) $r->plot_number),
                'value' => ValueNormalizer::areaToSqm($r->area ?? null) ?? 0,
            ])->all();
        $charts[] = BarBuilder::build('area', 'مساحة كل قطعة (م²)', $areaRows);

        // 2) doughnut: حالة الأرض
        $statusRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->land_status ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('land_status', 'حالة الأرض', $statusRows);

        // 3) bar: الاستخدام الحالي
        $useRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->current_use ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = BarBuilder::build('current_use', 'الاستخدام الحالي', $useRows);

        // 4) doughnut: إمكانية إعادة الاستغلال
        $reuseRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->reutilization_possibility ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('reutilization_possibility', 'إمكانية إعادة الاستغلال', $reuseRows);

        return [
            'module' => ['code' => $moduleCode, 'name_ar' => $module['name_ar']],
            'kpis'   => $kpis,
            'charts' => $charts,
            'map'    => ['points' => []],
        ];
    }
}