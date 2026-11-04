<?php

namespace App\Services;

use App\Models\ImportBatch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ImportHistoryService
{
    public function getHistory(array $filters = []): LengthAwarePaginator
    {
        $query = ImportBatch::query();

        $this->applySearch($query, $filters);
        $this->applyStatusFilter($query, $filters);
        $this->applyTargetTableFilter($query, $filters);

        return $query
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();
    }

    private function applySearch(
        Builder $query,
        array $filters
    ): void {
        $search = trim($filters['search'] ?? '');

        if ($search === '') {
            return;
        }

        $query->where(function (Builder $subQuery) use ($search) {
            $subQuery
                ->where('original_filename', 'LIKE', "%{$search}%")
                ->orWhere('organizational_unit', 'LIKE', "%{$search}%");
        });
    }

    private function applyStatusFilter(
        Builder $query,
        array $filters
    ): void {
        $status = $filters['status'] ?? null;

        if (!in_array($status, [
            'pending',
            'processing',
            'completed',
            'failed',
        ], true)) {
            return;
        }

        $query->where('status', $status);
    }

    private function applyTargetTableFilter(
        Builder $query,
        array $filters
    ): void {
        $targetTable = trim($filters['target_table'] ?? '');

        if ($targetTable === '') {
            return;
        }

        $query->where('target_table', $targetTable);
    }
}
