<?php $__env->startSection('title', 'Book a Room'); ?>

<?php $__env->startSection('content'); ?>
    <!-- Page title -->
    <section class="bg-primary py-3">
        <div class="container">
            <nav class="d-flex mb-1" aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-dots mb-0">
                    <li class="breadcrumb-item"><a class="text-white" href="<?php echo e(route('home')); ?>">Home</a></li>
                    <li class="breadcrumb-item"><a class="text-white" href="<?php echo e(route('hostel.detail', $hostel->slug)); ?>"><?php echo e($hostel->displayName()); ?></a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Book</li>
                </ol>
            </nav>
            <h1 class="text-white mb-0">Book Your Room</h1>
        </div>
    </section>

    <section class="pt-4 pt-lg-5">
        <div class="container">
            <div class="row g-4 g-xl-5">
                <!-- Left -->
                <div class="col-lg-7 col-xl-8">
                    <div class="card shadow rounded-3 p-4">
                        <div class="d-flex gap-3 align-items-center mb-4">
                            <img src="<?php echo e(asset($hostel->coverImagePath())); ?>"
                                class="rounded-2 object-fit-cover" style="height: 90px; width: 90px;" alt="<?php echo e($hostel->name); ?>">
                            <div>
                                <h4 class="mb-1"><?php echo e($hostel->displayName()); ?></h4>
                                <p class="mb-1"><i class="fa fa-location-dot text-primary me-1"></i><?php echo e($hostel->location); ?></p>
                                <small class="text-muted"><?php echo e($hostel->distance); ?></small>
                            </div>
                        </div>

                        <?php if($errors->any()): ?>
                            <div class="alert alert-danger py-2 mb-4">
                                <ul class="mb-0 small">
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="<?php echo e(route('booking.store', $hostel->slug)); ?>">
                            <?php echo csrf_field(); ?>

                            <div class="mb-4">
                                <h5 class="mb-0"><i class="bi bi-door-open text-primary me-2"></i>Select a Room</h5>
                            </div>

                            <div class="mb-4">
                                <label class="form-label" for="room_type_filter">Room Category</label>
                                <select id="room_type_filter" class="form-select" data-room-type-filter
                                    aria-describedby="room_type_filter_help">
                                    <option value="">All room categories</option>
                                    <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($type); ?>" <?php if ($selectedType === $type) { echo 'selected'; } ?>><?php echo e($type); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <small id="room_type_filter_help" class="text-muted">
                                    Pick a category to see every room in it, then choose the exact room you want.
                                </small>
                            </div>

                            <div data-room-list>
                                <?php $__currentLoopData = $roomsByType; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $rooms): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <section class="mb-3" data-room-group="<?php echo e($type); ?>">
                                        <h6 class="mt-3"><?php echo e($type); ?></h6>
                                        <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php ($space = $room->availableSpace()); ?>
                                            <label class="d-block border rounded-2 p-3 mb-2 booking-room-option <?php echo e(($space > 0) ? '' : 'opacity-50'); ?>">
                                                <div class="d-flex justify-content-between align-items-center gap-3">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <input type="radio" name="room" id="room_<?php echo e($room->id); ?>" value="<?php echo e($room->id); ?>"
                                                            class="form-check-input mt-0" <?php echo e((request('room') == $room->id) ? 'checked' : ''); ?>

                                                            <?php if($space <= 0): ?> disabled <?php endif; ?>>
                                                        <span>
                                                            <strong><?php echo e($room->name); ?></strong>
                                                            <small class="text-muted d-block">
                                                                <?php if($room->gender_category === 'Male'): ?>
                                                                    <i class="fa fa-male text-primary"></i>
                                                                <?php elseif($room->gender_category === 'Female'): ?>
                                                                    <i class="fa fa-female text-primary"></i>
                                                                <?php else: ?>
                                                                    <i class="fa fa-genderless text-primary"></i>
                                                                <?php endif; ?>
                                                                <?php echo e($room->gender_category); ?> &middot;
                                                                <?php if($space > 0): ?>
                                                                    <span class="text-success"><?php echo e($space); ?> bed<?php echo e($space === 1 ? '' : 's'); ?> left</span>
                                                                <?php else: ?>
                                                                    <span class="text-danger">Sold out</span>
                                                                <?php endif; ?>
                                                            </small>
                                                            <?php if($room->amenities->isNotEmpty()): ?>
                                                                <small class="d-flex flex-wrap gap-1 mt-2">
                                                                    <?php $__currentLoopData = $room->amenities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $amenity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                        <span class="badge bg-light text-body border small">
                                                                            <i class="bi <?php echo e($amenity->icon ?: 'bi-check2-circle'); ?> me-1 text-primary"></i><?php echo e($amenity->name); ?>

                                                                        </span>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                </small>
                                                            <?php endif; ?>
                                                            <?php if($room->bookings->isNotEmpty()): ?>
                                                                <?php ($activeOccupants = $room->bookings->filter(fn ($b) => $b->expires_at === null || $b->expires_at->gte(\Illuminate\Support\Carbon::now()))); ?>
                                                                <?php if($activeOccupants->isNotEmpty()): ?>
                                                                    <small class="text-muted d-block mt-1">
                                                                        <i class="bi bi-people me-1"></i><?php echo e($activeOccupants->count()); ?> occupant(s) already placed
                                                                    </small>
                                                                <?php endif; ?>
                                                            <?php endif; ?>
                                                        </span>
                                                    </div>
                                                    <span class="fw-bold text-primary">GHS<?php echo e(number_format($room->price, 2)); ?></span>
                                                </div>
                                            </label>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </section>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                <p class="text-muted small d-none" data-room-empty>
                                    No rooms match that category. Choose &ldquo;All room categories&rdquo; to see everything.
                                </p>
                            </div>

                            <hr class="my-4">

                            <h5 class="mb-3"><i class="bi bi-person text-primary me-2"></i>Student Details</h5>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="student_name" value="<?php echo e(old('student_name', auth()->user()->name ?? '')); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" value="<?php echo e(old('email', auth()->user()->email ?? '')); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" name="phone" value="<?php echo e(old('phone', auth()->user()->phone ?? '')); ?>" required placeholder="e.g. 0240000000">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Academic Year</label>
                                    <select class="form-select" name="academic_year">
                                        <option value="">Select academic year</option>
                                        <option value="2025/2026" <?php if (old('academic_year') === '2025/2026') { echo 'selected'; } ?>>2025/2026</option>
                                        <option value="2026/2027" <?php if (old('academic_year') === '2026/2027' || !old('academic_year')) { echo 'selected'; } ?>>2026/2027</option>
                                        <option value="2027/2028" <?php if (old('academic_year') === '2027/2028') { echo 'selected'; } ?>>2027/2028</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Institution</label>
                                    <input type="text" class="form-control" name="institution" value="<?php echo e(old('institution', $hostel->institution_name)); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Gender</label>
                                    <select class="form-select" name="gender">
                                        <option value="">Select gender</option>
                                        <option value="Male" <?php if (old('gender') === 'Male') { echo 'selected'; } ?>>Male</option>
                                        <option value="Female" <?php if (old('gender') === 'Female') { echo 'selected'; } ?>>Female</option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <a href="<?php echo e(route('hostel.detail', $hostel->slug)); ?>" class="btn btn-light mb-0"><i class="bi bi-arrow-left me-2"></i>Back</a>
                                <button type="submit" class="btn btn-primary mb-0">Confirm Booking<i class="bi bi-check2-circle ms-2"></i></button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Right summary -->
                <aside class="col-lg-5 col-xl-4">
                    <div class="card shadow rounded-3 p-4 sticky-lg-top">
                        <h5 class="mb-3">Booking Summary</h5>
                        <p class="small text-muted mb-3">Your reservation will be confirmed once payment is made. Pay directly at the hostel or via mobile money to the number provided.</p>

                        <ul class="list-group list-group-flush mb-4">
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Hostel</span>
                                <span class="fw-semibold text-end"><?php echo e(\Illuminate\Support\Str::limit($hostel->displayName(), 40)); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Range</span>
                                <span class="fw-semibold">GHS<?php echo e(number_format($hostel->starting_price, 2)); ?> - GHS<?php echo e(number_format($hostel->rooms->max('price'), 2)); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Payment</span>
                                <span class="fw-semibold">Per Academic Year</span>
                            </li>
                        </ul>

                        <div class="alert alert-primary border-0 mb-0">
                            <h6 class="alert-heading"><i class="bi bi-shield-check me-2"></i>Why book with us?</h6>
                            <ul class="small mb-0 text-primary-hover">
                                <li>Verified hostels only</li>
                                <li>Secure &amp; instant reservation</li>
                                <li>Dedicated student support</li>
                            </ul>
                        </div>
                    </div>

                    <?php if(isset($room) && $room && $occupants->isNotEmpty()): ?>
                        <div class="card shadow rounded-3 p-4 mt-4">
                            <h5 class="mb-3"><i class="bi bi-people text-primary me-2"></i>Already in this room</h5>
                            <p class="small text-muted mb-3">These students have confirmed this room. You&rsquo;ll be sharing with them once your booking is confirmed.</p>
                            <ul class="list-group list-group-flush">
                                <?php $__currentLoopData = $occupants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                                                <a href="mailto:<?php echo e($mate->email); ?>" class="text-muted small text-decoration-none d-block"><?php echo e($mate->email); ?></a>
                                            <?php endif; ?>
                                        </span>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                            <small class="text-muted d-block mt-2"><i class="bi bi-shield-lock me-1"></i>Please use this contact information responsibly and only to coordinate your stay.</small>
                        </div>
                    <?php endif; ?>
                </aside>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        (function () {
            const options = document.querySelectorAll('.booking-room-option');

            options.forEach(label => {
                const input = label.querySelector('input');
                if (!input) return;

                input.addEventListener('change', () => {
                    options.forEach(l => l.classList.remove('border-primary'));
                    if (input.checked) label.classList.add('border-primary');
                });

                if (input.checked) label.classList.add('border-primary');
            });

            const prechecked = document.querySelector('.booking-room-option input:checked');
            if (prechecked && prechecked.scrollIntoView) {
                prechecked.closest('.booking-room-option').scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            const filter = document.querySelector('[data-room-type-filter]');
            const groups = Array.from(document.querySelectorAll('[data-room-group]'));
            const emptyNote = document.querySelector('[data-room-empty]');

            if (!filter || !groups.length) return;

            function applyFilter(type) {
                let visible = 0;

                groups.forEach(group => {
                    const show = !type || group.dataset.roomGroup === type;
                    group.classList.toggle('d-none', !show);
                    if (show) visible++;
                });

                if (emptyNote) {
                    emptyNote.classList.toggle('d-none', visible > 0);
                }
            }

            filter.addEventListener('change', () => {
                const type = filter.value;

                // A room selected under a different category would be hidden with
                // its radio still checked, which submits a room the user cannot see.
                if (type) {
                    const stale = Array.from(document.querySelectorAll('input[name="room"]:checked'))
                        .find(input => !input.closest('[data-room-group]')
                            || input.closest('[data-room-group]').dataset.roomGroup !== type);

                    if (stale) stale.checked = false;
                }

                applyFilter(type);

                const url = new URL(window.location.href);

                if (type) {
                    url.searchParams.set('room_type', type);
                } else {
                    url.searchParams.delete('room_type');
                }

                window.history.replaceState({}, '', url);
            });

            applyFilter(filter.value);
        })();
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\studentroombookings\resources\views/pages/booking.blade.php ENDPATH**/ ?>