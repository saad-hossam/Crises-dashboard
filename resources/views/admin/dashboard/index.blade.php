@extends('layouts.admin')

@section('title', __('dashboard.title'))
@section('page_title', __('dashboard.title'))

@section('content')
<div class="welcome-card mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <h2 class="mb-1">{{ __('dashboard.welcome', ['name' => auth()->user()->name]) }}</h2>
        <p class="text-muted mb-1">{{ now()->translatedFormat('l, d F Y') }}</p>
        <p class="mb-0 opacity-75">{{ __('dashboard.description') }}</p>
    </div>
    <a href="{{ route('admin.excel-modules.create') }}" class="btn btn-light btn-lg shadow-sm">
        <i class="bi bi-cloud-upload me-2"></i>{{ __('dashboard.import_now') }}
    </a>
</div>

{{-- KPI cards --}}
<div class="row g-3 mb-4">
    @foreach ($stats as $stat)
        <div class="col-6 col-xl-3">
            <div class="stat-card card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stat-label mb-1">{{ $stat['label'] }}</p>
                            <h3 class="stat-value mb-0">{{ number_format($stat['value']) }}</h3>
                        </div>
                        <div class="stat-icon bg-{{ $stat['color'] }}-subtle text-{{ $stat['color'] }}">
                            <i class="bi {{ $stat['icon'] }}"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Charts --}}
<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-bar-chart-fill text-primary me-2"></i>{{ __('dashboard.records_overview') }}</h6>
            </div>
            <div class="card-body">
                @if(array_sum($chartCounts ?? []) > 0)
                    <canvas id="chartModules" height="140"></canvas>
                @else
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-graph-up display-5 d-block mb-2"></i>
                        {{ __('dashboard.chart_placeholder') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-pie-chart-fill text-success me-2"></i>{{ __('dashboard.operating_status') }}</h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                @if(array_sum($statusCounts ?? []) > 0)
                    <canvas id="chartStatus" height="200"></canvas>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-pie-chart display-5 d-block mb-2"></i>
                        {{ __('dashboard.chart_placeholder') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-geo-alt-fill text-danger me-2"></i>{{ __('dashboard.records_by_category') }}</h6>
            </div>
            <div class="card-body">
                @if(array_sum($govCounts ?? []) > 0)
                    <canvas id="chartGov" height="160"></canvas>
                @else
                    <div class="text-center text-muted py-4">{{ __('dashboard.chart_placeholder') }}</div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-shield-check text-warning me-2"></i>{{ __('dashboard.readiness') }}</h6>
            </div>
            <div class="card-body">
                @if(array_sum($readinessCounts ?? []) > 0)
                    <canvas id="chartReadiness" height="160"></canvas>
                @else
                    <div class="text-center text-muted py-4">{{ __('dashboard.chart_placeholder') }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Module cards --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-grid-3x3-gap me-2 text-primary"></i>{{ __('dashboard.module_stats') }}</h5>
    <a href="{{ route('admin.excel-modules.index') }}" class="btn btn-sm btn-outline-primary">{{ __('excel.modules') }}</a>
</div>
<div class="row g-3 mb-4">
    @foreach($moduleStats as $m)
        <div class="col-6 col-md-4 col-xl-3">
            <a href="{{ route('admin.excel-modules.table', $m['code']) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 module-mini-card">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-icon bg-{{ $m['color'] }}-subtle text-{{ $m['color'] }}" style="width:42px;height:42px;font-size:1.1rem">
                                <i class="bi {{ $m['icon'] }}"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="fw-semibold text-dark text-truncate">{{ $m['name'] }}</div>
                                <div class="text-muted small">{{ number_format($m['count']) }} {{ __('excel.records') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>

{{-- Recent imports --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 pt-3">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-2"></i>{{ __('dashboard.recent_imports') }}</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">{{ __('excel.modules') }}</th>
                        <th>الملف</th>
                        <th>الصفوف</th>
                        <th>الحالة</th>
                        <th class="pe-3">الوقت</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($recentImports as $batch)
                    <tr>
                        <td class="ps-3">
                            <span class="badge bg-primary-subtle text-primary border">{{ $batch->module_code }}</span>
                            <div class="small text-muted">{{ $batch->module_name_ar }}</div>
                        </td>
                        <td class="small">{{ \Illuminate\Support\Str::limit($batch->original_filename, 30) }}</td>
                        <td>{{ number_format($batch->imported_rows) }} / {{ number_format($batch->total_rows) }}</td>
                        <td>
                            @if($batch->status === 'completed')
                                <span class="badge bg-success-subtle text-success">completed</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger">{{ $batch->status }}</span>
                            @endif
                        </td>
                        <td class="pe-3 small text-muted">{{ $batch->created_at?->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">{{ __('dashboard.no_data') }}</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const isRtl = document.documentElement.dir === 'rtl';
const palette = ['#4f46e5','#06b6d4','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#64748b'];

function makeChart(id, type, labels, data, opts = {}) {
    const el = document.getElementById(id);
    if (!el) return;
    new Chart(el, {
        type,
        data: {
            labels,
            datasets: [{
                data,
                backgroundColor: palette.slice(0, labels.length),
                borderWidth: type === 'line' ? 2 : 0,
                borderRadius: type === 'bar' ? 6 : 0,
                tension: 0.3,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: type === 'doughnut' || type === 'pie' ? 'bottom' : 'none',
                    rtl: isRtl,
                    labels: { boxWidth: 12, font: { family: 'inherit' } }
                }
            },
            scales: type === 'doughnut' || type === 'pie' ? {} : {
                x: { ticks: { maxRotation: 45, minRotation: 0 }, grid: { display: false } },
                y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: 'rgba(0,0,0,.05)' } }
            },
            ...opts
        }
    });
}

makeChart('chartModules', 'bar', @json($chartLabels ?? []), @json($chartCounts ?? []));
makeChart('chartStatus', 'doughnut', @json($statusLabels ?? []), @json($statusCounts ?? []));
makeChart('chartGov', 'bar', @json($govLabels ?? []), @json($govCounts ?? []));
makeChart('chartReadiness', 'doughnut', @json($readinessLabels ?? []), @json($readinessCounts ?? []));
</script>
@endpush

@push('styles')
<style>
.module-mini-card { transition: transform .15s ease, box-shadow .15s ease; }
.module-mini-card:hover { transform: translateY(-3px); box-shadow: 0 .75rem 1.25rem rgba(0,0,0,.1)!important; }
.welcome-card {
    background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
    color: #fff;
    border-radius: 1rem;
    padding: 1.5rem 1.75rem;
}
.welcome-card .text-muted { color: rgba(255,255,255,.85)!important; }
.welcome-card .opacity-75 { color: rgba(255,255,255,.9); }
.stat-card { border-radius: .9rem; }
.stat-icon {
    width: 48px; height: 48px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center; font-size: 1.35rem;
}
.stat-label { font-size: .8rem; color: #64748b; margin: 0; }
.stat-value { font-weight: 700; letter-spacing: -.02em; }
</style>
@endpush
