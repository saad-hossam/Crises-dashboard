<header class="admin-navbar ao-navbar">
    <div class="ao-navbar-inner">
        
        <div class="navbar-start">
            <button type="button" class="btn ao-icon-btn sidebar-toggle" id="sidebarToggle" aria-label="Menu">
                <i class="bi bi-list"></i>
            </button>
            <div class="page-heading">
                <p class="ao-page-kicker d-none d-md-block mb-0">AOI Crises</p>
                <h1 class="page-title mb-0"><?php echo $__env->yieldContent('page_title', __('dashboard.title')); ?></h1>
            </div>
        </div>

        
        <div class="navbar-center d-none d-md-flex">
            <div class="ao-search" id="aoGlobalSearch">
                <i class="bi bi-search ao-search-icon"></i>
                <input type="search"
                       id="aoSearchInput"
                       class="form-control ao-search-input"
                       placeholder="<?php echo e(__('common.search')); ?>… (موديولات، سجلات، مستخدمون)"
                       autocomplete="off"
                       spellcheck="false"
                       role="combobox"
                       aria-autocomplete="list"
                       aria-expanded="false"
                       aria-controls="aoSearchResults">
                <kbd class="ao-search-kbd d-none d-lg-inline">/</kbd>
                <div class="ao-search-panel" id="aoSearchResults" hidden>
                    <div class="ao-search-hint" id="aoSearchHint">اكتب للبحث في كل الموقع…</div>
                    <div class="ao-search-body" id="aoSearchBody"></div>
                    <div class="ao-search-footer" id="aoSearchFooter" hidden>
                        <span><kbd>↑</kbd><kbd>↓</kbd> للتنقل</span>
                        <span><kbd>Enter</kbd> للفتح</span>
                        <span><kbd>Esc</kbd> للإغلاق</span>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="navbar-end">
            
          

            
            <a href="<?php echo e(route('admin.excel-modules.create')); ?>" class="btn ao-cta d-none d-md-inline-flex">
                <i class="bi bi-cloud-upload"></i>
                <span><?php echo e(__('excel.upload') ?? 'Upload'); ?></span>
            </a>

            
            <div class="dropdown">
                <button type="button" class="btn ao-chip-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-translate"></i>
                    <span><?php echo e(app()->getLocale() === 'ar' ? 'العربية' : 'EN'); ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end ao-menu">
                    <li>
                        <a class="dropdown-item <?php echo e(app()->getLocale()==='ar'?'active':''); ?>" href="<?php echo e(route('language.switch','ar')); ?>">
                            <span>العربية</span>
                            <?php if(app()->getLocale()==='ar'): ?><i class="bi bi-check2 ms-auto"></i><?php endif; ?>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item <?php echo e(app()->getLocale()==='en'?'active':''); ?>" href="<?php echo e(route('language.switch','en')); ?>">
                            <span>English</span>
                            <?php if(app()->getLocale()==='en'): ?><i class="bi bi-check2 ms-auto"></i><?php endif; ?>
                        </a>
                    </li>
                </ul>
            </div>

            
           

            
            

            
            <?php if(auth()->guard()->check()): ?>
            <div class="dropdown">
                <button type="button" class="btn ao-user dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="ao-avatar">
                        <?php echo e(strtoupper(mb_substr(auth()->user()->name, 0, 1))); ?>

                        <span class="ao-avatar-status" title="متصل الآن"></span>
                    </span>
                    <span class="d-none d-md-inline ao-user-name"><?php echo e(auth()->user()->name); ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end ao-menu ao-menu-user">
                    <li class="ao-menu-head">
                        <div class="ao-menu-head-avatar"><?php echo e(strtoupper(mb_substr(auth()->user()->name, 0, 1))); ?></div>
                        <div class="ao-menu-head-info">
                            <strong><?php echo e(auth()->user()->name); ?></strong>
                            <small><?php echo e(auth()->user()->email); ?></small>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="<?php echo e(route('admin.settings.index')); ?>">
                            <i class="bi bi-person-gear"></i>
                            <span><?php echo e(__('navigation.settings')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?php echo e(route('admin.dashboard')); ?>">
                            <i class="bi bi-speedometer2"></i>
                            <span><?php echo e(__('navigation.dashboard')); ?></span>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="<?php echo e(route('logout')); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>تسجيل الخروج</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="ao-search-mobile d-md-none" id="aoSearchMobileBar" hidden>
        <div class="ao-search ao-search-mobile-inner" id="aoGlobalSearchMobile">
            <i class="bi bi-search ao-search-icon"></i>
            <input type="search" id="aoSearchInputMobile" class="form-control ao-search-input"
                   placeholder="<?php echo e(__('common.search')); ?>…" autocomplete="off">
            <button type="button" class="btn ao-search-mobile-close" id="aoSearchMobileClose" aria-label="إغلاق">
                <i class="bi bi-x-lg"></i>
            </button>
            <div class="ao-search-panel" id="aoSearchResultsMobile" hidden>
                <div class="ao-search-hint" id="aoSearchHintMobile">اكتب للبحث…</div>
                <div class="ao-search-body" id="aoSearchBodyMobile"></div>
            </div>
        </div>
    </div>
</header>

<?php if (! $__env->hasRenderedOnce('6a84a0a4-26cb-4a33-ad64-16e7fe15ee78')): $__env->markAsRenderedOnce('6a84a0a4-26cb-4a33-ad64-16e7fe15ee78'); ?>


<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    'use strict';

    // ==================== CONFIG ====================
    const SEARCH_URL = <?php echo json_encode(route('admin.search'), 15, 512) ?>;
    const RECENT_KEY = 'ao_recent_searches';
    const MAX_RECENT = 5;

    // ==================== STATE ====================
    let timer = null;
    let aborter = null;
    let active = -1;
    let currentQuery = '';

    // ==================== ELEMENTS ====================
    const desktopInput = document.getElementById('aoSearchInput');
    const mobileInput = document.getElementById('aoSearchInputMobile');
    const desktopPanel = document.getElementById('aoSearchResults');
    const mobilePanel = document.getElementById('aoSearchResultsMobile');
    const desktopBody = document.getElementById('aoSearchBody');
    const mobileBody = document.getElementById('aoSearchBodyMobile');
    const desktopHint = document.getElementById('aoSearchHint');
    const mobileHint = document.getElementById('aoSearchHintMobile');
    const desktopFooter = document.getElementById('aoSearchFooter');
    const desktopWrap = document.getElementById('aoGlobalSearch');
    const mobileWrap = document.getElementById('aoGlobalSearchMobile');
    const mobileBar = document.getElementById('aoSearchMobileBar');
    const mobileBtn = document.getElementById('aoSearchMobileBtn');
    const mobileClose = document.getElementById('aoSearchMobileClose');

    // ==================== RECENT SEARCHES ====================
    function loadRecent() {
        try { return JSON.parse(localStorage.getItem(RECENT_KEY) || '[]'); }
        catch { return []; }
    }
    function saveRecent(query) {
        query = query.trim();
        if (!query) return;
        const recent = loadRecent().filter(q => q !== query);
        recent.unshift(query);
        localStorage.setItem(RECENT_KEY, JSON.stringify(recent.slice(0, MAX_RECENT)));
    }
    function clearRecent() {
        localStorage.removeItem(RECENT_KEY);
    }

    // ==================== HELPERS ====================
    function escapeHtml(s) {
        return String(s ?? '').replace(/[&<>"']/g, c => ({
            '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
        }[c]));
    }
    function highlight(text, q) {
        const t = escapeHtml(text);
        if (!q) return t;
        try {
            const re = new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'ig');
            return t.replace(re, '<mark>$1</mark>');
        } catch (e) { return t; }
    }

    // ==================== RENDER ====================
    function renderEmpty(body, hint, message) {
        body.innerHTML = '';
        hint.hidden = false;
        hint.textContent = message || 'اكتب للبحث في كل الموقع…';
    }

    function renderLoading(body, hint) {
        body.innerHTML = '';
        hint.hidden = true;
        body.innerHTML = `
            <div class="ao-search-loading">
                <div class="spinner"></div>
                <span style="font-size:.8rem;color:#94a3b8;">جاري البحث…</span>
            </div>`;
    }

    function renderRecent(body, hint) {
        const recent = loadRecent();
        if (!recent.length) {
            renderEmpty(body, hint, 'اكتب للبحث في كل الموقع…');
            return;
        }
        hint.hidden = true;
        body.innerHTML = `
            <div class="ao-search-group">
                <div class="ao-search-group-label">
                    <span>بحث حديث</span>
                </div>
                ${recent.map(q => `
                    <div class="ao-search-item" data-recent="${escapeHtml(q)}">
                        <span class="ao-search-icon-box"><i class="bi bi-clock-history"></i></span>
                        <span class="ao-search-text">
                            <span class="ao-search-title">${escapeHtml(q)}</span>
                        </span>
                    </div>
                `).join('')}
            </div>`;
    }

    function renderResults(body, hint, footer, data) {
        if (!data.groups || !data.groups.length || data.total === 0) {
            body.innerHTML = '';
            hint.hidden = true;
            footer && (footer.hidden = true);
            body.innerHTML = `
                <div class="ao-search-empty">
                    <i class="bi bi-search"></i>
                    <p>لا توجد نتائج لـ "<strong>${escapeHtml(data.query)}</strong>"</p>
                </div>`;
            return;
        }
        hint.hidden = true;
        if (footer) footer.hidden = false;

        let html = '';
        data.groups.forEach(g => {
            html += '<div class="ao-search-group">';
            html += '<div class="ao-search-group-label">' + escapeHtml(g.label) + '</div>';
            (g.results || []).forEach(r => {
                html += `
                    <a class="ao-search-item" href="${escapeHtml(r.url)}">
                        <span class="ao-search-icon-box"><i class="bi ${escapeHtml(r.icon || 'bi-search')}"></i></span>
                        <span class="ao-search-text">
                            <span class="ao-search-title">${highlight(r.title, data.query)}</span>
                            <span class="ao-search-sub">${escapeHtml(r.subtitle || '')}</span>
                        </span>
                    </a>`;
            });
            html += '</div>';
        });
        body.innerHTML = html;
        active = -1;
    }

    // ==================== SEARCH ====================
    function doSearch(query, which) {
        const isDesktop = which === 'desktop';
        const body = isDesktop ? desktopBody : mobileBody;
        const hint = isDesktop ? desktopHint : mobileHint;
        const footer = isDesktop ? desktopFooter : null;

        query = (query || '').trim();
        currentQuery = query;

        showPanel(which);

        if (!query) {
            renderRecent(body, hint);
            return;
        }

        renderLoading(body, hint);

        if (aborter) aborter.abort();
        aborter = new AbortController();

        fetch(SEARCH_URL + '?q=' + encodeURIComponent(query), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            signal: aborter.signal,
            credentials: 'same-origin'
        })
        .then(r => r.json())
        .then(data => renderResults(body, hint, footer, data))
        .catch(err => {
            if (err.name === 'AbortError') return;
            renderEmpty(body, hint, 'تعذر إكمال البحث');
        });
    }

    // ==================== PANEL ====================
    function showPanel(which) {
        const panel = which === 'desktop' ? desktopPanel : mobilePanel;
        if (panel) panel.hidden = false;
        if (which === 'desktop' && desktopInput) {
            desktopInput.setAttribute('aria-expanded', 'true');
        }
    }
    function hidePanel(which) {
        if (!which || which === 'desktop') {
            if (desktopPanel) desktopPanel.hidden = true;
            if (desktopInput) desktopInput.setAttribute('aria-expanded', 'false');
        }
        if (!which || which === 'mobile') {
            if (mobilePanel) mobilePanel.hidden = true;
        }
        active = -1;
    }

    // ==================== EVENTS ====================
    function bindInput(input, which) {
        if (!input) return;
        input.addEventListener('input', function (e) {
            clearTimeout(timer);
            const q = e.target.value;
            // sync both
            if (desktopInput && e.target !== desktopInput) desktopInput.value = q;
            if (mobileInput && e.target !== mobileInput) mobileInput.value = q;
            timer = setTimeout(() => doSearch(q, which), 200);
        });
        input.addEventListener('focus', function () {
            if (!input.value.trim()) {
                doSearch('', which);
            } else {
                showPanel(which);
            }
        });
        input.addEventListener('keydown', function (e) {
            const body = which === 'desktop' ? desktopBody : mobileBody;
            const items = body.querySelectorAll('.ao-search-item');
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (!items.length) return;
                active = Math.min(active + 1, items.length - 1);
                items.forEach((el, i) => el.classList.toggle('active', i === active));
                items[active]?.scrollIntoView({ block: 'nearest' });
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (!items.length) return;
                active = Math.max(active - 1, 0);
                items.forEach((el, i) => el.classList.toggle('active', i === active));
                items[active]?.scrollIntoView({ block: 'nearest' });
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (active >= 0 && items[active]) {
                    items[active].click();
                } else if (input.value.trim()) {
                    saveRecent(input.value);
                }
            } else if (e.key === 'Escape') {
                hidePanel(which);
                input.blur();
            }
        });
    }

    bindInput(desktopInput, 'desktop');
    bindInput(mobileInput, 'mobile');

    // Click handlers for recent items
    function bindRecentClicks(body, which) {
        body.addEventListener('click', function (e) {
            const recent = e.target.closest('[data-recent]');
            if (!recent) return;
            const q = recent.dataset.recent;
            const input = which === 'desktop' ? desktopInput : mobileInput;
            if (input) {
                input.value = q;
                doSearch(q, which);
                input.focus();
            }
        });
    }
    if (desktopBody) bindRecentClicks(desktopBody, 'desktop');
    if (mobileBody) bindRecentClicks(mobileBody, 'mobile');

    // Save recent on result click
    [desktopBody, mobileBody].forEach(body => {
        if (!body) return;
        body.addEventListener('click', function (e) {
            if (e.target.closest('.ao-search-item:not([data-recent])')) {
                if (currentQuery) saveRecent(currentQuery);
            }
        });
    });

    // Click outside
    document.addEventListener('click', function (e) {
        if (!e.target.closest('#aoGlobalSearch') && !e.target.closest('#aoGlobalSearchMobile')) {
            hidePanel();
        }
    });

    // ==================== SHORTCUT `/` ====================
    document.addEventListener('keydown', function (e) {
        if (e.key === '/' && !e.ctrlKey && !e.metaKey && !e.altKey) {
            const tag = (e.target.tagName || '').toLowerCase();
            if (tag === 'input' || tag === 'textarea' || e.target.isContentEditable) return;
            e.preventDefault();
            (desktopInput || mobileInput)?.focus();
        }
    });

    // ==================== MOBILE TOGGLE ====================
    if (mobileBtn && mobileBar) {
        mobileBtn.addEventListener('click', function () {
            mobileBar.hidden = false;
            setTimeout(() => mobileInput?.focus(), 50);
        });
    }
    if (mobileClose && mobileBar) {
        mobileClose.addEventListener('click', function () {
            mobileBar.hidden = true;
            hidePanel('mobile');
        });
    }

    // ==================== THEME TOGGLE ANIMATION ====================
    const themeBtn = document.getElementById('themeToggle');
    if (themeBtn) {
        themeBtn.addEventListener('click', function () {
            const icon = this.querySelector('i');
            if (icon) {
                const isDark = document.documentElement.classList.contains('dark-mode');
                icon.className = isDark ? 'bi bi-sun' : 'bi bi-moon-stars';
            }
        });
    }
})();
</script>
<?php $__env->stopPush(); ?>
<?php endif; ?><?php /**PATH /home/saad/Videos/Crises-dashboard/resources/views/components/admin/navbar.blade.php ENDPATH**/ ?>