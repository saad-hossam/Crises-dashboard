<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-brand">
        <a href="<?php echo e(route('admin.dashboard')); ?>" class="brand-link">
            <i class="bi bi-shield-exclamation"></i>
            <span class="brand-text"><?php echo e(__('common.app_name')); ?></span>
        </a>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav flex-column">

            
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('dashboard.view')): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>"
                       href="<?php echo e(route('admin.dashboard')); ?>"
                       data-tooltip="<?php echo e(__('navigation.dashboard')); ?>">
                        <i class="bi bi-speedometer2"></i>
                        <span><?php echo e(__('navigation.dashboard')); ?></span>
                        <?php if(request()->routeIs('admin.dashboard')): ?>
                            <span class="nav-active-dot" aria-hidden="true"></span>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endif; ?>

           

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('data.view')): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('admin.data.*') ? 'active' : ''); ?>"
                       href="<?php echo e(route('admin.dashboard')); ?>"
                       data-tooltip="<?php echo e(__('navigation.data_management')); ?>">
                        <i class="bi bi-database"></i>
                        <span><?php echo e(__('navigation.data_management')); ?></span>
                    </a>
                </li>
            <?php endif; ?>


            
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.excel-modules.*') ? 'active' : ''); ?>"
                   href="<?php echo e(route('admin.excel-modules.dashboard')); ?>"
                   data-tooltip="<?php echo e(__('navigation.excel_data')); ?>">
                    <i class="bi bi-file-earmark-spreadsheet"></i>
                    <span><?php echo e(__('navigation.excel_data')); ?></span>
                    <?php if(request()->routeIs('admin.excel-modules.*')): ?>
                        <span class="nav-active-dot" aria-hidden="true"></span>
                    <?php endif; ?>
                </a>
            </li>

            
            <?php if(auth()->user()->can('users.view')
              || auth()->user()->can('roles.view')
              || auth()->user()->can('permissions.view')
              || auth()->user()->can('units.view')): ?>
                <li class="nav-item sidebar-section-label">
                    <span><?php echo e(__('navigation.management')); ?></span>
                </li>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('users.view')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('admin.users.*') ? 'active' : ''); ?>"
                           href="<?php echo e(route('admin.users.index')); ?>"
                           data-tooltip="<?php echo e(__('navigation.users')); ?>">
                            <i class="bi bi-people"></i>
                            <span><?php echo e(__('navigation.users')); ?></span>
                            <?php if(request()->routeIs('admin.users.*')): ?>
                                <span class="nav-active-dot" aria-hidden="true"></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('roles.view')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('admin.roles.*') ? 'active' : ''); ?>"
                           href="<?php echo e(route('admin.roles.index')); ?>"
                           data-tooltip="<?php echo e(__('navigation.roles_permissions')); ?>">
                            <i class="bi bi-shield-lock"></i>
                            <span><?php echo e(__('navigation.roles_permissions')); ?></span>
                            <?php if(request()->routeIs('admin.roles.*')): ?>
                                <span class="nav-active-dot" aria-hidden="true"></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('permissions.view')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('admin.permissions.*') ? 'active' : ''); ?>"
                           href="<?php echo e(route('admin.permissions.index')); ?>"
                           data-tooltip="<?php echo e(__('navigation.permissions')); ?>">
                            <i class="bi bi-key"></i>
                            <span><?php echo e(__('navigation.permissions')); ?></span>
                            <?php if(request()->routeIs('admin.permissions.*')): ?>
                                <span class="nav-active-dot" aria-hidden="true"></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('units.view')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('admin.units.*') ? 'active' : ''); ?>"
                           href="<?php echo e(route('admin.units.index')); ?>"
                           data-tooltip="<?php echo e(__('navigation.units')); ?>">
                            <i class="bi bi-rulers"></i>
                            <span><?php echo e(__('navigation.units')); ?></span>
                            <?php if(request()->routeIs('admin.units.*')): ?>
                                <span class="nav-active-dot" aria-hidden="true"></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>

            
            <?php if(auth()->user()->can('settings.manage')
              || auth()->user()->can('activity_logs.view')): ?>
                <li class="nav-item sidebar-section-label">
                    <span><?php echo e(__('navigation.system')); ?></span>
                </li>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('settings.manage')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('admin.settings.*') ? 'active' : ''); ?>"
                           href="<?php echo e(route('admin.dashboard')); ?>"
                           data-tooltip="<?php echo e(__('navigation.settings')); ?>">
                            <i class="bi bi-gear"></i>
                            <span><?php echo e(__('navigation.settings')); ?></span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('activity_logs.view')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('admin.activity-logs.*') ? 'active' : ''); ?>"
                           href="<?php echo e(route('admin.dashboard')); ?>"
                           data-tooltip="<?php echo e(__('navigation.activity_logs')); ?>">
                            <i class="bi bi-clock-history"></i>
                            <span><?php echo e(__('navigation.activity_logs')); ?></span>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>

        </ul>
    </nav>
</aside>

<?php if (! $__env->hasRenderedOnce('dbc7d9ba-88a7-44ae-bd19-72ee05ec9b42')): $__env->markAsRenderedOnce('dbc7d9ba-88a7-44ae-bd19-72ee05ec9b42'); ?>
<?php $__env->startSection('scripts'); ?>
<script>
(function () {
    // Close mobile sidebar after navigating
    document.querySelectorAll('.admin-sidebar .nav-link').forEach((a) => {
        a.addEventListener('click', () => {
            if (window.innerWidth < 992) {
                document.getElementById('adminWrapper')?.classList.remove('sidebar-mobile-open');
            }
        });
    });
})();
</script>
<?php $__env->stopSection(); ?>
<?php endif; ?><?php /**PATH C:\Users\pc\Music\Crises-dashboard\resources\views/components/admin/sidebar.blade.php ENDPATH**/ ?>