<?php
// app/Services/Analytics/Modules/HotelsAnalytics.php

namespace App\Services\Analytics\Modules;

use Illuminate\Support\Facades\DB;
use App\Services\Analytics\ValueNormalizer;
use App\Services\Analytics\ChartBuilders\KpiBuilder;

class HotelsAnalytics
{
    public function build(string $moduleCode, array $module): array
    {
        $rows = DB::table('hotels')->get();
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
            KpiBuilder::card('rooms', 'عدد الغرف', (int) (ValueNormalizer::number($r->rooms_count ?? 0) ?? 0)),
            KpiBuilder::card('beds', 'عدد الأسرة', (int) (ValueNormalizer::number($r->beds_count ?? 0) ?? 0)),
            KpiBuilder::card('emergency', 'سعة الطوارئ', (int) (ValueNormalizer::number($r->emergency_capacity ?? 0) ?? 0)),
            KpiBuilder::card('license', 'حالة الترخيص', trim((string) ($r->license_status ?? '')) ?: 'غير محدد'),
        ];

        return [
            'module' => ['code' => $moduleCode, 'name_ar' => $module['name_ar']],
            'kpis'   => $kpis,
            'charts' => [],
            'map'    => ['points' => []],
        ];
    }
}