@extends('admin.layouts.admin')

@section('title', 'Manage Hostels')
@section('heading', 'Hostels')
@section('subheading', 'Set up and manage hostels on the platform')

@section('content')
    <div class="card shadow rounded-3">
        <div class="card-header bg-transparent py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <form method="GET" class="d-flex flex-wrap gap-2">
                <div class="input-group" style="width: 240px;">
                    <span class="input-group-text bg-body border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="Search hostels">
                </div>
                <select class="form-select" name="institution" style="width: 220px;">
                    <option value="">All institutions</option>
                    @foreach (\App\Models\Hostel::institutions() as $key => $label)
                        @if ($key !== '')
                            <option value="{{ $key }}" @selected(request('institution') === $key)>{{ $label }}</option>
                        @endif
                    @endforeach
                </select>
                <button class="btn btn-primary mb-0" type="submit">Filter</button>
                @if (request('q') || request('institution'))
                    <a href="{{ route('admin.hostels.index') }}" class="btn btn-light mb-0">Reset</a>
                @endif
            </form>
            <a href="{{ route('admin.hostels.create') }}" class="btn btn-primary mb-0"><i class="bi bi-plus-lg me-1"></i>Add Hostel</a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-muted small">
                        <th style="min-width: 280px;">Hostel</th>
                        <th>Institution</th>
                        <th>Rooms</th>
                        <th>Starting Price</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($hostels as $hostel)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ asset($hostel->coverImagePath()) }}"
                                        class="rounded-2 object-fit-cover flex-shrink-0" style="width: 52px; height: 40px;" alt="">
                                    <div>
                                        <div class="fw-semibold">{{ $hostel->displayName() }}</div>
                                        <small class="text-muted">{{ $hostel->location }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted small">{{ $hostel->institutionLabel() ?? '—' }}</td>
                            <td>
                                <a class="text-body text-primary-hover fw-semibold" href="{{ route('admin.hostels.rooms.index', $hostel) }}">
                                    {{ $hostel->rooms_count }}
                                </a>
                            </td>
                            <td>GHS{{ number_format($hostel->starting_price, 2) }}</td>
                            <td>
                                @if ($hostel->is_active)
                                    <span class="badge bg-success">Live</span>
                                @else
                                    <span class="badge bg-secondary">Hidden</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('admin.hostels.payments.edit', $hostel) }}" class="btn btn-sm btn-primary-soft mb-0"><i class="bi bi-credit-card"></i> Payments</a>
                                    <a href="{{ route('admin.hostels.edit', $hostel) }}" class="btn btn-sm btn-primary-soft mb-0"><i class="bi bi-pencil"></i> Edit</a>
                                    <a href="{{ route('admin.hostels.rooms.index', $hostel) }}" class="btn btn-sm btn-light mb-0"><i class="bi bi-door-open"></i> Rooms</a>
                                    <form method="POST" action="{{ route('admin.hostels.toggle', $hostel) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $hostel->is_active ? 'btn-warning' : 'btn-success' }} mb-0" title="{{ $hostel->is_active ? 'Deactivate (hide from website)' : 'Activate (show on website)' }}">
                                            <i class="bi bi-{{ $hostel->is_active ? 'eye-slash' : 'eye' }}"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.hostels.destroy', $hostel) }}"
                                        onsubmit="return confirm('Delete {{ addslashes($hostel->displayName()) }} and ALL its rooms & images?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger-soft mb-0"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No hostels found.</td>
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