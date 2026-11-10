@extends('admin.layouts.admin')

@section('title', 'Manage Users')
@section('heading', 'Users')
@section('subheading', 'Manage student, hostel manager and admin accounts')

@section('content')
    <div class="card shadow rounded-3">
        <div class="card-header bg-transparent py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <form method="GET" class="d-flex flex-wrap gap-2">
                <div class="input-group" style="width: 260px;">
                    <span class="input-group-text bg-body border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="Search name, email, phone">
                </div>
                <select class="form-select" name="role" style="width: 180px;">
                    <option value="">All roles</option>
                    <option value="student" @selected(request('role') === 'student')>Students</option>
                    <option value="manager" @selected(request('role') === 'manager')>Hostel Managers</option>
                    <option value="admin" @selected(request('role') === 'admin')>Admins</option>
                </select>
                <button class="btn btn-primary mb-0" type="submit">Filter</button>
                @if (request('q') || request('role'))
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light mb-0">Reset</a>
                @endif
            </form>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary mb-0"><i class="bi bi-person-plus me-1"></i>Add User</a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-muted small">
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td class="fw-semibold">
                                <i class="bi bi-person-circle me-2 text-primary"></i>{{ $user->name }}
                                @if ($user->id === auth()->id())
                                    <span class="badge bg-primary-soft text-primary ms-1">You</span>
                                @endif
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone ?? '—' }}</td>
                            <td>
                                @if ($user->isAdmin())
                                    <span class="badge bg-success">Admin</span>
                                @elseif ($user->isManager())
                                    <span class="badge bg-info text-dark">Hostel Manager</span>
                                    <div class="small text-muted"><i class="bi bi-building me-1"></i>{{ $user->managed_hostels_count }} {{ \Illuminate\Support\Str::plural('hostel', $user->managed_hostels_count) }}</div>
                                @else
                                    <span class="badge bg-light text-dark border">Student</span>
                                @endif
                            </td>
                            <td><small class="text-muted">{{ $user->created_at->format('d M Y') }}</small></td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary-soft mb-0"><i class="bi bi-pencil"></i> Edit</a>
                                    @if ($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                            onsubmit="return confirm('Delete {{ addslashes($user->name) }}? Their bookings will remain.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger-soft mb-0"><i class="bi bi-trash"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-transparent d-flex justify-content-between align-items-center py-3">
            <small class="text-muted">{{ $users->total() }} users</small>
            {{ $users->links() }}
        </div>
    </div>
@endsection