<?php
// app/Http/Controllers/Admin/ModuleAnalyticsController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Analytics\ModuleAnalyticsService;

class ModuleAnalyticsController extends Controller
{
    public function __construct(protected ModuleAnalyticsService $service) {}

    /**
     * JSON endpoint: /admin/excel-modules/VEHICLES/analytics
     */
    public function show(string $moduleCode)
    {
        return response()->json($this->service->build($moduleCode));
    }

    /**
     * HTML page: /admin/excel-modules/VEHICLES/analytics-page
     */
    public function page(string $moduleCode)
    {
        $registry = config('excel_modules', []);
        if (!isset($registry[$moduleCode])) {
            abort(404, "Module {$moduleCode} not found");
        }

        return view('admin.excel-modules.analytics', [
            'moduleCode' => $moduleCode,
            'moduleName' => $registry[$moduleCode]['name_ar'] ?? $moduleCode,
        ]);
    }

    /**
     * Dashboard: /admin/excel-modules/analytics
     */
    public function dashboard()
    {
        return view('admin.excel-modules.analytics-dashboard', [
            'modules' => config('excel_modules', []),
        ]);
    }
}