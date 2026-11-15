<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ModuleImportBatch;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $registry = config('excel_modules', []);

        $moduleStats = [];
        $totalRecords = 0;
        $activeModules = 0;
        $chartLabels = [];
        $chartCounts = [];

        $govMap = [];
        $statusMap = [];
        $readinessMap = [];

        foreach ($registry as $code => $cfg) {
            $model = $cfg['model'] ?? null;
            if (!$model || !class_exists($model)) {
                continue;
            }

            try {
                $count = $model::count();
            } catch (\Throwable $e) {
                $count = 0;
            }

            $totalRecords += $count;
            if ($count > 0) {
                $activeModules++;
            }

            $statusBreakdown = ['labels' => [], 'data' => []];
            $govBreakdown = ['labels' => [], 'data' => []];
            $readinessBreakdown = ['labels' => [], 'data' => []];
            $withCoords = 0;

            try {
                $table = (new $model)->getTable();

                if ($count > 0 && Schema::hasColumn($table, 'operating_status')) {
                    $rows = $model::query()
                        ->selectRaw('operating_status as k, count(*) as c')
                        ->whereNotNull('operating_status')
                        ->where('operating_status', '!=', '')
                        ->groupBy('operating_status')
                        ->orderByDesc('c')
                        ->limit(6)
                        ->pluck('c', 'k');
                    $statusBreakdown = [
                        'labels' => array_values($rows->keys()->all()),
                        'data'   => array_map('intval', array_values($rows->all())),
                    ];
                    foreach ($rows as $k => $c) {
                        $statusMap[$k] = ($statusMap[$k] ?? 0) + (int) $c;
                    }
                }

                if ($count > 0 && Schema::hasColumn($table, 'governorate')) {
                    $rows = $model::query()
                        ->selectRaw('governorate as k, count(*) as c')
                        ->whereNotNull('governorate')
                        ->where('governorate', '!=', '')
                        ->groupBy('governorate')
                        ->orderByDesc('c')
                        ->limit(6)
                        ->pluck('c', 'k');
                    $govBreakdown = [
                        'labels' => array_values($rows->keys()->all()),
                        'data'   => array_map('intval', array_values($rows->all())),
                    ];
                    foreach ($rows as $k => $c) {
                        $govMap[$k] = ($govMap[$k] ?? 0) + (int) $c;
                    }
                }

                if ($count > 0 && Schema::hasColumn($table, 'readiness_level')) {
                    $rows = $model::query()
                        ->selectRaw('readiness_level as k, count(*) as c')
                        ->whereNotNull('readiness_level')
                        ->where('readiness_level', '!=', '')
                        ->groupBy('readiness_level')
                        ->orderByDesc('c')
                        ->limit(6)
                        ->pluck('c', 'k');
                    $readinessBreakdown = [
                        'labels' => array_values($rows->keys()->all()),
                        'data'   => array_map('intval', array_values($rows->all())),
                    ];
                    foreach ($rows as $k => $c) {
                        $readinessMap[$k] = ($readinessMap[$k] ?? 0) + (int) $c;
                    }
                }

                if ($count > 0 && Schema::hasColumn($table, 'x_coordinate') && Schema::hasColumn($table, 'y_coordinate')) {
                    $withCoords = $model::query()
                        ->whereNotNull('x_coordinate')
                        ->whereNotNull('y_coordinate')
                        ->count();
                }
            } catch (\Throwable $e) {
                // table may not exist yet
            }

            $moduleStats[] = [
                'code'       => $code,
                'name'       => $cfg['name_ar'] ?? $code,
                'table'      => $cfg['table'] ?? '',
                'count'      => $count,
                'icon'       => $this->moduleIcon($code),
                'color'      => $this->moduleColor($code),
                'status'     => $statusBreakdown,
                'governorate'=> $govBreakdown,
                'readiness'  => $readinessBreakdown,
                'with_coords'=> $withCoords,
                'coverage'   => $count > 0 ? round(($withCoords / max($count, 1)) * 100) : 0,
            ];

            $chartLabels[] = $cfg['name_ar'] ?? $code;
            $chartCounts[] = $count;
        }

        arsort($govMap);
        $govMap = array_slice($govMap, 0, 8, true);
        arsort($statusMap);
        arsort($readinessMap);

        $stats = [
            [
                'label' => __('dashboard.total_records'),
                'value' => $totalRecords,
                'icon'  => 'bi-database',
                'color' => 'primary',
            ],
            [
                'label' => __('dashboard.modules_count'),
                'value' => $activeModules,
                'icon'  => 'bi-grid-3x3-gap',
                'color' => 'success',
            ],
            [
                'label' => __('dashboard.total_users'),
                'value' => User::count(),
                'icon'  => 'bi-people',
                'color' => 'info',
            ],
            [
                'label' => __('dashboard.total_roles'),
                'value' => Role::count(),
                'icon'  => 'bi-shield-lock',
                'color' => 'warning',
            ],
        ];

        $recentImports = class_exists(ModuleImportBatch::class)
            ? ModuleImportBatch::latest()->limit(8)->get()
            : collect();

        return view('admin.dashboard.index', [
            'stats'            => $stats,
            'moduleStats'      => $moduleStats,
            'chartLabels'      => $chartLabels,
            'chartCounts'      => $chartCounts,
            'govLabels'        => array_keys($govMap),
            'govCounts'        => array_values($govMap),
            'statusLabels'     => array_keys($statusMap),
            'statusCounts'     => array_values($statusMap),
            'readinessLabels'  => array_keys($readinessMap),
            'readinessCounts'  => array_values($readinessMap),
            'recentImports'    => $recentImports,
            'totalRecords'     => $totalRecords,
        ]);
    }

    private function moduleIcon(string $code): string
    {
        return match ($code) {
            'PRODUCTION_LINES' => 'bi-gear-wide-connected',
            'COMPANY' => 'bi-building',
            'SITES_COMPANIES' => 'bi-geo-alt',
            'PRODUCTIVE_CAPACITIES' => 'bi-speedometer2',
            'WAREHOUSES' => 'bi-box-seam',
            'PRODUCTS_SERVICES' => 'bi-box2',
            'FACILITIES_AND_PROPERTIES' => 'bi-houses',
            'MUTUAL_DEPENDENCE' => 'bi-diagram-3',
            default => 'bi-table',
        };
    }

    private function moduleColor(string $code): string
    {
        return match ($code) {
            'PRODUCTION_LINES' => 'primary',
            'COMPANY' => 'primary',
            'SITES_COMPANIES' => 'success',
            'PRODUCTIVE_CAPACITIES' => 'warning',
            'WAREHOUSES' => 'info',
            'PRODUCTS_SERVICES' => 'danger',
            'FACILITIES_AND_PROPERTIES' => 'secondary',
            'MUTUAL_DEPENDENCE' => 'dark',
            default => 'primary',
        };
    }
}
