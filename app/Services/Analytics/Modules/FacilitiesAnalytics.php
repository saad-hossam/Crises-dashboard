<?php
// app/Services/Analytics/Modules/FacilitiesAnalytics.php

namespace App\Services\Analytics\Modules;

use Illuminate\Support\Facades\DB;
use App\Services\Analytics\ValueNormalizer;
use App\Services\Analytics\ChartBuilders\BarBuilder;
use App\Services\Analytics\ChartBuilders\KpiBuilder;
use App\Services\Analytics\ChartBuilders\DoughnutBuilder;

class FacilitiesAnalytics
{
    public function build(string $moduleCode, array $module): array
    {
        $rows = DB::table('facilities_properties')->get();

        // KPIs
        $totalArea = $rows->sum(fn($r) => ValueNormalizer::areaToSqm($r->built_area ?? null) ?? 0);

        $kpis = [
            KpiBuilder::card('total', 'إجمالي المنشآت', $rows->count()),
            KpiBuilder::card('area', 'إجمالي المساحة المبنية', (int) $totalArea, 'م²'),
            KpiBuilder::card('companies', 'عدد الشركات', $rows->pluck('company_name')->filter()->unique()->count()),
            KpiBuilder::card('units', 'عدد الوحدات', $rows->pluck('unit_name')->filter()->unique()->count()),
        ];

        // 1) bar: نوع المنشأة أعلى 10
        $typeRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->facility_type ?? '')),
            'value' => 1,
        ])->all();
        $charts[] = BarBuilder::build('facility_type', 'أنواع المنشآت (أعلى 10)', $typeRows, false, 10);

        // 2) doughnut: الحالة الإنشائية
        $structRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->structural_condition ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('structural_condition', 'الحالة الإنشائية', $structRows);

        // 3) bar: مجموع المساحة المبنية لكل شركة (أعلى 15)
        $areaByCompany = [];
        foreach ($rows as $r) {
            $label = trim((string) ($r->company_name ?? ''));
            if ($label === '') continue;
            $area = ValueNormalizer::areaToSqm($r->built_area ?? null) ?? 0;
            $areaByCompany[$label] = ($areaByCompany[$label] ?? 0) + $area;
        }
        $areaData = [];
        foreach ($areaByCompany as $l => $v) {
            $areaData[] = ['label' => $l, 'value' => $v];
        }
        $charts[] = BarBuilder::build('built_area', 'المساحة المبنية لكل شركة (م²)', $areaData, true, 15);

        // 4) bar: سنة الإنشاء بالعقود
        $yearBuckets = [];
        foreach ($rows as $r) {
            $year = ValueNormalizer::number($r->construction_year ?? null);
            if ($year === null || $year < 1900 || $year > 2030) continue;
            $decade = (int) (floor($year / 10) * 10) . 's';
            $yearBuckets[$decade] = ($yearBuckets[$decade] ?? 0) + 1;
        }
        ksort($yearBuckets);
        $yearData = [];
        foreach ($yearBuckets as $l => $v) {
            $yearData[] = ['label' => $l, 'value' => $v];
        }
        $charts[] = BarBuilder::build('construction_year', 'سنة الإنشاء (بالعقود)', $yearData);

        // 5) doughnut: حالة التأمين
        $insRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->insurance_status ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('insurance_status', 'حالة التأمين', $insRows);

        // Map
        $points = $rows->map(function ($r) {
            $coord = ValueNormalizer::coordinate($r->x_coordinate ?? null, $r->y_coordinate ?? null);
            if (!$coord) return null;
            return [
                'lat'    => $coord['lat'],
                'lon'    => $coord['lon'],
                'label'  => ($r->facility_name ?? '') . ' - ' . ($r->company_name ?? ''),
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