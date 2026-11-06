@extends('layouts.admin')

@section('title', 'معاينة الاستيراد')
@section('page_title', 'معاينة الاستيراد')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.excel-modules.index') }}">موديولات Excel</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.excel-modules.create') }}">رفع ملف</a></li>
<li class="breadcrumb-item active">معاينة</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h4 class="mb-1 fw-bold">معاينة قبل الاستيراد</h4>
        <p class="text-muted mb-0 small">{{ $originalName }}</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-3">
                <div class="text-muted small">الموديول</div>
                <div class="fw-semibold">{{ $preview['module_name_ar'] ?? '—' }}</div>
                <div class="small text-primary">{{ $preview['module_code'] ?? '' }}</div>
                <div class="small text-muted">→ <code>{{ $preview['table'] ?? '' }}</code></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-3">
                <div class="text-muted small">إجمالي الصفوف</div>
                <div class="fs-4 fw-bold">{{ number_format($preview['total_rows'] ?? 0) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-3">
                <div class="text-muted small">الأعمدة</div>
                <div class="fs-4 fw-bold">{{ count($preview['headers'] ?? []) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-3">
                <div class="text-muted small">إصدار القالب</div>
                <div class="fw-semibold small">{{ $preview['template_version'] ?? '—' }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-eye me-2 text-primary"></i>
        عينة من البيانات (أول {{ count($preview['preview_rows'] ?? []) }} صف)
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        @foreach(array_slice($preview['headers'] ?? [], 0, 10) as $h)
                            <th class="text-nowrap small">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($preview['preview_rows'] ?? [] as $row)
                        <tr>
                            @foreach(array_slice($preview['headers'] ?? [], 0, 10) as $i => $h)
                                <td class="small text-nowrap" style="max-width:160px;overflow:hidden;text-overflow:ellipsis">
                                    {{ is_array($row[$i] ?? null) ? json_encode($row[$i], JSON_UNESCAPED_UNICODE) : ($row[$i] ?? '—') }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
    </div>
@endif

<form method="POST" action="{{ route('admin.excel-modules.import') }}" class="card border-0 shadow-sm">
    @csrf
    <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="replace_existing" value="1" id="replace" checked>
            <label class="form-check-label" for="replace">
                استبدال البيانات القديمة لنفس الموديول (إن وجدت)
            </label>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.excel-modules.create') }}" class="btn btn-outline-secondary">إلغاء</a>
            <button type="submit" class="btn btn-success btn-lg">
                <i class="bi bi-check2-circle me-1"></i> تأكيد الاستيراد
            </button>
        </div>
    </div>
</form>
@endsection
