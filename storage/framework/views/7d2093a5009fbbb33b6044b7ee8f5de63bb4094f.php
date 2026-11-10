<?php $__env->startSection('title', 'Manage Amenities'); ?>
<?php $__env->startSection('heading', 'Amenities'); ?>
<?php $__env->startSection('subheading', 'Add and manage the amenities that can be applied to rooms'); ?>

<?php $__env->startSection('content'); ?>
    <div class="card shadow rounded-3">
        <div class="card-header bg-transparent py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <form method="GET" class="d-flex flex-wrap gap-2">
                <div class="input-group" style="width: 260px;">
                    <span class="input-group-text bg-body border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="q" value="<?php echo e(request('q')); ?>" placeholder="Search amenities">
                </div>
                <button class="btn btn-primary mb-0" type="submit">Filter</button>
                <?php if(request('q')): ?>
                    <a href="<?php echo e(route('admin.amenities.index')); ?>" class="btn btn-light mb-0">Reset</a>
                <?php endif; ?>
            </form>
            <a href="<?php echo e(route('admin.amenities.create')); ?>" class="btn btn-primary mb-0"><i class="bi bi-plus-lg me-1"></i>Add Amenity</a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-muted small">
                        <th>Amenity</th>
                        <th>Icon</th>
                        <th>Used By</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $amenities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $amenity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="fw-semibold"><?php echo e($amenity->name); ?></td>
                            <td>
                                <i class="bi <?php echo e($amenity->icon); ?> text-primary"></i>
                                <code class="small text-muted ms-1"><?php echo e($amenity->icon); ?></code>
                            </td>
                            <td><?php echo e($amenity->rooms_count); ?> room<?php echo e($amenity->rooms_count === 1 ? '' : 's'); ?></td>
                            <td>
                                <?php if($amenity->is_active): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Hidden</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="<?php echo e(route('admin.amenities.edit', $amenity)); ?>" class="btn btn-sm btn-primary-soft mb-0"><i class="bi bi-pencil"></i> Edit</a>
                                    <form method="POST" action="<?php echo e(route('admin.amenities.toggle', $amenity)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm <?php echo e($amenity->is_active ? 'btn-warning' : 'btn-success'); ?> mb-0" title="<?php echo e($amenity->is_active ? 'Deactivate' : 'Activate'); ?>">
                                            <i class="bi bi-<?php echo e($amenity->is_active ? 'eye-slash' : 'eye'); ?>"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="<?php echo e(route('admin.amenities.destroy', $amenity)); ?>"
                                        onsubmit="return confirm('Delete amenity <?php echo e(addslashes($amenity->name)); ?>?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-danger-soft mb-0"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No amenities found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-transparent d-flex justify-content-between align-items-center py-3">
            <small class="text-muted"><?php echo e($amenities->total()); ?> amenities</small>
            <?php echo e($amenities->links()); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\studentroombookings\resources\views/admin/amenities/index.blade.php ENDPATH**/ ?>