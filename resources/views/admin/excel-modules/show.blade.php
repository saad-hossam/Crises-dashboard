@extends('layouts.admin')

@section('title', $module->name_ar)
@section('page_title', $module->name_ar)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.excel-modules.index') }}">موديولات Excel</a></li>
<li class="breadcrumb-item active">{{ $module->name_ar }}</li>
@endsection



@section('content')
<div class="mod-show" dir="rtl">

    {{-- ============================================================
         HERO HEADER
         ============================================================ --}}
    <div class="mod-show-hero">
        <div class="mod-show-hero-content">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <span class="mod-show-kicker">
                        <i class="bi bi-table"></i>
                        {{ $module->code }}
                    </span>
                    <h1 class="mod-show-title">{{ $module->name_ar }}</h1>
                    <div class="mod-show-meta">
                        <span>
                            <i class="bi bi-database"></i>
                            {{ number_format($module->records_count ?? $records->total()) }} سجل
                        </span>
                        @if($module->last_imported_at)
                            <span>
                                <i class="bi bi-clock-history"></i>
                                آخر استيراد {{ $module->last_imported_at->diffForHumans() }}
                            </span>
                        @endif
                        @if(!empty($headers))
                            <span>
                                <i class="bi bi-columns-gap"></i>
                                {{ count($headers) }} عمود
                            </span>
                        @endif
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.excel-modules.export', ['module' => $module->id] + request()->query()) }}"
                       class="mod-show-btn">
                        <i class="bi bi-file-earmark-excel"></i> تصدير Excel
                    </a>
                    <a href="{{ route('admin.excel-modules.create') }}" class="mod-show-btn mod-show-btn-primary">
                        <i class="bi bi-upload"></i> استيراد جديد
                    </a>
                    <a href="{{ route('admin.excel-modules.index') }}" class="mod-show-btn">
                        <i class="bi bi-grid"></i> الموديولات
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         STATS STRIP
         ============================================================ --}}
    <div class="mod-show-stats">
        <div class="mod-stat">
            <div class="mod-stat-icon" style="background:#0d6efd;">
                <i class="bi bi-database-fill"></i>
            </div>
            <div>
                <div class="mod-stat-label">إجمالي السجلات</div>
                <div class="mod-stat-value">{{ number_format($module->records_count ?? $records->total()) }}</div>
            </div>
        </div>
        <div class="mod-stat">
            <div class="mod-stat-icon" style="background:#10b981;">
                <i class="bi bi-funnel-fill"></i>
            </div>
            <div>
                <div class="mod-stat-label">السجلات المعروضة</div>
                <div class="mod-stat-value">{{ number_format($records->total()) }}</div>
            </div>
        </div>
        <div class="mod-stat">
            <div class="mod-stat-icon" style="background:#f59e0b;">
                <i class="bi bi-columns-gap"></i>
            </div>
            <div>
                <div class="mod-stat-label">عدد الأعمدة</div>
                <div class="mod-stat-value">{{ count($headers ?? []) }}</div>
            </div>
        </div>
        <div class="mod-stat">
            <div class="mod-stat-icon" style="background:#8b5cf6;">
                <i class="bi bi-file-earmark-bar-graph-fill"></i>
            </div>
            <div>
                <div class="mod-stat-label">الصفحة الحالية</div>
                <div class="mod-stat-value">{{ $records->currentPage() }} / {{ $records->lastPage() }}</div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         FILTERS
         ============================================================ --}}
    <div class="mod-filter-panel">
        <div class="mod-filter-title">
            <i class="bi bi-funnel"></i>
            تصفية البيانات
        </div>
        <form method="GET" action="{{ route('admin.excel-modules.show', $module) }}" id="filterForm">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label>بحث عام</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                               placeholder="ابحث في أي عمود...">
                    </div>
                </div>
                <div class="col-md-3">
                    <label>كود الوحدة</label>
                    <input type="text" name="unit_code" value="{{ request('unit_code') }}" class="form-control"
                           placeholder="مثال: U-001">
                </div>
                <div class="col-md-2">
                    <label>لكل صفحة</label>
                    <select name="per_page" class="form-select">
                        @foreach([25, 50, 100, 200] as $n)
                            <option value="{{ $n }}" @selected(request('per_page', 50) == $n)>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-primary flex-fill" type="submit" style="border-radius:.55rem;font-size:.85rem;">
                        <i class="bi bi-funnel me-1"></i> تطبيق
                    </button>
                    <a href="{{ route('admin.excel-modules.show', $module) }}" class="btn btn-outline-secondary" style="border-radius:.55rem;font-size:.85rem;">
                        إعادة
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- ============================================================
         TABLE TOOLBAR
         ============================================================ --}}
    <div class="mod-toolbar">
        <div class="mod-toolbar-info">
            <i class="bi bi-table text-primary"></i>
            البيانات
            <span class="mod-toolbar-count">{{ number_format($records->total()) }}</span>
            <span class="text-muted fw-normal small d-none d-md-inline">· اضغط على صف لعرض التفاصيل</span>
        </div>
        <div class="mod-toolbar-actions position-relative">
            {{-- Column Manager --}}
            <button type="button" class="mod-icon-btn" id="colManagerBtn" title="إدارة الأعمدة">
                <i class="bi bi-layout-three-columns"></i>
                الأعمدة
            </button>
            <div class="col-manager-dropdown" id="colManagerDropdown">
                <div class="small fw-bold text-muted mb-2 px-2">اختر الأعمدة المرئية</div>
                <div id="colManagerList"></div>
            </div>

            {{-- Density toggle --}}
            <button type="button" class="mod-icon-btn" id="densityBtn" title="كثافة العرض">
                <i class="bi bi-list-ul"></i>
                كثافة
            </button>

            {{-- Fullscreen --}}
            <button type="button" class="mod-icon-btn" id="fullscreenBtn" title="ملء الشاشة">
                <i class="bi bi-fullscreen"></i>
            </button>
        </div>
    </div>

    {{-- ============================================================
         DATA TABLE
         ============================================================ --}}
    <div class="mod-table-wrapper">
        <div class="table-responsive" id="tableWrapper">
            <table class="mod-data-table" id="dataTable">
                <thead>
                    <tr>
                        <th class="col-check">
                            <input type="checkbox" id="selectAll" class="form-check-input">
                        </th>
                        <th class="col-id">#</th>
                        @foreach($headers ?? [] as $h)
                            <th class="text-nowrap sortable col-{{ $h }}" data-col="{{ $h }}">
                                <span class="sort-icon"><i class="bi bi-arrow-down-up"></i></span>
                                {{ str_replace('_', ' ', $h) }}
                            </th>
                        @endforeach
                        <th class="col-actions">تفاصيل</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($records as $record)
                    <tr class="record-row" data-id="{{ $record->id }}"
                        onclick="window.location='{{ route('admin.excel-modules.records.show', [$module, $record]) }}'">
                        <td class="col-check" onclick="event.stopPropagation()">
                            <input type="checkbox" class="form-check-input row-checkbox" value="{{ $record->id }}">
                        </td>
                        <td class="col-id">{{ $record->id }}</td>
                        @foreach($headers ?? [] as $h)
                            <td class="col-{{ $h }} text-nowrap" title="">
                                @php $val = $record->data[$h] ?? null; @endphp
                                @if(is_null($val) || $val === '')
                                    <span class="cell-empty">—</span>
                                @elseif(is_numeric($val))
                                    <span class="cell-number">{{ $val }}</span>
                                @else
                                    {{ \Illuminate\Support\Str::limit((string)$val, 60) }}
                                @endif
                            </td>
                        @endforeach
                        <td class="col-actions" onclick="event.stopPropagation()">
                            <a href="{{ route('admin.excel-modules.records.show', [$module, $record]) }}"
                               class="mod-icon-btn" style="padding:.3rem .5rem;">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers ?? []) + 3 }}">
                            <div class="mod-empty">
                                <i class="bi bi-inbox"></i>
                                <p class="mb-2">لا توجد سجلات</p>
                                <a href="{{ route('admin.excel-modules.create') }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-upload me-1"></i> استورد ملفاً
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ============================================================
         PAGINATION
         ============================================================ --}}
    @if($records->hasPages())
        <div class="mod-pagination">
            <div class="text-muted small">
                عرض {{ $records->firstItem() }}–{{ $records->lastItem() }} من {{ number_format($records->total()) }}
            </div>
            <div>
                {{ $records->withQueryString()->links() }}
            </div>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const STORAGE_KEY = 'mod-show-columns-{{ $module->code }}';
    const DENSITY_KEY = 'mod-show-density-{{ $module->code }}';

    const table = document.getElementById('dataTable');
    const wrapper = document.getElementById('tableWrapper');
    const colManagerBtn = document.getElementById('colManagerBtn');
    const colManagerDropdown = document.getElementById('colManagerDropdown');
    const colManagerList = document.getElementById('colManagerList');
    const densityBtn = document.getElementById('densityBtn');
    const fullscreenBtn = document.getElementById('fullscreenBtn');
    const selectAll = document.getElementById('selectAll');

    if (!table) return;

    const allHeaders = [...table.querySelectorAll('thead th[data-col]')]
        .map(th => th.dataset.col);

    // ==================== COLUMN MANAGER ====================
    function loadHiddenCols() {
        try { return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'); }
        catch { return []; }
    }
    function saveHiddenCols(cols) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(cols));
    }

    function buildColumnManager() {
        const hidden = loadHiddenCols();
        colManagerList.innerHTML = allHeaders.map(h => `
            <label>
                <input type="checkbox" data-col="${h}" ${hidden.includes(h) ? '' : 'checked'}>
                <span>${h.replace(/_/g, ' ')}</span>
            </label>
        `).join('');

        colManagerList.querySelectorAll('input[type="checkbox"]').forEach(cb => {
            cb.addEventListener('change', function () {
                const hiddenNow = [...colManagerList.querySelectorAll('input:not(:checked)')]
                    .map(x => x.dataset.col);
                saveHiddenCols(hiddenNow);
                applyColumnVisibility(hiddenNow);
            });
        });
    }

    function applyColumnVisibility(hidden) {
        allHeaders.forEach(h => {
            const visible = !hidden.includes(h);
            table.querySelectorAll(`.col-${CSS.escape(h)}`).forEach(el => {
                el.style.display = visible ? '' : 'none';
            });
        });
    }

    buildColumnManager();
    applyColumnVisibility(loadHiddenCols());

    colManagerBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        colManagerDropdown.classList.toggle('show');
    });
    document.addEventListener('click', function (e) {
        if (!colManagerDropdown.contains(e.target) && e.target !== colManagerBtn) {
            colManagerDropdown.classList.remove('show');
        }
    });

    // ==================== DENSITY TOGGLE ====================
    function applyDensity(mode) {
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(r => {
            r.style.padding = mode === 'compact' ? '.35rem .5rem' : '';
        });
        table.querySelectorAll('tbody td').forEach(td => {
            td.style.padding = mode === 'compact' ? '.35rem .5rem' : '.7rem .75rem';
            td.style.fontSize = mode === 'compact' ? '.75rem' : '.82rem';
        });
        localStorage.setItem(DENSITY_KEY, mode);
    }
    let currentDensity = localStorage.getItem(DENSITY_KEY) || 'normal';
    applyDensity(currentDensity);

    densityBtn.addEventListener('click', function () {
        currentDensity = currentDensity === 'compact' ? 'normal' : 'compact';
        applyDensity(currentDensity);
        densityBtn.classList.toggle('active', currentDensity === 'compact');
    });
    if (currentDensity === 'compact') densityBtn.classList.add('active');

    // ==================== FULLSCREEN ====================
    fullscreenBtn.addEventListener('click', function () {
        if (!document.fullscreenElement) {
            wrapper.requestFullscreen().catch(() => {});
            fullscreenBtn.querySelector('i').className = 'bi bi-fullscreen-exit';
        } else {
            document.exitFullscreen();
            fullscreenBtn.querySelector('i').className = 'bi bi-fullscreen';
        }
    });

    // ==================== SELECT ALL ====================
    if (selectAll) {
        selectAll.addEventListener('change', function () {
            const checked = this.checked;
            table.querySelectorAll('.row-checkbox').forEach(cb => {
                cb.checked = checked;
                cb.closest('tr').classList.toggle('selected', checked);
            });
        });
    }

    table.querySelectorAll('.row-checkbox').forEach(cb => {
        cb.addEventListener('change', function () {
            this.closest('tr').classList.toggle('selected', this.checked);
        });
    });

    // ==================== SORT (client-side per page) ====================
    let sortState = { col: null, dir: 'asc' };
    table.querySelectorAll('thead th.sortable').forEach(th => {
        th.addEventListener('click', function (e) {
            if (e.target.closest('.col-check')) return;
            const col = this.dataset.col;
            if (!col) return;

            sortState.dir = (sortState.col === col && sortState.dir === 'asc') ? 'desc' : 'asc';
            sortState.col = col;

            // Update sort icons
            table.querySelectorAll('thead th.sortable').forEach(t => {
                t.classList.remove('sorted-asc', 'sorted-desc');
                t.querySelector('.sort-icon i').className = 'bi bi-arrow-down-up';
            });
            this.classList.add(sortState.dir === 'asc' ? 'sorted-asc' : 'sorted-desc');
            this.querySelector('.sort-icon i').className = sortState.dir === 'asc' ? 'bi bi-arrow-up' : 'bi bi-arrow-down';

            // Sort rows client-side
            const tbody = table.querySelector('tbody');
            const rows = [...tbody.querySelectorAll('tr.record-row')];
            const colIndex = [...table.querySelectorAll('thead th')].findIndex(t => t.dataset.col === col);

            rows.sort((a, b) => {
                const av = a.children[colIndex]?.textContent.trim() || '';
                const bv = b.children[colIndex]?.textContent.trim() || '';
                const an = parseFloat(av.replace(/,/g, ''));
                const bn = parseFloat(bv.replace(/,/g, ''));
                if (!isNaN(an) && !isNaN(bn)) return sortState.dir === 'asc' ? an - bn : bn - an;
                return sortState.dir === 'asc' ? av.localeCompare(bv, 'ar') : bv.localeCompare(av, 'ar');
            });

            rows.forEach(r => tbody.appendChild(r));
        });
    });

    // ==================== CTRL+F SHORTCUT ====================
    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
            const search = document.querySelector('input[name="q"]');
            if (search) { e.preventDefault(); search.focus(); search.select(); }
        }
    });
});
</script>
@endpush