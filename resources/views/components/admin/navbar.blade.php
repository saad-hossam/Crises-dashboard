<header class="admin-navbar ao-navbar">
    <div class="ao-navbar-inner">
        <div class="navbar-start">
            <button type="button" class="btn ao-icon-btn sidebar-toggle" id="sidebarToggle" aria-label="Menu">
                <i class="bi bi-list"></i>
            </button>
            <div class="page-heading">
                <p class="ao-page-kicker d-none d-md-block mb-0">AOI Crises</p>
                <h1 class="page-title mb-0">@yield('page_title', __('dashboard.title'))</h1>
            </div>
        </div>

        <div class="navbar-center d-none d-md-flex">
            <div class="ao-search" id="aoGlobalSearch">
                <i class="bi bi-search"></i>
                <input type="search"
                       id="aoSearchInput"
                       class="form-control"
                       placeholder="{{ __('common.search') }}… (موديولات، سجلات، مستخدمون)"
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
                </div>
            </div>
        </div>

        <div class="navbar-end">
            {{-- Mobile search toggle --}}
            <button type="button" class="btn ao-icon-btn d-md-none" id="aoSearchMobileBtn" title="بحث">
                <i class="bi bi-search"></i>
            </button>

            <a href="{{ route('admin.excel-modules.create') }}" class="btn ao-cta d-none d-md-inline-flex">
                <i class="bi bi-cloud-upload"></i>
                <span>{{ __('excel.upload') ?? 'Upload' }}</span>
            </a>

            <div class="dropdown">
                <button type="button" class="btn ao-chip-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-translate"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'العربية' : 'EN' }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end ao-menu">
                    <li>
                        <a class="dropdown-item {{ app()->getLocale()==='ar'?'active':'' }}" href="{{ route('language.switch','ar') }}">
                            العربية @if(app()->getLocale()==='ar')<i class="bi bi-check2 ms-auto"></i>@endif
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item {{ app()->getLocale()==='en'?'active':'' }}" href="{{ route('language.switch','en') }}">
                            English @if(app()->getLocale()==='en')<i class="bi bi-check2 ms-auto"></i>@endif
                        </a>
                    </li>
                </ul>
            </div>

            <button type="button" class="btn ao-icon-btn theme-toggle" id="themeToggle" title="Theme">
                <i class="bi bi-moon-stars" id="themeIcon"></i>
            </button>

            <a href="{{ route('admin.settings.index') }}" class="btn ao-icon-btn" title="Settings">
                <i class="bi bi-gear"></i>
            </a>

            @auth
            <div class="dropdown">
                <button type="button" class="btn ao-user dropdown-toggle" data-bs-toggle="dropdown">
                    <span class="ao-avatar">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                    <span class="d-none d-md-inline ao-user-name">{{ auth()->user()->name }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end ao-menu">
                    <li class="ao-menu-head">
                        <strong>{{ auth()->user()->name }}</strong>
                        <small>{{ auth()->user()->email }}</small>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('admin.settings.index') }}"><i class="bi bi-person-gear"></i> {{ __('navigation.settings') }}</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> {{ __('navigation.dashboard') }}</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right"></i> Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
            @endauth
        </div>
    </div>

    {{-- Mobile search bar --}}
    <div class="ao-search-mobile d-md-none" id="aoSearchMobileBar" hidden>
        <div class="ao-search ao-search-mobile-inner">
            <i class="bi bi-search"></i>
            <input type="search" id="aoSearchInputMobile" class="form-control"
                   placeholder="{{ __('common.search') }}…" autocomplete="off">
            <button type="button" class="btn btn-sm" id="aoSearchMobileClose">&times;</button>
        </div>
    </div>
</header>

@once
@push('scripts')
<script>
(function () {
    const URL = @json(route('admin.search'));
    const inputDesk = document.getElementById('aoSearchInput');
    const inputMob = document.getElementById('aoSearchInputMobile');
    const panel = document.getElementById('aoSearchResults');
    const body = document.getElementById('aoSearchBody');
    const hint = document.getElementById('aoSearchHint');
    const wrap = document.getElementById('aoGlobalSearch');
    if (!panel || !body) return;

    let timer = null;
    let aborter = null;
    let active = -1;
    let flatLinks = [];

    function showPanel() {
        panel.hidden = false;
        if (inputDesk) inputDesk.setAttribute('aria-expanded', 'true');
    }
    function hidePanel() {
        panel.hidden = true;
        if (inputDesk) inputDesk.setAttribute('aria-expanded', 'false');
        active = -1;
    }

    function render(data) {
        flatLinks = [];
        const groups = data.groups || [];
        if (!data.query) {
            body.innerHTML = '';
            hint.hidden = false;
            hint.textContent = 'اكتب للبحث في كل الموقع…';
            return;
        }
        if (!groups.length || data.total === 0) {
            body.innerHTML = '';
            hint.hidden = false;
            hint.textContent = 'لا توجد نتائج لـ “' + data.query + '”';
            return;
        }
        hint.hidden = true;
        let html = '';
        groups.forEach(g => {
            html += '<div class="ao-search-group">';
            html += '<div class="ao-search-group-label">' + escapeHtml(g.label) + '</div>';
            (g.results || []).forEach(r => {
                const idx = flatLinks.length;
                flatLinks.push(r.url);
                html += '<a class="ao-search-item" data-idx="' + idx + '" href="' + escapeHtml(r.url) + '">'
                    + '<span class="ao-search-icon"><i class="bi ' + escapeHtml(r.icon || 'bi-search') + '"></i></span>'
                    + '<span class="ao-search-text">'
                    +   '<span class="ao-search-title">' + highlight(r.title, data.query) + '</span>'
                    +   '<span class="ao-search-sub">' + escapeHtml(r.subtitle || '') + '</span>'
                    + '</span></a>';
            });
            html += '</div>';
        });
        body.innerHTML = html;
    }

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

    function search(q, fromInput) {
        q = (q || '').trim();
        showPanel();
        if (!q) {
            render({ query: '', groups: [], total: 0 });
            return;
        }
        hint.hidden = false;
        hint.textContent = 'جاري البحث…';
        body.innerHTML = '';

        if (aborter) aborter.abort();
        aborter = new AbortController();

        fetch(URL + '?q=' + encodeURIComponent(q), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            signal: aborter.signal,
            credentials: 'same-origin'
        })
        .then(r => r.json())
        .then(data => render(data))
        .catch(err => {
            if (err.name === 'AbortError') return;
            hint.hidden = false;
            hint.textContent = 'تعذر إكمال البحث';
        });
    }

    function onType(e) {
        const q = e.target.value;
        // sync both inputs
        if (inputDesk && e.target !== inputDesk) inputDesk.value = q;
        if (inputMob && e.target !== inputMob) inputMob.value = q;
        clearTimeout(timer);
        timer = setTimeout(() => search(q), 180);
    }

    function onKey(e) {
        const items = panel.querySelectorAll('.ao-search-item');
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
            if (active >= 0 && items[active]) {
                e.preventDefault();
                window.location = items[active].href;
            }
        } else if (e.key === 'Escape') {
            hidePanel();
            e.target.blur();
        }
    }

    if (inputDesk) {
        inputDesk.addEventListener('input', onType);
        inputDesk.addEventListener('focus', () => search(inputDesk.value));
        inputDesk.addEventListener('keydown', onKey);
    }
    if (inputMob) {
        inputMob.addEventListener('input', onType);
        inputMob.addEventListener('focus', () => search(inputMob.value));
        inputMob.addEventListener('keydown', onKey);
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('#aoGlobalSearch') && !e.target.closest('#aoSearchMobileBar')) {
            hidePanel();
        }
    });

    // shortcut /
    document.addEventListener('keydown', (e) => {
        if (e.key === '/' && !e.ctrlKey && !e.metaKey && !e.altKey) {
            const tag = (e.target.tagName || '').toLowerCase();
            if (tag === 'input' || tag === 'textarea' || e.target.isContentEditable) return;
            e.preventDefault();
            (inputDesk || inputMob)?.focus();
        }
    });

    // mobile open/close
    const mobBtn = document.getElementById('aoSearchMobileBtn');
    const mobBar = document.getElementById('aoSearchMobileBar');
    const mobClose = document.getElementById('aoSearchMobileClose');
    if (mobBtn && mobBar) {
        mobBtn.addEventListener('click', () => {
            mobBar.hidden = false;
            inputMob?.focus();
            // move panel under mobile if needed - panel is in desktop wrap; clone behavior by showing panel fixed
            if (wrap) {
                // attach panel visually - keep using same panel
                showPanel();
            }
        });
    }
    if (mobClose && mobBar) {
        mobClose.addEventListener('click', () => {
            mobBar.hidden = true;
            hidePanel();
        });
    }
})();
</script>
@endpush
@endonce