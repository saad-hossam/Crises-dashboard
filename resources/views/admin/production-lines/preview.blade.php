
@extends('layouts.admin')

@section('title', 'معاينة استيراد خطوط الإنتاج')
@section('page_title', 'معاينة استيراد خطوط الإنتاج')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.production-lines.index') }}">
            خطوط الإنتاج
        </a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">
        معاينة ملف Excel
    </li>
@endsection

@section('content')

@php
    $pendingUpload = session('production_lines_upload');

    $readyToImport = (int) ($validationSummary['ready_to_import'] ?? 0);
    $blockedCount = (int) ($counts['blocked'] ?? 0);

    $canImport = is_array($pendingUpload)
        && !empty($pendingUpload['stored_path'])
        && $readyToImport > 0
        && $blockedCount === 0;

    $statusLabels = [
        'valid' => 'سليم',
        'warning' => 'تحذير',
        'blocked' => 'مرفوض',
    ];

    $statusClasses = [
        'valid' => 'bg-success',
        'warning' => 'bg-warning text-dark',
        'blocked' => 'bg-danger',
    ];

    $fieldLabels = [
        'owner_organization' => 'الجهة المالكة',
        'location_scope' => 'نطاق الموقع',
        'governorate' => 'المحافظة',
        'name' => 'اسم خط الإنتاج',
        'product_name' => 'اسم المنتج',
        'manufacturer' => 'الشركة المصنعة للخط',
        'operating_year' => 'سنة التشغيل',
        'design_capacity' => 'الطاقة التصميمية',
        'capacity_unit' => 'وحدة الطاقة',
        'current_operational_capacity' => 'الطاقة التشغيلية الحالية',
        'annual_production_capacity' => 'طاقة الإنتاج السنوية',
        'actual_production' => 'الإنتاج الفعلي',
        'utilization_rate' => 'نسبة الاستغلال',
        'shifts_count' => 'عدد الورديات',
        'daily_operating_hours' => 'ساعات التشغيل اليومية',
        'workers_count' => 'عدد العمال',
        'line_status' => 'حالة الخط',
        'expected_lifetime_years' => 'العمر الافتراضي بالسنوات',
        'last_maintenance_date' => 'تاريخ آخر صيانة',
        'next_maintenance_date' => 'تاريخ الصيانة القادمة',
        'production_increase_possibility' => 'إمكانية زيادة الإنتاج',
        'production_increase_lead_time_days' => 'زمن زيادة الإنتاج',
        'company_name' => 'اسم الجهة / الشركة',
        'address' => 'العنوان',
        'operating_status' => 'حالة التشغيل',
        'readiness_level' => 'مستوى الجاهزية',
        'last_updated_date' => 'تاريخ آخر تحديث',
        'data_entry_responsible' => 'مسؤول الإدخال',
        'development_type' => 'نوع التطوير',
        'development_completion_date' => 'تاريخ انتهاء التطوير',
        'x_coordinate' => 'X',
        'y_coordinate' => 'Y',
        'unit_id' => 'كود الوحدة',
    ];
@endphp

<div class="container-fluid py-4">

    {{-- =========================================================
         FLASH MESSAGES
    ========================================================== --}}

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}

            @if(session('import_batch_id'))
                <div class="mt-2">
                    رقم عملية الاستيراد:
                    <strong>#{{ session('import_batch_id') }}</strong>
                </div>
            @endif

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>
                <i class="bi bi-exclamation-triangle me-2"></i>
                تعذر تنفيذ العملية:
            </strong>

            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- =========================================================
         PAGE HEADER
    ========================================================== --}}

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-file-earmark-spreadsheet text-success me-2"></i>
                معاينة بيانات خطوط الإنتاج
            </h3>

            <p class="text-muted mb-0">
                مراجعة البيانات والتحقق منها قبل حفظها في قاعدة البيانات.
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">

            <a href="{{ route('admin.production-lines.index') }}"
               class="btn btn-outline-secondary">

                <i class="bi bi-arrow-right me-1"></i>
                العودة لخطوط الإنتاج
            </a>

            <span class="badge bg-warning text-dark d-flex align-items-center px-3">
                <i class="bi bi-eye me-1"></i>
                معاينة فقط
            </span>

        </div>
    </div>


    {{-- =========================================================
         SOURCE FILE INFORMATION
    ========================================================== --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <div class="row g-3 align-items-center">

                <div class="col-lg-8">

                    <div class="text-muted small mb-1">
                        ملف المصدر
                    </div>

                    <div class="fw-bold">
                        {{ $previewMetadata['source']['file_name'] ?? 'Production lines.xlsx' }}
                    </div>

                    <div class="text-muted small mt-2">
                        ورقة البيانات:
                        {{ $previewMetadata['source']['worksheet'] ?? 'البيانات' }}
                    </div>

                </div>

                <div class="col-lg-4 text-lg-end">

                    <div class="text-muted small">
                        عدد الأعمدة المرتبطة
                    </div>

                    <h4 class="fw-bold mb-0">
                        {{ $previewMetadata['source']['total_mapped_columns'] ?? 33 }}
                    </h4>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         SUMMARY CARDS
    ========================================================== --}}

    <div class="row g-3 mb-4">

        <div class="col-sm-6 col-xl-3">

            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <div class="text-muted small mb-2">
                                إجمالي السجلات
                            </div>

                            <h3 class="fw-bold mb-0">
                                {{ number_format($counts['total']) }}
                            </h3>
                        </div>

                        <i class="bi bi-database fs-1 text-primary"></i>

                    </div>

                </div>
            </div>

        </div>


        <div class="col-sm-6 col-xl-3">

            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <div class="text-muted small mb-2">
                                سجلات سليمة
                            </div>

                            <h3 class="fw-bold text-success mb-0">
                                {{ number_format($counts['valid']) }}
                            </h3>
                        </div>

                        <i class="bi bi-check-circle fs-1 text-success"></i>

                    </div>

                </div>
            </div>

        </div>


        <div class="col-sm-6 col-xl-3">

            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <div class="text-muted small mb-2">
                                سجلات بها تحذيرات
                            </div>

                            <h3 class="fw-bold text-warning mb-0">
                                {{ number_format($counts['warning']) }}
                            </h3>
                        </div>

                        <i class="bi bi-exclamation-triangle fs-1 text-warning"></i>

                    </div>

                </div>
            </div>

        </div>


        <div class="col-sm-6 col-xl-3">

            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <div class="text-muted small mb-2">
                                سجلات مرفوضة
                            </div>

                            <h3 class="fw-bold text-danger mb-0">
                                {{ number_format($counts['blocked']) }}
                            </h3>
                        </div>

                        <i class="bi bi-x-circle fs-1 text-danger"></i>

                    </div>

                </div>
            </div>

        </div>

    </div>


    {{-- =========================================================
         VALIDATION SUMMARY
    ========================================================== --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <h5 class="fw-bold mb-3">
                <i class="bi bi-clipboard-check me-2"></i>
                نتيجة التحقق من البيانات
            </h5>

            <div class="row g-3">

                <div class="col-md-4">
                    <div class="p-3 rounded bg-light">
                        <div class="text-muted small">
                            إجمالي الأخطاء
                        </div>

                        <h4 class="fw-bold text-danger mb-0">
                            {{ number_format($validationSummary['total_errors'] ?? 0) }}
                        </h4>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-3 rounded bg-light">
                        <div class="text-muted small">
                            إجمالي التحذيرات
                        </div>

                        <h4 class="fw-bold text-warning mb-0">
                            {{ number_format($validationSummary['total_warnings'] ?? 0) }}
                        </h4>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-3 rounded bg-light">
                        <div class="text-muted small">
                            السجلات الجاهزة للاستيراد
                        </div>

                        <h4 class="fw-bold text-success mb-0">
                            {{ number_format($readyToImport) }}
                        </h4>
                    </div>
                </div>

            </div>

            <div class="alert alert-info mt-3 mb-0">

                <i class="bi bi-info-circle me-2"></i>

                السجلات ذات التحذيرات مسموح باستيرادها وفق قواعد المشروع.
                الأسماء الفارغة لا تمنع الاستيراد، والإحداثيات المشكوك فيها
                تحتفظ بقيمها الأصلية.

            </div>

        </div>

    </div>


    {{-- =========================================================
         IMPORT CONFIRMATION
    ========================================================== --}}

    @if($pendingUpload)

        <div class="card border-success shadow-sm mb-4">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

                    <div>

                        <h5 class="fw-bold mb-2">
                            <i class="bi bi-file-earmark-excel text-success me-2"></i>
                            الملف جاهز للاستيراد
                        </h5>

                        <div class="text-muted small">
                            اسم الملف:
                            <strong>
                                {{ $pendingUpload['original_name'] ?? 'Excel file' }}
                            </strong>
                        </div>

                        <div class="text-muted small mt-1">
                            SHA-256:
                            <code>
                                {{ $pendingUpload['sha256'] ?? '-' }}
                            </code>
                        </div>

                        <div class="text-muted small mt-1">
                            السجلات الجاهزة:
                            <strong>{{ number_format($readyToImport) }}</strong>
                        </div>

                    </div>

                    <div>

                        @if($canImport)

                            <form method="POST"
                                  action="{{ route('admin.production-lines.import') }}"
                                  onsubmit="return confirm('تأكيد الاستيراد النهائي؟ سيتم حفظ السجلات في قاعدة البيانات باستراتيجية Insert Only. لا يمكن استيراد الملف نفسه مرة أخرى بعد تسجيل العملية.');">

                                @csrf

                                <button type="submit"
                                        class="btn btn-success btn-lg">

                                    <i class="bi bi-cloud-arrow-down me-2"></i>

                                    تأكيد الاستيراد إلى قاعدة البيانات

                                </button>

                            </form>

                        @else

                            <button type="button"
                                    class="btn btn-secondary btn-lg"
                                    disabled>

                                <i class="bi bi-lock me-2"></i>
                                الاستيراد غير متاح

                            </button>

                        @endif

                    </div>

                </div>

            </div>

        </div>

    @else

        <div class="alert alert-secondary mb-4">

            <i class="bi bi-info-circle me-2"></i>

            لا يوجد ملف مرفوع حاليًا ينتظر التأكيد.
            يمكنك العودة إلى إدارة خطوط الإنتاج ورفع ملف Excel جديد.

            <div class="mt-2">
                <a href="{{ route('admin.production-lines.index') }}"
                   class="btn btn-outline-primary btn-sm">

                    <i class="bi bi-upload me-1"></i>
                    رفع ملف Excel

                </a>
            </div>

        </div>

    @endif


    {{-- =========================================================
         SEARCH AND FILTERS
    ========================================================== --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">

            <h5 class="fw-bold mb-0">
                <i class="bi bi-funnel me-2"></i>
                البحث والتصفية
            </h5>

        </div>

        <div class="card-body">

            <form method="GET"
                  action="{{ route('admin.production-lines.preview') }}">

                <div class="row g-3 align-items-end">

                    <div class="col-lg-6">

                        <label class="form-label">
                            البحث في السجلات
                        </label>

                        <input type="text"
                               name="search"
                               class="form-control"
                               value="{{ $search }}"
                               placeholder="اسم الخط أو المنتج أو الشركة أو رقم الصف...">

                    </div>

                    <div class="col-lg-3">

                        <label class="form-label">
                            حالة الفحص
                        </label>

                        <select name="status" class="form-select">

                            <option value="">
                                جميع السجلات
                            </option>

                            <option value="valid"
                                @selected($status === 'valid')>
                                سليمة
                            </option>

                            <option value="warning"
                                @selected($status === 'warning')>
                                بها تحذيرات
                            </option>

                            <option value="blocked"
                                @selected($status === 'blocked')>
                                مرفوضة
                            </option>

                        </select>

                    </div>

                    <div class="col-lg-3">

                        <div class="d-flex gap-2">

                            <button type="submit"
                                    class="btn btn-primary flex-grow-1">

                                <i class="bi bi-search me-1"></i>
                                بحث

                            </button>

                            <a href="{{ route('admin.production-lines.preview') }}"
                               class="btn btn-outline-secondary">

                                <i class="bi bi-arrow-counterclockwise"></i>

                            </a>

                        </div>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- =========================================================
         RECORDS TABLE
    ========================================================== --}}

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">

            <h5 class="fw-bold mb-0">
                <i class="bi bi-table me-2"></i>
                السجلات المستخرجة
            </h5>

            <span class="text-muted small">
                عدد النتائج:
                {{ number_format($records->total()) }}
            </span>

        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>
                            <th>صف Excel</th>
                            <th>اسم خط الإنتاج</th>
                            <th>اسم المنتج</th>
                            <th>الشركة</th>
                            <th>الوحدة ID</th>
                            <th>المحافظة</th>
                            <th>حالة الفحص</th>
                            <th>الملاحظات</th>
                            <th>التفاصيل</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($records as $record)

                            @php
                                $data = $record['data'] ?? [];

                                $recordStatus = $record['status'] ?? 'blocked';

                                $warnings = is_array($record['warnings'] ?? null)
                                    ? $record['warnings']
                                    : [];

                                $errors = is_array($record['errors'] ?? null)
                                    ? $record['errors']
                                    : [];

                                $lineName = $data['name'] ?? null;
                                $productName = $data['product_name'] ?? null;
                                $companyName = $data['company_name'] ?? null;
                            @endphp

                            <tr>

                                <td class="text-muted">
                                    #{{ $record['excel_row'] ?? '-' }}
                                </td>

                                <td style="min-width: 200px;">

                                    @if(is_scalar($lineName) && $lineName !== '')
                                        <strong>{{ $lineName }}</strong>
                                    @else
                                        <span class="text-muted">غير محدد</span>
                                    @endif

                                </td>

                                <td style="min-width: 160px;">

                                    @if(is_scalar($productName) && $productName !== '')
                                        {{ \Illuminate\Support\Str::limit((string) $productName, 60) }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif

                                </td>

                                <td style="min-width: 180px;">

                                    @if(is_scalar($companyName) && $companyName !== '')
                                        {{ \Illuminate\Support\Str::limit((string) $companyName, 60) }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif

                                </td>

                                <td>
                                    {{ $data['unit_id'] ?? '-' }}
                                </td>

                                <td>
                                    {{ is_scalar($data['governorate'] ?? null) ? ($data['governorate'] ?? '-') : '-' }}
                                </td>

                                <td>

                                    <span class="badge {{ $statusClasses[$recordStatus] ?? 'bg-secondary' }}">
                                        {{ $statusLabels[$recordStatus] ?? $recordStatus }}
                                    </span>

                                </td>

                                <td style="min-width: 170px;">

                                    @if(count($errors) + count($warnings) > 0)

                                        <details>

                                            <summary class="text-primary"
                                                     style="cursor:pointer;">

                                                عرض
                                                {{ count($errors) + count($warnings) }}
                                                ملاحظات

                                            </summary>

                                            <div class="mt-2">

                                                @foreach($errors as $error)
                                                    <div class="alert alert-danger py-2 small mb-2">
                                                        <i class="bi bi-x-circle me-1"></i>
                                                        {{ $error }}
                                                    </div>
                                                @endforeach

                                                @foreach($warnings as $warning)
                                                    <div class="alert alert-warning py-2 small mb-2">
                                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                                        {{ $warning }}
                                                    </div>
                                                @endforeach

                                            </div>

                                        </details>

                                    @else

                                        <span class="text-success small">
                                            <i class="bi bi-check-circle me-1"></i>
                                            لا توجد ملاحظات
                                        </span>

                                    @endif

                                </td>

                                <td>

                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#record-details-{{ $record['excel_row'] ?? $loop->index }}"
                                            aria-expanded="false">

                                        <i class="bi bi-eye me-1"></i>
                                        التفاصيل

                                    </button>

                                </td>

                            </tr>


                            {{-- Full Record Details --}}

                            <tr class="bg-light">

                                <td colspan="9" class="p-0 border-0">

                                    <div class="collapse"
                                         id="record-details-{{ $record['excel_row'] ?? $loop->index }}">

                                        <div class="p-3">

                                            <h6 class="fw-bold mb-3">
                                                بيانات السجل رقم
                                                {{ $record['excel_row'] ?? '-' }}
                                            </h6>

                                            <div class="table-responsive">

                                                <table class="table table-sm table-bordered bg-white mb-0">

                                                    <tbody>

                                                        @foreach($data as $field => $value)

                                                            <tr>

                                                                <th style="width: 230px;">
                                                                    {{ $fieldLabels[$field] ?? $field }}
                                                                </th>

                                                                <td>

                                                                    @if($value === null || $value === '')
                                                                        <span class="text-muted">فارغ</span>

                                                                    @elseif(is_scalar($value))
                                                                        {{ $value }}

                                                                    @else
                                                                        <code>
                                                                            {{ json_encode(
                                                                                $value,
                                                                                JSON_UNESCAPED_UNICODE
                                                                                | JSON_UNESCAPED_SLASHES
                                                                                | JSON_INVALID_UTF8_SUBSTITUTE
                                                                            ) }}
                                                                        </code>
                                                                    @endif

                                                                </td>

                                                            </tr>

                                                        @endforeach

                                                    </tbody>

                                                </table>

                                            </div>

                                        </div>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="9" class="text-center py-5">

                                    <i class="bi bi-search fs-1 text-muted d-block mb-3"></i>

                                    <h6 class="text-muted">
                                        لا توجد سجلات مطابقة
                                    </h6>

                                    <p class="text-muted small mb-0">
                                        جرّب تغيير البحث أو حالة الفحص.
                                    </p>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- =====================================================
             PAGINATION
        ====================================================== --}}

        @if($records->hasPages())

            <div class="card-footer bg-white py-3">

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                    <div class="text-muted small">

                        عرض
                        {{ $records->firstItem() ?? 0 }}

                        إلى

                        {{ $records->lastItem() ?? 0 }}

                        من

                        {{ number_format($records->total()) }}

                        سجل

                    </div>

                    <div>
                        {{ $records->links('pagination::bootstrap-5') }}
                    </div>

                </div>

            </div>

        @endif

    </div>


    {{-- =========================================================
         FOOTER INFORMATION
    ========================================================== --}}

    <div class="text-muted small mt-3">

        <i class="bi bi-clock me-1"></i>

        آخر تحديث للمعاينة:

        {{ date('Y-m-d H:i', $previewLastModified) }}

        <span class="mx-2">|</span>

        <i class="bi bi-shield-check me-1"></i>

        استراتيجية الاستيراد:

        Insert Only

        <span class="mx-2">|</span>

        <i class="bi bi-database me-1"></i>

        لم يتم تعديل قاعدة البيانات أثناء المعاينة.

    </div>

</div>

@endsection
