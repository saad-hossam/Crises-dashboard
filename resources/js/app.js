
import 'bootstrap';

document.addEventListener('DOMContentLoaded', () => {

    // ==========================================
    // 1. Password Visibility Toggle
    // ==========================================

    const toggleBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');

    if (toggleBtn && passwordInput && toggleIcon) {

        toggleBtn.addEventListener('click', () => {

            const isPassword = passwordInput.type === 'password';

            passwordInput.type = isPassword ? 'text' : 'password';

            toggleIcon.classList.toggle('bi-eye', !isPassword);
            toggleIcon.classList.toggle('bi-eye-slash', isPassword);

            toggleBtn.setAttribute(
                'aria-label',
                isPassword ? 'Hide password' : 'Show password'
            );

            passwordInput.focus();
        });
    }


    // ==========================================
    // 2. Login Form Loading State
    // ==========================================

    const loginForm = document.getElementById('loginForm');
    const loginBtn = document.getElementById('loginBtn');
    const loginSpinner = document.getElementById('loginSpinner');
    const loginBtnText = document.getElementById('loginBtnText');

    if (loginForm && loginBtn) {

        loginForm.addEventListener('submit', (event) => {

            // Prevent multiple submissions
            if (loginBtn.disabled) {
                event.preventDefault();
                return;
            }

            // Allow browser validation to work
            if (!loginForm.checkValidity()) {
                return;
            }

            loginBtn.disabled = true;

            if (loginSpinner) {
                loginSpinner.classList.remove('d-none');
            }

            if (loginBtnText) {
                loginBtnText.textContent =
                    loginBtnText.dataset.loading || 'Signing in...';
            }
        });
    }

});
