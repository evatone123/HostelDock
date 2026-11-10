<?php $__env->startSection('title', 'Booking Invoice'); ?>

<?php $__env->startSection('content'); ?>
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <div class="text-end mb-3 d-print-none">
                        <button class="btn btn-primary mb-0" onclick="window.print()"><i class="bi bi-printer me-2"></i>Print Invoice</button>
                        <a href="<?php echo e(route('booking.confirmation', $booking->booking_ref)); ?>" class="btn btn-light mb-0">Back</a>
                    </div>

                    <div class="card shadow rounded-3 p-4 p-md-5">
                        <div class="row justify-content-between align-items-center mb-4">
                            <div class="col-sm-6">
                                <img class="h-40px" src="<?php echo e(hd_asset('assets/images/logo.png')); ?>" alt="logo">
                            </div>
                            <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                                <h4 class="mb-0">Invoice</h4>
                                <p class="small text-muted mb-0">Ref: <?php echo e($booking->booking_ref); ?></p>
                                <p class="small text-muted mb-0">Issued: <?php echo e($booking->created_at->format('d M Y')); ?></p>
                            </div>
                        </div>

                        <hr>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <h6 class="mb-2">Billed To</h6>
                                <p class="mb-1 fw-semibold"><?php echo e($booking->student_name); ?></p>
                                <p class="mb-1 small"><?php echo e($booking->email); ?></p>
                                <p class="mb-1 small"><?php echo e($booking->phone); ?></p>
                                <p class="mb-0 small"><?php echo e($booking->institution); ?></p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="mb-2">Hostel</h6>
                                <p class="mb-1 fw-semibold"><?php echo e($booking->hostel->displayName()); ?></p>
                                <p class="mb-1 small"><i class="fa fa-location-dot text-primary me-1"></i><?php echo e($booking->hostel->location); ?></p>
                                <p class="mb-0 small"><i class="fa fa-walking text-primary me-1"></i><?php echo e($booking->hostel->distance); ?></p>
                            </div>
                        </div>

                        <table class="table table-bordered align-middle mb-4">
                            <thead class="table-light">
                                <tr>
                                    <th>Description</th>
                                    <th class="text-end" style="width: 160px;">Amount (GHS)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <?php echo e($booking->room_type); ?> - <?php echo e($booking->gender ?? 'General'); ?>

                                        <div class="small text-muted">Hostel accommodation for the academic year <?php echo e($booking->academic_year ?? 'N/A'); ?></div>
                                    </td>
                                    <td class="text-end fw-semibold"><?php echo e(number_format($booking->amount, 2)); ?></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="text-end">Total Due</th>
                                    <th class="text-end text-primary">GHS<?php echo e(number_format($booking->amount, 2)); ?></th>
                                </tr>
                            </tfoot>
                        </table>

                        <?php ($detail = $booking->hostel->paymentDetail); ?>
                        <?php ($payableOnline = ! $booking->isPaid() && $detail && $detail->hasPaystack()); ?>
                        <?php ($momoApiEnabled = ! $booking->isPaid() && $detail && $detail->hasMtnMoMoApi()); ?>
                        <?php if($booking->isPaid()): ?>
                            <div class="alert alert-success small border-0 mb-4">
                                <strong>Paid.</strong> GHS<?php echo e(number_format($booking->amount, 2)); ?> received
                                <?php if($booking->paid_at): ?>
                                    on <?php echo e($booking->paid_at->format('j M Y, g:i a')); ?>

                                <?php endif; ?>
                                <?php if($booking->paystack_transaction_ref): ?>
                                    &mdash; <?php echo e($booking->gatewayLabel()); ?> transaction <?php echo e($booking->paystack_transaction_ref); ?>.
                                <?php elseif($booking->provider_reference): ?>
                                    &mdash; <?php echo e($booking->gatewayLabel()); ?> transaction <?php echo e($booking->provider_reference); ?>.
                                <?php endif; ?>
                            </div>
                        <?php elseif($booking->isMomoPending()): ?>
                            <div class="alert alert-warning small border-0 mb-4">
                                <strong>MTN MoMo payment pending:</strong> a payment request was sent to
                                <strong><?php echo e($booking->momo_phone); ?></strong>. Approve it on your phone, then confirm the payment.
                                <?php if(auth()->guard()->check()): ?>
                                    <form method="POST" action="<?php echo e(route('booking.pay.momo.status', $booking->id)); ?>" class="d-print-none d-inline-block ms-1">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-primary mb-0">Confirm Payment</button>
                                    </form>
                                <?php else: ?>
                                    <a href="<?php echo e(route('login')); ?>" class="d-print-none d-inline-block alert-link ms-1">Log in to confirm</a>
                                <?php endif; ?>
                            </div>
                        <?php elseif($payableOnline || $momoApiEnabled): ?>
                            <div class="alert alert-primary small border-0 mb-4">
                                <strong>Pay online:</strong> choose a gateway below. Your booking is confirmed automatically once payment succeeds. Manual payment options below remain available.
                                <?php if(auth()->guard()->check()): ?>
                                    <?php if($payableOnline): ?>
                                        <form method="POST" action="<?php echo e(route('booking.pay', $booking->id)); ?>" class="d-print-none d-inline-block ms-1">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-sm btn-primary mb-0">Pay GHS<?php echo e(number_format($booking->amount, 2)); ?> with Paystack</button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if($momoApiEnabled): ?>
                                        <form method="POST" action="<?php echo e(route('booking.pay.momo', $booking->id)); ?>" class="d-print-none d-inline-block ms-1">
                                            <?php echo csrf_field(); ?>
                                            <input type="tel" name="phone" class="form-control form-control-sm d-inline-block" style="width: 180px;" placeholder="MoMo number (024...)" required maxlength="20" value="<?php echo e(old('phone', $booking->phone)); ?>">
                                            <button type="submit" class="btn btn-sm btn-primary mb-0">Pay with MTN MoMo</button>
                                        </form>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <a href="<?php echo e(route('login')); ?>" class="alert-link">Log in to pay online</a>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning small border-0 mb-4">
                                <strong>Payment instructions:</strong> Pay the total amount using any of the options listed below, then send your payment confirmation to info@hosteldock.com. Your booking is confirmed once the hostel confirms receipt.
                            </div>
                        <?php endif; ?>

                        <?php if($detail && $detail->is_active && ($detail->hasMomo() || $detail->bank_name || $detail->bank_account_number)): ?>
                            <div class="small mb-4">
                                <strong class="d-block mb-2">Payment options:</strong>
                                <?php if($detail->hasMomo()): ?>
                                    <div class="border rounded-2 p-2 mb-2">
                                        <strong>Mobile Money<?php echo e($detail->momo_provider ? ' (' . $detail->momo_provider . ')' : ''); ?></strong>
                                        <span class="text-muted"> &middot; Pay to: <?php echo e($detail->momo_number); ?></span>
                                        <?php if($detail->momo_account_name): ?>
                                            <p class="text-muted mb-0"><?php echo e($detail->momo_account_name); ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if($detail->bank_name || $detail->bank_account_number): ?>
                                    <div class="border rounded-2 p-2 mb-2">
                                        <strong>Bank Transfer<?php echo e($detail->bank_name ? ' (' . $detail->bank_name . ')' : ''); ?></strong>
                                        <?php if($detail->bank_account_number): ?>
                                            <span class="text-muted"> &middot; Pay to: <?php echo e($detail->bank_account_number); ?></span>
                                        <?php endif; ?>
                                        <?php if($detail->bank_account_name): ?>
                                            <p class="text-muted mb-0"><?php echo e($detail->bank_account_name); ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if($detail->instructions): ?>
                                    <p class="text-muted mb-0"><i class="bi bi-info-circle me-1"></i><?php echo e($detail->instructions); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <?php ($gateways = \App\Models\PaymentGateway::active()->get()); ?>
                            <?php if($gateways->isNotEmpty()): ?>
                                <div class="small mb-4">
                                    <strong class="d-block mb-2">Payment options:</strong>
                                    <?php $__currentLoopData = $gateways; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gateway): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="border rounded-2 p-2 mb-2">
                                            <strong><?php echo e($gateway->name); ?></strong>
                                            <?php if($gateway->account_number): ?>
                                                <span class="text-muted"> &middot; Pay to: <?php echo e($gateway->account_number); ?></span>
                                            <?php endif; ?>
                                            <?php if($gateway->instructions): ?>
                                                <p class="text-muted mb-0"><?php echo e($gateway->instructions); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between small text-muted">
                            <span>
                                Status: <span class="badge
                                    <?php if($booking->status === 'confirmed'): ?> bg-success
                                    <?php elseif($booking->status === 'cancelled'): ?> bg-danger
                                    <?php else: ?> bg-warning text-dark <?php endif; ?>"><?php echo e(ucfirst($booking->status)); ?></span>
                                &middot; Payment:
                                <span class="badge
                                    <?php if($booking->isPaid()): ?> bg-success
                                    <?php elseif($booking->payment_status === \App\Models\Booking::PAYMENT_PENDING): ?> bg-warning text-dark
                                    <?php elseif($booking->payment_status === \App\Models\Booking::PAYMENT_FAILED): ?> bg-danger
                                    <?php else: ?> bg-secondary <?php endif; ?>"><?php echo e($booking->paymentStatusLabel()); ?></span>
                            </span>
                            <span>Powered by HostelDock</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', ['body_class' => 'bg-mode'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\studentroombookings\resources\views/pages/booking-invoice.blade.php ENDPATH**/ ?>