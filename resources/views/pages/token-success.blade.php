@extends('layouts.app')

@section('title', 'Booking Token Payment Successful')

@section('content')
    <section class="bg-primary py-3">
        <div class="container">
            <nav class="d-flex mb-1" aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-dots mb-0">
                    <li class="breadcrumb-item"><a class="text-white" href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a class="text-white" href="{{ route('token.buy') }}">Buy Token</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Payment Confirmed</li>
                </ol>
            </nav>
            <h1 class="text-white mb-0">Payment Confirmed</h1>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-xl-6">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="card shadow rounded-3 p-4 text-center mb-4">
                        <div class="icon-lg bg-success bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:88px; height:88px;">
                            <i class="bi bi-patch-check text-success" style="font-size:2.6rem;"></i>
                        </div>
                        <h4 class="mb-2">You're ready to book! 🎉</h4>
                        <p class="text-muted mb-0">Your booking access token is active. Follow the link below to continue, or browse available rooms.</p>
                    </div>

                    @if ($active)
                        <div class="card shadow rounded-3 border-0 mb-4">
                            <div class="card-body">
                                <h6 class="mb-3"><i class="bi bi-patch-check text-success me-2"></i>Your Active Booking Token</h6>
                                <ul class="list-group list-group-flush text-start">
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Token Reference</span>
                                        <span class="fw-semibold font-monospace">{{ $active->token_ref }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Amount Paid</span>
                                        <span class="fw-semibold text-primary">GHS{{ number_format($active->amount, 2) }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Paid On</span>
                                        <span class="fw-semibold">{{ $active->paid_at?->format('d M Y, H:i') }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Status</span>
                                        <span class="badge bg-success">{{ $active->statusLabel() }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @endif

                    <div class="text-center">
                        @if ($intended)
                            <a href="{{ $intended }}" class="btn btn-primary mb-0">Continue Booking</a>
                        @else
                            <a href="{{ route('home') }}" class="btn btn-primary mb-0">Browse Rooms</a>
                            <a href="{{ route('my-bookings') }}" class="btn btn-outline-primary mb-0 ms-2">My Bookings</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
