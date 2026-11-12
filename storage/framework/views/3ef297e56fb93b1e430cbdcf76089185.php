<?php $__env->startSection('title', 'لوحة التحليلات'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid" dir="rtl">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">لوحة التحليلات</h3>
            <small class="text-muted"><?php echo e(count($modules)); ?> موديول</small>
        </div>
    </div>

    <div class="row">
        <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                <a href="<?php echo e(route('admin.excel-modules.analytics.page', $code)); ?>"
                   class="text-decoration-none">
                    <div class="card h-100 shadow-sm border-0 card-hover">
                        <div class="card-body text-center">
                            <div class="fs-4 mb-2 text-primary">
                                <i class="bi bi-bar-chart-line"></i>
                            </div>
                            <div class="fw-bold"><?php echo e($module['name_ar'] ?? $code); ?></div>
                            <div class="text-muted small"><?php echo e($code); ?></div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .card-hover { transition: transform .15s, box-shadow .15s; }
    .card-hover:hover { transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.1) !important; }
</style>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/saad/Videos/Crises-dashboard/resources/views/admin/excel-modules/analytics-dashboard.blade.php ENDPATH**/ ?>