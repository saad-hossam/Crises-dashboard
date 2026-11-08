<?php

namespace App\Http\Controllers;

use App\Models\ProductionLine;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class ProductionLineController extends Controller
{
    /**
     * Display production lines with search, filters and statistics.
     */
    public function index(Request $request)
    {
        $showDeleted = $request->boolean('deleted');
        $query = $showDeleted
        ? ProductionLine::onlyTrashed()
        : ProductionLine::query();
        // Search across all text fields
        if ($request->filled('search')) {
            $search = trim($request->search);

            $searchableColumns = [
                'owner_organization',
                'location_scope',
                'governorate',
                'name',
                'product_name',
                'manufacturer',
                'operating_year',
                'design_capacity',
                'capacity_unit',
                'current_operational_capacity',
                'annual_production_capacity',
                'actual_production',
                'utilization_rate',
                'shifts_count',
                'daily_operating_hours',
                'workers_count',
                'line_status',
                'expected_lifetime_years',
                'last_maintenance_date',
                'next_maintenance_date',
                'production_increase_possibility',
                'production_increase_lead_time_days',
                'development_type',
                'development_completion_date',
                'company_name',
                'address',
                'operating_status',
                'readiness_level',
                'last_updated_date',
                'data_entry_responsible',
                'x_coordinate',
                'y_coordinate',
            ];

            $query->where(function (Builder $builder) use ($search, $searchableColumns) {
                foreach ($searchableColumns as $column) {
                    $builder->orWhere($column, 'LIKE', "%{$search}%");
                }
            });
        }

        // Filters
        $filters = [
            'unit_id',
            'governorate',
            'owner_organization',
            'location_scope',
            'line_status',
            'operating_status',
            'readiness_level',
            'development_type',
            'operating_year',
        ];

        foreach ($filters as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        // Sorting
        $allowedSortColumns = ['name', 'operating_year', 'created_at'];

        $sortBy = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        if (! in_array($sortBy, $allowedSortColumns, true)) {
            $sortBy = 'created_at';
        }

        if (! in_array($sortDirection, ['asc', 'desc'], true)) {
            $sortDirection = 'desc';
        }

        $query->orderBy($sortBy, $sortDirection);

        // Pagination: 10 records per page
        $productionLines = $query
            ->with(['unit', 'creator', 'updater'])
            ->paginate(10)
            ->withQueryString();

        // Statistics for active records
        $statistics = [
            'total' => ProductionLine::count(),

            'working' => ProductionLine::where('line_status', 'يعمل')->count(),

            'stopped' => ProductionLine::where('line_status', 'لا يعمل')->count(),

            'under_development' => ProductionLine::where('line_status', 'تحت التطوير')->count(),

            'deleted' => ProductionLine::onlyTrashed()->count(),
        ];

        $governorates = ProductionLine::query()
            ->whereNotNull('governorate')
            ->where('governorate', '<>', '')
            ->distinct()
            ->orderBy('governorate')
            ->pluck('governorate');

        $units = \App\Models\Unit::orderBy('name')->get();

        return view('production-lines.index', compact(
            'productionLines',
            'showDeleted',
            'statistics',
            'governorates',
            'units'
        ));
    }

    /**
     * Display one production line.
     */
    public function show($id)
    {
        $productionLine = ProductionLine::withTrashed()
            ->with(['unit', 'creator', 'updater'])
            ->findOrFail($id);

        return view('production-lines.show', compact('productionLine'));
    }
}
