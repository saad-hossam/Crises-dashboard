<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\Import;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class IndustrialFacilitiesImport implements Import, WithMultipleSheets
{
    public function __construct(
        private ?string $organizationalUnit = null
    ) {}

    public function sheets(): array
    {
        return [
            'البيانات' => new IndustrialFacilitiesSheetImport(
                $this->organizationalUnit
            ),
        ];
    }
}
