@extends('admin.layouts.admin')

@section('title', 'Dashboard')
@section('heading', 'Manager Dashboard')
@section('subheading', 'Overview of the hostels assigned to you')

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow rounded-3 p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small mb-1">My Hostels</p>
                        <h4 class="mb-0">{{ $stats['hostels'] }}</h4>
                    </div>
                    <i class="bi bi-building fs-2 text-primary"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow rounded-3 p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small mb-1">Rooms Listed</p>
                        <h4 class="mb-0">{{ $stats['rooms'] }}</h4>
                    </div>
                    <i class="bi bi-door-open fs-2 text-success"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow rounded-3 p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small mb-1">Total Bookings</p>
                        <h4 class="mb-0">{{ $stats['bookings'] }}</h4>
                        <small class="text-muted">{{ $stats['pending_bookings'] }} pending &middot; {{ $stats['confirmed_bookings'] }} confirmed</small>
                    </div>
                    <i class="bi bi-bookmark-check fs-2 text-warning"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow rounded-3 p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small mb-1">Confirmed Revenue</p>
                        <h4 class="mb-0">GHS{{ number_format($stats['revenue'], 2) }}</h4>
                    </div>
                    <i class="bi bi-cash-stack fs-2 text-danger"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card shadow rounded-3">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                    <h6 class="mb-0">Recent Bookings</h6>
                    <a href="{{ route('manager.bookings.index') }}" class="btn btn-sm btn-primary-soft mb-0">View all</a>
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
                                    <td colspan="5" class="text-center text-muted py-4">No bookings for your hostels yet.</td>
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
                    <h6 class="mb-0">My Hostels</h6>
                </div>
                <ul class="list-group list-group-flush">
                    @forelse ($hostels as $hostel)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="small fw-semibold">{{ $hostel->displayName() }}</div>
                                <small class="text-muted">{{ $hostel->rooms_count }} rooms
                                    @if ($hostel->pending_bookings > 0)
                                        &middot; <span class="text-warning fw-semibold">{{ $hostel->pending_bookings }} pending booking(s)</span>
                                    @endif
                                </small>
                            </div>
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('manager.hostels.edit', $hostel) }}" class="btn btn-sm btn-primary-soft mb-0"><i class="bi bi-sliders"></i></a>
                                <a href="{{ route('manager.hostels.rooms.index', $hostel) }}" class="btn btn-sm btn-light mb-0"><i class="bi bi-door-open"></i></a>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-muted text-center py-4">
                            No hostels assigned to you yet.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection