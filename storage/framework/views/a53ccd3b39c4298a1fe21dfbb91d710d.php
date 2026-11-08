<?php $__env->startSection('title', __('excel.details').' — '.$config['name_ar']); ?>
<?php $__env->startSection('page_title', __('excel.details')); ?>

<?php $__env->startSection('breadcrumb'); ?>
<li class="breadcrumb-item"><a href="<?php echo e(route('admin.excel-modules.index')); ?>"><?php echo e(__('excel.modules')); ?></a></li>
<li class="breadcrumb-item"><a href="<?php echo e(route('admin.excel-modules.table', $moduleCode)); ?>"><?php echo e($config['name_ar']); ?></a></li>
<li class="breadcrumb-item active">#<?php echo e($record->id); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="hero-detail mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-white text-primary"><?php echo e($moduleCode); ?></span>
                <span class="badge bg-white bg-opacity-25"><?php echo e($config['table']); ?></span>
            </div>
            <h3 class="mb-1 fw-bold">
                <?php echo e($record->name
                    ?? $record->name_ar
                    ?? $record->site_name
                    ?? $record->warehouse_name
                    ?? $record->facility_name
                    ?? $record->product_name
                    ?? $record->unit_name
                    ?? ('#'.$record->id)); ?>

            </h3>
            <div class="opacity-75 small">
                <?php if($record->unit_code ?? null): ?>
                    <i class="bi bi-hash me-1"></i><?php echo e($record->unit_code); ?>

                <?php endif; ?>
                <?php if($record->governorate ?? null): ?>
                    <span class="mx-2">·</span>
                    <i class="bi bi-geo me-1"></i><?php echo e($record->governorate); ?>

                <?php endif; ?>
                <?php if($record->operating_status ?? null): ?>
                    <span class="mx-2">·</span>
                    <i class="bi bi-activity me-1"></i><?php echo e($record->operating_status); ?>

                <?php endif; ?>
            </div>
        </div>
        <a href="<?php echo e(route('admin.excel-modules.table', $moduleCode)); ?>" class="btn btn-light">
            <i class="bi bi-arrow-<?php echo e(app()->getLocale()==='ar' ? 'left' : 'right'); ?> me-1"></i>
            <?php echo e(__('excel.back_to_table')); ?>

        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3">
                <div class="text-muted small"><i class="bi bi-upc me-1"></i>ID</div>
                <div class="fs-5 fw-bold">#<?php echo e($record->id); ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3">
                <div class="text-muted small"><i class="bi bi-building me-1"></i><?php echo e(__('excel.unit_code')); ?></div>
                <div class="fs-6 fw-bold text-truncate"><?php echo e($record->unit_code ?? '—'); ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3">
                <div class="text-muted small"><i class="bi bi-flag me-1"></i>Status</div>
                <div class="fs-6 fw-bold"><?php echo e($record->operating_status ?? '—'); ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3">
                <div class="text-muted small"><i class="bi bi-shield-check me-1"></i>Readiness</div>
                <div class="fs-6 fw-bold"><?php echo e($record->readiness_level ?? '—'); ?></div>
            </div>
        </div>
    </div>
</div>

<?php if(!empty($record->x_coordinate) && !empty($record->y_coordinate)): ?>
<div class="card border-0 shadow-sm mb-4 overflow-hidden">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold"><i class="bi bi-geo-alt-fill text-danger me-2"></i><?php echo e(__('excel.location')); ?></span>
        <a class="small" target="_blank" rel="noopener"
           href="https://www.google.com/maps?q=<?php echo e($record->y_coordinate); ?>,<?php echo e($record->x_coordinate); ?>">
            <?php echo e(__('excel.open_maps')); ?> <i class="bi bi-box-arrow-up-left"></i>
        </a>
    </div>
    <div class="ratio ratio-21x9" style="min-height:240px">
        <iframe
            src="https://maps.google.com/maps?q=<?php echo e($record->y_coordinate); ?>,<?php echo e($record->x_coordinate); ?>&z=14&output=embed"
            style="border:0" loading="lazy" allowfullscreen></iframe>
    </div>
</div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-list-columns-reverse text-primary me-2"></i>
            <?php echo e(__('excel.details')); ?>

            <span class="badge bg-light text-muted border ms-1"><?php echo e(count($columns)); ?> <?php echo e(__('excel.fields')); ?></span>
        </h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $v = $record->{$col} ?? null; ?>
                <div class="col-md-6 col-xl-4">
                    <div class="detail-field">
                        <div class="label">
                            <i class="bi bi-dot"></i><?php echo e(str_replace('_', ' ', $col)); ?>

                        </div>
                        <div class="value">
                            <?php echo e($v === null || $v === '' ? '—' : $v); ?>

                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\pc\Music\Crises-dashboard\resources\views/admin/excel-modules/table-record.blade.php ENDPATH**/ ?>