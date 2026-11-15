/* ============================================================
   AOI DYNAMIC PALETTE SYSTEM — JS
   يحفظ الاختيار في localStorage ويطبقه على <html>
   ============================================================ */
(function () {
    'use strict';

    const STORAGE_KEY = 'aoi_palette';
    const THEME_KEY = 'aoi_theme';

    // ==================== APPLY PALETTE ====================
    function applyPalette(palette) {
        if (!palette) palette = 'aoi';
        document.documentElement.setAttribute('data-palette', palette);
        localStorage.setItem(STORAGE_KEY, palette);

        // Update active state on swatches
        document.querySelectorAll('.ao-swatch').forEach(sw => {
            sw.classList.toggle('active', sw.dataset.palette === palette);
        });
    }

    // ==================== APPLY THEME (Light/Dark) ====================
    function applyTheme(theme) {
        const isDark = theme === 'dark';
        document.documentElement.classList.toggle('dark-boot', isDark);
        document.body.classList.toggle('dark-mode', isDark);
        document.querySelector('.admin-wrapper')?.classList.toggle('dark-mode', isDark);

        localStorage.setItem(THEME_KEY, theme);

        // Update mode buttons
        document.querySelectorAll('.ao-mode-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.theme === theme);
        });

        // Update theme toggle icon
        const icon = document.getElementById('themeIcon');
        if (icon) {
            icon.className = isDark ? 'bi bi-sun' : 'bi bi-moon-stars';
        }
    }

    // ==================== INIT ON LOAD ====================
    function init() {
        const savedPalette = localStorage.getItem(STORAGE_KEY) || 'aoi';
        const savedTheme = localStorage.getItem(THEME_KEY) || 'light';

        applyPalette(savedPalette);
        applyTheme(savedTheme);

        // Bind swatch clicks
        document.querySelectorAll('.ao-swatch').forEach(sw => {
            sw.addEventListener('click', () => {
                applyPalette(sw.dataset.palette);
            });
        });

        // Bind theme mode buttons
        document.querySelectorAll('.ao-mode-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                applyTheme(btn.dataset.theme);
            });
        });

        // Bind theme toggle (navbar)
        const themeBtn = document.getElementById('themeToggle');
        if (themeBtn) {
            themeBtn.addEventListener('click', () => {
                const current = localStorage.getItem(THEME_KEY) || 'light';
                applyTheme(current === 'dark' ? 'light' : 'dark');
            });
        }

        // Bind palette dock toggle
        const dock = document.getElementById('aoPaletteDock');
        const dockToggle = document.getElementById('aoPaletteToggle');
        const dockClose = document.getElementById('aoPaletteClose');

        if (dockToggle) {
            dockToggle.addEventListener('click', () => {
                dock?.classList.toggle('open');
            });
        }
        if (dockClose) {
            dockClose.addEventListener('click', () => {
                dock?.classList.remove('open');
            });
        }

        // Close dock on outside click
        document.addEventListener('click', (e) => {
            if (dock && dock.classList.contains('open')) {
                if (!e.target.closest('#aoPaletteDock')) {
                    dock.classList.remove('open');
                }
            }
        });

        // Close dock on Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                dock?.classList.remove('open');
            }
        });
    }

    // Run on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();