<?php $__env->startSection('title', __('excel.modules')); ?>
<?php $__env->startSection('page_title', __('excel.modules')); ?>

<?php $__env->startSection('breadcrumb'); ?>
<li class="breadcrumb-item active"><?php echo e(__('excel.modules')); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h4 class="mb-1 fw-bold"><i class="bi bi-file-earmark-spreadsheet text-primary me-2"></i><?php echo e(__('excel.modules')); ?></h4>
        <p class="text-muted mb-0 small"><?php echo e(__('excel.modules_desc')); ?></p>
    </div>
    <a href="<?php echo e(route('admin.excel-modules.create')); ?>" class="btn btn-primary">
        <i class="bi bi-upload me-1"></i> <?php echo e(__('excel.upload')); ?>

    </a>
</div>

<div class="row g-3">
<?php $__empty_1 = true; $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="col-12 col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm h-100 module-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="stat-icon bg-primary-subtle text-primary">
                        <i class="bi bi-table"></i>
                    </div>
                    <span class="badge bg-light text-dark border font-monospace"><?php echo e($module['code']); ?></span>
                </div>
                <h5 class="card-title mb-1"><?php echo e($module['name_ar']); ?></h5>
                <p class="text-muted small mb-2">
                    <?php echo e(__('excel.table')); ?>: <code><?php echo e($module['table']); ?></code>
                </p>
                <p class="text-muted small mb-3">
                    <?php if($module['last_imported_at']): ?>
                        <?php echo e(__('excel.last_import')); ?> <?php echo e($module['last_imported_at']->diffForHumans()); ?>

                    <?php else: ?>
                        <?php echo e(__('excel.never_imported')); ?>

                    <?php endif; ?>
                </p>
                <div class="fs-3 fw-bold text-primary mb-1"><?php echo e(number_format($module['records_count'])); ?></div>
                <div class="text-muted small mb-0"><?php echo e(__('excel.records')); ?></div>
            </div>
            <div class="card-footer bg-white border-0 pt-0 pb-3">
                <a href="<?php echo e(route('admin.excel-modules.table', $module['code'])); ?>" class="btn btn-primary w-100">
                    <i class="bi bi-table me-1"></i> <?php echo e(__('excel.view_data')); ?>

                </a>
            </div>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5 text-muted"><?php echo e(__('dashboard.no_data')); ?></div>
        </div>
    </div>
<?php endif; ?>
</div>

<?php $__env->startPush('styles'); ?>
<style>
.module-card { transition: transform .15s ease, box-shadow .15s ease; border-radius: 1rem!important; }
.module-card:hover { transform: translateY(-3px); box-shadow: 0 .75rem 1.25rem rgba(0,0,0,.1)!important; }
.stat-icon { width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.25rem; }
</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/saad/Videos/Crises-dashboard/resources/views/admin/excel-modules/index.blade.php ENDPATH**/ ?>