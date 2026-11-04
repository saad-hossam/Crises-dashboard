<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Models\ImportBatch;
use Illuminate\Http\Request;
use App\Services\ImportHistoryService;

class ImportHistoryController extends Controller
{
    public function __construct(
        private ImportHistoryService $historyService
    ) {}

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'status' => [
                'nullable',
                'in:pending,processing,completed,failed',
            ],
            'target_table' => ['nullable', 'string', 'max:100'],
        ]);

        $batches = $this->historyService->getHistory($validated);

        return view('imports.history.index', compact('batches'));
    }

    public function show(ImportBatch $batch): View
    {
        return view('imports.history.show', compact('batch'));
    }
}
