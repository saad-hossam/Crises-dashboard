<?php $__env->startSection('title', 'معاينة الاستيراد'); ?>
<?php $__env->startSection('page_title', 'معاينة الاستيراد'); ?>

<?php $__env->startSection('breadcrumb'); ?>
<li class="breadcrumb-item"><a href="<?php echo e(route('admin.excel-modules.index')); ?>">موديولات Excel</a></li>
<li class="breadcrumb-item"><a href="<?php echo e(route('admin.excel-modules.create')); ?>">رفع ملف</a></li>
<li class="breadcrumb-item active">معاينة</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h4 class="mb-1 fw-bold">معاينة قبل الاستيراد</h4>
        <p class="text-muted mb-0 small"><?php echo e($originalName); ?></p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-3">
                <div class="text-muted small">الموديول</div>
                <div class="fw-semibold"><?php echo e($preview['module_name_ar'] ?? '—'); ?></div>
                <div class="small text-primary"><?php echo e($preview['module_code'] ?? ''); ?></div>
                <div class="small text-muted">→ <code><?php echo e($preview['table'] ?? ''); ?></code></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-3">
                <div class="text-muted small">إجمالي الصفوف</div>
                <div class="fs-4 fw-bold"><?php echo e(number_format($preview['total_rows'] ?? 0)); ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-3">
                <div class="text-muted small">الأعمدة</div>
                <div class="fs-4 fw-bold"><?php echo e(count($preview['headers'] ?? [])); ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-3">
                <div class="text-muted small">إصدار القالب</div>
                <div class="fw-semibold small"><?php echo e($preview['template_version'] ?? '—'); ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-eye me-2 text-primary"></i>
        عينة من البيانات (أول <?php echo e(count($preview['preview_rows'] ?? [])); ?> صف)
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <?php $__currentLoopData = array_slice($preview['headers'] ?? [], 0, 10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th class="text-nowrap small"><?php echo e($h); ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $preview['preview_rows'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <?php $__currentLoopData = array_slice($preview['headers'] ?? [], 0, 10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <td class="small text-nowrap" style="max-width:160px;overflow:hidden;text-overflow:ellipsis">
                                    <?php echo e(is_array($row[$i] ?? null) ? json_encode($row[$i], JSON_UNESCAPED_UNICODE) : ($row[$i] ?? '—')); ?>

                                </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if($errors->any()): ?>
    <div class="alert alert-danger">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <div><?php echo e($e); ?></div> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php endif; ?>

<form method="POST" action="<?php echo e(route('admin.excel-modules.import')); ?>" class="card border-0 shadow-sm">
    <?php echo csrf_field(); ?>
    <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="replace_existing" value="1" id="replace" checked>
            <label class="form-check-label" for="replace">
                استبدال البيانات القديمة لنفس الموديول (إن وجدت)
            </label>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('admin.excel-modules.create')); ?>" class="btn btn-outline-secondary">إلغاء</a>
            <button type="submit" class="btn btn-success btn-lg">
                <i class="bi bi-check2-circle me-1"></i> تأكيد الاستيراد
            </button>
        </div>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/saad/Videos/Crises-dashboard/resources/views/admin/excel-modules/preview.blade.php ENDPATH**/ ?>