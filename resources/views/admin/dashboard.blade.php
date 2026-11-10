@extends('admin.layouts.admin')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')
@section('subheading', 'Overview of your platform')

@section('content')
    <!-- Section Header: Financial Overview -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 text-dark fw-bold"><i class="bi bi-wallet2 text-primary me-2"></i>Financial & Revenue Overview</h5>
        <span class="badge bg-primary-soft text-primary px-3 py-2">All Payment Amounts</span>
    </div>

    <!-- Revenue & Payments Overview Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow rounded-3 p-3 h-100 border-start border-primary border-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-primary text-white small fw-semibold">UNLOCK ACCESS</span>
                    <i class="bi bi-ticket-perforated fs-4 text-primary"></i>
                </div>
                <div>
                    <p class="text-muted small mb-1 fw-semibold text-uppercase">Booking Token Payments</p>
                    <h4 class="mb-1 text-primary fw-bold">GHS{{ number_format($stats['booking_token_revenue'], 2) }}</h4>
                    <small class="text-muted">{{ $stats['booking_token_count'] }} paid booking tokens</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow rounded-3 p-3 h-100 border-start border-success border-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-success bg-opacity-15 text-success small fw-semibold">ROOM RESERVATIONS</span>
                    <i class="bi bi-cash-stack fs-4 text-success"></i>
                </div>
                <div>
                    <p class="text-muted small mb-1 fw-semibold text-uppercase">Confirmed Bookings Payments</p>
                    <h4 class="mb-1 text-success fw-bold">GHS{{ number_format($stats['confirmed_bookings_revenue'], 2) }}</h4>
                    <small class="text-muted">{{ $stats['confirmed_bookings'] }} confirmed bookings</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow rounded-3 p-3 h-100 border-start border-warning border-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-warning bg-opacity-25 text-dark small fw-semibold">TOTAL EARNINGS</span>
                    <i class="bi bi-wallet2 fs-4 text-warning"></i>
                </div>
                <div>
                    <p class="text-muted small mb-1 fw-semibold text-uppercase">Total Platform Revenue</p>
                    <h4 class="mb-1 text-dark fw-bold">GHS{{ number_format($stats['total_revenue'], 2) }}</h4>
                    <small class="text-muted">Tokens + Confirmed Bookings</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Header: Platform Statistics -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 text-dark fw-bold"><i class="bi bi-speedometer2 text-success me-2"></i>Platform Statistics</h5>
        <span class="badge bg-success-soft text-success px-3 py-2">General Metrics</span>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow rounded-3 p-3 h-100 border-start border-info border-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-info bg-opacity-15 text-info small fw-semibold">PROPERTIES</span>
                    <i class="bi bi-building fs-4 text-info"></i>
                </div>
                <div>
                    <p class="text-muted small mb-1 fw-semibold text-uppercase">Hostels</p>
                    <h4 class="mb-0 fw-bold">{{ $stats['hostels'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow rounded-3 p-3 h-100 border-start border-success border-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-success bg-opacity-15 text-success small fw-semibold">INVENTORY</span>
                    <i class="bi bi-door-open fs-4 text-success"></i>
                </div>
                <div>
                    <p class="text-muted small mb-1 fw-semibold text-uppercase">Rooms Listed</p>
                    <h4 class="mb-0 fw-bold">{{ $stats['rooms'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow rounded-3 p-3 h-100 border-start border-warning border-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-warning bg-opacity-25 text-dark small fw-semibold">RESERVATIONS</span>
                    <i class="bi bi-bookmark-check fs-4 text-warning"></i>
                </div>
                <div>
                    <p class="text-muted small mb-1 fw-semibold text-uppercase">Total Bookings</p>
                    <h4 class="mb-0 fw-bold">{{ $stats['bookings'] }}</h4>
                    <small class="text-muted">{{ $stats['pending_bookings'] }} pending &middot; {{ $stats['confirmed_bookings'] }} confirmed</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow rounded-3 p-3 h-100 border-start border-danger border-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-danger bg-opacity-15 text-danger small fw-semibold">ACCOUNTS</span>
                    <i class="bi bi-people fs-4 text-danger"></i>
                </div>
                <div>
                    <p class="text-muted small mb-1 fw-semibold text-uppercase">User Accounts</p>
                    <h4 class="mb-0 fw-bold">{{ $stats['users'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card shadow rounded-3 mb-4">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                    <h6 class="mb-0">Recent Confirmed Bookings & Payments</h6>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-primary-soft mb-0">View all</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr class="text-muted small">
                                <th>Ref</th>
                                <th>Student</th>
                                <th>Hostel</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentBookings as $booking)
                                <tr>
                                    <td><span class="fw-semibold">{{ $booking->booking_ref }}</span></td>
                                    <td>
                                        <div>{{ $booking->student_name }}</div>
                                        <small class="text-muted">{{ $booking->room_type }} &middot; {{ $booking->academic_year }}</small>
                                    </td>
                                    <td>{{ \Illuminate\Support\Str::limit(optional($booking->hostel)->displayName(), 22) }}</td>
                                    <td>GHS{{ number_format($booking->amount, 2) }}</td>
                                    <td>
                                        @if ($booking->status === 'confirmed')
                                            <span class="badge bg-success">Confirmed</span>
                                        @elseif ($booking->status === 'cancelled')
                                            <span class="badge bg-danger">Cancelled</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No bookings yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card shadow rounded-3">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                    <h6 class="mb-0">Recent Booking Token Payments (Unlock Access)</h6>
                    <a href="{{ route('admin.tokens.index') }}" class="btn btn-sm btn-primary-soft mb-0">View all</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr class="text-muted small">
                                <th>Token Ref</th>
                                <th>Student</th>
                                <th>Amount</th>
                                <th>Paid At</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentTokenPayments as $token)
                                <tr>
                                    <td><span class="fw-semibold">{{ $token->token_ref }}</span></td>
                                    <td>
                                        <div>{{ optional($token->user)->name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ optional($token->user)->email }}</small>
                                    </td>
                                    <td class="fw-semibold text-primary">GHS{{ number_format($token->amount, 2) }}</td>
                                    <td><small>{{ optional($token->paid_at)->format('M d, Y H:i') ?? $token->updated_at->format('M d, Y H:i') }}</small></td>
                                    <td>
                                        <span class="badge bg-success">{{ ucfirst($token->status) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No token payments yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow rounded-3 mb-3">
                <div class="card-header bg-transparent py-3">
                    <h6 class="mb-0">Low Availability Rooms</h6>
                </div>
                <ul class="list-group list-group-flush">
                    @forelse ($lowRooms as $room)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="small fw-semibold">{{ $room->name }}</div>
                                <small class="text-muted">{{ optional($room->hostel)->displayName() }}</small>
                            </div>
                            <span class="badge {{ $room->availableSpace() > 0 ? 'bg-warning text-dark' : 'bg-danger' }}">
                                {{ $room->availableSpace() }} bed{{ $room->availableSpace() === 1 ? '' : 's' }} left
                            </span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted text-center py-4">All rooms have full availability.</li>
                    @endforelse
                </ul>
            </div>

            <div class="card shadow rounded-3">
                <div class="card-header bg-transparent py-3">
                    <h6 class="mb-0">Hostel Payment Gateways</h6>
                </div>
                <div class="card-body pt-0">
                    <p class="small mb-3">Per-hostel gateways shown to students on the booking confirmation and invoice pages.</p>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['hostel_gateways_configured'] }}</h4>
                            <small class="text-muted">hostels with an online gateway (Paystack / MTN MoMo)</small>
                        </div>
                        <a href="{{ route('admin.hostels.index') }}" class="btn btn-sm btn-primary-soft mb-0">Hostels</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection