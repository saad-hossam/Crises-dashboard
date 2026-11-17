@extends('layouts.admin')

@section('title', $config['name_ar'])
@section('page_title', $config['name_ar'])

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.excel-modules.index') }}">موديولات Excel</a></li>
    <li class="breadcrumb-item active">{{ $config['name_ar'] }}</li>
@endsection

@php
    $rawColumns = $config['columns'] ?? [];
    $labelMap = [];
    foreach ($rawColumns as $k => $v) {
        if (is_string($k) && is_string($v)) {
            $labelMap[$v] = $k;
        } elseif (is_string($v)) {
            $labelMap[$v] = str_replace('_', ' ', $v);
        }
    }
    $colLabel = function (string $col) use ($labelMap): string {
        return $labelMap[$col] ?? str_replace('_', ' ', $col);
    };
@endphp



@section('content')
    <div class="tm-page" dir="rtl">

        {{-- ============================================================
         HERO
         ============================================================ --}}
        <div class="tm-hero">
            <div class="tm-hero-content">
                <span class="tm-hero-kicker">
                    <i class="bi bi-table"></i>
                    {{ $moduleCode }}
                </span>
                <h1 class="tm-hero-title">{{ $config['name_ar'] }}</h1>
                <div class="tm-hero-meta">
                    <span><i class="bi bi-database"></i> {{ number_format($records->total()) }} سجل</span>
                    @if ($records->total() > 0)
                        <span><i class="bi bi-list-ol"></i> {{ $records->firstItem() }}–{{ $records->lastItem() }}</span>
                    @endif
                    <span><i class="bi bi-columns-gap"></i> {{ count($columns ?? []) }} عمود</span>
                    <span><i class="bi bi-table"></i> {{ $config['table'] }}</span>
                </div>
                <div class="tm-hero-actions">
                   
                    <a href="{{ route('admin.excel-modules.create') }}" class="tm-hero-btn tm-hero-btn-primary">
                        <i class="bi bi-upload"></i> استيراد
                    </a>
                    <a href="{{ route('admin.excel-modules.index') }}" class="tm-hero-btn">
                        <i class="bi bi-grid"></i> الموديولات
                    </a>
                </div>
            </div>
        </div>

        {{-- ============================================================
         STATS BAR
         ============================================================ --}}
        <div class="tm-stats">
            <div class="tm-stat">
                <div class="tm-stat-icon" style="background:#0d6efd;">
                    <i class="bi bi-database-fill"></i>
                </div>
                <div>
                    <div class="tm-stat-label">إجمالي السجلات</div>
                    <div class="tm-stat-value">{{ number_format($records->total()) }}</div>
                </div>
            </div>
            <div class="tm-stat">
                <div class="tm-stat-icon" style="background:#10b981;">
                    <i class="bi bi-funnel-fill"></i>
                </div>
                <div>
                    <div class="tm-stat-label">معروضة</div>
                    <div class="tm-stat-value">{{ number_format($records->count()) }}</div>
                </div>
            </div>
            <div class="tm-stat">
                <div class="tm-stat-icon" style="background:#f59e0b;">
                    <i class="bi bi-columns-gap"></i>
                </div>
                <div>
                    <div class="tm-stat-label">الأعمدة</div>
                    <div class="tm-stat-value">{{ count($columns ?? []) }}</div>
                </div>
            </div>
            <div class="tm-stat">
                <div class="tm-stat-icon" style="background:#8b5cf6;">
                    <i class="bi bi-file-earmark-bar-graph-fill"></i>
                </div>
                <div>
                    <div class="tm-stat-label">الصفحة</div>
                    <div class="tm-stat-value">{{ $records->currentPage() }} / {{ $records->lastPage() }}</div>
                </div>
            </div>
        </div>

        {{-- ============================================================
         CHARTS
         ============================================================ --}}
        @php
            $charts = $moduleCharts ?? [];
            while (count($charts) < 4) {
                $charts[] = [
                    'id' => 'modChart' . count($charts),
                    'type' => 'bar',
                    'title' => '—',
                    'labels' => [],
                    'data' => [],
                ];
            }
            $chartIcons = ['bi-pie-chart-fill', 'bi-bar-chart-fill', 'bi-donut', 'bi-graph-up'];
        @endphp
        <div class="row g-2 g-md-3 mb-3">
            @foreach (array_slice($charts, 0, 4) as $i => $ch)
                <div class="col-6 col-md-6 col-xl-3">
                    <div class="tm-chart-card">
                        <div class="tm-chart-title">
                            <i class="bi {{ $chartIcons[$i % 4] }}"></i>
                            <span>{{ $ch['title'] ?? '—' }}</span>
                        </div>
                        <div class="tm-chart-box">
                            <canvas id="{{ $ch['id'] }}"></canvas>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ============================================================
         FILTERS
         ============================================================ --}}
        <div class="tm-filter-panel">
            <div class="tm-filter-title">
                <i class="bi bi-funnel"></i>
                تصفية البيانات
            </div>
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <label>بحث عام</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                            placeholder="ابحث في الأعمدة...">
                    </div>
                </div>
                <div class="col-12 col-md-3 tm-unit-col">
                    <label>اسم الوحدة</label>
                    <input type="text" name="unit_name" id="tm-unit-name" value="{{ request('unit_name') }}"
                        class="form-control" placeholder="مثال: صقر" autocomplete="off">
                    <div id="tm-unit-dropdown" class="tm-unit-dropdown"></div>
                    <input type="hidden" id="tm-unit-names-data" value="{{ ($unitNames ?? collect())->toJson() }}">
                </div>
                <div class="col-6 col-md-2">
                    <label>لكل صفحة</label>
                    <select name="per_page" class="form-select">
                        @foreach ([25, 50, 100, 200] as $n)
                            <option value="{{ $n }}" @selected(request('per_page', 50) == $n)>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3 d-flex gap-2">
                    <button class="btn btn-primary flex-fill" type="submit">
                        <i class="bi bi-funnel me-1"></i> تطبيق
                    </button>
                    <a href="{{ route('admin.excel-modules.table', $moduleCode) }}" class="btn btn-outline-secondary"
                        title="إعادة">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>

            @if (request('q') || request('unit_name'))
                <div class="mt-3 d-flex flex-wrap gap-2 align-items-center small">
                    <span class="text-muted">فلاتر نشطة:</span>
                    @if (request('q'))
                        <span class="badge bg-light text-dark border">بحث: {{ request('q') }}</span>
                    @endif
                    @if (request('unit_name'))
                        <span class="badge bg-light text-dark border">وحدة: {{ request('unit_name') }}</span>
                    @endif
                </div>
            @endif
        </div>

        {{-- ============================================================
         TABLE TOOLBAR
         ============================================================ --}}
        <div class="tm-toolbar">
            <div class="tm-toolbar-info">
                <i class="bi bi-table text-primary"></i>
                <span>البيانات</span>
                <span class="tm-toolbar-count">{{ number_format($records->total()) }}</span>
            </div>
            <div class="tm-toolbar-actions">
                <button type="button" class="tm-icon-btn" id="tmColManagerBtn" title="إدارة الأعمدة">
                    <i class="bi bi-layout-three-columns"></i>
                    <span class="d-none d-sm-inline">الأعمدة</span>
                </button>
                <div class="tm-col-dropdown" id="tmColManagerDropdown">
                    <div class="small fw-bold text-muted mb-2 px-2">اختر الأعمدة المرئية</div>
                    <div id="tmColManagerList"></div>
                </div>

                <button type="button" class="tm-icon-btn" id="tmDensityBtn" title="كثافة العرض">
                    <i class="bi bi-list-ul"></i>
                    <span class="d-none d-sm-inline">كثافة</span>
                </button>

                <button type="button" class="tm-icon-btn" id="tmFullscreenBtn" title="ملء الشاشة">
                    <i class="bi bi-fullscreen"></i>
                </button>
            </div>
        </div>

        {{-- ============================================================
         TABLE
         ============================================================ --}}
        @php
            $allCols = array_values($columns ?? []);
            $visibleCols = array_slice($allCols, 0, 8);
        @endphp

        <div class="tm-table-wrapper">
            {{-- Desktop Table (md and up) --}}
            <div class="d-none d-md-block">
                <div class="tm-table-scroll" id="tmTableWrapper">
                    <table class="tm-data-table" id="tmDataTable">
                        <thead>
                            <tr>
                                <th class="col-check">
                                    <input type="checkbox" id="tmSelectAll" class="form-check-input">
                                </th>
                                <th class="col-id">#</th>
                                @foreach ($allCols as $i => $col)
                                    <th class="sortable col-{{ $col }}" data-col="{{ $col }}"
                                        data-visible="{{ $i < 8 ? '1' : '0' }}">
                                        <span class="sort-icon"><i class="bi bi-arrow-down-up"></i></span>
                                        {{ $colLabel($col) }}
                                    </th>
                                @endforeach
                                <th class="col-actions">تفاصيل</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $record)
                                @php $recordUrl = route('admin.excel-modules.table.record', [$moduleCode, $record->id]); @endphp
                                <tr class="record-row" data-id="{{ $record->id }}"
                                    onclick="window.location='{{ $recordUrl }}'">
                                    <td class="col-check" onclick="event.stopPropagation()">
                                        <input type="checkbox" class="form-check-input row-checkbox"
                                            value="{{ $record->id }}">
                                    </td>
                                    <td class="col-id">{{ $record->id }}</td>
                                    @foreach ($allCols as $i => $col)
                                        @php
                                            $value = (string) ($record->{$col} ?? '');
                                            $isVisible = $i < 8;
                                        @endphp
                                        <td class="col-{{ $col }}" data-visible="{{ $isVisible ? '1' : '0' }}"
                                            title="{{ $colLabel($col) }}: {{ $value ?: '—' }}">
                                            @if ($value === '' || $value === '0')
                                                <span class="cell-empty">—</span>
                                            @elseif(is_numeric($value))
                                                <span class="cell-number">{{ $value }}</span>
                                            @else
                                                {{ \Illuminate\Support\Str::limit($value, 40) }}
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="col-actions" onclick="event.stopPropagation()">
                                        <a href="{{ $recordUrl }}" class="tm-icon-btn" style="padding:.3rem .5rem;">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($allCols) + 3 }}">
                                        <div class="tm-empty">
                                            <i class="bi bi-inbox"></i>
                                            <p class="mb-2">لا توجد سجلات</p>
                                            <a href="{{ route('admin.excel-modules.create') }}"
                                                class="btn btn-sm btn-primary">
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

            {{-- Mobile Cards (sm and below) --}}
            <div class="d-md-none tm-mobile-list">
                @forelse($records as $record)
                    @php $recordUrl = route('admin.excel-modules.table.record', [$moduleCode, $record->id]); @endphp
                    <a href="{{ $recordUrl }}" class="tm-mobile-item">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="tm-mobile-id">#{{ $record->id }}</span>
                            <i class="bi bi-chevron-left text-muted"></i>
                        </div>
                        @foreach (array_slice($visibleCols, 0, 4) as $col)
                            @php $value = (string) ($record->{$col} ?? '—'); @endphp
                            <div class="tm-mobile-field">
                                <span class="tm-mobile-label">{{ $colLabel($col) }}</span>
                                <span class="tm-mobile-value">{{ \Illuminate\Support\Str::limit($value, 48) }}</span>
                            </div>
                        @endforeach
                    </a>
                @empty
                    <div class="tm-empty">
                        <i class="bi bi-inbox"></i>
                        <p class="mb-3">لا توجد بيانات</p>
                        <a href="{{ route('admin.excel-modules.create') }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-upload me-1"></i> استورد ملفاً
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ============================================================
         PAGINATION
         ============================================================ --}}
        @if ($records->total() > 0)
            <div class="tm-pagination">
                <div class="text-muted small">
                    عرض {{ $records->firstItem() }}–{{ $records->lastItem() }}
                    من {{ number_format($records->total()) }}
                </div>
                <div>
                    {{ $records->withQueryString()->links() }}
                </div>
            </div>
        @endif

    </div>
@endsection





@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ==================== CHART.JS ====================
            if (typeof Chart === 'undefined') {
                console.error('Chart.js missing');
                return;
            }
            const isRtl = document.documentElement.dir === 'rtl';

            // ==================== ✅ ألوان فريدة — Golden Angle ====================
            // توزّع الألوان على عجلة HSL بزاوية 137.508° (توزيع فيبوناتشي الذهبي)
            // يضمن أن كل لون مختلف بصريًا حتى مع 50+ عنصرًا
            function generateDistinctColors(count) {
                const colors = [];
                const goldenAngle = 137.508;
                for (let i = 0; i < count; i++) {
                    const hue = (i * goldenAngle) % 360;
                    const saturation = 65 + (i % 3) * 8; // 65% – 81%
                    const lightness = 50 + (i % 4) * 5; // 50% – 65%
                    colors.push(`hsl(${hue.toFixed(1)}, ${saturation}%, ${lightness}%)`);
                }
                return colors;
            }

            // نسخة أفتح (للحدود)
            function lightenColor(hsl, amount = 20) {
                const m = hsl.match(/hsl\(([\d.]+),\s*([\d.]+)%,\s*([\d.]+)%\)/);
                if (!m) return hsl;
                const [, h, s, l] = m;
                const nl = Math.min(90, parseFloat(l) + amount);
                return `hsl(${h}, ${s}%, ${nl}%)`;
            }

            Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;
            Chart.defaults.font.size = 11;
            Chart.defaults.color = '#64748b';

            function draw(id, type, labels, data) {
                const el = document.getElementById(id);
                if (!el) return;
                const labs = labels || [];
                const vals = data || [];
                if (!labs.length || !vals.length) {
                    const box = el.parentElement;
                    if (box) box.innerHTML = '<div class="empty-chart">لا توجد بيانات كافية</div>';
                    return;
                }
                const isDonut = type === 'doughnut' || type === 'pie';
                const isLine = type === 'line';

                // ✅ ألوان فريدة لكل عنصر
                const uniqueColors = generateDistinctColors(Math.max(labs.length, 1));
                const borderColors = uniqueColors.map(c => lightenColor(c, 20));

                let bgColors, borderColor, borderWidth, borderRadius, tension;

                if (isDonut) {
                    bgColors = uniqueColors;
                    borderColor = '#fff';
                    borderWidth = 2;
                    borderRadius = 0;
                    tension = 0;
                } else if (isLine) {
                    bgColors = uniqueColors[0];
                    borderColor = uniqueColors[0];
                    borderWidth = 2;
                    borderRadius = 0;
                    tension = 0.35;
                } else {
                    bgColors = uniqueColors;
                    borderColor = borderColors;
                    borderWidth = 1;
                    borderRadius = 6;
                    tension = 0;
                }

                new Chart(el, {
                    type: type,
                    data: {
                        labels: labs,
                        datasets: [{
                            data: vals,
                            backgroundColor: bgColors,
                            borderColor: borderColor,
                            borderWidth: borderWidth,
                            borderRadius: borderRadius,
                            tension: tension,
                            cutout: type === 'doughnut' ? '58%' : undefined
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: isDonut,
                                position: 'bottom',
                                rtl: isRtl,
                                labels: {
                                    boxWidth: 12,
                                    boxHeight: 12,
                                    font: {
                                        size: 10
                                    },
                                    padding: 6,
                                    usePointStyle: true,
                                }
                            },
                            tooltip: {
                                rtl: isRtl
                            }
                        },
                        scales: isDonut ? {} : {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 9
                                    },
                                    maxRotation: 35
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    font: {
                                        size: 10
                                    }
                                },
                                grid: {
                                    color: 'rgba(15,23,42,.06)'
                                }
                            }
                        }
                    }
                });
            }

            const moduleCharts = @json($moduleCharts ?? []);
            moduleCharts.forEach(function(ch) {
                draw(ch.id, ch.type || 'bar', ch.labels || [], ch.data || []);
            });

            // ==================== UNIT AUTOCOMPLETE ====================
            (function() {
                const input = document.getElementById('tm-unit-name');
                const dropdown = document.getElementById('tm-unit-dropdown');
                const dataEl = document.getElementById('tm-unit-names-data');
                if (!input || !dropdown || !dataEl) return;

                let names = [];
                try {
                    names = JSON.parse(dataEl.value || '[]');
                } catch (e) {
                    names = [];
                }
                let activeIndex = -1;

                function escapeHtml(s) {
                    return String(s).replace(/[&<>"']/g, c => ({
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#39;'
                    } [c]));
                }

                function highlight(name, query) {
                    if (!query) return escapeHtml(name);
                    const i = name.toLowerCase().indexOf(query.toLowerCase());
                    if (i === -1) return escapeHtml(name);
                    return escapeHtml(name.slice(0, i)) +
                        '<mark>' + escapeHtml(name.slice(i, i + query.length)) + '</mark>' +
                        escapeHtml(name.slice(i + query.length));
                }

                function render(query) {
                    const q = (query || '').trim().toLowerCase();
                    const matches = names.filter(n => !q || n.toLowerCase().includes(q)).slice(0, 50);
                    if (!matches.length) {
                        dropdown.innerHTML = '<div class="tm-empty-item">لا توجد وحدات مطابقة</div>';
                    } else {
                        dropdown.innerHTML = matches.map((n, idx) =>
                            `<button type="button" class="dropdown-item" data-value="${escapeHtml(n)}" data-index="${idx}">
                        ${highlight(n, q)}
                    </button>`
                        ).join('');
                    }
                    activeIndex = -1;
                    dropdown.classList.add('show');
                }

                function hide() {
                    dropdown.classList.remove('show');
                    activeIndex = -1;
                }

                function setActive(idx) {
                    const items = dropdown.querySelectorAll('.dropdown-item');
                    items.forEach(el => el.classList.remove('active'));
                    if (idx >= 0 && idx < items.length) {
                        items[idx].classList.add('active');
                        items[idx].scrollIntoView({
                            block: 'nearest'
                        });
                        activeIndex = idx;
                    }
                }

                input.addEventListener('focus', () => render(input.value));
                input.addEventListener('input', () => render(input.value));
                input.addEventListener('keydown', (e) => {
                    const items = dropdown.querySelectorAll('.dropdown-item');
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        if (!dropdown.classList.contains('show')) render(input.value);
                        setActive(Math.min(activeIndex + 1, items.length - 1));
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        setActive(Math.max(activeIndex - 1, 0));
                    } else if (e.key === 'Enter') {
                        if (activeIndex >= 0 && items[activeIndex]) {
                            e.preventDefault();
                            input.value = items[activeIndex].dataset.value;
                            hide();
                        }
                    } else if (e.key === 'Escape') {
                        hide();
                    }
                });
                dropdown.addEventListener('mousedown', (e) => {
                    const item = e.target.closest('.dropdown-item');
                    if (!item) return;
                    e.preventDefault();
                    input.value = item.dataset.value;
                    hide();
                    input.focus();
                });
                input.addEventListener('blur', () => setTimeout(hide, 120));
                document.addEventListener('click', (e) => {
                    if (!e.target.closest('#tm-unit-name') && !e.target.closest('#tm-unit-dropdown'))
                        hide();
                });
            })();

            // ==================== COLUMN MANAGER ====================
            (function() {
                const STORAGE_KEY = 'tm-columns-{{ $moduleCode }}';
                const DENSITY_KEY = 'tm-density-{{ $moduleCode }}';
                const table = document.getElementById('tmDataTable');
                const colManagerBtn = document.getElementById('tmColManagerBtn');
                const colManagerDropdown = document.getElementById('tmColManagerDropdown');
                const colManagerList = document.getElementById('tmColManagerList');
                const densityBtn = document.getElementById('tmDensityBtn');
                const fullscreenBtn = document.getElementById('tmFullscreenBtn');
                const selectAll = document.getElementById('tmSelectAll');
                const wrapper = document.getElementById('tmTableWrapper');
                if (!table) return;

                const allHeaders = [...table.querySelectorAll('thead th[data-col]')].map(th => th.dataset.col);

                function loadHidden() {
                    try {
                        return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
                    } catch {
                        return [];
                    }
                }

                function saveHidden(cols) {
                    localStorage.setItem(STORAGE_KEY, JSON.stringify(cols));
                }

                function buildColManager() {
                    const hidden = loadHidden();
                    colManagerList.innerHTML = allHeaders.map(h => `
                <label>
                    <input type="checkbox" data-col="${h}" ${hidden.includes(h) ? '' : 'checked'}>
                    <span>${h.replace(/_/g, ' ')}</span>
                </label>
            `).join('');
                    colManagerList.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                        cb.addEventListener('change', function() {
                            const hiddenNow = [...colManagerList.querySelectorAll(
                                    'input:not(:checked)')]
                                .map(x => x.dataset.col);
                            saveHidden(hiddenNow);
                            applyColVisibility(hiddenNow);
                        });
                    });
                }

                function applyColVisibility(hidden) {
                    allHeaders.forEach(h => {
                        const visible = !hidden.includes(h);
                        table.querySelectorAll(`.col-${CSS.escape(h)}`).forEach(el => {
                            el.style.display = visible ? '' : 'none';
                        });
                    });
                }

                buildColManager();
                applyColVisibility(loadHidden());

                colManagerBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    colManagerDropdown.classList.toggle('show');
                });
                document.addEventListener('click', function(e) {
                    if (!colManagerDropdown.contains(e.target) && e.target !== colManagerBtn) {
                        colManagerDropdown.classList.remove('show');
                    }
                });

                function applyDensity(mode) {
                    table.querySelectorAll('tbody td').forEach(td => {
                        td.style.padding = mode === 'compact' ? '.35rem .5rem' : '.65rem';
                        td.style.fontSize = mode === 'compact' ? '.72rem' : '.8rem';
                    });
                    localStorage.setItem(DENSITY_KEY, mode);
                }
                let currentDensity = localStorage.getItem(DENSITY_KEY) || 'normal';
                applyDensity(currentDensity);
                densityBtn.classList.toggle('active', currentDensity === 'compact');

                densityBtn.addEventListener('click', function() {
                    currentDensity = currentDensity === 'compact' ? 'normal' : 'compact';
                    applyDensity(currentDensity);
                    densityBtn.classList.toggle('active', currentDensity === 'compact');
                });

                fullscreenBtn.addEventListener('click', function() {
                    if (!document.fullscreenElement) {
                        wrapper.requestFullscreen().catch(() => {});
                        fullscreenBtn.querySelector('i').className = 'bi bi-fullscreen-exit';
                    } else {
                        document.exitFullscreen();
                        fullscreenBtn.querySelector('i').className = 'bi bi-fullscreen';
                    }
                });

                if (selectAll) {
                    selectAll.addEventListener('change', function() {
                        const checked = this.checked;
                        table.querySelectorAll('.row-checkbox').forEach(cb => {
                            cb.checked = checked;
                            cb.closest('tr').classList.toggle('selected', checked);
                        });
                    });
                }
                table.querySelectorAll('.row-checkbox').forEach(cb => {
                    cb.addEventListener('change', function() {
                        this.closest('tr').classList.toggle('selected', this.checked);
                    });
                });

                let sortState = {
                    col: null,
                    dir: 'asc'
                };
                table.querySelectorAll('thead th.sortable').forEach(th => {
                    th.addEventListener('click', function(e) {
                        if (e.target.closest('.col-check')) return;
                        const col = this.dataset.col;
                        if (!col) return;
                        sortState.dir = (sortState.col === col && sortState.dir === 'asc') ?
                            'desc' : 'asc';
                        sortState.col = col;
                        table.querySelectorAll('thead th.sortable').forEach(t => {
                            t.classList.remove('sorted-asc', 'sorted-desc');
                            t.querySelector('.sort-icon i').className =
                                'bi bi-arrow-down-up';
                        });
                        this.classList.add(sortState.dir === 'asc' ? 'sorted-asc' :
                            'sorted-desc');
                        this.querySelector('.sort-icon i').className = sortState.dir === 'asc' ?
                            'bi bi-arrow-up' : 'bi bi-arrow-down';

                        const tbody = table.querySelector('tbody');
                        const rows = [...tbody.querySelectorAll('tr.record-row')];
                        const colIndex = [...table.querySelectorAll('thead th')].findIndex(t =>
                            t.dataset.col === col);
                        rows.sort((a, b) => {
                            const av = a.children[colIndex]?.textContent.trim() || '';
                            const bv = b.children[colIndex]?.textContent.trim() || '';
                            const an = parseFloat(av.replace(/,/g, ''));
                            const bn = parseFloat(bv.replace(/,/g, ''));
                            if (!isNaN(an) && !isNaN(bn)) return sortState.dir ===
                                'asc' ? an - bn : bn - an;
                            return sortState.dir === 'asc' ? av.localeCompare(bv,
                                'ar') : bv.localeCompare(av, 'ar');
                        });
                        rows.forEach(r => tbody.appendChild(r));
                    });
                });
            })();

            // ==================== CTRL+F SHORTCUT ====================
            document.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                    const search = document.querySelector('input[name="q"]');
                    if (search) {
                        e.preventDefault();
                        search.focus();
                        search.select();
                    }
                }
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        /* ============================================================
       TABLE MODULE VIEW — Modern Responsive (Zoom + Chart Safe)
       ============================================================ */

        /* ============ BASE ============ */
        .tm-page {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            max-width: 1600px;
            margin: 0 auto;
        }

        .tm-page * {
            min-width: 0;
        }

        /* ============ HERO ============ */
        .tm-hero {
            position: relative;
            border-radius: 1.35rem;
            padding: 1.75rem;
            margin-bottom: 1.25rem;
            background: linear-gradient(135deg, #E87524 0%, #CF5F15 55%, #17365D 100%);
            color: #fff;
            overflow: hidden;
            box-shadow: 0 14px 32px -10px rgba(232, 117, 36, 0.35);
        }

        .tm-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(circle at 15% 25%, rgba(255, 255, 255, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 85% 75%, rgba(255, 255, 255, 0.1) 0%, transparent 45%);
            pointer-events: none;
            z-index: 1;
        }

        .tm-hero-content {
            position: relative;
            z-index: 2;
        }

        .tm-hero-kicker {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            background: rgba(255, 255, 255, 0.18);
            padding: .3rem .75rem;
            border-radius: 50px;
            font-size: .72rem;
            font-weight: 600;
            margin-bottom: .6rem;
            border: 1px solid rgba(255, 255, 255, .2);
        }

        .tm-hero-title {
            font-size: clamp(1.1rem, 3vw, 1.6rem);
            font-weight: 800;
            margin: 0 0 .35rem;
            line-height: 1.3;
            word-break: break-word;
        }

        .tm-hero-meta {
            display: flex;
            flex-wrap: wrap;
            gap: .4rem .85rem;
            font-size: .78rem;
            color: rgba(255, 255, 255, .88);
        }

        .tm-hero-meta span {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            white-space: nowrap;
        }

        .tm-hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .4rem;
            margin-top: 1rem;
        }

        .tm-hero-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .35rem;
            padding: .55rem .9rem;
            border-radius: .6rem;
            font-size: .8rem;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid rgba(255, 255, 255, .3);
            background: rgba(255, 255, 255, .15);
            color: #fff;
            transition: all .2s;
            min-height: 38px;
            white-space: nowrap;
        }

        .tm-hero-btn:hover {
            background: rgba(255, 255, 255, .28);
            color: #fff;
            transform: translateY(-1px);
        }

        .tm-hero-btn-primary {
            background: #fff;
            color: #E87524;
            border-color: #fff;
        }

        .tm-hero-btn-primary:hover {
            background: #f8fafc;
            color: #E87524;
        }

        /* ============ STATS ============ */
        .tm-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: .65rem;
            margin-bottom: 1rem;
        }

        .tm-stat {
            background: #ffffff;
            border-radius: .85rem;
            padding: .8rem .95rem;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: .65rem;
            min-width: 0;
        }

        .tm-stat-icon {
            width: 36px;
            height: 36px;
            border-radius: .6rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: .95rem;
            flex-shrink: 0;
        }

        .tm-stat>div:last-child {
            min-width: 0;
        }

        .tm-stat-label {
            font-size: .68rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .3px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .tm-stat-value {
            font-size: 1.05rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
        }

        /* ============ CHARTS ============ */
        .tm-chart-card {
            background: #ffffff;
            border-radius: .9rem;
            border: 1px solid #e2e8f0;
            padding: 1rem;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .tm-chart-title {
            font-size: .8rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: .65rem;
            display: flex;
            align-items: center;
            gap: .35rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            position: relative;
            z-index: 1;
        }

        .tm-chart-title i {
            color: #E87524;
            flex-shrink: 0;
        }

        .tm-chart-box {
            position: relative;
            height: 180px;
            flex: 1;
            background: #ffffff;
        }

        .tm-chart-box canvas {
            background: #ffffff !important;
            display: block;
            width: 100% !important;
            height: 100% !important;
        }

        .tm-chart-box .empty-chart {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #94a3b8;
            font-size: .8rem;
            text-align: center;
            padding: 1rem;
        }

        /* ============ FILTERS ============ */
        .tm-filter-panel {
            background: #ffffff;
            border-radius: .9rem;
            border: 1px solid #e2e8f0;
            padding: 1rem;
            margin-bottom: 1rem;
            position: relative;
            z-index: 50;
        }

        .tm-filter-title {
            font-size: .72rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: .75rem;
            display: flex;
            align-items: center;
            gap: .4rem;
        }

        .tm-filter-panel label {
            font-size: .72rem;
            color: #64748b;
            font-weight: 600;
            margin-bottom: .3rem;
            display: block;
        }

        .tm-filter-panel .form-control,
        .tm-filter-panel .form-select {
            font-size: .85rem;
            border-radius: .5rem;
            border-color: #e2e8f0;
            padding: .5rem .75rem;
            min-height: 42px;
            background-color: #ffffff;
            color: #0f172a;
        }

        .tm-filter-panel .form-control:focus,
        .tm-filter-panel .form-select:focus {
            border-color: #E87524;
            box-shadow: 0 0 0 3px rgba(232, 117, 36, .15);
            background-color: #ffffff;
        }

        .tm-filter-panel .input-group-text {
            border-radius: .5rem 0 0 .5rem;
            border-color: #e2e8f0;
            background: #ffffff;
            color: #475569;
        }

        /* ============ TOOLBAR ============ */
        .tm-toolbar {
            background: #ffffff;
            border-radius: .9rem .9rem 0 0;
            border: 1px solid #e2e8f0;
            border-bottom: 0;
            padding: .75rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: .5rem;
        }

        .tm-toolbar-info {
            font-size: .82rem;
            font-weight: 600;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: .4rem;
            min-width: 0;
            flex-wrap: wrap;
        }

        .tm-toolbar-count {
            background: #dbeafe;
            color: #1e40af;
            padding: .1rem .45rem;
            border-radius: 50px;
            font-size: .7rem;
            font-weight: 700;
        }

        .tm-toolbar-actions {
            display: flex;
            gap: .3rem;
            flex-wrap: wrap;
            position: relative;
        }

        .tm-icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .25rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569;
            padding: .45rem .65rem;
            border-radius: .5rem;
            font-size: .75rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .15s;
            min-height: 36px;
        }

        .tm-icon-btn:hover {
            background: #fff4eb;
            color: #E87524;
            border-color: #fed7aa;
        }

        .tm-icon-btn.active {
            background: #E87524;
            color: #fff;
            border-color: #E87524;
        }

        /* ============ COLUMN MANAGER ============ */
        .tm-col-dropdown {
            position: absolute;
            top: calc(100% + .3rem);
            inset-inline-start: 0;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: .65rem;
            box-shadow: 0 10px 30px rgba(15, 23, 42, .15);
            padding: .55rem;
            min-width: 220px;
            max-width: calc(100vw - 2rem);
            max-height: 60vh;
            overflow-y: auto;
            z-index: 100;
            display: none;
        }

        .tm-col-dropdown.show {
            display: block;
        }

        .tm-col-dropdown label {
            display: flex;
            align-items: center;
            gap: .5rem;
            padding: .45rem .5rem;
            border-radius: .4rem;
            font-size: .8rem;
            cursor: pointer;
            margin: 0;
        }

        .tm-col-dropdown label:hover {
            background: #f8fafc;
        }

        .tm-col-dropdown label input {
            margin: 0;
            flex-shrink: 0;
        }

        /* ============ TABLE ============ */
        .tm-table-wrapper {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 0 0 .9rem .9rem;
            overflow: hidden;
        }

        .tm-table-scroll {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            background: #ffffff;
        }

        .tm-data-table {
            width: 100%;
            min-width: 720px;
            margin: 0;
            border-collapse: separate;
            border-spacing: 0;
            background-color: #ffffff;
        }

        .tm-data-table thead th {
            background-color: #fafbfc;
            font-size: .7rem;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: .3px;
            padding: .75rem .65rem;
            border-bottom: 2px solid #e2e8f0;
            white-space: nowrap;
            user-select: none;
        }

        .tm-data-table thead th.sortable {
            cursor: pointer;
        }

        .tm-data-table thead th.sortable:hover {
            background-color: #fff4eb;
            color: #E87524;
        }

        .tm-data-table thead th .sort-icon {
            display: inline-block;
            margin-inline-end: .2rem;
            opacity: .35;
            font-size: .65rem;
        }

        .tm-data-table thead th.sorted-asc .sort-icon,
        .tm-data-table thead th.sorted-desc .sort-icon {
            opacity: 1;
            color: #E87524;
        }

        .tm-data-table tbody {
            background-color: #ffffff;
        }

        .tm-data-table tbody td {
            background-color: #ffffff;
            padding: .65rem;
            font-size: .8rem;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            max-width: 220px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .tm-data-table tbody tr {
            cursor: pointer;
            transition: background .1s;
            background-color: #ffffff;
        }

        .tm-data-table tbody tr:hover {
            background-color: #f8fafc;
        }

        .tm-data-table tbody tr:hover td {
            background-color: #f8fafc;
        }

        .tm-data-table tbody tr.selected {
            background-color: #eff6ff;
        }

        .tm-data-table tbody tr.selected td {
            background-color: #eff6ff;
        }

        .tm-data-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .tm-data-table .col-id {
            width: 55px;
            text-align: center;
            color: #94a3b8;
            font-size: .72rem;
        }

        .tm-data-table .col-check {
            width: 38px;
            text-align: center;
        }

        .tm-data-table .col-actions {
            width: 60px;
            text-align: center;
        }

        .tm-data-table .cell-empty {
            color: #cbd5e1;
            font-style: italic;
            font-size: .75rem;
        }

        .tm-data-table .cell-number {
            font-family: 'Courier New', monospace;
            color: #0f172a;
            font-weight: 600;
        }

        /* ============ PAGINATION ============ */
        .tm-pagination {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-top: 0;
            border-radius: 0 0 .9rem .9rem;
            padding: .75rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: .5rem;
        }

        .tm-pagination .pagination {
            margin: 0;
            flex-wrap: wrap;
            gap: .15rem;
        }

        .tm-pagination .page-link {
            font-size: .8rem;
            padding: .35rem .6rem;
            border-radius: .4rem;
            color: #E87524;
            min-width: 34px;
            text-align: center;
            background-color: #ffffff;
        }

        .tm-pagination .page-item.active .page-link {
            background-color: #E87524;
            border-color: #E87524;
            color: #fff;
        }

        /* ============ MOBILE CARDS ============ */
        .tm-mobile-list {
            padding: .65rem;
            background: #ffffff;
        }

        .tm-mobile-item {
            display: block;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: .8rem;
            padding: .85rem;
            margin-bottom: .55rem;
            color: inherit;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(15, 23, 42, .04);
            transition: all .15s;
        }

        .tm-mobile-item:hover,
        .tm-mobile-item:active {
            color: inherit;
            border-color: #E87524;
        }

        .tm-mobile-id {
            font-size: .72rem;
            font-weight: 700;
            color: #E87524;
            background: #fff4eb;
            padding: .15rem .45rem;
            border-radius: 5px;
        }

        .tm-mobile-field {
            display: grid;
            grid-template-columns: 42% 1fr;
            gap: .35rem;
            padding: .35rem 0;
            font-size: .78rem;
            border-bottom: 1px dashed #f1f5f9;
        }

        .tm-mobile-field:last-child {
            border-bottom: 0;
        }

        .tm-mobile-label {
            color: #64748b;
            font-weight: 600;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .tm-mobile-value {
            color: #0f172a;
            font-weight: 600;
            text-align: end;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* ============ EMPTY ============ */
        .tm-empty {
            padding: 3rem 1rem;
            text-align: center;
            color: #94a3b8;
            background: #ffffff;
        }

        .tm-empty i {
            font-size: 2.5rem;
            opacity: .4;
            margin-bottom: .75rem;
            display: block;
        }

        /* ============ UNIT AUTOCOMPLETE ============ */
        .tm-unit-col {
            position: relative;
        }

        .tm-unit-dropdown {
            position: absolute;
            inset-inline: 0;
            top: 100%;
            z-index: 1055;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: .5rem;
            box-shadow: 0 10px 25px rgba(15, 23, 42, .15);
            max-height: 260px;
            overflow-y: auto;
            display: none;
            margin-top: .2rem;
        }

        .tm-unit-dropdown.show {
            display: block;
        }

        .tm-unit-dropdown .dropdown-item {
            cursor: pointer;
            white-space: normal;
            word-break: break-word;
            padding: .55rem .75rem;
            font-size: .82rem;
            border: 0;
            background: transparent;
            width: 100%;
            text-align: start;
            color: #0f172a;
        }

        .tm-unit-dropdown .dropdown-item:hover,
        .tm-unit-dropdown .dropdown-item.active {
            background: #fff4eb;
            color: #E87524;
        }

        .tm-unit-dropdown .dropdown-item mark {
            background: #fef3c7;
            padding: 0 1px;
            border-radius: 2px;
        }

        .tm-unit-dropdown .tm-empty-item {
            padding: .55rem .75rem;
            color: #94a3b8;
            font-size: .8rem;
        }

        /* ============ RESPONSIVE ============ */
        @media (max-width: 991.98px) {
            .tm-data-table {
                min-width: 640px;
            }

            .tm-data-table tbody td {
                max-width: 160px;
            }
        }

        @media (max-width: 767.98px) {
            .tm-hero {
                padding: 1.15rem;
                border-radius: 1rem;
            }

            .tm-hero-title {
                font-size: 1.1rem;
            }

            .tm-hero-meta {
                font-size: .72rem;
                gap: .35rem .65rem;
            }

            .tm-hero-actions {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                width: 100%;
                gap: .4rem;
            }

            .tm-hero-btn {
                padding: .5rem .6rem;
                font-size: .72rem;
                min-height: 40px;
            }

            .tm-stats {
                grid-template-columns: repeat(2, 1fr);
                gap: .5rem;
            }

            .tm-stat {
                padding: .65rem .75rem;
                gap: .5rem;
            }

            .tm-stat-icon {
                width: 32px;
                height: 32px;
                font-size: .85rem;
            }

            .tm-stat-value {
                font-size: .95rem;
            }

            .tm-stat-label {
                font-size: .62rem;
            }

            .tm-chart-box {
                height: 150px;
            }

            .tm-chart-card {
                padding: .85rem;
            }

            .tm-chart-title {
                font-size: .78rem;
            }

            .tm-filter-panel {
                padding: .85rem;
            }

            .tm-filter-panel .row {
                --bs-gutter-y: .5rem;
            }

            .tm-filter-panel .row>[class*="col-"] {
                width: 100%;
            }

            .tm-filter-panel label {
                font-size: .7rem;
            }

            .tm-filter-panel .form-control,
            .tm-filter-panel .form-select {
                font-size: .82rem;
                min-height: 44px;
            }

            .tm-filter-panel .btn {
                min-height: 44px;
                font-size: .82rem;
            }

            .tm-toolbar {
                padding: .65rem .85rem;
                border-radius: .8rem .8rem 0 0;
            }

            .tm-toolbar-info {
                font-size: .78rem;
            }

            .tm-toolbar-actions {
                width: 100%;
                justify-content: flex-start;
                gap: .35rem;
            }

            .tm-icon-btn {
                font-size: .72rem;
                padding: .5rem .7rem;
                min-height: 40px;
                flex: 1;
            }

            .tm-mobile-list {
                padding: .55rem;
            }

            .tm-mobile-item {
                padding: .75rem;
                border-radius: .7rem;
            }

            .tm-mobile-field {
                grid-template-columns: 45% 1fr;
                font-size: .75rem;
                padding: .3rem 0;
            }

            .tm-pagination {
                padding: .65rem .85rem;
                border-radius: 0 0 .8rem .8rem;
                flex-direction: column;
                align-items: stretch;
                gap: .5rem;
            }

            .tm-pagination>div:first-child {
                text-align: center;
                font-size: .75rem;
            }

            .tm-pagination .pagination {
                justify-content: center;
            }

            .tm-pagination .page-link {
                font-size: .72rem;
                padding: .3rem .5rem;
                min-width: 30px;
            }

            .tm-col-dropdown {
                inset-inline-start: auto;
                inset-inline-end: 0;
                min-width: 200px;
                max-width: calc(100vw - 1.5rem);
            }

            .tm-empty {
                padding: 2rem 1rem;
            }

            .tm-empty i {
                font-size: 2rem;
            }
        }

        @media (max-width: 575.98px) {
            .tm-hero {
                padding: 1rem;
            }

            .tm-hero-actions {
                grid-template-columns: 1fr;
            }

            .tm-hero-btn {
                min-height: 44px;
            }

            .tm-stats {
                grid-template-columns: 1fr 1fr;
            }

            .tm-mobile-field {
                grid-template-columns: 1fr;
                gap: .15rem;
                padding: .4rem 0;
            }

            .tm-mobile-label {
                font-size: .7rem;
            }

            .tm-mobile-value {
                text-align: start;
                font-size: .8rem;
            }

            .tm-filter-panel .btn {
                width: 100%;
            }
        }

        /* ============ DARK MODE ============ */
        .admin-wrapper.dark-mode .tm-hero {
            box-shadow: 0 14px 32px -10px rgba(0, 0, 0, 0.5);
        }

        .admin-wrapper.dark-mode .tm-stat,
        .admin-wrapper.dark-mode .tm-chart-card,
        .admin-wrapper.dark-mode .tm-filter-panel,
        .admin-wrapper.dark-mode .tm-toolbar,
        .admin-wrapper.dark-mode .tm-table-wrapper,
        .admin-wrapper.dark-mode .tm-pagination,
        .admin-wrapper.dark-mode .tm-mobile-item,
        .admin-wrapper.dark-mode .tm-col-dropdown,
        .admin-wrapper.dark-mode .tm-unit-dropdown {
            background-color: #1e293b;
            border-color: #334155;
        }

        .admin-wrapper.dark-mode .tm-chart-box,
        .admin-wrapper.dark-mode .tm-chart-box canvas {
            background: #1e293b !important;
        }

        .admin-wrapper.dark-mode .tm-table-scroll,
        .admin-wrapper.dark-mode .tm-data-table,
        .admin-wrapper.dark-mode .tm-data-table tbody,
        .admin-wrapper.dark-mode .tm-data-table tbody tr,
        .admin-wrapper.dark-mode .tm-data-table tbody td {
            background-color: #1e293b;
        }

        .admin-wrapper.dark-mode .tm-data-table thead th {
            background-color: #17243a;
            color: #cbd5e1;
            border-color: #334155;
        }

        .admin-wrapper.dark-mode .tm-data-table tbody td {
            border-color: #334155;
            color: #cbd5e1;
        }

        .admin-wrapper.dark-mode .tm-data-table tbody tr:hover,
        .admin-wrapper.dark-mode .tm-data-table tbody tr:hover td {
            background-color: #263750;
        }

        .admin-wrapper.dark-mode .tm-stat-value,
        .admin-wrapper.dark-mode .tm-chart-title,
        .admin-wrapper.dark-mode .tm-toolbar-info,
        .admin-wrapper.dark-mode .tm-mobile-value {
            color: #f1f5f9;
        }

        .admin-wrapper.dark-mode .tm-stat-label,
        .admin-wrapper.dark-mode .tm-mobile-label {
            color: #94a3b8;
        }

        .admin-wrapper.dark-mode .tm-filter-panel .form-control,
        .admin-wrapper.dark-mode .tm-filter-panel .form-select {
            background-color: #0f172a;
            border-color: #334155;
            color: #e2e8f0;
        }

        .admin-wrapper.dark-mode .tm-empty,
        .admin-wrapper.dark-mode .tm-mobile-list {
            background-color: #1e293b;
        }
    </style>
@endpush
