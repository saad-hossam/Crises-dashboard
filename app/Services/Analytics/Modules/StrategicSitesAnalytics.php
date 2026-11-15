<?php
// app/Services/Analytics/Modules/StrategicSitesAnalytics.php

namespace App\Services\Analytics\Modules;

use Illuminate\Support\Facades\DB;
use App\Services\Analytics\ValueNormalizer;
use App\Services\Analytics\ChartBuilders\KpiBuilder;

class StrategicSitesAnalytics
{
    public function build(string $moduleCode, array $module): array
    {
        $rows = DB::table('strategic_sites')->get();

        $kpis = [
            KpiBuilder::card('total', 'إجمالي المواقع', $rows->count()),
            KpiBuilder::card('entrances', 'إجمالي المداخل', (int) $rows->sum(fn($r) => ValueNormalizer::number($r->entrances_count ?? 0) ?? 0)),
            KpiBuilder::card('exits', 'إجمالي المخارج', (int) $rows->sum(fn($r) => ValueNormalizer::number($r->exits_count ?? 0) ?? 0)),
            KpiBuilder::card('governorates', 'عدد المحافظات', $rows->pluck('governorate')->filter()->unique()->count()),
        ];

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
            'charts' => [],
            'map'    => ['points' => $points],
        ];
    }
}