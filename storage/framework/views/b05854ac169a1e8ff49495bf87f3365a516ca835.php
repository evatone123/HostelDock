<?php $__env->startSection('title', 'My Bookings'); ?>

<?php $__env->startSection('content'); ?>
    <section class="bg-primary py-3">
        <div class="container">
            <h1 class="text-white mb-0">My Bookings</h1>
        </div>
    </section>

    <section class="pt-4 pt-lg-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card shadow rounded-3 p-4 text-center">
                        <div class="avatar avatar-xl mx-auto mb-3">
                            <img class="avatar-img rounded-circle" src="<?php echo e(asset('assets/images/avatar/13.png')); ?>" alt="avatar">
                        </div>
                        <h5 class="mb-0"><?php echo e(auth()->user()->name); ?></h5>
                        <p class="text-muted mb-0"><?php echo e(auth()->user()->email); ?></p>
                        <hr class="my-4">
                        <div class="d-grid gap-2">
                            <a href="<?php echo e(route('profile')); ?>" class="btn btn-light mb-0">My Profile</a>
                            <a href="<?php echo e(route('my-bookings')); ?>" class="btn btn-primary-soft mb-0"><i class="bi bi-bookmark-check me-2"></i>My Bookings</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo e(session('success')); ?>

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

                    <?php $__empty_1 = true; $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="card shadow rounded-3 p-3 mb-3">
                            <div class="row g-3 align-items-center">
                                <?php if($booking->hostel): ?>
                                    <div class="col-sm-3 col-md-2">
                                        <img src="<?php echo e(asset($booking->hostel->coverImagePath())); ?>"
                                            class="rounded-2 w-100 object-fit-cover" style="height: 80px;" alt="<?php echo e($booking->hostel->name); ?>">
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <h6 class="mb-1">
                                            <a href="<?php echo e(route('hostel.detail', $booking->hostel->slug)); ?>" class="text-body text-primary-hover"><?php echo e($booking->hostel->displayName()); ?></a>
                                        </h6>
                                        <p class="small text-muted mb-1"><?php echo e($booking->room_type); ?> &middot; <?php echo e($booking->gender ?? 'General'); ?> &middot; <?php echo e($booking->academic_year ?? 'N/A'); ?></p>
                                        <p class="small mb-0"><strong>Ref:</strong> <?php echo e($booking->booking_ref); ?></p>
                                    </div>
                                <?php else: ?>
                                    <div class="col-sm-8 col-md-8">
                                        <h6 class="mb-1">Hostel no longer available</h6>
                                        <p class="small text-muted mb-1"><?php echo e($booking->room_type); ?> &middot; <?php echo e($booking->gender ?? 'General'); ?> &middot; <?php echo e($booking->academic_year ?? 'N/A'); ?></p>
                                        <p class="small mb-0"><strong>Ref:</strong> <?php echo e($booking->booking_ref); ?></p>
                                    </div>
                                <?php endif; ?>
                                <div class="col-sm-3 col-md-4 text-md-end">
                                    <div class="mb-2">
                                        <?php if($booking->status === 'pending'): ?>
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        <?php elseif($booking->status === 'confirmed'): ?>
                                            <span class="badge bg-success">Confirmed</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Cancelled</span>
                                        <?php endif; ?>
                                        <?php if($booking->isPaid()): ?>
                                            <span class="badge bg-success"><i class="bi bi-check2-circle me-1"></i>Paid</span>
                                        <?php elseif($booking->payment_status === \App\Models\Booking::PAYMENT_PENDING): ?>
                                            <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Payment Pending</span>
                                        <?php elseif($booking->payment_status === \App\Models\Booking::PAYMENT_FAILED): ?>
                                            <span class="badge bg-danger">Payment Failed</span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="fw-bold text-primary mb-2">GHS<?php echo e(number_format($booking->amount, 2)); ?></p>
                                    <div class="d-flex justify-content-md-end gap-1 flex-wrap">
                                        <?php if($booking->isMomoPending()): ?>
                                            <form method="POST" action="<?php echo e(route('booking.pay.momo.status', $booking->id)); ?>">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="btn btn-sm btn-primary mb-0"><i class="bi bi-arrow-repeat me-1"></i>Confirm Payment</button>
                                            </form>
                                        <?php elseif(! $booking->isPaid() && $booking->status !== 'cancelled' && $booking->hostel && $booking->hostel->paymentDetail && ($booking->hostel->paymentDetail->hasPaystack() || $booking->hostel->paymentDetail->hasMtnMoMoApi())): ?>
                                            <a href="<?php echo e(route('booking.confirmation', $booking->booking_ref)); ?>" class="btn btn-sm btn-primary mb-0"><i class="bi bi-credit-card me-1"></i>Pay Now</a>
                                        <?php endif; ?>
                                        <a href="<?php echo e(route('booking.invoice', $booking->booking_ref)); ?>" target="_blank" class="btn btn-sm btn-light mb-0"><i class="bi bi-file-earmark-text me-1"></i>Invoice</a>
                                        <?php if($booking->status === 'pending'): ?>
                                            <form method="POST" action="<?php echo e(route('booking.cancel', $booking->id)); ?>" onsubmit="return confirm('Cancel this booking?')">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="btn btn-sm btn-danger-soft mb-0">Cancel</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <?php if($booking->status === 'confirmed' && $booking->expires_at): ?>
                                <?php ($renewing = $booking->isWithinRenewalWindow()); ?>
                                <div class="mt-3 pt-3 border-top">
                                    <?php if($renewing && ! $booking->isRenewalLocked()): ?>
                                        <div class="alert alert-warning rounded-2 py-2 px-3 mb-2 small">
                                            <i class="bi bi-clock-history me-1"></i>Your occupancy ends <strong><?php echo e($booking->expires_at->format('j M Y')); ?></strong>.
                                            The <?php echo e(\App\Models\Booking::RENEWAL_WINDOW_DAYS); ?>-day renewal window is open &mdash; lock your room to keep it for another
                                            <?php echo e(\App\Models\Booking::OCCUPANCY_DAYS); ?> days.
                                        </div>
                                        <form method="POST" action="<?php echo e(route('booking.renewal.lock', $booking->id)); ?>">
                                            <?php echo csrf_field(); ?>
                                            <button class="btn btn-sm btn-primary mb-0"><i class="bi bi-lock me-1"></i>Lock &amp; Rebook (GHS<?php echo e(number_format($booking->renewalPrice(), 2)); ?>)</button>
                                        </form>
                                    <?php elseif($renewing && $booking->isRenewalLocked()): ?>
                                        <?php if($booking->isRenewalMomoPending()): ?>
                                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                                <span class="small">
                                                    <i class="bi bi-hourglass-split me-1"></i>MoMo renewal request sent to <strong><?php echo e($booking->renewal_phone); ?></strong>. Approve it on your phone, then confirm.
                                                </span>
                                                <form method="POST" action="<?php echo e(route('booking.renewal.pay.momo.status', $booking->id)); ?>">
                                                    <?php echo csrf_field(); ?>
                                                    <button class="btn btn-sm btn-primary mb-0"><i class="bi bi-arrow-repeat me-1"></i>Confirm Payment</button>
                                                </form>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-success rounded-2 py-2 px-3 mb-2 small">
                                                <i class="bi bi-check2-lock me-1"></i>Your room is <strong>locked for renewal</strong>. Complete payment before
                                                <strong><?php echo e($booking->expires_at->format('j M Y')); ?></strong> to extend your stay by <?php echo e(\App\Models\Booking::OCCUPANCY_DAYS); ?> days.
                                            </div>
                                            <?php ($detail = $booking->hostel ? $booking->hostel->paymentDetail : null); ?>
                                            <div class="d-flex flex-wrap gap-2">
                                                <?php if($detail && $detail->hasPaystack()): ?>
                                                    <form method="POST" action="<?php echo e(route('booking.renewal.pay', $booking->id)); ?>">
                                                        <?php echo csrf_field(); ?>
                                                        <button class="btn btn-sm btn-primary mb-0"><i class="bi bi-credit-card me-1"></i>Pay GHS<?php echo e(number_format($booking->renewalPrice(), 2)); ?> Renewal</button>
                                                    </form>
                                                <?php endif; ?>
                                                <?php if($detail && $detail->hasMtnMoMoApi()): ?>
                                                    <form method="POST" action="<?php echo e(route('booking.renewal.pay.momo', $booking->id)); ?>" class="d-flex gap-2">
                                                        <?php echo csrf_field(); ?>
                                                        <input type="tel" name="phone" class="form-control form-control-sm" style="width: 180px;"
                                                            placeholder="MoMo number e.g. 0240000000" value="<?php echo e(old('phone', $booking->phone)); ?>" required maxlength="20">
                                                        <button class="btn btn-sm btn-primary mb-0"><i class="bi bi-phone me-1"></i>Pay with MoMo</button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php elseif(now()->lt($booking->expires_at)): ?>
                                        <div class="small text-muted">
                                            <i class="bi bi-calendar-check me-1"></i>Occupancy until <strong><?php echo e($booking->expires_at->format('j M Y')); ?></strong>.
                                            You can lock and rebook within <?php echo e(\App\Models\Booking::RENEWAL_WINDOW_DAYS); ?> days of this date.
                                        </div>
                                    <?php else: ?>
                                        <div class="small text-danger">
                                            <i class="bi bi-hourglass-split me-1"></i>Occupancy ended <?php echo e($booking->expires_at->format('j M Y')); ?>. This room is now available to other students.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php ($mates = $booking->room ? $booking->room->bookings->where('id', '!=', $booking->id) : collect()); ?>
                            <?php if($mates->isNotEmpty()): ?>
                                <div class="mt-3 pt-3 border-top">
                                    <a class="small fw-semibold text-body text-decoration-none collapsed d-inline-flex align-items-center gap-2"
                                        data-bs-toggle="collapse" href="#roommates-<?php echo e($booking->id); ?>" role="button" aria-expanded="false" aria-controls="roommates-<?php echo e($booking->id); ?>">
                                        <i class="bi bi-people text-primary"></i>Roommates (<?php echo e($mates->count()); ?>)
                                        <i class="bi bi-chevron-down small"></i>
                                    </a>
                                    <div class="collapse mt-2" id="roommates-<?php echo e($booking->id); ?>">
                                        <ul class="list-group list-group-flush">
                                            <?php $__currentLoopData = $mates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center gap-3 px-0">
                                                    <span class="fw-semibold small">
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
                                                            <a href="mailto:<?php echo e($mate->email); ?>" class="text-muted text-decoration-none d-block small"><?php echo e($mate->email); ?></a>
                                                        <?php endif; ?>
                                                    </span>
                                                </li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </ul>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="card shadow rounded-3 p-5 text-center">
                            <i class="bi bi-bookmark-x display-4 text-muted mb-3"></i>
                            <h5>No bookings yet</h5>
                            <p class="text-muted mb-3">Browse hostels and reserve a room to see your bookings here.</p>
                            <div>
                                <a href="<?php echo e(route('home')); ?>" class="btn btn-primary mb-0">Find a Hostel</a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="mt-3">
                        <?php echo e($bookings->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\studentroombookings\resources\views/account/my-bookings.blade.php ENDPATH**/ ?>