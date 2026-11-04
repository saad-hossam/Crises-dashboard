<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', __('common.app_name'))</title>

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

    {{-- Application CSS --}}
    <link
        rel="stylesheet"
        href="{{ asset('assets/css/app.css') }}?v=1"
    >

    {{-- Admin Dashboard CSS --}}
    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin.css') }}?v=1"
    >

    {{-- Page-specific styles --}}
    @stack('styles')
</head>

<body class="auth-body">

    {{-- Page Content --}}
    @yield('content')


    {{-- Bootstrap JavaScript --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Application JavaScript --}}
    <script src="{{ asset('assets/js/app.js') }}?v=1"></script>

    {{-- Admin Dashboard JavaScript --}}
    <script src="{{ asset('assets/js/admin.js') }}?v=1"></script>

    {{-- Page-specific scripts --}}
    @stack('scripts')

</body>
</html>
