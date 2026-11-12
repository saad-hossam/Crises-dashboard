<?php
// app/Services/Analytics/Modules/SitesAnalytics.php

namespace App\Services\Analytics\Modules;

use Illuminate\Support\Facades\DB;
use App\Services\Analytics\ValueNormalizer;
use App\Services\Analytics\ChartBuilders\BarBuilder;
use App\Services\Analytics\ChartBuilders\KpiBuilder;
use App\Services\Analytics\ChartBuilders\DoughnutBuilder;

class SitesAnalytics
{
    public function build(string $moduleCode, array $module): array
    {
        $rows = DB::table('sites')->get();

        $totalEmployees = $rows->sum(fn($r) => ValueNormalizer::number($r->employees_count ?? 0) ?? 0);

        $kpis = [
            KpiBuilder::card('total', 'إجمالي المواقع', $rows->count()),
            KpiBuilder::card('employees', 'إجمالي العاملين', (int) $totalEmployees),
            KpiBuilder::card('types', 'عدد الأنواع', $rows->pluck('site_type')->filter()->unique()->count()),
            KpiBuilder::card('governorates', 'عدد المحافظات', $rows->pluck('governorate')->filter()->unique()->count()),
        ];

        // 1) doughnut: نوع الموقع
        $typeRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->site_type ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('site_type', 'نوع الموقع', $typeRows);

        // 2) bar: العاملين لكل موقع
        $empRows = $rows->filter(fn($r) => !empty($r->site_name))
            ->map(fn($r) => [
                'label' => trim((string) $r->site_name),
                'value' => ValueNormalizer::number($r->employees_count ?? 0) ?? 0,
            ])->all();
        $charts[] = BarBuilder::build('employees_count', 'العاملون لكل موقع', $empRows, true, 20);

        // 3) bar: الشركة المشغلة
        $operatorRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->operator_company ?? '')),
            'value' => 1,
        ])->all();
        $charts[] = BarBuilder::build('operator_company', 'الشركة المشغلة', $operatorRows, true, 15);

        // 4) doughnut: الجاهزية
        $readyRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->readiness_level ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('readiness_level', 'مستوى الجاهزية', $readyRows);

        // Map
        $points = $rows->map(function ($r) {
            $coord = ValueNormalizer::coordinate($r->x_coordinate ?? null, $r->y_coordinate ?? null);
            if (!$coord) return null;
            return [
                'lat'    => $coord['lat'],
                'lon'    => $coord['lon'],
                'label'  => $r->site_name ?? '',
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