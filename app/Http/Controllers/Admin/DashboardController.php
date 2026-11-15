<?php

namespace App\Http\Controllers\Admin;

use App\Models\ModuleImportBatch;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\LengthAwarePaginator;

class DashboardController extends Controller
{
    public function index()
    {
        $registry = config('excel_modules', []);

        // ============================================================
        // 1. عدد الماكينات
        // ============================================================
        $machineCount = Schema::hasTable('machines') ? DB::table('machines')->count() : 0;

        // ============================================================
        // 2. عدد خطوط الإنتاج
        // ============================================================
        $lineCount = Schema::hasTable('production_lines') ? DB::table('production_lines')->count() : 0;

        // ============================================================
        // 3. الموديولات
        // ============================================================
        $moduleStats = [];
        $totalRecords = 0;
        $activeModules = 0;

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

            $withCoords = 0;
            try {
                $table = (new $model)->getTable();
                if ($count > 0
                    && Schema::hasColumn($table, 'x_coordinate')
                    && Schema::hasColumn($table, 'y_coordinate')) {
                    $withCoords = $model::query()
                        ->whereNotNull('x_coordinate')
                        ->whereNotNull('y_coordinate')
                        ->count();
                }
            } catch (\Throwable $e) {
                // ignore
            }

            $moduleStats[] = [
                'code'        => $code,
                'name'        => $cfg['name_ar'] ?? $code,
                'table'       => $cfg['table'] ?? '',
                'count'       => $count,
                'icon'        => $this->moduleIcon($code),
                'color'       => $this->moduleColor($code),
                'with_coords' => $withCoords,
                'coverage'    => $count > 0 ? round(($withCoords / max($count, 1)) * 100) : 0,
            ];
        }

        // ============================================================
        // 4. إجمالي السجلات
        // ============================================================
        $knownTables = array_column($moduleStats, 'table');
        if (!in_array('machines', $knownTables) && $machineCount > 0) {
            $totalRecords += $machineCount;
        }
        if (!in_array('production_lines', $knownTables) && $lineCount > 0) {
            $totalRecords += $lineCount;
        }

        // ============================================================
        // 5. ترتيب الموديولات + Top 10
        // ============================================================
        $sortedModules = collect($moduleStats)->sortByDesc('count')->values();

        $top10Modules = $sortedModules->take(10);
        $top10ModulesLabels = $top10Modules->pluck('name')->toArray();
        $top10ModulesData   = $top10Modules->pluck('count')->toArray();

        $allModulesLabels = collect($moduleStats)->pluck('name')->toArray();
        $allModulesData   = collect($moduleStats)->pluck('count')->toArray();

        // ============================================================
        // 6. Pagination للموديولات — 8 لكل صفحة
        // ============================================================
        $perPage = 8;
        $currentPage = LengthAwarePaginator::resolveCurrentPage('modules_page');
        $moduleCollection = collect($moduleStats)->sortByDesc('count')->values();

        $paginatedModules = new LengthAwarePaginator(
            $moduleCollection->forPage($currentPage, $perPage)->values(),
            $moduleCollection->count(),
            $perPage,
            $currentPage,
            [
                'path'     => request()->url(),
                'pageName' => 'modules_page',
            ]
        );

        // ============================================================
        // 7. Recent Imports
        // ============================================================
        $recentImports = class_exists(ModuleImportBatch::class)
            ? ModuleImportBatch::latest()->limit(8)->get()
            : collect();

        return view('admin.dashboard.index', [
            // KPIs
            'totalRecords'  => $totalRecords,
            'machineCount'  => $machineCount,
            'lineCount'     => $lineCount,
            'activeModules' => $activeModules,
            'totalModules'  => count($moduleStats),

            // Charts data
            'top10ModulesLabels' => $top10ModulesLabels,
            'top10ModulesData'   => $top10ModulesData,
            'allModulesLabels'   => $allModulesLabels,
            'allModulesData'     => $allModulesData,

            // Lists
            'moduleStats'      => $moduleStats,
            'sortedModules'    => $sortedModules,
            'paginatedModules' => $paginatedModules,
            'recentImports'    => $recentImports,
        ]);
    }

    /**
     * خريطة كاملة: أيقونات لكل الموديولات
     */
    private function moduleIcon(string $code): string
    {
        return match ($code) {
            // ============ الإنتاج ============
            'PRODUCTION_LINES'          => 'bi-diagram-3-fill',
            'PRODUCTIVE_CAPACITIES'     => 'bi-speedometer2',

            // ============ الشركات والمواقع ============
            'COMPANY'                   => 'bi-building-fill',
            'SITES_COMPANIES'           => 'bi-geo-alt-fill',
            'MUTUAL_DEPENDENCE'         => 'bi-diagram-2-fill',

            // ============ المخازن والمنتجات ============
            'WAREHOUSES'                => 'bi-archive-fill',
            'PRODUCTS_SERVICES'         => 'bi-box-seam-fill',

            // ============ المنشآت والعقارات ============
            'FACILITIES_AND_PROPERTIES' => 'bi-house-fill',

            // ============ الماكينات والمركبات ============
            'MACHINES'                  => 'bi-cpu-fill',
            'VEHICLES'                  => 'bi-truck',
            'CARS'                      => 'bi-car-front-fill',
            'EQUIPMENT'                 => 'bi-tools',
            'TOOLS'                     => 'bi-wrench-adjustable',
            'SPARE_PARTS'               => 'bi-gear-fill',

            // ============ الموارد البشرية ============
            'HR'                        => 'bi-people-fill',
            'EMPLOYEES'                 => 'bi-person-badge-fill',
            'STAFF'                     => 'bi-person-vcard-fill',
            'TEAMS'                     => 'bi-people',
            'DEPARTMENTS'               => 'bi-diagram-3',

            // ============ المالية ============
            'FINANCE'                   => 'bi-cash-stack',
            'BUDGETS'                   => 'bi-wallet2',
            'EXPENSES'                  => 'bi-receipt',
            'INVOICES'                  => 'bi-file-earmark-text-fill',
            'CONTRACTS'                 => 'bi-file-earmark-check-fill',
            'PAYMENTS'                  => 'bi-credit-card-fill',

            // ============ المشروعات ============
            'PROJECTS'                  => 'bi-kanban-fill',
            'TASKS'                     => 'bi-list-task',
            'MILESTONES'                => 'bi-flag-fill',
            'PROGRESS'                  => 'bi-bar-chart-steps',

            // ============ المخزون ============
            'INVENTORY'                 => 'bi-boxes',
            'STOCK'                     => 'bi-box-seam',
            'SUPPLIES'                  => 'bi-basket-fill',
            'MATERIALS'                 => 'bi-layers-fill',
            'RAW_MATERIALS'             => 'bi-box-fill',

            // ============ الطائرات والدفاع ============
            'AIRCRAFT'                  => 'bi-airplane-fill',
            'AIRCRAFTS'                 => 'bi-airplane-engines-fill',
            'PLANES'                    => 'bi-airplane',
            'DEFENSE'                   => 'bi-shield-fill-check',
            'WEAPONS'                   => 'bi-crosshair',
            'MISSILES'                  => 'bi-rocket-fill',

            // ============ الطاقة ============
            'ENERGY'                    => 'bi-lightning-charge-fill',
            'POWER'                     => 'bi-lightning-fill',
            'FUEL'                      => 'bi-fuel-pump-fill',
            'ELECTRICITY'               => 'bi-plug-fill',
            'WATER'                     => 'bi-droplet-fill',
            'SOLAR'                     => 'bi-sun-fill',

            // ============ الاتصالات والإلكترونيات ============
            'ELECTRONICS'               => 'bi-cpu',
            'TELECOM'                   => 'bi-broadcast-pin',
            'NETWORKS'                  => 'bi-router-fill',
            'DEVICES'                   => 'bi-phone-fill',
            'SOFTWARE'                  => 'bi-code-slash',
            'SYSTEMS'                   => 'bi-gear-wide-connected',

            // ============ المباني والمرافق ============
            'BUILDINGS'                 => 'bi-buildings-fill',
            'CONSTRUCTION'              => 'bi-bricks',
            'FACILITIES'                => 'bi-building-gear',
            'UTILITIES'                 => 'bi-plug',
            'MAINTENANCE'               => 'bi-tools',

            // ============ السلامة والأمن ============
            'SAFETY'                    => 'bi-shield-check',
            'SECURITY'                  => 'bi-shield-lock-fill',
            'FIRE'                      => 'bi-fire',
            'EMERGENCY'                 => 'bi-exclamation-triangle-fill',

            // ============ الصحة ============
            'HEALTH'                    => 'bi-heart-pulse-fill',
            'HOSPITALS'                 => 'bi-hospital-fill',
            'MEDICAL'                   => 'bi-bandaid-fill',
            'PHARMACY'                  => 'bi-capsule',

            // ============ التعليم ============
            'EDUCATION'                 => 'bi-mortarboard-fill',
            'TRAINING'                  => 'bi-book-fill',
            'COURSES'                   => 'bi-journal-bookmark-fill',

            // ============ النقل واللوجستيات ============
            'TRANSPORT'                 => 'bi-truck',
            'LOGISTICS'                 => 'bi-box-seam',
            'SHIPPING'                  => 'bi-ship',
            'DELIVERY'                  => 'bi-send-fill',
            'SUPPLY_CHAIN'              => 'bi-diagram-2',

            // ============ الأصول ============
            'ASSETS'                    => 'bi-collection-fill',
            'PROPERTIES'                => 'bi-house-door-fill',
            'LAND'                      => 'bi-map-fill',
            'REAL_ESTATE'               => 'bi-building-check',

            // ============ عام ============
            'GENERAL'                   => 'bi-grid-fill',
            'OTHER'                     => 'bi-three-dots',
            'MISC'                      => 'bi-collection',

            default                     => $this->fallbackIcon($code),
        };
    }

    /**
     * خريطة كاملة: ألوان لكل الموديولات
     * الألوان فاتحة وحيوية — بدون أي لون غامق أو أسود
     */
    private function moduleColor(string $code): string
    {
        return match ($code) {
            // ============ الإنتاج ============
            'PRODUCTION_LINES'          => '#E87524',
            'PRODUCTIVE_CAPACITIES'     => '#f59e0b',

            // ============ الشركات والمواقع ============
            'COMPANY'                   => '#5B9BD5',
            'SITES_COMPANIES'           => '#10b981',
            'MUTUAL_DEPENDENCE'         => '#A78BFA',

            // ============ المخازن والمنتجات ============
            'WAREHOUSES'                => '#06b6d4',
            'PRODUCTS_SERVICES'         => '#EC4899',

            // ============ المنشآت والعقارات ============
            'FACILITIES_AND_PROPERTIES' => '#F97316',

            // ============ الماكينات والمركبات ============
            'MACHINES'                  => '#FF9A56',
            'VEHICLES'                  => '#7EC8E3',
            'CARS'                      => '#22D3EE',
            'EQUIPMENT'                 => '#84cc16',
            'TOOLS'                     => '#FBBF24',
            'SPARE_PARTS'               => '#34D399',

            // ============ الموارد البشرية ============
            'HR'                        => '#10b981',
            'EMPLOYEES'                 => '#34D399',
            'STAFF'                     => '#84cc16',
            'TEAMS'                     => '#A78BFA',
            'DEPARTMENTS'               => '#5B9BD5',

            // ============ المالية ============
            'FINANCE'                   => '#FBBF24',
            'BUDGETS'                   => '#F59E0B',
            'EXPENSES'                  => '#F97316',
            'INVOICES'                  => '#FF9A56',
            'CONTRACTS'                 => '#EC4899',
            'PAYMENTS'                  => '#34D399',

            // ============ المشروعات ============
            'PROJECTS'                  => '#A78BFA',
            'TASKS'                     => '#7EC8E3',
            'MILESTONES'                => '#F43F5E',
            'PROGRESS'                  => '#22D3EE',

            // ============ المخزون ============
            'INVENTORY'                 => '#06b6d4',
            'STOCK'                     => '#0ea5e9',
            'SUPPLIES'                  => '#FBBF24',
            'MATERIALS'                 => '#84cc16',
            'RAW_MATERIALS'             => '#F97316',

            // ============ الطائرات والدفاع ============
            'AIRCRAFT'                  => '#5B9BD5',
            'AIRCRAFTS'                 => '#3b82f6',
            'PLANES'                    => '#60a5fa',
            'DEFENSE'                   => '#ef4444',
            'WEAPONS'                   => '#F43F5E',
            'MISSILES'                  => '#f97316',

            // ============ الطاقة ============
            'ENERGY'                    => '#FBBF24',
            'POWER'                     => '#f59e0b',
            'FUEL'                      => '#FF9A56',
            'ELECTRICITY'               => '#FACC15',
            'WATER'                     => '#7EC8E3',
            'SOLAR'                     => '#fbbf24',

            // ============ الاتصالات والإلكترونيات ============
            'ELECTRONICS'               => '#06b6d4',
            'TELECOM'                   => '#22D3EE',
            'NETWORKS'                  => '#5B9BD5',
            'DEVICES'                   => '#A78BFA',
            'SOFTWARE'                  => '#EC4899',
            'SYSTEMS'                   => '#60a5fa',

            // ============ المباني والمرافق ============
            'BUILDINGS'                 => '#F97316',
            'CONSTRUCTION'              => '#FF9A56',
            'FACILITIES'                => '#FBBF24',
            'UTILITIES'                 => '#7EC8E3',
            'MAINTENANCE'               => '#84cc16',

            // ============ السلامة والأمن ============
            'SAFETY'                    => '#10b981',
            'SECURITY'                  => '#5B9BD5',
            'FIRE'                      => '#ef4444',
            'EMERGENCY'                 => '#F97316',

            // ============ الصحة ============
            'HEALTH'                    => '#34D399',
            'HOSPITALS'                 => '#22D3EE',
            'MEDICAL'                   => '#EC4899',
            'PHARMACY'                  => '#A78BFA',

            // ============ التعليم ============
            'EDUCATION'                 => '#5B9BD5',
            'TRAINING'                  => '#7EC8E3',
            'COURSES'                   => '#A78BFA',

            // ============ النقل واللوجستيات ============
            'TRANSPORT'                 => '#06b6d4',
            'LOGISTICS'                 => '#FF9A56',
            'SHIPPING'                  => '#5B9BD5',
            'DELIVERY'                  => '#10b981',
            'SUPPLY_CHAIN'              => '#84cc16',

            // ============ الأصول ============
            'ASSETS'                    => '#FBBF24',
            'PROPERTIES'                => '#F97316',
            'LAND'                      => '#84cc16',
            'REAL_ESTATE'               => '#FF9A56',

            // ============ عام ============
            'GENERAL'                   => '#5B9BD5',
            'OTHER'                     => '#94a3b8',
            'MISC'                      => '#cbd5e1',

            default                     => $this->fallbackColor($code),
        };
    }

    /**
     * Fallback ذكي — لو الموديول مش في القائمة
     * بيختار لون من 15 لون فاتح بناءً على hash الكود
     */
    private function fallbackColor(string $code): string
    {
        $colors = [
            '#E87524', '#FF9A56', '#5B9BD5', '#7EC8E3', '#10b981',
            '#34D399', '#f59e0b', '#FBBF24', '#A78BFA', '#EC4899',
            '#06b6d4', '#22D3EE', '#84cc16', '#F97316', '#F43F5E',
        ];
        $index = abs(crc32($code)) % count($colors);
        return $colors[$index];
    }

    /**
     * Fallback ذكي — لو الموديول مش في القائمة
     * بيختار أيقونة من مجموعة بناءً على hash الكود
     */
    private function fallbackIcon(string $code): string
    {
        $icons = [
            'bi-grid-fill', 'bi-collection-fill', 'bi-box-seam-fill',
            'bi-diagram-3-fill', 'bi-kanban-fill', 'bi-stack',
            'bi-layers-fill', 'bi-folder-fill', 'bi-file-earmark-fill',
            'bi-table', 'bi-list-ul', 'bi-card-list',
        ];
        $index = abs(crc32($code)) % count($icons);
        return $icons[$index];
    }
}