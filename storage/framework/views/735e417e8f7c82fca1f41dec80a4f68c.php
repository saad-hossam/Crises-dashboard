

<?php $__env->startSection('title', __('roles.show')); ?>
<?php $__env->startSection('page_title', __('roles.show')); ?>

<?php $__env->startSection('content'); ?>
<?php
    $groupLabels = [
        'dashboard'     => __('permissions.groups.dashboard'),
        'users'         => __('permissions.groups.users'),
        'roles'         => __('permissions.groups.roles'),
        'permissions'   => __('permissions.groups.permissions'),
        'imports'       => __('permissions.groups.imports'),
        'data'          => __('permissions.groups.data'),
        'reports'       => __('permissions.groups.reports'),
        'settings'      => __('permissions.groups.settings'),
        'activity_logs' => __('permissions.groups.activity_logs'),
        'units'         => __('permissions.groups.units') ?? 'Units',
    ];
?>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3"><?php echo e(__('roles.details')); ?></h5>
                <dl class="row mb-0">
                    <dt class="col-sm-5"><?php echo e(__('roles.name')); ?></dt>
                    <dd class="col-sm-7">
                        <strong><?php echo e($role->name); ?></strong>
                        <?php if($role->is_protected): ?>
                            <span class="badge bg-warning-subtle text-warning-emphasis ms-2">
                                <i class="bi bi-shield-fill"></i> <?php echo e(__('roles.protected') ?? 'Protected'); ?>

                            </span>
                        <?php endif; ?>
                    </dd>

                    <dt class="col-sm-5"><?php echo e(__('roles.description')); ?></dt>
                    <dd class="col-sm-7"><?php echo e($role->description ?? '—'); ?></dd>

                    <dt class="col-sm-5"><?php echo e(__('roles.users_count')); ?></dt>
                    <dd class="col-sm-7"><?php echo e($role->users->count()); ?></dd>

                    <dt class="col-sm-5"><?php echo e(__('roles.permissions_count')); ?></dt>
                    <dd class="col-sm-7"><?php echo e($role->permissions->count()); ?></dd>

                    <dt class="col-sm-5"><?php echo e(__('roles.created_at')); ?></dt>
                    <dd class="col-sm-7"><?php echo e($role->created_at?->format('Y-m-d H:i') ?? '—'); ?></dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title mb-3"><?php echo e(__('roles.permissions')); ?></h5>
                <?php $grouped = $role->permissions->groupBy('group'); ?>
                <?php if($grouped->isEmpty()): ?>
                    <p class="text-muted mb-0"><?php echo e(__('roles.no_permissions')); ?></p>
                <?php else: ?>
                    <?php $__currentLoopData = $grouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $perms): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="mb-2">
                            <strong class="d-block small text-uppercase text-muted mb-1">
                                <?php echo e($groupLabels[$group] ?? ucfirst($group ?: '—')); ?>

                            </strong>
                            <?php $__currentLoopData = $perms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="badge bg-primary-subtle text-primary-emphasis me-1 mb-1">
                                    <?php echo e($p->name); ?>

                                </span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3"><?php echo e(__('roles.assigned_users')); ?></h5>
                <?php if($role->users->isEmpty()): ?>
                    <p class="text-muted mb-0"><?php echo e(__('roles.no_users')); ?></p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th><?php echo e(__('users.name')); ?></th>
                                    <th><?php echo e(__('users.email')); ?></th>
                                    <th><?php echo e(__('users.status')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $role->users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($user->id); ?></td>
                                        <td><?php echo e($user->name); ?></td>
                                        <td><?php echo e($user->email); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo e($user->status === 'active' ? 'success' : 'secondary'); ?>">
                                                <?php echo e(__('users.' . $user->status)); ?>

                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="<?php echo e(route('admin.roles.index')); ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> <?php echo e(__('common.back')); ?>

    </a>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/saad/Videos/Crises-dashboard/resources/views/admin/roles/show.blade.php ENDPATH**/ ?>