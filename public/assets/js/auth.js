document.addEventListener('DOMContentLoaded', () => {
    const form      = document.getElementById('loginForm');
    const email     = document.getElementById('email');
    const password  = document.getElementById('password');
    const toggleBtn = document.getElementById('togglePassword');
    const toggleIcn = document.getElementById('toggleIcon');
    const loginBtn  = document.getElementById('loginBtn');
    const btnText   = document.getElementById('loginBtnText');
    const spinner   = document.getElementById('loginSpinner');
    const capsHint  = document.getElementById('capsHint');
    const page      = document.getElementById('authPage');

    /* ---------- Password toggle ---------- */
    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            const isPwd = password.type === 'password';
            password.type = isPwd ? 'text' : 'password';
            toggleIcn.className = isPwd ? 'bi bi-eye-slash' : 'bi bi-eye';
            toggleBtn.setAttribute('aria-pressed', String(isPwd));
            password.focus();
        });
    }

    /* ---------- Caps lock detector ---------- */
    function checkCaps(e) {
        if (!capsHint) return;
        const on = e.getModifierState && e.getModifierState('CapsLock');
        capsHint.classList.toggle('d-none', !on);
    }
    password.addEventListener('keydown', checkCaps);
    password.addEventListener('keyup', checkCaps);

    /* ---------- Real-time validation ---------- */
    function setStatus(input, ok) {
        const status = document.querySelector(`[data-status-for="${input.id}"]`);
        if (!status) return;
        status.classList.remove('is-valid', 'is-invalid');
        if (ok === null) return;               // neutral
        status.classList.add(ok ? 'is-valid' : 'is-invalid');
        status.innerHTML = ok
            ? '<i class="bi bi-check-circle-fill"></i>'
            : '<i class="bi bi-x-circle-fill"></i>';
    }

    const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    email.addEventListener('input', () => {
        if (!email.value) return setStatus(email, null);
        setStatus(email, emailRe.test(email.value));
    });
    email.addEventListener('blur', () => {
        if (!email.value) return;
        setStatus(email, emailRe.test(email.value));
    });

    password.addEventListener('input', () => {
        if (!password.value) return setStatus(password, null);
        setStatus(password, password.value.length >= 6);
    });

    /* ---------- Submit loading state ---------- */
    form.addEventListener('submit', (e) => {
        if (!form.checkValidity()) { e.preventDefault(); return; }
        loginBtn.disabled = true;
        btnText.classList.add('d-none');
        spinner.classList.remove('d-none');
    });

    /* ---------- Mouse-reactive parallax ---------- */
    const orbs = document.querySelectorAll('[data-parallax]');
    let raf = null;
    if (window.matchMedia('(pointer: fine)').matches && page) {
        page.addEventListener('mousemove', (e) => {
            if (raf) return;
            raf = requestAnimationFrame(() => {
                const cx = window.innerWidth / 2;
                const cy = window.innerHeight / 2;
                const dx = e.clientX - cx;
                const dy = e.clientY - cy;
                orbs.forEach(orb => {
                    const depth = parseFloat(orb.dataset.parallax) || 0.03;
                    orb.style.transform = `translate3d(${dx * depth}px, ${dy * depth}px, 0)`;
                });
                raf = null;
            });
        });
        page.addEventListener('mouseleave', () => {
            orbs.forEach(o => o.style.transform = '');
        });
    }

    /* ---------- Focus first invalid field ---------- */
    const firstInvalid = form.querySelector('.is-invalid');
    if (firstInvalid) firstInvalid.focus();
});