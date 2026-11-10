<?php $__env->startSection('title', 'Rooms'); ?>
<?php $__env->startSection('heading', 'Rooms'); ?>
<?php $__env->startSection('subheading', $hostel->displayName()); ?>

<?php $__env->startSection('content'); ?>
    <div class="card shadow rounded-3">
        <div class="card-header bg-transparent py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="d-flex align-items-center gap-2">
                <a href="<?php echo e(route('manager.hostels.index')); ?>" class="btn btn-sm btn-light mb-0"><i class="bi bi-arrow-left"></i></a>
                <div>
                    <h6 class="mb-0"><?php echo e($hostel->displayName()); ?></h6>
                    <small class="text-muted"><?php echo e($hostel->rooms->count()); ?> rooms &middot; from GHS<?php echo e(number_format($hostel->starting_price, 2)); ?> &middot; <?php echo e($hostel->availableRooms()); ?> available total</small>
                </div>
            </div>
            <a href="<?php echo e(route('manager.hostels.rooms.create', $hostel)); ?>" class="btn btn-primary mb-0"><i class="bi bi-door-open me-1"></i>Add Room</a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-muted small">
                        <th>Room</th>
                        <th>Type</th>
                        <th>Gender</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Available</th>
                        <th>Occupants</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="fw-semibold"><?php echo e($room->name); ?></td>
                            <td><?php echo e($room->room_type); ?></td>
                            <td>
                                <?php if($room->gender_category === 'Male'): ?>
                                    <i class="fa fa-male me-1 text-primary"></i>
                                <?php elseif($room->gender_category === 'Female'): ?>
                                    <i class="fa fa-female me-1 text-primary"></i>
                                <?php else: ?>
                                    <i class="fa fa-genderless me-1 text-muted"></i>
                                <?php endif; ?>
                                <?php echo e($room->gender_category); ?>

                            </td>
                            <td>GHS<?php echo e(number_format($room->price, 2)); ?></td>
                            <td><?php echo e($room->total_rooms); ?></td>
                            <td>
                                <span class="badge <?php echo e($room->availableSpace() > 0 ? 'bg-success' : 'bg-danger'); ?>"><?php echo e($room->availableSpace()); ?></span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-light border d-inline-flex align-items-center gap-1 mb-0"
                                    data-bs-toggle="collapse" href="#room-occupants-<?php echo e($room->id); ?>" aria-expanded="false" aria-controls="room-occupants-<?php echo e($room->id); ?>">
                                    <i class="bi bi-people"></i><?php echo e($room->bookings->count()); ?>

                                </button>
                            </td>
                            <td>
                                <?php if($room->is_active): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Hidden</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="<?php echo e(route('manager.hostels.rooms.edit', [$hostel, $room])); ?>" class="btn btn-sm btn-primary-soft mb-0"><i class="bi bi-pencil"></i> Edit</a>
                                    <form method="POST" action="<?php echo e(route('manager.hostels.rooms.toggle', [$hostel, $room])); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm <?php echo e($room->is_active ? 'btn-warning' : 'btn-success'); ?> mb-0" title="<?php echo e($room->is_active ? 'Deactivate' : 'Activate'); ?>">
                                            <i class="bi bi-<?php echo e($room->is_active ? 'eye-slash' : 'eye'); ?>"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="<?php echo e(route('manager.hostels.rooms.destroy', [$hostel, $room])); ?>"
                                        onsubmit="return confirm('Delete room <?php echo e(addslashes($room->name)); ?>?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-danger-soft mb-0"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <tr class="collapse" id="room-occupants-<?php echo e($room->id); ?>">
                            <td colspan="9">
                                <div class="p-3">
                                    <?php if($room->bookings->isNotEmpty()): ?>
                                        <div class="table-responsive">
                                            <table class="table table-sm align-middle mb-0">
                                                <thead>
                                                    <tr class="text-muted small">
                                                        <th>Student</th>
                                                        <th>Phone</th>
                                                        <th>Email</th>
                                                        <th>Booking Ref</th>
                                                        <th>Status</th>
                                                        <th>Payment</th>
                                                        <th>Occupancy</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $__currentLoopData = $room->bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr>
                                                            <td class="fw-semibold">
                                                                <?php echo e($mate->student_name); ?>

                                                                <?php if($mate->gender): ?>
                                                                    <small class="text-muted">&middot; <?php echo e($mate->gender); ?></small>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?php if($mate->phone): ?>
                                                                    <a href="tel:<?php echo e($mate->phone); ?>" class="text-decoration-none"><?php echo e($mate->phone); ?></a>
                                                                <?php else: ?>
                                                                    <span class="text-muted">-</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?php if($mate->email): ?>
                                                                    <a href="mailto:<?php echo e($mate->email); ?>" class="text-decoration-none small"><?php echo e($mate->email); ?></a>
                                                                <?php else: ?>
                                                                    <span class="text-muted">-</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="small"><?php echo e($mate->booking_ref); ?></td>
                                                            <td>
                                                                <?php if($mate->status === 'confirmed'): ?>
                                                                    <span class="badge bg-success">Confirmed</span>
                                                                <?php elseif($mate->status === 'cancelled'): ?>
                                                                    <span class="badge bg-danger">Cancelled</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?php if($mate->isPaid()): ?>
                                                                    <span class="badge bg-success">Paid</span>
                                                                <?php elseif($mate->payment_status === \App\Models\Booking::PAYMENT_PENDING): ?>
                                                                    <span class="badge bg-warning text-dark">Pay Pending</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-secondary">Unpaid</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="small">
                                                                <?php if($mate->status === 'confirmed' && $mate->isOccupying()): ?>
                                                                    <span class="badge bg-success">Until <?php echo e($mate->expires_at ? $mate->expires_at->format('j M y') : 'N/A'); ?></span>
                                                                    <?php if($mate->isWithinRenewalWindow()): ?>
                                                                        <span class="badge bg-warning text-dark mt-1">Renewal window</span>
                                                                    <?php endif; ?>
                                                                    <?php if($mate->isRenewalLocked()): ?>
                                                                        <span class="badge bg-info text-dark mt-1">Locked</span>
                                                                    <?php endif; ?>
                                                                <?php elseif($mate->status === 'confirmed'): ?>
                                                                    <span class="badge bg-secondary">Expired <?php echo e($mate->expires_at ? $mate->expires_at->format('j M y') : ''); ?></span>
                                                                <?php else: ?>
                                                                    <span class="text-muted">-</span>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <p class="small text-muted mb-0"><i class="bi bi-people me-1"></i>No occupants in this room yet.</p>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                No rooms yet. <a class="text-primary" href="<?php echo e(route('manager.hostels.rooms.create', $hostel)); ?>">Add the first room</a>.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\studentroombookings\resources\views/manager/rooms/index.blade.php ENDPATH**/ ?>