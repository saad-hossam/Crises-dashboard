<aside class="admin-sidebar" id="adminSidebar">

    {{-- ==================== BRAND ==================== --}}
    <div class="sidebar-brand">
        <a href="{{ route('admin.dashboard') }}" class="brand-link">
            <div class="brand-icon">
                <i class="bi bi-shield-exclamation"></i>
            </div>
            <div class="brand-text-wrap">
                <span class="brand-text">{{ __('common.app_name') }}</span>
                <span class="brand-sub">AOI Crises</span>
            </div>
        </a>
        <button type="button" class="sidebar-close-btn d-lg-none" id="sidebarCloseBtn" aria-label="إغلاق">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    {{-- ==================== NAVIGATION ==================== --}}
    <nav class="sidebar-nav" id="sidebarNav">
        <ul class="nav flex-column">

            {{-- ============ MAIN ============ --}}
           

            @can('dashboard.view')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                       href="{{ route('admin.dashboard') }}"
                       data-tooltip="{{ __('navigation.dashboard') }}">
                        <span class="nav-icon"><i class="bi bi-speedometer2"></i></span>
                        <span class="nav-text">{{ __('navigation.dashboard') }}</span>
                        @if (request()->routeIs('admin.dashboard'))
                            <span class="nav-active-dot" aria-hidden="true"></span>
                        @endif
                    </a>
                </li>
            @endcan

            {{-- Charts --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.excel-modules.analytics.index') ? 'active' : '' }}"
                   href="{{ route('admin.excel-modules.analytics.index') }}"
                   data-tooltip="{{ __('navigation.charts') }}">
                    <span class="nav-icon"><i class="bi bi-bar-chart-line-fill"></i></span>
                    <span class="nav-text">{{ __('navigation.charts') }}</span>
                    @if (request()->routeIs('admin.excel-modules.analytics.index'))
                        <span class="nav-active-dot" aria-hidden="true"></span>
                    @endif
                </a>
            </li>

            {{-- Excel Modules --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.excel-modules.*') && !request()->routeIs('admin.excel-modules.analytics.*') ? 'active' : '' }}"
                   href="{{ route('admin.excel-modules.index') }}"
                   data-tooltip="{{ __('excel.modules') }}">
                    <span class="nav-icon"><i class="bi bi-file-earmark-spreadsheet-fill"></i></span>
                    <span class="nav-text">{{ __('excel.modules') }}</span>
                    @if (request()->routeIs('admin.excel-modules.*') && !request()->routeIs('admin.excel-modules.analytics.*'))
                        <span class="nav-active-dot" aria-hidden="true"></span>
                    @endif
                </a>
            </li>

            {{-- Manufacturing Report --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.manufacturing-report.index') ? 'active' : '' }}"
                   href="{{ route('admin.manufacturing-report.index') }}"
                   data-tooltip="تقرير التصنيع الشامل">
                    <span class="nav-icon"><i class="bi bi-gear-wide-connected"></i></span>
                    <span class="nav-text">تقرير التصنيع الشامل</span>
                    @if (request()->routeIs('admin.manufacturing-report.*'))
                        <span class="nav-active-dot" aria-hidden="true"></span>
                    @endif
                </a>
            </li>

            {{-- ============ MANAGEMENT ============ --}}
            @if (auth()->user()->can('users.view')
              || auth()->user()->can('roles.view')
              || auth()->user()->can('permissions.view')
              || auth()->user()->can('units.view'))
                <li class="nav-item sidebar-section-label">
                    <span>{{ __('navigation.management') }}</span>
                </li>

                @can('users.view')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                           href="{{ route('admin.users.index') }}"
                           data-tooltip="{{ __('navigation.users') }}">
                            <span class="nav-icon"><i class="bi bi-people-fill"></i></span>
                            <span class="nav-text">{{ __('navigation.users') }}</span>
                            @if (request()->routeIs('admin.users.*'))
                                <span class="nav-active-dot" aria-hidden="true"></span>
                            @endif
                        </a>
                    </li>
                @endcan

                @can('roles.view')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}"
                           href="{{ route('admin.roles.index') }}"
                           data-tooltip="{{ __('navigation.roles_permissions') }}">
                            <span class="nav-icon"><i class="bi bi-shield-lock-fill"></i></span>
                            <span class="nav-text">{{ __('navigation.roles') }}</span>
                            @if (request()->routeIs('admin.roles.*'))
                                <span class="nav-active-dot" aria-hidden="true"></span>
                            @endif
                        </a>
                    </li>
                @endcan

                @can('permissions.view')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}"
                           href="{{ route('admin.permissions.index') }}"
                           data-tooltip="{{ __('navigation.permissions') }}">
                            <span class="nav-icon"><i class="bi bi-key-fill"></i></span>
                            <span class="nav-text">{{ __('navigation.permissions') }}</span>
                            @if (request()->routeIs('admin.permissions.*'))
                                <span class="nav-active-dot" aria-hidden="true"></span>
                            @endif
                        </a>
                    </li>
                @endcan

                @can('units.view')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.units.*') ? 'active' : '' }}"
                           href="{{ route('admin.units.index') }}"
                           data-tooltip="{{ __('navigation.units') }}">
                            <span class="nav-icon"><i class="bi bi-rulers"></i></span>
                            <span class="nav-text">{{ __('navigation.units') }}</span>
                            @if (request()->routeIs('admin.units.*'))
                                <span class="nav-active-dot" aria-hidden="true"></span>
                            @endif
                        </a>
                    </li>
                @endcan
            @endif

            {{-- ============ SYSTEM ============ --}}
            @if (auth()->user()->can('settings.manage')
              || auth()->user()->can('activity_logs.view'))
                <li class="nav-item sidebar-section-label">
                    <span>{{ __('navigation.system') }}</span>
                </li>

                @can('settings.manage')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"
                           href="{{ route('admin.settings.index') }}"
                           data-tooltip="{{ __('navigation.settings') }}">
                            <span class="nav-icon"><i class="bi bi-gear-fill"></i></span>
                            <span class="nav-text">{{ __('navigation.settings') }}</span>
                            @if (request()->routeIs('admin.settings.*'))
                                <span class="nav-active-dot" aria-hidden="true"></span>
                            @endif
                        </a>
                    </li>
                @endcan
            @endif

        </ul>
    </nav>

    {{-- ==================== USER CARD ==================== --}}
    @auth
    <div class="sidebar-user">
        <div class="sidebar-user-inner">
            <div class="sidebar-user-avatar">
                {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                <span class="sidebar-user-status"></span>
            </div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                <div class="sidebar-user-email">{{ auth()->user()->email }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="sidebar-user-logout">
                @csrf
                <button type="submit" class="sidebar-logout-btn" title="تسجيل الخروج">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>
    @endauth

    {{-- ==================== MINI TOGGLE (Desktop) ==================== --}}
    <button type="button" class="sidebar-mini-toggle d-none d-lg-flex" id="sidebarMiniToggle" title="تصغير القائمة">
        <i class="bi bi-chevron-double-right" id="sidebarMiniIcon"></i>
    </button>
</aside>

{{-- ==================== OVERLAY (Mobile) ==================== --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

@once
@push('scripts')
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
@endpush
@endonce