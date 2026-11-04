
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', __('dashboard.title'))
        — {{ __('common.app_name') }}
    </title>

    {{-- Bootstrap 5 --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    {{-- Bootstrap Icons --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

    {{-- Admin Dashboard CSS --}}
    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin.css') }}?v={{ filemtime(public_path('assets/css/admin.css')) }}"
    >

    @stack('styles')
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="{{ asset('assets/js/admin.js') }}?v={{ filemtime(public_path('assets/js/admin.js')) }}"></script>

@stack('scripts')

@stack('scripts')

        </div>

    </div>

    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

</body>
</html>
