<!-- Header START - floating pill navigation -->
<header class="mc-nav-wrap">
    <nav class="navbar mc-nav navbar-expand-xl">
        <div class="container-fluid px-0 flex-wrap flex-xl-nowrap align-items-center gap-2">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
                <img class="navbar-brand-item" src="{{ hd_asset('assets/images/logo.png') }}" alt="HostelDock">
            </a>

            <button class="navbar-toggler ms-auto mx-3 p-0 p-sm-2" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-animation">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
            </button>

            <div class="navbar-collapse collapse" id="navbarCollapse">
                <ul class="navbar-nav navbar-nav-scroll mx-auto">
                    <li class="nav-item mx-lg-1">
                        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">Home</a>
                    </li>
                    <li class="nav-item mx-lg-1">
                        <a class="nav-link {{ request()->is('about-us') ? 'active' : '' }}" href="{{ route('about') }}">About Us</a>
                    </li>
                    <li class="nav-item mx-lg-1">
                        <a class="nav-link {{ request()->is('contact-us') ? 'active' : '' }}" href="{{ route('contact') }}">Contact Us</a>
                    </li>
                    <li class="nav-item mx-lg-1">
                        <a class="nav-link {{ request()->is('faq') ? 'active' : '' }}" href="{{ route('faq') }}">Help &amp; FAQ</a>
                    </li>
                </ul>

                <ul class="nav flex-row flex-wrap align-items-center list-unstyled ms-xl-auto mt-2 mt-xl-0 gap-2">
                    <li class="nav-item">
                        <button type="button" class="mc-theme-toggle js-theme-toggle" aria-label="Toggle light / dark mode" title="Toggle light / dark mode">
                            <i class="bi bi-moon-stars theme-icon-light"></i>
                            <i class="bi bi-sun theme-icon-dark d-none"></i>
                        </button>
                    </li>
                    @auth
                        <li class="nav-item dropdown" style="list-style-type: none;">
                            <a class="avatar avatar-sm p-0" href="#" id="profileDropdown" role="button"
                                data-bs-auto-close="outside" data-bs-display="static" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <img class="avatar-img" src="{{ asset('assets/images/avatar/13.png') }}" alt="avatar">
                            </a>

                            <ul class="dropdown-menu dropdown-animation dropdown-menu-end shadow pt-3" aria-labelledby="profileDropdown">
                                <li class="px-3 mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-3">
                                            <img class="avatar-img rounded-circle" src="{{ asset('assets/images/avatar/13.png') }}" alt="avatar">
                                        </div>
                                        <div>
                                            <a class="h6 mt-2 mt-sm-0" href="{{ route('profile') }}">Hello, {{ auth()->user()->name }}</a>
                                            <p class="small m-0">{{ auth()->user()->email }}</p>
                                        </div>
                                    </div>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="bi bi-person fa-fw me-2"></i>My Profile</a></li>
                                <li><a class="dropdown-item" href="{{ route('my-bookings') }}"><i class="bi bi-bookmark-check fa-fw me-2"></i>My Bookings</a></li>
                                <li><a class="dropdown-item" href="{{ route('faq') }}"><i class="bi bi-info-circle fa-fw me-2"></i>Help Center</a></li>
                                @if (auth()->user()->isAdmin())
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 fa-fw me-2"></i>Admin Panel</a></li>
                                @elseif (auth()->user()->isManager())
                                    <li><a class="dropdown-item" href="{{ route('manager.dashboard') }}"><i class="bi bi-buildings fa-fw me-2"></i>Manager Panel</a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="dropdown-item p-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item btn-link bg-transparent border-0 w-100 text-start">
                                            <i class="bi bi-box-arrow-right fa-fw me-2"></i>Sign Out
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="btn btn-sm btn-outline-primary mb-0" href="{{ route('register') }}">Register</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-sm btn-primary mb-0" href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right me-1"></i>Sign In</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
</header>
<!-- Header END -->