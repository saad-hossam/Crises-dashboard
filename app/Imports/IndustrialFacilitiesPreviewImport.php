<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\Import;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class IndustrialFacilitiesPreviewImport implements Import, WithMultipleSheets
{
    public array $rows = [];

    public function sheets(): array
    {
        return [
            'البيانات' => new IndustrialFacilitiesPreviewSheetImport($this),
        ];
    }
}
