@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
    <section class="bg-primary py-3">
        <div class="container">
            <h1 class="text-white mb-0">My Profile</h1>
        </div>
    </section>

    <section class="pt-4 pt-lg-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card shadow rounded-3 p-4 text-center">
                        <div class="avatar avatar-xl mx-auto mb-3">
                            <img class="avatar-img rounded-circle" src="{{ asset('assets/images/avatar/13.png') }}" alt="avatar">
                        </div>
                        <h5 class="mb-0">{{ auth()->user()->name }}</h5>
                        <p class="text-muted mb-0">{{ auth()->user()->email }}</p>

                        <hr class="my-4">

                        <div class="d-grid gap-2">
                            <a href="{{ route('profile') }}" class="btn btn-primary-soft mb-0">My Profile</a>
                            <a href="{{ route('my-bookings') }}" class="btn btn-light mb-0"><i class="bi bi-bookmark-check me-2"></i>My Bookings</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow rounded-3 p-4">
                        <h5 class="mb-4">Edit Profile</h5>

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger py-2 mb-4">
                                <ul class="mb-0 small">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('profile') }}">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" class="form-control" name="name" value="{{ auth()->user()->name }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" value="{{ auth()->user()->email }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="tel" class="form-control" name="phone" value="{{ auth()->user()->phone }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">New Password <small class="text-muted">(leave blank to keep current)</small></label>
                                    <input type="password" class="form-control" name="password" autocomplete="new-password">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" name="password_confirmation" autocomplete="new-password">
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary mb-0"><i class="bi bi-check-lg me-2"></i>Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection