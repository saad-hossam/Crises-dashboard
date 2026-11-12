<?php
// app/Services/Analytics/Modules/EquipmentAnalytics.php

namespace App\Services\Analytics\Modules;

use Illuminate\Support\Facades\DB;
use App\Services\Analytics\ValueNormalizer;
use App\Services\Analytics\ChartBuilders\BarBuilder;
use App\Services\Analytics\ChartBuilders\KpiBuilder;
use App\Services\Analytics\ChartBuilders\DoughnutBuilder;

class EquipmentAnalytics
{
    public function build(string $moduleCode, array $module): array
    {
        $rows = DB::table('equipment')->get();

        // KPIs
        $kpis = [
            KpiBuilder::card('total', 'إجمالي المعدات', $rows->count()),
            KpiBuilder::card('operational', 'صالحة للعمل', $rows->filter(fn($r) =>
                ValueNormalizer::status($r->condition_status ?? '') === 'operational')->count()),
            KpiBuilder::card('broken', 'عاطلة', $rows->filter(fn($r) =>
                ValueNormalizer::status($r->condition_status ?? '') === 'broken')->count()),
            KpiBuilder::card('units', 'عدد الوحدات', $rows->pluck('unit_name')->filter()->unique()->count()),
        ];

        // 1) doughnut: الحالة
        $statusRows = $rows->map(fn($r) => [
            'label' => ValueNormalizer::status($r->condition_status ?? ''),
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('condition_status', 'حالة المعدة', $statusRows);

        // 2) bar: اسم المعدة أعلى 10
        $nameRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->equipment_name ?? '')),
            'value' => 1,
        ])->all();
        $charts[] = BarBuilder::build('equipment_name', 'أعلى 10 أنواع معدات', $nameRows, false, 10);

        // 3) bar: طبيعة الاستخدام (بدون "لا يوجد")
        $usageRows = $rows->filter(fn($r) =>
            !empty($r->usage_nature) &&
            !in_array(trim((string)$r->usage_nature), ['لا يوجد', 'لايوجد', '-', 'لا'])
        )->map(fn($r) => [
            'label' => trim((string) $r->usage_nature),
            'value' => 1,
        ])->all();
        if (count($usageRows) > 0) {
            $charts[] = BarBuilder::build('usage_nature', 'طبيعة الاستخدام (أعلى 10)', $usageRows, true, 10);
        }

        // 4) bar: عدد المعدات لكل وحدة
        $unitRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->unit_name ?? '')),
            'value' => 1,
        ])->all();
        $charts[] = BarBuilder::build('unit_name', 'عدد المعدات لكل وحدة', $unitRows, true, 15);

        // 5) bar: أعلى 10 ماركات
        $modelRows = $rows->filter(fn($r) => !empty($r->model))
            ->map(fn($r) => ['label' => trim((string) $r->model), 'value' => 1])->all();
        if (count($modelRows) > 0) {
            $charts[] = BarBuilder::build('model', 'أعلى 10 طرازات', $modelRows, false, 10);
        }

        // Map
        $points = $rows->map(function ($r) {
            $coord = ValueNormalizer::coordinate($r->x_coordinate ?? null, $r->y_coordinate ?? null);
            if (!$coord) return null;
            return [
                'lat'    => $coord['lat'],
                'lon'    => $coord['lon'],
                'label'  => ($r->equipment_name ?? '') . ' - ' . ($r->model ?? ''),
                'status' => ValueNormalizer::status($r->condition_status ?? ''),
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