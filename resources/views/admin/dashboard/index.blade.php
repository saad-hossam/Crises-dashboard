@extends('layouts.admin')

@section('title', __('dashboard.title'))
@section('page_title', __('dashboard.title'))

@section('content')
{{-- Hero --}}
<div class="dash-hero mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <p class="dash-hero-kicker mb-1">
                <i class="bi bi-lightning-charge-fill me-1"></i>{{ __('dashboard.analysis') }}
            </p>
            <h1 class="dash-hero-title mb-1">{{ __('dashboard.welcome', ['name' => auth()->user()->name]) }}</h1>
            <p class="dash-hero-sub mb-0">{{ now()->translatedFormat('l, d F Y') }} · {{ __('dashboard.description') }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.excel-modules.index') }}" class="btn btn-light btn-sm">
                <i class="bi bi-grid me-1"></i>{{ __('excel.modules') }}
            </a>
            <a href="{{ route('admin.excel-modules.create') }}" class="btn btn-warning btn-sm fw-semibold">
                <i class="bi bi-cloud-upload me-1"></i>{{ __('dashboard.import_now') }}
            </a>
        </div>
    </div>
</div>

{{-- KPI --}}
<div class="row g-3 mb-4">
    @foreach ($stats as $stat)
        <div class="col-6 col-lg-3">
            <div class="kpi-card h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-label">{{ $stat['label'] }}</div>
                        <div class="kpi-value">{{ number_format($stat['value']) }}</div>
                    </div>
                    <div class="kpi-icon text-{{ $stat['color'] }} bg-{{ $stat['color'] }}-subtle">
                        <i class="bi {{ $stat['icon'] }}"></i>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Global charts --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-xl-7">
        <div class="panel-card h-100">
            <div class="panel-head">
                <span><i class="bi bi-bar-chart-fill text-primary me-2"></i>{{ __('dashboard.records_overview') }}</span>
            </div>
            <div class="panel-body">
                @if(array_sum($chartCounts ?? []) > 0)
                    <div class="chart-box"><canvas id="chartModules"></canvas></div>
                @else
                    <div class="empty-chart">{{ __('dashboard.chart_placeholder') }}</div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-5">
        <div class="panel-card h-100">
            <div class="panel-head">
                <span><i class="bi bi-pie-chart-fill text-success me-2"></i>{{ __('dashboard.operating_status') }}</span>
            </div>
            <div class="panel-body">
                @if(array_sum($statusCounts ?? []) > 0)
                    <div class="chart-box chart-box-sm"><canvas id="chartStatus"></canvas></div>
                @else
                    <div class="empty-chart">{{ __('dashboard.chart_placeholder') }}</div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-6">
        <div class="panel-card h-100">
            <div class="panel-head">
                <span><i class="bi bi-geo-alt-fill text-danger me-2"></i>{{ __('dashboard.records_by_category') }}</span>
            </div>
            <div class="panel-body">
                @if(array_sum($govCounts ?? []) > 0)
                    <div class="chart-box chart-box-sm"><canvas id="chartGov"></canvas></div>
                @else
                    <div class="empty-chart">{{ __('dashboard.chart_placeholder') }}</div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-6">
        <div class="panel-card h-100">
            <div class="panel-head">
                <span><i class="bi bi-shield-check text-warning me-2"></i>{{ __('dashboard.readiness') }}</span>
            </div>
            <div class="panel-body">
                @if(array_sum($readinessCounts ?? []) > 0)
                    <div class="chart-box chart-box-sm"><canvas id="chartReadiness"></canvas></div>
                @else
                    <div class="empty-chart">{{ __('dashboard.chart_placeholder') }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Per-module cards + mini charts --}}
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h2 class="section-title mb-0">
        <i class="bi bi-grid-3x3-gap-fill text-primary me-2"></i>{{ __('dashboard.module_stats') }}
    </h2>
    <a href="{{ route('admin.excel-modules.index') }}" class="btn btn-outline-primary btn-sm">
        {{ __('excel.modules') }}
    </a>
</div>

<div class="row g-3 mb-4">
@foreach($moduleStats as $i => $m)
    <div class="col-12 col-md-6 col-xl-4">
        <div class="module-panel h-100">
            <div class="module-panel-top">
                <div class="d-flex align-items-start gap-3">
                    <div class="kpi-icon text-{{ $m['color'] }} bg-{{ $m['color'] }}-subtle">
                        <i class="bi {{ $m['icon'] }}"></i>
                    </div>
                    <div class="min-w-0 flex-grow-1">
                        <div class="d-flex justify-content-between gap-2">
                            <h3 class="module-title text-truncate mb-0">{{ $m['name'] }}</h3>
                            <span class="badge bg-light text-dark border font-monospace small">{{ $m['code'] }}</span>
                        </div>
                        <div class="module-meta">
                            <code>{{ $m['table'] }}</code>
                        </div>
                    </div>
                </div>
                <div class="row g-2 mt-3">
                    <div class="col-4">
                        <div class="mini-stat">
                            <div class="mini-stat-val">{{ number_format($m['count']) }}</div>
                            <div class="mini-stat-lbl">{{ __('excel.records') }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mini-stat">
                            <div class="mini-stat-val">{{ number_format($m['with_coords']) }}</div>
                            <div class="mini-stat-lbl"><i class="bi bi-geo-alt"></i> GPS</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mini-stat">
                            <div class="mini-stat-val">{{ $m['coverage'] }}%</div>
                            <div class="mini-stat-lbl">{{ __('excel.location') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="module-panel-chart">
                @if(array_sum($m['status']['data'] ?? []) > 0)
                    <canvas id="modChart{{ $i }}" height="120"></canvas>
                @elseif($m['count'] === 0)
                    <div class="empty-chart py-3">{{ __('dashboard.no_data') }}</div>
                @else
                    <div class="empty-chart py-3">{{ __('dashboard.chart_placeholder') }}</div>
                @endif
            </div>
            <div class="module-panel-foot">
                <a href="{{ route('admin.excel-modules.table', $m['code']) }}" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-table me-1"></i>{{ __('excel.view_data') }}
                </a>
            </div>
        </div>
    </div>
@endforeach
</div>

{{-- Recent imports --}}
<div class="panel-card">
    <div class="panel-head">
        <span><i class="bi bi-clock-history me-2"></i>{{ __('dashboard.recent_imports') }}</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 dash-table">
            <thead>
                <tr>
                    <th class="ps-3">{{ __('excel.modules') }}</th>
                    <th class="d-none d-md-table-cell">File</th>
                    <th>Rows</th>
                    <th>Status</th>
                    <th class="pe-3">Time</th>
                </tr>
            </thead>
            <tbody>
            @forelse($recentImports as $batch)
                <tr>
                    <td class="ps-3">
                        <span class="badge bg-primary-subtle text-primary border">{{ $batch->module_code }}</span>
                        <div class="small text-muted text-truncate" style="max-width:160px">{{ $batch->module_name_ar }}</div>
                    </td>
                    <td class="d-none d-md-table-cell small text-truncate" style="max-width:180px">
                        {{ $batch->original_filename }}
                    </td>
                    <td class="small">{{ number_format($batch->imported_rows) }}/{{ number_format($batch->total_rows) }}</td>
                    <td>
                        @if($batch->status === 'completed')
                            <span class="badge bg-success-subtle text-success">OK</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger">{{ $batch->status }}</span>
                        @endif
                    </td>
                    <td class="pe-3 small text-muted text-nowrap">{{ $batch->created_at?->diffForHumans() }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">{{ __('dashboard.no_data') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const isRtl = document.documentElement.dir === 'rtl';
const palette = ['#4f46e5','#06b6d4','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#64748b','#0ea5e9','#84cc16'];

Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;
Chart.defaults.font.size = 12;
Chart.defaults.color = '#64748b';

function makeChart(id, type, labels, data) {
    const el = document.getElementById(id);
    if (!el || !labels || !labels.length) return;
    const isDonut = type === 'doughnut' || type === 'pie';
    new Chart(el, {
        type,
        data: {
            labels,
            datasets: [{
                data,
                backgroundColor: palette.slice(0, Math.max(labels.length, 1)),
                borderWidth: 0,
                borderRadius: type === 'bar' ? 8 : 0,
                cutout: type === 'doughnut' ? '62%' : undefined,
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
                    labels: { boxWidth: 10, boxHeight: 10, padding: 12, font: { size: 11 } }
                },
                tooltip: { rtl: isRtl }
            },
            scales: isDonut ? {} : {
                x: { grid: { display: false }, ticks: { font: { size: 10 }, maxRotation: 40 } },
                y: { beginAtZero: true, ticks: { precision: 0, font: { size: 10 } }, grid: { color: 'rgba(15,23,42,.06)' } }
            }
        }
    });
}

makeChart('chartModules', 'bar', @json($chartLabels ?? []), @json($chartCounts ?? []));
makeChart('chartStatus', 'doughnut', @json($statusLabels ?? []), @json($statusCounts ?? []));
makeChart('chartGov', 'bar', @json($govLabels ?? []), @json($govCounts ?? []));
makeChart('chartReadiness', 'doughnut', @json($readinessLabels ?? []), @json($readinessCounts ?? []));

@foreach($moduleStats as $i => $m)
@if(array_sum($m['status']['data'] ?? []) > 0)
makeChart('modChart{{ $i }}', 'doughnut', @json($m['status']['labels']), @json($m['status']['data']));
@endif
@endforeach
</script>
@endpush
