/* =========================================================
   ADMIN DASHBOARD INTERACTIONS
========================================================= */

document.addEventListener('DOMContentLoaded', () => {

    const wrapper = document.getElementById('adminWrapper');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');
    const sidebar = document.getElementById('adminSidebar');

    if (!wrapper || !sidebarToggle) return;

    const mobileBreakpoint = 992;

    const isMobile = () => window.innerWidth < mobileBreakpoint;

    // Toggle sidebar
    sidebarToggle.addEventListener('click', () => {

        if (isMobile()) {
            wrapper.classList.toggle('sidebar-mobile-open');
        } else {
            wrapper.classList.toggle('sidebar-collapsed');
        }

    });

    // Close mobile sidebar
    const closeMobileSidebar = () => {
        wrapper.classList.remove('sidebar-mobile-open');
    };

    // Backdrop click
    sidebarBackdrop?.addEventListener('click', closeMobileSidebar);

    // Close after navigation
    sidebar?.querySelectorAll('.nav-link').forEach(link => {

        link.addEventListener('click', () => {

            if (isMobile()) {
                closeMobileSidebar();
            }

        });

    });

    // Escape key
    document.addEventListener('keydown', event => {

        if (event.key === 'Escape') {
            closeMobileSidebar();
        }

    });

    // Handle window resizing
    window.addEventListener('resize', () => {

        if (!isMobile()) {
            closeMobileSidebar();
        }

    });

});

/* ========================================
   DARK / LIGHT MODE
======================================== */

document.addEventListener('DOMContentLoaded', function () {
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');

    if (!themeToggle || !themeIcon) {
        return;
    }

    const savedTheme = localStorage.getItem('admin-theme') || 'light';

    applyTheme(savedTheme);

    themeToggle.addEventListener('click', function () {
        const isDark = document.body.classList.contains('dark-mode');

        const newTheme = isDark ? 'light' : 'dark';

        applyTheme(newTheme);

        localStorage.setItem('admin-theme', newTheme);
    });

    function applyTheme(theme) {
        const isDark = theme === 'dark';

        document.body.classList.toggle('dark-mode', isDark);

        themeIcon.className = isDark
            ? 'bi bi-sun'
            : 'bi bi-moon-stars';

        themeToggle.setAttribute(
            'aria-label',
            isDark ? 'Switch to light mode' : 'Switch to dark mode'
        );

        themeToggle.setAttribute(
            'title',
            isDark ? 'Light Mode' : 'Dark Mode'
        );
    }
});