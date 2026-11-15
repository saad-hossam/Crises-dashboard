<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImportBatch;
use Illuminate\Http\Request;

class ImportBatchController extends Controller
{
    /**
     * Display import history.
     */
    public function index(Request $request)
    {
        $query = ImportBatch::query()
            ->with('creator')
            ->withCount([
                'errors',
                'productionLines',
            ]);

        /*
         * Search by file name or SHA-256.
         */
        $search = trim((string) $request->query('search', ''));

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('file_name', 'like', "%{$search}%")
                    ->orWhere('file_hash', 'like', "%{$search}%");
            });
        }

        /*
         * Filter by import status.
         */
        $allowedStatuses = [
            ImportBatch::STATUS_PENDING,
            ImportBatch::STATUS_PROCESSING,
            ImportBatch::STATUS_COMPLETED,
            ImportBatch::STATUS_FAILED,
        ];

        $status = $request->query('status', '');

        if (in_array($status, $allowedStatuses, true)) {
            $query->where('status', $status);
        } else {
            $status = '';
        }

        /*
         * Summary statistics.
         */
        $statistics = [
            'total' => ImportBatch::count(),

            'completed' => ImportBatch::where(
                'status',
                ImportBatch::STATUS_COMPLETED
            )->count(),

            'processing' => ImportBatch::where(
                'status',
                ImportBatch::STATUS_PROCESSING
            )->count(),

            'failed' => ImportBatch::where(
                'status',
                ImportBatch::STATUS_FAILED
            )->count(),

            'imported_rows' => (int) ImportBatch::sum('imported_rows'),

            'failed_rows' => (int) ImportBatch::sum('failed_rows'),
        ];

        $batches = $query
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.import-batches.index', compact(
            'batches',
            'statistics',
            'search',
            'status'
        ));
    }

    /**
     * Display one import batch and its errors.
     */
    public function show(ImportBatch $importBatch)
    {
        $importBatch->load('creator');

        $importBatch->loadCount([
            'errors',
            'productionLines',
        ]);

        $errors = $importBatch->errors()
            ->orderBy('excel_row')
            ->orderBy('id')
            ->paginate(50, ['*'], 'errors_page');

        return view('admin.import-batches.show', compact(
            'importBatch',
            'errors'
        ));
    }
}
