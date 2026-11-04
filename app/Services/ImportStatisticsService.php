<?php

namespace App\Services;

use App\Models\ImportBatch;
use Illuminate\Support\Facades\DB;

class ImportStatisticsService
{
    public function getStatistics(): array
    {
        $statistics = ImportBatch::query()
            ->selectRaw('COUNT(*) as total_batches')
            ->selectRaw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_batches")
            ->selectRaw("SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_batches")
            ->selectRaw("SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing_batches")
            ->selectRaw('COALESCE(SUM(total_rows), 0) as total_rows')
            ->selectRaw('COALESCE(SUM(imported_rows), 0) as imported_rows')
            ->selectRaw('COALESCE(SUM(failed_rows), 0) as failed_rows')
            ->first();

        $totalRows = (int) $statistics->total_rows;
        $importedRows = (int) $statistics->imported_rows;

        $successRate = $totalRows > 0
            ? round(($importedRows / $totalRows) * 100, 2)
            : 0;

        return [
            'total_batches' => (int) $statistics->total_batches,
            'completed_batches' => (int) $statistics->completed_batches,
            'failed_batches' => (int) $statistics->failed_batches,
            'processing_batches' => (int) $statistics->processing_batches,
            'total_rows' => $totalRows,
            'imported_rows' => $importedRows,
            'failed_rows' => (int) $statistics->failed_rows,
            'success_rate' => $successRate,
        ];
    }

    public function getStatusDistribution(): array
    {
        return ImportBatch::query()
            ->select('status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
    }

    public function getRecentImports(int $limit = 5)
    {
        return ImportBatch::query()
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }

public function getRowComparison(): array
{
    $result = ImportBatch::query()
        ->selectRaw('COALESCE(SUM(imported_rows), 0) as imported_rows')
        ->selectRaw('COALESCE(SUM(failed_rows), 0) as failed_rows')
        ->first();

    return [
        'imported_rows' => (int) $result->imported_rows,
        'failed_rows' => (int) $result->failed_rows,
    ];
}

}
