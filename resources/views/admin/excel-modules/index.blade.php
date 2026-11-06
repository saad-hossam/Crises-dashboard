@extends('layouts.admin')

@section('title', 'موديولات Excel')
@section('page_title', 'موديولات Excel')

@section('breadcrumb')
<li class="breadcrumb-item active">موديولات Excel</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h4 class="mb-1 fw-bold">موديولات البيانات</h4>
        <p class="text-muted mb-0 small">كل ملف Excel مستورد يظهر هنا كجدول مستقل للتحليل</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.excel-modules.dashboard') }}" class="btn btn-outline-primary">
            <i class="bi bi-speedometer2 me-1"></i> لوحة المؤشرات
        </a>
        <a href="{{ route('admin.excel-modules.create') }}" class="btn btn-primary">
            <i class="bi bi-upload me-1"></i> رفع ملف
        </a>
    </div>
</div>

@if($modules->isEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <div class="mb-3">
                <i class="bi bi-file-earmark-spreadsheet text-muted" style="font-size:3rem"></i>
            </div>
            <h5>لا توجد موديولات بعد</h5>
            <p class="text-muted">ابدأ برفع أول ملف Excel من القوالب المعتمدة</p>
            <a href="{{ route('admin.excel-modules.create') }}" class="btn btn-primary">
                <i class="bi bi-upload me-1"></i> رفع ملف الآن
            </a>
        </div>
    </div>
@else
    <div class="row g-3">
        @foreach($modules as $module)
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100 module-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="rounded-3 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center"
                                 style="width:48px;height:48px">
                                <i class="bi bi-table fs-5"></i>
                            </div>
                            <span class="badge bg-light text-dark border">{{ $module->code }}</span>
                        </div>
                        <h5 class="card-title mb-1">{{ $module->name_ar }}</h5>
                        <p class="text-muted small mb-3">
                            @if($module->last_imported_at)
                                آخر استيراد {{ $module->last_imported_at->diffForHumans() }}
                            @else
                                لم يُستورد بعد
                            @endif
                        </p>
                        <div class="d-flex gap-3 mb-3">
                            <div>
                                <div class="fs-4 fw-bold text-primary">{{ number_format($module->records_count) }}</div>
                                <div class="text-muted small">سجل</div>
                            </div>
                            <div>
                                <div class="fs-4 fw-bold">{{ $module->batches_count ?? '—' }}</div>
                                <div class="text-muted small">دفعة</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 pt-0 pb-3 d-flex gap-2">
                        <a href="{{ route('admin.excel-modules.show', $module) }}" class="btn btn-primary flex-fill">
                            <i class="bi bi-table me-1"></i> عرض البيانات
                        </a>
                        <form method="POST" action="{{ route('admin.excel-modules.destroy', $module) }}"
                              onsubmit="return confirm('حذف الموديول وكل سجلاته؟')">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger" type="submit" title="حذف">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

@push('styles')
<style>
    .module-card { transition: transform .15s ease, box-shadow .15s ease; }
    .module-card:hover { transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.08)!important; }
</style>
@endpush
@endsection
