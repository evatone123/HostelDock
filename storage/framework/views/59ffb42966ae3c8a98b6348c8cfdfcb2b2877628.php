<!-- Footer START -->
<footer class="footer-site pt-5">
    <div class="container">
        <div class="row g-4 border-0 rounded-4">
            <div class="col-12 text-center mb-2">
                <h2 class="mc-footer-headline h2 mb-0">Find a room that feels like home.</h2>
                <p class="mt-2 mb-0" style="max-width: 520px; margin-inline: auto;">Verified hostels, honest prices, and one simple booking journey from campus search to keys.</p>
            </div>
        </div>

        <div class="row g-4 pt-3">
            <div class="col-lg-4">
                <a href="<?php echo e(url('/')); ?>">
                    <img class="h-40px" src="<?php echo e(hd_asset('assets/images/logo.png')); ?>" alt="HostelDock" style="filter: brightness(0) invert(1);">
                </a>
                <p class="my-3" style="opacity: 0.75;">We are located in Tarkwa &amp; Takoradi</p>
                <p class="mb-2">
                    <a href="tel:+233503968424" class="text-decoration-none"><i class="bi bi-telephone me-2"></i>+233 (50) 396 8424</a>
                </p>
                <p class="mb-2">
                    <a href="tel:+233547668839" class="text-decoration-none"><i class="bi bi-telephone me-2"></i>+233 (54) 766 8839</a>
                </p>
                <p class="mb-0">
                    <a href="mailto:info@hosteldock.com" class="text-decoration-none"><i class="bi bi-envelope me-2"></i>info@hosteldock.com</a>
                </p>
            </div>

            <div class="col-lg-8">
                <div class="row g-4">
                    <div class="col-6 col-md-4">
                        <h6 class="mb-3">Pages</h6>
                        <ul class="nav flex-column">
                            <li class="nav-item mb-1"><a class="nav-link p-0" href="<?php echo e(url('/')); ?>">Home</a></li>
                            <li class="nav-item mb-1"><a class="nav-link p-0" href="<?php echo e(route('about')); ?>">About Us</a></li>
                            <li class="nav-item mb-1"><a class="nav-link p-0" href="<?php echo e(route('contact')); ?>">Contact Us</a></li>
                            <li class="nav-item mb-1"><a class="nav-link p-0" href="<?php echo e(route('faq')); ?>">Help / FAQ's</a></li>
                        </ul>
                    </div>

                    <div class="col-6 col-md-4">
                        <h6 class="mb-3">Account</h6>
                        <ul class="nav flex-column">
                            <?php if(auth()->guard()->guest()): ?>
                                <li class="nav-item mb-1"><a class="nav-link p-0" href="<?php echo e(route('login')); ?>">Sign In</a></li>
                                <li class="nav-item mb-1"><a class="nav-link p-0" href="<?php echo e(route('register')); ?>">Register</a></li>
                            <?php else: ?>
                                <li class="nav-item mb-1"><a class="nav-link p-0" href="<?php echo e(route('profile')); ?>">My Profile</a></li>
                                <li class="nav-item mb-1"><a class="nav-link p-0" href="<?php echo e(route('my-bookings')); ?>">My Bookings</a></li>
                            <?php endif; ?>
                            <li class="nav-item mb-1"><a class="nav-link p-0" href="<?php echo e(route('privacy')); ?>">Privacy Policy</a></li>
                            <li class="nav-item mb-1"><a class="nav-link p-0" href="<?php echo e(route('terms')); ?>">Terms &amp; Condition</a></li>
                            <li class="nav-item mb-1"><a class="nav-link p-0" href="<?php echo e(route('contact')); ?>">Support</a></li>
                        </ul>
                    </div>

                    <div class="col-6 col-md-4">
                        <h6 class="mb-3">Our Offices</h6>
                        <ul class="nav flex-column">
                            <li class="nav-item mb-3">
                                <h6 class="mb-0">Tarkwa Office</h6>
                                <p class="mb-0" style="opacity: 0.75;">Tamso Estate Junction, Tarkwa, Western Region, Ghana</p>
                            </li>
                            <li class="nav-item">
                                <h6 class="mb-0">Takoradi Office</h6>
                                <p class="mb-0" style="opacity: 0.75;">Near Takoradi Technical University, Takoradi, Western Region, Ghana</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 justify-content-between mt-2">
            <div class="col-sm-7 col-md-6 col-lg-4"></div>

            <div class="col-sm-5 col-md-6 col-lg-3 text-sm-end">
                
            </div>
        </div>

        <hr class="mt-4 mb-0">
        <div class="row">
            <div class="d-lg-flex justify-content-between align-items-center py-3 text-center text-lg-start">
                <div class="text-primary-hover" style="opacity: 0.75;">
                    &copy; 2026 HostelDock by <a href="https://www.evatechsolutionsgh.com/" target="_blank" class="text-decoration-none mc-footer-credit">Evatech Solutions.</a> All rights reserved.
                </div>
                <ul class="list-inline mb-0">
                    <li class="list-inline-item me-0"><a class="nav-link py-1 p-0" href="<?php echo e(route('privacy')); ?>">Privacy policy</a></li>
                    <li class="list-inline-item me-0"><a class="nav-link py-1 p-0" href="<?php echo e(route('terms')); ?>">Terms and conditions</a></li>
                    <li class="list-inline-item me-0"><a class="nav-link py-1 p-0 pe-0" href="<?php echo e(route('residency')); ?>">Residency policy</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>
<!-- Footer END --><?php /**PATH C:\wamp64\www\studentroombookings\resources\views/partials/footer.blade.php ENDPATH**/ ?>