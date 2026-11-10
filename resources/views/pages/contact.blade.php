@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
    <section class="bg-primary py-3">
        <div class="container">
            <nav class="d-flex mb-1" aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-dots mb-0">
                    <li class="breadcrumb-item"><a class="text-white" href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Contact Us</li>
                </ol>
            </nav>
            <h1 class="text-white mb-0">Contact Us</h1>
        </div>
    </section>

    <section class="pt-4 pt-lg-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card shadow rounded-3 p-4 mb-4">
                        <h5 class="mb-3"><i class="bi bi-geo-alt text-primary me-2"></i>Our Offices</h5>
                        <div class="mb-3">
                            <h6 class="mb-1">Tarkwa Office</h6>
                            <p class="small text-muted mb-0">Tamso Estate Junction, Tarkwa, Western Region, Ghana</p>
                        </div>
                        <div class="mb-3">
                            <h6 class="mb-1">Takoradi Office</h6>
                            <p class="small text-muted mb-0">Near Takoradi Technical University, Takoradi, Western Region, Ghana</p>
                        </div>
                    </div>

                    <div class="card shadow rounded-3 p-4">
                        <h5 class="mb-3"><i class="bi bi-headset text-primary me-2"></i>Reach Us</h5>
                        <p class="mb-2"><a class="text-body text-primary-hover" href="tel:+233503968424"><i class="bi bi-telephone text-primary me-2"></i>+233 (50) 396 8424</a></p>
                        <p class="mb-2"><a class="text-body text-primary-hover" href="tel:+233547668839"><i class="bi bi-telephone text-primary me-2"></i>+233 (54) 766 8839</a></p>
                        <p class="mb-2"><a class="text-body text-primary-hover" href="mailto:info@hosteldock.com"><i class="bi bi-envelope text-primary me-2"></i>info@hosteldock.com</a></p>
                        {{-- <ul class="list-inline mb-0 mt-3">
                            <li class="list-inline-item"><a class="btn btn-sm px-2 bg-facebook mb-0" href="https://bit.ly/studentroombooking" target="_blank"><i class="fab fa-fw fa-facebook-f"></i></a></li>
                            <li class="list-inline-item"><a class="btn btn-sm shadow px-2 bg-youtube mb-0" href="https://www.youtube.com/channel/UCEbB8yMyDJgn5C6gYW_anpQ" target="_blank"><i class="fab fa-fw fa-youtube"></i></a></li>
                            <li class="list-inline-item"><a class="btn btn-sm shadow px-2 bg-twitter mb-0" href="https://bit.ly/SRBTwitter" target="_blank"><i class="fab fa-fw fa-twitter"></i></a></li>
                            <li class="list-inline-item"><a class="btn btn-sm shadow px-2 bg-whatsapp mb-0" href="https://whatsapp.com/channel/0029VazWtRHGOj9qjnoMso2A" target="_blank"><i class="fab fa-fw fa-whatsapp"></i></a></li>
                        </ul> --}}
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow rounded-3 p-4">
                        <h4 class="mb-3">Send us a message</h4>
                        <p class="text-muted mb-4">Have a question, a hostel to list or need help with a booking? We would love to hear from you.</p>

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

                        <form method="POST" action="{{ route('contact.submit') }}">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Subject</label>
                                    <input type="text" class="form-control" name="subject" value="{{ old('subject') }}" placeholder="e.g. Help with my booking">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Message <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="message" rows="5" required>{{ old('message') }}</textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary mb-0"><i class="bi bi-send me-2"></i>Send Message</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection