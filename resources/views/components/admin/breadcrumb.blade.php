<nav aria-label="breadcrumb" class="admin-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item breadcrumb-home">
            <a href="{{ route('admin.dashboard') }}"
               aria-label="{{ __('navigation.dashboard') }}">
                <i class="bi bi-house-door"></i>
                <span class="d-none d-sm-inline">{{ __('navigation.dashboard') }}</span>
            </a>
        </li>

        @hasSection('breadcrumb')
            @yield('breadcrumb')
        @endif
    </ol>
</nav>