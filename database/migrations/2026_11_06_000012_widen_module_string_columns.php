<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $alters = [
            'companies' => [
                'founded_at', 'founding_law', 'main_activity', 'sub_activities',
                'foreign_branches', 'address', 'headquarters', 'affiliation',
                'authorized_capital', 'issued_capital', 'paid_capital',
                'board_chairman', 'ceo', 'trade_name', 'name_ar', 'name_en',
            ],
            'production_lines' => [
                'name', 'product_name', 'manufacturer', 'address', 'company_name',
                'owner_organization', 'design_capacity', 'actual_production',
            ],
            'facilities_properties' => [
                'facility_name', 'usage', 'address', 'owner_organization', 'company_name',
            ],
            'sites' => ['site_name', 'address', 'owner_organization', 'company_name'],
            'warehouses' => ['warehouse_name', 'address', 'owner_organization', 'materials_type'],
            'products_services' => ['name', 'usage', 'address', 'main_raw_materials', 'gov_beneficiaries'],
            'productive_capacities' => ['product_name', 'address', 'required_raw_materials'],
            'mutual_dependencies' => ['counterpart', 'outage_impact', 'mitigation_action', 'address'],
        ];

        foreach ($alters as $table => $cols) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            foreach ($cols as $col) {
                if (!Schema::hasColumn($table, $col)) {
                    continue;
                }
                try {
                    DB::statement("ALTER TABLE `{$table}` MODIFY `{$col}` TEXT NULL");
                } catch (\Throwable $e) {
                    // ignore
                }
            }
        }
    }

    public function down(): void
    {
        //
    }
};
