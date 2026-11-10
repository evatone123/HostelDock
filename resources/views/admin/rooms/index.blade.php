@extends('admin.layouts.admin')

@section('title', 'Rooms')
@section('heading', 'Rooms')
@section('subheading', $hostel->displayName())

@section('content')
    <div class="card shadow rounded-3">
        <div class="card-header bg-transparent py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.hostels.index') }}" class="btn btn-sm btn-light mb-0"><i class="bi bi-arrow-left"></i></a>
                <div>
                    <h6 class="mb-0">{{ $hostel->displayName() }}</h6>
                    <small class="text-muted">{{ $hostel->rooms->count() }} rooms &middot; from GHS{{ number_format($hostel->starting_price, 2) }} &middot; {{ $hostel->availableRooms() }} beds available in total</small>
                </div>
            </div>
            <a href="{{ route('admin.hostels.rooms.create', $hostel) }}" class="btn btn-primary mb-0"><i class="bi bi-door-open me-1"></i>Add Room</a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-muted small">
                        <th>Room</th>
                        <th>Type</th>
                        <th>Gender</th>
                        <th>Price</th>
                        <th>Rooms</th>
                        <th>Beds Free</th>
                        <th>Occupants</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rooms as $room)
                        <tr>
                            <td class="fw-semibold">{{ $room->name }}</td>
                            <td>{{ $room->room_type }}</td>
                            <td>
                                @if ($room->gender_category === 'Male')
                                    <i class="fa fa-male me-1 text-primary"></i>
                                @elseif ($room->gender_category === 'Female')
                                    <i class="fa fa-female me-1 text-primary"></i>
                                @else
                                    <i class="fa fa-genderless me-1 text-muted"></i>
                                @endif
                                {{ $room->gender_category }}
                            </td>
                            <td>GHS{{ number_format($room->price, 2) }}</td>
                            <td>{{ $room->total_rooms }}</td>
                            <td>
                                <span class="badge {{ $room->availableSpace() > 0 ? 'bg-success' : 'bg-danger' }}">{{ $room->availableSpace() }} / {{ $room->totalBeds() }}</span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-light border d-inline-flex align-items-center gap-1 mb-0"
                                    data-bs-toggle="collapse" href="#room-occupants-{{ $room->id }}" aria-expanded="false" aria-controls="room-occupants-{{ $room->id }}">
                                    <i class="bi bi-people"></i>{{ $room->bookings->count() }}
                                </button>
                            </td>
                            <td>
                                @if ($room->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Hidden</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('admin.hostels.rooms.edit', [$hostel, $room]) }}" class="btn btn-sm btn-primary-soft mb-0"><i class="bi bi-pencil"></i> Edit</a>
                                    <form method="POST" action="{{ route('admin.hostels.rooms.toggle', [$hostel, $room]) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $room->is_active ? 'btn-warning' : 'btn-success' }} mb-0" title="{{ $room->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="bi bi-{{ $room->is_active ? 'eye-slash' : 'eye' }}"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.hostels.rooms.destroy', [$hostel, $room]) }}"
                                        onsubmit="return confirm('Delete room {{ addslashes($room->name) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger-soft mb-0"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <tr class="collapse" id="room-occupants-{{ $room->id }}">
                            <td colspan="9">
                                <div class="p-3">
                                    @if ($room->bookings->isNotEmpty())
                                        <div class="table-responsive">
                                            <table class="table table-sm align-middle mb-0">
                                                <thead>
                                                    <tr class="text-muted small">
                                                        <th>Student</th>
                                                        <th>Phone</th>
                                                        <th>Email</th>
                                                        <th>Booking Ref</th>
                                                        <th>Status</th>
                                                        <th>Payment</th>
                                                        <th>Occupancy</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($room->bookings as $mate)
                                                        <tr>
                                                            <td class="fw-semibold">
                                                                {{ $mate->student_name }}
                                                                @if ($mate->gender)
                                                                    <small class="text-muted">&middot; {{ $mate->gender }}</small>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($mate->phone)
                                                                    <a href="tel:{{ $mate->phone }}" class="text-decoration-none">{{ $mate->phone }}</a>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($mate->email)
                                                                    <a href="mailto:{{ $mate->email }}" class="text-decoration-none small">{{ $mate->email }}</a>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                            <td class="small">{{ $mate->booking_ref }}</td>
                                                            <td>
                                                                @if ($mate->status === 'confirmed')
                                                                    <span class="badge bg-success">Confirmed</span>
                                                                @elseif ($mate->status === 'cancelled')
                                                                    <span class="badge bg-danger">Cancelled</span>
                                                                @else
                                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($mate->isPaid())
                                                                    <span class="badge bg-success">Paid</span>
                                                                @elseif ($mate->payment_status === \App\Models\Booking::PAYMENT_PENDING)
                                                                    <span class="badge bg-warning text-dark">Pay Pending</span>
                                                                @else
                                                                    <span class="badge bg-secondary">Unpaid</span>
                                                                @endif
                                                            </td>
                                                            <td class="small">
                                                                @if ($mate->status === 'confirmed' && $mate->isOccupying())
                                                                    <span class="badge bg-success">Until {{ $mate->expires_at ? $mate->expires_at->format('j M y') : 'N/A' }}</span>
                                                                    @if ($mate->isWithinRenewalWindow())
                                                                        <span class="badge bg-warning text-dark mt-1">Renewal window</span>
                                                                    @endif
                                                                    @if ($mate->isRenewalLocked())
                                                                        <span class="badge bg-info text-dark mt-1">Locked</span>
                                                                    @endif
                                                                @elseif ($mate->status === 'confirmed')
                                                                    <span class="badge bg-secondary">Expired {{ $mate->expires_at ? $mate->expires_at->format('j M y') : '' }}</span>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="small text-muted mb-0"><i class="bi bi-people me-1"></i>No occupants in this room yet.</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                No rooms yet. <a class="text-primary" href="{{ route('admin.hostels.rooms.create', $hostel) }}">Add the first room</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection