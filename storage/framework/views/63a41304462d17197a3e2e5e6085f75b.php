<aside class="admin-sidebar" id="adminSidebar">

    
    <div class="sidebar-brand">
        <a href="<?php echo e(route('admin.dashboard')); ?>" class="brand-link">
            <div class="brand-icon">
                <i class="bi bi-shield-exclamation"></i>
            </div>
            <div class="brand-text-wrap">
                <span class="brand-text"><?php echo e(__('common.app_name')); ?></span>
                <span class="brand-sub">AOI Crises</span>
            </div>
        </a>
        <button type="button" class="sidebar-close-btn d-lg-none" id="sidebarCloseBtn" aria-label="إغلاق">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    
    <nav class="sidebar-nav" id="sidebarNav">
        <ul class="nav flex-column">

            
           

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('dashboard.view')): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>"
                       href="<?php echo e(route('admin.dashboard')); ?>"
                       data-tooltip="<?php echo e(__('navigation.dashboard')); ?>">
                        <span class="nav-icon"><i class="bi bi-speedometer2"></i></span>
                        <span class="nav-text"><?php echo e(__('navigation.dashboard')); ?></span>
                        <?php if(request()->routeIs('admin.dashboard')): ?>
                            <span class="nav-active-dot" aria-hidden="true"></span>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endif; ?>

            
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.excel-modules.analytics.index') ? 'active' : ''); ?>"
                   href="<?php echo e(route('admin.excel-modules.analytics.index')); ?>"
                   data-tooltip="<?php echo e(__('navigation.charts')); ?>">
                    <span class="nav-icon"><i class="bi bi-bar-chart-line-fill"></i></span>
                    <span class="nav-text"><?php echo e(__('navigation.charts')); ?></span>
                    <?php if(request()->routeIs('admin.excel-modules.analytics.index')): ?>
                        <span class="nav-active-dot" aria-hidden="true"></span>
                    <?php endif; ?>
                </a>
            </li>

            
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.excel-modules.*') && !request()->routeIs('admin.excel-modules.analytics.*') ? 'active' : ''); ?>"
                   href="<?php echo e(route('admin.excel-modules.index')); ?>"
                   data-tooltip="<?php echo e(__('excel.modules')); ?>">
                    <span class="nav-icon"><i class="bi bi-file-earmark-spreadsheet-fill"></i></span>
                    <span class="nav-text"><?php echo e(__('excel.modules')); ?></span>
                    <?php if(request()->routeIs('admin.excel-modules.*') && !request()->routeIs('admin.excel-modules.analytics.*')): ?>
                        <span class="nav-active-dot" aria-hidden="true"></span>
                    <?php endif; ?>
                </a>
            </li>

            
            <li class="nav-item">
                        <?php if(Route::has('admin.manufacturing-report.index')): ?>

                <a class="nav-link <?php echo e(request()->routeIs('admin.manufacturing-report.index') ? 'active' : ''); ?>"
                   href="<?php echo e(route('admin.manufacturing-report.index')); ?>"
                   data-tooltip="تقرير التصنيع الشامل">
                    <span class="nav-icon"><i class="bi bi-gear-wide-connected"></i></span>
                    <span class="nav-text">تقرير التصنيع الشامل</span>
                    <?php if(request()->routeIs('admin.manufacturing-report.index')): ?>
                        <span class="nav-active-dot" aria-hidden="true"></span>
                    <?php endif; ?>
                </a>
                <?php endif; ?>

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
                            <span class="nav-icon"><i class="bi bi-people-fill"></i></span>
                            <span class="nav-text"><?php echo e(__('navigation.users')); ?></span>
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
                            <span class="nav-icon"><i class="bi bi-shield-lock-fill"></i></span>
                            <span class="nav-text"><?php echo e(__('navigation.roles')); ?></span>
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
                            <span class="nav-icon"><i class="bi bi-key-fill"></i></span>
                            <span class="nav-text"><?php echo e(__('navigation.permissions')); ?></span>
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
                            <span class="nav-icon"><i class="bi bi-rulers"></i></span>
                            <span class="nav-text"><?php echo e(__('navigation.units')); ?></span>
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
                           href="<?php echo e(route('admin.settings.index')); ?>"
                           data-tooltip="<?php echo e(__('navigation.settings')); ?>">
                            <span class="nav-icon"><i class="bi bi-gear-fill"></i></span>
                            <span class="nav-text"><?php echo e(__('navigation.settings')); ?></span>
                            <?php if(request()->routeIs('admin.settings.*')): ?>
                                <span class="nav-active-dot" aria-hidden="true"></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>

        </ul>
    </nav>

    
    <?php if(auth()->guard()->check()): ?>
    <div class="sidebar-user">
        <div class="sidebar-user-inner">
            <div class="sidebar-user-avatar">
                <?php echo e(strtoupper(mb_substr(auth()->user()->name, 0, 1))); ?>

                <span class="sidebar-user-status"></span>
            </div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name"><?php echo e(auth()->user()->name); ?></div>
                <div class="sidebar-user-email"><?php echo e(auth()->user()->email); ?></div>
            </div>
            <form method="POST" action="<?php echo e(route('logout')); ?>" class="sidebar-user-logout">
                <?php echo csrf_field(); ?>
                <button type="submit" class="sidebar-logout-btn" title="تسجيل الخروج">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    
    <button type="button" class="sidebar-mini-toggle d-none d-lg-flex" id="sidebarMiniToggle" title="تصغير القائمة">
        <i class="bi bi-chevron-double-right" id="sidebarMiniIcon"></i>
    </button>
</aside>


<div class="sidebar-overlay" id="sidebarOverlay"></div>

<?php if (! $__env->hasRenderedOnce('0d58d605-353a-4f0b-8ed8-e643b2007908')): $__env->markAsRenderedOnce('0d58d605-353a-4f0b-8ed8-e643b2007908'); ?>
<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    'use strict';

    const sidebar    = document.getElementById('adminSidebar');
    const overlay    = document.getElementById('sidebarOverlay');
    const miniToggle = document.getElementById('sidebarMiniToggle');
    const miniIcon   = document.getElementById('sidebarMiniIcon');
    const closeBtn   = document.getElementById('sidebarCloseBtn');
    const navLinks   = sidebar?.querySelectorAll('.nav-link') || [];

    if (!sidebar) return;

    // ==================== MOBILE SIDEBAR ====================
    function openMobile() {
        sidebar.classList.add('mobile-open');
        overlay?.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeMobile() {
        sidebar.classList.remove('mobile-open');
        overlay?.classList.remove('active');
        document.body.style.overflow = '';
    }

    overlay?.addEventListener('click', closeMobile);
    closeBtn?.addEventListener('click', closeMobile);

    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 992) closeMobile();
        });
    });

    // Navbar toggle button
    const sidebarToggleBtn = document.getElementById('sidebarToggle');
    if (sidebarToggleBtn) {
        sidebarToggleBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            if (window.innerWidth < 992) {
                sidebar.classList.contains('mobile-open') ? closeMobile() : openMobile();
            } else {
                toggleMini();
            }
        });
    }

    // Escape closes
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && sidebar.classList.contains('mobile-open')) {
            closeMobile();
        }
    });

    // Resize cleanup
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            if (window.innerWidth >= 992) closeMobile();
        }, 150);
    });

    // ==================== MINI MODE ====================
    const MINI_KEY = 'ao_sidebar_mini';

    function toggleMini() {
        const isMini = sidebar.classList.toggle('mini');
        localStorage.setItem(MINI_KEY, isMini ? '1' : '0');
        updateMiniIcon();
    }
    function updateMiniIcon() {
        if (!miniIcon) return;
        miniIcon.className = sidebar.classList.contains('mini')
            ? 'bi bi-chevron-double-left'
            : 'bi bi-chevron-double-right';
    }

    if (localStorage.getItem(MINI_KEY) === '1' && window.innerWidth >= 992) {
        sidebar.classList.add('mini');
    }
    updateMiniIcon();

    miniToggle?.addEventListener('click', toggleMini);

    // ==================== KEYBOARD SHORTCUT ====================
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
            e.preventDefault();
            if (window.innerWidth >= 992) {
                toggleMini();
            } else {
                sidebar.classList.contains('mobile-open') ? closeMobile() : openMobile();
            }
        }
    });

    // ==================== AUTO SCROLL ACTIVE ====================
    const activeLink = sidebar.querySelector('.nav-link.active');
    if (activeLink) {
        setTimeout(() => activeLink.scrollIntoView({ block: 'nearest', behavior: 'smooth' }), 200);
    }

})();
</script>
<?php $__env->stopPush(); ?>
<?php endif; ?><?php /**PATH C:\Users\pc\Music\Crises-dashboard\Crises-dashboard\resources\views/components/admin/sidebar.blade.php ENDPATH**/ ?>