<?php ($recaptchaEnabled = app(\App\Services\RecaptchaService::class)->isEnabled()); ?>
<?php if($recaptchaEnabled): ?>
    <div class="mb-3">
        <div class="g-recaptcha" data-sitekey="<?php echo e(config('services.recaptcha.site_key')); ?>"></div>
    </div>
    <?php $__env->startPush('scripts'); ?>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <?php $__env->stopPush(); ?>
<?php endif; ?><?php /**PATH C:\wamp64\www\studentroombookings\resources\views/partials/recaptcha.blade.php ENDPATH**/ ?>