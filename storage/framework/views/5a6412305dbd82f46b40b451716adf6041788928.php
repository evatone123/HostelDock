<?php $__env->startSection('title', 'Reset Password'); ?>

<?php $__env->startSection('content'); ?>
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card shadow rounded-3 p-4 auth-card">
                        <div class="text-center mb-4">
                            <a href="<?php echo e(route('home')); ?>" class="d-inline-block mb-3">
                                <img class="h-40px" src="<?php echo e(hd_asset('assets/images/logo.png')); ?>" alt="logo">
                            </a>
                            <h4 class="mb-1">Reset Password</h4>
                            <p class="text-muted mb-0">Choose a new password for your account</p>
                        </div>

                        <?php if($errors->any()): ?>
                            <div class="alert alert-danger py-2 mb-4" role="alert">
                                <?php echo e($errors->first()); ?>

                            </div>
                        <?php endif; ?>

                        <form method="POST" action="<?php echo e(route('password.update')); ?>">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="token" value="<?php echo e($token); ?>">
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" name="email" value="<?php echo e($email ?? old('email')); ?>" required autofocus autocomplete="email">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" class="form-control" name="password" required autocomplete="new-password" minlength="6">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" minlength="6">
                            </div>
                            <button type="submit" class="btn btn-primary mb-0 w-100"><i class="bi bi-key me-2"></i>Update Password</button>
                        </form>

                        <p class="small text-center mt-4 mb-0">
                            <a href="<?php echo e(route('login')); ?>" class="text-primary"><i class="bi bi-box-arrow-in-right me-1"></i>Back to Sign In</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\studentroombookings\resources\views/auth/passwords/reset.blade.php ENDPATH**/ ?>