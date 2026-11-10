@extends('layouts.app', ['body_class' => 'bg-mode'])

@section('title', 'Booking Invoice')

@section('content')
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <div class="text-end mb-3 d-print-none">
                        <button class="btn btn-primary mb-0" onclick="window.print()"><i class="bi bi-printer me-2"></i>Print Invoice</button>
                        <a href="{{ route('booking.confirmation', $booking->booking_ref) }}" class="btn btn-light mb-0">Back</a>
                    </div>

                    <div class="card shadow rounded-3 p-4 p-md-5">
                        <div class="row justify-content-between align-items-center mb-4">
                            <div class="col-sm-6">
                                <img class="h-40px" src="{{ hd_asset('assets/images/logo.png') }}" alt="logo">
                            </div>
                            <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                                <h4 class="mb-0">Invoice</h4>
                                <p class="small text-muted mb-0">Ref: {{ $booking->booking_ref }}</p>
                                <p class="small text-muted mb-0">Issued: {{ $booking->created_at->format('d M Y') }}</p>
                            </div>
                        </div>

                        <hr>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <h6 class="mb-2">Billed To</h6>
                                <p class="mb-1 fw-semibold">{{ $booking->student_name }}</p>
                                <p class="mb-1 small">{{ $booking->email }}</p>
                                <p class="mb-1 small">{{ $booking->phone }}</p>
                                <p class="mb-0 small">{{ $booking->institution }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="mb-2">Hostel</h6>
                                <p class="mb-1 fw-semibold">{{ $booking->hostel->displayName() }}</p>
                                <p class="mb-1 small"><i class="fa fa-location-dot text-primary me-1"></i>{{ $booking->hostel->location }}</p>
                                <p class="mb-0 small"><i class="fa fa-walking text-primary me-1"></i>{{ $booking->hostel->distance }}</p>
                            </div>
                        </div>

                        <table class="table table-bordered align-middle mb-4">
                            <thead class="table-light">
                                <tr>
                                    <th>Description</th>
                                    <th class="text-end" style="width: 160px;">Amount (GHS)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        {{ $booking->room_type }} - {{ $booking->gender ?? 'General' }}
                                        <div class="small text-muted">Hostel accommodation for the academic year {{ $booking->academic_year ?? 'N/A' }}</div>
                                    </td>
                                    <td class="text-end fw-semibold">{{ number_format($booking->amount, 2) }}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="text-end">Total Due</th>
                                    <th class="text-end text-primary">GHS{{ number_format($booking->amount, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>

                        @php($detail = $booking->hostel->paymentDetail)
                        @php($payableOnline = ! $booking->isPaid() && $detail && $detail->hasPaystack())
                        @php($momoApiEnabled = ! $booking->isPaid() && $detail && $detail->hasMtnMoMoApi())
                        @if ($booking->isPaid())
                            <div class="alert alert-success small border-0 mb-4">
                                <strong>Paid.</strong> GHS{{ number_format($booking->amount, 2) }} received
                                @if ($booking->paid_at)
                                    on {{ $booking->paid_at->format('j M Y, g:i a') }}
                                @endif
                                @if ($booking->paystack_transaction_ref)
                                    &mdash; {{ $booking->gatewayLabel() }} transaction {{ $booking->paystack_transaction_ref }}.
                                @elseif ($booking->provider_reference)
                                    &mdash; {{ $booking->gatewayLabel() }} transaction {{ $booking->provider_reference }}.
                                @endif
                            </div>
                        @elseif ($booking->isMomoPending())
                            <div class="alert alert-warning small border-0 mb-4">
                                <strong>MTN MoMo payment pending:</strong> a payment request was sent to
                                <strong>{{ $booking->momo_phone }}</strong>. Approve it on your phone, then confirm the payment.
                                @auth
                                    <form method="POST" action="{{ route('booking.pay.momo.status', $booking->id) }}" class="d-print-none d-inline-block ms-1">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary mb-0">Confirm Payment</button>
                                    </form>
                                @else
                                    <a href="{{ route('login') }}" class="d-print-none d-inline-block alert-link ms-1">Log in to confirm</a>
                                @endauth
                            </div>
                        @elseif ($payableOnline || $momoApiEnabled)
                            <div class="alert alert-primary small border-0 mb-4">
                                <strong>Pay online:</strong> choose a gateway below. Your booking is confirmed automatically once payment succeeds. Manual payment options below remain available.
                                @auth
                                    @if ($payableOnline)
                                        <form method="POST" action="{{ route('booking.pay', $booking->id) }}" class="d-print-none d-inline-block ms-1">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary mb-0">Pay GHS{{ number_format($booking->amount, 2) }} with Paystack</button>
                                        </form>
                                    @endif
                                    @if ($momoApiEnabled)
                                        <form method="POST" action="{{ route('booking.pay.momo', $booking->id) }}" class="d-print-none d-inline-block ms-1">
                                            @csrf
                                            <input type="tel" name="phone" class="form-control form-control-sm d-inline-block" style="width: 180px;" placeholder="MoMo number (024...)" required maxlength="20" value="{{ old('phone', $booking->phone) }}">
                                            <button type="submit" class="btn btn-sm btn-primary mb-0">Pay with MTN MoMo</button>
                                        </form>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="alert-link">Log in to pay online</a>
                                @endauth
                            </div>
                        @else
                            <div class="alert alert-warning small border-0 mb-4">
                                <strong>Payment instructions:</strong> Pay the total amount using any of the options listed below, then send your payment confirmation to info@hosteldock.com. Your booking is confirmed once the hostel confirms receipt.
                            </div>
                        @endif

                        @if ($detail && $detail->is_active && ($detail->hasMomo() || $detail->bank_name || $detail->bank_account_number))
                            <div class="small mb-4">
                                <strong class="d-block mb-2">Payment options:</strong>
                                @if ($detail->hasMomo())
                                    <div class="border rounded-2 p-2 mb-2">
                                        <strong>Mobile Money{{ $detail->momo_provider ? ' (' . $detail->momo_provider . ')' : '' }}</strong>
                                        <span class="text-muted"> &middot; Pay to: {{ $detail->momo_number }}</span>
                                        @if ($detail->momo_account_name)
                                            <p class="text-muted mb-0">{{ $detail->momo_account_name }}</p>
                                        @endif
                                    </div>
                                @endif
                                @if ($detail->bank_name || $detail->bank_account_number)
                                    <div class="border rounded-2 p-2 mb-2">
                                        <strong>Bank Transfer{{ $detail->bank_name ? ' (' . $detail->bank_name . ')' : '' }}</strong>
                                        @if ($detail->bank_account_number)
                                            <span class="text-muted"> &middot; Pay to: {{ $detail->bank_account_number }}</span>
                                        @endif
                                        @if ($detail->bank_account_name)
                                            <p class="text-muted mb-0">{{ $detail->bank_account_name }}</p>
                                        @endif
                                    </div>
                                @endif
                                @if ($detail->instructions)
                                    <p class="text-muted mb-0"><i class="bi bi-info-circle me-1"></i>{{ $detail->instructions }}</p>
                                @endif
                            </div>
                        @else
                            @php($gateways = \App\Models\PaymentGateway::active()->get())
                            @if ($gateways->isNotEmpty())
                                <div class="small mb-4">
                                    <strong class="d-block mb-2">Payment options:</strong>
                                    @foreach ($gateways as $gateway)
                                        <div class="border rounded-2 p-2 mb-2">
                                            <strong>{{ $gateway->name }}</strong>
                                            @if ($gateway->account_number)
                                                <span class="text-muted"> &middot; Pay to: {{ $gateway->account_number }}</span>
                                            @endif
                                            @if ($gateway->instructions)
                                                <p class="text-muted mb-0">{{ $gateway->instructions }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endif

                        <div class="d-flex justify-content-between small text-muted">
                            <span>
                                Status: <span class="badge
                                    @if ($booking->status === 'confirmed') bg-success
                                    @elseif ($booking->status === 'cancelled') bg-danger
                                    @else bg-warning text-dark @endif">{{ ucfirst($booking->status) }}</span>
                                &middot; Payment:
                                <span class="badge
                                    @if ($booking->isPaid()) bg-success
                                    @elseif ($booking->payment_status === \App\Models\Booking::PAYMENT_PENDING) bg-warning text-dark
                                    @elseif ($booking->payment_status === \App\Models\Booking::PAYMENT_FAILED) bg-danger
                                    @else bg-secondary @endif">{{ $booking->paymentStatusLabel() }}</span>
                            </span>
                            <span>Powered by HostelDock</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection