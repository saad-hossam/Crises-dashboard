<footer class="admin-footer">
    <div class="footer-left">
        <span class="footer-brand">{{ __('common.app_name') }}</span>
        <span class="footer-sep">·</span>
        <span class="footer-copy">&copy; {{ date('Y') }} {{ __('common.copyright') }}</span>
    </div>

    <div class="footer-right">
        <span class="footer-clock" id="footerClock">{{ now()->format('H:i') }}</span>
        <span class="footer-sep">·</span>
        <span class="footer-version">
            <span class="footer-pulse" aria-hidden="true"></span>
        </span>
    </div>
</footer>

@once
@push('scripts')
<script>
(function () {
    const clock = document.getElementById('footerClock');
    if (!clock) return;
    const tick = () => {
        const now = new Date();
        const hh = String(now.getHours()).padStart(2, '0');
        const mm = String(now.getMinutes()).padStart(2, '0');
        clock.textContent = `${hh}:${mm}`;
    };
    tick();
    setInterval(tick, 30000);
})();
</script>
@endpush
@endonce