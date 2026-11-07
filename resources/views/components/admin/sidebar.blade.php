<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-brand">
        <a href="{{ route('admin.dashboard') }}" class="brand-link">
            <i class="bi bi-shield-exclamation"></i>
            <span class="brand-text">{{ __('common.app_name') }}</span>
        </a>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav flex-column">

            {{-- ============ Main navigation ============ --}}
            @can('dashboard.view')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                       href="{{ route('admin.dashboard') }}"
                       data-tooltip="{{ __('navigation.dashboard') }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>{{ __('navigation.dashboard') }}</span>
                        @if (request()->routeIs('admin.dashboard'))
                            <span class="nav-active-dot" aria-hidden="true"></span>
                        @endif
                    </a>
                </li>
            @endcan

           



         

            {{-- ============ Management section ============ --}}
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
                            <i class="bi bi-people"></i>
                            <span>{{ __('navigation.users') }}</span>
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
                            <i class="bi bi-shield-lock"></i>
                            <span>{{ __('navigation.roles') }}</span>
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
                            <i class="bi bi-key"></i>
                            <span>{{ __('navigation.permissions') }}</span>
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
                            <i class="bi bi-rulers"></i>
                            <span>{{ __('navigation.units') }}</span>
                            @if (request()->routeIs('admin.units.*'))
                                <span class="nav-active-dot" aria-hidden="true"></span>
                            @endif
                        </a>
                    </li>
                @endcan
            @endif

            {{-- ============ System section ============ --}}
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
                            <i class="bi bi-gear"></i>
                            <span>{{ __('navigation.settings') }}</span>
                        </a>
                    </li>
                @endcan

               
            @endif

        </ul>
    </nav>
</aside>

@once
@section('scripts')
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
@endsection
@endonce