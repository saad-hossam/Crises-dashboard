@extends('layouts.admin')

@section('title', __('excel.modules'))
@section('page_title', __('excel.modules'))

@section('breadcrumb')
<li class="breadcrumb-item active">{{ __('excel.modules') }}</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h4 class="mb-1 fw-bold"><i class="bi bi-file-earmark-spreadsheet text-primary me-2"></i>{{ __('excel.modules') }}</h4>
        <p class="text-muted mb-0 small">{{ __('excel.modules_desc') }}</p>
    </div>
    <a href="{{ route('admin.excel-modules.create') }}" class="btn btn-primary">
        <i class="bi bi-upload me-1"></i> {{ __('excel.upload') }}
    </a>
</div>

<div class="row g-3">
@forelse($modules as $module)
    <div class="col-12 col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm h-100 module-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="stat-icon bg-primary-subtle text-primary">
                        <i class="bi bi-table"></i>
                    </div>
                    <span class="badge bg-light text-dark border font-monospace">{{ $module['code'] }}</span>
                </div>
                <h5 class="card-title mb-1">{{ $module['name_ar'] }}</h5>
                <p class="text-muted small mb-2">
                    {{ __('excel.table') }}: <code>{{ $module['table'] }}</code>
                </p>
                <p class="text-muted small mb-3">
                    @if($module['last_imported_at'])
                        {{ __('excel.last_import') }} {{ $module['last_imported_at']->diffForHumans() }}
                    @else
                        {{ __('excel.never_imported') }}
                    @endif
                </p>
                <div class="fs-3 fw-bold text-primary mb-1">{{ number_format($module['records_count']) }}</div>
                <div class="text-muted small mb-0">{{ __('excel.records') }}</div>
            </div>
            <div class="card-footer bg-white border-0 pt-0 pb-3">
                <a href="{{ route('admin.excel-modules.table', $module['code']) }}" class="btn btn-primary w-100">
                    <i class="bi bi-table me-1"></i> {{ __('excel.view_data') }}
                </a>
            </div>
        </div>
    </div>
@empty
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5 text-muted">{{ __('dashboard.no_data') }}</div>
        </div>
    </div>
@endforelse
</div>

@push('styles')
<style>
.module-card { transition: transform .15s ease, box-shadow .15s ease; border-radius: 1rem!important; }
.module-card:hover { transform: translateY(-3px); box-shadow: 0 .75rem 1.25rem rgba(0,0,0,.1)!important; }
.stat-icon { width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.25rem; }
</style>
@endpush
@endsection
