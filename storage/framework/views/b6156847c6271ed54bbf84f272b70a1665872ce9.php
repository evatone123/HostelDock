<?php $__env->startSection('title', 'Hostel Detail'); ?>

<?php $__env->startSection('content'); ?>
    <!-- Page title -->
    <section class="bg-primary py-3">
        <div class="container">
            <nav class="d-flex mb-1" aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-dots mb-0">
                    <li class="breadcrumb-item"><a class="text-white" href="<?php echo e(route('home')); ?>">Home</a></li>
                    <li class="breadcrumb-item"><a class="text-white" href="<?php echo e(route('home')); ?>#hotel-view">Hostels</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page"><?php echo e(\Illuminate\Support\Str::limit($hostel->displayName(), 50)); ?></li>
                </ol>
            </nav>
            <h1 class="text-white mb-0"><?php echo e($hostel->displayName()); ?></h1>
        </div>
    </section>

    <!-- Hostel detail START -->
    <section class="pt-4 pt-lg-5">
        <div class="container">
            <div class="row g-4 g-xl-5">
                <!-- Main content -->
                <div class="col-xl-8">

                    <!-- Gallery START -->
                    <?php
                        $images = $hostel->galleryImages();
                        $main = $images[0];
                        $rest = array_slice($images, 1, 4);
                        $galleryGroup = 'hostel-gallery-' . $hostel->id;
                        $totalPhotos = count($images);
                    ?>
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="mb-0"><i class="bi fa-fw bi-images text-primary me-2"></i>Gallery</h5>
                            <?php if($totalPhotos): ?>
                                <span class="badge bg-primary-soft text-primary"><?php echo e($totalPhotos); ?> Photos</span>
                            <?php endif; ?>
                        </div>

                        <div class="row g-2">
                            <div class="col-md-8">
                                <a class="position-relative d-block w-100 h-100 glightbox gallery-item" href="<?php echo e(asset($main)); ?>"
                                    data-gallery="<?php echo e($galleryGroup); ?>" data-glightbox="title: <?php echo e($hostel->displayName()); ?>"
                                    title="View all photos">
                                    <img src="<?php echo e(asset($main)); ?>" class="rounded-2 w-100 h-300px object-fit-cover" alt="<?php echo e($hostel->name); ?>">
                                    <span class="gallery-overlay rounded-2">
                                        <span class="gallery-overlay-inner">
                                            <i class="bi bi-zoom-in fs-4"></i>
                                            <span class="fw-semibold">View all photos</span>
                                        </span>
                                    </span>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <?php $__empty_1 = true; $__currentLoopData = $rest; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <a class="position-relative d-block w-100 glightbox gallery-item" href="<?php echo e(asset($image)); ?>"
                                        data-gallery="<?php echo e($galleryGroup); ?>" data-glightbox="title: <?php echo e($hostel->displayName()); ?>"
                                        title="View photo">
                                        <img src="<?php echo e(asset($image)); ?>" class="rounded-2 w-100 mb-2 object-fit-cover" style="height: 145px;" alt="<?php echo e($hostel->name); ?>">
                                        <span class="gallery-overlay rounded-2">
                                            <span class="gallery-overlay-inner"><i class="bi bi-zoom-in"></i></span>
                                        </span>
                                    </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <a class="position-relative d-block w-100 h-100 glightbox gallery-item" href="<?php echo e(hd_asset('assets/images/about/slider-imgs.jpg')); ?>"
                                        data-gallery="<?php echo e($galleryGroup); ?>" data-glightbox="title: <?php echo e($hostel->displayName()); ?>">
                                        <img src="<?php echo e(hd_asset('assets/images/about/slider-imgs.jpg')); ?>" class="rounded-2 w-100 h-300px object-fit-cover" alt="<?php echo e($hostel->name); ?>">
                                        <span class="gallery-overlay rounded-2">
                                            <span class="gallery-overlay-inner"><i class="bi bi-zoom-in"></i></span>
                                        </span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <!-- Gallery END -->

                    <!-- Overview START -->
                    <div class="card shadow rounded-3 p-4 mb-4">
                        <div class="d-sm-flex justify-content-sm-between mb-3">
                            <div>
                                <span class="badge bg-dark text-white me-1"><i class="bi fa-fw bi-star-fill me-1 text-warning"></i> <?php echo e(number_format($hostel->rating, 1)); ?></span>
                                <span class="badge bg-dark text-white"><i class="bi fa-fw bi-door-open me-1"></i> <?php echo e($hostel->availableRooms()); ?> Beds Available</span>
                            </div>
                            <p class="text-primary mb-0 mt-2 mt-sm-0 fw-bold">From GHS<?php echo e(number_format($hostel->starting_price, 2)); ?></p>
                        </div>

                        <h5 class="mb-3">Overview</h5>
                        <p class="mb-0"><?php echo e($hostel->description); ?></p>

                        <hr class="my-4">

                        <div class="row g-3">
                            <div class="col-sm-6 col-lg-3">
                                <div class="d-flex">
                                    <i class="fa fa-location-dot text-primary fs-4 me-2"></i>
                                    <div>
                                        <h6 class="mb-0">Location</h6>
                                        <small class="mb-0"><?php echo e($hostel->location); ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="d-flex">
                                    <i class="fa fa-walking text-primary fs-4 me-2"></i>
                                    <div>
                                        <h6 class="mb-0">Distance</h6>
                                        <small class="mb-0"><?php echo e($hostel->distance); ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="d-flex">
                                    <i class="fa fa-graduation-cap text-primary fs-4 me-2"></i>
                                    <div>
                                        <h6 class="mb-0">Institution</h6>
                                        <small class="mb-0"><?php echo e($hostel->institution_name); ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="d-flex">
                                    <i class="fa fa-genderless text-primary fs-4 me-2"></i>
                                    <div>
                                        <h6 class="mb-0">Beds (M/F/U)</h6>
                                        <small class="mb-0"><?php echo e($hostel->maleRooms()); ?> / <?php echo e($hostel->femaleRooms()); ?> / <?php echo e($hostel->unisexRooms()); ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Overview END -->

                    <!-- Location & Directions START -->
                    <?php
                        $campus = $hostel->campusCoordinates();
                        $mapsKey = trim((string) config('services.google.maps_api_key'));
                        $useJsMap = $mapsKey !== '' && $hostel->hasCoordinates();
                        $hostelQuery = $hostel->hasCoordinates()
                            ? $hostel->latitude . ',' . $hostel->longitude
                            : ($hostel->location ?: 'Kumasi, Ghana');
                        $campusQuery = ($campus && $campus['lat'] !== null)
                            ? $campus['lat'] . ',' . $campus['lng']
                            : ($campus['name'] ?? 'campus');
                    ?>
                    <div class="card shadow rounded-3 p-4 mb-4">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                            <h5 class="mb-0"><i class="bi bi-geo-alt text-primary me-2"></i>Location &amp; Directions</h5>
                            <span class="badge bg-primary-soft text-primary"><?php echo e($hostel->location); ?></span>
                        </div>

                        <?php if($useJsMap): ?>
                            <div id="hostel-map" class="hostel-map rounded-2 mb-3"
                                data-hostel-lat="<?php echo e($hostel->latitude); ?>"
                                data-hostel-lng="<?php echo e($hostel->longitude); ?>"
                                data-hostel-name="<?php echo e($hostel->displayName()); ?>"
                                data-campus-lat="<?php echo e($campus['lat'] ?? ''); ?>"
                                data-campus-lng="<?php echo e($campus['lng'] ?? ''); ?>"
                                data-campus-name="<?php echo e($campus['name'] ?? ''); ?>">
                                <div class="hostel-map-loading"><span class="spinner-border text-primary"></span></div>
                            </div>

                            <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                                <div class="btn-group" role="group" aria-label="Travel mode">
                                    <button type="button" class="btn btn-sm btn-primary hostel-map-mode" data-map-mode="walking"><i class="bi bi-person-walking me-1"></i>Walk</button>
                                    <button type="button" class="btn btn-sm btn-outline-primary hostel-map-mode" data-map-mode="driving"><i class="bi bi-car-front me-1"></i>Drive</button>
                                </div>
                                <button type="button" class="btn btn-sm btn-primary-soft" id="hostel-map-mylocation"><i class="bi bi-crosshair me-1"></i>Directions from my location</button>
                                <span class="ms-auto small text-muted" id="hostel-map-info"></span>
                            </div>
                        <?php else: ?>
                            <div class="hostel-map rounded-2 mb-3 overflow-hidden">
                                <?php if($hostel->hasCoordinates()): ?>
                                    <iframe src="<?php echo e('https://maps.google.com/maps?q=' . $hostel->latitude . ',' . $hostel->longitude . '&z=15&output=embed'); ?>"
                                        width="100%" height="100%" style="border:0" loading="lazy" allowfullscreen
                                        title="Map showing <?php echo e($hostel->displayName()); ?>"></iframe>
                                <?php else: ?>
                                    <div class="bg-light d-flex align-items-center justify-content-center text-center h-100">
                                        <div class="p-4">
                                            <i class="bi bi-geo-alt fs-3 d-block text-primary mb-2"></i>
                                            <small class="text-muted mb-0">Map preview unavailable for this hostel.</small>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <p class="small text-muted mb-0">
                            <i class="bi bi-info-circle me-1"></i>Need directions to this hostel?
                            <a href="<?php echo e('https://www.google.com/maps/dir/?api=1&origin=' . urlencode($campusQuery) . '&destination=' . urlencode($hostelQuery) . '&travelmode=walking'); ?>" target="_blank" rel="noopener">Walk</a> /
                            <a href="<?php echo e('https://www.google.com/maps/dir/?api=1&origin=' . urlencode($campusQuery) . '&destination=' . urlencode($hostelQuery) . '&travelmode=driving'); ?>" target="_blank" rel="noopener">Drive</a>
                            there on Google Maps.
                            <?php if(!$useJsMap && $mapsKey === ''): ?>
                                <br><em class="text-warning">Tip: set a <code>GOOGLE_MAPS_API_KEY</code> in your <code>.env</code> to enable the live route map here.</em>
                            <?php endif; ?>
                        </p>
                    </div>
                    <!-- Location & Directions END -->

                    <!-- Rates START -->
                    <div id="rates" class="card shadow rounded-3 p-4 mb-4">
                        <h5 class="mb-4">Room Rates</h5>

                        <?php $__empty_1 = true; $__currentLoopData = $roomsByType; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $rooms): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="mb-4">
                                <h6 class="mb-3 d-flex align-items-center">
                                    <i class="bi bi-building me-2 text-primary"></i><?php echo e($type); ?>

                                </h6>

                                <div class="table-responsive">
                                    <table class="table table-borderless table-striped table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th>Room</th>
                                                <th>Gender</th>
                                                <th>Amenities</th>
                                                <th>Available</th>
                                                <th>Price</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td class="fw-semibold"><?php echo e($room->name); ?></td>
                                                    <td>
                                                        <?php if($room->gender_category === 'Male'): ?>
                                                            <i class="fa fa-male text-primary me-1"></i> Male
                                                        <?php elseif($room->gender_category === 'Female'): ?>
                                                            <i class="fa fa-female text-primary me-1"></i> Female
                                                        <?php else: ?>
                                                            <i class="fa fa-genderless text-primary me-1"></i> Unisex
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if($room->amenities->isNotEmpty()): ?>
                                                            <div class="d-flex flex-wrap gap-1" style="max-width: 280px;">
                                                                <?php $__currentLoopData = $room->amenities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $amenity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <span class="badge bg-light text-body border small">
                                                                        <i class="bi <?php echo e($amenity->icon ?: 'bi-check2-circle'); ?> me-1 text-primary"></i><?php echo e($amenity->name); ?>

                                                                    </span>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </div>
                                                        <?php else: ?>
                                                            <span class="text-muted small">—</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                            $space = $room->availableSpace();
                                                        ?>
                                                        <?php if($space > 0): ?>
                                                            <span class="text-success"><?php echo e($space); ?> left</span>
                                                        <?php else: ?>
                                                            <span class="text-danger">Full</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="fw-bold">GHS<?php echo e(number_format($room->price, 2)); ?></td>
                                                    <td class="text-end">
                                                        <?php if($space > 0): ?>
                                                            <a href="<?php echo e(route('booking.create', ['hostel' => $hostel->slug, 'room' => $room->id])); ?>"
                                                                class="btn btn-sm btn-primary mb-0">Book Now</a>
                                                        <?php else: ?>
                                                            <button class="btn btn-sm btn-light mb-0" disabled>Sold Out</button>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="alert alert-warning mb-0">No rooms currently available at this hostel.</div>
                        <?php endif; ?>
                    </div>
                    <!-- Rates END -->

                    <!-- Amenities START -->
                    <div class="card shadow rounded-3 p-4 mb-4">
                        <h5 class="mb-4">What this hostel offers</h5>
                        <?php
                            $roomAmenities = $hostel->rooms
                                ->filter(function ($r) {
                                    return $r->is_active;
                                })
                                ->flatMap(function ($r) {
                                    return $r->amenities;
                                })
                                ->unique('id')
                                ->values();

                            $fallbackAmenities = [
                                ['icon' => 'bi-shield-check', 'name' => '24/7 Security'],
                                ['icon' => 'bi-droplet-half', 'name' => 'Constant Water Supply'],
                                ['icon' => 'bi-lightning-charge', 'name' => 'Reliable Electricity'],
                                ['icon' => 'bi-wifi', 'name' => 'Internet Connectivity'],
                                ['icon' => 'bi-house-door', 'name' => 'Furnished Rooms'],
                                ['icon' => 'bi-bounding-box', 'name' => 'Spacious Rooms'],
                                ['icon' => 'bi-cup-hot', 'name' => 'Water Dispensers'],
                                ['icon' => 'bi-parking', 'name' => 'Car Parking'],
                            ];

                            $shownAmenities = $roomAmenities->isNotEmpty()
                                ? $roomAmenities
                                : collect($fallbackAmenities)->map(function ($item) {
                                    return (object) $item;
                                });
                        ?>
                        <div class="row g-3">
                            <?php $__currentLoopData = $shownAmenities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-sm-6 col-lg-3">
                                    <div class="bg-mode rounded-2 p-3 text-center">
                                        <i class="bi <?php echo e($item->icon ?? 'bi-check2-circle'); ?> text-primary fs-3"></i>
                                        <h6 class="mt-2 mb-0"><?php echo e($item->name); ?></h6>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <!-- Amenities END -->
                </div>
                <!-- Main content END -->

                <!-- Sidebar START -->
                <aside class="col-xl-4">
                    <div class="card card-body shadow rounded-3 p-4 mb-4">
                        <h5 class="mb-3">Quick Booking</h5>
                        <p class="small text-muted mb-3">Pick a category, then choose a room at <?php echo e($hostel->displayName()); ?>.</p>

                        <form method="GET" action="<?php echo e(route('booking.create', $hostel->slug)); ?>">
                            <div class="mb-3">
                                <label class="form-label" for="quick_room_type">Room Category</label>
                                <select class="form-select" id="quick_room_type" name="room_type" data-quick-type-filter>
                                    <option value="">All room categories</option>
                                    <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($type); ?>"><?php echo e($type); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="quick_room">Room</label>
                                <select class="form-select" id="quick_room" name="room" required data-quick-room-select>
                                    <option value="">Choose a room...</option>
                                    <?php $__currentLoopData = $roomsByType; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $rooms): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php ($space = $room->availableSpace()); ?>
                                            <option value="<?php echo e($room->id); ?>" data-room-type="<?php echo e($type); ?>" <?php if($space <= 0): ?> disabled <?php endif; ?>>
                                                <?php echo e($room->name); ?> &middot; <?php echo e($room->gender_category); ?> &middot;
                                                GHS<?php echo e(number_format($room->price, 2)); ?> &middot;
                                                <?php if($space > 0): ?>
                                                    <?php echo e($space); ?> bed<?php echo e($space === 1 ? '' : 's'); ?> left
                                                <?php else: ?>
                                                    Sold out
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <small class="text-muted d-none" data-quick-empty>
                                    Every room in this category is fully booked. Try another category.
                                </small>
                            </div>

                            <button type="submit" class="btn btn-primary mb-0 w-100"><i class="bi bi-bookmark-check me-2"></i>Continue to Book</button>
                        </form>
                    </div>

                    <div class="card shadow rounded-3 p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar">
                                <img class="avatar-img rounded-circle" src="<?php echo e(hd_asset('assets/images/logo2.png')); ?>" alt="HostelDock">
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0">HostelDock</h6>
                                <small class="text-muted">Verified Hostel Partner</small>
                            </div>
                        </div>
                        <p class="small mb-3">Safe and secure booking with trusted hostels. Pay directly to the hostel or contact us for help.</p>
                        <div class="d-grid gap-2">
                            <a href="<?php echo e(route('contact')); ?>" class="btn btn-primary-soft mb-0"><i class="bi bi-headset me-2"></i>Contact Us</a>
                            <a href="<?php echo e(route('faq')); ?>" class="btn btn-light mb-0"><i class="bi bi-info-circle me-2"></i>Help Center</a>
                        </div>
                    </div>
                </aside>
                <!-- Sidebar END -->
            </div>

            <?php if($similar->isNotEmpty()): ?>
                <!-- Similar hostels -->
                <div class="row mt-5">
                    <div class="col-12">
                        <h5 class="mb-4">Similar Hostels</h5>
                    </div>
                    <?php $__currentLoopData = $similar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hostelItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-6 col-xl-4">
                            <div class="card shadow p-2 pb-0 h-100">
                                <div class="rounded-2 overflow-hidden">
                                    <img src="<?php echo e(asset($hostelItem->coverImagePath())); ?>"
                                        class="w-100 object-fit-cover" style="height: 200px;" alt="<?php echo e($hostelItem->name); ?>">
                                </div>
                                <div class="card-body px-3 pb-0">
                                    <h5 class="card-title"><a href="<?php echo e(route('hostel.detail', $hostelItem->slug)); ?>" class="stretched-link"><?php echo e($hostelItem->displayName()); ?></a></h5>
                                    <ul class="nav nav-divider mb-2">
                                        <li class="nav-item"><i class="fa fa-location-dot text-primary"></i><?php echo e($hostelItem->location); ?></li>
                                    </ul>
                                    <ul class="nav nav-divider mb-2">
                                        <li class="nav-item"><i class="fa fa-cash-register text-primary"></i>&nbsp;GHS<?php echo e(number_format($hostelItem->starting_price, 2)); ?> starting price</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <!-- Hostel detail END -->
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <?php if(isset($useJsMap) && $useJsMap): ?>
        <script>
            window.initHostelMap = (function () {
                const mapEl = document.getElementById('hostel-map');
                if (!mapEl) return;

                const hostelPos = { lat: parseFloat(mapEl.dataset.hostelLat), lng: parseFloat(mapEl.dataset.hostelLng) };
                const hasCampus = mapEl.dataset.campusLat !== '' && mapEl.dataset.campusLng !== '';
                const campusPos = hasCampus ? { lat: parseFloat(mapEl.dataset.campusLat), lng: parseFloat(mapEl.dataset.campusLng) } : null;

                let map = null;
                let renderer = null;
                let mode = 'walking';
                let userPos = null;
                const infoEl = document.getElementById('hostel-map-info');

                const campusIcon = {
                    url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(
                        '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="#7c3aed" stroke="#fff" stroke-width="1.5" d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>'
                    ),
                    scaledSize: new google.maps.Size(22, 22),
                    anchor: new google.maps.Point(11, 22),
                };

                function setInfo(text) {
                    if (infoEl) infoEl.textContent = text || '';
                }

                function fitBounds() {
                    const bounds = new google.maps.LatLngBounds().extend(hostelPos);
                    if (campusPos) bounds.extend(campusPos);
                    map.fitBounds(bounds);
                    if (map.getZoom() > 16) map.setZoom(16);
                }

                function drawRoute() {
                    if (!renderer) return;
                    const service = new google.maps.DirectionsService();
                    const origin = userPos || campusPos || hostelPos;

                    if (!userPos && !campusPos) {
                        setInfo('Showing hostel location only.');
                        return;
                    }

                    setInfo('Calculating route...');
                    service.route({
                        origin: origin,
                        destination: hostelPos,
                        travelMode: mode === 'walking'
                            ? google.maps.TravelMode.WALKING
                            : google.maps.TravelMode.DRIVING,
                    }, (result, status) => {
                        if (status === google.maps.DirectionsStatus.OK) {
                            renderer.setDirections(result);
                            const leg = result.routes[0].legs[0];
                            const from = userPos ? 'your location' : (mapEl.dataset.campusName || 'campus');
                            setInfo(from + ' &rarr; hostel: ' + leg.distance.text + ', approx ' + leg.duration.text);
                        } else {
                            renderer.setDirections({ routes: [] });
                            fitBounds();
                            setInfo('Route unavailable (' + status + ').');
                        }
                    });
                }

                function bindControls() {
                    document.querySelectorAll('.hostel-map-mode').forEach(btn => {
                        btn.addEventListener('click', () => {
                            mode = btn.dataset.mapMode;
                            document.querySelectorAll('.hostel-map-mode').forEach(b => {
                                b.classList.toggle('btn-primary', b === btn);
                                b.classList.toggle('btn-outline-primary', b !== btn);
                            });
                            drawRoute();
                        });
                    });

                    const myLocBtn = document.getElementById('hostel-map-mylocation');
                    if (myLocBtn) {
                        myLocBtn.addEventListener('click', () => {
                            if (!navigator.geolocation) {
                                setInfo('Geolocation is not supported by your browser.');
                                return;
                            }
                            setInfo('Locating you...');
                            navigator.geolocation.getCurrentPosition((pos) => {
                                userPos = { lat: pos.coords.latitude, lng: pos.coords.longitude };
                                setInfo('Located. Drawing route...');
                                drawRoute();
                            }, () => {
                                setInfo('Could not determine your location.');
                            }, { timeout: 8000, maximumAge: 60000 });
                        });
                    }
                }

                return function initMap() {
                    window.__hostelMapReady = true;
                    mapEl.querySelector('.hostel-map-loading')?.remove();
                    map = new google.maps.Map(mapEl, { zoom: 15, center: hostelPos, streetViewControl: false, mapTypeControl: false });
                    renderer = new google.maps.DirectionsRenderer({ map: map, suppressMarkers: true });

                    new google.maps.Marker({ position: hostelPos, map: map, title: mapEl.dataset.hostelName });
                    if (campusPos) {
                        new google.maps.Marker({
                            position: campusPos, map: map, title: mapEl.dataset.campusName || 'Campus', icon: campusIcon,
                        });
                    }

                    fitBounds();
                    drawRoute();
                    bindControls();
                };
            })();

            (function () {
                window.__hostelMapReady = false;
                const fallbackToEmbed = function () {
                    if (window.__hostelMapReady) return;
                    const el = document.getElementById('hostel-map');
                    if (!el) return;
                    el.querySelector('.hostel-map-loading')?.remove();
                    el.innerHTML = '<iframe src="https://maps.google.com/maps?q=' + el.dataset.hostelLat + ',' + el.dataset.hostelLng + '&z=15&output=embed" width="100%" height="100%" style="border:0" loading="lazy" allowfullscreen></iframe>';
                    document.getElementById('hostel-map-info')?.remove();
                };
                const src = 'https://maps.googleapis.com/maps/api/js?key=<?php echo e($mapsKey); ?>&callback=initHostelMap';
                if (window.google && window.google.maps) {
                    if (typeof window.initHostelMap === 'function') window.initHostelMap();
                    return;
                }
                const script = document.createElement('script');
                script.src = src;
                script.async = true;
                script.defer = true;
                script.onerror = fallbackToEmbed;
                document.head.appendChild(script);
                setTimeout(fallbackToEmbed, 9000);
            })();
        </script>
    <?php endif; ?>

    <script>
        (function () {
            const typeFilter = document.querySelector('[data-quick-type-filter]');
            const roomSelect = document.querySelector('[data-quick-room-select]');
            const emptyNote = document.querySelector('[data-quick-empty]');

            if (!typeFilter || !roomSelect) return;

            const allOptions = Array.from(roomSelect.querySelectorAll('option[data-room-type]'));
            if (!allOptions.length) return;

            function applyTypeFilter(type) {
                // Options are detached and re-attached rather than hidden: `hidden`
                // on an <option> is not honoured by every browser, while moving
                // the node itself always removes it from the dropdown.
                allOptions.forEach(option => {
                    const show = !type || option.dataset.roomType === type;

                    if (show) {
                        if (option.parentNode !== roomSelect) roomSelect.appendChild(option);
                    } else if (option.parentNode === roomSelect) {
                        option.remove();
                    }
                });

                // A room picked under a different category must not survive the
                // filter, or the form submits a room outside the chosen category.
                roomSelect.value = '';

                const selectable = Array.from(roomSelect.querySelectorAll('option[data-room-type]'))
                    .filter(option => !option.disabled)
                    .length;

                if (emptyNote) {
                    emptyNote.classList.toggle('d-none', selectable > 0);
                }
            }

            typeFilter.addEventListener('change', () => applyTypeFilter(typeFilter.value));
        })();
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\studentroombookings\resources\views/pages/hostel-detail.blade.php ENDPATH**/ ?>