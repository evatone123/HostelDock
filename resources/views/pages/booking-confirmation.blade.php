@extends('layouts.app')

@section('title', 'Booking Confirmation')

@section('content')
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

                    @php($detail = $booking->hostel->paymentDetail)
                    @php($paystackEnabled = ! $booking->isPaid() && $detail && $detail->hasPaystack())
                    @php($momoApiEnabled = ! $booking->isPaid() && $detail && $detail->hasMtnMoMoApi())
                    @php($hasOnlineGateway = $paystackEnabled || $momoApiEnabled)

                    <div class="card shadow rounded-3 p-4 text-center mb-4">
                        <div class="icon-lg bg-success bg-opacity-10 rounded-circle mx-auto mt-2 mb-3 d-flex align-items-center justify-content-center" style="width:80px; height:80px;">
                            <i class="bi bi-check2-circle text-success fs-1"></i>
                        </div>
                        <h3 class="mb-2">Booking Received!</h3>
                        <p class="mb-2">Your reservation request has been received successfully.</p>
                        <h5 class="mb-0 text-primary">Reference: {{ $booking->booking_ref }}</h5>
                    </div>

                    <div class="card shadow rounded-3 p-4 mb-4">
                        <h5 class="mb-3"><i class="bi bi-receipt text-primary me-2"></i>Booking Details</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Hostel</span>
                                <span class="fw-semibold text-end">{{ $booking->hostel->displayName() }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Room</span>
                                <span class="fw-semibold">{{ $booking->room_type }} ({{ $booking->room->name ?? 'General' }})</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Student</span>
                                <span class="fw-semibold">{{ $booking->student_name }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Academic Year</span>
                                <span class="fw-semibold">{{ $booking->academic_year ?? '-' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Amount Due</span>
                                <span class="fw-bold text-primary">GHS{{ number_format($booking->amount, 2) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Status</span>
                                <span class="badge
                                    @if ($booking->status === 'confirmed') bg-success
                                    @elseif ($booking->status === 'cancelled') bg-danger
                                    @else bg-warning text-dark @endif">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Payment</span>
                                <span class="badge
                                    @if ($booking->isPaid()) bg-success
                                    @elseif ($booking->payment_status === \App\Models\Booking::PAYMENT_PENDING) bg-warning text-dark
                                    @elseif ($booking->payment_status === \App\Models\Booking::PAYMENT_FAILED) bg-danger
                                    @else bg-secondary @endif">
                                    {{ $booking->paymentStatusLabel() }}
                                </span>
                            </li>
                            @if ($booking->expires_at)
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span class="text-muted">Occupancy ends</span>
                                    <span class="fw-semibold">{{ $booking->expires_at->format('j M Y') }}</span>
                                </li>
                            @endif
                            @if ($booking->renewal_count > 0)
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span class="text-muted">Renewals</span>
                                    <span class="fw-semibold text-success">{{ $booking->renewal_count }} &times; renewed</span>
                                </li>
                            @endif
                        </ul>
                    </div>

                    @if (isset($roommates))
                        <div class="card shadow rounded-3 p-4 mb-4">
                            <h5 class="mb-3"><i class="bi bi-people text-primary me-2"></i>Your Roommates</h5>
                            @if ($roommates->isNotEmpty())
                                <p class="small text-muted mb-3">
                                    These students have confirmed reservations in the same room. Reach out to introduce yourself and coordinate your stay.
                                </p>
                                <ul class="list-group list-group-flush">
                                    @foreach ($roommates as $mate)
                                        <li class="list-group-item d-flex justify-content-between align-items-center gap-3 px-0">
                                            <span class="fw-semibold">
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
                                <small class="text-muted d-block mt-3"><i class="bi bi-shield-lock me-1"></i>Please use this contact information responsibly and only to coordinate your stay.</small>
                            @else
                                <p class="text-muted mb-0">
                                    <i class="bi bi-people me-1"></i>You&rsquo;re currently the only one in this room. Other students who confirm this room will appear here.
                                </p>
                            @endif
                        </div>
                    @endif

                    <div class="alert alert-info mb-4">
                        <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i>Next steps</h6>
                        <ol class="small mb-0 ps-3">
                            @if ($hasOnlineGateway)
                                <li>Pay the amount due online below — your booking is confirmed instantly once payment succeeds.</li>
                            @else
                                <li>Pay the amount due using the payment options below.</li>
                                <li>Send your payment confirmation to the available contact details.</li>
                                <li>Your booking will be confirmed by the hostel shortly.</li>
                            @endif
                            @auth
                                <li>Track your bookings anytime from <a href="{{ route('my-bookings') }}">My Bookings</a>.</li>
                            @endauth
                        </ol>
                    </div>

                    @if ($booking->isPaid())
                        <div class="card shadow rounded-3 p-4 text-center mb-4">
                            <div class="icon-lg bg-success bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:64px; height:64px;">
                                <i class="bi bi-check2-circle text-success fs-3"></i>
                            </div>
                            <h5 class="mb-1">Payment Received</h5>
                            <p class="text-muted mb-2">GHS{{ number_format($booking->amount, 2) }} paid
                                @if ($booking->paid_at)
                                    on {{ $booking->paid_at->format('j M Y, g:i a') }}
                                @endif
                            </p>
                            @if ($booking->gateway)
                                <p class="small text-muted mb-0">Paid via {{ $booking->gatewayLabel() }}
                                    @if ($booking->paystack_transaction_ref)
                                        &middot; {{ $booking->paystack_transaction_ref }}
                                    @elseif ($booking->provider_reference)
                                        &middot; {{ $booking->provider_reference }}
                                    @endif
                                </p>
                            @endif
                        </div>
                    @elseif ($booking->isMomoPending())
                        <div class="card shadow rounded-3 p-3 mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0"><i class="bi bi-phone text-primary me-2"></i>MTN MoMo Payment Pending</h6>
                                <span class="badge bg-warning text-dark">Pending</span>
                            </div>
                            <p class="small text-muted mb-2">
                                A payment request of <strong>GHS{{ number_format($booking->amount, 2) }}</strong> was sent to
                                <strong>{{ $booking->momo_phone }}</strong>. Approve it on your phone via the MTN MoMo app
                                or dial <strong>*170#</strong>, then confirm the payment below.
                            </p>
                            @auth
                                <form method="POST" action="{{ route('booking.pay.momo.status', $booking->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-primary mb-0 w-100"><i class="bi bi-arrow-repeat me-2"></i>Confirm Payment</button>
                                </form>
                            @endauth
                        </div>
                    @elseif ($hasOnlineGateway)
                        <div class="card shadow rounded-3 p-3 mb-4">
                            <h6 class="mb-3"><i class="bi bi-credit-card text-primary me-2"></i>Pay Online</h6>
                            <p class="small text-muted mb-2">
                                Choose how you would like to pay <strong>GHS{{ number_format($booking->amount, 2) }}</strong>.
                                The booking is confirmed automatically once payment succeeds.
                            </p>
                            @if ($paystackEnabled)
                                <div class="border rounded-2 p-3 mb-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <strong class="small"><i class="bi bi-credit-card me-1 text-primary"></i>Paystack</strong>
                                        <span class="badge bg-light text-dark border">Card / Bank</span>
                                    </div>
                                    <p class="small text-muted mb-2">Pay securely through {{ $booking->hostel->displayName() }}&rsquo;s Paystack account.</p>
                                    @auth
                                        <form method="POST" action="{{ route('booking.pay', $booking->id) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-primary mb-0 w-100"><i class="bi bi-lock me-2"></i>Pay GHS{{ number_format($booking->amount, 2) }} with Paystack</button>
                                        </form>
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-outline-primary mb-0 w-100">Log in to pay</a>
                                    @endauth
                                </div>
                            @endif
                            @if ($momoApiEnabled)
                                <div class="border rounded-2 p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <strong class="small"><i class="bi bi-phone me-1 text-primary"></i>MTN Mobile Money</strong>
                                        <span class="badge bg-light text-dark border">MTN</span>
                                    </div>
                                    <p class="small text-muted mb-2">We send an approval prompt to your phone and you approve the payment securely via the MTN MoMo app or *170#.</p>
                                    @auth
                                        <form method="POST" action="{{ route('booking.pay.momo', $booking->id) }}">
                                            @csrf
                                            <div class="mb-2">
                                                <input type="tel" name="phone" class="form-control" placeholder="Your MoMo number (e.g. 0240000000)" required maxlength="20" value="{{ old('phone', $booking->phone) }}" autocomplete="tel">
                                            </div>
                                            <button type="submit" class="btn btn-primary mb-0 w-100"><i class="bi bi-phone me-2"></i>Pay GHS{{ number_format($booking->amount, 2) }} with MTN MoMo</button>
                                        </form>
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-primary mb-0 w-100">Log in to pay</a>
                                    @endauth
                                </div>
                            @endif
                        </div>
                    @endif

                    @if ($detail && $detail->is_active && ($detail->hasMomo() || $detail->bank_name || $detail->bank_account_number))
                        <div class="card shadow rounded-3 p-3 mb-4">
                            <h6 class="mb-3"><i class="bi bi-credit-card text-primary me-2"></i>Payment Options</h6>
                            @if ($booking->isMomoPending() && $detail->hasMomo())
                                <div class="border rounded-2 p-3 mb-2">
                                    <strong class="small text-muted">MTN MoMo prompt sent — check your phone. If you prefer to transfer manually instead:</strong>
                                </div>
                            @endif
                            @if ($detail->hasMomo())
                                <div class="border rounded-2 p-3 mb-2">
                                    <strong class="small"><i class="bi bi-phone me-1 text-primary"></i>Mobile Money</strong>
                                    @if ($detail->momo_provider)
                                        <span class="badge bg-light text-dark border ms-1">{{ $detail->momo_provider }}</span>
                                    @endif
                                    <p class="small mb-1 fw-semibold">Pay to: {{ $detail->momo_number }}</p>
                                    @if ($detail->momo_account_name)
                                        <p class="small text-muted mb-0">{{ $detail->momo_account_name }}</p>
                                    @endif
                                </div>
                            @endif
                            @if ($detail->bank_name || $detail->bank_account_number)
                                <div class="border rounded-2 p-3">
                                    <strong class="small"><i class="bi bi-bank me-1 text-success"></i>Bank Transfer</strong>
                                    @if ($detail->bank_name)
                                        <span class="badge bg-light text-dark border ms-1">{{ $detail->bank_name }}</span>
                                    @endif
                                    @if ($detail->bank_account_number)
                                        <p class="small mb-1 fw-semibold">Pay to: {{ $detail->bank_account_number }}</p>
                                    @endif
                                    @if ($detail->bank_account_name)
                                        <p class="small text-muted mb-1">{{ $detail->bank_account_name }}@if ($detail->bank_branch) ({{ $detail->bank_branch }})@endif</p>
                                    @endif
                                </div>
                            @endif
                            @if ($detail->instructions)
                                <p class="small text-muted mt-2 mb-0"><i class="bi bi-info-circle me-1"></i>{{ $detail->instructions }}</p>
                            @endif
                        </div>
                    @else
                        @php($gateways = \App\Models\PaymentGateway::active()->get())
                        @if ($gateways->isNotEmpty())
                            <div class="card shadow rounded-3 p-3 mb-4">
                                <h6 class="mb-3"><i class="bi bi-credit-card text-primary me-2"></i>Payment Options</h6>
                                <div class="row g-2">
                                    @foreach ($gateways as $gateway)
                                        <div class="col-sm-6">
                                            <div class="border rounded-2 p-3 h-100">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <strong class="small">
                                                        @if ($gateway->type === 'mobile_money')
                                                            <i class="bi bi-phone me-1 text-primary"></i>
                                                        @elseif ($gateway->type === 'bank')
                                                            <i class="bi bi-bank me-1 text-success"></i>
                                                        @elseif ($gateway->type === 'card')
                                                            <i class="bi bi-credit-card me-1 text-warning"></i>
                                                        @else
                                                            <i class="bi bi-cash me-1 text-secondary"></i>
                                                        @endif
                                                        {{ $gateway->name }}
                                                    </strong>
                                                    @if ($gateway->network)
                                                        <span class="badge bg-light text-dark border">{{ $gateway->network }}</span>
                                                    @endif
                                                </div>
                                                @if ($gateway->account_number)
                                                    <p class="small mb-1 fw-semibold">Pay to: {{ $gateway->account_number }}</p>
                                                @endif
                                                @if ($gateway->account_name)
                                                    <p class="small text-muted mb-1">{{ $gateway->account_name }}</p>
                                                @endif
                                                @if ($gateway->instructions)
                                                    <p class="small text-muted mb-0">{{ $gateway->instructions }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif

                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <a href="{{ route('booking.invoice', $booking->booking_ref) }}" target="_blank" class="btn btn-primary mb-0"><i class="bi bi-printer me-2"></i>View Invoice</a>
                        <a href="{{ route('home') }}" class="btn btn-light mb-0">Back to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection