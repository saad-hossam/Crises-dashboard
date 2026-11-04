<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

class IndustrialFacilitiesPreviewSheetImport implements ToArray
{
    public function __construct(
        private IndustrialFacilitiesPreviewImport $parent
    ) {}

    public function array(array $array): void
    {
        $this->parent->rows = $array;
    }
}
