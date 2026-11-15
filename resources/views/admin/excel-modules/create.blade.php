@extends('layouts.admin')

@section('title', 'رفع ملف Excel')
@section('page_title', 'رفع ملف Excel')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.excel-modules.index') }}">موديولات Excel</a></li>
<li class="breadcrumb-item active">رفع ملف</li>
@endsection

@section('content')
<div class="upload-page" dir="rtl">

    <div class="upload-hero">
        <div class="upload-hero-content">
            <span class="upload-hero-kicker">
                <i class="bi bi-cloud-upload-fill"></i>
                استيراد جديد
            </span>
            <h1 class="upload-hero-title">رفع ملف Excel</h1>
            <p class="upload-hero-sub">
                <i class="bi bi-info-circle me-1"></i>
                الملف يُكتشف تلقائياً من شيت <code style="color:#a5d6ff;">_System</code> (ModuleCode)
            </p>
        </div>
    </div>

    <div class="upload-steps">
        <div class="upload-step active"><span class="upload-step-num">1</span> اختيار الملف</div>
        <span class="upload-step-line"></span>
        <div class="upload-step"><span class="upload-step-num">2</span> المعاينة</div>
        <span class="upload-step-line"></span>
        <div class="upload-step"><span class="upload-step-num">3</span> الاستيراد</div>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-lg-9 col-xl-8">

            @if($errors->any())
                <div class="upload-alert upload-alert-danger">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>
                        <strong class="d-block mb-1">حدثت أخطاء:</strong>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="upload-card">
                <form method="POST"
                      action="{{ route('admin.excel-modules.preview') }}"
                      enctype="multipart/form-data"
                      id="uploadForm">
                    @csrf

                    <div class="drop-zone" id="dropZone">
                        <input type="file"
                               name="excel_file"
                               id="fileInput"
                               accept=".xlsx,.xls"
                               class="drop-zone-input"
                               required>

                        <div class="drop-zone-icon" id="dropIcon">
                            <i class="bi bi-cloud-arrow-up"></i>
                        </div>
                        <div class="drop-zone-title" id="dropTitle">
                            اسحب الملف هنا أو اضغط للاختيار
                        </div>
                        <div class="drop-zone-sub" id="dropSub">
                            يدعم ملفات Excel فقط (xlsx, xls)
                        </div>
                        <span class="drop-zone-browse">
                            <i class="bi bi-folder2-open"></i>
                            اختيار ملف
                        </span>

                        <div class="drop-zone-formats">
                            <span><i class="bi bi-check-circle-fill text-success"></i> .xlsx</span>
                            <span><i class="bi bi-check-circle-fill text-success"></i> .xls</span>
                            <span><i class="bi bi-x-circle-fill text-danger"></i> حد أقصى 50 MB</span>
                        </div>
                    </div>

                    <div class="file-preview" id="filePreview">
                        <div class="file-preview-icon">
                            <i class="bi bi-file-earmark-excel-fill"></i>
                        </div>
                        <div class="file-preview-info">
                            <p class="file-preview-name" id="previewName">—</p>
                            <div class="file-preview-meta" id="previewMeta">—</div>
                        </div>
                        <button type="button" class="file-preview-remove" id="removeFile" title="إزالة الملف">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>

                    <div class="upload-progress" id="uploadProgress">
                        <div class="upload-progress-label">
                            <span><i class="bi bi-arrow-up-circle me-1"></i> جاري الرفع...</span>
                            <span id="progressPercent">0%</span>
                        </div>
                        <div class="upload-progress-bar">
                            <div class="upload-progress-fill" id="progressFill"></div>
                        </div>
                    </div>

                    <div class="upload-actions">
                        <button type="submit"
                                class="upload-btn upload-btn-primary"
                                id="submitBtn"
                                disabled>
                            <i class="bi bi-eye"></i>
                            معاينة قبل الاستيراد
                        </button>
                        <a href="{{ route('admin.excel-modules.index') }}"
                           class="upload-btn upload-btn-outline">
                            <i class="bi bi-x-lg"></i>
                            إلغاء
                        </a>
                    </div>
                </form>
            </div>

            <div class="row g-3 mt-3">
                <div class="col-md-6">
                    <div class="info-card">
                        <div class="info-card-title">
                            <i class="bi bi-info-circle-fill"></i>
                            كيف يعمل الاستيراد؟
                        </div>
                        <ul class="info-list">
                            <li><i class="bi bi-check-circle-fill"></i> <span>النظام ديناميكي: أي ملف من الـ 26 بنفس القالب يعمل بدون تعديل كود</span></li>
                            <li><i class="bi bi-check-circle-fill"></i> <span>عند إعادة الاستيراد لنفس الموديول يمكن استبدال البيانات القديمة</span></li>
                            <li><i class="bi bi-check-circle-fill"></i> <span>البيانات تُحفظ JSON مع فهارس لكود الوحدة والإحداثيات</span></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-card">
                        <div class="info-card-title">
                            <i class="bi bi-gear-fill"></i>
                            محتوى شيت <code>_System</code>
                        </div>
                        <p class="text-muted small mb-0">
                            يجب أن يحتوي الملف على شيت باسم <code>_System</code> به الأعمدة:
                        </p>
                        <div class="schema-box">
<span class="schema-key">ModuleCode</span>  →  <span class="schema-val">MACHINES</span><br>
<span class="schema-key">ModuleName</span>  →  <span class="schema-val">الماكينات</span><br>
<span class="schema-key">TableName</span>   →  <span class="schema-val">machines</span><br>
<span class="schema-key">Version</span>     →  <span class="schema-val">1.0</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dropZone     = document.getElementById('dropZone');
    const fileInput    = document.getElementById('fileInput');
    const filePreview  = document.getElementById('filePreview');
    const previewName  = document.getElementById('previewName');
    const previewMeta  = document.getElementById('previewMeta');
    const removeBtn    = document.getElementById('removeFile');
    const submitBtn    = document.getElementById('submitBtn');
    const dropIcon     = document.getElementById('dropIcon');
    const dropTitle    = document.getElementById('dropTitle');
    const dropSub      = document.getElementById('dropSub');
    const form         = document.getElementById('uploadForm');
    const progress     = document.getElementById('uploadProgress');
    const progressFill = document.getElementById('progressFill');
    const progressPct  = document.getElementById('progressPercent');

    const MAX_SIZE = 50 * 1024 * 1024;
    const ALLOWED  = ['.xlsx', '.xls'];

    if (!dropZone) return;

    function formatBytes(bytes) {
        if (bytes === 0) return '0 B';
        const k = 1024, sizes = ['B','KB','MB','GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return (bytes / Math.pow(k, i)).toFixed(2) + ' ' + sizes[i];
    }
    function getExtension(name) {
        const i = name.lastIndexOf('.');
        return i >= 0 ? name.substring(i).toLowerCase() : '';
    }
    function showFilePreview(file) {
        if (!file) return;
        const ext = getExtension(file.name);
        if (!ALLOWED.includes(ext)) {
            alert('صيغة الملف غير مدعومة. يُسمح فقط بـ .xlsx و .xls');
            resetInput(); return;
        }
        if (file.size > MAX_SIZE) {
            alert('حجم الملف يتجاوز 50 MB. حجم ملفك: ' + formatBytes(file.size));
            resetInput(); return;
        }
        previewName.textContent = file.name;
        previewMeta.textContent = formatBytes(file.size) + ' · ' + ext.toUpperCase();
        filePreview.classList.add('show');
        dropZone.classList.add('has-file');
        dropIcon.innerHTML = '<i class="bi bi-file-earmark-check"></i>';
        dropTitle.textContent = 'تم اختيار الملف';
        dropSub.textContent = 'اضغط على "إزالة" لاختيار ملف آخر';
        submitBtn.disabled = false;
    }
    function resetInput() {
        fileInput.value = '';
        filePreview.classList.remove('show');
        dropZone.classList.remove('has-file');
        dropIcon.innerHTML = '<i class="bi bi-cloud-arrow-up"></i>';
        dropTitle.textContent = 'اسحب الملف هنا أو اضغط للاختيار';
        dropSub.textContent = 'يدعم ملفات Excel فقط (xlsx, xls)';
        submitBtn.disabled = true;
    }

    fileInput.addEventListener('change', function () {
        if (this.files && this.files[0]) showFilePreview(this.files[0]);
    });

    removeBtn.addEventListener('click', function (e) {
        e.stopPropagation(); resetInput();
    });

    ['dragenter','dragover','dragleave','drop'].forEach(evt => {
        dropZone.addEventListener(evt, e => { e.preventDefault(); e.stopPropagation(); });
    });
    ['dragenter','dragover'].forEach(evt => {
        dropZone.addEventListener(evt, () => dropZone.classList.add('dragover'));
    });
    ['dragleave','drop'].forEach(evt => {
        dropZone.addEventListener(evt, () => dropZone.classList.remove('dragover'));
    });
    dropZone.addEventListener('drop', e => {
        const files = e.dataTransfer.files;
        if (files && files.length > 0) {
            fileInput.files = files;
            showFilePreview(files[0]);
        }
    });

    // ===== SUBMIT: form submission عادي — المتصفح يتبع الـ redirect =====
    form.addEventListener('submit', function (e) {
        if (!fileInput.files || !fileInput.files[0]) {
            e.preventDefault();
            return;
        }
        // لا e.preventDefault() — نترك المتصفح يرسل ويتبع 302 → GET /preview
        progress.classList.add('show');
        progressFill.style.width = '100%';
        progressPct.textContent = '...';
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> جاري الرفع والمعاينة...';
    });

    document.addEventListener('dragover', e => e.preventDefault());
    document.addEventListener('drop', e => e.preventDefault());

    document.addEventListener('keydown', e => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'u') {
            e.preventDefault(); fileInput.click();
        }
    });
});
</script>
@endpush