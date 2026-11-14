@extends('layouts.admin')

@section('title', 'تفاصيل السجل #'.$record->id)
@section('page_title', 'تفاصيل السجل')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.excel-modules.index') }}">موديولات Excel</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.excel-modules.show', $module) }}">{{ $module->name_ar }}</a></li>
<li class="breadcrumb-item active">سجل #{{ $record->id }}</li>
@endsection



@section('content')
@php
    /* ========= Build groups from $data ========= */
    $rawData = $data ?? ($record->toArray() ?? []);
    $iconMap = [
        'أساسي'            => ['icon' => 'bi-info-circle-fill', 'color' => '#0d6efd'],
        'أساسيات'          => ['icon' => 'bi-info-circle-fill', 'color' => '#0d6efd'],
        'موقع'             => ['icon' => 'bi-geo-alt-fill',     'color' => '#10b981'],
        'موقع / إحداثيات'  => ['icon' => 'bi-geo-alt-fill',     'color' => '#10b981'],
        'سعات / أرقام'     => ['icon' => 'bi-bar-chart-fill',   'color' => '#8b5cf6'],
        'أرقام'            => ['icon' => 'bi-bar-chart-fill',   'color' => '#8b5cf6'],
        'حالة / تواريخ'    => ['icon' => 'bi-calendar-event-fill','color' => '#f59e0b'],
        'تواريخ'           => ['icon' => 'bi-calendar-event-fill','color' => '#f59e0b'],
        'تشغيل'            => ['icon' => 'bi-gear-fill',        'color' => '#06b6d4'],
        'اتصال'            => ['icon' => 'bi-telephone-fill',   'color' => '#ec4899'],
        'أخرى'             => ['icon' => 'bi-list-ul',          'color' => '#64748b'],
    ];

    $groups = $groups ?? [];
    $totalFields = 0;
    foreach ($groups as $g) { $totalFields += is_array($g) ? count($g) : 0; }

    // Detect coords
    $hasCoords = !empty($record->x_coordinate) && !empty($record->y_coordinate);

    // Record title
    $recordTitle = $record->unit_name ?: ($record->unit_code ?: ('سجل #'.$record->id));

    // KPI Status
    $kpiStatus = $record->operating_status
        ?? $record->line_status
        ?? $record->station_status
        ?? $record->machine_status
        ?? null;

    $statusClass = function ($status) {
        if (!$status) return 'rd-status-neutral';
        $s = mb_strtolower((string)$status);
        if (str_contains($s, 'oper') || str_contains($s, 'good') || str_contains($s, 'جاهز') || str_contains($s, 'يعمل')) return 'rd-status-good';
        if (str_contains($s, 'warn') || str_contains($s, 'متوسط') || str_contains($s, 'صيانة')) return 'rd-status-warn';
        if (str_contains($s, 'bad') || str_contains($s, 'معطل') || str_contains($s, 'fail')) return 'rd-status-bad';
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
                <span class="rd-badge"><i class="bi bi-grid-3x3-gap"></i>{{ $module->code ?? '' }}</span>
                <span class="rd-badge"><i class="bi bi-table"></i>{{ $module->name_ar ?? '' }}</span>
                <span class="rd-badge rd-badge-primary"><i class="bi bi-hash"></i>{{ $record->id }}</span>
            </div>
            <h1 class="rd-hero-title">{{ $recordTitle }}</h1>
            <div class="rd-hero-meta">
                @if(!empty($record->unit_code))
                    <span><i class="bi bi-hash"></i>{{ $record->unit_code }}</span>
                @endif
                @if($hasCoords)
                    <span>
                        <i class="bi bi-geo-alt"></i>
                        {{ is_numeric($record->x_coordinate) ? number_format((float)$record->x_coordinate, 4) : $record->x_coordinate }},
                        {{ is_numeric($record->y_coordinate) ? number_format((float)$record->y_coordinate, 4) : $record->y_coordinate }}
                    </span>
                @endif
                @if($kpiStatus)
                    <span class="rd-status-pill {{ $statusClass($kpiStatus) }}">
                        <i class="bi bi-activity"></i>{{ $kpiStatus }}
                    </span>
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
                <button type="button" class="rd-hero-btn" id="rdDownloadJson">
                    <i class="bi bi-download"></i>
                    <span>تحميل JSON</span>
                </button>
                <a href="{{ route('admin.excel-modules.show', $module) }}" class="rd-hero-btn rd-hero-btn-primary">
                    <i class="bi bi-arrow-{{ app()->getLocale()==='ar' ? 'right' : 'left' }}"></i>
                    <span>رجوع للجدول</span>
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
                <div class="rd-kpi-label">رقم السجل</div>
                <div class="rd-kpi-value">#{{ $record->id }}</div>
            </div>
        </div>
        <div class="rd-kpi">
            <div class="rd-kpi-icon" style="background:#10b981;">
                <i class="bi bi-building"></i>
            </div>
            <div class="rd-kpi-body">
                <div class="rd-kpi-label">كود الوحدة</div>
                <div class="rd-kpi-value" title="{{ $record->unit_code ?? '' }}">{{ $record->unit_code ?: '—' }}</div>
            </div>
        </div>
        <div class="rd-kpi">
            <div class="rd-kpi-icon" style="background:#f59e0b;">
                <i class="bi bi-list-check"></i>
            </div>
            <div class="rd-kpi-body">
                <div class="rd-kpi-label">عدد الحقول</div>
                <div class="rd-kpi-value">{{ $totalFields }}</div>
            </div>
        </div>
        <div class="rd-kpi">
            <div class="rd-kpi-icon" style="background:#8b5cf6;">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="rd-kpi-body">
                <div class="rd-kpi-label">آخر تحديث</div>
                <div class="rd-kpi-value" style="font-size:.85rem">
                    {{ $record->updated_at?->format('Y-m-d H:i') ?? '—' }}
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         MAP
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
            <span class="rd-toolbar-count">{{ $totalFields }}</span>
            <span class="text-muted small d-none d-md-inline">· {{ count($groups) }} مجموعات</span>
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
    <div class="row g-3" id="rdGroups">
        @forelse($groups as $groupName => $fields)
            @php
                $meta = $iconMap[$groupName] ?? ['icon' => 'bi-list-ul', 'color' => '#64748b'];
            @endphp
            <div class="col-12 col-lg-6 rd-group-col" data-group="{{ $groupName }}">
                <div class="rd-group">
                    <div class="rd-group-head" style="border-right: 3px solid {{ $meta['color'] }};">
                        <i class="bi {{ $meta['icon'] }}" style="color: {{ $meta['color'] }};"></i>
                        <span>{{ $groupName }}</span>
                        <span class="rd-group-count" style="background: {{ $meta['color'] }};">{{ count($fields) }}</span>
                    </div>
                    <div class="rd-group-body">
                        @foreach($fields as $key => $value)
                            @php
                                $isEmpty = is_null($value) || $value === '';
                                $strVal  = is_array($value)
                                    ? json_encode($value, JSON_UNESCAPED_UNICODE)
                                    : (string)$value;
                                $isLong  = mb_strlen($strVal) > 180;
                                $label   = str_replace('_', ' ', $key);
                            @endphp
                            <div class="rd-field"
                                 data-field-label="{{ mb_strtolower($label) }}"
                                 data-field-value="{{ mb_strtolower($strVal) }}">
                                <div class="rd-field-label">
                                    <span>{{ $label }}</span>
                                    @if(!$isEmpty)
                                        <button type="button" class="copy-btn"
                                                data-copy="{{ e($strVal) }}"
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
                                    @elseif(is_numeric($value) && !str_contains(strtolower((string)$key), 'code') && !str_contains((string)$key, 'كود'))
                                        <span style="font-family:'Courier New',monospace;">{{ $strVal }}</span>
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
                <div class="rd-empty-state">
                    <i class="bi bi-inbox"></i>
                    <p class="mb-0">لا توجد حقول لعرضها</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- No Results --}}
    <div class="rd-empty-state d-none mt-3" id="rdNoResults">
        <i class="bi bi-search"></i>
        <p class="mb-0">لا توجد حقول مطابقة للبحث</p>
    </div>

    {{-- ============================================================
         RAW JSON
         ============================================================ --}}
    <div class="rd-raw-card" id="rdRawCard">
        <div class="rd-raw-head" id="rdRawToggle">
            <h3 class="rd-raw-head-title">
                <i class="bi bi-braces"></i>
                البيانات الخام (JSON)
            </h3>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="rd-icon-btn" id="rdRawCopyBtn" title="نسخ JSON">
                    <i class="bi bi-clipboard"></i>
                </button>
                <i class="bi bi-chevron-down rd-raw-head-toggle"></i>
            </div>
        </div>
        <pre class="rd-raw-body" id="rdRawBody">{{ json_encode($rawData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const page = document.getElementById('rdPage');
    if (!page) return;

    const recordData = @json($rawData);

    // ==================== COPY SINGLE FIELD ====================
    document.querySelectorAll('.copy-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const text = this.dataset.copy || '';
            navigator.clipboard.writeText(text).then(() => {
                const original = this.innerHTML;
                this.innerHTML = '<i class="bi bi-check2"></i>';
                this.style.color = '#10b981';
                setTimeout(() => {
                    this.innerHTML = original;
                    this.style.color = '';
                }, 1200);
            }).catch(err => console.error('Copy failed', err));
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
                this.style.background = 'rgba(16,185,129,.35)';
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
            a.download = `record-{{ $module->code ?? 'record' }}-{{ $record->id }}.json`;
            a.click();
            URL.revokeObjectURL(url);
        });
    }

    // ==================== RAW JSON TOGGLE ====================
    const rawCard = document.getElementById('rdRawCard');
    const rawToggle = document.getElementById('rdRawToggle');
    if (rawToggle && rawCard) {
        rawToggle.addEventListener('click', function (e) {
            if (e.target.closest('#rdRawCopyBtn')) return;
            rawCard.classList.toggle('open');
        });
    }

    // ==================== RAW JSON COPY ====================
    const rawCopyBtn = document.getElementById('rdRawCopyBtn');
    if (rawCopyBtn) {
        rawCopyBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            const body = document.getElementById('rdRawBody');
            if (!body) return;
            navigator.clipboard.writeText(body.textContent).then(() => {
                const original = this.innerHTML;
                this.innerHTML = '<i class="bi bi-check2"></i>';
                this.style.background = '#dcfce7';
                this.style.color = '#10b981';
                setTimeout(() => {
                    this.innerHTML = original;
                    this.style.background = '';
                    this.style.color = '';
                }, 1200);
            });
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