<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
      data-palette="{{ request()->cookie('aoi_palette', session('palette', 'navy')) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('dashboard.title')) — {{ __('common.app_name') }}</title>

    <script>
    (function () {
        try {
            var theme = localStorage.getItem('admin-theme')
                || '{{ request()->cookie('aoi_theme', session('theme', 'light')) }}'
                || 'light';
            var palette = localStorage.getItem('aoi-palette')
                || '{{ request()->cookie('aoi_palette', session('palette', 'navy')) }}'
                || 'navy';
            document.documentElement.setAttribute('data-palette', palette);
            if (theme === 'dark') document.documentElement.classList.add('dark-boot');
            window.__AOI_THEME__ = theme;
            window.__AOI_PALETTE__ = palette;
        } catch (e) {}
    })();
    </script>

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

    {{-- Color palette — fixed LEFT edge --}}
    <aside class="ao-palette-dock" id="aoPaletteDock" aria-label="Color palette">
        <button type="button" class="ao-palette-toggle" id="aoPaletteToggle" title="Theme & colors" aria-expanded="false">
            <i class="bi bi-palette2"></i>
        </button>
        <div class="ao-palette-panel" id="aoPalettePanel">
            <div class="ao-palette-header">
                <span>Colors</span>
                <button type="button" class="ao-palette-close" id="aoPaletteClose" aria-label="Close">&times;</button>
            </div>
            <div class="ao-palette-title">Palette</div>
                                                <div class="ao-palette-swatches">
                <button type="button" class="ao-swatch" data-palette="navy" style="--sw:#2c5282" title="Navy"><span>Navy</span></button>
                <button type="button" class="ao-swatch" data-palette="graphite" style="--sw:#556274" title="Graphite"><span>Graphite</span></button>
                <button type="button" class="ao-swatch" data-palette="forest" style="--sw:#3d7a6a" title="Forest"><span>Forest</span></button>
                <button type="button" class="ao-swatch" data-palette="steel" style="--sw:#4a7594" title="Steel"><span>Steel</span></button>
                <button type="button" class="ao-swatch" data-palette="wine" style="--sw:#854a5c" title="Wine"><span>Wine</span></button>
            </div>
            <div class="ao-palette-title">Appearance</div>
            <div class="ao-palette-modes">
                <button type="button" class="ao-mode-btn" data-theme="light"><i class="bi bi-sun-fill"></i> Light</button>
                <button type="button" class="ao-mode-btn" data-theme="dark"><i class="bi bi-moon-stars-fill"></i> Dark</button>
            </div>
        </div>
    </aside>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/admin.js') }}?v={{ @filemtime(public_path('assets/js/admin.js')) ?: time() }}"></script>
    <script>
    (function () {
        const wrapper = document.getElementById('adminWrapper');
        function applyTheme(theme) {
            const dark = theme === 'dark';
            document.documentElement.classList.toggle('dark-boot', dark);
            document.body.classList.toggle('dark-mode', dark);
            if (wrapper) wrapper.classList.toggle('dark-mode', dark);
            localStorage.setItem('admin-theme', theme);
            document.cookie = 'aoi_theme=' + theme + ';path=/;max-age=31536000';
            const icon = document.getElementById('themeIcon');
            if (icon) icon.className = dark ? 'bi bi-sun' : 'bi bi-moon-stars';
            document.querySelectorAll('.ao-mode-btn').forEach(b => {
                b.classList.toggle('active', b.dataset.theme === theme);
            });
        }
        function applyPalette(p) {
            document.documentElement.setAttribute('data-palette', p);
            localStorage.setItem('aoi-palette', p);
            document.cookie = 'aoi_palette=' + p + ';path=/;max-age=31536000';
            document.querySelectorAll('.ao-swatch').forEach(b => {
                b.classList.toggle('active', b.dataset.palette === p);
            });
        }
        applyTheme(window.__AOI_THEME__ || 'light');
        applyPalette(window.__AOI_PALETTE__ || 'navy');

        const dock = document.getElementById('aoPaletteDock');
        const toggle = document.getElementById('aoPaletteToggle');
        if (toggle) toggle.addEventListener('click', () => dock.classList.toggle('open'));
        document.querySelectorAll('.ao-swatch').forEach(btn => {
            btn.addEventListener('click', () => applyPalette(btn.dataset.palette));
        });
        document.querySelectorAll('.ao-mode-btn').forEach(btn => {
            btn.addEventListener('click', () => applyTheme(btn.dataset.theme));
        });
        const themeToggle = document.getElementById('themeToggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', function (e) {
                e.preventDefault();
                const next = (wrapper && wrapper.classList.contains('dark-mode')) ? 'light' : 'dark';
                applyTheme(next);
            });
        }
    })();
    </script>
    @stack('scripts')
</body>
</html>