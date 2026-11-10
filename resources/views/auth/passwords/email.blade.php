@extends('layouts.app')

@section('title', 'Forgot Password')

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
                            <h4 class="mb-1">Forgot Password?</h4>
                            <p class="text-muted mb-0">Enter your email and we&rsquo;ll send you a reset link</p>
                        </div>

                        @if (session('status'))
                            <div class="alert alert-success py-2 mb-4" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger py-2 mb-4" role="alert">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
                            </div>
                            @include('partials.recaptcha')
                            <button type="submit" class="btn btn-primary mb-0 w-100"><i class="bi bi-envelope me-2"></i>Send Password Reset Link</button>
                        </form>

                        <p class="small text-center mt-4 mb-0">
                            Remembered your password?
                            <a href="{{ route('login') }}" class="text-primary">Back to Sign In</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection