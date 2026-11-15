<?php
// app/Services/Analytics/Modules/HumanResourcesAnalytics.php

namespace App\Services\Analytics\Modules;

use Illuminate\Support\Facades\DB;
use App\Services\Analytics\ValueNormalizer;
use App\Services\Analytics\ChartBuilders\BarBuilder;
use App\Services\Analytics\ChartBuilders\KpiBuilder;
use App\Services\Analytics\ChartBuilders\DoughnutBuilder;

class HumanResourcesAnalytics
{
    public function build(string $moduleCode, array $module): array
    {
        $rows = DB::table('human_resources')->get();

        // KPIs
        $totalEmployees = $rows->sum(fn($r) => ValueNormalizer::number($r->employees_count ?? 0) ?? 0);
        $emergencyAvail = $rows->sum(fn($r) => ValueNormalizer::number($r->emergency_available_count ?? 0) ?? 0);

        $kpis = [
            KpiBuilder::card('total_records', 'إجمالي السجلات', $rows->count()),
            KpiBuilder::card('employees', 'إجمالي العاملين', (int) $totalEmployees),
            KpiBuilder::card('emergency', 'متاحون للطوارئ', (int) $emergencyAvail),
            KpiBuilder::card('units', 'عدد الوحدات', $rows->pluck('unit_name')->filter()->unique()->count()),
        ];

        // 1) bar: مجموع العاملين لكل شركة (أعلى 15)
        $companyRows = [];
        foreach ($rows as $r) {
            $label = trim((string) ($r->company_name ?? ''));
            if ($label === '') continue;
            $val = ValueNormalizer::number($r->employees_count ?? 0) ?? 0;
            $companyRows[$label] = ($companyRows[$label] ?? 0) + $val;
        }
        $companyData = [];
        foreach ($companyRows as $l => $v) {
            $companyData[] = ['label' => $l, 'value' => $v];
        }
        $charts[] = BarBuilder::build('company_name', 'العاملون لكل شركة (أعلى 15)', $companyData, true, 15);

        // 2) doughnut: مهارة حرجة (نعم/لا)
        $criticalRows = $rows->map(fn($r) => [
            'label' => empty($r->critical_skill) || trim((string)$r->critical_skill) === 'لا يوجد'
                ? 'لا توجد مهارة حرجة'
                : 'توجد مهارة حرجة',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('critical_skill', 'هل توجد مهارة حرجة؟', $criticalRows);

        // 3) doughnut: عدد الورديات
        $shiftRows = $rows->map(fn($r) => [
            'label' => (string) (ValueNormalizer::number($r->shifts_count ?? 0) ?? 'غير محدد'),
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('shifts_count', 'عدد الورديات', $shiftRows);

        // 4) bar: مجموع العاملين لكل وحدة
        $unitRows = [];
        foreach ($rows as $r) {
            $label = trim((string) ($r->unit_name ?? ''));
            if ($label === '') continue;
            $val = ValueNormalizer::number($r->employees_count ?? 0) ?? 0;
            $unitRows[$label] = ($unitRows[$label] ?? 0) + $val;
        }
        $unitData = [];
        foreach ($unitRows as $l => $v) {
            $unitData[] = ['label' => $l, 'value' => $v];
        }
        $charts[] = BarBuilder::build('unit_name', 'العاملون لكل وحدة', $unitData, true, 20);

        // 5) bar: أعلى 10 مسميات وظيفية
        $jobRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->job_title ?? '')),
            'value' => 1,
        ])->all();
        $charts[] = BarBuilder::build('job_title', 'أعلى 10 مسميات وظيفية', $jobRows, true, 10);

        return [
            'module' => ['code' => $moduleCode, 'name_ar' => $module['name_ar']],
            'kpis'   => $kpis,
            'charts' => $charts,
            'map'    => ['points' => []],
        ];
    }
}