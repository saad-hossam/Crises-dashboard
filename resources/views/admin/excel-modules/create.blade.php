@extends('layouts.admin')

@section('title', 'رفع ملف Excel')
@section('page_title', 'رفع ملف Excel')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.excel-modules.index') }}">موديولات Excel</a></li>
<li class="breadcrumb-item active">رفع ملف</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center mb-3"
                         style="width:72px;height:72px">
                        <i class="bi bi-cloud-upload fs-2"></i>
                    </div>
                    <h4 class="fw-bold">رفع ملف Excel</h4>
                    <p class="text-muted mb-0">الملف يُكتشف تلقائياً من شيت <code>_System</code> (ModuleCode)</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 pe-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.excel-modules.preview') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-semibold">اختر ملف .xlsx</label>
                        <input type="file" name="excel_file" accept=".xlsx,.xls" class="form-control form-control-lg" required>
                        <div class="form-text">الحد الأقصى 50 ميجابايت · يجب أن يحتوي على شيت البيانات و _System</div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-eye me-1"></i> معاينة قبل الاستيراد
                        </button>
                        <a href="{{ route('admin.excel-modules.index') }}" class="btn btn-outline-secondary">إلغاء</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body">
                <h6 class="fw-semibold mb-3"><i class="bi bi-lightbulb text-warning me-2"></i>ملاحظات</h6>
                <ul class="mb-0 text-muted small">
                    <li>النظام ديناميكي: أي ملف من الـ 26 بنفس القالب يعمل بدون تعديل كود</li>
                    <li>عند إعادة الاستيراد لنفس الموديول يمكن استبدال البيانات القديمة</li>
                    <li>البيانات تُحفظ JSON مع فهارس لكود الوحدة والإحداثيات</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
