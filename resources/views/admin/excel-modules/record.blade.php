@extends('layouts.admin')

@section('title', 'تفاصيل السجل')
@section('page_title', 'تفاصيل السجل')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.excel-modules.index') }}">موديولات Excel</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.excel-modules.show', $module) }}">{{ $module->name_ar }}</a></li>
<li class="breadcrumb-item active">سجل #{{ $record->id }}</li>
@endsection

@section('content')
{{-- Header --}}
<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <h4 class="mb-1 fw-bold d-flex align-items-center gap-2">
            <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-primary bg-opacity-10 text-primary" style="width:42px;height:42px">
                <i class="bi bi-file-earmark-text fs-5"></i>
            </span>
            <span>{{ $record->unit_name ?: ($record->unit_code ?: ('سجل #'.$record->id)) }}</span>
        </h4>
        <div class="text-muted small d-flex flex-wrap align-items-center gap-2 mt-1">
            <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-2">{{ $module->code }}</span>
            <span>{{ $module->name_ar }}</span>
            @if($record->unit_code)
                <span class="text-secondary"><i class="bi bi-hash"></i> {{ $record->unit_code }}</span>
            @endif
            @if(!empty($record->operating_status) || !empty($data['حالة التشغيل'] ?? null))
                @php $st = $record->operating_status ?? ($data['حالة التشغيل'] ?? null); @endphp
                <span class="badge rounded-pill {{ str_contains((string)$st, 'يعمل') || str_contains((string)$st, 'تعمل') ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} border">
                    {{ $st }}
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

{{-- KPI strip --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100 record-kpi">
            <div class="card-body py-3 d-flex align-items-center gap-3">
                <div class="kpi-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-hash"></i></div>
                <div>
                    <div class="text-muted small">رقم السجل</div>
                    <div class="fs-5 fw-bold">#{{ $record->id }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100 record-kpi">
            <div class="card-body py-3 d-flex align-items-center gap-3">
                <div class="kpi-icon bg-info bg-opacity-10 text-info"><i class="bi bi-building"></i></div>
                <div class="min-w-0">
                    <div class="text-muted small">كود الوحدة</div>
                    <div class="fs-6 fw-semibold text-truncate">{{ $record->unit_code ?: '—' }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100 record-kpi">
            <div class="card-body py-3 d-flex align-items-center gap-3">
                <div class="kpi-icon bg-success bg-opacity-10 text-success"><i class="bi bi-list-check"></i></div>
                <div>
                    <div class="text-muted small">عدد الحقول</div>
                    <div class="fs-5 fw-bold">{{ count($data) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100 record-kpi">
            <div class="card-body py-3 d-flex align-items-center gap-3">
                <div class="kpi-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-clock-history"></i></div>
                <div>
                    <div class="text-muted small">آخر تحديث</div>
                    <div class="fs-6 fw-semibold">{{ $record->updated_at?->format('Y-m-d H:i') ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Coordinates as compact info (no map) --}}
@if($record->x_coordinate && $record->y_coordinate)
<div class="alert alert-light border d-flex flex-wrap align-items-center gap-3 mb-4 py-2">
    <i class="bi bi-geo-alt-fill text-danger"></i>
    <span class="small text-muted">الإحداثيات:</span>
    <code class="small">X: {{ $record->x_coordinate }}</code>
    <code class="small">Y: {{ $record->y_coordinate }}</code>
    <a href="https://www.google.com/maps?q={{ $record->y_coordinate }},{{ $record->x_coordinate }}"
       target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary ms-auto">
        فتح في Google Maps <i class="bi bi-box-arrow-up-left"></i>
    </a>
</div>
@endif

{{-- Grouped fields — full width stacked for readability --}}
<div class="record-groups">
@foreach($groups as $groupName => $fields)
    @php
        $icon = match($groupName) {
            'أساسي', 'بيانات أساسية', 'أساسية' => 'bi-info-circle text-primary',
            'موقع / إحداثيات', 'الموقع', 'موقع' => 'bi-pin-map text-danger',
            'سعات / أرقام', 'أرقام', 'سعات' => 'bi-bar-chart text-success',
            'حالة / تواريخ', 'حالة', 'تواريخ' => 'bi-calendar3 text-warning',
            'تشغيل', 'الإنتاج' => 'bi-gear text-info',
            'اتصال', 'اتصالات' => 'bi-telephone text-primary',
            default => 'bi-list-ul text-secondary',
        };
        $color = match(true) {
            str_contains($icon, 'primary') => 'primary',
            str_contains($icon, 'danger') => 'danger',
            str_contains($icon, 'success') => 'success',
            str_contains($icon, 'warning') => 'warning',
            str_contains($icon, 'info') => 'info',
            default => 'secondary',
        };
    @endphp
    <div class="card border-0 shadow-sm mb-3 record-group-card">
        <div class="card-header bg-white border-0 pt-3 pb-2 d-flex align-items-center gap-2">
            <span class="d-inline-flex align-items-center justify-content-center rounded-2 bg-{{ $color }} bg-opacity-10 text-{{ $color }}" style="width:32px;height:32px">
                <i class="bi {{ $icon }} small"></i>
            </span>
            <h6 class="mb-0 fw-semibold">{{ $groupName }}</h6>
            <span class="badge bg-light text-muted border ms-1">{{ count($fields) }}</span>
        </div>
        <div class="card-body pt-0">
            <div class="row g-0">
                @foreach($fields as $key => $value)
                    <div class="col-12 col-md-6 border-bottom py-2 px-2 field-row">
                        <div class="d-flex flex-column flex-sm-row gap-1 gap-sm-3">
                            <div class="text-muted small fw-medium field-label" style="min-width:140px">
                                {{ str_replace('_', ' ', $key) }}
                            </div>
                            <div class="field-value flex-grow-1">
                                @if(is_null($value) || $value === '' || $value === '—')
                                    <span class="text-muted">—</span>
                                @elseif(is_array($value))
                                    <code class="small text-break">{{ json_encode($value, JSON_UNESCAPED_UNICODE) }}</code>
                                @elseif(is_numeric($value) && !str_contains(mb_strtolower((string)$key), 'كود') && !str_contains(mb_strtolower((string)$key), 'code') && !str_contains(mb_strtolower((string)$key), 'رقم'))
                                    <span class="fw-semibold text-dark">{{ is_float($value + 0) ? number_format((float)$value, 2) : number_format((int)$value) }}</span>
                                @else
                                    <span class="text-break">{{ $value }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endforeach
</div>

{{-- Raw JSON (collapsed) --}}
<div class="card border-0 shadow-sm mt-2">
    <div class="card-header bg-white py-2">
        <a class="text-decoration-none text-dark d-flex justify-content-between align-items-center small"
           data-bs-toggle="collapse" href="#rawJson" role="button" aria-expanded="false">
            <span><i class="bi bi-braces me-2 text-secondary"></i>البيانات الخام (JSON)</span>
            <i class="bi bi-chevron-down"></i>
        </a>
    </div>
    <div class="collapse" id="rawJson">
        <div class="card-body pt-0">
            <pre class="bg-light p-3 rounded small mb-0" style="max-height:360px;overflow:auto;direction:ltr;text-align:left">{{ json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.record-kpi .kpi-icon {
    width: 40px; height: 40px; border-radius: 10px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1.1rem; flex-shrink: 0;
}
.record-group-card { transition: box-shadow .2s ease; }
.record-group-card:hover { box-shadow: 0 .5rem 1.25rem rgba(0,0,0,.08) !important; }
.field-row:hover { background: rgba(13,110,253,.03); }
.field-label { letter-spacing: .01em; }
@media (max-width: 575.98px) {
    .field-label { min-width: 0 !important; }
}
</style>
@endpush
