@extends('layouts.admin')

@section('title', 'لوحة بيانات Excel')
@section('page_title', 'لوحة بيانات Excel')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">لوحة بيانات Excel</li>
@endsection



@section('content')
<div class="container-fluid py-4 excel-dash" dir="rtl">

    {{-- ==================== HEADER ==================== --}}
    <div class="dash-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 position-relative">
            <div>
                <h2><i class="bi bi-file-earmark-spreadsheet me-2"></i> لوحة بيانات Excel</h2>
                <p>إدارة شاملة للموديولات وعمليات الاستيراد</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.excel-modules.create') }}" class="header-action-btn primary">
                    <i class="bi bi-cloud-upload"></i> رفع ملف جديد
                </a>
                <a href="{{ route('admin.excel-modules.index') }}" class="header-action-btn">
                    <i class="bi bi-grid-3x3-gap"></i> كل الموديولات
                </a>
            </div>
        </div>
    </div>

    {{-- ==================== KPI CARDS ==================== --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="--kpi-color: #0d6efd;">
                <div class="kpi-icon"><i class="bi bi-collection"></i></div>
                <div class="kpi-label">عدد الموديولات</div>
                <div class="kpi-value">{{ number_format($totalModules) }}</div>
                <div class="kpi-sub">موديول مسجّل</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="--kpi-color: #198754;">
                <div class="kpi-icon"><i class="bi bi-table"></i></div>
                <div class="kpi-label">إجمالي الصفوف</div>
                <div class="kpi-value">{{ number_format($totalRecords) }}</div>
                <div class="kpi-sub">صف في كل الموديولات</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="--kpi-color: #fd7e14;">
                <div class="kpi-icon"><i class="bi bi-clock-history"></i></div>
                <div class="kpi-label">آخر استيراد</div>
                <div class="kpi-value" style="font-size: 1.1rem;">
                    @if($lastImport)
                        {{ $lastImport->created_at->diffForHumans() }}
                    @else
                        —
                    @endif
                </div>
                @if($lastImport?->module)
                    <div class="kpi-sub text-truncate">
                        <i class="bi bi-arrow-left-short"></i> {{ $lastImport->module->name_ar }}
                    </div>
                @else
                    <div class="kpi-sub">لا يوجد استيراد بعد</div>
                @endif
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card" style="--kpi-color: #8b5cf6;">
                <div class="kpi-icon"><i class="bi bi-graph-up-arrow"></i></div>
                <div class="kpi-label">متوسط الصفوف</div>
                <div class="kpi-value">
                    {{ $totalModules > 0 ? number_format(round($totalRecords / $totalModules)) : 0 }}
                </div>
                <div class="kpi-sub">صف لكل موديول</div>
            </div>
        </div>
    </div>

    {{-- ==================== CHART + ACTIVITY ==================== --}}
    <div class="row g-4 mb-4">
        {{-- Chart --}}
        <div class="col-lg-7">
            <div class="card-modern">
                <div class="card-header-modern">
                    <span><i class="bi bi-bar-chart-line me-2 text-primary"></i> توزيع الصفوف حسب الموديول</span>
                    @if(count($chartLabels) > 0)
                        <button class="chart-export-btn" onclick="exportModulesChart()" title="تحميل PNG" style="position: static;">
                            <i class="bi bi-download"></i>
                        </button>
                    @endif
                </div>
                <div class="card-body-modern" style="position: relative;">
                    @if(count($chartLabels) > 0)
                        <div id="modulesChartTotal" class="chart-total-pill"></div>
                        <div style="position: relative; height: 280px;">
                            <canvas id="modulesChart"></canvas>
                        </div>
                        <div id="modulesChartLegend" class="chart-legend"></div>
                    @else
                        <div class="empty-state">
                            <i class="bi bi-bar-chart"></i>
                            <p>لا توجد بيانات بعد — ابدأ برفع ملف Excel</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Activity Timeline --}}
        <div class="col-lg-5">
            <div class="card-modern">
                <div class="card-header-modern">
                    <span><i class="bi bi-clock-history me-2 text-warning"></i> آخر عمليات الاستيراد</span>
                    <span class="badge bg-light text-dark small">{{ $recentBatches->count() }}</span>
                </div>
                <div class="card-body-modern" style="padding: 1rem 1.25rem;">
                    @if($recentBatches->count() > 0)
                        <div class="activity-timeline">
                            @foreach($recentBatches as $index => $batch)
                                @php
                                    $dotColors = ['#0d6efd', '#198754', '#fd7e14', '#8b5cf6', '#dc3545'];
                                    $dotColor = $dotColors[$index % count($dotColors)];
                                @endphp
                                <div class="activity-item" style="--dot-color: {{ $dotColor }};">
                                    <div class="activity-title">
                                        {{ $batch->module?->name_ar ?? '—' }}
                                    </div>
                                    <div class="activity-file" title="{{ $batch->original_filename }}">
                                        <i class="bi bi-file-earmark-text me-1"></i>
                                        {{ $batch->original_filename }}
                                    </div>
                                    <div class="activity-meta d-flex justify-content-between align-items-center mt-1">
                                        <span class="activity-badge">
                                            <i class="bi bi-check-circle-fill"></i>
                                            {{ number_format($batch->imported_rows) }} صف
                                        </span>
                                        <span class="text-muted">
                                            <i class="bi bi-clock"></i>
                                            {{ $batch->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <p>لا توجد عمليات استيراد بعد</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== MODULES TABLE ==================== --}}
    <div class="card-modern">
        <div class="card-header-modern">
            <span><i class="bi bi-table me-2 text-success"></i> كل الموديولات</span>
            <div class="d-flex gap-2 align-items-center">
                <div class="search-wrapper">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" id="moduleSearch" class="search-input" placeholder="ابحث...">
                </div>
                <a href="{{ route('admin.excel-modules.index') }}" class="btn btn-sm btn-outline-primary" style="border-radius: 50px;">
                    <i class="bi bi-grid"></i> كروت
                </a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-modern mb-0" id="modulesTable">
                <thead>
                    <tr>
                        <th>الموديول</th>
                        <th>الكود</th>
                        <th>عدد الصفوف</th>
                        <th>آخر استيراد</th>
                        <th class="text-end">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($modules as $module)
                        @php
                            $maxRows = $modules->max('records_count') ?: 1;
                            $pct = $maxRows > 0 ? ($module->records_count / $maxRows) * 100 : 0;
                        @endphp
                        <tr class="module-row"
                            data-name="{{ mb_strtolower($module->name_ar) }}"
                            data-code="{{ mb_strtolower($module->code) }}">
                            <td>
                                <div class="module-name-cell">
                                    <div class="module-avatar">
                                        <i class="bi bi-file-earmark-spreadsheet"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $module->name_ar }}</div>
                                        <div class="text-muted small">موديول Excel</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="module-code-badge">{{ $module->code }}</span>
                            </td>
                            <td>
                                <div class="rows-progress">
                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{ $pct }}%;"></div>
                                    </div>
                                    <span class="rows-count">{{ number_format($module->records_count) }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted small">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ $module->last_imported_at?->format('Y-m-d H:i') ?? '—' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.excel-modules.show', $module) }}"
                                   class="btn-action-icon"
                                   title="عرض التفاصيل">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.excel-modules.analytics.page', $module->code) }}"
                                   class="btn-action-icon ms-1"
                                   title="التحليلات">
                                    <i class="bi bi-bar-chart"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <p>لا توجد موديولات — ابدأ برفع ملف Excel جديد</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div id="noSearchResults" class="empty-state d-none">
            <i class="bi bi-search"></i>
            <p>لا توجد نتائج مطابقة لبحثك</p>
        </div>
    </div>

</div>
@endsection

@push('scripts')
@if(count($chartLabels) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
    'use strict';

    let modulesChartInstance = null;

    // ==================== CHART ====================
    function initChart() {
        const ctx = document.getElementById('modulesChart');
        if (!ctx) return;

        const labels = @json($chartLabels);
        const data = @json($chartData);
        const total = data.reduce((s, v) => s + (Number(v) || 0), 0);

        // Pill الإجمالي
        const pill = document.getElementById('modulesChartTotal');
        if (pill) {
            pill.innerHTML = `<i class="bi bi-calculator"></i> إجمالي الصفوف: <strong>${total.toLocaleString('en-US')}</strong> صف`;
        }

        // Legend
        const legend = document.getElementById('modulesChartLegend');
        if (legend) {
            const palette = ['#0d6efd', '#0dcaf0', '#198754', '#ffc107', '#fd7e14', '#dc3545', '#8b5cf6', '#6c757d'];
            legend.innerHTML = labels.map((lbl, i) => `
                <span class="legend-item">
                    <span class="legend-dot" style="background:${palette[i % palette.length]};"></span>
                    <span>${lbl} (${Number(data[i]).toLocaleString('en-US')})</span>
                </span>
            `).join('');
        }

        // Colors
        const palette = ['#0d6efd', '#0dcaf0', '#198754', '#ffc107', '#fd7e14', '#dc3545', '#8b5cf6', '#6c757d'];
        const bgColors = labels.map((_, i) => palette[i % palette.length]);

        modulesChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'عدد الصفوف',
                    data: data,
                    backgroundColor: bgColors,
                    borderColor: '#fff',
                    borderWidth: 2,
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    title: {
                        display: true,
                        text: `توزيع الصفوف — الإجمالي: ${total.toLocaleString('en-US')} صف`,
                        font: { size: 13, weight: 'bold' },
                        color: '#0b2b5c',
                        padding: { bottom: 12 }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                const val = Number(ctx.raw) || 0;
                                const pct = total ? ((val / total) * 100).toFixed(1) : 0;
                                return ` ${ctx.label}: ${val.toLocaleString('en-US')} صف (${pct}%)`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 },
                        grid: { color: '#e9ecef' },
                        title: { display: true, text: 'عدد الصفوف' }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 0,
                            font: { size: 11 }
                        },
                        title: { display: true, text: 'الموديول' }
                    }
                }
            }
        });
    }

    // ==================== EXPORT CHART ====================
    window.exportModulesChart = function () {
        if (!modulesChartInstance) return;
        const link = document.createElement('a');
        link.download = `modules-chart-${new Date().toISOString().slice(0, 10)}.png`;
        link.href = modulesChartInstance.toBase64Image();
        link.click();
    };

    // ==================== SEARCH ====================
    function initSearch() {
        const input = document.getElementById('moduleSearch');
        const rows = document.querySelectorAll('.module-row');
        const noResults = document.getElementById('noSearchResults');

        if (!input) return;

        input.addEventListener('input', function (e) {
            const query = e.target.value.trim().toLowerCase();
            let visible = 0;

            rows.forEach(row => {
                const name = row.dataset.name || '';
                const code = row.dataset.code || '';
                const match = !query || name.includes(query) || code.includes(query);

                row.style.display = match ? '' : 'none';
                if (match) visible++;
            });

            if (noResults) {
                noResults.classList.toggle('d-none', visible > 0 || rows.length === 0);
            }
        });
    }

    // ==================== BOOTSTRAP ====================
    document.addEventListener('DOMContentLoaded', function () {
        initChart();
        initSearch();
    });
})();
</script>
@endif
@endpush