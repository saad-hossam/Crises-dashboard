@extends('layouts.admin')

@section('title', __('excel.details').' — '.($config['name_ar'] ?? $moduleCode))
@section('page_title', __('excel.details'))

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.excel-modules.index') }}">{{ __('excel.modules') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.excel-modules.table', $moduleCode) }}">{{ $config['name_ar'] ?? $moduleCode }}</a></li>
<li class="breadcrumb-item active">#{{ $record->id }}</li>
@endsection


@section('content')
@php
    /* ========= 1. Label map ========= */
    $rawColumns = $config['columns'] ?? [];
    $labelMap = [];
    foreach ($rawColumns as $k => $v) {
        if (is_string($k) && is_string($v)) {
            $labelMap[$v] = $k;
        } elseif (is_string($v)) {
            $labelMap[$v] = str_replace('_', ' ', $v);
        }
    }

    /* ========= 2. Title ========= */
    $title = collect([
        $record->name ?? null,
        $record->name_ar ?? null,
        $record->site_name ?? null,
        $record->warehouse_name ?? null,
        $record->facility_name ?? null,
        $record->product_name ?? null,
        $record->unit_name ?? null,
        $record->company_name ?? null,
    ])->first(fn ($v) => filled($v)) ?? ('#'.$record->id);

    /* ========= 3. Groups ========= */
    $groupsDef = [
        'identity' => [
            'title' => 'البيانات الأساسية',
            'icon'  => 'bi-card-heading',
            'keys'  => ['unit_code','unit_name','name','name_ar','name_en','company_name','owner_organization','product_name','site_name','warehouse_name','facility_name','asset_name','line_name','machine_name'],
        ],
        'location' => [
            'title' => 'الموقع والعنوان',
            'icon'  => 'bi-geo-alt',
            'keys'  => ['governorate','location_scope','address','headquarters','city','district','center','x_coordinate','y_coordinate'],
        ],
        'status' => [
            'title' => 'الحالة والجاهزية',
            'icon'  => 'bi-activity',
            'keys'  => ['operating_status','readiness_level','line_status','status','station_status','technical_status','machine_status'],
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

    /* ========= 4. Iterate columns ========= */
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

    $grouped = array_filter($grouped, fn ($g) => count($g['items']) > 0);

    /* ========= 5. KPIs ========= */
    $kpiStatus = $record->operating_status ?? $record->line_status ?? $record->station_status ?? $record->machine_status ?? '—';
    $kpiReady  = $record->readiness_level ?? '—';
    $kpiGov    = $record->governorate ?? '—';
    $kpiUnit   = $record->unit_name ?? $record->company_name ?? $record->owner_organization ?? '—';

    /* ========= 6. Coords ========= */
    $hasCoords = ($record->x_coordinate !== null && $record->y_coordinate !== null);

    /* ========= 7. Status helper ========= */
    $statusClass = function ($status) {
        if (!$status || $status === '—') return 'rd-status-neutral';
        $s = mb_strtolower((string)$status);
        if (str_contains($s, 'oper') || str_contains($s, 'good') || str_contains($s, 'جاهز') || str_contains($s, 'يعمل')) return 'rd-status-good';
        if (str_contains($s, 'warn') || str_contains($s, 'medium') || str_contains($s, 'متوسط') || str_contains($s, 'صيانة')) return 'rd-status-warn';
        if (str_contains($s, 'bad') || str_contains($s, 'fail') || str_contains($s, 'broken') || str_contains($s, 'معطل')) return 'rd-status-bad';
        return 'rd-status-neutral';
    };
@endphp

<div class="rd-page" dir="rtl" id="rdPage">

    {{-- ============================================================
         HERO
         ============================================================ --}}
    <div class="rd-hero">
        <div class="rd-hero-content">
            <div class="rd-hero-badges">
                <span class="rd-badge"><i class="bi bi-grid-3x3-gap"></i>{{ $moduleCode }}</span>
                <span class="rd-badge"><i class="bi bi-table"></i>{{ $config['name_ar'] ?? $moduleCode }}</span>
                <span class="rd-badge rd-badge-primary"><i class="bi bi-hash"></i>{{ $record->id }}</span>
            </div>
            <h1 class="rd-hero-title">{{ $title }}</h1>
            <div class="rd-hero-meta">
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
            <div class="rd-hero-actions">
                @if($hasCoords)
                    <a class="rd-hero-btn"
                       target="_blank" rel="noopener"
                       href="https://www.google.com/maps?q={{ $record->y_coordinate }},{{ $record->x_coordinate }}">
                        <i class="bi bi-map"></i>
                        <span>فتح الخريطة</span>
                    </a>
                @endif
                <button type="button" class="rd-hero-btn" id="rdCopyAll">
                    <i class="bi bi-clipboard"></i>
                    <span>نسخ الكل</span>
                </button>
              
                <a href="{{ route('admin.excel-modules.table', $moduleCode) }}"
                   class="rd-hero-btn rd-hero-btn-primary">
                    <i class="bi bi-arrow-{{ app()->getLocale()==='ar' ? 'right' : 'left' }}"></i>
                    <span>العودة للجدول</span>
                </a>
            </div>
        </div>
    </div>

    {{-- ============================================================
         KPIs
         ============================================================ --}}
    <div class="rd-kpis">
        <div class="rd-kpi">
            <div class="rd-kpi-icon" style="background:#0d6efd;">
                <i class="bi bi-hash"></i>
            </div>
            <div class="rd-kpi-body">
                <div class="rd-kpi-label">المعرّف</div>
                <div class="rd-kpi-value">#{{ $record->id }}</div>
            </div>
        </div>
        <div class="rd-kpi">
            <div class="rd-kpi-icon" style="background:#10b981;">
                <i class="bi bi-building"></i>
            </div>
            <div class="rd-kpi-body">
                <div class="rd-kpi-label">الوحدة / الجهة</div>
                <div class="rd-kpi-value" title="{{ $kpiUnit }}">{{ $kpiUnit }}</div>
            </div>
        </div>
        <div class="rd-kpi">
            <div class="rd-kpi-icon" style="background:#f59e0b;">
                <i class="bi bi-activity"></i>
            </div>
            <div class="rd-kpi-body">
                <div class="rd-kpi-label">حالة التشغيل</div>
                <div class="rd-kpi-value">
                    <span class="rd-status-pill {{ $statusClass($kpiStatus) }}">{{ $kpiStatus }}</span>
                </div>
            </div>
        </div>
        <div class="rd-kpi">
            <div class="rd-kpi-icon" style="background:#8b5cf6;">
                <i class="bi bi-geo-alt"></i>
            </div>
            <div class="rd-kpi-body">
                <div class="rd-kpi-label">المحافظة</div>
                <div class="rd-kpi-value">{{ $kpiGov }}</div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         MAP + COORDS
         ============================================================ --}}
    @if($hasCoords)
        <div class="rd-map-row">
            <div class="rd-map-wrapper">
                <iframe
                    loading="lazy"
                    src="https://www.openstreetmap.org/export/embed.html?bbox={{ (float)$record->x_coordinate - 0.01 }},{{ (float)$record->y_coordinate - 0.01 }},{{ (float)$record->x_coordinate + 0.01 }},{{ (float)$record->y_coordinate + 0.01 }}&layer=mapnik&marker={{ $record->y_coordinate }},{{ $record->x_coordinate }}"
                    title="الخريطة">
                </iframe>
            </div>
            <div class="rd-map-info">
                <h3 class="rd-map-title">
                    <i class="bi bi-geo-alt-fill"></i>
                    الموقع الجغرافي
                </h3>
                <a class="rd-coord-chip" target="_blank" rel="noopener"
                   href="https://www.google.com/maps?q={{ $record->y_coordinate }},{{ $record->x_coordinate }}">
                    <i class="bi bi-pin-map-fill"></i>
                    X: {{ $record->x_coordinate }} · Y: {{ $record->y_coordinate }}
                    <i class="bi bi-box-arrow-up-left"></i>
                </a>
                <p class="rd-map-meta mb-0">
                    اضغط على الإحداثيات لفتح الموقع في خرائط Google
                </p>
            </div>
        </div>
    @endif

    {{-- ============================================================
         TOOLBAR
         ============================================================ --}}
    <div class="rd-toolbar">
        <div class="rd-toolbar-info">
            <i class="bi bi-list-check text-primary"></i>
            <span>الحقول</span>
            <span class="rd-toolbar-count">{{ collect($grouped)->sum(fn($g) => count($g['items'])) }}</span>
            <span class="text-muted small d-none d-md-inline">· {{ count($grouped) }} مجموعات</span>
        </div>
        <div class="rd-toolbar-actions">
            <div class="rd-field-search-wrap">
                <i class="bi bi-search"></i>
                <input type="text" id="rdFieldSearch" class="rd-field-search" placeholder="ابحث في الحقول...">
            </div>
        </div>
    </div>

    {{-- ============================================================
         GROUPS
         ============================================================ --}}
    @php
        $groupColors = [
            'identity' => '#0d6efd',
            'location' => '#10b981',
            'status'   => '#f59e0b',
            'capacity' => '#8b5cf6',
            'org'      => '#ec4899',
            'other'    => '#64748b',
        ];
    @endphp
    <div class="row g-3" id="rdGroups">
        @forelse($grouped as $gKey => $g)
            <div class="col-12 col-lg-6 rd-group-col" data-group="{{ $gKey }}">
                <div class="rd-group">
                    <div class="rd-group-head" style="border-right: 3px solid {{ $groupColors[$gKey] ?? '#0d6efd' }};">
                        <i class="bi {{ $g['icon'] }}" style="color: {{ $groupColors[$gKey] ?? '#0d6efd' }};"></i>
                        <span>{{ $g['title'] }}</span>
                        <span class="rd-group-count">{{ count($g['items']) }}</span>
                    </div>
                    <div class="rd-group-body">
                        @foreach($g['items'] as $col => $value)
                            @php
                                $label   = $labelMap[$col] ?? str_replace('_', ' ', $col);
                                $isEmpty = $value === null || $value === '';
                                $strVal  = (string) $value;
                                $isLong  = mb_strlen($strVal) > 180;
                            @endphp
                            <div class="rd-field"
                                 data-field-label="{{ mb_strtolower($label) }}"
                                 data-field-value="{{ mb_strtolower($strVal) }}">
                                <div class="rd-field-label">
                                    <span>{{ $label }}</span>
                                    @if(!$isEmpty)
                                        <button type="button" class="copy-btn"
                                                data-copy="{{ $strVal }}"
                                                title="نسخ القيمة">
                                            <i class="bi bi-clipboard"></i>
                                        </button>
                                    @endif
                                </div>
                                <div class="rd-field-value {{ $isEmpty ? 'empty' : '' }} {{ $isLong ? 'long' : '' }}">
                                    @if($isEmpty)
                                        —
                                    @elseif($isLong)
                                        <details>
                                            <summary>{{ \Illuminate\Support\Str::limit($strVal, 160) }}</summary>
                                            <div class="rd-full-text">{{ $strVal }}</div>
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
        @empty
            <div class="col-12">
                <div class="rd-empty">
                    <i class="bi bi-inbox"></i>
                    <p>لا توجد بيانات متاحة</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- No Results --}}
    <div class="rd-empty d-none" id="rdNoResults">
        <i class="bi bi-search"></i>
        <p>لا توجد حقول مطابقة للبحث</p>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const page = document.getElementById('rdPage');
    if (!page) return;

    const recordData = @json($record->toArray());

    // ==================== COPY SINGLE FIELD ====================
    document.querySelectorAll('.copy-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const text = this.dataset.copy || '';
            navigator.clipboard.writeText(text).then(() => {
                const original = this.innerHTML;
                this.innerHTML = '<i class="bi bi-check2"></i>';
                setTimeout(() => { this.innerHTML = original; }, 1200);
            }).catch(err => {
                console.error('Copy failed', err);
            });
        });
    });

    // ==================== COPY ALL ====================
    const copyAllBtn = document.getElementById('rdCopyAll');
    if (copyAllBtn) {
        copyAllBtn.addEventListener('click', function () {
            const text = Object.entries(recordData)
                .map(([k, v]) => `${k}: ${v ?? ''}`)
                .join('\n');
            navigator.clipboard.writeText(text).then(() => {
                const span = this.querySelector('span');
                const original = span.textContent;
                span.textContent = 'تم النسخ ✓';
                this.style.background = 'rgba(16,185,129,.3)';
                setTimeout(() => {
                    span.textContent = original;
                    this.style.background = '';
                }, 1500);
            });
        });
    }

    // ==================== DOWNLOAD JSON ====================
    const downloadBtn = document.getElementById('rdDownloadJson');
    if (downloadBtn) {
        downloadBtn.addEventListener('click', function () {
            const blob = new Blob(
                [JSON.stringify(recordData, null, 2)],
                { type: 'application/json;charset=utf-8' }
            );
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `record-{{ $moduleCode }}-{{ $record->id }}.json`;
            a.click();
            URL.revokeObjectURL(url);
        });
    }

    // ==================== FIELD SEARCH ====================
    const searchInput = document.getElementById('rdFieldSearch');
    const noResults = document.getElementById('rdNoResults');
    const groupCols = document.querySelectorAll('.rd-group-col');

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = this.value.trim().toLowerCase();
            let totalVisible = 0;

            groupCols.forEach(col => {
                let visibleInGroup = 0;
                col.querySelectorAll('.rd-field').forEach(field => {
                    const label = field.dataset.fieldLabel || '';
                    const value = field.dataset.fieldValue || '';
                    const match = !q || label.includes(q) || value.includes(q);
                    field.classList.toggle('hidden', !match);
                    if (match) visibleInGroup++;
                });
                // Hide group if no fields match
                const show = !q || visibleInGroup > 0;
                col.style.display = show ? '' : 'none';
                totalVisible += visibleInGroup;
            });

            noResults.classList.toggle('d-none', totalVisible > 0);
        });
    }

    // ==================== CTRL+F SHORTCUT ====================
    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'f' && searchInput) {
            e.preventDefault();
            searchInput.focus();
            searchInput.select();
        }
    });
});
</script>
@endpush