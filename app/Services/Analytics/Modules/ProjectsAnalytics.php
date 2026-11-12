<?php
// app/Services/Analytics/Modules/ProjectsAnalytics.php

namespace App\Services\Analytics\Modules;

use Illuminate\Support\Facades\DB;
use App\Services\Analytics\ValueNormalizer;
use App\Services\Analytics\ChartBuilders\BarBuilder;
use App\Services\Analytics\ChartBuilders\KpiBuilder;
use App\Services\Analytics\ChartBuilders\DoughnutBuilder;

class ProjectsAnalytics
{
    public function build(string $moduleCode, array $module): array
    {
        $rows = DB::table('projects')->get();

        $totalCost = $rows->sum(fn($r) => ValueNormalizer::number($r->estimated_cost ?? 0) ?? 0);
        $avgProgress = $rows->count() > 0
            ? round($rows->avg(fn($r) => ValueNormalizer::number($r->progress_percent ?? 0) ?? 0), 1)
            : 0;

        $kpis = [
            KpiBuilder::card('total', 'إجمالي المشروعات', $rows->count()),
            KpiBuilder::card('cost', 'إجمالي التكلفة التقديرية', (int) $totalCost),
            KpiBuilder::card('avg_progress', 'متوسط نسبة الإنجاز', $avgProgress, '%'),
            KpiBuilder::card('companies', 'عدد الجهات', $rows->pluck('company_name')->filter()->unique()->count()),
        ];

        // 1) doughnut: حالة المشروع
        $statusRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->project_status ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('project_status', 'حالة المشروع', $statusRows);

        // 2) bar: نوع المشروع
        $typeRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->project_type ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = BarBuilder::build('project_type', 'نوع المشروع', $typeRows);

        // 3) doughnut: مصدر التمويل
        $fundRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->funding_source ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('funding_source', 'مصدر التمويل', $fundRows);

        // 4) bar أفقي: نسبة الإنجاز لكل مشروع
        $progressRows = $rows->filter(fn($r) => !empty($r->project_name))
            ->map(fn($r) => [
                'label' => trim((string) $r->project_name),
                'value' => ValueNormalizer::number($r->progress_percent ?? 0) ?? 0,
            ])->all();
        $charts[] = BarBuilder::build('progress_percent', 'نسبة الإنجاز لكل مشروع', $progressRows, true, 20);

        return [
            'module' => ['code' => $moduleCode, 'name_ar' => $module['name_ar']],
            'kpis'   => $kpis,
            'charts' => $charts,
            'map'    => ['points' => []],
        ];
    }
}