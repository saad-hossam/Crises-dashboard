

<?php $__env->startSection('title', __('users.title')); ?>
<?php $__env->startSection('page_title', __('users.title')); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><?php echo e(__('users.title')); ?></h2>
        <a href="<?php echo e(route('admin.users.create')); ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> <?php echo e(__('users.create')); ?>

        </a>
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-4">
        <input type="text" name="search" class="form-control"
               placeholder="<?php echo e(__('users.search_placeholder')); ?>"
               value="<?php echo e(request('search')); ?>">
    </div>
    <div class="col-md-2">
        <select name="status" class="form-select">
            <option value=""><?php echo e(__('users.all_statuses')); ?></option>
            <option value="active" <?php if(request('status') === 'active'): echo 'selected'; endif; ?>><?php echo e(__('users.active')); ?></option>
            <option value="inactive" <?php if(request('status') === 'inactive'): echo 'selected'; endif; ?>><?php echo e(__('users.inactive')); ?></option>
        </select>
    </div>
    <div class="col-md-3">
        <select name="role" class="form-select">
            <option value=""><?php echo e(__('users.all_roles')); ?></option>
            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($role->name); ?>" <?php if(request('role') === $role->name): echo 'selected'; endif; ?>>
                    <?php echo e($role->name); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-outline-secondary w-100">
            <i class="bi bi-search"></i> <?php echo e(__('common.search')); ?>

        </button>
    </div>
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th><?php echo e(__('users.name')); ?></th>
                    <th><?php echo e(__('users.email')); ?></th>
                    <th><?php echo e(__('users.phone')); ?></th>
                    <th><?php echo e(__('users.roles')); ?></th>
                    <th><?php echo e(__('users.status')); ?></th>
                    <th><?php echo e(__('users.last_login')); ?></th>
                    <th><?php echo e(__('common.actions')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($user->id); ?></td>
                        <td><?php echo e($user->name); ?></td>
                        <td><?php echo e($user->email); ?></td>
                        <td><?php echo e($user->phone ?? '—'); ?></td>
                        <td>
                            <?php $__empty_2 = true; $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                <span class="badge bg-primary-subtle text-primary-emphasis">
                                    <?php echo e($role->name); ?>

                                </span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-<?php echo e($user->status === 'active' ? 'success' : 'secondary'); ?>">
                                <?php echo e(__('users.' . $user->status)); ?>

                            </span>
                        </td>
                        <td><?php echo e($user->last_login_at?->diffForHumans() ?? '—'); ?></td>
                        <td>
                                <a href="<?php echo e(route('admin.users.show', $user)); ?>"
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?php echo e(route('admin.users.edit', $user)); ?>"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="<?php echo e(route('admin.users.destroy', $user)); ?>" method="POST" class="d-inline"
                                      onsubmit="return confirm('<?php echo e(__('users.delete_confirm')); ?>')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4"><?php echo e(__('common.no_data')); ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3"><?php echo e($users->links()); ?></div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/saad/Videos/Crises-dashboard/resources/views/admin/users/index.blade.php ENDPATH**/ ?>