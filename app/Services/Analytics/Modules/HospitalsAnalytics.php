<?php
// app/Services/Analytics/Modules/HospitalsAnalytics.php

namespace App\Services\Analytics\Modules;

use Illuminate\Support\Facades\DB;
use App\Services\Analytics\ValueNormalizer;
use App\Services\Analytics\ChartBuilders\BarBuilder;
use App\Services\Analytics\ChartBuilders\KpiBuilder;

class HospitalsAnalytics
{
    public function build(string $moduleCode, array $module): array
    {
        $rows = DB::table('hospitals')->get();
        $r = $rows->first();

        if (!$r) {
            return [
                'module' => ['code' => $moduleCode, 'name_ar' => $module['name_ar']],
                'kpis'   => [],
                'charts' => [],
                'map'    => ['points' => []],
            ];
        }

        $kpis = [
            KpiBuilder::card('total_beds', 'إجمالي الأسرة', (int) (ValueNormalizer::number($r->total_beds ?? 0) ?? 0)),
            KpiBuilder::card('icu', 'أسرة العناية المركزة', (int) (ValueNormalizer::number($r->icu_beds ?? 0) ?? 0)),
            KpiBuilder::card('doctors', 'عدد الأطباء', (int) (ValueNormalizer::number($r->doctors_count ?? 0) ?? 0)),
            KpiBuilder::card('nursing', 'عدد التمريض', (int) (ValueNormalizer::number($r->nursing_count ?? 0) ?? 0)),
        ];

        // 1) bar: الأسرة حسب النوع
        $bedRows = [
            ['label' => 'عناية مركزة', 'value' => ValueNormalizer::number($r->icu_beds ?? 0) ?? 0],
            ['label' => 'رعاية متوسطة', 'value' => ValueNormalizer::number($r->intermediate_care_beds ?? 0) ?? 0],
            ['label' => 'إفاقة', 'value' => ValueNormalizer::number($r->recovery_beds ?? 0) ?? 0],
            ['label' => 'طوارئ', 'value' => ValueNormalizer::number($r->emergency_beds ?? 0) ?? 0],
            ['label' => 'استقبال', 'value' => ValueNormalizer::number($r->reception_beds ?? 0) ?? 0],
            ['label' => 'عزل', 'value' => ValueNormalizer::number($r->isolation_beds ?? 0) ?? 0],
            ['label' => 'حروق', 'value' => ValueNormalizer::number($r->burn_care_beds ?? 0) ?? 0],
            ['label' => 'حضانات', 'value' => ValueNormalizer::number($r->neonatal_beds ?? 0) ?? 0],
        ];
        $charts[] = BarBuilder::build('beds_by_type', 'الأسرة حسب النوع', $bedRows);

        // 2) bar: الأطباء حسب التخصص (لو متاح) — نستخدم doctors_count كإجمالي
        $staffRows = [
            ['label' => 'أطباء', 'value' => ValueNormalizer::number($r->doctors_count ?? 0) ?? 0],
            ['label' => 'تمريض', 'value' => ValueNormalizer::number($r->nursing_count ?? 0) ?? 0],
        ];
        $charts[] = BarBuilder::build('staff', 'الفريق الطبي', $staffRows);

        $coord = ValueNormalizer::coordinate($r->x_coordinate ?? null, $r->y_coordinate ?? null);
        $points = $coord ? [[
            'lat'    => $coord['lat'],
            'lon'    => $coord['lon'],
            'label'  => $r->hospital_name ?? '',
            'status' => 'other',
        ]] : [];

        return [
            'module' => ['code' => $moduleCode, 'name_ar' => $module['name_ar']],
            'kpis'   => $kpis,
            'charts' => $charts,
            'map'    => ['points' => $points],
        ];
    }
}