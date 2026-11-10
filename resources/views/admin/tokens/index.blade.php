@extends('admin.layouts.admin')

@section('title', 'Unlock Access Tokens')
@section('heading', 'Unlock Access Tokens')
@section('subheading', 'All booking token payments (unlock access)')

@section('content')
    <div class="card shadow rounded-3">
        <div class="card-header bg-transparent py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <form method="GET" class="d-flex flex-wrap gap-2">
                <div class="input-group" style="width: 240px;">
                    <span class="input-group-text bg-body border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="Token ref, student, email">
                </div>
                <select class="form-select" name="status" style="width: 160px;">
                    @foreach ($statuses as $key => $label)
                        <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                <button class="btn btn-primary mb-0" type="submit">Filter</button>
                @if (request('q') || request('status'))
                    <a href="{{ route('admin.tokens.index') }}" class="btn btn-light mb-0">Reset</a>
                @endif
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-muted small">
                        <th>Token Ref / Date</th>
                        <th>Student</th>
                        <th>Amount</th>
                        <th>Gateway</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tokens as $token)
                        <tr>
                            <td>
                                <span class="fw-semibold">{{ $token->token_ref }}</span>
                                <small class="text-muted d-block">
                                    @if ($token->paid_at)
                                        {{ $token->paid_at->format('d M Y, H:i') }}
                                    @else
                                        {{ $token->created_at->format('d M Y, H:i') }}
                                    @endif
                                </small>
                            </td>
                            <td>
                                <div>{{ optional($token->user)->name ?? 'N/A' }}</div>
                                <small class="text-muted">{{ optional($token->user)->email }}</small>
                            </td>
                            <td class="fw-semibold text-primary">GHS{{ number_format($token->amount, 2) }}</td>
                            <td>
                                @if ($token->gateway === 'paystack')
                                    <span class="badge bg-light text-dark border">Paystack</span>
                                @elseif ($token->gateway === 'mtn_momo')
                                    <span class="badge bg-light text-dark border">MTN MoMo</span>
                                @else
                                    <span class="badge bg-light text-muted border">—</span>
                                @endif
                            </td>
                            <td>
                                @if ($token->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif ($token->status === 'used')
                                    <span class="badge bg-info text-white">Used</span>
                                @elseif ($token->status === 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif ($token->status === 'failed')
                                    <span class="badge bg-danger">Failed</span>
                                @else
                                    <span class="badge bg-secondary">{{ $token->statusLabel() }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No tokens found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-transparent d-flex justify-content-between align-items-center py-3">
            <small class="text-muted">{{ $tokens->total() }} tokens</small>
            {{ $tokens->links() }}
        </div>
    </div>
@endsection