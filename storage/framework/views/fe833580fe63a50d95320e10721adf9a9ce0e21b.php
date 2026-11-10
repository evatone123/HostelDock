<?php $__env->startSection('title', 'Manage Room Categories'); ?>
<?php $__env->startSection('heading', 'Room Categories'); ?>
<?php $__env->startSection('subheading', 'Add and manage the categories that can be applied to rooms'); ?>

<?php $__env->startSection('content'); ?>
    <div class="card shadow rounded-3">
        <div class="card-header bg-transparent py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <form method="GET" class="d-flex flex-wrap gap-2">
                <div class="input-group" style="width: 260px;">
                    <span class="input-group-text bg-body border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="q" value="<?php echo e(request('q')); ?>" placeholder="Search categories">
                </div>
                <button class="btn btn-primary mb-0" type="submit">Filter</button>
                <?php if(request('q')): ?>
                    <a href="<?php echo e(route('admin.room-categories.index')); ?>" class="btn btn-light mb-0">Reset</a>
                <?php endif; ?>
            </form>
            <a href="<?php echo e(route('admin.room-categories.create')); ?>" class="btn btn-primary mb-0"><i class="bi bi-plus-lg me-1"></i>Add Category</a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-muted small">
                        <th>Category</th>
                        <th>People Per Room</th>
                        <th>Used By</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="fw-semibold"><?php echo e($category->name); ?></td>
                            <td><?php echo e($category->capacity); ?></td>
                            <td><?php echo e($category->rooms_count); ?> room<?php echo e($category->rooms_count === 1 ? '' : 's'); ?></td>
                            <td>
                                <?php if($category->is_active): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Hidden</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="<?php echo e(route('admin.room-categories.edit', $category)); ?>" class="btn btn-sm btn-primary-soft mb-0"><i class="bi bi-pencil"></i> Edit</a>
                                    <form method="POST" action="<?php echo e(route('admin.room-categories.toggle', $category)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm <?php echo e($category->is_active ? 'btn-warning' : 'btn-success'); ?> mb-0" title="<?php echo e($category->is_active ? 'Deactivate' : 'Activate'); ?>">
                                            <i class="bi bi-<?php echo e($category->is_active ? 'eye-slash' : 'eye'); ?>"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="<?php echo e(route('admin.room-categories.destroy', $category)); ?>"
                                        onsubmit="return confirm('Delete category <?php echo e(addslashes($category->name)); ?>?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-danger-soft mb-0"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No room categories found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-transparent d-flex justify-content-between align-items-center py-3">
            <small class="text-muted"><?php echo e($categories->total()); ?> categories</small>
            <?php echo e($categories->links()); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\studentroombookings\resources\views/admin/room-categories/index.blade.php ENDPATH**/ ?>