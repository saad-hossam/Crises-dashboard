@extends('layouts.admin')

@section('title', __('excel.details').' — '.$config['name_ar'])
@section('page_title', __('excel.details'))

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.excel-modules.index') }}">{{ __('excel.modules') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.excel-modules.table', $moduleCode) }}">{{ $config['name_ar'] }}</a></li>
<li class="breadcrumb-item active">#{{ $record->id }}</li>
@endsection

@section('content')
<div class="hero-detail mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-white text-primary">{{ $moduleCode }}</span>
                <span class="badge bg-white bg-opacity-25">{{ $config['table'] }}</span>
            </div>
            <h3 class="mb-1 fw-bold">
                {{ $record->name
                    ?? $record->name_ar
                    ?? $record->site_name
                    ?? $record->warehouse_name
                    ?? $record->facility_name
                    ?? $record->product_name
                    ?? $record->unit_name
                    ?? ('#'.$record->id) }}
            </h3>
            <div class="opacity-75 small">
                @if($record->unit_code ?? null)
                    <i class="bi bi-hash me-1"></i>{{ $record->unit_code }}
                @endif
                @if($record->governorate ?? null)
                    <span class="mx-2">·</span>
                    <i class="bi bi-geo me-1"></i>{{ $record->governorate }}
                @endif
                @if($record->operating_status ?? null)
                    <span class="mx-2">·</span>
                    <i class="bi bi-activity me-1"></i>{{ $record->operating_status }}
                @endif
            </div>
        </div>
        <a href="{{ route('admin.excel-modules.table', $moduleCode) }}" class="btn btn-light">
            <i class="bi bi-arrow-{{ app()->getLocale()==='ar' ? 'left' : 'right' }} me-1"></i>
            {{ __('excel.back_to_table') }}
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3">
                <div class="text-muted small"><i class="bi bi-upc me-1"></i>ID</div>
                <div class="fs-5 fw-bold">#{{ $record->id }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3">
                <div class="text-muted small"><i class="bi bi-building me-1"></i>{{ __('excel.unit_code') }}</div>
                <div class="fs-6 fw-bold text-truncate">{{ $record->unit_code ?? '—' }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3">
                <div class="text-muted small"><i class="bi bi-flag me-1"></i>Status</div>
                <div class="fs-6 fw-bold">{{ $record->operating_status ?? '—' }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3">
                <div class="text-muted small"><i class="bi bi-shield-check me-1"></i>Readiness</div>
                <div class="fs-6 fw-bold">{{ $record->readiness_level ?? '—' }}</div>
            </div>
        </div>
    </div>
</div>

@if(!empty($record->x_coordinate) && !empty($record->y_coordinate))
<div class="card border-0 shadow-sm mb-4 overflow-hidden">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold"><i class="bi bi-geo-alt-fill text-danger me-2"></i>{{ __('excel.location') }}</span>
        <a class="small" target="_blank" rel="noopener"
           href="https://www.google.com/maps?q={{ $record->y_coordinate }},{{ $record->x_coordinate }}">
            {{ __('excel.open_maps') }} <i class="bi bi-box-arrow-up-left"></i>
        </a>
    </div>
    <div class="ratio ratio-21x9" style="min-height:240px">
        <iframe
            src="https://maps.google.com/maps?q={{ $record->y_coordinate }},{{ $record->x_coordinate }}&z=14&output=embed"
            style="border:0" loading="lazy" allowfullscreen></iframe>
    </div>
</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-list-columns-reverse text-primary me-2"></i>
            {{ __('excel.details') }}
            <span class="badge bg-light text-muted border ms-1">{{ count($columns) }} {{ __('excel.fields') }}</span>
        </h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @foreach($columns as $col)
                @php $v = $record->{$col} ?? null; @endphp
                <div class="col-md-6 col-xl-4">
                    <div class="detail-field">
                        <div class="label">
                            <i class="bi bi-dot"></i>{{ str_replace('_', ' ', $col) }}
                        </div>
                        <div class="value">
                            {{ $v === null || $v === '' ? '—' : $v }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
