<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" dir="<?php echo e(app()->getLocale() === 'ar' ? 'rtl' : 'ltr'); ?>"
      data-palette="<?php echo e(request()->cookie('aoi_palette', session('palette', 'navy'))); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', __('dashboard.title')); ?> — <?php echo e(__('common.app_name')); ?></title>

    <script>
    (function () {
        try {
            var theme = localStorage.getItem('admin-theme')
                || '<?php echo e(request()->cookie('aoi_theme', session('theme', 'light'))); ?>'
                || 'light';
            var palette = localStorage.getItem('aoi-palette')
                || '<?php echo e(request()->cookie('aoi_palette', session('palette', 'navy'))); ?>'
                || 'navy';
            document.documentElement.setAttribute('data-palette', palette);
            if (theme === 'dark') document.documentElement.classList.add('dark-boot');
            window.__AOI_THEME__ = theme;
            window.__AOI_PALETTE__ = palette;
        } catch (e) {}
    })();
    </script>

    <?php if(app()->getLocale() === 'ar'): ?>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <?php else: ?>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php endif; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/admin.css')); ?>?v=<?php echo e(@filemtime(public_path('assets/css/admin.css')) ?: time()); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/modern.css')); ?>?v=<?php echo e(@filemtime(public_path('assets/css/modern.css')) ?: time()); ?>">
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="admin-body locale-<?php echo e(app()->getLocale()); ?>">
    <div class="admin-wrapper" id="adminWrapper">
        <?php echo $__env->make('components.admin.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div class="admin-main" id="adminMain">
            <?php echo $__env->make('components.admin.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <main class="admin-content">
                <?php echo $__env->make('components.admin.breadcrumb', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->make('components.admin.alert', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->yieldContent('content'); ?>
            </main>
            <?php echo $__env->make('components.admin.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    
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
    <script src="<?php echo e(asset('assets/js/admin.js')); ?>?v=<?php echo e(@filemtime(public_path('assets/js/admin.js')) ?: time()); ?>"></script>
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
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\Users\pc\Music\Crises-dashboard\resources\views/layouts/admin.blade.php ENDPATH**/ ?>