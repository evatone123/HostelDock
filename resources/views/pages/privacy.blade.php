@extends('layouts.app')

@section('title', 'Privacy Policy')

@section('content')
    <section class="bg-primary py-3">
        <div class="container">
            <nav class="d-flex mb-1" aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-dots mb-0">
                    <li class="breadcrumb-item"><a class="text-white" href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Privacy Policy</li>
                </ol>
            </nav>
            <h1 class="text-white mb-0">Privacy Policy</h1>
        </div>
    </section>

    <section class="pt-4 pt-lg-5">
        <div class="container">
            <div class="card shadow rounded-3 p-4 p-md-5">
                <div class="col-lg-10 mx-auto">
                    <p class="text-muted">Last updated: September 2026</p>
                    <p>HostelDock (&ldquo;we&rdquo;, &ldquo;us&rdquo;, &ldquo;our&rdquo;) respects your privacy and is committed to protecting your personal data. This policy explains what information we collect when you use our platform and how we use it.</p>

                    <h5 class="mt-4">1. Information We Collect</h5>
                    <p>We collect your name, email address, phone number and any other details you provide when creating an account, making a booking, contacting us or using our services.</p>

                    <h5 class="mt-4">2. How We Use Your Information</h5>
                    <ul>
                        <li>To process and manage your hostel bookings.</li>
                        <li>To communicate with you about your bookings and support requests.</li>
                        <li>To improve our platform and services.</li>
                        <li>To comply with legal obligations.</li>
                    </ul>

                    <h5 class="mt-4">3. Sharing of Information</h5>
                    <p>We only share your details with the hostel you book or intend to book, and with trusted service providers necessary to operate the platform. We never sell your personal data.</p>

                    <h5 class="mt-4">4. Data Security</h5>
                    <p>We use appropriate technical and organisational measures to keep your information safe, including secure connections and restricted access to personal data.</p>

                    <h5 class="mt-4">5. Your Rights</h5>
                    <p>You may access, correct or delete your personal information at any time by visiting your profile settings or contacting us at info@hosteldock.com.</p>

                    <h5 class="mt-4">6. Contact Us</h5>
                    <p>Questions about this policy? Reach us at <a href="mailto:info@hosteldock.com">info@hosteldock.com</a> or call +233 (50) 396 8424.</p>
                </div>
            </div>
        </div>
    </section>
@endsection