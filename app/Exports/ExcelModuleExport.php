<?php

namespace App\Exports;

use App\Models\ExcelModule;
use App\Models\ExcelRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * تصدير بيانات أي موديول كـ XLSX حقيقي باستخدام Maatwebsite/Excel
 */
class ExcelModuleExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    private array $headers = [];
    private Collection $records;

    public function __construct(
        private readonly ExcelModule $module,
        private readonly Request $request
    ) {
        $this->buildQuery();
    }

    private function buildQuery(): void
    {
        $query = ExcelRecord::where('excel_module_id', $this->module->id);

        if ($search = trim((string) $this->request->get('q'))) {
            $query->where('data', 'like', '%' . $search . '%');
        }
        if ($unit = $this->request->get('unit_code')) {
            $query->where('unit_code', $unit);
        }
        $filterColumn = $this->request->get('filter_column');
        $filterValue  = trim((string) $this->request->get('filter_value'));
        if ($filterColumn && $filterValue !== '') {
            $query->where("data->{$filterColumn}", 'like', '%' . $filterValue . '%');
        }

        $this->records = $query->orderBy('id')->get();

        // Collect all keys
        $allKeys = [];
        foreach ($this->records as $r) {
            foreach (array_keys($r->data ?? []) as $k) {
                $allKeys[$k] = true;
            }
        }
        $this->headers = array_keys($allKeys);

        // Prefer original header order from last import
        if (!empty($this->module->last_headers)) {
            $normalized = collect($this->module->last_headers)
                ->map(fn ($h) => preg_replace('/\s+/', '_', trim((string) $h)))
                ->filter()
                ->values()
                ->all();

            $this->headers = array_values(array_unique(array_merge(
                array_intersect($normalized, $this->headers),
                $this->headers
            )));
        }
    }

    public function collection(): Collection
    {
        return $this->records;
    }

    public function headings(): array
    {
        // Human-readable headers
        return array_map(
            fn ($h) => str_replace('_', ' ', $h),
            $this->headers
        );
    }

    public function map($record): array
    {
        $row = [];
        foreach ($this->headers as $h) {
            $val = $record->data[$h] ?? '';
            $row[] = is_array($val) ? json_encode($val, JSON_UNESCAPED_UNICODE) : $val;
        }
        return $row;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
