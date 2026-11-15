
@extends('layouts.admin')

@section('title', 'إدارة خطوط الإنتاج')
@section('page_title', 'إدارة خطوط الإنتاج')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">
        إدارة خطوط الإنتاج
    </li>
@endsection

@section('content')
<div class="container-fluid py-4">

{{-- Upload Success --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        {{ session('success') }}

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Upload Error --}}
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i>
        {{ session('error') }}

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Validation Errors --}}
@if($errors->any())
    <div class="alert alert-danger">
        <strong>تعذر رفع الملف:</strong>

        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    {{-- Deleted Records Alert --}}
    @if($showDeleted)
        <div class="alert alert-warning d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <i class="bi bi-trash me-2"></i>
                <strong>أنت تعرض السجلات المحذوفة مؤقتًا</strong>
                <div class="small mt-1">
                    هذه السجلات لم تُحذف نهائيًا ويمكن استعادتها لاحقًا.
                </div>
            </div>

            <a href="{{ route('admin.production-lines.index') }}"
               class="btn btn-outline-dark btn-sm">
                <i class="bi bi-arrow-right me-1"></i>
                العودة للسجلات النشطة
            </a>
        </div>
    @endif

    {{-- Page Header --}}
   
{{-- Page Header --}}

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">

    <div>
        <h3 class="fw-bold mb-1">إدارة خطوط الإنتاج</h3>
        <p class="text-muted mb-0">عرض ومتابعة بيانات خطوط الإنتاج</p>
    </div>

    <div class="d-flex flex-wrap gap-2 align-items-center">

        <a href="{{ route('admin.production-lines.preview') }}"
           class="btn btn-outline-primary">
            <i class="bi bi-eye me-2"></i>
            معاينة البيانات
        </a>

        <form action="{{ route('admin.production-lines.upload-excel') }}"
              method="POST"
              enctype="multipart/form-data"
              class="d-flex flex-wrap gap-2 align-items-center">
            @csrf

            <input type="file"
                   name="excel_file"
                   class="form-control"
                   accept=".xlsx,.xls"
                   required
                   style="max-width:260px;">

            <button type="submit" class="btn btn-success">
                <i class="bi bi-cloud-arrow-up me-2"></i>
                رفع واستخراج Excel
            </button>
        </form>

    </div>
</div>



    {{-- Statistics --}}
    <div class="row g-3 mb-4">

        <div class="col-sm-6 col-xl">
            <a href="{{ route('admin.production-lines.index') }}"
               class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="text-muted">
                                    إجمالي خطوط الإنتاج
                                </div>
                                <h3 class="fw-bold mt-2 mb-0">
                                    {{ number_format($statistics['total']) }}
                                </h3>
                            </div>
                            <i class="bi bi-database fs-2 text-primary"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-xl">
            <a href="{{ route('admin.production-lines.index', ['line_status' => 'يعمل']) }}"
               class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="text-muted">خطوط تعمل</div>
                                <h3 class="fw-bold text-success mt-2 mb-0">
                                    {{ number_format($statistics['working']) }}
                                </h3>
                            </div>
                            <i class="bi bi-check-circle fs-2 text-success"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-xl">
            <a href="{{ route('admin.production-lines.index', ['line_status' => 'لا يعمل']) }}"
               class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="text-muted">خطوط لا تعمل</div>
                                <h3 class="fw-bold text-danger mt-2 mb-0">
                                    {{ number_format($statistics['stopped']) }}
                                </h3>
                            </div>
                            <i class="bi bi-pause-circle fs-2 text-danger"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-xl">
            <a href="{{ route('admin.production-lines.index', ['line_status' => 'تحت التطوير']) }}"
               class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="text-muted">تحت التطوير</div>
                                <h3 class="fw-bold text-warning mt-2 mb-0">
                                    {{ number_format($statistics['under_development']) }}
                                </h3>
                            </div>
                            <i class="bi bi-tools fs-2 text-warning"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-xl">
            <a href="{{ route('admin.production-lines.index', ['deleted' => 1]) }}"
               class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="text-muted">محذوفة مؤقتًا</div>
                                <h3 class="fw-bold text-secondary mt-2 mb-0">
                                    {{ number_format($statistics['deleted']) }}
                                </h3>
                            </div>
                            <i class="bi bi-trash fs-2 text-secondary"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

    </div>

    {{-- Search and Filters --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-funnel me-2"></i>
                البحث والفلاتر
            </h5>
        </div>

        <div class="card-body">

            <form method="GET"
                  action="{{ route('admin.production-lines.index') }}">

                @if($showDeleted)
                    <input type="hidden" name="deleted" value="1">
                @endif

                <div class="row g-3">

                    {{-- General Search --}}
                    <div class="col-lg-6 col-md-12">
                        <label class="form-label">بحث شامل</label>
                        <input type="text"
                               name="search"
                               class="form-control"
                               value="{{ request('search') }}"
                               placeholder="ابحث باسم الخط أو المنتج أو الشركة أو أي بيان...">
                    </div>

                    {{-- Unit --}}
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">الوحدة</label>

                        <select name="unit_id" class="form-select">
                            <option value="">كل الوحدات</option>

                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}"
                                    @selected((string) request('unit_id') === (string) $unit->id)>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Governorate --}}
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">المحافظة</label>

                        <select name="governorate" class="form-select">
                            <option value="">كل المحافظات</option>

                            @foreach($governorates as $governorate)
                                <option value="{{ $governorate }}"
                                    @selected(request('governorate') === $governorate)>
                                    {{ $governorate }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Owner Organization --}}
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">الجهة المالكة</label>

                        <input type="text"
                               name="owner_organization"
                               class="form-control"
                               value="{{ request('owner_organization') }}"
                               placeholder="الجهة المالكة">
                    </div>

                    {{-- Location Scope --}}
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">نطاق الموقع</label>

                        <input type="text"
                               name="location_scope"
                               class="form-control"
                               value="{{ request('location_scope') }}"
                               placeholder="نطاق الموقع">
                    </div>

                    {{-- Line Status --}}
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">حالة الخط</label>

                        <select name="line_status" class="form-select">
                            <option value="">كل الحالات</option>

                            @foreach([
                                'يعمل',
                                'لا يعمل',
                                'تحت التطوير',
                                'تحت التجهيز والتشغيل التجريبي'
                            ] as $lineStatus)
                                <option value="{{ $lineStatus }}"
                                    @selected(request('line_status') === $lineStatus)>
                                    {{ $lineStatus }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Operating Status --}}
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">حالة التشغيل</label>

                        <input type="text"
                               name="operating_status"
                               class="form-control"
                               value="{{ request('operating_status') }}"
                               placeholder="حالة التشغيل">
                    </div>

                    {{-- Readiness Level --}}
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">مستوى الجاهزية</label>

                        <input type="text"
                               name="readiness_level"
                               class="form-control"
                               value="{{ request('readiness_level') }}"
                               placeholder="مستوى الجاهزية">
                    </div>

                    {{-- Development Type --}}
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">نوع التطوير</label>

                        <input type="text"
                               name="development_type"
                               class="form-control"
                               value="{{ request('development_type') }}"
                               placeholder="نوع التطوير">
                    </div>

                    {{-- Operating Year --}}
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">سنة التشغيل</label>

                        <input type="text"
                               name="operating_year"
                               class="form-control"
                               value="{{ request('operating_year') }}"
                               placeholder="سنة التشغيل">
                    </div>

                    {{-- Sorting --}}
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">ترتيب حسب</label>

                        <select name="sort" class="form-select">
                            <option value="created_at"
                                @selected(request('sort', 'created_at') === 'created_at')>
                                تاريخ الإضافة
                            </option>

                            <option value="name"
                                @selected(request('sort') === 'name')>
                                اسم خط الإنتاج
                            </option>

                            <option value="operating_year"
                                @selected(request('sort') === 'operating_year')>
                                سنة التشغيل
                            </option>
                        </select>
                    </div>

                    {{-- Direction --}}
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">اتجاه الترتيب</label>

                        <select name="direction" class="form-select">
                            <option value="desc"
                                @selected(request('direction', 'desc') === 'desc')>
                                تنازلي
                            </option>

                            <option value="asc"
                                @selected(request('direction') === 'asc')>
                                تصاعدي
                            </option>
                        </select>
                    </div>

                    {{-- Buttons --}}
                    <div class="col-12">
                        <div class="d-flex flex-wrap gap-2">

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>
                                تطبيق البحث والفلاتر
                            </button>

                            <a href="{{ $showDeleted
                                ? route('admin.production-lines.index', ['deleted' => 1])
                                : route('admin.production-lines.index') }}"
                               class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-counterclockwise me-1"></i>
                                إعادة ضبط
                            </a>

                        </div>
                    </div>

                </div>
            </form>

        </div>
    </div>

    {{-- Production Lines Table --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">

            <h5 class="fw-bold mb-0">
                <i class="bi bi-table me-2"></i>
                {{ $showDeleted ? 'السجلات المحذوفة مؤقتًا' : 'قائمة خطوط الإنتاج' }}
            </h5>

            <span class="text-muted small">
                عدد النتائج: {{ number_format($productionLines->total()) }}
            </span>

        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th class="text-center">#</th>
                            <th>اسم خط الإنتاج</th>
                            <th>اسم المنتج</th>
                            <th>الوحدة</th>
                            <th>المحافظة</th>
                            <th>سنة التشغيل</th>
                            <th>حالة الخط</th>
                            <th>حالة التشغيل</th>
                            <th>مستوى الجاهزية</th>
                            <th class="text-center">التفاصيل</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($productionLines as $productionLine)

                            <tr>

                                <td class="text-center text-muted">
                                    {{ $productionLines->firstItem() + $loop->index }}
                                </td>

                                <td style="min-width: 200px;">
                                    <strong>
                                        {{ filled($productionLine->name)
                                            ? \Illuminate\Support\Str::limit($productionLine->name, 70)
                                            : 'غير محدد' }}
                                    </strong>
                                </td>

                                <td style="min-width: 150px;">
                                    {{ filled($productionLine->product_name)
                                        ? \Illuminate\Support\Str::limit($productionLine->product_name, 55)
                                        : '-' }}
                                </td>

                                <td>
                                    {{ $productionLine->unit?->name ?? '-' }}
                                </td>

                                <td>
                                    {{ $productionLine->governorate ?: '-' }}
                                </td>

                                <td>
                                    {{ $productionLine->operating_year ?: '-' }}
                                </td>

                                <td>
                                    @php
                                        $lineStatusClass = match($productionLine->line_status) {
                                            'يعمل' => 'bg-success',
                                            'لا يعمل' => 'bg-danger',
                                            'تحت التطوير' => 'bg-warning text-dark',
                                            default => 'bg-secondary'
                                        };
                                    @endphp

                                    @if($productionLine->line_status)
                                        <span class="badge {{ $lineStatusClass }}">
                                            {{ $productionLine->line_status }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td>
                                    {{ $productionLine->operating_status ?: '-' }}
                                </td>

                                <td>
                                    {{ $productionLine->readiness_level ?: '-' }}
                                </td>

                                <td class="text-center">
                                    <a href="{{ route('admin.production-lines.show', $productionLine->id) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       title="عرض التفاصيل">
                                        <i class="bi bi-eye"></i>
                                        عرض
                                    </a>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="10" class="text-center py-5">

                                    <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>

                                    <h6 class="text-muted mb-1">
                                        لا توجد خطوط إنتاج لعرضها
                                    </h6>

                                    <p class="text-muted small mb-0">
                                        لا توجد سجلات مطابقة للبحث أو الفلاتر المحددة.
                                    </p>

                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>
        </div>

        {{-- Pagination --}}
        @if($productionLines->hasPages())
            <div class="card-footer bg-white py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                    <div class="text-muted small">
                        عرض
                        {{ $productionLines->firstItem() ?? 0 }}
                        إلى
                        {{ $productionLines->lastItem() ?? 0 }}
                        من
                        {{ number_format($productionLines->total()) }}
                        سجل
                    </div>

                    <div>
                        {{ $productionLines->withQueryString()->links('pagination::bootstrap-5') }}
                    </div>

                </div>
            </div>
        @endif

    </div>

</div>
@endsection
