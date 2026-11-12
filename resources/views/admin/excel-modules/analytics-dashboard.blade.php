{{-- resources/views/admin/excel-modules/analytics-dashboard.blade.php --}}
@extends('layouts.admin')

@section('title', 'لوحة التحليلات')

@section('content')
<div class="container-fluid" dir="rtl">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">لوحة التحليلات</h3>
            <small class="text-muted">{{ count($modules) }} موديول</small>
        </div>
    </div>

    <div class="row">
        @foreach ($modules as $code => $module)
            <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                <a href="{{ route('admin.excel-modules.analytics.page', $code) }}"
                   class="text-decoration-none">
                    <div class="card h-100 shadow-sm border-0 card-hover">
                        <div class="card-body text-center">
                            <div class="fs-4 mb-2 text-primary">
                                <i class="bi bi-bar-chart-line"></i>
                            </div>
                            <div class="fw-bold">{{ $module['name_ar'] ?? $code }}</div>
                            <div class="text-muted small">{{ $code }}</div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>
@endsection

@push('styles')
<style>
    .card-hover { transition: transform .15s, box-shadow .15s; }
    .card-hover:hover { transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.1) !important; }
</style>
@endpush