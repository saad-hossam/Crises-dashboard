(() => {
    const wrapper   = document.getElementById('adminWrapper');
    const sidebar   = document.getElementById('adminSidebar');
    const toggle    = document.getElementById('sidebarToggle');
    const backdrop  = document.getElementById('sidebarBackdrop');
    const searchBtn = document.getElementById('searchToggle');
    const cmdBar    = document.getElementById('commandBar');
    const cmdInput  = document.getElementById('commandInput');
    const clock     = document.getElementById('footerClock');
  
    const isMobile = () => window.matchMedia('(max-width: 991.98px)').matches;
  
    /* ---------- Sidebar toggle ---------- */
    toggle?.addEventListener('click', () => {
      if (isMobile()) {
        wrapper.classList.toggle('sidebar-open');
        backdrop.classList.toggle('visible',
          wrapper.classList.contains('sidebar-open'));
      } else {
        wrapper.classList.toggle('sidebar-collapsed');
        localStorage.setItem('sidebarCollapsed',
          wrapper.classList.contains('sidebar-collapsed') ? '1' : '0');
      }
    });
  
    backdrop?.addEventListener('click', () => {
      wrapper.classList.remove('sidebar-open');
      backdrop.classList.remove('visible');
    });
  
    /* Restore collapsed state (desktop only) */
    if (!isMobile() && localStorage.getItem('sidebarCollapsed') === '1') {
      wrapper.classList.add('sidebar-collapsed');
    }
  
    /* ---------- Collapsible groups ---------- */
    document.querySelectorAll('.sidebar-group-toggle').forEach(btn => {
      btn.addEventListener('click', () => {
        const group = btn.closest('.sidebar-group');
        const collapsed = group.dataset.collapsed === 'true';
        group.dataset.collapsed = collapsed ? 'false' : 'true';
        btn.setAttribute('aria-expanded', collapsed ? 'true' : 'false');
      });
    });
  
    /* ---------- Command bar (⌘K / Ctrl+K) ---------- */
    const openCmd = () => {
      if (!cmdBar) return;
      cmdBar.hidden = false;
      requestAnimationFrame(() => cmdInput?.focus());
    };
    const closeCmd = () => {
      if (!cmdBar) return;
      cmdBar.hidden = true;
      cmdInput.value = '';
    };
  
    searchBtn?.addEventListener('click', () => {
      cmdBar.hidden ? openCmd() : closeCmd();
    });
  
    document.addEventListener('keydown', (e) => {
      const metaK = (e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k';
      if (metaK) { e.preventDefault(); openCmd(); }
      if (e.key === 'Escape' && !cmdBar.hidden) closeCmd();
    });
  
    /* ---------- Live clock ---------- */
    const tick = () => {
      if (!clock) return;
      const d = new Date();
      clock.textContent =
        String(d.getHours()).padStart(2, '0') + ':' +
        String(d.getMinutes()).padStart(2, '0');
    };
    tick();
    setInterval(tick, 30_000);
  
    /* ---------- Close mobile sidebar on link click ---------- */
    document.querySelectorAll('.sidebar-nav .nav-link')
      .forEach(a => a.addEventListener('click', () => {
        if (isMobile()) {
          wrapper.classList.remove('sidebar-open');
          backdrop.classList.remove('visible');
        }
      }));
  })();