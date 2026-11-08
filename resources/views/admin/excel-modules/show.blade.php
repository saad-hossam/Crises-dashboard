@extends('layouts.admin')

@section('title', $module->name_ar)
@section('page_title', $module->name_ar)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.excel-modules.index') }}">موديولات Excel</a></li>
<li class="breadcrumb-item active">{{ $module->name_ar }}</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <h4 class="mb-1 fw-bold">{{ $module->name_ar }}</h4>
        <div class="text-muted small">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $module->code }}</span>
            <span class="ms-2">{{ number_format($module->records_count ?? $records->total()) }} سجل</span>
            @if($module->last_imported_at)
                <span class="ms-2">· آخر استيراد {{ $module->last_imported_at->diffForHumans() }}</span>
            @endif
        </div>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.excel-modules.export', ['module' => $module->id] + request()->query()) }}"
           class="btn btn-success">
            <i class="bi bi-file-earmark-excel me-1"></i> تصدير Excel
        </a>
        <a href="{{ route('admin.excel-modules.create') }}" class="btn btn-primary">
            <i class="bi bi-upload me-1"></i> استيراد جديد
        </a>
        <a href="{{ route('admin.excel-modules.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-grid me-1"></i> الموديولات
        </a>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.excel-modules.show', $module) }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">بحث عام</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                           placeholder="ابحث في أي عمود...">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">كود الوحدة</label>
                <input type="text" name="unit_code" value="{{ request('unit_code') }}" class="form-control"
                       placeholder="مثال: U-001">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">لكل صفحة</label>
                <select name="per_page" class="form-select">
                    @foreach([25, 50, 100, 200] as $n)
                        <option value="{{ $n }}" @selected(request('per_page', 50) == $n)>{{ $n }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary flex-fill" type="submit">
                    <i class="bi bi-funnel me-1"></i> تطبيق
                </button>
                <a href="{{ route('admin.excel-modules.show', $module) }}" class="btn btn-outline-secondary">
                    إعادة
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Data table --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <span class="fw-semibold">
            <i class="bi bi-table me-2 text-primary"></i>
            البيانات
            <span class="badge bg-light text-dark border ms-1">{{ number_format($records->total()) }}</span>
        </span>
        <span class="text-muted small">اضغط على صف لعرض التفاصيل</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm align-middle mb-0 excel-data-table">
                <thead class="table-light sticky-top">
                    <tr>
                        <th class="ps-3" style="width:48px">#</th>
                        @foreach(array_slice($headers ?? [], 0, 8) as $h)
                            <th class="text-nowrap">{{ str_replace('_', ' ', $h) }}</th>
                        @endforeach
                        <th class="text-center pe-3" style="width:90px">تفاصيل</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($records as $record)
                    <tr class="record-row" style="cursor:pointer"
                        onclick="window.location='{{ route('admin.excel-modules.records.show', [$module, $record]) }}'">
                        <td class="ps-3 text-muted small">{{ $record->id }}</td>
                        @foreach(array_slice($headers ?? [], 0, 8) as $h)
                            <td class="text-nowrap" style="max-width:180px;overflow:hidden;text-overflow:ellipsis">
                                @php $val = $record->data[$h] ?? null; @endphp
                                @if(is_null($val) || $val === '')
                                    <span class="text-muted">—</span>
                                @else
                                    {{ is_array($val) ? json_encode($val, JSON_UNESCAPED_UNICODE) : \Illuminate\Support\Str::limit((string)$val, 40) }}
                                @endif
                            </td>
                        @endforeach
                        <td class="text-center pe-3" onclick="event.stopPropagation()">
                            <a href="{{ route('admin.excel-modules.records.show', [$module, $record]) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            لا توجد سجلات. <a href="{{ route('admin.excel-modules.create') }}">استورد ملفاً</a>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($records->hasPages())
        <div class="card-footer bg-white">
            {{ $records->withQueryString()->links() }}
        </div>
    @endif
</div>

@push('styles')
<style>
    .excel-data-table tbody tr.record-row:hover { background-color: rgba(13,110,253,.06); }
    .excel-data-table thead th { font-size: .8rem; font-weight: 600; white-space: nowrap; }
    .excel-data-table tbody td { font-size: .875rem; }
</style>
@endpush
@endsection
