<?php $__env->startSection('title', 'Unlock Access Tokens'); ?>
<?php $__env->startSection('heading', 'Unlock Access Tokens'); ?>
<?php $__env->startSection('subheading', 'All booking token payments (unlock access)'); ?>

<?php $__env->startSection('content'); ?>
    <div class="card shadow rounded-3">
        <div class="card-header bg-transparent py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <form method="GET" class="d-flex flex-wrap gap-2">
                <div class="input-group" style="width: 240px;">
                    <span class="input-group-text bg-body border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="q" value="<?php echo e(request('q')); ?>" placeholder="Token ref, student, email">
                </div>
                <select class="form-select" name="status" style="width: 160px;">
                    <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php if (request('status') === $key) { echo 'selected'; } ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <button class="btn btn-primary mb-0" type="submit">Filter</button>
                <?php if(request('q') || request('status')): ?>
                    <a href="<?php echo e(route('admin.tokens.index')); ?>" class="btn btn-light mb-0">Reset</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-muted small">
                        <th>Token Ref / Date</th>
                        <th>Student</th>
                        <th>Amount</th>
                        <th>Gateway</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $tokens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $token): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <span class="fw-semibold"><?php echo e($token->token_ref); ?></span>
                                <small class="text-muted d-block">
                                    <?php if($token->paid_at): ?>
                                        <?php echo e($token->paid_at->format('d M Y, H:i')); ?>

                                    <?php else: ?>
                                        <?php echo e($token->created_at->format('d M Y, H:i')); ?>

                                    <?php endif; ?>
                                </small>
                            </td>
                            <td>
                                <div><?php echo e(optional($token->user)->name ?? 'N/A'); ?></div>
                                <small class="text-muted"><?php echo e(optional($token->user)->email); ?></small>
                            </td>
                            <td class="fw-semibold text-primary">GHS<?php echo e(number_format($token->amount, 2)); ?></td>
                            <td>
                                <?php if($token->gateway === 'paystack'): ?>
                                    <span class="badge bg-light text-dark border">Paystack</span>
                                <?php elseif($token->gateway === 'mtn_momo'): ?>
                                    <span class="badge bg-light text-dark border">MTN MoMo</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted border">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($token->status === 'active'): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php elseif($token->status === 'used'): ?>
                                    <span class="badge bg-info text-white">Used</span>
                                <?php elseif($token->status === 'pending'): ?>
                                    <span class="badge bg-warning text-dark">Pending</span>
                                <?php elseif($token->status === 'failed'): ?>
                                    <span class="badge bg-danger">Failed</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?php echo e($token->statusLabel()); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No tokens found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-transparent d-flex justify-content-between align-items-center py-3">
            <small class="text-muted"><?php echo e($tokens->total()); ?> tokens</small>
            <?php echo e($tokens->links()); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\studentroombookings\resources\views/admin/tokens/index.blade.php ENDPATH**/ ?>