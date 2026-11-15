@extends('layouts.admin')

@section('title', __('excel.modules'))
@section('page_title', __('excel.modules'))

@section('breadcrumb')
<li class="breadcrumb-item active">{{ __('excel.modules') }}</li>
@endsection

@section('content')
<div class="mod-index" dir="rtl">

    {{-- ============================================================
         HERO
         ============================================================ --}}
    <div class="mod-index-hero">
        <div class="mod-index-hero-content">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <span class="mod-index-kicker">
                        <i class="bi bi-file-earmark-spreadsheet"></i>
                        {{ __('excel.modules') }}
                    </span>
                    <h1 class="mod-index-title">{{ __('excel.modules') }}</h1>
                    <p class="mod-index-sub">{{ __('excel.modules_desc') }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.excel-modules.create') }}" class="mod-index-btn mod-index-btn-primary">
                        <i class="bi bi-cloud-upload"></i> {{ __('excel.upload') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         STATS BAR
         ============================================================ --}}
    @php
        $totalModules   = count($modules);
        $totalRecords   = array_sum(array_column($modules, 'records_count'));
        $withRecords    = count(array_filter($modules, fn($m) => ($m['records_count'] ?? 0) > 0));
        $recentlyImport = count(array_filter($modules, function ($m) {
            $date = $m['last_imported_at'] ?? null;
            if (!$date) return false;
            try {
                $carbon = $date instanceof \Carbon\Carbon
                    ? $date
                    : \Carbon\Carbon::parse($date);
                return $carbon->diffInDays(now()) <= 30;
            } catch (\Throwable $e) {
                return false;
            }
        }));
    @endphp
    <div class="mod-index-stats">
        <div class="mod-index-stat">
            <div class="mod-index-stat-icon" style="background:#E87524;">
                <i class="bi bi-grid-3x3-gap-fill"></i>
            </div>
            <div>
                <div class="mod-index-stat-label">الموديولات</div>
                <div class="mod-index-stat-value" id="statTotal">{{ $totalModules }}</div>
            </div>
        </div>
        <div class="mod-index-stat">
            <div class="mod-index-stat-icon" style="background:#10b981;">
                <i class="bi bi-database-fill"></i>
            </div>
            <div>
                <div class="mod-index-stat-label">إجمالي السجلات</div>
                <div class="mod-index-stat-value">{{ number_format($totalRecords) }}</div>
            </div>
        </div>
        <div class="mod-index-stat">
            <div class="mod-index-stat-icon" style="background:#f59e0b;">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div>
                <div class="mod-index-stat-label">فيها بيانات</div>
                <div class="mod-index-stat-value">{{ $withRecords }}</div>
            </div>
        </div>
        <div class="mod-index-stat">
            <div class="mod-index-stat-icon" style="background:#8b5cf6;">
                <i class="bi bi-clock-history"></i>
            </div>
            <div>
                <div class="mod-index-stat-label">آخر 30 يوم</div>
                <div class="mod-index-stat-value">{{ $recentlyImport }}</div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         TOOLBAR
         ============================================================ --}}
    <div class="mod-index-toolbar">
        <div class="mod-search-wrapper">
            <i class="bi bi-search"></i>
            <input type="text"
                   id="modSearch"
                   class="mod-search-input"
                   placeholder="ابحث باسم الموديول أو الكود...">
        </div>

        <div class="d-flex flex-wrap gap-2 align-items-center">
            <select id="modSort" class="mod-sort-select">
                <option value="name-asc">الاسم (أ-ي)</option>
                <option value="name-desc">الاسم (ي-أ)</option>
                <option value="count-desc">السجلات (الأكثر أولاً)</option>
                <option value="count-asc">السجلات (الأقل أولاً)</option>
                <option value="recent">الأحدث استيراداً</option>
            </select>

            <div class="mod-view-toggle">
                <button type="button" class="mod-view-btn active" data-view="grid" title="عرض شبكي">
                    <i class="bi bi-grid-3x3-gap"></i>
                </button>
                <button type="button" class="mod-view-btn" data-view="list" title="عرض قائمة">
                    <i class="bi bi-list-ul"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- ============================================================
         NO RESULTS
         ============================================================ --}}
    <div id="noResults" class="mod-index-empty mod-hidden">
        <i class="bi bi-search"></i>
        <p class="mb-3">لا توجد موديولات مطابقة لبحثك</p>
        <button class="btn btn-sm btn-outline-primary" onclick="clearSearch()">
            <i class="bi bi-x-circle me-1"></i> مسح البحث
        </button>
    </div>

    {{-- ============================================================
         GRID VIEW
         ============================================================ --}}
    @php
        $modColors = [
            '#E87524', '#FF9A56', '#5B9BD5', '#7EC8E3',
            '#10b981', '#34D399', '#f59e0b', '#FBBF24',
            '#A78BFA', '#EC4899', '#06b6d4', '#22D3EE',
            '#84cc16', '#F97316', '#F43F5E',
        ];

        /**
         * خريطة كاملة: أيقونات لكل الموديولات
         */
        $iconMap = function ($code) {
            $c = strtoupper($code);

            // ============ الإنتاج ============
            if (str_contains($c, 'PRODUCTION') || str_contains($c, 'LINE')) return 'bi-diagram-3-fill';
            if (str_contains($c, 'CAPACIT')) return 'bi-speedometer2';

            // ============ الماكينات والمركبات ============
            if (str_contains($c, 'MACHINE')) return 'bi-cpu-fill';
            if (str_contains($c, 'VEHICLE') || str_contains($c, 'CAR')) return 'bi-truck';
            if (str_contains($c, 'AIRCRAFT') || str_contains($c, 'PLANE')) return 'bi-airplane-fill';

            // ============ المخازن والمخزون ============
            if (str_contains($c, 'WAREHOUSE')) return 'bi-archive-fill';
            if (str_contains($c, 'INVENTORY') || str_contains($c, 'STOCK') || str_contains($c, 'MATERIAL')) return 'bi-boxes';

            // ============ الموارد البشرية ============
            if (str_contains($c, 'HR') || str_contains($c, 'EMPLOYEE') || str_contains($c, 'USER') || str_contains($c, 'STAFF')) return 'bi-people-fill';

            // ============ الشركات ============
            if (str_contains($c, 'COMPANY') || str_contains($c, 'COMPANIES')) return 'bi-building-fill';

            // ============ المواقع ============
            if (str_contains($c, 'SITE') || str_contains($c, 'LOCATION')) return 'bi-geo-alt-fill';

            // ============ المنشآت والعقارات ============
            if (str_contains($c, 'FACILIT') || str_contains($c, 'PROPERT') || str_contains($c, 'REAL_ESTATE')) return 'bi-house-fill';

            // ============ المنتجات والخدمات ============
            if (str_contains($c, 'PRODUCT') || str_contains($c, 'SERVICE')) return 'bi-box-seam-fill';

            // ============ الترابط ============
            if (str_contains($c, 'DEPEND') || str_contains($c, 'RELATION')) return 'bi-diagram-2-fill';

            // ============ المشروعات ============
            if (str_contains($c, 'PROJECT')) return 'bi-kanban-fill';

            // ============ المالية ============
            if (str_contains($c, 'FINANCE') || str_contains($c, 'BUDGET') || str_contains($c, 'EXPENSE') || str_contains($c, 'INVOICE') || str_contains($c, 'PAYMENT')) return 'bi-cash-stack';

            // ============ الطاقة ============
            if (str_contains($c, 'ENERGY') || str_contains($c, 'POWER') || str_contains($c, 'FUEL') || str_contains($c, 'ELECTRIC')) return 'bi-lightning-charge-fill';

            // ============ الصحة ============
            if (str_contains($c, 'HEALTH') || str_contains($c, 'MEDICAL') || str_contains($c, 'HOSPITAL') || str_contains($c, 'PHARMAC')) return 'bi-heart-pulse-fill';

            // ============ التعليم ============
            if (str_contains($c, 'EDUCATION') || str_contains($c, 'TRAINING') || str_contains($c, 'COURSE')) return 'bi-mortarboard-fill';

            // ============ الاتصالات ============
            if (str_contains($c, 'TELECOM') || str_contains($c, 'NETWORK') || str_contains($c, 'DEVICE')) return 'bi-broadcast-pin';

            // ============ الإلكترونيات ============
            if (str_contains($c, 'ELECTRONIC') || str_contains($c, 'SOFTWARE') || str_contains($c, 'SYSTEM')) return 'bi-cpu';

            // ============ الأمن ============
            if (str_contains($c, 'SECURITY') || str_contains($c, 'SAFETY') || str_contains($c, 'DEFENSE')) return 'bi-shield-fill-check';

            // ============ النقل ============
            if (str_contains($c, 'TRANSPORT') || str_contains($c, 'LOGISTIC') || str_contains($c, 'SHIPPING') || str_contains($c, 'DELIVERY')) return 'bi-send-fill';

            // ============ المباني ============
            if (str_contains($c, 'BUILDING') || str_contains($c, 'CONSTRUCTION')) return 'bi-buildings-fill';

            // ============ الأصول ============
            if (str_contains($c, 'ASSET')) return 'bi-collection-fill';

            // ============ fallback ============
            return 'bi-table';
        };
    @endphp

    <div class="row g-3" id="gridView">
        @forelse($modules as $i => $module)
            @php
                $color = $modColors[$i % count($modColors)];
                $icon  = $iconMap($module['code']);

                $lastImported = $module['last_imported_at'] ?? null;
                $lastTs = 0;
                $lastHuman = null;
                if ($lastImported) {
                    try {
                        $carbon = $lastImported instanceof \Carbon\Carbon
                            ? $lastImported
                            : \Carbon\Carbon::parse($lastImported);
                        $lastTs = $carbon->timestamp;
                        $lastHuman = $carbon->diffForHumans();
                    } catch (\Throwable $e) {
                        $lastTs = 0;
                    }
                }
            @endphp
            <div class="col-12 col-sm-6 col-xl-4 module-col"
                 data-name="{{ mb_strtolower($module['name_ar']) }}"
                 data-code="{{ mb_strtolower($module['code']) }}"
                 data-count="{{ (int)($module['records_count'] ?? 0) }}"
                 data-recent="{{ $lastTs }}">
                <div class="mod-card" style="--mod-color: {{ $color }};">
                    <div class="mod-card-head">
                        <div class="mod-card-icon">
                            <i class="bi {{ $icon }}"></i>
                        </div>
                        <span class="mod-card-code">{{ $module['code'] }}</span>
                    </div>
                    <h5 class="mod-card-title" title="{{ $module['name_ar'] }}">{{ $module['name_ar'] }}</h5>
                    <div class="mod-card-table">
                        <i class="bi bi-table"></i>
                        <span>{{ $module['table'] }}</span>
                    </div>
                    <div class="mod-card-count">
                        <span class="mod-card-count-num">{{ number_format($module['records_count']) }}</span>
                        <span class="mod-card-count-lbl">{{ __('excel.records') }}</span>
                    </div>
                    <div class="mod-card-meta">
                        <i class="bi bi-clock"></i>
                        @if($lastHuman)
                            {{ __('excel.last_import') }} {{ $lastHuman }}
                        @else
                            {{ __('excel.never_imported') }}
                        @endif
                    </div>
                    <div class="mod-card-footer">
                        <a href="{{ route('admin.excel-modules.table', $module['code']) }}"
                           class="mod-card-btn">
                            <i class="bi bi-table"></i>
                            {{ __('excel.view_data') }}
                        </a>
                        <a href="{{ route('admin.excel-modules.analytics.page', $module['code']) }}"
                           class="mod-card-btn mod-card-btn-icon"
                           title="التحليلات">
                            <i class="bi bi-bar-chart-line"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="mod-index-empty">
                    <i class="bi bi-inbox"></i>
                    <p class="mb-3">{{ __('dashboard.no_data') }}</p>
                    <a href="{{ route('admin.excel-modules.create') }}" class="btn btn-primary">
                        <i class="bi bi-cloud-upload me-1"></i> استيراد أول موديول
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    {{-- ============================================================
         LIST VIEW
         ============================================================ --}}
    <div class="d-none" id="listView">
        @foreach($modules as $i => $module)
            @php
                $color = $modColors[$i % count($modColors)];
                $icon  = $iconMap($module['code']);

                $lastImported = $module['last_imported_at'] ?? null;
                $lastTs = 0;
                if ($lastImported) {
                    try {
                        $carbon = $lastImported instanceof \Carbon\Carbon
                            ? $lastImported
                            : \Carbon\Carbon::parse($lastImported);
                        $lastTs = $carbon->timestamp;
                    } catch (\Throwable $e) {
                        $lastTs = 0;
                    }
                }
            @endphp
            <a href="{{ route('admin.excel-modules.table', $module['code']) }}"
               class="mod-list-item module-col"
               style="--mod-color: {{ $color }};"
               data-name="{{ mb_strtolower($module['name_ar']) }}"
               data-code="{{ mb_strtolower($module['code']) }}"
               data-count="{{ (int)($module['records_count'] ?? 0) }}"
               data-recent="{{ $lastTs }}">
                <div class="mod-list-icon">
                    <i class="bi {{ $icon }}"></i>
                </div>
                <div class="mod-list-info">
                    <div class="mod-list-name">{{ $module['name_ar'] }}</div>
                    <div class="mod-list-code">{{ $module['code'] }} · {{ $module['table'] }}</div>
                </div>
                <div class="mod-list-count">{{ number_format($module['records_count']) }}</div>
            </a>
        @endforeach
    </div>

</div>
@endsection

@push('scripts')
<script>
function clearSearch() {
    const search = document.getElementById('modSearch');
    if (!search) return;
    search.value = '';
    search.dispatchEvent(new Event('input'));
}

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('modSearch');
    const sortSelect  = document.getElementById('modSort');
    const gridView    = document.getElementById('gridView');
    const listView    = document.getElementById('listView');
    const noResults   = document.getElementById('noResults');
    const viewBtns    = document.querySelectorAll('.mod-view-btn');

    if (!searchInput) return;

    // ==================== VIEW TOGGLE ====================
    viewBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const view = this.dataset.view;
            viewBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            if (view === 'grid') {
                gridView.classList.remove('d-none');
                listView.classList.add('d-none');
            } else {
                gridView.classList.add('d-none');
                listView.classList.remove('d-none');
            }
        });
    });

    // ==================== FILTER + SORT ====================
    function applyFiltersAndSort() {
        const query = searchInput.value.trim().toLowerCase();
        const sort  = sortSelect.value;

        // Filter — على الاتنين grid + list
        let visibleCount = 0;
        document.querySelectorAll('.module-col').forEach(col => {
            const name = col.dataset.name || '';
            const code = col.dataset.code || '';
            const match = !query || name.includes(query) || code.includes(query);
            col.classList.toggle('mod-hidden', !match);
            if (match) visibleCount++;
        });

        // Sort
        const containers = [gridView, listView];
        containers.forEach(container => {
            if (!container) return;
            const items = [...container.querySelectorAll('.module-col')];
            items.sort((a, b) => {
                const an = a.dataset.name, bn = b.dataset.name;
                const ac = parseInt(a.dataset.count || 0);
                const bc = parseInt(b.dataset.count || 0);
                const ar = parseInt(a.dataset.recent || 0);
                const br = parseInt(b.dataset.recent || 0);
                switch (sort) {
                    case 'name-asc':    return an.localeCompare(bn, 'ar');
                    case 'name-desc':   return bn.localeCompare(an, 'ar');
                    case 'count-desc':  return bc - ac;
                    case 'count-asc':   return ac - bc;
                    case 'recent':      return br - ar;
                    default:            return 0;
                }
            });
            items.forEach(el => container.appendChild(el));
        });

        // Show/hide no results
        noResults.classList.toggle('mod-hidden', visibleCount > 0 || document.querySelectorAll('.module-col').length === 0);
    }

    searchInput.addEventListener('input', applyFiltersAndSort);
    sortSelect.addEventListener('change', applyFiltersAndSort);

    // ==================== CTRL+F SHORTCUT ====================
    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
            e.preventDefault();
            searchInput.focus();
            searchInput.select();
        }
    });
});
</script>
@endpush

@push('styles')
<style>
/* ============================================================
   EXCEL MODULES INDEX — Modern Redesign
   ============================================================ */
.mod-index {
    font-family: 'Segoe UI', Tahoma, sans-serif;
    max-width: 1600px;
    margin: 0 auto;
}

/* ============ HERO ============ */
.mod-index-hero {
    position: relative;
    border-radius: 1.35rem;
    padding: 2rem 1.75rem;
    margin-bottom: 1.5rem;
    background: linear-gradient(135deg, #E87524 0%, #CF5F15 55%, #17365D 100%);
    color: #fff;
    overflow: hidden;
    box-shadow: 0 20px 40px -12px rgba(232, 117, 36, 0.35);
}
.mod-index-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image:
        radial-gradient(circle at 15% 25%, rgba(255, 255, 255, 0.15) 0%, transparent 40%),
        radial-gradient(circle at 85% 75%, rgba(255, 255, 255, 0.1) 0%, transparent 45%);
    pointer-events: none;
}
.mod-index-hero-content { position: relative; z-index: 2; }
.mod-index-kicker {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    background: rgba(255, 255, 255, 0.18);
    padding: .35rem .85rem;
    border-radius: 50px;
    font-size: .75rem;
    font-weight: 600;
    margin-bottom: .75rem;
    border: 1px solid rgba(255, 255, 255, .2);
}
.mod-index-title {
    font-size: clamp(1.4rem, 2.5vw, 1.9rem);
    font-weight: 800;
    margin-bottom: .4rem;
    color: #fff;
}
.mod-index-sub {
    font-size: .9rem;
    color: rgba(255, 255, 255, .8);
    margin: 0;
}
.mod-index-btn {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    padding: .65rem 1.15rem;
    border-radius: .75rem;
    font-size: .85rem;
    font-weight: 600;
    text-decoration: none;
    transition: all .2s;
    border: 1px solid rgba(255, 255, 255, .25);
    background: rgba(255, 255, 255, .15);
    color: #fff;
}
.mod-index-btn:hover {
    background: rgba(255, 255, 255, .25);
    color: #fff;
    transform: translateY(-2px);
}
.mod-index-btn-primary {
    background: #fff;
    color: #E87524;
    border-color: #fff;
}
.mod-index-btn-primary:hover {
    background: #f8fafc;
    color: #E87524;
}

/* ============ STATS BAR ============ */
.mod-index-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: .85rem;
    margin-bottom: 1.5rem;
}
.mod-index-stat {
    background: #fff;
    border-radius: 1rem;
    padding: 1rem 1.15rem;
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: .85rem;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
    transition: all .2s;
}
.mod-index-stat:hover {
    border-color: #cbd5e1;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
}
.mod-index-stat-icon {
    width: 42px;
    height: 42px;
    border-radius: .75rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.1rem;
    flex-shrink: 0;
}
.mod-index-stat-label {
    font-size: .72rem;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: .3px;
    font-weight: 700;
}
.mod-index-stat-value {
    font-size: 1.25rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
}

/* ============ TOOLBAR ============ */
.mod-index-toolbar {
    background: #fff;
    border-radius: 1rem;
    padding: 1rem 1.15rem;
    border: 1px solid #e2e8f0;
    margin-bottom: 1.25rem;
    display: flex;
    flex-wrap: wrap;
    gap: .75rem;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
}
.mod-search-wrapper {
    position: relative;
    flex: 1;
    min-width: 240px;
    max-width: 420px;
}
.mod-search-wrapper .bi-search {
    position: absolute;
    inset-inline-end: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    pointer-events: none;
    font-size: .9rem;
}
.mod-search-input {
    width: 100%;
    border-radius: 50px;
    border: 1px solid #e2e8f0;
    padding: .6rem 2.5rem .6rem 1.15rem;
    font-size: .88rem;
    background: #f8fafc;
    transition: all .2s;
}
.mod-search-input:focus {
    outline: none;
    background: #fff;
    border-color: #E87524;
    box-shadow: 0 0 0 3px rgba(232, 117, 36, .15);
}
.mod-sort-select {
    border-radius: .6rem;
    border: 1px solid #e2e8f0;
    padding: .55rem .9rem;
    font-size: .85rem;
    background: #f8fafc;
    color: #475569;
    font-weight: 500;
    cursor: pointer;
}
.mod-sort-select:focus {
    outline: none;
    border-color: #E87524;
}
.mod-view-toggle {
    display: inline-flex;
    background: #f1f5f9;
    border-radius: .6rem;
    padding: .2rem;
    gap: .15rem;
}
.mod-view-btn {
    background: transparent;
    border: 0;
    color: #64748b;
    padding: .4rem .75rem;
    border-radius: .45rem;
    font-size: .85rem;
    cursor: pointer;
    transition: all .15s;
}
.mod-view-btn.active {
    background: #fff;
    color: #E87524;
    box-shadow: 0 1px 4px rgba(15, 23, 42, .1);
}

/* ============ MODULE CARD ============ */
.mod-card {
    background: #fff;
    border-radius: 1.15rem;
    padding: 1.35rem 1.15rem;
    border: 1px solid #e2e8f0;
    height: 100%;
    transition: all .25s ease;
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
}
.mod-card::before {
    content: '';
    position: absolute;
    top: 0;
    inset-inline-end: 0;
    width: 100%;
    height: 4px;
    background: var(--mod-color, #E87524);
    transform: scaleX(.3);
    transform-origin: inset-inline-end;
    transition: transform .35s ease;
}
.mod-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 18px 35px rgba(15, 23, 42, .12);
    border-color: transparent;
}
.mod-card:hover::before { transform: scaleX(1); }

.mod-card-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}
.mod-card-icon {
    width: 48px;
    height: 48px;
    border-radius: .9rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: var(--mod-color, #E87524);
    color: #fff;
    font-size: 1.3rem;
    flex-shrink: 0;
    box-shadow: 0 6px 15px -5px var(--mod-color, #E87524);
    transition: transform .25s ease;
}
.mod-card:hover .mod-card-icon {
    transform: scale(1.08) rotate(-5deg);
}
.mod-card-code {
    font-family: 'Courier New', monospace;
    font-size: .7rem;
    color: #64748b;
    background: #f1f5f9;
    padding: .25rem .6rem;
    border-radius: 5px;
    font-weight: 700;
    letter-spacing: .3px;
}
.mod-card-title {
    font-size: 1rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 .35rem;
    line-height: 1.3;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.mod-card-table {
    font-size: .75rem;
    color: #94a3b8;
    margin-bottom: .85rem;
    font-family: 'Courier New', monospace;
    display: flex;
    align-items: center;
    gap: .3rem;
}
.mod-card-count {
    display: flex;
    align-items: baseline;
    gap: .4rem;
    margin-bottom: 1rem;
}
.mod-card-count-num {
    font-size: 1.9rem;
    font-weight: 800;
    color: var(--mod-color, #E87524);
    line-height: 1;
    letter-spacing: -.5px;
}
.mod-card-count-lbl {
    font-size: .8rem;
    color: #94a3b8;
    font-weight: 500;
}
.mod-card-meta {
    font-size: .78rem;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: .35rem;
    margin-bottom: 1rem;
    padding: .5rem .75rem;
    background: #f8fafc;
    border-radius: .55rem;
}
.mod-card-meta i { color: #94a3b8; }
.mod-card-footer {
    margin-top: auto;
    padding-top: 1rem;
    border-top: 1px dashed #e2e8f0;
    display: flex;
    gap: .4rem;
}
.mod-card-btn {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .3rem;
    padding: .55rem .75rem;
    border-radius: .55rem;
    font-size: .8rem;
    font-weight: 600;
    text-decoration: none;
    transition: all .15s;
    border: 1px solid var(--mod-color, #E87524);
    color: var(--mod-color, #E87524);
    background: transparent;
}
.mod-card-btn:hover {
    background: var(--mod-color, #E87524);
    color: #fff;
}
.mod-card-btn-icon {
    flex: 0 0 auto;
    width: 34px;
    padding: .55rem;
}

/* ============ LIST VIEW ============ */
.mod-list-item {
    background: #fff;
    border-radius: .85rem;
    border: 1px solid #e2e8f0;
    padding: .9rem 1.15rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all .15s;
    text-decoration: none;
    color: inherit;
    margin-bottom: .5rem;
}
.mod-list-item:hover {
    border-color: var(--mod-color, #E87524);
    color: inherit;
    background: #f8fafc;
}
.mod-list-icon {
    width: 40px;
    height: 40px;
    border-radius: .6rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: var(--mod-color, #E87524);
    color: #fff;
    font-size: 1rem;
    flex-shrink: 0;
}
.mod-list-info { flex: 1; min-width: 0; }
.mod-list-name {
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 .15rem;
    font-size: .92rem;
}
.mod-list-code {
    font-size: .72rem;
    color: #94a3b8;
    font-family: 'Courier New', monospace;
}
.mod-list-count {
    font-size: 1.1rem;
    font-weight: 800;
    color: #0f172a;
    text-align: left;
}

/* ============ EMPTY ============ */
.mod-index-empty {
    text-align: center;
    padding: 4rem 1rem;
    background: #fff;
    border-radius: 1rem;
    border: 1px dashed #cbd5e1;
    color: #94a3b8;
}
.mod-index-empty i {
    font-size: 3rem;
    opacity: .4;
    margin-bottom: 1rem;
    display: block;
}

.mod-hidden { display: none !important; }

/* ============ RESPONSIVE ============ */
@media (max-width: 767.98px) {
    .mod-index-hero { padding: 1.5rem 1.15rem; }
    .mod-card-count-num { font-size: 1.5rem; }
    .mod-index-toolbar { padding: .85rem 1rem; }
    .mod-index-stats { grid-template-columns: repeat(2, 1fr); gap: .65rem; }
    .mod-index-stat { padding: .85rem; gap: .65rem; }
    .mod-index-stat-icon { width: 36px; height: 36px; font-size: .95rem; }
    .mod-index-stat-value { font-size: 1.05rem; }
    .mod-search-wrapper { max-width: 100%; flex: 1 1 100%; }
    .mod-list-count { font-size: .95rem; }
}

@media (max-width: 575.98px) {
    .mod-index-stats { grid-template-columns: 1fr 1fr; }
    .mod-index-toolbar { flex-direction: column; align-items: stretch; }
    .mod-sort-select { width: 100%; }
}

/* ============ DARK MODE ============ */
.admin-wrapper.dark-mode .mod-index-stat,
.admin-wrapper.dark-mode .mod-index-toolbar,
.admin-wrapper.dark-mode .mod-card,
.admin-wrapper.dark-mode .mod-list-item {
    background: #1e293b;
    border-color: #334155;
}
.admin-wrapper.dark-mode .mod-index-stat-value,
.admin-wrapper.dark-mode .mod-card-title,
.admin-wrapper.dark-mode .mod-list-name,
.admin-wrapper.dark-mode .mod-list-count {
    color: #f1f5f9;
}
.admin-wrapper.dark-mode .mod-card-table,
.admin-wrapper.dark-mode .mod-card-count-lbl,
.admin-wrapper.dark-mode .mod-card-meta,
.admin-wrapper.dark-mode .mod-list-code,
.admin-wrapper.dark-mode .mod-index-stat-label {
    color: #94a3b8;
}
.admin-wrapper.dark-mode .mod-card-meta {
    background: #0f172a;
}
.admin-wrapper.dark-mode .mod-card-code {
    background: #0f172a;
    color: #cbd5e1;
}
.admin-wrapper.dark-mode .mod-index-empty {
    background: #1e293b;
    border-color: #334155;
}
.admin-wrapper.dark-mode .mod-search-input,
.admin-wrapper.dark-mode .mod-sort-select {
    background: #0f172a;
    border-color: #334155;
    color: #e2e8f0;
}
.admin-wrapper.dark-mode .mod-view-toggle {
    background: #0f172a;
}
.admin-wrapper.dark-mode .mod-view-btn.active {
    background: #334155;
    color: #FF9A56;
}
</style>
@endpush