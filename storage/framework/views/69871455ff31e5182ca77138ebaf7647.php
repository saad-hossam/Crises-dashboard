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

    $selectedPermissions = $selectedPermissions ?? [];
    $selectedPermissions = array_map('intval', (array) $selectedPermissions);
?>

<div class="mb-3">
    <label class="form-label fw-semibold"><?php echo e(__('roles.permissions')); ?></label>
    <div class="text-muted small mb-3"><?php echo e(__('roles.permissions_hint')); ?></div>

    <?php if($errors->has('permissions') || $errors->has('permissions.*')): ?>
        <div class="alert alert-danger py-2 small">
            <?php echo e($errors->first('permissions') ?: $errors->first('permissions.*')); ?>

        </div>
    <?php endif; ?>

    <div class="row g-3">
        <?php $__empty_1 = true; $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $groupPermissions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php $groupKey = $group ?: 'other'; ?>
            <div class="col-md-6">
                <div class="border rounded p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="fw-semibold mb-0">
                            <?php echo e($groupLabels[$group] ?? ucfirst($groupKey)); ?>

                        </label>
                        <button type="button"
                                class="btn btn-sm btn-link p-0 text-decoration-none"
                                onclick="toggleGroup('<?php echo e($groupKey); ?>', this)">
                            <?php echo e(__('roles.select_all')); ?>

                        </button>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <?php $__currentLoopData = $groupPermissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="form-check">
                                <input class="form-check-input group-<?php echo e($groupKey); ?>"
                                       type="checkbox"
                                       name="permissions[]"
                                       id="perm_<?php echo e($permission->id); ?>"
                                       value="<?php echo e($permission->id); ?>"
                                       <?php if(in_array($permission->id, $selectedPermissions, true)): echo 'checked'; endif; ?>>
                                <label class="form-check-label small" for="perm_<?php echo e($permission->id); ?>">
                                    <code><?php echo e($permission->name); ?></code>
                                </label>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-12">
                <div class="alert alert-info mb-0"><?php echo e(__('roles.no_permissions')); ?></div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleGroup(group, btn) {
    const boxes = document.querySelectorAll('.group-' + group);
    const anyUnchecked = Array.from(boxes).some(b => !b.checked);
    boxes.forEach(b => b.checked = anyUnchecked);
    btn.textContent = anyUnchecked ? '<?php echo e(__('roles.deselect_all')); ?>' : '<?php echo e(__('roles.select_all')); ?>';
}
</script><?php /**PATH /home/saad/Videos/Crises-dashboard/resources/views/admin/roles/_permission-groups.blade.php ENDPATH**/ ?>