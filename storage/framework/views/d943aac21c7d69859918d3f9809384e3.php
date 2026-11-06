

<?php $__env->startSection('title', __('units.title')); ?>
<?php $__env->startSection('page_title', __('units.title')); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('navigation.units')); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><?php echo e(__('units.title')); ?></h2>
        <a href="<?php echo e(route('admin.units.create')); ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> <?php echo e(__('units.create')); ?>

        </a>
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-4">
        <input type="text" name="search" class="form-control"
               placeholder="<?php echo e(__('units.search_placeholder')); ?>"
               value="<?php echo e(request('search')); ?>">
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-outline-secondary w-100">
            <i class="bi bi-search"></i> <?php echo e(__('common.search') ?? 'Search'); ?>

        </button>
    </div>
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th><?php echo e(__('units.name')); ?></th>
                    <th><?php echo e(__('units.created_at')); ?></th>
                    <th><?php echo e(__('common.actions')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($unit->id); ?></td>
                        <td><?php echo e($unit->name); ?></td>
                        <td><?php echo e($unit->created_at->format('Y-m-d H:i')); ?></td>
                        <td>
                                <a href="<?php echo e(route('admin.units.edit', $unit)); ?>"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="<?php echo e(route('admin.units.destroy', $unit)); ?>" method="POST" class="d-inline"
                                      onsubmit="return confirm('<?php echo e(__('units.delete_confirm')); ?>')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4"><?php echo e(__('common.no_data')); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3"><?php echo e($units->links()); ?></div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/saad/Videos/Crises-dashboard/resources/views/admin/units/index.blade.php ENDPATH**/ ?>