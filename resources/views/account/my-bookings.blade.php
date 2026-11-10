@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')
    <section class="bg-primary py-3">
        <div class="container">
            <h1 class="text-white mb-0">My Bookings</h1>
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
                            <a href="{{ route('profile') }}" class="btn btn-light mb-0">My Profile</a>
                            <a href="{{ route('my-bookings') }}" class="btn btn-primary-soft mb-0"><i class="bi bi-bookmark-check me-2"></i>My Bookings</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
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

                    @forelse ($bookings as $booking)
                        <div class="card shadow rounded-3 p-3 mb-3">
                            <div class="row g-3 align-items-center">
                                @if ($booking->hostel)
                                    <div class="col-sm-3 col-md-2">
                                        <img src="{{ asset($booking->hostel->coverImagePath()) }}"
                                            class="rounded-2 w-100 object-fit-cover" style="height: 80px;" alt="{{ $booking->hostel->name }}">
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <h6 class="mb-1">
                                            <a href="{{ route('hostel.detail', $booking->hostel->slug) }}" class="text-body text-primary-hover">{{ $booking->hostel->displayName() }}</a>
                                        </h6>
                                        <p class="small text-muted mb-1">{{ $booking->room_type }} &middot; {{ $booking->gender ?? 'General' }} &middot; {{ $booking->academic_year ?? 'N/A' }}</p>
                                        <p class="small mb-0"><strong>Ref:</strong> {{ $booking->booking_ref }}</p>
                                    </div>
                                @else
                                    <div class="col-sm-8 col-md-8">
                                        <h6 class="mb-1">Hostel no longer available</h6>
                                        <p class="small text-muted mb-1">{{ $booking->room_type }} &middot; {{ $booking->gender ?? 'General' }} &middot; {{ $booking->academic_year ?? 'N/A' }}</p>
                                        <p class="small mb-0"><strong>Ref:</strong> {{ $booking->booking_ref }}</p>
                                    </div>
                                @endif
                                <div class="col-sm-3 col-md-4 text-md-end">
                                    <div class="mb-2">
                                        @if ($booking->status === 'pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @elseif ($booking->status === 'confirmed')
                                            <span class="badge bg-success">Confirmed</span>
                                        @else
                                            <span class="badge bg-danger">Cancelled</span>
                                        @endif
                                        @if ($booking->isPaid())
                                            <span class="badge bg-success"><i class="bi bi-check2-circle me-1"></i>Paid</span>
                                        @elseif ($booking->payment_status === \App\Models\Booking::PAYMENT_PENDING)
                                            <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Payment Pending</span>
                                        @elseif ($booking->payment_status === \App\Models\Booking::PAYMENT_FAILED)
                                            <span class="badge bg-danger">Payment Failed</span>
                                        @endif
                                    </div>
                                    <p class="fw-bold text-primary mb-2">GHS{{ number_format($booking->amount, 2) }}</p>
                                    <div class="d-flex justify-content-md-end gap-1 flex-wrap">
                                        @if ($booking->isMomoPending())
                                            <form method="POST" action="{{ route('booking.pay.momo.status', $booking->id) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary mb-0"><i class="bi bi-arrow-repeat me-1"></i>Confirm Payment</button>
                                            </form>
                                        @elseif (! $booking->isPaid() && $booking->status !== 'cancelled' && $booking->hostel && $booking->hostel->paymentDetail && ($booking->hostel->paymentDetail->hasPaystack() || $booking->hostel->paymentDetail->hasMtnMoMoApi()))
                                            <a href="{{ route('booking.confirmation', $booking->booking_ref) }}" class="btn btn-sm btn-primary mb-0"><i class="bi bi-credit-card me-1"></i>Pay Now</a>
                                        @endif
                                        <a href="{{ route('booking.invoice', $booking->booking_ref) }}" target="_blank" class="btn btn-sm btn-light mb-0"><i class="bi bi-file-earmark-text me-1"></i>Invoice</a>
                                        @if ($booking->status === 'pending')
                                            <form method="POST" action="{{ route('booking.cancel', $booking->id) }}" onsubmit="return confirm('Cancel this booking?')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger-soft mb-0">Cancel</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if ($booking->status === 'confirmed' && $booking->expires_at)
                                @php($renewing = $booking->isWithinRenewalWindow())
                                <div class="mt-3 pt-3 border-top">
                                    @if ($renewing && ! $booking->isRenewalLocked())
                                        <div class="alert alert-warning rounded-2 py-2 px-3 mb-2 small">
                                            <i class="bi bi-clock-history me-1"></i>Your occupancy ends <strong>{{ $booking->expires_at->format('j M Y') }}</strong>.
                                            The {{ \App\Models\Booking::RENEWAL_WINDOW_DAYS }}-day renewal window is open &mdash; lock your room to keep it for another
                                            {{ \App\Models\Booking::OCCUPANCY_DAYS }} days.
                                        </div>
                                        <form method="POST" action="{{ route('booking.renewal.lock', $booking->id) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-primary mb-0"><i class="bi bi-lock me-1"></i>Lock &amp; Rebook (GHS{{ number_format($booking->renewalPrice(), 2) }})</button>
                                        </form>
                                    @elseif ($renewing && $booking->isRenewalLocked())
                                        @if ($booking->isRenewalMomoPending())
                                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                                <span class="small">
                                                    <i class="bi bi-hourglass-split me-1"></i>MoMo renewal request sent to <strong>{{ $booking->renewal_phone }}</strong>. Approve it on your phone, then confirm.
                                                </span>
                                                <form method="POST" action="{{ route('booking.renewal.pay.momo.status', $booking->id) }}">
                                                    @csrf
                                                    <button class="btn btn-sm btn-primary mb-0"><i class="bi bi-arrow-repeat me-1"></i>Confirm Payment</button>
                                                </form>
                                            </div>
                                        @else
                                            <div class="alert alert-success rounded-2 py-2 px-3 mb-2 small">
                                                <i class="bi bi-check2-lock me-1"></i>Your room is <strong>locked for renewal</strong>. Complete payment before
                                                <strong>{{ $booking->expires_at->format('j M Y') }}</strong> to extend your stay by {{ \App\Models\Booking::OCCUPANCY_DAYS }} days.
                                            </div>
                                            @php($detail = $booking->hostel ? $booking->hostel->paymentDetail : null)
                                            <div class="d-flex flex-wrap gap-2">
                                                @if ($detail && $detail->hasPaystack())
                                                    <form method="POST" action="{{ route('booking.renewal.pay', $booking->id) }}">
                                                        @csrf
                                                        <button class="btn btn-sm btn-primary mb-0"><i class="bi bi-credit-card me-1"></i>Pay GHS{{ number_format($booking->renewalPrice(), 2) }} Renewal</button>
                                                    </form>
                                                @endif
                                                @if ($detail && $detail->hasMtnMoMoApi())
                                                    <form method="POST" action="{{ route('booking.renewal.pay.momo', $booking->id) }}" class="d-flex gap-2">
                                                        @csrf
                                                        <input type="tel" name="phone" class="form-control form-control-sm" style="width: 180px;"
                                                            placeholder="MoMo number e.g. 0240000000" value="{{ old('phone', $booking->phone) }}" required maxlength="20">
                                                        <button class="btn btn-sm btn-primary mb-0"><i class="bi bi-phone me-1"></i>Pay with MoMo</button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endif
                                    @elseif (now()->lt($booking->expires_at))
                                        <div class="small text-muted">
                                            <i class="bi bi-calendar-check me-1"></i>Occupancy until <strong>{{ $booking->expires_at->format('j M Y') }}</strong>.
                                            You can lock and rebook within {{ \App\Models\Booking::RENEWAL_WINDOW_DAYS }} days of this date.
                                        </div>
                                    @else
                                        <div class="small text-danger">
                                            <i class="bi bi-hourglass-split me-1"></i>Occupancy ended {{ $booking->expires_at->format('j M Y') }}. This room is now available to other students.
                                        </div>
                                    @endif
                                </div>
                            @endif

                            @php($mates = $booking->room ? $booking->room->bookings->where('id', '!=', $booking->id) : collect())
                            @if ($mates->isNotEmpty())
                                <div class="mt-3 pt-3 border-top">
                                    <a class="small fw-semibold text-body text-decoration-none collapsed d-inline-flex align-items-center gap-2"
                                        data-bs-toggle="collapse" href="#roommates-{{ $booking->id }}" role="button" aria-expanded="false" aria-controls="roommates-{{ $booking->id }}">
                                        <i class="bi bi-people text-primary"></i>Roommates ({{ $mates->count() }})
                                        <i class="bi bi-chevron-down small"></i>
                                    </a>
                                    <div class="collapse mt-2" id="roommates-{{ $booking->id }}">
                                        <ul class="list-group list-group-flush">
                                            @foreach ($mates as $mate)
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
                                                            <a href="mailto:{{ $mate->email }}" class="text-muted text-decoration-none d-block small">{{ $mate->email }}</a>
                                                        @endif
                                                    </span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="card shadow rounded-3 p-5 text-center">
                            <i class="bi bi-bookmark-x display-4 text-muted mb-3"></i>
                            <h5>No bookings yet</h5>
                            <p class="text-muted mb-3">Browse hostels and reserve a room to see your bookings here.</p>
                            <div>
                                <a href="{{ route('home') }}" class="btn btn-primary mb-0">Find a Hostel</a>
                            </div>
                        </div>
                    @endforelse

                    <div class="mt-3">
                        {{ $bookings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection