<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <title><?php echo $__env->yieldContent('title', 'Admin'); ?> &middot; HostelDock</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link rel="icon" type="image/png" href="<?php echo e(asset('assets/images/favicon.png')); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Sofia+Sans:wght@300;400;450;500;600;700&amp;family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;family=Inter:wght@400;500;600;700&amp;family=Fira+Code:wght@400;500&amp;display=swap">
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/font-awesome/css/all.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/bootstrap-icons/bootstrap-icons.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/style.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/docuforge-design.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/mastercard-design.css')); ?>">
    <script src="<?php echo e(asset('assets/js/theme.js')); ?>"></script>
    <?php echo $__env->yieldPushContent('styles'); ?>
    <style>
        .sidebar-docs {
            transition: width .25s ease, transform .3s ease, box-shadow .3s ease;
        }

        /* The wordmark is far wider than the 86px collapsed rail, so the square
           mark is swapped in there. Both stay in the DOM and are toggled by CSS
           because an <img> src cannot be changed by CSS. */
        .sidebar-logo-collapsed {
            display: none;
        }

        @media (min-width: 992px) {
            .sidebar-docs {
                width: 280px;
                flex-shrink: 0;
            }

            .is-sidebar-collapsed .sidebar-docs {
                width: 86px;
            }

            .is-sidebar-collapsed .sidebar-docs .navbar-brand {
                justify-content: center;
                padding: 0;
            }

            .is-sidebar-collapsed .sidebar-logo-expanded {
                display: none;
            }

            .is-sidebar-collapsed .sidebar-logo-collapsed {
                display: block;
            }

            .is-sidebar-collapsed .sidebar-docs .nav .nav-link,
            .is-sidebar-collapsed .sidebar-docs .sidebar-bottom .nav-link {
                justify-content: center;
                gap: 0;
                padding-left: 0;
                padding-right: 0;
            }

            .is-sidebar-collapsed .sidebar-label,
            .is-sidebar-collapsed .sidebar-group-label {
                display: none;
            }

            .sidebar-backdrop {
                display: none;
            }
        }

        @media (max-width: 991.98px) {
            .sidebar-docs {
                position: fixed;
                top: 0;
                bottom: 0;
                left: 0;
                z-index: 1050;
                width: 280px;
                transform: translateX(-100%);
            }

            body.sidebar-open .sidebar-docs {
                transform: translateX(0);
                box-shadow: 0 0 0 1px rgba(0, 0, 0, .05), 0 20px 50px rgba(15, 23, 42, .22);
            }

            .sidebar-backdrop {
                position: fixed;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                z-index: 1045;
                background: rgba(15, 23, 42, .55);
                opacity: 0;
                visibility: hidden;
                transition: opacity .25s ease, visibility .25s ease;
            }

            body.sidebar-open .sidebar-backdrop {
                opacity: 1;
                visibility: visible;
            }
        }
    </style>
</head>
<body class="bg-light d-flex vh-100 overflow-hidden p-0">
    <!-- Sidebar -->
    <aside class="sidebar-docs bg-body d-flex flex-column border-end" id="adminSidebar" aria-label="Sidebar navigation">
        <?php
            $isAdmin = auth()->user()->isAdmin();
            $dashRoute = $isAdmin ? 'admin.dashboard' : 'manager.dashboard';
        ?>
        <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
            <a href="<?php echo e(route($dashRoute)); ?>" class="navbar-brand flex-grow-1 d-flex align-items-center gap-2">
                <img class="h-40px sidebar-logo-expanded" src="<?php echo e(hd_asset('assets/images/logo.png')); ?>" alt="HostelDock">
                <img class="h-40px sidebar-logo-collapsed" src="<?php echo e(hd_asset('assets/images/logo2.png')); ?>" alt="HostelDock">
            </a>
            <button type="button" class="btn btn-sm btn-light d-lg-none" data-sidebar-toggle aria-label="Close menu" title="Close menu">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <nav class="nav flex-column flex-grow-1 p-3" style="overflow-y: auto;">
            <p class="small text-muted text-uppercase fw-bold mb-2 px-2 sidebar-group-label">Menu</p>
            <a class="nav-link d-flex align-items-center gap-2 rounded-2 mb-1 px-3 py-2 <?php echo e(request()->routeIs('admin.dashboard', 'manager.dashboard') ? 'bg-primary text-white' : 'text-body'); ?>"
                href="<?php echo e(route($dashRoute)); ?>"><i class="bi bi-speedometer2"></i><span class="sidebar-label"><?php echo e($isAdmin ? 'Dashboard' : 'My Dashboard'); ?></span></a>

            <p class="small text-muted text-uppercase fw-bold mb-2 px-2 mt-3 sidebar-group-label">Hostels</p>
            <a class="nav-link d-flex align-items-center gap-2 rounded-2 mb-1 px-3 py-2 <?php echo e(request()->routeIs('admin.hostels.*', 'manager.hostels.*') ? 'bg-primary text-white' : 'text-body'); ?>"
                href="<?php echo e(route($isAdmin ? 'admin.hostels.index' : 'manager.hostels.index')); ?>"><i class="bi bi-building"></i><span class="sidebar-label"><?php echo e($isAdmin ? 'Hostels' : 'My Hostels'); ?></span></a>
            <a class="nav-link d-flex align-items-center gap-2 rounded-2 mb-1 px-3 py-2 <?php echo e(request()->routeIs('admin.bookings.*', 'manager.bookings.*') ? 'bg-primary text-white' : 'text-body'); ?>"
                href="<?php echo e(route($isAdmin ? 'admin.bookings.index' : 'manager.bookings.index')); ?>"><i class="bi bi-bookmark-check"></i><span class="sidebar-label">Bookings</span></a>

            <?php if($isAdmin): ?>
                <p class="small text-muted text-uppercase fw-bold mb-2 px-2 mt-3 sidebar-group-label">Platform</p>
                <a class="nav-link d-flex align-items-center gap-2 rounded-2 mb-1 px-3 py-2 <?php echo e(request()->routeIs('admin.tokens.*') ? 'bg-primary text-white' : 'text-body'); ?>"
                    href="<?php echo e(route('admin.tokens.index')); ?>"><i class="bi bi-ticket-perforated"></i><span class="sidebar-label">Unlock Access Tokens</span></a>
                <a class="nav-link d-flex align-items-center gap-2 rounded-2 mb-1 px-3 py-2 <?php echo e(request()->routeIs('admin.campuses.*') ? 'bg-primary text-white' : 'text-body'); ?>"
                    href="<?php echo e(route('admin.campuses.index')); ?>"><i class="bi bi-geo-alt"></i><span class="sidebar-label">Campuses</span></a>
                <a class="nav-link d-flex align-items-center gap-2 rounded-2 mb-1 px-3 py-2 <?php echo e(request()->routeIs('admin.amenities.*') ? 'bg-primary text-white' : 'text-body'); ?>"
                    href="<?php echo e(route('admin.amenities.index')); ?>"><i class="bi bi-stars"></i><span class="sidebar-label">Amenities</span></a>
                <a class="nav-link d-flex align-items-center gap-2 rounded-2 mb-1 px-3 py-2 <?php echo e(request()->routeIs('admin.room-categories.*') ? 'bg-primary text-white' : 'text-body'); ?>"
                    href="<?php echo e(route('admin.room-categories.index')); ?>"><i class="bi bi-door-closed"></i><span class="sidebar-label">Room Categories</span></a>
                <a class="nav-link d-flex align-items-center gap-2 rounded-2 mb-1 px-3 py-2 <?php echo e(request()->routeIs('admin.users.*') ? 'bg-primary text-white' : 'text-body'); ?>"
                    href="<?php echo e(route('admin.users.index')); ?>"><i class="bi bi-people"></i><span class="sidebar-label">Users</span></a>
                <a class="nav-link d-flex align-items-center gap-2 rounded-2 mb-1 px-3 py-2 <?php echo e(request()->routeIs('admin.messages.*') ? 'bg-primary text-white' : 'text-body'); ?>"
                    href="<?php echo e(route('admin.messages.index')); ?>"><i class="bi bi-envelope"></i><span class="sidebar-label">Messages</span></a>
            <?php endif; ?>

            </nav>

        <div class="p-3 border-top sidebar-bottom">
            <a class="nav-link d-flex align-items-center gap-2 rounded-2 mb-1 px-3 py-2 text-body"
                href="<?php echo e(route('home')); ?>"><i class="bi bi-box-arrow-up-right"></i><span class="sidebar-label">View Website</span></a>
            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-link nav-link d-flex align-items-center gap-2 rounded-2 px-3 py-2 text-body m-0">
                    <i class="bi bi-power"></i><span class="sidebar-label">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Backdrop (mobile) -->
    <div class="sidebar-backdrop" data-sidebar-toggle aria-hidden="true"></div>

    <!-- Main -->
    <main class="flex-grow-1 d-flex flex-column" style="min-width: 0;">
        <header class="bg-body border-bottom px-3 px-xl-4 py-3 d-flex align-items-center gap-2">
            <button type="button" class="btn btn-light btn-sm d-lg-none" data-sidebar-toggle aria-label="Open menu" title="Open menu" aria-expanded="false" aria-controls="adminSidebar">
                <i class="bi bi-list fs-5"></i>
            </button>
            <button type="button" class="btn btn-light btn-sm d-none d-lg-inline-flex align-items-center" data-sidebar-collapse aria-label="Collapse sidebar" title="Collapse sidebar">
                <i class="bi bi-layout-sidebar-inset sidebar-collapse-icon"></i>
                <i class="bi bi-layout-sidebar-inset-reverse sidebar-expand-icon d-none"></i>
            </button>

            <div class="flex-grow-1" style="min-width: 0;">
                <h5 class="mb-0 text-truncate"><?php echo $__env->yieldContent('heading', 'Admin'); ?></h5>
                <small class="text-muted text-truncate d-block"><?php echo $__env->yieldContent('subheading'); ?></small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="mc-theme-toggle js-theme-toggle" aria-label="Toggle light / dark mode" title="Toggle light / dark mode">
                    <i class="bi bi-moon-stars theme-icon-light"></i>
                    <i class="bi bi-sun theme-icon-dark d-none"></i>
                </button>
                <a href="<?php echo e(route('home')); ?>" class="btn btn-sm btn-light mb-0"><i class="bi bi-globe me-1"></i>View Site</a>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center gap-2 text-body text-decoration-none" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle fs-4"></i>
                        <span class="small fw-semibold d-none d-sm-inline"><?php echo e(auth()->user()->name); ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow rounded-3">
                        <li class="px-3 py-2">
                            <strong class="d-block text-truncate" style="max-width: 240px;"><?php echo e(auth()->user()->name); ?></strong>
                            <small class="text-muted text-truncate d-block" style="max-width: 240px;"><?php echo e(auth()->user()->email); ?></small>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="<?php echo e(route('logout')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="dropdown-item"><i class="bi bi-power me-2"></i>Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <section class="p-3 p-xl-4" style="overflow-y: auto;">
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if($errors->any()): ?>
                <div class="alert alert-danger py-2">
                    <ul class="mb-0 small">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php echo $__env->yieldContent('content'); ?>
        </section>
    </main>

    <script src="<?php echo e(asset('assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js')); ?>"></script>
    <script>
        (function () {
            var body = document.body;
            var toggleBtns = document.querySelectorAll('[data-sidebar-toggle]');
            var collapseBtn = document.querySelector('[data-sidebar-collapse]');
            var COLLAPSE_KEY = 'hosteldock.sidebar.collapsed';

            function setOpen(open) {
                body.classList.toggle('sidebar-open', open);
                toggleBtns.forEach(function (b) {
                    b.setAttribute('aria-expanded', open ? 'true' : 'false');
                });
            }

            toggleBtns.forEach(function (b) {
                b.addEventListener('click', function () {
                    setOpen(!body.classList.contains('sidebar-open'));
                });
            });

            document.querySelectorAll('.sidebar-docs .nav-link, .sidebar-docs .sidebar-bottom form button').forEach(function (el) {
                el.addEventListener('click', function () {
                    setOpen(false);
                });
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    setOpen(false);
                }
            });

            function setCollapsed(collapsed) {
                body.classList.toggle('sidebar-collapsed', collapsed);
                if (collapseBtn) {
                    collapseBtn.querySelector('.sidebar-collapse-icon').classList.toggle('d-none', collapsed);
                    collapseBtn.querySelector('.sidebar-expand-icon').classList.toggle('d-none', !collapsed);
                    collapseBtn.setAttribute('aria-label', collapsed ? 'Expand sidebar' : 'Collapse sidebar');
                    collapseBtn.setAttribute('title', collapsed ? 'Expand sidebar' : 'Collapse sidebar');
                }
                try {
                    localStorage.setItem(COLLAPSE_KEY, collapsed ? '1' : '0');
                } catch (e) {}
            }

            if (collapseBtn) {
                collapseBtn.addEventListener('click', function () {
                    setCollapsed(!body.classList.contains('sidebar-collapsed'));
                });
            }

            var saved = null;
            try {
                saved = localStorage.getItem(COLLAPSE_KEY);
            } catch (e) {}
            if (saved === '1') {
                setCollapsed(true);
            }

            window.addEventListener('resize', function () {
                if (window.innerWidth >= 992) {
                    setOpen(false);
                }
            });
        })();
    </script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\wamp64\www\studentroombookings\resources\views/admin/layouts/admin.blade.php ENDPATH**/ ?>