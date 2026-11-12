<?php
// app/Services/Analytics/ModuleAnalyticsService.php

namespace App\Services\Analytics;

use Illuminate\Support\Facades\DB;
use App\Services\Analytics\Modules\LandsAnalytics;
use App\Services\Analytics\Modules\SitesAnalytics;
use App\Services\Analytics\Modules\HotelsAnalytics;
use App\Services\Analytics\Modules\MachinesAnalytics;
use App\Services\Analytics\Modules\ProjectsAnalytics;
use App\Services\Analytics\Modules\VehiclesAnalytics;
use App\Services\Analytics\Modules\CompaniesAnalytics;
use App\Services\Analytics\Modules\EquipmentAnalytics;
use App\Services\Analytics\Modules\HospitalsAnalytics;
use App\Services\Analytics\Modules\FacilitiesAnalytics;
use App\Services\Analytics\Modules\WarehousesAnalytics;
use App\Services\Analytics\Modules\FireStationsAnalytics;
use App\Services\Analytics\Modules\UnusedAssetsAnalytics;
use App\Services\Analytics\Modules\PowerStationsAnalytics;
use App\Services\Analytics\Modules\CommunicationsAnalytics;
use App\Services\Analytics\Modules\HumanResourcesAnalytics;
use App\Services\Analytics\Modules\InfrastructureAnalytics;
use App\Services\Analytics\Modules\StrategicSitesAnalytics;
use App\Services\Analytics\Modules\OperationsRoomsAnalytics;
use App\Services\Analytics\Modules\ProductionLinesAnalytics;
use App\Services\Analytics\Modules\MutualDependencyAnalytics;
use App\Services\Analytics\Modules\ProductionScopesAnalytics;
use App\Services\Analytics\Modules\ProductsServicesAnalytics;
use App\Services\Analytics\Modules\ContractsOperationsAnalytics;
use App\Services\Analytics\Modules\SubsidiaryCompaniesAnalytics;
use App\Services\Analytics\Modules\ProductiveCapacitiesAnalytics;
class ModuleAnalyticsService
{
    protected array $analyticsMap = [
    // الدفعة 1
    'VEHICLES'                  => VehiclesAnalytics::class,
    'EQUIPMENT'                 => EquipmentAnalytics::class,
    'HUMAN_RESOURCES'           => HumanResourcesAnalytics::class,
    'FACILITIES_AND_PROPERTIES' => FacilitiesAnalytics::class,

    // الدفعة 2
    'PRODUCTS_SERVICES'         => ProductsServicesAnalytics::class,
    'PROJECTS'                  => ProjectsAnalytics::class,
    'PRODUCTION_LINES'          => ProductionLinesAnalytics::class,
    'PRODUCTION_SCOPES'         => ProductionScopesAnalytics::class,
    'MACHINES'                  => MachinesAnalytics::class,
    'OPERATIONS_ROOMS'          => OperationsRoomsAnalytics::class,
    'POWER_STATIONS'            => PowerStationsAnalytics::class,
    'HOSPITALS'                 => HospitalsAnalytics::class,

    // الدفعة 3
    'COMPANY'                   => CompaniesAnalytics::class,
    'SITES_COMPANIES'           => SitesAnalytics::class,
    'PRODUCTIVE_CAPACITIES'     => ProductiveCapacitiesAnalytics::class,
    'WAREHOUSES'                => WarehousesAnalytics::class,
    'MUTUAL_DEPENDENCE'         => MutualDependencyAnalytics::class,
    'COMMUNICATIONS'            => CommunicationsAnalytics::class,
    'LANDS'                     => LandsAnalytics::class,
    'UNUSED_ID'                 => UnusedAssetsAnalytics::class,
    'STRATEGIC_SITES'           => StrategicSitesAnalytics::class,
    'INFRASTRUCTURE'            => InfrastructureAnalytics::class,
    'CONTINUED_COMPANY'         => SubsidiaryCompaniesAnalytics::class,
    'CONTRACTS_AND_OPERATIONS'  => ContractsOperationsAnalytics::class,
    'HOTELS'                    => HotelsAnalytics::class,
    'FIRE_STATIONS'             => FireStationsAnalytics::class,
];

    public function build(string $moduleCode): array
    {
        $registry = config('excel_modules');
        $module = $registry[$moduleCode] ?? null;
        if (!$module) {
            abort(404, "Module {$moduleCode} not found");
        }

        $class = $this->analyticsMap[$moduleCode] ?? null;
        if (!$class) {
            return $this->generic($moduleCode, $module);
        }

        return app($class)->build($moduleCode, $module);
    }

    protected function generic(string $moduleCode, array $module): array
    {
        $table = $module['table'];
        $charts = [];

        if (DB::getSchemaBuilder()->hasColumn($table, 'operating_status')) {
            $rows = DB::table($table)
                ->select('operating_status as label', DB::raw('count(*) as value'))
                ->groupBy('operating_status')->get()->map(fn($r) => (array) $r)->all();
            $charts[] = \App\Services\Analytics\ChartBuilders\DoughnutBuilder::build(
                'operating_status', 'حالة التشغيل', $rows
            );
        }

        if (DB::getSchemaBuilder()->hasColumn($table, 'governorate')) {
            $rows = DB::table($table)
                ->select('governorate as label', DB::raw('count(*) as value'))
                ->groupBy('governorate')->get()->map(fn($r) => (array) $r)->all();
            $charts[] = \App\Services\Analytics\ChartBuilders\BarBuilder::build(
                'governorate', 'التوزيع حسب المحافظة', $rows
            );
        }

        if (DB::getSchemaBuilder()->hasColumn($table, 'unit_name')) {
            $rows = DB::table($table)
                ->select('unit_name as label', DB::raw('count(*) as value'))
                ->whereNotNull('unit_name')->groupBy('unit_name')
                ->get()->map(fn($r) => (array) $r)->all();
            $charts[] = \App\Services\Analytics\ChartBuilders\BarBuilder::build(
                'unit_name', 'عدد السجلات لكل وحدة', $rows, true
            );
        }

        return [
            'module' => ['code' => $moduleCode, 'name_ar' => $module['name_ar']],
            'kpis'   => [],
            'charts' => $charts,
        ];
    }
}