@extends('layouts.admin')

@section('title', 'تفاصيل السجل')
@section('page_title', 'تفاصيل السجل')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.excel-modules.index') }}">موديولات Excel</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.excel-modules.show', $module) }}">{{ $module->name_ar }}</a></li>
<li class="breadcrumb-item active">سجل #{{ $record->id }}</li>
@endsection

@push('styles')
<style>
    .rd-hero {
        background: linear-gradient(135deg, color-mix(in srgb, var(--aoi-primary, #3b82f6) 12%, transparent), transparent);
        border: 1px solid color-mix(in srgb, var(--aoi-primary, #3b82f6) 18%, transparent);
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
    }
    .rd-kpi {
        border: 0;
        border-radius: 14px;
        box-shadow: 0 4px 14px rgba(15,23,42,.06);
        transition: transform .15s ease, box-shadow .15s ease;
        background: var(--bs-body-bg, #fff);
    }
    .rd-kpi:hover { transform: translateY(-2px); box-shadow: 0 8px 22px rgba(15,23,42,.1); }
    .rd-kpi .kpi-label { font-size: .75rem; color: var(--bs-secondary-color); letter-spacing: .02em; }
    .rd-kpi .kpi-val { font-size: 1.15rem; font-weight: 700; line-height: 1.3; }
    .rd-group {
        border: 0;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(15,23,42,.06);
        overflow: hidden;
        height: 100%;
        background: var(--bs-body-bg, #fff);
    }
    .rd-group .card-header {
        background: color-mix(in srgb, var(--aoi-primary, #3b82f6) 8%, transparent);
        border-bottom: 1px solid color-mix(in srgb, var(--aoi-primary, #3b82f6) 12%, transparent);
        padding: .85rem 1.1rem;
    }
    .rd-group .card-header h6 { margin: 0; font-weight: 700; font-size: .95rem; }
    .rd-group .table { margin: 0; }
    .rd-group .table th {
        width: 40%;
        font-weight: 500;
        color: var(--bs-secondary-color);
        font-size: .82rem;
        padding: .65rem 1rem;
        vertical-align: top;
        border-color: rgba(15,23,42,.06);
    }
    .rd-group .table td {
        padding: .65rem 1rem;
        font-size: .9rem;
        border-color: rgba(15,23,42,.06);
        word-break: break-word;
    }
    .rd-group .table tr:last-child th,
    .rd-group .table tr:last-child td { border-bottom: 0; }
    .rd-empty { color: var(--bs-secondary-color); opacity: .7; }
    .rd-coords {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        background: color-mix(in srgb, var(--aoi-primary, #3b82f6) 10%, transparent);
        border: 1px solid color-mix(in srgb, var(--aoi-primary, #3b82f6) 18%, transparent);
        border-radius: 999px;
        padding: .2rem .65rem;
        font-size: .78rem;
    }
    .rd-raw pre {
        max-height: 360px;
        overflow: auto;
        direction: ltr;
        text-align: left;
        font-size: .78rem;
        border-radius: 12px;
        background: #0f172a0a;
        padding: 1rem;
        margin: 0;
    }
    [data-bs-theme="dark"] .rd-raw pre,
    .theme-dark .rd-raw pre { background: rgba(255,255,255,.05); }
</style>
@endpush

@section('content')

{{-- Hero --}}
<div class="rd-hero mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="bi bi-file-earmark-text text-primary me-2"></i>
                {{ $record->unit_name ?: ($record->unit_code ?: ('سجل #'.$record->id)) }}
            </h4>
            <div class="text-muted small d-flex flex-wrap align-items-center gap-2 mt-1">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $module->code ?? '' }}</span>
                <span>{{ $module->name_ar ?? '' }}</span>
                @if(!empty($record->unit_code))
                    <span><i class="bi bi-hash"></i> {{ $record->unit_code }}</span>
                @endif
                @if(!empty($record->x_coordinate) && !empty($record->y_coordinate))
                    <span class="rd-coords">
                        <i class="bi bi-geo-alt-fill text-danger"></i>
                        {{ is_numeric($record->x_coordinate) ? number_format((float)$record->x_coordinate, 5) : $record->x_coordinate }},
                        {{ is_numeric($record->y_coordinate) ? number_format((float)$record->y_coordinate, 5) : $record->y_coordinate }}
                    </span>
                @endif
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.excel-modules.show', $module) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-right me-1"></i> رجوع للجدول
            </a>
        </div>
    </div>
</div>

{{-- Quick KPIs --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card rd-kpi h-100">
            <div class="card-body py-3">
                <div class="kpi-label">رقم السجل</div>
                <div class="kpi-val">#{{ $record->id }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card rd-kpi h-100">
            <div class="card-body py-3">
                <div class="kpi-label">كود الوحدة</div>
                <div class="kpi-val text-truncate">{{ $record->unit_code ?: '—' }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card rd-kpi h-100">
            <div class="card-body py-3">
                <div class="kpi-label">عدد الحقول</div>
                <div class="kpi-val">{{ is_countable($data ?? []) ? count($data) : 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card rd-kpi h-100">
            <div class="card-body py-3">
                <div class="kpi-label">آخر تحديث</div>
                <div class="kpi-val" style="font-size:1rem">{{ $record->updated_at?->format('Y-m-d H:i') ?? '—' }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Grouped fields (no map) --}}
@php
    $iconMap = [
        'أساسي' => 'bi-info-circle text-primary',
        'أساسيات' => 'bi-info-circle text-primary',
        'موقع' => 'bi-pin-map text-danger',
        'موقع / إحداثيات' => 'bi-pin-map text-danger',
        'سعات / أرقام' => 'bi-bar-chart text-success',
        'أرقام' => 'bi-bar-chart text-success',
        'حالة / تواريخ' => 'bi-calendar3 text-warning',
        'تواريخ' => 'bi-calendar3 text-warning',
        'تشغيل' => 'bi-gear text-info',
        'اتصال' => 'bi-telephone text-primary',
        'أخرى' => 'bi-list-ul text-secondary',
    ];
@endphp

<div class="row g-4">
@forelse(($groups ?? []) as $groupName => $fields)
    <div class="col-12 col-lg-6">
        <div class="card rd-group">
            <div class="card-header">
                <h6>
                    <i class="bi {{ $iconMap[$groupName] ?? 'bi-list-ul text-secondary' }} me-2"></i>
                    {{ $groupName }}
                    <span class="badge bg-light text-muted border ms-1">{{ count($fields) }}</span>
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-borderless mb-0 align-middle">
                        <tbody>
                        @foreach($fields as $key => $value)
                            <tr>
                                <th>{{ str_replace('_', ' ', $key) }}</th>
                                <td>
                                    @if(is_null($value) || $value === '')
                                        <span class="rd-empty">—</span>
                                    @elseif(is_array($value))
                                        <code class="small">{{ json_encode($value, JSON_UNESCAPED_UNICODE) }}</code>
                                    @elseif(is_numeric($value) && !str_contains((string)$key, 'كود') && !str_contains(strtolower((string)$key), 'code'))
                                        <span class="fw-semibold">{{ $value }}</span>
                                    @else
                                        {{ $value }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="col-12">
        <div class="alert alert-light border text-center py-4">لا توجد حقول لعرضها</div>
    </div>
@endforelse
</div>

{{-- Raw JSON --}}
<div class="card rd-group mt-4">
    <div class="card-header">
        <a class="text-decoration-none text-dark d-flex justify-content-between align-items-center"
           data-bs-toggle="collapse" href="#rawJson" role="button" aria-expanded="false">
            <span><i class="bi bi-braces me-2"></i>البيانات الخام (JSON)</span>
            <i class="bi bi-chevron-down"></i>
        </a>
    </div>
    <div class="collapse" id="rawJson">
        <div class="card-body rd-raw">
            <pre>{{ json_encode($data ?? [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
        </div>
    </div>
</div>
@endsection
