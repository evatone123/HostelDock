<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('heading', 'Dashboard'); ?>
<?php $__env->startSection('subheading', 'Overview of your platform'); ?>

<?php $__env->startSection('content'); ?>
    <!-- Section Header: Financial Overview -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 text-dark fw-bold"><i class="bi bi-wallet2 text-primary me-2"></i>Financial & Revenue Overview</h5>
        <span class="badge bg-primary-soft text-primary px-3 py-2">All Payment Amounts</span>
    </div>

    <!-- Revenue & Payments Overview Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow rounded-3 p-3 h-100 border-start border-primary border-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-primary text-white small fw-semibold">UNLOCK ACCESS</span>
                    <i class="bi bi-ticket-perforated fs-4 text-primary"></i>
                </div>
                <div>
                    <p class="text-muted small mb-1 fw-semibold text-uppercase">Booking Token Payments</p>
                    <h4 class="mb-1 text-primary fw-bold">GHS<?php echo e(number_format($stats['booking_token_revenue'], 2)); ?></h4>
                    <small class="text-muted"><?php echo e($stats['booking_token_count']); ?> paid booking tokens</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow rounded-3 p-3 h-100 border-start border-success border-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-success bg-opacity-15 text-success small fw-semibold">ROOM RESERVATIONS</span>
                    <i class="bi bi-cash-stack fs-4 text-success"></i>
                </div>
                <div>
                    <p class="text-muted small mb-1 fw-semibold text-uppercase">Confirmed Bookings Payments</p>
                    <h4 class="mb-1 text-success fw-bold">GHS<?php echo e(number_format($stats['confirmed_bookings_revenue'], 2)); ?></h4>
                    <small class="text-muted"><?php echo e($stats['confirmed_bookings']); ?> confirmed bookings</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow rounded-3 p-3 h-100 border-start border-warning border-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-warning bg-opacity-25 text-dark small fw-semibold">TOTAL EARNINGS</span>
                    <i class="bi bi-wallet2 fs-4 text-warning"></i>
                </div>
                <div>
                    <p class="text-muted small mb-1 fw-semibold text-uppercase">Total Platform Revenue</p>
                    <h4 class="mb-1 text-dark fw-bold">GHS<?php echo e(number_format($stats['total_revenue'], 2)); ?></h4>
                    <small class="text-muted">Tokens + Confirmed Bookings</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Header: Platform Statistics -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 text-dark fw-bold"><i class="bi bi-speedometer2 text-success me-2"></i>Platform Statistics</h5>
        <span class="badge bg-success-soft text-success px-3 py-2">General Metrics</span>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow rounded-3 p-3 h-100 border-start border-info border-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-info bg-opacity-15 text-info small fw-semibold">PROPERTIES</span>
                    <i class="bi bi-building fs-4 text-info"></i>
                </div>
                <div>
                    <p class="text-muted small mb-1 fw-semibold text-uppercase">Hostels</p>
                    <h4 class="mb-0 fw-bold"><?php echo e($stats['hostels']); ?></h4>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow rounded-3 p-3 h-100 border-start border-success border-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-success bg-opacity-15 text-success small fw-semibold">INVENTORY</span>
                    <i class="bi bi-door-open fs-4 text-success"></i>
                </div>
                <div>
                    <p class="text-muted small mb-1 fw-semibold text-uppercase">Rooms Listed</p>
                    <h4 class="mb-0 fw-bold"><?php echo e($stats['rooms']); ?></h4>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow rounded-3 p-3 h-100 border-start border-warning border-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-warning bg-opacity-25 text-dark small fw-semibold">RESERVATIONS</span>
                    <i class="bi bi-bookmark-check fs-4 text-warning"></i>
                </div>
                <div>
                    <p class="text-muted small mb-1 fw-semibold text-uppercase">Total Bookings</p>
                    <h4 class="mb-0 fw-bold"><?php echo e($stats['bookings']); ?></h4>
                    <small class="text-muted"><?php echo e($stats['pending_bookings']); ?> pending &middot; <?php echo e($stats['confirmed_bookings']); ?> confirmed</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow rounded-3 p-3 h-100 border-start border-danger border-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-danger bg-opacity-15 text-danger small fw-semibold">ACCOUNTS</span>
                    <i class="bi bi-people fs-4 text-danger"></i>
                </div>
                <div>
                    <p class="text-muted small mb-1 fw-semibold text-uppercase">User Accounts</p>
                    <h4 class="mb-0 fw-bold"><?php echo e($stats['users']); ?></h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card shadow rounded-3 mb-4">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                    <h6 class="mb-0">Recent Confirmed Bookings & Payments</h6>
                    <a href="<?php echo e(route('admin.bookings.index')); ?>" class="btn btn-sm btn-primary-soft mb-0">View all</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr class="text-muted small">
                                <th>Ref</th>
                                <th>Student</th>
                                <th>Hostel</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $recentBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><span class="fw-semibold"><?php echo e($booking->booking_ref); ?></span></td>
                                    <td>
                                        <div><?php echo e($booking->student_name); ?></div>
                                        <small class="text-muted"><?php echo e($booking->room_type); ?> &middot; <?php echo e($booking->academic_year); ?></small>
                                    </td>
                                    <td><?php echo e(\Illuminate\Support\Str::limit(optional($booking->hostel)->displayName(), 22)); ?></td>
                                    <td>GHS<?php echo e(number_format($booking->amount, 2)); ?></td>
                                    <td>
                                        <?php if($booking->status === 'confirmed'): ?>
                                            <span class="badge bg-success">Confirmed</span>
                                        <?php elseif($booking->status === 'cancelled'): ?>
                                            <span class="badge bg-danger">Cancelled</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No bookings yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card shadow rounded-3">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                    <h6 class="mb-0">Recent Booking Token Payments (Unlock Access)</h6>
                    <a href="<?php echo e(route('admin.tokens.index')); ?>" class="btn btn-sm btn-primary-soft mb-0">View all</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr class="text-muted small">
                                <th>Token Ref</th>
                                <th>Student</th>
                                <th>Amount</th>
                                <th>Paid At</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $recentTokenPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $token): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><span class="fw-semibold"><?php echo e($token->token_ref); ?></span></td>
                                    <td>
                                        <div><?php echo e(optional($token->user)->name ?? 'N/A'); ?></div>
                                        <small class="text-muted"><?php echo e(optional($token->user)->email); ?></small>
                                    </td>
                                    <td class="fw-semibold text-primary">GHS<?php echo e(number_format($token->amount, 2)); ?></td>
                                    <td><small><?php echo e(optional($token->paid_at)->format('M d, Y H:i') ?? $token->updated_at->format('M d, Y H:i')); ?></small></td>
                                    <td>
                                        <span class="badge bg-success"><?php echo e(ucfirst($token->status)); ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No token payments yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow rounded-3 mb-3">
                <div class="card-header bg-transparent py-3">
                    <h6 class="mb-0">Low Availability Rooms</h6>
                </div>
                <ul class="list-group list-group-flush">
                    <?php $__empty_1 = true; $__currentLoopData = $lowRooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="small fw-semibold"><?php echo e($room->name); ?></div>
                                <small class="text-muted"><?php echo e(optional($room->hostel)->displayName()); ?></small>
                            </div>
                            <span class="badge <?php echo e($room->availableSpace() > 0 ? 'bg-warning text-dark' : 'bg-danger'); ?>">
                                <?php echo e($room->availableSpace()); ?> bed<?php echo e($room->availableSpace() === 1 ? '' : 's'); ?> left
                            </span>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="list-group-item text-muted text-center py-4">All rooms have full availability.</li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="card shadow rounded-3">
                <div class="card-header bg-transparent py-3">
                    <h6 class="mb-0">Hostel Payment Gateways</h6>
                </div>
                <div class="card-body pt-0">
                    <p class="small mb-3">Per-hostel gateways shown to students on the booking confirmation and invoice pages.</p>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-0"><?php echo e($stats['hostel_gateways_configured']); ?></h4>
                            <small class="text-muted">hostels with an online gateway (Paystack / MTN MoMo)</small>
                        </div>
                        <a href="<?php echo e(route('admin.hostels.index')); ?>" class="btn btn-sm btn-primary-soft mb-0">Hostels</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\studentroombookings\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>