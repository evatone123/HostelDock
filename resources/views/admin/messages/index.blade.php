@extends('admin.layouts.admin')

@section('title', 'Messages')
@section('heading', 'Messages')
@section('subheading', 'Messages sent through the contact form')

@section('content')
    <div class="card shadow rounded-3">
        <div class="card-header bg-transparent py-3">
            <form method="GET" class="d-flex flex-wrap gap-2">
                <div class="input-group" style="width: 280px;">
                    <span class="input-group-text bg-body border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="Search name, email, subject">
                </div>
                <button class="btn btn-primary mb-0" type="submit">Filter</button>
                @if (request('q'))
                    <a href="{{ route('admin.messages.index') }}" class="btn btn-light mb-0">Reset</a>
                @endif
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-muted small">
                        <th>Message</th>
                        <th>Sender</th>
                        <th>Subject</th>
                        <th>Received</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($messages as $message)
                        <tr>
                            <td style="max-width: 380px;">
                                <div class="text-truncate">{{ $message->message }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $message->name }}</div>
                                <small class="text-muted">{{ $message->email }}</small>
                            </td>
                            <td>{{ $message->subject }}</td>
                            <td><small class="text-muted">{{ \Illuminate\Support\Carbon::parse($message->created_at)->format('d M Y, H:i') }}</small></td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('admin.messages.destroy', $message->id) }}"
                                    onsubmit="return confirm('Delete this message?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger-soft mb-0"><i class="bi bi-trash"></i> Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No messages yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-transparent d-flex justify-content-between align-items-center py-3">
            <small class="text-muted">{{ $messages->total() }} messages</small>
            {{ $messages->links() }}
        </div>
    </div>
@endsection