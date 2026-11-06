<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', __('dashboard.title'))
        — {{ __('common.app_name') }}
    </title>

    @if(app()->getLocale() === 'ar')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}?v={{ @filemtime(public_path('assets/css/admin.css')) ?: time() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/modern.css') }}?v={{ @filemtime(public_path('assets/css/modern.css')) ?: time() }}">

    @stack('styles')
</head>
<body class="admin-body locale-{{ app()->getLocale() }}">

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
        </div>
    </div>

    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/admin.js') }}?v={{ @filemtime(public_path('assets/js/admin.js')) ?: time() }}"></script>
    @stack('scripts')
</body>
</html>
