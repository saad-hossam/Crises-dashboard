<?php $__env->startSection('title', 'معاينة الاستيراد'); ?>
<?php $__env->startSection('page_title', 'معاينة الاستيراد'); ?>

<?php $__env->startSection('breadcrumb'); ?>
<li class="breadcrumb-item"><a href="<?php echo e(route('admin.excel-modules.index')); ?>">موديولات Excel</a></li>
<li class="breadcrumb-item"><a href="<?php echo e(route('admin.excel-modules.create')); ?>">رفع ملف</a></li>
<li class="breadcrumb-item active">معاينة</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $headers       = $preview['headers'] ?? [];
    $previewRows   = $preview['rows'] ?? $preview['preview_rows'] ?? [];
    $totalRows     = (int) ($preview['total_rows'] ?? 0);
    $totalCols     = count($headers);
    $moduleCode    = $preview['module_code'] ?? null;
    $moduleNameAr  = $preview['module_name_ar'] ?? $preview['module_name'] ?? '—';
    $tableName     = $preview['table'] ?? $preview['table_name'] ?? '';
    $templateVer   = $preview['template_version'] ?? '—';
    $isInferred    = (bool) ($preview['_inferred'] ?? false);

    $shownCols       = array_slice($headers, 0, 10);
    $hiddenColsCount = max(0, $totalCols - 10);
    $sampleCount     = count($previewRows);
?>

<div class="ip-page" dir="rtl">

    
    <div class="ip-hero">
        <div class="ip-hero-content">
            <span class="ip-hero-kicker">
                <i class="bi bi-eye-fill"></i>
                معاينة قبل الاستيراد
            </span>
            <h1 class="ip-hero-title">
                <i class="bi bi-file-earmark-excel me-2"></i>
                <?php echo e($originalName); ?>

            </h1>
            <p class="ip-hero-sub">
                <i class="bi bi-info-circle"></i>
                راجع البيانات أدناه قبل تأكيد الاستيراد
                <?php if($moduleCode): ?>
                    · <code><?php echo e($moduleCode); ?></code>
                <?php endif; ?>
            </p>
        </div>
    </div>

    
    <?php if($isInferred): ?>
        <div class="alert alert-warning d-flex align-items-start gap-2 mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div>
                <strong>تنبيه:</strong>
                لم يتم العثور على شيت <code>_System</code> في الملف،
                وتم استنتاج <code>ModuleCode</code> من اسم الملف:
                <code><?php echo e($moduleCode); ?></code>.
                راجع البيانات بعناية قبل التأكيد.
            </div>
        </div>
    <?php endif; ?>

    
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

    
    <div class="ip-kpis">
        <div class="ip-kpi">
            <div class="ip-kpi-icon" style="background:#0d6efd;">
                <i class="bi bi-grid-3x3-gap-fill"></i>
            </div>
            <div class="ip-kpi-body">
                <div class="ip-kpi-label">الموديول</div>
                <div class="ip-kpi-value" title="<?php echo e($moduleNameAr); ?>"><?php echo e($moduleNameAr); ?></div>
                <div class="ip-kpi-sub"><?php echo e($tableName); ?></div>
            </div>
        </div>
        <div class="ip-kpi">
            <div class="ip-kpi-icon" style="background:#10b981;">
                <i class="bi bi-list-ol"></i>
            </div>
            <div class="ip-kpi-body">
                <div class="ip-kpi-label">إجمالي الصفوف</div>
                <div class="ip-kpi-value"><?php echo e(number_format($totalRows)); ?></div>
            </div>
        </div>
        <div class="ip-kpi">
            <div class="ip-kpi-icon" style="background:#f59e0b;">
                <i class="bi bi-columns-gap"></i>
            </div>
            <div class="ip-kpi-body">
                <div class="ip-kpi-label">الأعمدة</div>
                <div class="ip-kpi-value"><?php echo e($totalCols); ?></div>
            </div>
        </div>
        <div class="ip-kpi">
            <div class="ip-kpi-icon" style="background:#8b5cf6;">
                <i class="bi bi-tag-fill"></i>
            </div>
            <div class="ip-kpi-body">
                <div class="ip-kpi-label">إصدار القالب</div>
                <div class="ip-kpi-value" style="font-size:.9rem"><?php echo e($templateVer); ?></div>
            </div>
        </div>
    </div>

    
    <div class="ip-alert">
        <div class="ip-alert-icon">
            <i class="bi bi-info-circle-fill"></i>
        </div>
        <div class="ip-alert-body">
            <h4 class="ip-alert-title">ملاحظة قبل الاستيراد</h4>
            <p class="ip-alert-text">
                سيتم استيراد <strong><?php echo e(number_format($totalRows)); ?></strong> صف
                بـ <strong><?php echo e($totalCols); ?></strong> عمود.
                <?php if($hiddenColsCount > 0): ?>
                    · يتم عرض أول 10 أعمدة فقط، <strong><?php echo e($hiddenColsCount); ?></strong> عمود إضافي سيتم استيراده أيضاً.
                <?php endif; ?>
                <?php if($totalRows > 5000): ?>
                    <br><small class="text-warning">
                        <i class="bi bi-clock-history"></i>
                        حجم كبير — قد يستغرق الاستيراد حتى دقيقة. لا تغلق الصفحة.
                    </small>
                <?php endif; ?>
            </p>
        </div>
    </div>

    
    <?php if(!empty($headers)): ?>
        <div class="ip-panel">
            <div class="ip-panel-head">
                <h3 class="ip-panel-title">
                    <i class="bi bi-columns-gap"></i>
                    الأعمدة المكتشفة
                </h3>
                <span class="ip-panel-count"><?php echo e($totalCols); ?></span>
            </div>
            <div class="ip-cols-preview">
                <?php $__currentLoopData = $shownCols; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="ip-col-chip">
                        <i class="bi bi-tag"></i>
                        <?php echo e($col); ?>

                    </span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if($hiddenColsCount > 0): ?>
                    <span class="ip-col-chip more" title="<?php echo e(implode(', ', array_slice($headers, 10))); ?>">
                        + <?php echo e($hiddenColsCount); ?> عمود إضافي
                    </span>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    
    <div class="ip-panel">
        <div class="ip-panel-head">
            <h3 class="ip-panel-title">
                <i class="bi bi-table"></i>
                عينة من البيانات
            </h3>
            <span class="ip-panel-count">
                <?php echo e($sampleCount); ?> من <?php echo e(number_format($totalRows)); ?>

            </span>
        </div>
        <?php if($sampleCount > 0): ?>
            <div class="ip-table-wrapper">
                <table class="ip-table">
                    <thead>
                        <tr>
                            <th class="col-row-num">#</th>
                            <?php $__currentLoopData = $shownCols; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <th class="text-nowrap"><?php echo e($h); ?></th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $previewRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="col-row-num"><?php echo e($idx + 1); ?></td>
                                <?php $__currentLoopData = $shownCols; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $val     = $row[$i] ?? null;
                                        $isArr   = is_array($val);
                                        $strVal  = $isArr ? json_encode($val, JSON_UNESCAPED_UNICODE) : (string) ($val ?? '');
                                        $isEmpty = !$isArr && ($val === null || $val === '');
                                    ?>
                                    <td title="<?php echo e($strVal); ?>">
                                        <?php if($isEmpty): ?>
                                            <span class="cell-empty">—</span>
                                        <?php else: ?>
                                            <?php echo e(\Illuminate\Support\Str::limit($strVal, 40)); ?>

                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="p-4 text-center text-muted">
                <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>
                لا توجد صفوف للمعاينة
            </div>
        <?php endif; ?>
    </div>

    
    <?php if($errors->any()): ?>
        <div class="ip-errors">
            <div class="ip-errors-icon">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <div>
                <strong style="font-size:.85rem;display:block;margin-bottom:.35rem;">حدثت أخطاء:</strong>
                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($e); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    
    <form method="POST" action="/admin/excel-modules/import" id="ipImportForm">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="import_ticket" value="<?php echo e($importTicket ?? session('excel_import_ticket')); ?>">
        <input type="hidden" name="import_payload" value="<?php echo e($importPayload ?? session('excel_import_payload')); ?>">
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
                    <a href="<?php echo e(route('admin.excel-modules.create')); ?>" class="ip-btn ip-btn-outline">
                        <i class="bi bi-x-lg"></i>
                        إلغاء
                    </a>
                    <button type="submit" class="ip-btn ip-btn-success" id="ipSubmitBtn">
                        <i class="bi bi-check2-circle"></i>
                        تأكيد الاستيراد (<?php echo e(number_format($totalRows)); ?> صف)
                    </button>
                </div>
            </div>
        </div>
    </form>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form       = document.getElementById('ipImportForm');
    const submitBtn  = document.getElementById('ipSubmitBtn');
    const replaceBox = document.getElementById('replace');

    if (!form || !submitBtn) return;

    form.addEventListener('submit', function (e) {
        let message = 'هل تريد تأكيد الاستيراد؟\n\n';
        message += '• عدد الصفوف: <?php echo e(number_format($totalRows)); ?>\n';
        message += '• عدد الأعمدة: <?php echo e($totalCols); ?>\n';
        if (replaceBox && replaceBox.checked) {
            message += '\n⚠️ سيتم استبدال البيانات القديمة لهذا الموديول.';
        }
        message += '\n\nهل تريد المتابعة؟';

        if (!confirm(message)) {
            e.preventDefault();
            return;
        }

        // تعطيل الزر فقط — بدون أي overlay أو loader
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> جاري الاستيراد...';
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\pc\Music\Crises-dashboard\Crises-dashboard\resources\views/admin/excel-modules/preview.blade.php ENDPATH**/ ?>