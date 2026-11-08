@extends('layouts.admin')

@section('title', 'تفاصيل السجل')
@section('page_title', 'تفاصيل السجل')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.excel-modules.index') }}">موديولات Excel</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.excel-modules.show', $module) }}">{{ $module->name_ar }}</a></li>
<li class="breadcrumb-item active">سجل #{{ $record->id }}</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <h4 class="mb-1 fw-bold">
            <i class="bi bi-file-earmark-text text-primary me-2"></i>
            {{ $record->unit_name ?: ($record->unit_code ?: 'سجل #'.$record->id) }}
        </h4>
        <div class="text-muted small">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle me-1">{{ $module->code }}</span>
            <span class="me-2">{{ $module->name_ar }}</span>
            @if($record->unit_code)
                <span class="me-2"><i class="bi bi-hash"></i> {{ $record->unit_code }}</span>
            @endif
            @if($record->x_coordinate && $record->y_coordinate)
                <span><i class="bi bi-geo-alt"></i> {{ number_format($record->x_coordinate, 6) }}, {{ number_format($record->y_coordinate, 6) }}</span>
            @endif
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.excel-modules.show', $module) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-right me-1"></i> رجوع للجدول
        </a>
    </div>
</div>

{{-- Quick stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3">
                <div class="text-muted small">رقم السجل</div>
                <div class="fs-5 fw-semibold">#{{ $record->id }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3">
                <div class="text-muted small">كود الوحدة</div>
                <div class="fs-5 fw-semibold text-truncate">{{ $record->unit_code ?: '—' }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3">
                <div class="text-muted small">عدد الحقول</div>
                <div class="fs-5 fw-semibold">{{ count($data) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3">
                <div class="text-muted small">آخر تحديث</div>
                <div class="fs-6 fw-semibold">{{ $record->updated_at?->format('Y-m-d H:i') }}</div>
            </div>
        </div>
    </div>
</div>

@if($record->x_coordinate && $record->y_coordinate)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 pt-3 pb-0">
        <h6 class="mb-0"><i class="bi bi-geo-alt-fill text-danger me-2"></i>الموقع على الخريطة</h6>
    </div>
    <div class="card-body">
        <div class="ratio ratio-21x9 rounded overflow-hidden border" style="min-height:220px">
            <iframe
                src="https://maps.google.com/maps?q={{ $record->y_coordinate }},{{ $record->x_coordinate }}&z=14&output=embed"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                style="border:0" allowfullscreen></iframe>
        </div>
        <div class="small text-muted mt-2">
            X: {{ $record->x_coordinate }} &nbsp;|&nbsp; Y: {{ $record->y_coordinate }}
            &nbsp;·&nbsp;
            <a href="https://www.google.com/maps?q={{ $record->y_coordinate }},{{ $record->x_coordinate }}" target="_blank" rel="noopener">
                فتح في Google Maps <i class="bi bi-box-arrow-up-left"></i>
            </a>
        </div>
    </div>
</div>
@endif

{{-- Grouped fields --}}
<div class="row g-4">
@foreach($groups as $groupName => $fields)
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom-0 pt-3">
                <h6 class="mb-0 fw-semibold">
                    @switch($groupName)
                        @case('أساسي')
                            <i class="bi bi-info-circle text-primary me-2"></i>
                            @break
                        @case('موقع / إحداثيات')
                            <i class="bi bi-pin-map text-danger me-2"></i>
                            @break
                        @case('سعات / أرقام')
                            <i class="bi bi-bar-chart text-success me-2"></i>
                            @break
                        @case('حالة / تواريخ')
                            <i class="bi bi-calendar3 text-warning me-2"></i>
                            @break
                        @default
                            <i class="bi bi-list-ul text-secondary me-2"></i>
                    @endswitch
                    {{ $groupName }}
                    <span class="badge bg-light text-muted border ms-1">{{ count($fields) }}</span>
                </h6>
            </div>
            <div class="card-body pt-2">
                <div class="table-responsive">
                    <table class="table table-sm table-borderless mb-0 align-middle">
                        <tbody>
                        @foreach($fields as $key => $value)
                            <tr class="border-bottom">
                                <th class="text-muted fw-normal py-2 pe-3" style="width:42%; white-space:nowrap">
                                    {{ str_replace('_', ' ', $key) }}
                                </th>
                                <td class="py-2">
                                    @if(is_null($value) || $value === '')
                                        <span class="text-muted">—</span>
                                    @elseif(is_array($value))
                                        <code class="small">{{ json_encode($value, JSON_UNESCAPED_UNICODE) }}</code>
                                    @elseif(is_numeric($value) && !str_contains((string)$key, 'كود') && !str_contains((string)$key, 'code'))
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
@endforeach
</div>

{{-- Raw JSON accordion --}}
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white">
        <a class="text-decoration-none text-dark d-flex justify-content-between align-items-center"
           data-bs-toggle="collapse" href="#rawJson" role="button" aria-expanded="false">
            <span><i class="bi bi-braces me-2"></i>البيانات الخام (JSON)</span>
            <i class="bi bi-chevron-down"></i>
        </a>
    </div>
    <div class="collapse" id="rawJson">
        <div class="card-body">
            <pre class="bg-light p-3 rounded small mb-0" style="max-height:400px;overflow:auto;direction:ltr;text-align:left">{{ json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
        </div>
    </div>
</div>
@endsection
