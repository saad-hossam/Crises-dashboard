document.addEventListener('DOMContentLoaded', () => {
    const wrapper = document.getElementById('adminWrapper');
    const toggleBtn = document.getElementById('sidebarToggle');
    const backdrop = document.getElementById('sidebarBackdrop');

    if (!wrapper || !toggleBtn) return;

    const isMobile = () => window.innerWidth < 992;

    // Persist desktop collapse state
    const savedState = localStorage.getItem('sidebarCollapsed');
    if (savedState === 'true' && !isMobile()) {
        wrapper.classList.add('sidebar-collapsed');
    }

    toggleBtn.addEventListener('click', () => {
        if (isMobile()) {
            wrapper.classList.toggle('sidebar-mobile-open');
        } else {
            wrapper.classList.toggle('sidebar-collapsed');
            localStorage.setItem(
                'sidebarCollapsed',
                wrapper.classList.contains('sidebar-collapsed')
            );
        }
    });

    if (backdrop) {
        backdrop.addEventListener('click', () => {
            wrapper.classList.remove('sidebar-mobile-open');
        });
    }

    // Close mobile sidebar on resize to desktop
    window.addEventListener('resize', () => {
        if (!isMobile()) {
            wrapper.classList.remove('sidebar-mobile-open');
        }
    });
});