<?php
// app/Services/Analytics/Modules/FireStationsAnalytics.php

namespace App\Services\Analytics\Modules;

use Illuminate\Support\Facades\DB;
use App\Services\Analytics\ValueNormalizer;
use App\Services\Analytics\ChartBuilders\BarBuilder;
use App\Services\Analytics\ChartBuilders\KpiBuilder;
use App\Services\Analytics\ChartBuilders\DoughnutBuilder;

class FireStationsAnalytics
{
    public function build(string $moduleCode, array $module): array
    {
        $rows = DB::table('fire_stations')->get();

        $sum = fn($field) => (int) $rows->sum(fn($r) => ValueNormalizer::number($r->$field ?? 0) ?? 0);

        $kpis = [
            KpiBuilder::card('total', 'إجمالي المحطات', $rows->count()),
            KpiBuilder::card('trucks', 'إجمالي سيارات الإطفاء', $sum('heavy_fire_trucks') + $sum('light_fire_trucks') + $sum('medium_fire_trucks')),
            KpiBuilder::card('extinguishers', 'أجهزة الإطفاء اليدوي', $sum('handheld_extinguishers')),
            KpiBuilder::card('civilians', 'عدد المدنيين', $sum('civilians_count')),
        ];

        // 1) bar: أنواع المحطات
        $typeRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->station_type ?? '')),
            'value' => 1,
        ])->all();
        $charts[] = BarBuilder::build('station_type', 'أنواع المحطات', $typeRows);

        // 2) doughnut: نوع الملكية
        $ownRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->ownership_type ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('ownership_type', 'نوع الملكية', $ownRows);

        return [
            'module' => ['code' => $moduleCode, 'name_ar' => $module['name_ar']],
            'kpis'   => $kpis,
            'charts' => $charts,
            'map'    => ['points' => []],
        ];
    }
}