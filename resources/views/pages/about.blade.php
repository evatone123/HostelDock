@extends('layouts.app')

@section('title', 'About Us')

@section('content')
    <section class="bg-primary py-3">
        <div class="container">
            <nav class="d-flex mb-1" aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-dots mb-0">
                    <li class="breadcrumb-item"><a class="text-white" href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">About Us</li>
                </ol>
            </nav>
            <h1 class="text-white mb-0">About Us</h1>
        </div>
    </section>

    <section class="pt-4 pt-lg-5 pb-0">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <img class="rounded-3 w-100 object-fit-cover" style="height: 400px;" src="{{ hd_asset('assets/images/about/slider-imgs.jpg') }}" alt="About HostelDock">
                </div>
                <div class="col-lg-6">
                    <h2 class="mb-3">About HostelDock</h2>
                    <p class="mb-3">
                        HostelDock is Ghana's trusted platform for booking student hostels and rooms near your
                        campus. We connect students to verified and affordable accommodation options, making the search
                        for a new home easy, safe and stress-free.
                    </p>
                    <p class="mb-3">
                        Founded with the mission of easing the burden of hostel hunting, we partner with hostels across
                        KNUST, KSTU, University of Ghana and more, so you can compare prices, check availability and
                        reserve a room in just a few clicks.
                    </p>
                    <div class="row g-3 mt-2">
                        <div class="col-sm-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill text-primary me-2 fs-4"></i>
                                <div><h6 class="mb-0">Verified Hostels</h6></div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-shield-check text-primary me-2 fs-4"></i>
                                <div><h6 class="mb-0">Safe Booking</h6></div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-headset text-primary me-2 fs-4"></i>
                                <div><h6 class="mb-0">Student Support</h6></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-4">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow rounded-3 p-4 h-100">
                        <i class="bi bi-pin-map text-primary fs-1 mb-3"></i>
                        <h5>Our Mission</h5>
                        <p class="mb-0">To help every student find a safe, affordable and convenient place to call home for the academic year.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow rounded-3 p-4 h-100">
                        <i class="bi bi-people text-primary fs-1 mb-3"></i>
                        <h5>Our Vision</h5>
                        <p class="mb-0">To become Ghana's leading digital accommodation marketplace for students across every university and campus.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow rounded-3 p-4 h-100">
                        <i class="bi bi-hand-thumbs-up text-primary fs-1 mb-3"></i>
                        <h5>Our Promise</h5>
                        <p class="mb-0">Transparent pricing, trusted hostels and a booking experience built around the student.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-4 pt-lg-5">
        <div class="container">
            <div class="card shadow rounded-3 p-4 p-md-5">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <h5 class="mb-2"><i class="bi bi-building me-2 text-primary"></i>Tarkwa Office</h5>
                        <p class="mb-1">Tamso Estate Junction, Tarkwa, Western Region, Ghana</p>
                        <p class="mb-1"><a href="tel:+233503968424" class="text-body text-primary-hover">+233 (50) 396 8424</a></p>
                        <p class="mb-0"><a href="mailto:info@hosteldock.com" class="text-body text-primary-hover">info@hosteldock.com</a></p>
                    </div>
                    <div class="col-lg-6">
                        <h5 class="mb-2"><i class="bi bi-building text-primary me-2"></i>Takoradi Office</h5>
                        <p class="mb-1">Near Takoradi Technical University, Takoradi, Western Region, Ghana</p>
                        <p class="mb-1"><a href="tel:+233547668839" class="text-body text-primary-hover">+233 (54) 766 8839</a></p>
                        <a href="{{ route('contact') }}" class="btn btn-primary-soft mt-2">Get in Touch <i class="bi bi-arrow-right ms-2"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection