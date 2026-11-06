<nav aria-label="breadcrumb" class="admin-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item breadcrumb-home">
            <a href="<?php echo e(route('admin.dashboard')); ?>"
               aria-label="<?php echo e(__('navigation.dashboard')); ?>">
                <i class="bi bi-house-door"></i>
                <span class="d-none d-sm-inline"><?php echo e(__('navigation.dashboard')); ?></span>
            </a>
        </li>

        <?php if (! empty(trim($__env->yieldContent('breadcrumb')))): ?>
            <?php echo $__env->yieldContent('breadcrumb'); ?>
        <?php endif; ?>
    </ol>
</nav><?php /**PATH /home/saad/Videos/Crises-dashboard/resources/views/components/admin/breadcrumb.blade.php ENDPATH**/ ?>