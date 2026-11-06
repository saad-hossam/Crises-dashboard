

<?php $__env->startSection('title', __('permissions.title')); ?>
<?php $__env->startSection('page_title', __('permissions.title')); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><?php echo e(__('permissions.title')); ?></h2>
    <a href="<?php echo e(route('admin.permissions.create')); ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> <?php echo e(__('permissions.create')); ?>

    </a>
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-4">
        <input type="text" name="search" class="form-control"
               placeholder="<?php echo e(__('permissions.search_placeholder')); ?>"
               value="<?php echo e(request('search')); ?>">
    </div>
    <div class="col-md-3">
        <select name="group" class="form-select">
            <option value=""><?php echo e(__('permissions.all_groups')); ?></option>
            <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($g); ?>" <?php if(request('group') === $g): echo 'selected'; endif; ?>><?php echo e($g); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-outline-secondary w-100">
            <i class="bi bi-search"></i> <?php echo e(__('common.search')); ?>

        </button>
    </div>
</form>

<?php $__empty_1 = true; $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $groupPermissions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong><?php echo e($group ?: __('permissions.ungrouped')); ?></strong>
            <span class="badge bg-secondary"><?php echo e($groupPermissions->count()); ?></span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 35%"><?php echo e(__('permissions.name')); ?></th>
                        <th><?php echo e(__('permissions.description')); ?></th>
                        <th><?php echo e(__('permissions.roles_count')); ?></th>
                        <th><?php echo e(__('common.actions')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $groupPermissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><code><?php echo e($permission->name); ?></code></td>
                            <td class="text-muted small"><?php echo e($permission->description ?? '—'); ?></td>
                            <td>
                                <span class="badge bg-info-subtle text-info-emphasis">
                                    <?php echo e($permission->roles_count); ?>

                                </span>
                            </td>
                            <td>
                                <a href="<?php echo e(route('admin.permissions.show', $permission)); ?>"
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?php echo e(route('admin.permissions.edit', $permission)); ?>"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php if($permission->roles_count > 0): ?>
                                    <button class="btn btn-sm btn-outline-danger" disabled
                                            title="<?php echo e(__('permissions.has_roles_hint')); ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                <?php else: ?>
                                    <form action="<?php echo e(route('admin.permissions.destroy', $permission)); ?>"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('<?php echo e(__('permissions.delete_confirm')); ?>')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="alert alert-info"><?php echo e(__('common.no_data')); ?></div>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/saad/Videos/Crises-dashboard/resources/views/admin/permissions/index.blade.php ENDPATH**/ ?>