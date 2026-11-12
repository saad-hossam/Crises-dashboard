<?php
// app/Services/Analytics/Modules/ProductionLinesAnalytics.php

namespace App\Services\Analytics\Modules;

use Illuminate\Support\Facades\DB;
use App\Services\Analytics\ValueNormalizer;
use App\Services\Analytics\ChartBuilders\BarBuilder;
use App\Services\Analytics\ChartBuilders\KpiBuilder;
use App\Services\Analytics\ChartBuilders\DoughnutBuilder;

class ProductionLinesAnalytics
{
    public function build(string $moduleCode, array $module): array
    {
        $rows = DB::table('production_lines')->get();

        $kpis = [
            KpiBuilder::card('total', 'إجمالي خطوط الإنتاج', $rows->count()),
            KpiBuilder::card('companies', 'عدد الجهات', $rows->pluck('company_name')->filter()->unique()->count()),
            KpiBuilder::card('products', 'عدد المنتجات', $rows->pluck('product_name')->filter()->unique()->count()),
            KpiBuilder::card('units', 'عدد الوحدات', $rows->pluck('unit_name')->filter()->unique()->count()),
        ];

        $charts = [];

        // 1) doughnut: حالة الخط
        $statusRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->line_status ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('line_status', 'حالة الخط', $statusRows);

        // 2) bar: خطوط لكل وحدة  ← مع meta للـ tooltip
        $unitRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->unit_name ?? '')) ?: 'غير محدد',
            'value' => 1,
            'meta'  => [
                'line_name'    => $r->name ?? '',
                'company_name' => $r->company_name ?? '',
                'product_name' => $r->product_name ?? '',
                'status'       => $r->line_status ?? '',
                'readiness'    => $r->readiness_level ?? '',
            ],
        ])->all();

        $charts[] = BarBuilder::build(
            'unit_name',
            'خطوط الإنتاج لكل وحدة',
            $unitRows,
            true,   // aggregate by label
            15,
            ['showMetaInTooltip' => true]
        );

        // 3) doughnut: مستوى الجاهزية
        $readyRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->readiness_level ?? '')) ?: 'غير محدد',
            'value' => 1,
        ])->all();
        $charts[] = DoughnutBuilder::build('readiness_level', 'مستوى الجاهزية', $readyRows);

        // 4) doughnut: إمكانية زيادة الإنتاج
        $incRows = $rows->filter(fn($r) => !empty($r->production_increase_possibility))
            ->map(fn($r) => [
                'label' => trim((string) $r->production_increase_possibility),
                'value' => 1,
            ])->all();
        if (count($incRows) > 0) {
            $charts[] = DoughnutBuilder::build('production_increase_possibility', 'إمكانية زيادة الإنتاج', $incRows);
        }

        // 5) bar: أعلى 10 شركات  ← مع meta للـ tooltip
        $companyRows = $rows->map(fn($r) => [
            'label' => trim((string) ($r->company_name ?? '')) ?: 'غير محدد',
            'value' => 1,
            'meta'  => [
                'line_name'    => $r->name ?? '',
                'unit_name'    => $r->unit_name ?? '',
                'product_name' => $r->product_name ?? '',
                'status'       => $r->line_status ?? '',
            ],
        ])->all();

        $charts[] = BarBuilder::build(
            'company_name',
            'أعلى 10 جهات',
            $companyRows,
            true,
            10,
            ['showMetaInTooltip' => true]
        );

        // Map
        $points = $rows->map(function ($r) {
            $coord = ValueNormalizer::coordinate($r->x_coordinate ?? null, $r->y_coordinate ?? null);
            if (!$coord) return null;
            return [
                'lat'    => $coord['lat'],
                'lon'    => $coord['lon'],
                'label'  => ($r->name ?? '') . ' - ' . ($r->company_name ?? ''),
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