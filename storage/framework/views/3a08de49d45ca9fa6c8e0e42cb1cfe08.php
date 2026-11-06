
<!DOCTYPE html>
<html
    lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>"
    dir="<?php echo e(app()->getLocale() === 'ar' ? 'rtl' : 'ltr'); ?>"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <meta name="theme-color" content="#101a35">

    <title><?php echo $__env->yieldContent('title', __('common.app_name')); ?></title>

    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

    
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/app.css')); ?>?v=2">

    
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/auth.css')); ?>?v=2">

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body class="auth-body">

    <?php echo $__env->yieldContent('content'); ?>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    
    <script src="<?php echo e(asset('assets/js/app.js')); ?>?v=2"></script>

    <?php echo $__env->yieldPushContent('scripts'); ?>

</body>
</html>
<?php /**PATH /home/saad/Videos/Crises-dashboard/resources/views/layouts/guest.blade.php ENDPATH**/ ?>