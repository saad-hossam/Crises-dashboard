
@extends('layouts.admin')

@section('title', __('production_lines.show'))
@section('page_title', __('production_lines.show'))

@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('admin.production-lines.index') }}">
        {{ __('production_lines.title') }}
    </a>
</li>
<li class="breadcrumb-item active" aria-current="page">
    {{ __('production_lines.show') }}
</li>
@endsection

@section('content')

@php
    $sections = [
        'basic' => [
            'owner_organization',
            'location_scope',
            'governorate',
            'name',
            'product_name',
            'manufacturer',
        ],
        'capacity' => [
            'operating_year',
            'design_capacity',
            'capacity_unit',
            'current_operational_capacity',
            'annual_production_capacity',
            'actual_production',
            'utilization_rate',
        ],
        'operations' => [
            'shifts_count',
            'daily_operating_hours',
            'workers_count',
            'line_status',
            'expected_lifetime_years',
        ],
        'maintenance' => [
            'last_maintenance_date',
            'next_maintenance_date',
            'production_increase_possibility',
            'production_increase_lead_time_days',
            'development_type',
            'development_completion_date',
        ],
        'company' => [
            'company_name',
            'address',
            'operating_status',
            'readiness_level',
        ],
        'location' => [
            'x_coordinate',
            'y_coordinate',
        ],
        'tracking' => [
            'last_updated_date',
            'data_entry_responsible',
            'unit',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ],
    ];

    $getValue = function ($field) use ($productionLine) {
        return match ($field) {
            'unit' => $productionLine->unit?->name,
            'created_by' => $productionLine->creator?->name,
            'updated_by' => $productionLine->updater?->name,
            'created_at' => $productionLine->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $productionLine->updated_at?->format('Y-m-d H:i:s'),
            default => $productionLine->{$field},
        };
    };
@endphp

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h3 class="fw-bold mb-1">
            {{ $productionLine->name ?: __('production_lines.empty') }}
        </h3>

        <p class="text-muted mb-0">
            {{ __('production_lines.show') }}
            #{{ $productionLine->id }}
        </p>
    </div>

    <a href="{{ route('admin.production-lines.index') }}"
       class="btn btn-outline-secondary">
        <i class="bi bi-arrow-right"></i>
        {{ __('production_lines.back') }}
    </a>
</div>

@foreach ($sections as $section => $fields)
    <div class="card mb-4">
        <div class="card-header bg-transparent py-3">
            <h5 class="fw-bold mb-0">
                {{ __('production_lines.sections.' . $section) }}
            </h5>
        </div>

        <div class="card-body">
            <dl class="row mb-0">
                @foreach ($fields as $field)
                    @php
                        $value = $getValue($field);
                    @endphp

                    <dt class="col-md-3 col-sm-4 mb-2">
                        {{ __('production_lines.fields.' . $field) }}
                    </dt>

                    <dd class="col-md-9 col-sm-8 mb-3"
                        style="overflow-wrap: anywhere; white-space: pre-wrap;">
                        {{ filled($value) ? $value : __('production_lines.empty') }}
                    </dd>
                @endforeach
            </dl>
        </div>
    </div>
@endforeach

<div class="mb-4">
    <a href="{{ route('admin.production-lines.index') }}"
       class="btn btn-outline-secondary">
        <i class="bi bi-arrow-right"></i>
        {{ __('production_lines.back') }}
    </a>
</div>

@endsection
