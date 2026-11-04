
<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', __('dashboard.title'))
        — {{ __('common.app_name') }}
    </title>

  @vite(['resources/css/app.scss', 'resources/css/admin.css'])

    
</head>

<body class="admin-body">

    <div class="admin-wrapper" id="adminWrapper">

        @include('components.admin.sidebar')

        <div class="admin-main" id="adminMain">

            @include('components.admin.navbar')

            <main class="admin-content">

                @include('components.admin.breadcrumb')

                @include('components.admin.alert')

                @yield('content')

            </main>

            @include('components.admin.footer')
@vite(['resources/js/app.js', 'resources/js/admin.js'])
@stack('scripts')

        </div>

    </div>

    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

</body>
</html>
