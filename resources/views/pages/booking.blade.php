@extends('layouts.app')

@section('title', 'Book a Room')

@section('content')
    <!-- Page title -->
    <section class="bg-primary py-3">
        <div class="container">
            <nav class="d-flex mb-1" aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-dots mb-0">
                    <li class="breadcrumb-item"><a class="text-white" href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a class="text-white" href="{{ route('hostel.detail', $hostel->slug) }}">{{ $hostel->displayName() }}</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Book</li>
                </ol>
            </nav>
            <h1 class="text-white mb-0">Book Your Room</h1>
        </div>
    </section>

    <section class="pt-4 pt-lg-5">
        <div class="container">
            <div class="row g-4 g-xl-5">
                <!-- Left -->
                <div class="col-lg-7 col-xl-8">
                    <div class="card shadow rounded-3 p-4">
                        <div class="d-flex gap-3 align-items-center mb-4">
                            <img src="{{ asset($hostel->coverImagePath()) }}"
                                class="rounded-2 object-fit-cover" style="height: 90px; width: 90px;" alt="{{ $hostel->name }}">
                            <div>
                                <h4 class="mb-1">{{ $hostel->displayName() }}</h4>
                                <p class="mb-1"><i class="fa fa-location-dot text-primary me-1"></i>{{ $hostel->location }}</p>
                                <small class="text-muted">{{ $hostel->distance }}</small>
                            </div>
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

                        <form method="POST" action="{{ route('booking.store', $hostel->slug) }}">
                            @csrf

                            <div class="mb-4">
                                <h5 class="mb-0"><i class="bi bi-door-open text-primary me-2"></i>Select a Room</h5>
                            </div>

                            <div class="mb-4">
                                <label class="form-label" for="room_type_filter">Room Category</label>
                                <select id="room_type_filter" class="form-select" data-room-type-filter
                                    aria-describedby="room_type_filter_help">
                                    <option value="">All room categories</option>
                                    @foreach ($types as $type)
                                        <option value="{{ $type }}" @selected($selectedType === $type)>{{ $type }}</option>
                                    @endforeach
                                </select>
                                <small id="room_type_filter_help" class="text-muted">
                                    Pick a category to see every room in it, then choose the exact room you want.
                                </small>
                            </div>

                            <div data-room-list>
                                @foreach ($roomsByType as $type => $rooms)
                                    <section class="mb-3" data-room-group="{{ $type }}">
                                        <h6 class="mt-3">{{ $type }}</h6>
                                        @foreach ($rooms as $room)
                                            @php($space = $room->availableSpace())
                                            <label class="d-block border rounded-2 p-3 mb-2 booking-room-option {{ ($space > 0) ? '' : 'opacity-50' }}">
                                                <div class="d-flex justify-content-between align-items-center gap-3">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <input type="radio" name="room" id="room_{{ $room->id }}" value="{{ $room->id }}"
                                                            class="form-check-input mt-0" {{ (request('room') == $room->id) ? 'checked' : '' }}
                                                            @if ($space <= 0) disabled @endif>
                                                        <span>
                                                            <strong>{{ $room->name }}</strong>
                                                            <small class="text-muted d-block">
                                                                @if ($room->gender_category === 'Male')
                                                                    <i class="fa fa-male text-primary"></i>
                                                                @elseif ($room->gender_category === 'Female')
                                                                    <i class="fa fa-female text-primary"></i>
                                                                @else
                                                                    <i class="fa fa-genderless text-primary"></i>
                                                                @endif
                                                                {{ $room->gender_category }} &middot;
                                                                @if ($space > 0)
                                                                    <span class="text-success">{{ $space }} bed{{ $space === 1 ? '' : 's' }} left</span>
                                                                @else
                                                                    <span class="text-danger">Sold out</span>
                                                                @endif
                                                            </small>
                                                            @if ($room->amenities->isNotEmpty())
                                                                <small class="d-flex flex-wrap gap-1 mt-2">
                                                                    @foreach ($room->amenities as $amenity)
                                                                        <span class="badge bg-light text-body border small">
                                                                            <i class="bi {{ $amenity->icon ?: 'bi-check2-circle' }} me-1 text-primary"></i>{{ $amenity->name }}
                                                                        </span>
                                                                    @endforeach
                                                                </small>
                                                            @endif
                                                            @if ($room->bookings->isNotEmpty())
                                                                @php($activeOccupants = $room->bookings->filter(fn ($b) => $b->expires_at === null || $b->expires_at->gte(\Illuminate\Support\Carbon::now())))
                                                                @if ($activeOccupants->isNotEmpty())
                                                                    <small class="text-muted d-block mt-1">
                                                                        <i class="bi bi-people me-1"></i>{{ $activeOccupants->count() }} occupant(s) already placed
                                                                    </small>
                                                                @endif
                                                            @endif
                                                        </span>
                                                    </div>
                                                    <span class="fw-bold text-primary">GHS{{ number_format($room->price, 2) }}</span>
                                                </div>
                                            </label>
                                        @endforeach
                                    </section>
                                @endforeach

                                <p class="text-muted small d-none" data-room-empty>
                                    No rooms match that category. Choose &ldquo;All room categories&rdquo; to see everything.
                                </p>
                            </div>

                            <hr class="my-4">

                            <h5 class="mb-3"><i class="bi bi-person text-primary me-2"></i>Student Details</h5>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="student_name" value="{{ old('student_name', auth()->user()->name ?? '') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}" required placeholder="e.g. 0240000000">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Academic Year</label>
                                    <select class="form-select" name="academic_year">
                                        <option value="">Select academic year</option>
                                        <option value="2025/2026" @selected(old('academic_year') === '2025/2026')>2025/2026</option>
                                        <option value="2026/2027" @selected(old('academic_year') === '2026/2027' || !old('academic_year'))>2026/2027</option>
                                        <option value="2027/2028" @selected(old('academic_year') === '2027/2028')>2027/2028</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Institution</label>
                                    <input type="text" class="form-control" name="institution" value="{{ old('institution', $hostel->institution_name) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Gender</label>
                                    <select class="form-select" name="gender">
                                        <option value="">Select gender</option>
                                        <option value="Male" @selected(old('gender') === 'Male')>Male</option>
                                        <option value="Female" @selected(old('gender') === 'Female')>Female</option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <a href="{{ route('hostel.detail', $hostel->slug) }}" class="btn btn-light mb-0"><i class="bi bi-arrow-left me-2"></i>Back</a>
                                <button type="submit" class="btn btn-primary mb-0">Confirm Booking<i class="bi bi-check2-circle ms-2"></i></button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Right summary -->
                <aside class="col-lg-5 col-xl-4">
                    <div class="card shadow rounded-3 p-4 sticky-lg-top">
                        <h5 class="mb-3">Booking Summary</h5>
                        <p class="small text-muted mb-3">Your reservation will be confirmed once payment is made. Pay directly at the hostel or via mobile money to the number provided.</p>

                        <ul class="list-group list-group-flush mb-4">
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Hostel</span>
                                <span class="fw-semibold text-end">{{ \Illuminate\Support\Str::limit($hostel->displayName(), 40) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Range</span>
                                <span class="fw-semibold">GHS{{ number_format($hostel->starting_price, 2) }} - GHS{{ number_format($hostel->rooms->max('price'), 2) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Payment</span>
                                <span class="fw-semibold">Per Academic Year</span>
                            </li>
                        </ul>

                        <div class="alert alert-primary border-0 mb-0">
                            <h6 class="alert-heading"><i class="bi bi-shield-check me-2"></i>Why book with us?</h6>
                            <ul class="small mb-0 text-primary-hover">
                                <li>Verified hostels only</li>
                                <li>Secure &amp; instant reservation</li>
                                <li>Dedicated student support</li>
                            </ul>
                        </div>
                    </div>

                    @if (isset($room) && $room && $occupants->isNotEmpty())
                        <div class="card shadow rounded-3 p-4 mt-4">
                            <h5 class="mb-3"><i class="bi bi-people text-primary me-2"></i>Already in this room</h5>
                            <p class="small text-muted mb-3">These students have confirmed this room. You&rsquo;ll be sharing with them once your booking is confirmed.</p>
                            <ul class="list-group list-group-flush">
                                @foreach ($occupants as $mate)
                                    <li class="list-group-item d-flex justify-content-between align-items-center gap-3 px-0">
                                        <span class="fw-semibold small">
                                            {{ $mate->student_name }}
                                            @if ($mate->gender)
                                                <small class="text-muted">&middot; {{ $mate->gender }}</small>
                                            @endif
                                        </span>
                                        <span class="text-end small">
                                            @if ($mate->phone)
                                                <a href="tel:{{ $mate->phone }}" class="text-body text-decoration-none d-block">{{ $mate->phone }}</a>
                                            @endif
                                            @if ($mate->email)
                                                <a href="mailto:{{ $mate->email }}" class="text-muted small text-decoration-none d-block">{{ $mate->email }}</a>
                                            @endif
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                            <small class="text-muted d-block mt-2"><i class="bi bi-shield-lock me-1"></i>Please use this contact information responsibly and only to coordinate your stay.</small>
                        </div>
                    @endif
                </aside>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (function () {
            const options = document.querySelectorAll('.booking-room-option');

            options.forEach(label => {
                const input = label.querySelector('input');
                if (!input) return;

                input.addEventListener('change', () => {
                    options.forEach(l => l.classList.remove('border-primary'));
                    if (input.checked) label.classList.add('border-primary');
                });

                if (input.checked) label.classList.add('border-primary');
            });

            const prechecked = document.querySelector('.booking-room-option input:checked');
            if (prechecked && prechecked.scrollIntoView) {
                prechecked.closest('.booking-room-option').scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            const filter = document.querySelector('[data-room-type-filter]');
            const groups = Array.from(document.querySelectorAll('[data-room-group]'));
            const emptyNote = document.querySelector('[data-room-empty]');

            if (!filter || !groups.length) return;

            function applyFilter(type) {
                let visible = 0;

                groups.forEach(group => {
                    const show = !type || group.dataset.roomGroup === type;
                    group.classList.toggle('d-none', !show);
                    if (show) visible++;
                });

                if (emptyNote) {
                    emptyNote.classList.toggle('d-none', visible > 0);
                }
            }

            filter.addEventListener('change', () => {
                const type = filter.value;

                // A room selected under a different category would be hidden with
                // its radio still checked, which submits a room the user cannot see.
                if (type) {
                    const stale = Array.from(document.querySelectorAll('input[name="room"]:checked'))
                        .find(input => !input.closest('[data-room-group]')
                            || input.closest('[data-room-group]').dataset.roomGroup !== type);

                    if (stale) stale.checked = false;
                }

                applyFilter(type);

                const url = new URL(window.location.href);

                if (type) {
                    url.searchParams.set('room_type', type);
                } else {
                    url.searchParams.delete('room_type');
                }

                window.history.replaceState({}, '', url);
            });

            applyFilter(filter.value);
        })();
    </script>
@endpush