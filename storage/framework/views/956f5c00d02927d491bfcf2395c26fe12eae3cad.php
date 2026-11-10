<?php $__env->startSection('title', 'Booking Confirmation'); ?>

<?php $__env->startSection('content'); ?>
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

                    <?php ($detail = $booking->hostel->paymentDetail); ?>
                    <?php ($paystackEnabled = ! $booking->isPaid() && $detail && $detail->hasPaystack()); ?>
                    <?php ($momoApiEnabled = ! $booking->isPaid() && $detail && $detail->hasMtnMoMoApi()); ?>
                    <?php ($hasOnlineGateway = $paystackEnabled || $momoApiEnabled); ?>

                    <div class="card shadow rounded-3 p-4 text-center mb-4">
                        <div class="icon-lg bg-success bg-opacity-10 rounded-circle mx-auto mt-2 mb-3 d-flex align-items-center justify-content-center" style="width:80px; height:80px;">
                            <i class="bi bi-check2-circle text-success fs-1"></i>
                        </div>
                        <h3 class="mb-2">Booking Received!</h3>
                        <p class="mb-2">Your reservation request has been received successfully.</p>
                        <h5 class="mb-0 text-primary">Reference: <?php echo e($booking->booking_ref); ?></h5>
                    </div>

                    <div class="card shadow rounded-3 p-4 mb-4">
                        <h5 class="mb-3"><i class="bi bi-receipt text-primary me-2"></i>Booking Details</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Hostel</span>
                                <span class="fw-semibold text-end"><?php echo e($booking->hostel->displayName()); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Room</span>
                                <span class="fw-semibold"><?php echo e($booking->room_type); ?> (<?php echo e($booking->room->name ?? 'General'); ?>)</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Student</span>
                                <span class="fw-semibold"><?php echo e($booking->student_name); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Academic Year</span>
                                <span class="fw-semibold"><?php echo e($booking->academic_year ?? '-'); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Amount Due</span>
                                <span class="fw-bold text-primary">GHS<?php echo e(number_format($booking->amount, 2)); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Status</span>
                                <span class="badge
                                    <?php if($booking->status === 'confirmed'): ?> bg-success
                                    <?php elseif($booking->status === 'cancelled'): ?> bg-danger
                                    <?php else: ?> bg-warning text-dark <?php endif; ?>">
                                    <?php echo e(ucfirst($booking->status)); ?>

                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Payment</span>
                                <span class="badge
                                    <?php if($booking->isPaid()): ?> bg-success
                                    <?php elseif($booking->payment_status === \App\Models\Booking::PAYMENT_PENDING): ?> bg-warning text-dark
                                    <?php elseif($booking->payment_status === \App\Models\Booking::PAYMENT_FAILED): ?> bg-danger
                                    <?php else: ?> bg-secondary <?php endif; ?>">
                                    <?php echo e($booking->paymentStatusLabel()); ?>

                                </span>
                            </li>
                            <?php if($booking->expires_at): ?>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span class="text-muted">Occupancy ends</span>
                                    <span class="fw-semibold"><?php echo e($booking->expires_at->format('j M Y')); ?></span>
                                </li>
                            <?php endif; ?>
                            <?php if($booking->renewal_count > 0): ?>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span class="text-muted">Renewals</span>
                                    <span class="fw-semibold text-success"><?php echo e($booking->renewal_count); ?> &times; renewed</span>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <?php if(isset($roommates)): ?>
                        <div class="card shadow rounded-3 p-4 mb-4">
                            <h5 class="mb-3"><i class="bi bi-people text-primary me-2"></i>Your Roommates</h5>
                            <?php if($roommates->isNotEmpty()): ?>
                                <p class="small text-muted mb-3">
                                    These students have confirmed reservations in the same room. Reach out to introduce yourself and coordinate your stay.
                                </p>
                                <ul class="list-group list-group-flush">
                                    <?php $__currentLoopData = $roommates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center gap-3 px-0">
                                            <span class="fw-semibold">
                                                <?php echo e($mate->student_name); ?>

                                                <?php if($mate->gender): ?>
                                                    <small class="text-muted">&middot; <?php echo e($mate->gender); ?></small>
                                                <?php endif; ?>
                                            </span>
                                            <span class="text-end small">
                                                <?php if($mate->phone): ?>
                                                    <a href="tel:<?php echo e($mate->phone); ?>" class="text-body text-decoration-none d-block"><?php echo e($mate->phone); ?></a>
                                                <?php endif; ?>
                                                <?php if($mate->email): ?>
                                                    <a href="mailto:<?php echo e($mate->email); ?>" class="text-muted small text-decoration-none d-block"><?php echo e($mate->email); ?></a>
                                                <?php endif; ?>
                                            </span>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                                <small class="text-muted d-block mt-3"><i class="bi bi-shield-lock me-1"></i>Please use this contact information responsibly and only to coordinate your stay.</small>
                            <?php else: ?>
                                <p class="text-muted mb-0">
                                    <i class="bi bi-people me-1"></i>You&rsquo;re currently the only one in this room. Other students who confirm this room will appear here.
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="alert alert-info mb-4">
                        <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i>Next steps</h6>
                        <ol class="small mb-0 ps-3">
                            <?php if($hasOnlineGateway): ?>
                                <li>Pay the amount due online below — your booking is confirmed instantly once payment succeeds.</li>
                            <?php else: ?>
                                <li>Pay the amount due using the payment options below.</li>
                                <li>Send your payment confirmation to the available contact details.</li>
                                <li>Your booking will be confirmed by the hostel shortly.</li>
                            <?php endif; ?>
                            <?php if(auth()->guard()->check()): ?>
                                <li>Track your bookings anytime from <a href="<?php echo e(route('my-bookings')); ?>">My Bookings</a>.</li>
                            <?php endif; ?>
                        </ol>
                    </div>

                    <?php if($booking->isPaid()): ?>
                        <div class="card shadow rounded-3 p-4 text-center mb-4">
                            <div class="icon-lg bg-success bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:64px; height:64px;">
                                <i class="bi bi-check2-circle text-success fs-3"></i>
                            </div>
                            <h5 class="mb-1">Payment Received</h5>
                            <p class="text-muted mb-2">GHS<?php echo e(number_format($booking->amount, 2)); ?> paid
                                <?php if($booking->paid_at): ?>
                                    on <?php echo e($booking->paid_at->format('j M Y, g:i a')); ?>

                                <?php endif; ?>
                            </p>
                            <?php if($booking->gateway): ?>
                                <p class="small text-muted mb-0">Paid via <?php echo e($booking->gatewayLabel()); ?>

                                    <?php if($booking->paystack_transaction_ref): ?>
                                        &middot; <?php echo e($booking->paystack_transaction_ref); ?>

                                    <?php elseif($booking->provider_reference): ?>
                                        &middot; <?php echo e($booking->provider_reference); ?>

                                    <?php endif; ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php elseif($booking->isMomoPending()): ?>
                        <div class="card shadow rounded-3 p-3 mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0"><i class="bi bi-phone text-primary me-2"></i>MTN MoMo Payment Pending</h6>
                                <span class="badge bg-warning text-dark">Pending</span>
                            </div>
                            <p class="small text-muted mb-2">
                                A payment request of <strong>GHS<?php echo e(number_format($booking->amount, 2)); ?></strong> was sent to
                                <strong><?php echo e($booking->momo_phone); ?></strong>. Approve it on your phone via the MTN MoMo app
                                or dial <strong>*170#</strong>, then confirm the payment below.
                            </p>
                            <?php if(auth()->guard()->check()): ?>
                                <form method="POST" action="<?php echo e(route('booking.pay.momo.status', $booking->id)); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-primary mb-0 w-100"><i class="bi bi-arrow-repeat me-2"></i>Confirm Payment</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php elseif($hasOnlineGateway): ?>
                        <div class="card shadow rounded-3 p-3 mb-4">
                            <h6 class="mb-3"><i class="bi bi-credit-card text-primary me-2"></i>Pay Online</h6>
                            <p class="small text-muted mb-2">
                                Choose how you would like to pay <strong>GHS<?php echo e(number_format($booking->amount, 2)); ?></strong>.
                                The booking is confirmed automatically once payment succeeds.
                            </p>
                            <?php if($paystackEnabled): ?>
                                <div class="border rounded-2 p-3 mb-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <strong class="small"><i class="bi bi-credit-card me-1 text-primary"></i>Paystack</strong>
                                        <span class="badge bg-light text-dark border">Card / Bank</span>
                                    </div>
                                    <p class="small text-muted mb-2">Pay securely through <?php echo e($booking->hostel->displayName()); ?>&rsquo;s Paystack account.</p>
                                    <?php if(auth()->guard()->check()): ?>
                                        <form method="POST" action="<?php echo e(route('booking.pay', $booking->id)); ?>">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-outline-primary mb-0 w-100"><i class="bi bi-lock me-2"></i>Pay GHS<?php echo e(number_format($booking->amount, 2)); ?> with Paystack</button>
                                        </form>
                                    <?php else: ?>
                                        <a href="<?php echo e(route('login')); ?>" class="btn btn-outline-primary mb-0 w-100">Log in to pay</a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <?php if($momoApiEnabled): ?>
                                <div class="border rounded-2 p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <strong class="small"><i class="bi bi-phone me-1 text-primary"></i>MTN Mobile Money</strong>
                                        <span class="badge bg-light text-dark border">MTN</span>
                                    </div>
                                    <p class="small text-muted mb-2">We send an approval prompt to your phone and you approve the payment securely via the MTN MoMo app or *170#.</p>
                                    <?php if(auth()->guard()->check()): ?>
                                        <form method="POST" action="<?php echo e(route('booking.pay.momo', $booking->id)); ?>">
                                            <?php echo csrf_field(); ?>
                                            <div class="mb-2">
                                                <input type="tel" name="phone" class="form-control" placeholder="Your MoMo number (e.g. 0240000000)" required maxlength="20" value="<?php echo e(old('phone', $booking->phone)); ?>" autocomplete="tel">
                                            </div>
                                            <button type="submit" class="btn btn-primary mb-0 w-100"><i class="bi bi-phone me-2"></i>Pay GHS<?php echo e(number_format($booking->amount, 2)); ?> with MTN MoMo</button>
                                        </form>
                                    <?php else: ?>
                                        <a href="<?php echo e(route('login')); ?>" class="btn btn-primary mb-0 w-100">Log in to pay</a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if($detail && $detail->is_active && ($detail->hasMomo() || $detail->bank_name || $detail->bank_account_number)): ?>
                        <div class="card shadow rounded-3 p-3 mb-4">
                            <h6 class="mb-3"><i class="bi bi-credit-card text-primary me-2"></i>Payment Options</h6>
                            <?php if($booking->isMomoPending() && $detail->hasMomo()): ?>
                                <div class="border rounded-2 p-3 mb-2">
                                    <strong class="small text-muted">MTN MoMo prompt sent — check your phone. If you prefer to transfer manually instead:</strong>
                                </div>
                            <?php endif; ?>
                            <?php if($detail->hasMomo()): ?>
                                <div class="border rounded-2 p-3 mb-2">
                                    <strong class="small"><i class="bi bi-phone me-1 text-primary"></i>Mobile Money</strong>
                                    <?php if($detail->momo_provider): ?>
                                        <span class="badge bg-light text-dark border ms-1"><?php echo e($detail->momo_provider); ?></span>
                                    <?php endif; ?>
                                    <p class="small mb-1 fw-semibold">Pay to: <?php echo e($detail->momo_number); ?></p>
                                    <?php if($detail->momo_account_name): ?>
                                        <p class="small text-muted mb-0"><?php echo e($detail->momo_account_name); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <?php if($detail->bank_name || $detail->bank_account_number): ?>
                                <div class="border rounded-2 p-3">
                                    <strong class="small"><i class="bi bi-bank me-1 text-success"></i>Bank Transfer</strong>
                                    <?php if($detail->bank_name): ?>
                                        <span class="badge bg-light text-dark border ms-1"><?php echo e($detail->bank_name); ?></span>
                                    <?php endif; ?>
                                    <?php if($detail->bank_account_number): ?>
                                        <p class="small mb-1 fw-semibold">Pay to: <?php echo e($detail->bank_account_number); ?></p>
                                    <?php endif; ?>
                                    <?php if($detail->bank_account_name): ?>
                                        <p class="small text-muted mb-1"><?php echo e($detail->bank_account_name); ?><?php if($detail->bank_branch): ?> (<?php echo e($detail->bank_branch); ?>)<?php endif; ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <?php if($detail->instructions): ?>
                                <p class="small text-muted mt-2 mb-0"><i class="bi bi-info-circle me-1"></i><?php echo e($detail->instructions); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <?php ($gateways = \App\Models\PaymentGateway::active()->get()); ?>
                        <?php if($gateways->isNotEmpty()): ?>
                            <div class="card shadow rounded-3 p-3 mb-4">
                                <h6 class="mb-3"><i class="bi bi-credit-card text-primary me-2"></i>Payment Options</h6>
                                <div class="row g-2">
                                    <?php $__currentLoopData = $gateways; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gateway): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="col-sm-6">
                                            <div class="border rounded-2 p-3 h-100">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <strong class="small">
                                                        <?php if($gateway->type === 'mobile_money'): ?>
                                                            <i class="bi bi-phone me-1 text-primary"></i>
                                                        <?php elseif($gateway->type === 'bank'): ?>
                                                            <i class="bi bi-bank me-1 text-success"></i>
                                                        <?php elseif($gateway->type === 'card'): ?>
                                                            <i class="bi bi-credit-card me-1 text-warning"></i>
                                                        <?php else: ?>
                                                            <i class="bi bi-cash me-1 text-secondary"></i>
                                                        <?php endif; ?>
                                                        <?php echo e($gateway->name); ?>

                                                    </strong>
                                                    <?php if($gateway->network): ?>
                                                        <span class="badge bg-light text-dark border"><?php echo e($gateway->network); ?></span>
                                                    <?php endif; ?>
                                                </div>
                                                <?php if($gateway->account_number): ?>
                                                    <p class="small mb-1 fw-semibold">Pay to: <?php echo e($gateway->account_number); ?></p>
                                                <?php endif; ?>
                                                <?php if($gateway->account_name): ?>
                                                    <p class="small text-muted mb-1"><?php echo e($gateway->account_name); ?></p>
                                                <?php endif; ?>
                                                <?php if($gateway->instructions): ?>
                                                    <p class="small text-muted mb-0"><?php echo e($gateway->instructions); ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <a href="<?php echo e(route('booking.invoice', $booking->booking_ref)); ?>" target="_blank" class="btn btn-primary mb-0"><i class="bi bi-printer me-2"></i>View Invoice</a>
                        <a href="<?php echo e(route('home')); ?>" class="btn btn-light mb-0">Back to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\studentroombookings\resources\views/pages/booking-confirmation.blade.php ENDPATH**/ ?>