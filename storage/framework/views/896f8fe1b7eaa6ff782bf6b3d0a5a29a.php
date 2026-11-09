<header class="admin-navbar">

    
    <div class="navbar-start">

        
        <button
            type="button"
            class="btn btn-icon sidebar-toggle"
            id="sidebarToggle"
            aria-label="<?php echo e(__('common.toggle_sidebar')); ?>"
            aria-controls="adminSidebar"
            aria-expanded="true"
        >
            <i class="bi bi-list"></i>
        </button>

        
        <div class="page-heading">
            <h1 class="page-title">
                <?php echo $__env->yieldContent('page_title', __('dashboard.title')); ?>
            </h1>
        </div>

    </div>


    
    <div class="navbar-end">

        
        <div class="dropdown navbar-item">

            <button
                type="button"
                class="btn btn-icon navbar-action dropdown-toggle"
                data-bs-toggle="dropdown"
                aria-expanded="false"
                aria-label="<?php echo e(__('common.language')); ?>"
                title="<?php echo e(__('common.language')); ?>"
            >
                <i class="bi bi-globe2"></i>
            </button>

            <ul class="dropdown-menu dropdown-menu-end navbar-dropdown">

                <li>
                    <a
                        class="dropdown-item <?php echo e(app()->getLocale() === 'en' ? 'active' : ''); ?>"
                        href="<?php echo e(route('language.switch', 'en')); ?>"
                    >
                        <i class="bi bi-translate"></i>
                        <?php echo e(__('common.english')); ?>


                        <?php if(app()->getLocale() === 'en'): ?>
                            <i class="bi bi-check2 ms-auto"></i>
                        <?php endif; ?>
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item <?php echo e(app()->getLocale() === 'ar' ? 'active' : ''); ?>"
                        href="<?php echo e(route('language.switch', 'ar')); ?>"
                    >
                        <i class="bi bi-translate"></i>
                        <?php echo e(__('common.arabic')); ?>


                        <?php if(app()->getLocale() === 'ar'): ?>
                            <i class="bi bi-check2 ms-auto"></i>
                        <?php endif; ?>
                    </a>
                </li>

            </ul>
        </div>


        
        <button
            type="button"
            class="btn btn-icon navbar-action theme-toggle"
            id="themeToggle"
            aria-label="<?php echo e(__('common.toggle_theme')); ?>"
            title="<?php echo e(__('common.toggle_theme')); ?>"
        >
            <i class="bi bi-moon-stars" id="themeIcon"></i>
        </button>


        
        <button
            type="button"
            class="btn btn-icon navbar-action notifications-button"
            disabled
            aria-label="<?php echo e(__('common.no_notifications')); ?>"
            title="<?php echo e(__('common.no_notifications')); ?>"
        >
            <i class="bi bi-bell"></i>
        </button>


        
        <?php if(auth()->guard()->check()): ?>
            <div class="dropdown navbar-user">

                <button
                    type="button"
                    class="btn user-dropdown-toggle dropdown-toggle"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                >

                    <span class="user-avatar">
                        <?php echo e(strtoupper(mb_substr(auth()->user()->name, 0, 1))); ?>

                    </span>

                    <span class="user-info">
                        <span class="user-name">
                            <?php echo e(auth()->user()->name); ?>

                        </span>

                        <!-- <span class="user-role">
                            <?php echo e(__('common.administrator')); ?>

                        </span> -->
                    </span>

                    <i class="bi bi-chevron-down user-dropdown-icon"></i>

                </button>


                <ul class="dropdown-menu dropdown-menu-end navbar-dropdown user-menu">

                    
                    <li class="user-menu-header">

                        <div class="user-avatar user-avatar-lg">
                            <?php echo e(strtoupper(mb_substr(auth()->user()->name, 0, 1))); ?>

                        </div>

                        <div class="user-menu-info">
                            <strong>
                                <?php echo e(auth()->user()->name); ?>

                            </strong>

                            <small>
                                <?php echo e(auth()->user()->email); ?>

                            </small>
                        </div>

                    </li>

                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    
                    <li>
                        <a
                            class="dropdown-item disabled"
                            href="#"
                            aria-disabled="true"
                        >
                            <i class="bi bi-person"></i>
                            <?php echo e(__('common.profile')); ?>

                        </a>
                    </li>

                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    
                    <li>
                        <form
                            method="POST"
                            action="<?php echo e(route('logout')); ?>"
                            onsubmit="return confirm(<?php echo \Illuminate\Support\Js::from(__('auth.logout_confirm'))->toHtml() ?>)"
                        >
                            <?php echo csrf_field(); ?>

                            <button
                                type="submit"
                                class="dropdown-item text-danger"
                            >
                                <i class="bi bi-box-arrow-right"></i>
                                <?php echo e(__('common.logout')); ?>

                            </button>
                        </form>
                    </li>

                </ul>

            </div>
        <?php endif; ?>

    </div>

</header>
<?php /**PATH C:\Users\pc\Music\Crises-dashboard\resources\views/components/admin/navbar.blade.php ENDPATH**/ ?>