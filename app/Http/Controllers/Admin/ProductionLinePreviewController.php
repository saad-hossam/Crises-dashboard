<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use JsonException;

class ProductionLinePreviewController extends Controller
{
    /**
     * Display extracted production lines preview.
     */
    public function index(Request $request)
    {
        $filePath = storage_path(
            'app/previews/production_lines_preview.json'
        );

        if (!File::exists($filePath)) {
            abort(404, 'ملف معاينة خطوط الإنتاج غير موجود. شغّل أمر Preview أولاً.');
        }

        try {
            $payload = json_decode(
                File::get($filePath),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (JsonException $exception) {
            abort(500, 'ملف معاينة خطوط الإنتاج غير صالح.');
        }

        if (
            !is_array($payload)
            || !isset($payload['records'])
            || !is_array($payload['records'])
        ) {
            abort(500, 'تركيب ملف معاينة خطوط الإنتاج غير صحيح.');
        }

        /*
         * All preview records.
         * No database insertion or modification.
         */
        $allRecords = collect($payload['records'])
            ->filter(fn ($record) => is_array($record))
            ->values();

        /*
         * Calculate summary from actual preview records.
         */
        $counts = [
            'total' => $allRecords->count(),
            'valid' => $allRecords->where('status', 'valid')->count(),
            'warning' => $allRecords->where('status', 'warning')->count(),
            'blocked' => $allRecords->where('status', 'blocked')->count(),
        ];

        /*
         * Search and status filter.
         */
        $search = trim((string) $request->query('search', ''));

        $status = $request->query('status', '');

        $allowedStatuses = [
            'valid',
            'warning',
            'blocked',
        ];

        if (!in_array($status, $allowedStatuses, true)) {
            $status = '';
        }

        $filteredRecords = $allRecords;

        if ($status !== '') {
            $filteredRecords = $filteredRecords
                ->where('status', $status)
                ->values();
        }

        if ($search !== '') {
            $filteredRecords = $filteredRecords
                ->filter(function (array $record) use ($search) {

                    $searchableData = [
                        $record['excel_row'] ?? '',
                        $record['status'] ?? '',
                        $record['data'] ?? [],
                        $record['errors'] ?? [],
                        $record['warnings'] ?? [],
                    ];

                    $searchableText = $this->flattenSearchableValues(
                        $searchableData
                    );

                    return mb_stripos($searchableText, $search) !== false;
                })
                ->values();
        }

        /*
         * Pagination: 10 records per page.
         */
        $perPage = 10;

        $currentPage = max(
            1,
            (int) $request->query('page', 1)
        );

        $paginatedRecords = new LengthAwarePaginator(
            $filteredRecords->forPage($currentPage, $perPage)->values(),
            $filteredRecords->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        /*
         * Preview metadata and original validation summary.
         */
        $previewMetadata = $payload['metadata'] ?? [];

        $validationSummary = $payload['summary'] ?? [];

        $lastModified = File::lastModified($filePath);

        return view('admin.production-lines.preview', [
            'records' => $paginatedRecords,
            'counts' => $counts,
            'search' => $search,
            'status' => $status,
            'previewMetadata' => $previewMetadata,
            'validationSummary' => $validationSummary,
            'previewLastModified' => $lastModified,
        ]);
    }

    /**
     * Convert nested record values into searchable text.
     */
    private function flattenSearchableValues(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        if ($value instanceof Collection) {
            $value = $value->all();
        }

        if (is_array($value)) {
            $parts = [];

            foreach ($value as $item) {
                $parts[] = $this->flattenSearchableValues($item);
            }

            return implode(' ', $parts);
        }

        return '';
    }
}
