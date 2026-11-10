<?php $__env->startSection('title', 'Manage Campuses'); ?>
<?php $__env->startSection('heading', 'Campuses'); ?>
<?php $__env->startSection('subheading', 'Add and manage the campuses hostels link to on the platform'); ?>

<?php $__env->startSection('content'); ?>
    <div class="card shadow rounded-3">
        <div class="card-header bg-transparent py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <form method="GET" class="d-flex flex-wrap gap-2">
                <div class="input-group" style="width: 260px;">
                    <span class="input-group-text bg-body border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="q" value="<?php echo e(request('q')); ?>" placeholder="Search campuses">
                </div>
                <button class="btn btn-primary mb-0" type="submit">Filter</button>
                <?php if(request('q')): ?>
                    <a href="<?php echo e(route('admin.campuses.index')); ?>" class="btn btn-light mb-0">Reset</a>
                <?php endif; ?>
            </form>
            <a href="<?php echo e(route('admin.campuses.create')); ?>" class="btn btn-primary mb-0"><i class="bi bi-plus-lg me-1"></i>Add Campus</a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-muted small">
                        <th>Campus</th>
                        <th>Slug</th>
                        <th>Linked Hostels</th>
                        <th>Map Coordinates</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $campuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $campus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <div class="fw-semibold"><?php echo e($campus->name); ?></div>
                                <?php if($campus->short_name): ?>
                                    <small class="text-muted"><?php echo e($campus->short_name); ?></small>
                                <?php endif; ?>
                            </td>
                            <td><code class="small"><?php echo e($campus->slug); ?></code></td>
                            <td>
                                <a class="text-body text-primary-hover fw-semibold" href="<?php echo e(route('admin.hostels.index', ['institution' => $campus->slug])); ?>">
                                    <?php echo e($campus->hostels_count); ?>

                                </a>
                            </td>
                            <td class="text-muted small">
                                <?php if($campus->latitude !== null && $campus->longitude !== null): ?>
                                    <?php echo e(number_format((float) $campus->latitude, 6)); ?>, <?php echo e(number_format((float) $campus->longitude, 6)); ?>

                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($campus->is_active): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Hidden</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="<?php echo e(route('admin.campuses.edit', $campus)); ?>" class="btn btn-sm btn-primary-soft mb-0"><i class="bi bi-pencil"></i> Edit</a>
                                    <form method="POST" action="<?php echo e(route('admin.campuses.toggle', $campus)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm <?php echo e($campus->is_active ? 'btn-warning' : 'btn-success'); ?> mb-0" title="<?php echo e($campus->is_active ? 'Deactivate' : 'Activate'); ?>">
                                            <i class="bi bi-<?php echo e($campus->is_active ? 'eye-slash' : 'eye'); ?>"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="<?php echo e(route('admin.campuses.destroy', $campus)); ?>"
                                        onsubmit="return confirm('Delete <?php echo e(addslashes($campus->name)); ?>?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-danger-soft mb-0"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No campuses found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-transparent d-flex justify-content-between align-items-center py-3">
            <small class="text-muted"><?php echo e($campuses->total()); ?> campuses</small>
            <?php echo e($campuses->links()); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\studentroombookings\resources\views/admin/campuses/index.blade.php ENDPATH**/ ?>