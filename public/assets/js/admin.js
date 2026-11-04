/* =========================================================
   THEME SWITCHER
========================================================= */

document.addEventListener('DOMContentLoaded', () => {

    const wrapper = document.getElementById('adminWrapper');
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');

    if (!wrapper || !themeToggle || !themeIcon) return;

    const savedTheme = localStorage.getItem('admin-theme');

    if (savedTheme === 'dark') {
        wrapper.classList.add('dark-mode');
    }

    const updateThemeIcon = () => {
        const isDark = wrapper.classList.contains('dark-mode');

        themeIcon.className = isDark
            ? 'bi bi-sun'
            : 'bi bi-moon-stars';

        themeToggle.title = isDark
            ? 'Switch to light mode'
            : 'Switch to dark mode';
    };

    updateThemeIcon();

    themeToggle.addEventListener('click', () => {

        wrapper.classList.toggle('dark-mode');

        const isDark = wrapper.classList.contains('dark-mode');

        localStorage.setItem(
            'admin-theme',
            isDark ? 'dark' : 'light'
        );

        updateThemeIcon();

    });

});