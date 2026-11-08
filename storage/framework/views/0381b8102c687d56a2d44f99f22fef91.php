<footer class="admin-footer">
    <div class="footer-left">
        <span class="footer-brand"><?php echo e(__('common.app_name')); ?></span>
        <span class="footer-sep">·</span>
        <span class="footer-copy">&copy; <?php echo e(date('Y')); ?> <?php echo e(__('common.copyright')); ?></span>
    </div>

    <div class="footer-right">
        <span class="footer-clock" id="footerClock"><?php echo e(now()->format('H:i')); ?></span>
        <span class="footer-sep">·</span>
        <span class="footer-version">
            <span class="footer-pulse" aria-hidden="true"></span>
        </span>
    </div>
</footer>

<?php if (! $__env->hasRenderedOnce('1efbbbcd-64fb-4811-b8fb-498501c64e76')): $__env->markAsRenderedOnce('1efbbbcd-64fb-4811-b8fb-498501c64e76'); ?>
<?php $__env->startPush('scripts'); ?>
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
<?php $__env->stopPush(); ?>
<?php endif; ?><?php /**PATH C:\Users\pc\Music\Crises-dashboard\resources\views/components/admin/footer.blade.php ENDPATH**/ ?>