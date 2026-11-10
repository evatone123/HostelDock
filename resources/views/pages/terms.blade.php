@extends('layouts.app')

@section('title', 'Terms of Service')

@section('content')
    <section class="bg-primary py-3">
        <div class="container">
            <nav class="d-flex mb-1" aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-dots mb-0">
                    <li class="breadcrumb-item"><a class="text-white" href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Terms &amp; Conditions</li>
                </ol>
            </nav>
            <h1 class="text-white mb-0">Terms &amp; Conditions</h1>
        </div>
    </section>

    <section class="pt-4 pt-lg-5">
        <div class="container">
            <div class="card shadow rounded-3 p-4 p-md-5">
                <div class="col-lg-10 mx-auto">
                    <p class="text-muted">Last updated: September 2026</p>
                    <p>By using the HostelDock platform, you agree to these terms and conditions. Please read them carefully before making any booking.</p>

                    <h5 class="mt-4">1. The Service</h5>
                    <p>HostelDock is a digital marketplace that connects students with verified hostels. We facilitate bookings but are not the landlord of the hostels listed.</p>

                    <h5 class="mt-4">2. Bookings</h5>
                    <p>A booking is considered valid once the reservation is made and, where applicable, payment is confirmed by the hostel. Room availability shown on the platform may change at any time.</p>

                    <h5 class="mt-4">3. Pricing &amp; Payments</h5>
                    <p>All prices are listed in Ghana Cedi (GHS) and are per academic year unless stated otherwise. Prices are set by the hostel and may be changed by the hostel without notice once a booking is completed.</p>

                    <h5 class="mt-4">4. Cancellations &amp; Refunds</h5>
                    <p>Cancellations of pending bookings may be done from your account or by contacting support. Refunds are subject to the individual hostel&rsquo;s cancellation policy.</p>

                    <h5 class="mt-4">5. Student Responsibility</h5>
                    <p>Students are responsible for providing accurate information and for complying with the hostel&rsquo;s rules and regulations during their stay.</p>

                    <h5 class="mt-4">6. Limitation of Liability</h5>
                    <p>While we verify hostels, we are not liable for issues arising directly between a student and a hostel. We will, however, do our best to resolve disputes fairly.</p>

                    <h5 class="mt-4">7. Contact</h5>
                    <p>Questions about these terms? Contact us at <a href="mailto:info@hosteldock.com">info@hosteldock.com</a>.</p>
                </div>
            </div>
        </div>
    </section>
@endsection