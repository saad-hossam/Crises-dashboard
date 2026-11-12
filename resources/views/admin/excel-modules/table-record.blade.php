@extends('layouts.admin')

@section('title', __('excel.details').' — '.($config['name_ar'] ?? $moduleCode))
@section('page_title', __('excel.details'))

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.excel-modules.index') }}">{{ __('excel.modules') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.excel-modules.table', $moduleCode) }}">{{ $config['name_ar'] ?? $moduleCode }}</a></li>
<li class="breadcrumb-item active">#{{ $record->id }}</li>
@endsection

@push('styles')
<style>
/* ===== Aliases (local fallback) ===== */
:root {
    --aoi-primary: var(--admin-primary, #E87524);
    --aoi-surface: var(--admin-white, #fff);
    --aoi-border:  var(--admin-border, #e2e8f0);
    --aoi-muted:   var(--admin-muted, #64748b);
    --aoi-text:    var(--admin-text, #0f172a);
}


/* Hero (color-mix with fallback) */
.ao-detail-hero {
    background: #17365D;
    background: linear-gradient(
        135deg,
        color-mix(in srgb, var(--aoi-primary) 92%, #0f172a),
        var(--aoi-primary)
    );
    color: #fff;
    border-radius: 18px;
    padding: 1.35rem 1.5rem;
    box-shadow: 0 12px 30px rgba(15,23,42,.12);
}
.ao-detail-title { font-size: 1.35rem; font-weight: 800; margin: 0; word-break: break-word; }
.ao-detail-badge {
    display: inline-flex; align-items: center; gap: .35rem;
    background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.22);
    color: #fff; border-radius: 999px; padding: .2rem .65rem; font-size: .75rem; font-weight: 600;
}
.ao-detail-meta { display: flex; flex-wrap: wrap; gap: .75rem 1.1rem; opacity: .92; font-size: .875rem; }
.ao-detail-meta i { opacity: .85; margin-inline-end: .25rem; }

/* KPI */
.ao-detail-kpi {
    background: var(--aoi-surface);
    border: 1px solid var(--aoi-border);
    border-radius: 14px;
    padding: 1rem 1.1rem;
    height: 100%;
    box-shadow: 0 1px 2px rgba(15,23,42,.04);
}
.ao-detail-kpi-label { font-size: .75rem; color: var(--aoi-muted); font-weight: 600; margin-bottom: .35rem; }
.ao-detail-kpi-value { font-size: 1.05rem; font-weight: 800; color: var(--aoi-text); line-height: 1.3; }

/* Group card */
.ao-group-card {
    background: var(--aoi-surface);
    border: 1px solid var(--aoi-border);
    border-radius: 16px;
    overflow: hidden;
    height: 100%;
    box-shadow: 0 1px 2px rgba(15,23,42,.04);
}
.ao-group-head {
    display: flex; align-items: center; gap: .5rem;
    padding: .85rem 1.1rem;
    background: #f8fafc;
    background: color-mix(in srgb, var(--aoi-primary) 6%, var(--aoi-surface));
    border-bottom: 1px solid var(--aoi-border);
    font-weight: 700; font-size: .92rem;
}
.ao-group-head i { color: var(--aoi-primary); }
.ao-group-body { padding: .35rem 0; }

.ao-field-row {
    display: grid;
    grid-template-columns: minmax(120px, 38%) 1fr;
    gap: .75rem;
    padding: .7rem 1.1rem;
    border-bottom: 1px solid var(--aoi-border);
}
.ao-field-row:last-child { border-bottom: 0; }
.ao-field-row:hover {
    background: #fef6ee;
    background: color-mix(in srgb, var(--aoi-primary) 4%, transparent);
}
.ao-field-label {
    font-size: .8rem; font-weight: 600; color: var(--aoi-muted);
    line-height: 1.4;
}
.ao-field-value {
    font-size: .9rem; font-weight: 600; color: var(--aoi-text);
    word-break: break-word; line-height: 1.45;
}
.ao-field-value.empty { color: var(--aoi-muted); font-weight: 500; }

/* Coord chip */
.ao-coord-chip {
    display: inline-flex; align-items: center; gap: .35rem;
    background: #fff7ed;
    background: color-mix(in srgb, var(--aoi-primary) 8%, #fff);
    border: 1px solid #fed7aa;
    border-color: color-mix(in srgb, var(--aoi-primary) 18%, #e2e8f0);
    color: var(--aoi-primary);
    border-radius: 999px; padding: .25rem .7rem; font-size: .8rem; font-weight: 600;
    text-decoration: none;
}
.ao-coord-chip:hover {
    background: #ffedd5;
    background: color-mix(in srgb, var(--aoi-primary) 14%, #fff);
    color: var(--aoi-primary);
}

/* Responsive */
@media (max-width: 575.98px) {
    .ao-field-row { grid-template-columns: 1fr; gap: .25rem; padding: .65rem 1rem; }
    .ao-detail-title { font-size: 1.15rem; }
}

/* Dark mode */
.admin-wrapper.dark-mode .ao-detail-kpi,
.admin-wrapper.dark-mode .ao-group-card {
    background: #1e293b;
    border-color: #334155;
}
.admin-wrapper.dark-mode .ao-field-label,
.admin-wrapper.dark-mode .ao-detail-kpi-label { color: #94a3b8; }
.admin-wrapper.dark-mode .ao-field-value,
.admin-wrapper.dark-mode .ao-detail-kpi-value { color: #e2e8f0; }
.admin-wrapper.dark-mode .ao-group-head {
    background: #17243a;
    border-color: #334155;
    color: #e2e8f0;
}
</style>
@endpush

@section('content')
@php
    /* ========= 1. label map (supports both config shapes) ========= */
    $rawColumns = $config['columns'] ?? [];
    $labelMap = [];
    foreach ($rawColumns as $k => $v) {
        if (is_string($k) && is_string($v)) {
            // shape A: ['الاسم العربي' => 'db_column']
            $labelMap[$v] = $k;
        } elseif (is_string($v)) {
            // shape B: [0 => 'db_column']
            $labelMap[$v] = str_replace('_', ' ', $v);
        }
    }

    /* ========= 2. title fallback ========= */
    $title = collect([
        $record->name           ?? null,
        $record->name_ar        ?? null,
        $record->site_name      ?? null,
        $record->warehouse_name ?? null,
        $record->facility_name  ?? null,
        $record->product_name   ?? null,
        $record->unit_name      ?? null,
        $record->company_name   ?? null,
    ])->first(fn ($v) => filled($v)) ?? ('#'.$record->id);

    /* ========= 3. groups ========= */
    $groupsDef = [
        'identity' => [
            'title' => 'البيانات الأساسية',
            'icon'  => 'bi-card-heading',
            'keys'  => ['unit_code','unit_name','name','name_ar','name_en','company_name','owner_organization','product_name','site_name','warehouse_name','facility_name','asset_name','line_name'],
        ],
        'location' => [
            'title' => 'الموقع والعنوان',
            'icon'  => 'bi-geo-alt',
            'keys'  => ['governorate','location_scope','address','headquarters','city','district','center','x_coordinate','y_coordinate'],
        ],
        'status' => [
            'title' => 'الحالة والجاهزية',
            'icon'  => 'bi-activity',
            'keys'  => ['operating_status','readiness_level','line_status','status','station_status','technical_status'],
        ],
        'capacity' => [
            'title' => 'السعات والطاقة',
            'icon'  => 'bi-speedometer2',
            'keys'  => ['design_capacity','current_operational_capacity','annual_production_capacity','actual_production','utilization_rate','capacity_unit','area','area_unit','employees_count','workers_count','shifts_count','daily_operating_hours'],
        ],
        'org' => [
            'title' => 'البيانات التنظيمية',
            'icon'  => 'bi-building',
            'keys'  => ['company_type','legal_form','affiliation','main_activity','sub_activities','board_chairman','ceo','phone','email','website','commercial_register','tax_id','founded_at','founding_law'],
        ],
        'other' => [
            'title' => 'بيانات إضافية',
            'icon'  => 'bi-collection',
            'keys'  => [],
        ],
    ];

    $grouped = [];
    foreach ($groupsDef as $gKey => $g) {
        $grouped[$gKey] = ['title' => $g['title'], 'icon' => $g['icon'], 'items' => []];
    }

    /* ========= 4. iterate columns safely ========= */
    $allColumns = $columns ?? array_keys($labelMap);

    foreach ($allColumns as $col) {
        $placed = false;
        foreach ($groupsDef as $gKey => $g) {
            if ($gKey === 'other') continue;
            if (in_array($col, $g['keys'], true)) {
                $grouped[$gKey]['items'][$col] = $record->{$col} ?? null;
                $placed = true;
                break;
            }
        }
        if (!$placed) {
            $grouped['other']['items'][$col] = $record->{$col} ?? null;
        }
    }

    // drop empty groups
    $grouped = array_filter($grouped, fn ($g) => count($g['items']) > 0);

    /* ========= 5. KPIs ========= */
    $kpiStatus = $record->operating_status ?? $record->line_status ?? $record->station_status ?? '—';
    $kpiReady  = $record->readiness_level ?? '—';
    $kpiGov    = $record->governorate ?? '—';
    $kpiUnit   = $record->unit_name ?? $record->company_name ?? $record->owner_organization ?? '—';

    /* ========= 6. coordinates ========= */
    $hasCoords = ($record->x_coordinate !== null && $record->y_coordinate !== null);
@endphp

<div class="ao-detail-page">

    {{-- Hero --}}
    <div class="ao-detail-hero mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div class="min-w-0">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="ao-detail-badge">
                        <i class="bi bi-grid-3x3-gap"></i>{{ $moduleCode }}
                    </span>
                    <span class="ao-detail-badge">{{ $config['name_ar'] ?? $moduleCode }}</span>
                    <span class="ao-detail-badge">#{{ $record->id }}</span>
                </div>
                <h2 class="ao-detail-title mb-2">{{ $title }}</h2>
                <div class="ao-detail-meta">
                    @if(!empty($record->unit_code))
                        <span><i class="bi bi-hash"></i>{{ $record->unit_code }}</span>
                    @endif
                    @if(!empty($record->governorate))
                        <span><i class="bi bi-geo-alt"></i>{{ $record->governorate }}</span>
                    @endif
                    @if($kpiStatus !== '—')
                        <span><i class="bi bi-activity"></i>{{ $kpiStatus }}</span>
                    @endif
                    @if($kpiReady !== '—')
                        <span><i class="bi bi-shield-check"></i>{{ $kpiReady }}</span>
                    @endif
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @if($hasCoords)
                    <a class="btn btn-light btn-sm fw-semibold"
                       target="_blank" rel="noopener"
                       href="https://www.google.com/maps?q={{ $record->y_coordinate }},{{ $record->x_coordinate }}">
                        <i class="bi bi-map me-1"></i> فتح الخريطة
                    </a>
                @endif
                <a href="{{ route('admin.excel-modules.table', $moduleCode) }}"
                   class="btn btn-outline-light btn-sm fw-semibold">
                    <i class="bi bi-arrow-{{ app()->getLocale()==='ar' ? 'left' : 'right' }} me-1"></i>
                    {{ __('excel.back_to_table') ?? 'العودة للجدول' }}
                </a>
            </div>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="ao-detail-kpi">
                <div class="ao-detail-kpi-label"><i class="bi bi-upc me-1"></i> المعرّف</div>
                <div class="ao-detail-kpi-value">#{{ $record->id }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ao-detail-kpi">
                <div class="ao-detail-kpi-label"><i class="bi bi-building me-1"></i> الوحدة / الجهة</div>
                <div class="ao-detail-kpi-value text-truncate" title="{{ $kpiUnit }}">{{ $kpiUnit }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ao-detail-kpi">
                <div class="ao-detail-kpi-label"><i class="bi bi-activity me-1"></i> حالة التشغيل</div>
                <div class="ao-detail-kpi-value">{{ $kpiStatus }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ao-detail-kpi">
                <div class="ao-detail-kpi-label"><i class="bi bi-geo-alt me-1"></i> المحافظة</div>
                <div class="ao-detail-kpi-value">{{ $kpiGov }}</div>
            </div>
        </div>
    </div>

    {{-- Coordinates --}}
    @if($hasCoords)
        <div class="mb-4 d-flex flex-wrap gap-2 align-items-center">
            <a class="ao-coord-chip" target="_blank" rel="noopener"
               href="https://www.google.com/maps?q={{ $record->y_coordinate }},{{ $record->x_coordinate }}">
                <i class="bi bi-geo-alt-fill"></i>
                X: {{ $record->x_coordinate }} · Y: {{ $record->y_coordinate }}
                <i class="bi bi-box-arrow-up-left ms-1"></i>
            </a>
            <span class="text-muted small">اضغط لفتح الموقع في خرائط Google</span>
        </div>
    @endif

    {{-- Grouped fields --}}
    <div class="row g-3">
        @foreach($grouped as $g)
            <div class="col-12 col-lg-6">
                <div class="ao-group-card">
                    <div class="ao-group-head">
                        <i class="bi {{ $g['icon'] }}"></i>
                        <span>{{ $g['title'] }}</span>
                        <span class="badge bg-primary-subtle text-primary border ms-auto">
                            {{ count($g['items']) }}
                        </span>
                    </div>
                    <div class="ao-group-body">
                        @foreach($g['items'] as $col => $value)
                            @php
                                $label   = $labelMap[$col] ?? str_replace('_', ' ', $col);
                                $isEmpty = $value === null || $value === '';
                                $strVal  = (string) $value;
                                $isLong  = mb_strlen($strVal) > 200;
                            @endphp
                            <div class="ao-field-row">
                                <div class="ao-field-label">{{ $label }}</div>
                                <div class="ao-field-value {{ $isEmpty ? 'empty' : '' }}" title="{{ $strVal }}">
                                    @if($isEmpty)
                                        —
                                    @elseif($isLong)
                                        <details>
                                            <summary>{{ \Illuminate\Support\Str::limit($strVal, 180) }}</summary>
                                            <div class="mt-1">{{ $strVal }}</div>
                                        </details>
                                    @else
                                        {{ $strVal }}
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection