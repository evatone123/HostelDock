@extends('layouts.app')

@section('title', 'Sign In')

@section('content')
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card shadow rounded-3 p-4 auth-card">
                        <div class="text-center mb-4">
                            <a href="{{ route('home') }}" class="d-inline-block mb-3">
                                <img class="h-40px" src="{{ hd_asset('assets/images/logo.png') }}" alt="logo">
                            </a>
                            <h4 class="mb-1">Welcome Back</h4>
                            <p class="text-muted mb-0">Sign in to manage your bookings</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger py-2 mb-4" role="alert">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" required autocomplete="current-password">
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                    <label class="form-check-label" for="remember">Remember me</label>
                                </div>
                                <a href="{{ route('password.request') }}" class="small text-primary">Forgot password?</a>
                            </div>
                            @include('partials.recaptcha')
                            <button type="submit" class="btn btn-primary mb-0 w-100">Sign In</button>
                        </form>

                        <p class="small text-center mt-4 mb-0">
                            Don&rsquo;t have an account?
                            <a href="{{ route('register') }}" class="text-primary">Register here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection