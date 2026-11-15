{{-- resources/views/admin/excel-modules/analytics-dashboard.blade.php --}}
@extends('layouts.admin')

@section('title', 'لوحة التحليلات')

@section('content')
<div class="container-fluid analytics-dashboard" dir="rtl">

    {{-- Header --}}
    <div class="dashboard-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 position-relative">
            <div>
                <h3>
                    <i class="bi bi-bar-chart-line-fill me-2"></i>
                    لوحة التحليلات
                </h3>
                <p class="mb-0 opacity-75 small">اختر موديول لعرض تحليلاته ورسومه البيانية</p>
            </div>
            <div>
                <span class="badge-count">
                    <i class="bi bi-grid-3x3-gap me-1"></i>
                    {{ count($modules) }} موديول
                </span>
            </div>
        </div>
    </div>

    {{-- Stats Bar --}}
    

    {{-- Search Bar --}}
    <div class="search-wrapper mb-4">
        <i class="bi bi-search search-icon"></i>
        <input type="text"
               id="moduleSearch"
               class="form-control module-search"
               placeholder="ابحث عن موديول بالاسم أو الكود...">
    </div>

    {{-- Modules Grid --}}
    @php
        /**
         * خريطة كاملة: أيقونة + لون لكل موديول
         * ترجع: [icon, colorHex, colorClass]
         */
        function moduleMeta($code) {
            $code = strtoupper($code);

            // ============ الإنتاج ============
            if (str_contains($code, 'PRODUCTION') || str_contains($code, 'LINE') || str_contains($code, 'CAPACIT'))
                return ['bi-diagram-3-fill', '#E87524', 'icon-warning'];

            // ============ الماكينات ============
            if (str_contains($code, 'MACHINE'))
                return ['bi-cpu-fill', '#FF9A56', 'icon-warning'];

            // ============ المركبات ============
            if (str_contains($code, 'VEHICLE') || str_contains($code, 'CAR') || str_contains($code, 'TRUCK'))
                return ['bi-truck', '#7EC8E3', 'icon-info'];

            // ============ الطائرات ============
            if (str_contains($code, 'AIRCRAFT') || str_contains($code, 'PLANE') || str_contains($code, 'FLIGHT'))
                return ['bi-airplane-fill', '#5B9BD5', 'icon-primary'];

            // ============ المخازن ============
            if (str_contains($code, 'WAREHOUSE'))
                return ['bi-archive-fill', '#06b6d4', 'icon-info'];

            // ============ المخزون ============
            if (str_contains($code, 'INVENTORY') || str_contains($code, 'STOCK') || str_contains($code, 'MATERIAL'))
                return ['bi-boxes', '#84cc16', 'icon-success'];

            // ============ الموارد البشرية ============
            if (str_contains($code, 'HR') || str_contains($code, 'EMPLOYEE') || str_contains($code, 'USER') || str_contains($code, 'STAFF'))
                return ['bi-people-fill', '#10b981', 'icon-success'];

            // ============ الشركات ============
            if (str_contains($code, 'COMPANY') || str_contains($code, 'COMPANIES'))
                return ['bi-building-fill', '#5B9BD5', 'icon-primary'];

            // ============ المواقع ============
            if (str_contains($code, 'SITE') || str_contains($code, 'LOCATION'))
                return ['bi-geo-alt-fill', '#34D399', 'icon-success'];

            // ============ المنشآت والعقارات ============
            if (str_contains($code, 'FACILIT') || str_contains($code, 'PROPERT') || str_contains($code, 'REAL_ESTATE'))
                return ['bi-house-fill', '#F97316', 'icon-warning'];

            // ============ المنتجات والخدمات ============
            if (str_contains($code, 'PRODUCT') || str_contains($code, 'SERVICE'))
                return ['bi-box-seam-fill', '#EC4899', 'icon-danger'];

            // ============ الترابط ============
            if (str_contains($code, 'DEPEND') || str_contains($code, 'RELATION'))
                return ['bi-diagram-2-fill', '#A78BFA', 'icon-purple'];

            // ============ المشروعات ============
            if (str_contains($code, 'PROJECT'))
                return ['bi-kanban-fill', '#A78BFA', 'icon-purple'];

            // ============ المالية ============
            if (str_contains($code, 'FINANCE') || str_contains($code, 'BUDGET') || str_contains($code, 'EXPENSE') || str_contains($code, 'INVOICE') || str_contains($code, 'PAYMENT'))
                return ['bi-cash-stack', '#FBBF24', 'icon-warning'];

            // ============ الطاقة ============
            if (str_contains($code, 'ENERGY') || str_contains($code, 'POWER') || str_contains($code, 'FUEL') || str_contains($code, 'ELECTRIC'))
                return ['bi-lightning-charge-fill', '#FACC15', 'icon-warning'];

            // ============ الصحة ============
            if (str_contains($code, 'HEALTH') || str_contains($code, 'MEDICAL') || str_contains($code, 'HOSPITAL') || str_contains($code, 'PHARMAC'))
                return ['bi-heart-pulse-fill', '#34D399', 'icon-success'];

            // ============ التعليم ============
            if (str_contains($code, 'EDUCATION') || str_contains($code, 'TRAINING') || str_contains($code, 'COURSE'))
                return ['bi-mortarboard-fill', '#5B9BD5', 'icon-primary'];

            // ============ الاتصالات ============
            if (str_contains($code, 'TELECOM') || str_contains($code, 'NETWORK') || str_contains($code, 'DEVICE'))
                return ['bi-broadcast-pin', '#22D3EE', 'icon-info'];

            // ============ الإلكترونيات ============
            if (str_contains($code, 'ELECTRONIC') || str_contains($code, 'SOFTWARE') || str_contains($code, 'SYSTEM'))
                return ['bi-cpu', '#06b6d4', 'icon-info'];

            // ============ الأمن والسلامة ============
            if (str_contains($code, 'SECURITY') || str_contains($code, 'SAFETY') || str_contains($code, 'DEFENSE'))
                return ['bi-shield-fill-check', '#10b981', 'icon-success'];

            // ============ النقل واللوجستيات ============
            if (str_contains($code, 'TRANSPORT') || str_contains($code, 'LOGISTIC') || str_contains($code, 'SHIPPING') || str_contains($code, 'DELIVERY'))
                return ['bi-send-fill', '#FF9A56', 'icon-warning'];

            // ============ المباني ============
            if (str_contains($code, 'BUILDING') || str_contains($code, 'CONSTRUCTION'))
                return ['bi-buildings-fill', '#F97316', 'icon-warning'];

            // ============ الأصول ============
            if (str_contains($code, 'ASSET'))
                return ['bi-collection-fill', '#FBBF24', 'icon-warning'];

            // ============ fallback ذكي ============
            $hash = abs(crc32($code));
            $fallback = [
                ['bi-grid-fill',        '#5B9BD5', 'icon-primary'],
                ['bi-collection-fill',  '#10b981', 'icon-success'],
                ['bi-diagram-3-fill',   '#A78BFA', 'icon-purple'],
                ['bi-layers-fill',      '#FBBF24', 'icon-warning'],
                ['bi-stack',            '#06b6d4', 'icon-info'],
                ['bi-folder-fill',      '#EC4899', 'icon-danger'],
                ['bi-file-earmark-fill','#FF9A56', 'icon-warning'],
                ['bi-table',            '#7EC8E3', 'icon-info'],
            ];
            return $fallback[$hash % count($fallback)];
        }
    @endphp

    <div class="row g-3" id="modulesGrid">
        @forelse ($modules as $code => $module)
            @php
                [$icon, $colorHex, $colorClass] = moduleMeta($code);
                $nameAr = $module['name_ar'] ?? $code;
            @endphp
            <div class="col-sm-6 col-md-4 col-lg-3 module-item"
                 data-name="{{ mb_strtolower($nameAr) }}"
                 data-code="{{ mb_strtolower($code) }}">
                <a href="{{ route('admin.excel-modules.analytics.page', $code) }}"
                   class="module-card"
                   style="--mod-color: {{ $colorHex }};">
                    <div class="module-icon {{ $colorClass }}">
                        <i class="bi {{ $icon }}"></i>
                    </div>
                    <div class="module-name">{{ $nameAr }}</div>
                    <span class="module-code">{{ $code }}</span>
                    <i class="bi bi-arrow-left module-arrow"></i>
                </a>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <p>لا توجد موديولات متاحة حالياً</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- No Results State --}}
    <div id="noResults" class="empty-state d-none">
        <i class="bi bi-search"></i>
        <p>لا توجد نتائج مطابقة لبحثك</p>
    </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    const searchInput  = document.getElementById('moduleSearch');
    const moduleItems  = document.querySelectorAll('.module-item');
    const noResults    = document.getElementById('noResults');
    const visibleCount = document.getElementById('visibleCount');

    if (!searchInput) return;

    searchInput.addEventListener('input', function (e) {
        const query = e.target.value.trim().toLowerCase();
        let visible = 0;

        moduleItems.forEach(item => {
            const name = item.dataset.name || '';
            const code = item.dataset.code || '';
            const match = !query || name.includes(query) || code.includes(query);

            if (match) {
                item.classList.remove('d-none');
                visible++;
            } else {
                item.classList.add('d-none');
            }
        });

        visibleCount.textContent = visible;
        noResults.classList.toggle('d-none', visible > 0 || moduleItems.length === 0);
    });

    // Ctrl+K / Cmd+K shortcut to focus search
    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            searchInput.focus();
            searchInput.select();
        }
    });
})();
</script>
@endpush

@push('styles')
<style>
/* ============================================================
   ANALYTICS DASHBOARD — Modern Redesign
   ============================================================ */
.analytics-dashboard {
    font-family: 'Segoe UI', Tahoma, sans-serif;
    max-width: 1600px;
    margin: 0 auto;
}

/* Header */
.dashboard-header {
    background: linear-gradient(135deg, #E87524 0%, #CF5F15 55%, #17365D 100%);
    color: #fff;
    padding: 2rem 1.75rem;
    border-radius: 1.35rem;
    margin-bottom: 1.75rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 20px 40px -12px rgba(232, 117, 36, 0.35);
}
.dashboard-header::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image:
        radial-gradient(circle at 15% 25%, rgba(255, 255, 255, 0.15) 0%, transparent 40%),
        radial-gradient(circle at 85% 75%, rgba(255, 255, 255, 0.1) 0%, transparent 45%);
    pointer-events: none;
}
.dashboard-header > * { position: relative; z-index: 2; }
.dashboard-header h3 {
    font-weight: 800;
    font-size: clamp(1.3rem, 2.5vw, 1.75rem);
    margin-bottom: .35rem;
}
.dashboard-header .badge-count {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    background: rgba(255, 255, 255, .18);
    backdrop-filter: blur(8px);
    padding: .45rem 1rem;
    border-radius: 50px;
    font-size: .85rem;
    font-weight: 700;
    border: 1px solid rgba(255, 255, 255, .25);
}

/* Stats Bar */
.stats-bar {
    display: flex;
    justify-content: center;
    gap: .75rem;
    flex-wrap: wrap;
    margin-bottom: 1.25rem;
}
.stat-pill {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    background: #fff;
    padding: .55rem 1.1rem;
    border-radius: 50px;
    box-shadow: 0 4px 14px rgba(15, 23, 42, .06);
    border: 1px solid #e2e8f0;
    font-size: .85rem;
    font-weight: 600;
    color: #475569;
}
.stat-pill i { color: #E87524; font-size: .95rem; }
.stat-pill strong { color: #0f172a; font-weight: 800; }

/* Search */
.search-wrapper {
    position: relative;
    max-width: 500px;
    margin: 0 auto 1.5rem;
}
.search-wrapper .search-icon {
    position: absolute;
    inset-inline-end: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    pointer-events: none;
    font-size: .95rem;
}
.module-search {
    width: 100%;
    border-radius: 50px;
    padding: .75rem 2.75rem .75rem 1.25rem;
    border: 1px solid #e2e8f0;
    font-size: .9rem;
    background: #fff;
    transition: all .2s;
    box-shadow: 0 2px 8px rgba(15, 23, 42, .04);
}
.module-search:focus {
    outline: none;
    border-color: #E87524;
    box-shadow: 0 0 0 3px rgba(232, 117, 36, .15);
}

/* Module Card */
.module-card {
    display: block;
    text-decoration: none;
    color: inherit;
    background: #fff;
    border-radius: 1rem;
    padding: 1.35rem 1.15rem;
    border: 1px solid #e2e8f0;
    height: 100%;
    transition: all .25s ease;
    position: relative;
    overflow: hidden;
}
.module-card::before {
    content: '';
    position: absolute;
    top: 0;
    inset-inline-end: 0;
    width: 100%;
    height: 4px;
    background: var(--mod-color, #E87524);
    transform: scaleX(0);
    transform-origin: inset-inline-end;
    transition: transform .35s ease;
}
.module-card:hover {
    color: inherit;
    transform: translateY(-5px);
    box-shadow: 0 16px 32px rgba(15, 23, 42, 0.12);
    border-color: var(--mod-color, #E87524);
}
.module-card:hover::before { transform: scaleX(1); }

/* Module Icon */
.module-icon {
    width: 52px;
    height: 52px;
    border-radius: 1rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    color: #fff;
    margin-bottom: 1rem;
    background: var(--mod-color, #E87524);
    box-shadow: 0 6px 15px -5px var(--mod-color, #E87524);
    transition: transform .25s ease;
}
.module-card:hover .module-icon {
    transform: scale(1.1) rotate(-5deg);
}

/* Module Name */
.module-name {
    font-size: 1rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: .35rem;
    line-height: 1.35;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Module Code */
.module-code {
    display: inline-block;
    font-size: .72rem;
    font-weight: 700;
    font-family: 'Courier New', monospace;
    color: #64748b;
    background: #f1f5f9;
    padding: .2rem .55rem;
    border-radius: 5px;
    letter-spacing: .3px;
}

/* Module Arrow */
.module-arrow {
    position: absolute;
    bottom: 1rem;
    inset-inline-start: 1.15rem;
    color: #cbd5e1;
    font-size: 1.1rem;
    transition: all .2s;
}
.module-card:hover .module-arrow {
    color: var(--mod-color, #E87524);
    transform: translateX(-4px);
}
[dir="rtl"] .module-card:hover .module-arrow {
    transform: translateX(4px);
}

/* Empty */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #94a3b8;
}
.empty-state i {
    font-size: 3rem;
    opacity: .4;
    margin-bottom: .75rem;
    display: block;
}
.empty-state p {
    font-size: .95rem;
    margin: 0;
}

/* Responsive */
@media (max-width: 767.98px) {
    .dashboard-header { padding: 1.5rem 1.25rem; }
    .module-card { padding: 1.15rem 1rem; }
    .module-icon { width: 44px; height: 44px; font-size: 1.2rem; }
    .module-name { font-size: .9rem; }
}

/* Dark Mode */
.admin-wrapper.dark-mode .stat-pill,
.admin-wrapper.dark-mode .module-card,
.admin-wrapper.dark-mode .module-search {
    background: #1e293b;
    border-color: #334155;
    color: #e2e8f0;
}
.admin-wrapper.dark-mode .stat-pill strong,
.admin-wrapper.dark-mode .module-name {
    color: #f1f5f9;
}
.admin-wrapper.dark-mode .module-code {
    background: #0f172a;
    color: #94a3b8;
}
</style>
@endpush