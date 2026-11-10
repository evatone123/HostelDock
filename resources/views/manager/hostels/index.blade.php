@extends('admin.layouts.admin')

@section('title', 'My Hostels')
@section('heading', 'My Hostels')
@section('subheading', 'Manage the hostels assigned to you by the administrator')

@section('content')
    <div class="card shadow rounded-3">
        <div class="card-header bg-transparent py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <form method="GET" class="d-flex flex-wrap gap-2">
                <div class="input-group" style="width: 260px;">
                    <span class="input-group-text bg-body border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="Search hostel name">
                </div>
                <button class="btn btn-primary mb-0" type="submit">Search</button>
                @if (request('q'))
                    <a href="{{ route('manager.hostels.index') }}" class="btn btn-light mb-0">Reset</a>
                @endif
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-muted small">
                        <th>Hostel</th>
                        <th>Location / Institution</th>
                        <th>Rooms</th>
                        <th>Bookings</th>
                        <th>Rate</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($hostels as $hostel)
                        <tr>
                            <td>
                                <span class="fw-semibold">{{ $hostel->displayName() }}</span>
                                <small class="text-muted d-block">from GHS{{ number_format($hostel->starting_price, 2) }}</small>
                            </td>
                            <td>
                                <div>{{ $hostel->location }}</div>
                                <small class="text-muted">{{ $hostel->institutionLabel() }}</small>
                            </td>
                            <td>{{ $hostel->rooms_count }}</td>
                            <td>{{ $hostel->bookings_count }}</td>
                            <td>
                                <i class="bi bi-star-fill text-warning me-1"></i>{{ number_format($hostel->rating, 1) }}
                            </td>
                            <td>
                                @if ($hostel->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Hidden</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('manager.hostels.payments.edit', $hostel) }}" class="btn btn-sm btn-primary-soft mb-0"><i class="bi bi-credit-card"></i> Payments</a>
                                    <a href="{{ route('manager.hostels.rooms.index', $hostel) }}" class="btn btn-sm btn-primary-soft mb-0"><i class="bi bi-door-open"></i> Rooms</a>
                                    <a href="{{ route('manager.hostels.edit', $hostel) }}" class="btn btn-sm btn-light mb-0"><i class="bi bi-pencil"></i> Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No hostels assigned to you. Ask an administrator to assign hostel(s) to your account.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-transparent d-flex justify-content-between align-items-center py-3">
            <small class="text-muted">{{ $hostels->total() }} hostels</small>
            {{ $hostels->links() }}
        </div>
    </div>
@endsection