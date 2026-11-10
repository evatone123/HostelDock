@extends('layouts.app')

@section('title', 'Buy Booking Access Token')

@section('content')
    <section class="bg-primary py-3">
        <div class="container">
            <nav class="d-flex mb-1" aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-dots mb-0">
                    <li class="breadcrumb-item"><a class="text-white" href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Buy Token</li>
                </ol>
            </nav>
            <h1 class="text-white mb-0">Buy Booking Access Token</h1>
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

                    @if (session('info'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            {{ session('info') }}
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

                    @if ($active)
                        <div class="card shadow rounded-3 p-4 text-center mb-4">
                            <div class="icon-lg bg-success bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:88px; height:88px;">
                                <i class="bi bi-patch-check text-success" style="font-size:2.4rem;"></i>
                            </div>
                            <h4 class="mb-2">You have an active booking token</h4>
                            <p class="text-muted mb-3">Token: <strong>{{ $active->token_ref }}</strong></p>

                            @if ($intended)
                                <a href="{{ $intended }}" class="btn btn-primary mb-0">Continue to Booking<i class="bi bi-arrow-right ms-2"></i></a>
                            @else
                                <a href="{{ route('home') }}" class="btn btn-primary mb-0">Browse Hostels</a>
                            @endif

                            <a href="{{ route('home') }}" class="btn btn-light mb-0">Back to Home</a>
                        </div>
                    @else
                        <div class="card shadow rounded-3 p-4 text-center mb-4">
                            <div class="icon-lg bg-primary bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:88px; height:88px;">
                                <i class="bi bi-patch-question text-primary" style="font-size:2.4rem;"></i>
                            </div>
                            <h4 class="mb-2">Unlock Booking Access</h4>
                            <p class="text-muted mb-4">
                                Pay a one-time booking access fee of <strong class="text-primary">GHS{{ number_format(\App\Models\BookingToken::PRICE_GHS, 2) }}</strong>.
                                Once paid, you can book a room in any hostel.
                            </p>

                            <form method="POST" action="{{ route('token.pay') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-lg w-100 mb-2">
                                    <i class="bi bi-credit-card me-2"></i>Pay GHS{{ number_format(\App\Models\BookingToken::PRICE_GHS, 2) }} via Paystack
                                </button>
                            </form>
                            <p class="small text-muted mb-0">
                                <i class="bi bi-shield-lock me-1"></i>You'll be redirected to Paystack to complete payment securely. Your token becomes active immediately after payment.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
