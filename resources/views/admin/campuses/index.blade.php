@extends('admin.layouts.admin')

@section('title', 'Manage Campuses')
@section('heading', 'Campuses')
@section('subheading', 'Add and manage the campuses hostels link to on the platform')

@section('content')
    <div class="card shadow rounded-3">
        <div class="card-header bg-transparent py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <form method="GET" class="d-flex flex-wrap gap-2">
                <div class="input-group" style="width: 260px;">
                    <span class="input-group-text bg-body border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="Search campuses">
                </div>
                <button class="btn btn-primary mb-0" type="submit">Filter</button>
                @if (request('q'))
                    <a href="{{ route('admin.campuses.index') }}" class="btn btn-light mb-0">Reset</a>
                @endif
            </form>
            <a href="{{ route('admin.campuses.create') }}" class="btn btn-primary mb-0"><i class="bi bi-plus-lg me-1"></i>Add Campus</a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-muted small">
                        <th>Campus</th>
                        <th>Slug</th>
                        <th>Linked Hostels</th>
                        <th>Map Coordinates</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($campuses as $campus)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $campus->name }}</div>
                                @if ($campus->short_name)
                                    <small class="text-muted">{{ $campus->short_name }}</small>
                                @endif
                            </td>
                            <td><code class="small">{{ $campus->slug }}</code></td>
                            <td>
                                <a class="text-body text-primary-hover fw-semibold" href="{{ route('admin.hostels.index', ['institution' => $campus->slug]) }}">
                                    {{ $campus->hostels_count }}
                                </a>
                            </td>
                            <td class="text-muted small">
                                @if ($campus->latitude !== null && $campus->longitude !== null)
                                    {{ number_format((float) $campus->latitude, 6) }}, {{ number_format((float) $campus->longitude, 6) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if ($campus->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Hidden</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('admin.campuses.edit', $campus) }}" class="btn btn-sm btn-primary-soft mb-0"><i class="bi bi-pencil"></i> Edit</a>
                                    <form method="POST" action="{{ route('admin.campuses.toggle', $campus) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $campus->is_active ? 'btn-warning' : 'btn-success' }} mb-0" title="{{ $campus->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="bi bi-{{ $campus->is_active ? 'eye-slash' : 'eye' }}"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.campuses.destroy', $campus) }}"
                                        onsubmit="return confirm('Delete {{ addslashes($campus->name) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger-soft mb-0"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No campuses found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-transparent d-flex justify-content-between align-items-center py-3">
            <small class="text-muted">{{ $campuses->total() }} campuses</small>
            {{ $campuses->links() }}
        </div>
    </div>
@endsection