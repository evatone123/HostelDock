@extends('admin.layouts.admin')

@section('title', 'Manage Amenities')
@section('heading', 'Amenities')
@section('subheading', 'Add and manage the amenities that can be applied to rooms')

@section('content')
    <div class="card shadow rounded-3">
        <div class="card-header bg-transparent py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <form method="GET" class="d-flex flex-wrap gap-2">
                <div class="input-group" style="width: 260px;">
                    <span class="input-group-text bg-body border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="Search amenities">
                </div>
                <button class="btn btn-primary mb-0" type="submit">Filter</button>
                @if (request('q'))
                    <a href="{{ route('admin.amenities.index') }}" class="btn btn-light mb-0">Reset</a>
                @endif
            </form>
            <a href="{{ route('admin.amenities.create') }}" class="btn btn-primary mb-0"><i class="bi bi-plus-lg me-1"></i>Add Amenity</a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-muted small">
                        <th>Amenity</th>
                        <th>Icon</th>
                        <th>Used By</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($amenities as $amenity)
                        <tr>
                            <td class="fw-semibold">{{ $amenity->name }}</td>
                            <td>
                                <i class="bi {{ $amenity->icon }} text-primary"></i>
                                <code class="small text-muted ms-1">{{ $amenity->icon }}</code>
                            </td>
                            <td>{{ $amenity->rooms_count }} room{{ $amenity->rooms_count === 1 ? '' : 's' }}</td>
                            <td>
                                @if ($amenity->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Hidden</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('admin.amenities.edit', $amenity) }}" class="btn btn-sm btn-primary-soft mb-0"><i class="bi bi-pencil"></i> Edit</a>
                                    <form method="POST" action="{{ route('admin.amenities.toggle', $amenity) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $amenity->is_active ? 'btn-warning' : 'btn-success' }} mb-0" title="{{ $amenity->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="bi bi-{{ $amenity->is_active ? 'eye-slash' : 'eye' }}"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.amenities.destroy', $amenity) }}"
                                        onsubmit="return confirm('Delete amenity {{ addslashes($amenity->name) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger-soft mb-0"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No amenities found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-transparent d-flex justify-content-between align-items-center py-3">
            <small class="text-muted">{{ $amenities->total() }} amenities</small>
            {{ $amenities->links() }}
        </div>
    </div>
@endsection