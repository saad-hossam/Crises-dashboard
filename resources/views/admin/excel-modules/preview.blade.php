@extends('layouts.admin')

@section('title', 'معاينة الاستيراد')
@section('page_title', 'معاينة الاستيراد')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.excel-modules.index') }}">موديولات Excel</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.excel-modules.create') }}">رفع ملف</a></li>
<li class="breadcrumb-item active">معاينة</li>
@endsection



@section('content')
@php
    $headers = $preview['headers'] ?? [];
    $previewRows = $preview['preview_rows'] ?? [];
    $totalRows = $preview['total_rows'] ?? 0;
    $totalCols = count($headers);
    $shownCols = array_slice($headers, 0, 10);
    $hiddenColsCount = max(0, $totalCols - 10);
    $sampleCount = count($previewRows);
@endphp

<div class="ip-page" dir="rtl">

    {{-- ============================================================
         HERO
         ============================================================ --}}
    <div class="ip-hero">
        <div class="ip-hero-content">
            <span class="ip-hero-kicker">
                <i class="bi bi-eye-fill"></i>
                معاينة قبل الاستيراد
            </span>
            <h1 class="ip-hero-title">
                <i class="bi bi-file-earmark-excel me-2"></i>
                {{ $originalName }}
            </h1>
            <p class="ip-hero-sub">
                <i class="bi bi-info-circle"></i>
                راجع البيانات أدناه قبل تأكيد الاستيراد
                @if(!empty($preview['module_code']))
                    · <code>{{ $preview['module_code'] }}</code>
                @endif
            </p>
        </div>
    </div>

    {{-- ============================================================
         STEPS
         ============================================================ --}}
    <div class="ip-steps">
        <div class="ip-step done">
            <span class="ip-step-num"><i class="bi bi-check"></i></span>
            اختيار الملف
        </div>
        <span class="ip-step-line done"></span>
        <div class="ip-step active">
            <span class="ip-step-num">2</span>
            المعاينة
        </div>
        <span class="ip-step-line"></span>
        <div class="ip-step">
            <span class="ip-step-num">3</span>
            الاستيراد
        </div>
    </div>

    {{-- ============================================================
         KPIs
         ============================================================ --}}
    <div class="ip-kpis">
        <div class="ip-kpi">
            <div class="ip-kpi-icon" style="background:#0d6efd;">
                <i class="bi bi-grid-3x3-gap-fill"></i>
            </div>
            <div class="ip-kpi-body">
                <div class="ip-kpi-label">الموديول</div>
                <div class="ip-kpi-value" title="{{ $preview['module_name_ar'] ?? '—' }}">
                    {{ $preview['module_name_ar'] ?? '—' }}
                </div>
                <div class="ip-kpi-sub">{{ $preview['table'] ?? '' }}</div>
            </div>
        </div>
        <div class="ip-kpi">
            <div class="ip-kpi-icon" style="background:#10b981;">
                <i class="bi bi-list-ol"></i>
            </div>
            <div class="ip-kpi-body">
                <div class="ip-kpi-label">إجمالي الصفوف</div>
                <div class="ip-kpi-value">{{ number_format($totalRows) }}</div>
            </div>
        </div>
        <div class="ip-kpi">
            <div class="ip-kpi-icon" style="background:#f59e0b;">
                <i class="bi bi-columns-gap"></i>
            </div>
            <div class="ip-kpi-body">
                <div class="ip-kpi-label">الأعمدة</div>
                <div class="ip-kpi-value">{{ $totalCols }}</div>
            </div>
        </div>
        <div class="ip-kpi">
            <div class="ip-kpi-icon" style="background:#8b5cf6;">
                <i class="bi bi-tag-fill"></i>
            </div>
            <div class="ip-kpi-body">
                <div class="ip-kpi-label">إصدار القالب</div>
                <div class="ip-kpi-value" style="font-size:.9rem">
                    {{ $preview['template_version'] ?? '—' }}
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         ALERT (ملاحظة)
         ============================================================ --}}
    <div class="ip-alert">
        <div class="ip-alert-icon">
            <i class="bi bi-info-circle-fill"></i>
        </div>
        <div class="ip-alert-body">
            <h4 class="ip-alert-title">ملاحظة قبل الاستيراد</h4>
            <p class="ip-alert-text">
                سيتم استيراد <strong>{{ number_format($totalRows) }}</strong> صف
                بـ <strong>{{ $totalCols }}</strong> عمود.
                @if($hiddenColsCount > 0)
                    · يتم عرض أول 10 أعمدة فقط، <strong>{{ $hiddenColsCount }}</strong> عمود إضافي سيتم استيراده أيضاً.
                @endif
            </p>
        </div>
    </div>

    {{-- ============================================================
         COLUMNS PREVIEW
         ============================================================ --}}
    @if(!empty($headers))
        <div class="ip-panel">
            <div class="ip-panel-head">
                <h3 class="ip-panel-title">
                    <i class="bi bi-columns-gap"></i>
                    الأعمدة المكتشفة
                </h3>
                <span class="ip-panel-count">{{ $totalCols }}</span>
            </div>
            <div class="ip-cols-preview">
                @foreach($shownCols as $col)
                    <span class="ip-col-chip">
                        <i class="bi bi-tag"></i>
                        {{ $col }}
                    </span>
                @endforeach
                @if($hiddenColsCount > 0)
                    <span class="ip-col-chip more" title="{{ implode(', ', array_slice($headers, 10)) }}">
                        + {{ $hiddenColsCount }} عمود إضافي
                    </span>
                @endif
            </div>
        </div>
    @endif

    {{-- ============================================================
         SAMPLE DATA TABLE
         ============================================================ --}}
    <div class="ip-panel">
        <div class="ip-panel-head">
            <h3 class="ip-panel-title">
                <i class="bi bi-table"></i>
                عينة من البيانات
            </h3>
            <span class="ip-panel-count">
                {{ $sampleCount }} من {{ number_format($totalRows) }}
            </span>
        </div>
        @if($sampleCount > 0)
            <div class="ip-table-wrapper">
                <table class="ip-table">
                    <thead>
                        <tr>
                            <th class="col-row-num">#</th>
                            @foreach($shownCols as $h)
                                <th class="text-nowrap">{{ $h }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($previewRows as $idx => $row)
                            <tr>
                                <td class="col-row-num">{{ $idx + 1 }}</td>
                                @foreach($shownCols as $i => $h)
                                    @php
                                        $val = $row[$i] ?? null;
                                        $isArr = is_array($val);
                                        $strVal = $isArr ? json_encode($val, JSON_UNESCAPED_UNICODE) : (string)($val ?? '');
                                        $isEmpty = !$isArr && ($val === null || $val === '');
                                    @endphp
                                    <td title="{{ $strVal }}">
                                        @if($isEmpty)
                                            <span class="cell-empty">—</span>
                                        @else
                                            {{ \Illuminate\Support\Str::limit($strVal, 40) }}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-4 text-center text-muted">
                <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>
                لا توجد صفوف للمعاينة
            </div>
        @endif
    </div>

    {{-- ============================================================
         ERRORS
         ============================================================ --}}
    @if($errors->any())
        <div class="ip-errors">
            <div class="ip-errors-icon">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <div>
                <strong style="font-size:.85rem;display:block;margin-bottom:.35rem;">حدثت أخطاء:</strong>
                <ul>
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- ============================================================
         ACTION CARD
         ============================================================ --}}
    <form method="POST" action="{{ route('admin.excel-modules.import') }}" id="ipImportForm">
        @csrf
        <div class="ip-action-card">
            <div class="ip-action-card-inner">
                <div class="ip-checkbox">
                    <input class="form-check-input" type="checkbox" name="replace_existing" value="1" id="replace" checked>
                    <div>
                        <label class="ip-checkbox-label" for="replace">
                            استبدال البيانات القديمة لنفس الموديول (إن وجدت)
                        </label>
                        <p class="ip-checkbox-hint mb-0">
                            عند التفعيل، سيتم حذف جميع السجلات السابقة لهذا الموديول قبل الاستيراد الجديد.
                        </p>
                    </div>
                </div>

                <div class="ip-actions">
                    <a href="{{ route('admin.excel-modules.create') }}" class="ip-btn ip-btn-outline">
                        <i class="bi bi-x-lg"></i>
                        إلغاء
                    </a>
                    <button type="submit" class="ip-btn ip-btn-success" id="ipSubmitBtn">
                        <i class="bi bi-check2-circle"></i>
                        تأكيد الاستيراد ({{ number_format($totalRows) }} صف)
                    </button>
                </div>
            </div>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('ipImportForm');
    const submitBtn = document.getElementById('ipSubmitBtn');
    const replaceCheckbox = document.getElementById('replace');

    if (!form || !submitBtn) return;

    // ==================== CONFIRM ON SUBMIT ====================
    form.addEventListener('submit', function (e) {
        // Build confirmation message
        let message = 'هل تريد تأكيد الاستيراد؟\n\n';
        message += '• عدد الصفوف: {{ number_format($totalRows) }}\n';
        message += '• عدد الأعمدة: {{ $totalCols }}\n';
        if (replaceCheckbox && replaceCheckbox.checked) {
            message += '\n⚠️ سيتم استبدال البيانات القديمة لهذا الموديول.';
        }
        message += '\n\nهل تريد المتابعة؟';

        if (!confirm(message)) {
            e.preventDefault();
            return;
        }

        // Disable button + show loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> جاري الاستيراد...';
    });
});
</script>
@endpush