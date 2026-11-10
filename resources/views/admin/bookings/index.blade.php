@extends('admin.layouts.admin')

@section('title', 'Bookings')
@section('heading', 'Bookings')
@section('subheading', 'Review and update booking statuses')

@section('content')
    <div class="card shadow rounded-3">
        <div class="card-header bg-transparent py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <form method="GET" class="d-flex flex-wrap gap-2">
                <div class="input-group" style="width: 240px;">
                    <span class="input-group-text bg-body border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="Ref, student, email">
                </div>
                <select class="form-select" name="status" style="width: 160px;">
                    @foreach ($statuses as $key => $label)
                        <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                <button class="btn btn-primary mb-0" type="submit">Filter</button>
                @if (request('q') || request('status'))
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-light mb-0">Reset</a>
                @endif
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-muted small">
                        <th>Ref / Date</th>
                        <th>Student</th>
                        <th>Hostel / Room</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bookings as $booking)
                        <tr>
                            <td>
                                <span class="fw-semibold">{{ $booking->booking_ref }}</span>
                                <small class="text-muted d-block">{{ $booking->created_at->format('d M Y, H:i') }}</small>
                            </td>
                            <td>
                                <div>{{ $booking->student_name }}</div>
                                <small class="text-muted">{{ $booking->email }} &middot; {{ $booking->phone }}</small>
                            </td>
                            <td>
                                <div>{{ \Illuminate\Support\Str::limit(optional($booking->hostel)->displayName(), 24) }}</div>
                                <small class="text-muted">{{ $booking->room_type }} &middot; {{ $booking->academic_year }}</small>
                            </td>
                            <td class="fw-semibold">GHS{{ number_format($booking->amount, 2) }}</td>
                            <td>
                                @if ($booking->status === 'confirmed')
                                    <span class="badge bg-success">Confirmed</span>
                                @elseif ($booking->status === 'cancelled')
                                    <span class="badge bg-danger">Cancelled</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                                @if ($booking->isPaid())
                                    <span class="badge bg-success d-block mt-1"><i class="bi bi-check2-circle me-1"></i>Paid
                                        @if ($booking->paid_at)
                                            {{ $booking->paid_at->format('d M') }}
                                        @endif
                                    </span>
                                @elseif ($booking->payment_status === \App\Models\Booking::PAYMENT_FAILED)
                                    <span class="badge bg-danger d-block mt-1">Payment Failed</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('booking.invoice', $booking->booking_ref) }}" target="_blank" class="btn btn-sm btn-light mb-0"><i class="bi bi-file-earmark-text"></i></a>
                                    @if ($booking->status !== 'cancelled')
                                        <form method="POST" action="{{ route('admin.bookings.status', $booking) }}">
                                            @csrf
                                            @if ($booking->status === 'pending')
                                                <button type="submit" name="status" value="confirmed" class="btn btn-sm btn-success mb-0">Confirm</button>
                                                <button type="submit" name="status" value="cancelled" class="btn btn-sm btn-danger-soft mb-0">Cancel</button>
                                            @else
                                                <button type="submit" name="status" value="pending" class="btn btn-sm btn-warning mb-0">Reopen</button>
                                            @endif
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No bookings found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-transparent d-flex justify-content-between align-items-center py-3">
            <small class="text-muted">{{ $bookings->total() }} bookings</small>
            {{ $bookings->links() }}
        </div>
    </div>
@endsection