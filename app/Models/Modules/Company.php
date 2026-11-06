<?php

namespace App\Models\Modules;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies';

    protected $fillable = [
        'owner_organization',
        'location_scope',
        'governorate',
        'name_ar',
        'name_en',
        'trade_name',
        'abbreviation',
        'company_type',
        'legal_form',
        'headquarters',
        'website',
        'phone',
        'board_chairman',
        'ceo',
        'data_entry_responsible',
        'founding_law',
        'affiliation',
        'state_ownership_percent',
        'authorized_capital',
        'email',
        'issued_capital',
        'employees_count',
        'subsidiaries_count',
        'sites_count',
        'paid_capital',
        'main_activity',
        'sub_activities',
        'founded_at',
        'commercial_register',
        'tax_id',
        'operating_status',
        'readiness_level',
        'last_updated_date',
        'address',
        'foreign_branches',
        'stock_listing_status',
        'x_coordinate',
        'y_coordinate',
        'unit_name',
        'unit_code',
        'import_batch_id',
    ];

    protected $casts = [
        'x_coordinate' => 'float',
        'y_coordinate' => 'float',
    ];
}
