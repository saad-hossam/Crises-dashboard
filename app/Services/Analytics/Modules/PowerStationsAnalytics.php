<?php
// app/Services/Analytics/Modules/PowerStationsAnalytics.php

namespace App\Services\Analytics\Modules;

use Illuminate\Support\Facades\DB;
use App\Services\Analytics\ValueNormalizer;
use App\Services\Analytics\ChartBuilders\BarBuilder;
use App\Services\Analytics\ChartBuilders\KpiBuilder;
use App\Services\Analytics\ChartBuilders\DoughnutBuilder;

class PowerStationsAnalytics
{
    public function build(string $moduleCode, array $module): array
    {
        $rows = DB::table('power_stations')->get();

        $totalCapacity = $rows->sum(fn($r) => ValueNormalizer::number($r->capacity_mva ?? 0) ?? 0);

        $kpis = [
            KpiBuilder::card('total', 'إجمالي المحطات', $rows->count()),
            KpiBuilder::card('capacity', 'إجمالي القدرة', round($totalCapacity, 2), 'MVA'),
            KpiBuilder::card('governorates', 'عدد المحافظات', $rows->pluck('governorate')->filter()->unique()->count()),
            KpiBuilder::card('operators', 'عدد الجهات المشغلة', $rows->pluck('operator_entity')->filter()->unique()->count()),
        ];

        // 1) bar: القدرة لكل محطة
        $capRows = $rows->filter(fn($r) => !empty($r->station_name))
            ->map(fn($r) => [
                'label' => trim((string) $r->station_name),
                'value' => ValueNormalizer::number($r->capacity_mva ?? 0) ?? 0,
            ])->all();
        $charts[] = BarBuilder::build('capacity_mva', 'القدرة (MVA) لكل محطة', $capRows, true, 20);

        // 2) doughnut: نوع التشغيل
        $opRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->operation_type ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('operation_type', 'نوع التشغيل', $opRows);

        // 3) bar: الجهة المشغلة
        $opEntityRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->operator_entity ?? '')),
            'value' => 1,
        ])->all();
        $charts[] = BarBuilder::build('operator_entity', 'الجهة المشغلة', $opEntityRows);

        // 4) doughnut: مصدر الطاقة
        $srcRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->energy_source ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('energy_source', 'مصدر الطاقة', $srcRows);

        // Map
        $points = $rows->map(function ($r) {
            $coord = ValueNormalizer::coordinate($r->x_coordinate ?? null, $r->y_coordinate ?? null);
            if (!$coord) return null;
            return [
                'lat'    => $coord['lat'],
                'lon'    => $coord['lon'],
                'label'  => $r->station_name ?? '',
                'status' => 'other',
            ];
        })->filter()->values()->all();

        return [
            'module' => ['code' => $moduleCode, 'name_ar' => $module['name_ar']],
            'kpis'   => $kpis,
            'charts' => $charts,
            'map'    => ['points' => $points],
        ];
    }
}