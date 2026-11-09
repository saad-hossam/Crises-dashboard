
document.addEventListener('DOMContentLoaded', function () {

    // Password visibility toggle
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');
    const toggleIcon = document.getElementById('toggleIcon');

    if (passwordInput && togglePassword && toggleIcon) {
        togglePassword.addEventListener('click', function () {

            const isPassword = passwordInput.type === 'password';

            passwordInput.type = isPassword ? 'text' : 'password';

            toggleIcon.classList.toggle('bi-eye', !isPassword);
            toggleIcon.classList.toggle('bi-eye-slash', isPassword);

            togglePassword.setAttribute(
                'aria-pressed',
                isPassword ? 'true' : 'false'
            );
        });
    }

    // Login loading state
    const loginForm = document.getElementById('loginForm');
    const loginBtn = document.getElementById('loginBtn');
    const loginBtnText = document.getElementById('loginBtnText');
    const loginSpinner = document.getElementById('loginSpinner');

    if (loginForm && loginBtn) {
        loginForm.addEventListener('submit', function (event) {

            // Allow browser validation to run first.
            if (!loginForm.checkValidity()) {
                return;
            }

            if (loginForm.dataset.submitting === 'true') {
                event.preventDefault();
                return;
            }

            loginForm.dataset.submitting = 'true';

            loginBtn.disabled = true;

            if (loginBtnText) {
                loginBtnText.classList.add('d-none');
            }

            if (loginSpinner) {
                loginSpinner.classList.remove('d-none');
            }
        });
    }

});
