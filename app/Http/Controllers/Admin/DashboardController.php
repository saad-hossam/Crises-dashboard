<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ModuleImportBatch;
use App\Models\Permission;
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
        $govLabels = [];
        $govCounts = [];
        $statusLabels = [];
        $statusCounts = [];
        $readinessLabels = [];
        $readinessCounts = [];

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

            $moduleStats[] = [
                'code'   => $code,
                'name'   => $cfg['name_ar'] ?? $code,
                'table'  => $cfg['table'] ?? '',
                'count'  => $count,
                'icon'   => $this->moduleIcon($code),
                'color'  => $this->moduleColor($code),
            ];

            $chartLabels[] = $cfg['name_ar'] ?? $code;
            $chartCounts[] = $count;

            // تجميع للمحافظات / الحالة إن وُجدت أعمدة
            try {
                $table = (new $model)->getTable();
                if ($count > 0 && Schema::hasColumn($table, 'governorate')) {
                    $rows = $model::query()
                        ->selectRaw('governorate, count(*) as c')
                        ->whereNotNull('governorate')
                        ->where('governorate', '!=', '')
                        ->groupBy('governorate')
                        ->orderByDesc('c')
                        ->limit(10)
                        ->pluck('c', 'governorate');
                    foreach ($rows as $g => $c) {
                        $govMap[$g] = ($govMap[$g] ?? 0) + (int) $c;
                    }
                }
                if ($count > 0 && Schema::hasColumn($table, 'operating_status')) {
                    $rows = $model::query()
                        ->selectRaw('operating_status, count(*) as c')
                        ->whereNotNull('operating_status')
                        ->where('operating_status', '!=', '')
                        ->groupBy('operating_status')
                        ->pluck('c', 'operating_status');
                    foreach ($rows as $s => $c) {
                        $statusMap[$s] = ($statusMap[$s] ?? 0) + (int) $c;
                    }
                }
                if ($count > 0 && Schema::hasColumn($table, 'readiness_level')) {
                    $rows = $model::query()
                        ->selectRaw('readiness_level, count(*) as c')
                        ->whereNotNull('readiness_level')
                        ->where('readiness_level', '!=', '')
                        ->groupBy('readiness_level')
                        ->pluck('c', 'readiness_level');
                    foreach ($rows as $r => $c) {
                        $readinessMap[$r] = ($readinessMap[$r] ?? 0) + (int) $c;
                    }
                }
            } catch (\Throwable $e) {
                // تجاهل جداول غير جاهزة
            }
        }

        arsort($govMap);
        $govMap = array_slice($govMap, 0, 8, true);
        $govLabels = array_keys($govMap);
        $govCounts = array_values($govMap);

        arsort($statusMap);
        $statusLabels = array_keys($statusMap);
        $statusCounts = array_values($statusMap);

        arsort($readinessMap);
        $readinessLabels = array_keys($readinessMap);
        $readinessCounts = array_values($readinessMap);

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

        $recentImports = ModuleImportBatch::latest()->limit(6)->get();

        return view('admin.dashboard.index', compact(
            'stats',
            'moduleStats',
            'chartLabels',
            'chartCounts',
            'govLabels',
            'govCounts',
            'statusLabels',
            'statusCounts',
            'readinessLabels',
            'readinessCounts',
            'recentImports',
            'totalRecords'
        ));
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
            'COMPANY' => 'indigo',
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
