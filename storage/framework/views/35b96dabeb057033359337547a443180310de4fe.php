<div>
    <style>
        .z-index-1050 {
            z-index: 1050;
        }
    </style>

<section class="hero-promo pt-5 pb-4 mt-2 position-relative overflow-hidden">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <span class="eyebrow mb-3 d-inline-flex">Love where you stay</span>
                    <h1 class="hero-title my-3">HostelDock</h1>
                    <p class="lead mb-4">
                        Browse verified hostels near your campus, unlock booking access with a one-time payment,
                        and reserve the room you love in minutes.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="#hotel-view" class="btn btn-primary mb-0">Reserve a Room Today</a>
                        <a href="<?php echo e(route('about')); ?>" class="btn btn-outline-primary mb-0">Learn more</a>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block">
                    <div class="mc-stage">
                        <div class="mc-watermark" aria-hidden="true">hosteldock</div>
                        <div class="mc-orbit">
                            <div class="mc-portrait">
                                <img src="<?php echo e(hd_asset('assets/images/about/slider-imgs.jpg')); ?>" alt="HostelDock">
                            </div>
                            <a href="#hotel-view" class="mc-satellite mc-satellite-s1" aria-label="Search rooms">
                                <i class="bi bi-arrow-right"></i>
                            </a>
                            <a href="<?php echo e(route('about')); ?>" class="mc-satellite mc-satellite-s2" aria-label="About us">
                                <i class="bi bi-plus"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<section class="py-4 mt-2">
        <div class="container">
            <div class="row g-4" id="hotel-view">

                <div class="mb-2 position-relative">
                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                        wire:loading.class="bg-white bg-opacity-75 z-index-1050">
                        <div class="spinner-grow text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>

                    <style>
                        .input-group .form-control {
                            height: 48px;
                        }
                        .input-group-text {
                            background-color: transparent;
                        }
                        .animate-placeholder input::placeholder {
                            opacity: 0.6;
                            transition: all 0.3s ease;
                        }
                        .animate-placeholder input:focus::placeholder {
                            opacity: 0.3;
                            transform: translateY(-2px);
                        }
                    </style>

                    <form class="bg-mode border border-secondary-subtle rounded-3 position-relative p-4 pe-md-5 pb-5 pb-md-4 mb-4" wire:submit.prevent>

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="form-control-border form-control-transparent form-fs-md d-flex">
                                    <i class="bi bi-search fs-3 me-2 mt-2"></i>
                                    <div class="flex-grow-1">
                                        <label class="form-label">Hostel Name</label>
                                        <input type="text" class="form-control" placeholder="Enter hostel name" wire:model.debounce.550ms="hostel_name" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="position-relative text-center my-4">
                            <hr>
                            <span class="position-absolute top-50 start-50 translate-middle px-2 bg-mode small">Advance Filters</span>
                        </div>

                        <div class="row g-4 align-items-center">
                            <div class="col-lg-4">
                                <div class="form-control-border form-control-transparent form-fs-md d-flex">
                                    <i class="bi bi-clock-history fs-3 me-2 mt-2"></i>
                                    <div class="flex-grow-1">
                                        <label class="form-label">Institution</label>
                                        <select class="form-select" wire:model="institution">
                                            <?php $__currentLoopData = $institutions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($value); ?>"><?php echo e($label); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="d-flex">
                                    <i class="bi bi-building fs-3 me-2 mt-2"></i>
                                    <div class="form-control-border form-control-transparent form-fs-md">
                                        <label class="form-label">Room Type</label>
                                        <select class="form-select" wire:model="room_type">
                                            <?php $__currentLoopData = $roomTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($value); ?>"><?php echo e($label); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-control-border form-control-transparent form-fs-md d-flex">
                                    <i class="bi bi-gender-ambiguous fs-3 me-2 mt-2"></i>
                                    <div class="form-control-border form-control-transparent form-fs-md w-100">
                                        <label class="form-label">Gender</label>
                                        <select class="form-select" wire:model="gender_category">
                                            <?php $__currentLoopData = $genders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($value); ?>"><?php echo e($label); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 align-items-center mt-1">
                            <div class="col-lg-4">
                                <div class="form-control-border form-control-transparent form-fs-md d-flex">
                                    <i class="bi bi-cash-coin fs-3 me-2 mt-2"></i>
                                    <div class="flex-grow-1">
                                        <label class="form-label">Minimum Price (GHS)</label>
                                        <input type="number" min="0" step="1" class="form-control" placeholder="e.g. 500" autocomplete="off" wire:model.debounce.550ms="min_price">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-control-border form-control-transparent form-fs-md d-flex">
                                    <i class="bi bi-cash-coin fs-3 me-2 mt-2"></i>
                                    <div class="flex-grow-1">
                                        <label class="form-label">Maximum Price (GHS)</label>
                                        <input type="number" min="0" step="1" class="form-control" placeholder="e.g. 2000" autocomplete="off" wire:model.debounce.550ms="max_price">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-control-border form-control-transparent form-fs-md d-flex">
                                    <i class="bi bi-sort-alpha-down fs-3 me-2 mt-2"></i>
                                    <div class="flex-grow-1">
                                        <label class="form-label">Sort By</label>
                                        <select class="form-select" wire:model="sort_by">
                                            <?php $__currentLoopData = $sortOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($value); ?>"><?php echo e($label); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="btn-position-md-middle">
                            <button type="button" class="icon-lg btn btn-round btn-primary mb-0" wire:loading.remove wire:click="resetFilters">
                                <i class="fa fa-refresh fa-fw"></i>
                            </button>
                            <button type="button" class="icon-lg btn btn-round btn-primary mb-0" wire:loading wire:target="resetFilters">
                                <i class="fa fa-spinner fa-spin fa-fw"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <?php $__empty_1 = true; $__currentLoopData = $hostels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hostel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="col-md-6 col-xl-4">
                        <div class="card p-2 pb-0 h-100">
<div class="tiny-slider arrow-round arrow-xs arrow-dark rounded-2 overflow-hidden">
                                <div class="tiny-slider-inner" data-autoplay="true" data-arrow="true" data-dots="false" data-items="1"
                                    id="hostel-slider-<?php echo e($hostel->id); ?>" style="height: 250px;">
                                    <?php $__currentLoopData = $hostel->galleryImages(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div>
                                            <a href="<?php echo e(route('hostel.detail', $hostel->slug)); ?>">
                                                <img src="<?php echo e(asset($image)); ?>" alt="<?php echo e($hostel->displayName()); ?>" loading="lazy">
                                            </a>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>

                            <div class="card-body px-3 pb-0">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="badge bg-dark text-white"><i class="bi fa-fw bi-star-fill me-2 text-warning"></i><?php echo e(number_format($hostel->rating, 1)); ?></span>
                                    <span class="badge bg-dark text-white"><i class="bi fa-fw bi-door-open me-2"></i><?php echo e($hostel->availableRooms()); ?> Rooms Available</span>
                                </div>

                                <h5 class="card-title mb-1">
                                    <a href="<?php echo e(route('hostel.detail', $hostel->slug)); ?>">
                                        <?php echo e($hostel->displayName()); ?>

                                    </a>
                                </h5>
                                <?php if($hostel->institutionLabel()): ?>
                                    <p class="small text-muted mb-2"><i class="bi fa-fw bi-mortarboard me-1"></i><?php echo e($hostel->institutionLabel()); ?></p>
                                <?php endif; ?>

                                <ul class="nav nav-divider mb-2 mb-sm-3">
                                    <li class="nav-item"><i class="fa fa-location-dot text-primary"></i><?php echo e($hostel->location); ?></li>
                                </ul>
                                <ul class="nav nav-divider mb-2 mb-sm-3">
                                    <li class="nav-item"><i class="fa fa-walking text-primary"></i>&nbsp;<?php echo e($hostel->distance); ?></li>
                                </ul>
                                <ul class="nav nav-divider mb-2 mb-sm-3">
                                    <li class="nav-item"><i class="fa fa-male text-primary"></i>&nbsp; <?php echo e($hostel->maleRooms()); ?></li>
                                    <li class="nav-item"><i class="fa fa-female text-primary"></i>&nbsp; <?php echo e($hostel->femaleRooms()); ?></li>
                                    <li class="nav-item"><i class="fa fa-genderless text-primary"></i>&nbsp; <?php echo e($hostel->unisexRooms()); ?></li>
                                </ul>
                                <ul class="nav nav-divider mb-2 mb-sm-3">
                                    <li class="nav-item"><i class="fa fa-cash-register text-primary"></i>&nbsp;GHS<?php echo e(number_format($hostel->starting_price, 2)); ?> starting price</li>
                                </ul>
                            </div>

                            <div class="card-footer pt-0">
                                <div class="d-sm-flex justify-content-sm-between align-items-center">
                                    <div class="mt-2 mt-sm-0 w-100">
                                        <a href="<?php echo e(route('hostel.detail', $hostel->slug)); ?>" class="btn btn-sm btn-primary mb-0 w-100">View Detail<i class="bi bi-arrow-right ms-2"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-12">
                        <div class="card p-5 text-center">
                            <h5>No hostels found</h5>
                            <p class="mb-3">Try adjusting your search or filters to find a hostel.</p>
                            <div>
                                <button type="button" class="btn btn-primary-soft" wire:click="resetFilters">Clear Filters</button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="col-12">
                    <?php echo e($hostels->links()); ?>

                </div>

            </div>
        </div>
    </section>
</div>
<?php /**PATH C:\wamp64\www\studentroombookings\resources\views/livewire/home-component.blade.php ENDPATH**/ ?>