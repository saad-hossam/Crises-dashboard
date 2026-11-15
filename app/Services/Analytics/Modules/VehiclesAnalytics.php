<?php
// app/Services/Analytics/Modules/VehiclesAnalytics.php

namespace App\Services\Analytics\Modules;

use Illuminate\Support\Facades\DB;
use App\Services\Analytics\ValueNormalizer;
use App\Services\Analytics\ChartBuilders\BarBuilder;
use App\Services\Analytics\ChartBuilders\KpiBuilder;
use App\Services\Analytics\ChartBuilders\DoughnutBuilder;

class VehiclesAnalytics
{
    public function build(string $moduleCode, array $module): array
    {
        $rows = DB::table('vehicles')->get();

        // 1) KPIs
        $kpis = [
            KpiBuilder::card('total', 'إجمالي المركبات', $rows->count()),
            KpiBuilder::card('operational', 'صالح', $rows->filter(fn($r) =>
                ValueNormalizer::status($r->vehicle_condition) === 'operational')->count()),
            KpiBuilder::card('broken', 'عاطل', $rows->filter(fn($r) =>
                ValueNormalizer::status($r->vehicle_condition) === 'broken')->count()),
            KpiBuilder::card('units', 'عدد الوحدات', $rows->pluck('unit_name')->filter()->unique()->count()),
        ];

        // 2) doughnut: حالة المركبة
        $statusRows = $rows->map(fn($r) => [
            'label' => ValueNormalizer::status($r->vehicle_condition),
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build(
            'vehicle_condition', 'حالة المركبة', $statusRows
        );

        // 3) bar: نوع المركبة
        $typeRows = $rows->map(fn($r) => [
            'label' => trim((string) $r->vehicle_type),
            'value' => 1,
        ])->all();
        $charts[] = BarBuilder::build(
            'vehicle_type', 'أنواع المركبات', $typeRows
        );

        // 4) doughnut: نوع الوقود
        $fuelRows = $rows->map(fn($r) => [
            'label' => trim((string) $r->fuel_type),
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build(
            'fuel_type', 'نوع الوقود', $fuelRows
        );

        // 5) bar: أعلى 10 ماركات
        $brandRows = $rows->map(fn($r) => [
            'label' => trim((string) $r->brand),
            'value' => 1,
        ])->all();
        $charts[] = BarBuilder::build(
            'brand', 'أعلى 10 ماركات', $brandRows, false, 10
        );

        // 6) bar: مركبات لكل وحدة
        $unitRows = $rows->map(fn($r) => [
            'label' => trim((string) $r->unit_name),
            'value' => 1,
        ])->all();
        $charts[] = BarBuilder::build(
            'unit_name', 'المركبات لكل وحدة', $unitRows, true, 15
        );

        // 7) Map: نقاط X/Y
        $points = $rows->map(function ($r) {
            $coord = ValueNormalizer::coordinate($r->x_coordinate, $r->y_coordinate);
            if (!$coord) return null;
            return [
                'lat'    => $coord['lat'],
                'lon'    => $coord['lon'],
                'label'  => $r->plate_number . ' - ' . $r->brand,
                'status' => ValueNormalizer::status($r->vehicle_condition),
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