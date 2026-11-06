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

<?php if (! $__env->hasRenderedOnce('0c18371b-e97c-4915-a34e-ae5f81c68cc3')): $__env->markAsRenderedOnce('0c18371b-e97c-4915-a34e-ae5f81c68cc3'); ?>
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
<?php endif; ?><?php /**PATH /home/saad/Videos/Crises-dashboard/resources/views/components/admin/footer.blade.php ENDPATH**/ ?>