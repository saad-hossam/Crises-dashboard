<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Services\ImportStatisticsService;

class ImportDashboardController extends Controller
{
    public function __construct(
        private ImportStatisticsService $statisticsService
    ) {}

    public function index(): View
    {
        $statistics = $this->statisticsService->getStatistics();

        $statusDistribution = $this->statisticsService
            ->getStatusDistribution();

        $rowComparison = $this->statisticsService
            ->getRowComparison();

        $recentImports = $this->statisticsService
            ->getRecentImports();

        return view('imports.dashboard.index', compact(
            'statistics',
            'statusDistribution',
            'rowComparison',
            'recentImports'
        ));
    }
}
