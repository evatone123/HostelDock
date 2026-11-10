<?php $__env->startSection('title', 'Buy Booking Access Token'); ?>

<?php $__env->startSection('content'); ?>
    <section class="bg-primary py-3">
        <div class="container">
            <nav class="d-flex mb-1" aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-dots mb-0">
                    <li class="breadcrumb-item"><a class="text-white" href="<?php echo e(route('home')); ?>">Home</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Buy Token</li>
                </ol>
            </nav>
            <h1 class="text-white mb-0">Buy Booking Access Token</h1>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-xl-6">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo e(session('success')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if(session('info')): ?>
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <?php echo e(session('info')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if($active): ?>
                        <div class="card shadow rounded-3 p-4 text-center mb-4">
                            <div class="icon-lg bg-success bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:88px; height:88px;">
                                <i class="bi bi-patch-check text-success" style="font-size:2.4rem;"></i>
                            </div>
                            <h4 class="mb-2">You have an active booking token</h4>
                            <p class="text-muted mb-3">Token: <strong><?php echo e($active->token_ref); ?></strong></p>

                            <?php if($intended): ?>
                                <a href="<?php echo e($intended); ?>" class="btn btn-primary mb-0">Continue to Booking<i class="bi bi-arrow-right ms-2"></i></a>
                            <?php else: ?>
                                <a href="<?php echo e(route('home')); ?>" class="btn btn-primary mb-0">Browse Hostels</a>
                            <?php endif; ?>

                            <a href="<?php echo e(route('home')); ?>" class="btn btn-light mb-0">Back to Home</a>
                        </div>
                    <?php else: ?>
                        <div class="card shadow rounded-3 p-4 text-center mb-4">
                            <div class="icon-lg bg-primary bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:88px; height:88px;">
                                <i class="bi bi-patch-question text-primary" style="font-size:2.4rem;"></i>
                            </div>
                            <h4 class="mb-2">Unlock Booking Access</h4>
                            <p class="text-muted mb-4">
                                Pay a one-time booking access fee of <strong class="text-primary">GHS<?php echo e(number_format(\App\Models\BookingToken::PRICE_GHS, 2)); ?></strong>.
                                Once paid, you can book a room in any hostel.
                            </p>

                            <form method="POST" action="<?php echo e(route('token.pay')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-primary btn-lg w-100 mb-2">
                                    <i class="bi bi-credit-card me-2"></i>Pay GHS<?php echo e(number_format(\App\Models\BookingToken::PRICE_GHS, 2)); ?> via Paystack
                                </button>
                            </form>
                            <p class="small text-muted mb-0">
                                <i class="bi bi-shield-lock me-1"></i>You'll be redirected to Paystack to complete payment securely. Your token becomes active immediately after payment.
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\studentroombookings\resources\views/pages/token-buy.blade.php ENDPATH**/ ?>