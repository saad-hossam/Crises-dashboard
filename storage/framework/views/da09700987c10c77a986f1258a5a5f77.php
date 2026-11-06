<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" dir="<?php echo e(app()->getLocale() === 'ar' ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title>
        <?php echo $__env->yieldContent('title', __('dashboard.title')); ?>
        — <?php echo e(__('common.app_name')); ?>

    </title>

    <?php if(app()->getLocale() === 'ar'): ?>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <?php else: ?>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php endif; ?>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo e(asset('assets/css/admin.css')); ?>?v=<?php echo e(@filemtime(public_path('assets/css/admin.css')) ?: time()); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/modern.css')); ?>?v=<?php echo e(@filemtime(public_path('assets/css/modern.css')) ?: time()); ?>">

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="admin-body locale-<?php echo e(app()->getLocale()); ?>">

    <div class="admin-wrapper" id="adminWrapper">
        <?php echo $__env->make('components.admin.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="admin-main" id="adminMain">
            <?php echo $__env->make('components.admin.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <main class="admin-content">
                <?php echo $__env->make('components.admin.breadcrumb', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->make('components.admin.alert', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->yieldContent('content'); ?>
            </main>

            <?php echo $__env->make('components.admin.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>

    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo e(asset('assets/js/admin.js')); ?>?v=<?php echo e(@filemtime(public_path('assets/js/admin.js')) ?: time()); ?>"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /home/saad/Videos/Crises-dashboard/resources/views/layouts/admin.blade.php ENDPATH**/ ?>