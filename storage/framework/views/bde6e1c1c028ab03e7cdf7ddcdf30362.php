

<?php $__env->startSection('title', __('roles.roles')); ?>
<?php $__env->startSection('page_title', __('roles.roles')); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><?php echo e(__('roles.roles')); ?></h2>
    <a href="<?php echo e(route('admin.roles.create')); ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> <?php echo e(__('roles.create')); ?>

    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th><?php echo e(__('roles.name')); ?></th>
                    <th><?php echo e(__('roles.permissions_count')); ?></th>
                    <th><?php echo e(__('roles.created_at')); ?></th>
                    <th><?php echo e(__('common.actions')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($role->id); ?></td>
                        <td>
                            <strong><?php echo e($role->name); ?></strong>
                            <?php if($role->is_protected): ?>
                                <span class="badge bg-warning-subtle text-warning-emphasis ms-1">
                                    <i class="bi bi-shield-fill"></i> <?php echo e(__('roles.protected')); ?>

                                </span>
                            <?php endif; ?>
                        </td>
                       
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                <?php echo e($role->permissions_count ?? '—'); ?>

                            </span>
                        </td>
                        <td><?php echo e($role->created_at->format('Y-m-d')); ?></td>
                        <td>
                            <a href="<?php echo e(route('admin.roles.show', $role)); ?>"
                               class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="<?php echo e(route('admin.roles.edit', $role)); ?>"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php if($role->is_protected): ?>
                                <button class="btn btn-sm btn-outline-danger" disabled
                                        title="<?php echo e(__('roles.protected_hint')); ?>">
                                    <i class="bi bi-trash"></i>
                                </button>
                            <?php elseif($role->users_count > 0): ?>
                                <button class="btn btn-sm btn-outline-danger" disabled
                                        title="<?php echo e(__('roles.has_users_hint')); ?>">
                                    <i class="bi bi-trash"></i>
                                </button>
                            <?php else: ?>
                                <form action="<?php echo e(route('admin.roles.destroy', $role)); ?>"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('<?php echo e(__('roles.delete_confirm')); ?>')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4"><?php echo e(__('common.no_data')); ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3"><?php echo e($roles->links()); ?></div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/saad/Videos/Crises-dashboard/resources/views/admin/roles/index.blade.php ENDPATH**/ ?>