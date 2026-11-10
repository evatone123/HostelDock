<?php $__env->startSection('title', 'Forgot Password'); ?>

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
                            <h4 class="mb-1">Forgot Password?</h4>
                            <p class="text-muted mb-0">Enter your email and we&rsquo;ll send you a reset link</p>
                        </div>

                        <?php if(session('status')): ?>
                            <div class="alert alert-success py-2 mb-4" role="alert">
                                <?php echo e(session('status')); ?>

                            </div>
                        <?php endif; ?>

                        <?php if($errors->any()): ?>
                            <div class="alert alert-danger py-2 mb-4" role="alert">
                                <?php echo e($errors->first()); ?>

                            </div>
                        <?php endif; ?>

                        <form method="POST" action="<?php echo e(route('password.email')); ?>">
                            <?php echo csrf_field(); ?>
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" name="email" value="<?php echo e(old('email')); ?>" required autofocus autocomplete="email">
                            </div>
                            <?php echo $__env->make('partials.recaptcha', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            <button type="submit" class="btn btn-primary mb-0 w-100"><i class="bi bi-envelope me-2"></i>Send Password Reset Link</button>
                        </form>

                        <p class="small text-center mt-4 mb-0">
                            Remembered your password?
                            <a href="<?php echo e(route('login')); ?>" class="text-primary">Back to Sign In</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\studentroombookings\resources\views/auth/passwords/email.blade.php ENDPATH**/ ?>