@extends('layouts.app')

@section('title', 'Residency Policy')

@section('content')
    <section class="bg-primary py-3">
        <div class="container">
            <nav class="d-flex mb-1" aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-dots mb-0">
                    <li class="breadcrumb-item"><a class="text-white" href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Residency Policy</li>
                </ol>
            </nav>
            <h1 class="text-white mb-0">Residency Policy</h1>
        </div>
    </section>

    <section class="pt-4 pt-lg-5">
        <div class="container">
            <div class="card shadow rounded-3 p-4 p-md-5">
                <div class="col-lg-10 mx-auto">
                    <p class="text-muted">Last updated: September 2026</p>
                    <p>The following policy describes the expectations for students residing in hostels booked through HostelDock.</p>

                    <h5 class="mt-4">1. Eligibility</h5>
                    <p>Hostel accommodation is reserved for registered students of accredited institutions. You may be asked to present a student ID during check-in.</p>

                    <h5 class="mt-4">2. Code of Conduct</h5>
                    <ul>
                        <li>Respect fellow residents and hostel staff at all times.</li>
                        <li>Keep your room and common areas clean.</li>
                        <li>Refrain from excessive noise, especially at night.</li>
                        <li>Do not smoke or consume alcohol within the hostel premises unless permitted by the hostel.</li>
                    </ul>

                    <h5 class="mt-4">3. Visitors</h5>
                    <p>Visitors are generally allowed during the day. Overnight guests should be approved by hostel management, and guest registration may be required.</p>

                    <h5 class="mt-4">4. Loss or Damage</h5>
                    <p>Students are responsible for damage they cause to hostel property and will be billed accordingly by the hostel.</p>

                    <h5 class="mt-4">5. Security</h5>
                    <p>Report any security concerns to the hostel management immediately. Keep your room locked and do not share keys or access codes.</p>

                    <h5 class="mt-4">6. Check-Out</h5>
                    <p>At the end of your stay, return all keys and settle any outstanding fees. Personal belongings must be removed before the check-out date agreed with the hostel.</p>
                </div>
            </div>
        </div>
    </section>
@endsection