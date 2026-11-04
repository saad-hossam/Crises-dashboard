<?php

namespace App\Imports;

use App\Models\IndustrialFacility;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class IndustrialFacilitiesSheetImport implements ToModel, WithStartRow
{
    public function __construct(
        private ?string $organizationalUnit = null
    ) {}

    public function startRow(): int
    {
        return 2;
    }

    /**
     * Convert Excel cell value to string.
     */
    private function textValue(array $row, int $index): ?string
    {
        $value = $row[$index] ?? null;

        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    /**
     * Convert coordinate value to float.
     */
    private function coordinateValue(
        array $row,
        int $index
    ): ?float {
        $value = $row[$index] ?? null;

        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        if (!is_numeric($value)) {
            throw new \UnexpectedValueException(
                'Invalid coordinate at column ' . ($index + 1)
            );
        }

        return (float) $value;
    }

    /**
     * Check whether the Excel row is empty.
     */
    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $value) {
            if (
                $value !== null &&
                trim((string) $value) !== ''
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Map Excel row to IndustrialFacility model.
     */
    public function model(array $row): ?IndustrialFacility
    {
        // Ignore completely empty rows.
        if ($this->isEmptyRow($row)) {
            return null;
        }

        return new IndustrialFacility([
            // Ownership and location
            'owner_organization' => $this->textValue($row, 0),
            'location_scope' => $this->textValue($row, 1),
            'governorate' => $this->textValue($row, 2),
            'facility_number' => $this->textValue($row, 3),
            'industrial_sector' => $this->textValue($row, 4),

            // Facility information
            'facility_name' => $this->textValue($row, 5),
            'trade_name' => $this->textValue($row, 6),
            'investor_code' => $this->textValue($row, 7),
            'tax_number' => $this->textValue($row, 8),
            'industrial_register_number' => $this->textValue($row, 9),

            // Industrial register dates
            'register_issue_date' => $this->textValue($row, 10),
            'register_expiry_date' => $this->textValue($row, 11),

            // Industrial activities
            'main_industrial_activity' => $this->textValue($row, 12),
            'main_activity_code' => $this->textValue($row, 13),
            'sub_industrial_activity' => $this->textValue($row, 14),
            'sub_activity_code' => $this->textValue($row, 15),

            // Commercial information
            'commercial_register_capital' => $this->textValue($row, 16),

            // Address and industrial region
            'city' => $this->textValue($row, 17),
            'industrial_region' => $this->textValue($row, 18),
            'industrial_zone_status' => $this->textValue($row, 19),
            'address' => $this->textValue($row, 20),

            // Contact information
            'contact_person' => $this->textValue($row, 21),
            'contact_job_title' => $this->textValue($row, 22),
            'official_phone' => $this->textValue($row, 23),
            'emergency_phone' => $this->textValue($row, 24),
            'email' => $this->textValue($row, 25),

            // Operation and production
            'operating_status' => $this->textValue($row, 26),
            'product_name' => $this->textValue($row, 27),
            'product_code' => $this->textValue($row, 28),
            'annual_production_quantity' => $this->textValue($row, 29),
            'production_unit' => $this->textValue($row, 30),
            'raw_materials' => $this->textValue($row, 31),

            // Workforce and consumption
            'total_employees' => $this->textValue($row, 32),
            'power_capacity_kw' => $this->textValue($row, 33),
            'annual_electricity_consumption' => $this->textValue($row, 34),
            'annual_water_consumption' => $this->textValue($row, 35),
            'main_fuel_type' => $this->textValue($row, 36),
            'annual_fuel_consumption' => $this->textValue($row, 37),

            // License information
            'license_number' => $this->textValue($row, 38),
            'license_issue_date' => $this->textValue($row, 39),
            'area_sqm' => $this->textValue($row, 40),
            'license_type' => $this->textValue($row, 41),

            // Geographic coordinates
            'longitude_x' => $this->coordinateValue($row, 42),
            'latitude_y' => $this->coordinateValue($row, 43),

            // Organizational information
            'organizational_unit' => $this->organizationalUnit,
        ]);
    }
}
