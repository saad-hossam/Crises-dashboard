<header class="admin-navbar">

    {{-- Navbar Start --}}
    <div class="navbar-start">

        {{-- Sidebar Toggle --}}
        <button
            type="button"
            class="btn btn-icon sidebar-toggle"
            id="sidebarToggle"
            aria-label="{{ __('common.toggle_sidebar') }}"
            aria-controls="adminSidebar"
            aria-expanded="true"
        >
            <i class="bi bi-list"></i>
        </button>

        {{-- Page Title --}}
        <div class="page-heading">
            <h1 class="page-title">
                @yield('page_title', __('dashboard.title'))
            </h1>
        </div>

    </div>


    {{-- Navbar End --}}
    <div class="navbar-end">

        {{-- Language Switcher --}}
        <div class="dropdown navbar-item">

            <button
                type="button"
                class="btn btn-icon navbar-action dropdown-toggle"
                data-bs-toggle="dropdown"
                aria-expanded="false"
                aria-label="{{ __('common.language') }}"
                title="{{ __('common.language') }}"
            >
                <i class="bi bi-globe2"></i>
            </button>

            <ul class="dropdown-menu dropdown-menu-end navbar-dropdown">

                <li>
                    <a
                        class="dropdown-item {{ app()->getLocale() === 'en' ? 'active' : '' }}"
                        href="{{ route('language.switch', 'en') }}"
                    >
                        <i class="bi bi-translate"></i>
                        {{ __('common.english') }}

                        @if(app()->getLocale() === 'en')
                            <i class="bi bi-check2 ms-auto"></i>
                        @endif
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item {{ app()->getLocale() === 'ar' ? 'active' : '' }}"
                        href="{{ route('language.switch', 'ar') }}"
                    >
                        <i class="bi bi-translate"></i>
                        {{ __('common.arabic') }}

                        @if(app()->getLocale() === 'ar')
                            <i class="bi bi-check2 ms-auto"></i>
                        @endif
                    </a>
                </li>

            </ul>
        </div>


        {{-- Dark / Light Mode Toggle --}}
        <button
            type="button"
            class="btn btn-icon navbar-action theme-toggle"
            id="themeToggle"
            aria-label="{{ __('common.toggle_theme') }}"
            title="{{ __('common.toggle_theme') }}"
        >
            <i class="bi bi-moon-stars" id="themeIcon"></i>
        </button>


        {{-- Notifications --}}
     


        {{-- User Dropdown --}}
        @auth
            <div class="dropdown navbar-user">

                <button
                    type="button"
                    class="btn user-dropdown-toggle dropdown-toggle"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                >

                    <span class="user-avatar">
                        {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                    </span>

                    <span class="user-info">
                        <span class="user-name">
                            {{ auth()->user()->name }}
                        </span>

                        <!-- <span class="user-role">
                            {{ __('common.administrator') }}
                        </span> -->
                    </span>

                    <i class="bi bi-chevron-down user-dropdown-icon"></i>

                </button>


                <ul class="dropdown-menu dropdown-menu-end navbar-dropdown user-menu">

                    {{-- User Information --}}
                    <li class="user-menu-header">

                        <div class="user-avatar user-avatar-lg">
                            {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                        </div>

                        <div class="user-menu-info">
                            <strong>
                                {{ auth()->user()->name }}
                            </strong>

                            <small>
                                {{ auth()->user()->email }}
                            </small>
                        </div>

                    </li>

                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    {{-- Profile --}}
                    <li>
                        <a
                            class="dropdown-item disabled"
                            href="#"
                            aria-disabled="true"
                        >
                            <i class="bi bi-person"></i>
                            {{ __('common.profile') }}
                        </a>
                    </li>

                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    {{-- Logout --}}
                    <li>
                        <form
                            method="POST"
                            action="{{ route('logout') }}"
                            onsubmit="return confirm(@js(__('auth.logout_confirm')))"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="dropdown-item text-danger"
                            >
                                <i class="bi bi-box-arrow-right"></i>
                                {{ __('common.logout') }}
                            </button>
                        </form>
                    </li>

                </ul>

            </div>
        @endauth

    </div>

</header>
