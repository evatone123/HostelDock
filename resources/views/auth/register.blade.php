@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card shadow rounded-3 p-4 auth-card">
                        <div class="text-center mb-4">
                            <a href="{{ route('home') }}" class="d-inline-block mb-3">
                                <img class="h-40px" src="{{ hd_asset('assets/images/logo.png') }}" alt="logo">
                            </a>
                            <h4 class="mb-1">Create an Account</h4>
                            <p class="text-muted mb-0">Join HostelDock to book and manage hostels</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger py-2 mb-4">
                                <ul class="mb-0 small">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}" required autocomplete="email">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" name="phone" value="{{ old('phone') }}" placeholder="e.g. 0240000000">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" required autocomplete="new-password">
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                            @include('partials.recaptcha')
                            <button type="submit" class="btn btn-primary mb-0 w-100">Create Account</button>
                        </form>

                        <p class="small text-center mt-4 mb-0">
                            Already have an account?
                            <a href="{{ route('login') }}" class="text-primary">Sign in</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection