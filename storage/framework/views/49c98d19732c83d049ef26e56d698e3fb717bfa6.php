<?php $__env->startSection('title', 'Edit Room'); ?>
<?php $__env->startSection('heading', 'Edit Room'); ?>
<?php $__env->startSection('subheading', $room->name . ' &middot; ' . $hostel->displayName()); ?>

<?php $__env->startSection('content'); ?>
    <div class="card shadow rounded-3 p-4 col-lg-8">
        <form method="POST" action="<?php echo e(route('admin.hostels.rooms.update', [$hostel, $room])); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Room Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" value="<?php echo e(old('name', $room->name)); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Room Type <span class="text-danger">*</span></label>
                    <select class="form-select" name="room_type">
                        <?php $__currentLoopData = $roomTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php if (old('room_type', $room->room_type) === $key) { echo 'selected'; } ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gender Category <span class="text-danger">*</span></label>
                    <select class="form-select" name="gender_category">
                        <?php $__currentLoopData = $genders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php if (old('gender_category', $room->gender_category) === $key) { echo 'selected'; } ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Price Per Academic Year (GHS) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="price" value="<?php echo e(old('price', $room->price)); ?>" min="0" step="0.01" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Total Rooms <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="total_rooms" value="<?php echo e(old('total_rooms', $room->total_rooms)); ?>" min="1" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label d-block">Beds Available</label>
                    <div class="form-control-plaintext">
                        <strong><?php echo e($room->availableSpace()); ?> of <?php echo e($room->totalBeds()); ?></strong>
                    </div>
                    <small class="text-muted"><?php echo e($room->total_rooms); ?> rooms &times; <?php echo e($room->capacity); ?> people per room.</small>
                </div>
                <div class="col-12">
                    <label class="form-label d-block">Room Amenities <small class="text-muted">(optional)</small></label>
                    <?php if($amenities->isEmpty()): ?>
                        <div class="alert alert-warning py-2 mb-0">
                            No amenities available yet. An admin can add them on the
                            <a class="text-primary" href="<?php echo e(route('admin.amenities.index')); ?>">Amenities</a> page.
                        </div>
                    <?php else: ?>
                        <div class="border rounded-2 p-3" style="max-height: 220px; overflow-y: auto;">
                            <div class="row g-2">
                                <?php $__currentLoopData = $amenities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $amenity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-md-4">
                                        <div class="form-check py-1 m-0">
                                            <input class="form-check-input" type="checkbox" name="amenities[]" value="<?php echo e($amenity->id); ?>"
                                                id="amenity_<?php echo e($amenity->id); ?>"
                                                <?php if (in_array($amenity->id, old('amenities', $selected ?? []))) { echo 'checked'; } ?>>
                                            <label class="form-check-label" for="amenity_<?php echo e($amenity->id); ?>">
                                                <?php if($amenity->icon): ?>
                                                    <i class="bi <?php echo e($amenity->icon); ?> me-1 text-primary"></i>
                                                <?php endif; ?>
                                                <?php echo e($amenity->name); ?>

                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" <?php if (old('is_active', $room->is_active)) { echo 'checked'; } ?>>
                        <label class="form-check-label" for="is_active">Active &amp; bookable by students</label>
                    </div>
                </div>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary mb-0"><i class="bi bi-check-lg me-1"></i>Save Changes</button>
                    <a href="<?php echo e(route('admin.hostels.rooms.index', $hostel)); ?>" class="btn btn-light mb-0">Cancel</a>
                </div>
            </div>
        </form>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\studentroombookings\resources\views/admin/rooms/edit.blade.php ENDPATH**/ ?>