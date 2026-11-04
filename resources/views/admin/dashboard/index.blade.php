@extends('layouts.admin')

@section('title', __('dashboard.title'))
@section('page_title', __('dashboard.title'))

@section('content')
{{-- Welcome Section --}}
<div class="welcome-card mb-4">
    <h2>{{ __('dashboard.welcome', ['name' => auth()->user()->name]) }}</h2>
    <p class="text-muted mb-1">{{ now()->translatedFormat('l, d F Y') }}</p>
    <p class="mb-0">{{ __('dashboard.description') }}</p>
</div>

{{-- Statistics --}}
<div class="row g-4 mb-4">
    @foreach ($stats as $stat)
        <div class="col-md-6 col-xl-3">
            <div class="stat-card card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stat-label">{{ $stat['label'] }}</p>
                            <h3 class="stat-value">{{ number_format($stat['value']) }}</h3>
                            @if (! empty($stat['note']))
                                <small class="text-muted">{{ $stat['note'] }}</small>
                            @endif
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

{{-- Chart Placeholders --}}
<div class="row g-4">
    <div class="col-lg-8">
        <div class="chart-placeholder card h-100">
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <i class="bi bi-graph-up display-4 text-muted mb-3"></i>
                <h5>{{ __('dashboard.records_overview') }}</h5>
                <p class="text-muted mb-0">{{ __('dashboard.chart_placeholder') }}</p>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="chart-placeholder card h-100">
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <i class="bi bi-pie-chart display-4 text-muted mb-3"></i>
                <h5>{{ __('dashboard.records_by_category') }}</h5>
                <p class="text-muted mb-0">{{ __('dashboard.chart_placeholder') }}</p>
            </div>
        </div>
    </div>
 
</div>
@endsection