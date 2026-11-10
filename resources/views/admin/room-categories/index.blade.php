@extends('admin.layouts.admin')

@section('title', 'Manage Room Categories')
@section('heading', 'Room Categories')
@section('subheading', 'Add and manage the categories that can be applied to rooms')

@section('content')
    <div class="card shadow rounded-3">
        <div class="card-header bg-transparent py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <form method="GET" class="d-flex flex-wrap gap-2">
                <div class="input-group" style="width: 260px;">
                    <span class="input-group-text bg-body border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="Search categories">
                </div>
                <button class="btn btn-primary mb-0" type="submit">Filter</button>
                @if (request('q'))
                    <a href="{{ route('admin.room-categories.index') }}" class="btn btn-light mb-0">Reset</a>
                @endif
            </form>
            <a href="{{ route('admin.room-categories.create') }}" class="btn btn-primary mb-0"><i class="bi bi-plus-lg me-1"></i>Add Category</a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-muted small">
                        <th>Category</th>
                        <th>People Per Room</th>
                        <th>Used By</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td class="fw-semibold">{{ $category->name }}</td>
                            <td>{{ $category->capacity }}</td>
                            <td>{{ $category->rooms_count }} room{{ $category->rooms_count === 1 ? '' : 's' }}</td>
                            <td>
                                @if ($category->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Hidden</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('admin.room-categories.edit', $category) }}" class="btn btn-sm btn-primary-soft mb-0"><i class="bi bi-pencil"></i> Edit</a>
                                    <form method="POST" action="{{ route('admin.room-categories.toggle', $category) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $category->is_active ? 'btn-warning' : 'btn-success' }} mb-0" title="{{ $category->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="bi bi-{{ $category->is_active ? 'eye-slash' : 'eye' }}"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.room-categories.destroy', $category) }}"
                                        onsubmit="return confirm('Delete category {{ addslashes($category->name) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger-soft mb-0"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No room categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-transparent d-flex justify-content-between align-items-center py-3">
            <small class="text-muted">{{ $categories->total() }} categories</small>
            {{ $categories->links() }}
        </div>
    </div>
@endsection
