<header class="admin-navbar">
    <div class="navbar-start">
        <button class="btn btn-icon" id="sidebarToggle" aria-label="{{ __('common.toggle_sidebar') }}">
            <i class="bi bi-list"></i>
        </button>
        <h1 class="page-title">@yield('page_title', __('dashboard.title'))</h1>
    </div>

    <div class="navbar-end">
        {{-- Language Switcher --}}
        <div class="dropdown">
            <button class="btn btn-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-globe2"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item {{ app()->getLocale() === 'en' ? 'active' : '' }}"
                       href="{{ route('language.switch', 'en') }}">
                        {{ __('common.english') }}
                    </a>
                </li>
                <li>
                    <a class="dropdown-item {{ app()->getLocale() === 'ar' ? 'active' : '' }}"
                       href="{{ route('language.switch', 'ar') }}">
                        {{ __('common.arabic') }}
                    </a>
                </li>
            </ul>
        </div>

        {{-- Notifications placeholder --}}
        <button class="btn btn-icon" disabled title="{{ __('common.no_notifications') }}">
            <i class="bi bi-bell"></i>
        </button>

        {{-- User dropdown --}}
        <div class="dropdown">
            <button class="btn user-dropdown-toggle dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </span>
                <span class="user-name">{{ auth()->user()->name }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item disabled" href="#" aria-disabled="true">
                        <i class="bi bi-person"></i> {{ __('common.profile') }}
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}"
                          onsubmit="return confirm('{{ __('auth.logout_confirm') }}')">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="bi bi-box-arrow-right"></i> {{ __('common.logout') }}
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>