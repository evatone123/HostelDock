<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Full Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="name" value="{{ old('name', $user->name ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Email <span class="text-danger">*</span></label>
        <input type="email" class="form-control" name="email" value="{{ old('email', $user->email ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Phone</label>
        <input type="text" class="form-control" name="phone" value="{{ old('phone', $user->phone ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Role <span class="text-danger">*</span></label>
        <select class="form-select" name="role" id="roleSelect">
            @foreach ($roles as $key => $label)
                <option value="{{ $key }}" @selected(old('role', $user->role ?? 'student') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <div class="form-text">Managers can only access the hostels you select.</div>
    </div>

    <div class="col-12 {{ isset($user) && $user->isManager() ? '' : 'd-none' }}" id="hostelAssignment">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="form-label mb-0">Assigned Hostels</label>
            <small class="text-muted"><span id="hostelCount">{{ count(old('hostels', $assigned ?? [])) }}</span> selected</small>
        </div>
        @if ($hostels->isEmpty())
            <div class="alert alert-warning py-2 mb-0">No hostels available. Create a hostel first, then assign it here.</div>
        @else
            <div class="border rounded-2 p-3" style="max-height: 260px; overflow-y: auto;">
                <div class="row g-2">
                    @foreach ($hostels as $hostel)
                        <div class="col-md-6">
                            <div class="form-check py-1 m-0">
                                <input class="form-check-input" type="checkbox" name="hostels[]" value="{{ $hostel->id }}"
                                    id="hostel_{{ $hostel->id }}"
                                    @checked(in_array($hostel->id, old('hostels', $assigned ?? [])))>
                                <label class="form-check-label" for="hostel_{{ $hostel->id }}">{{ $hostel->displayName() }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    @if ($editablePassword ?? true)
        <div class="col-md-6">
            <label class="form-label">Password {{ $requirePassword ?? false ? '<span class="text-danger">*</span>' : '' }}
                @if (!($requirePassword ?? false))
                    <small class="text-muted">(leave blank to keep current)</small>
                @endif
            </label>
            <input type="password" class="form-control" name="password" minlength="6" {{ ($requirePassword ?? false) ? 'required' : '' }}>
        </div>
        <div class="col-md-6">
            <label class="form-label">Confirm Password {{ $requirePassword ?? false ? '<span class="text-danger">*</span>' : '' }}</label>
            <input type="password" class="form-control" name="password_confirmation" minlength="6" {{ ($requirePassword ?? false) ? 'required' : '' }}>
        </div>
    @endif

    <div class="col-12 d-flex gap-2">
        <button type="submit" class="btn btn-primary mb-0"><i class="bi bi-check-lg me-1"></i>{{ $submitLabel ?? 'Save' }}</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-light mb-0">Cancel</a>
    </div>
</div>

@push('scripts')
    <script>
        const roleSelect = document.getElementById('roleSelect');
        const assignment = document.getElementById('hostelAssignment');
        const countEl = document.getElementById('hostelCount');
        const boxes = assignment ? assignment.querySelectorAll('input[name="hostels[]"]') : [];
        const updateCount = () => {
            if (countEl) countEl.textContent = Array.from(boxes).filter(b => b.checked).length;
        };
        if (roleSelect && assignment) {
            const toggle = () => assignment.classList.toggle('d-none', roleSelect.value !== 'manager');
            roleSelect.addEventListener('change', toggle);
        }
        boxes.forEach(b => b.addEventListener('change', updateCount));
        updateCount();
    </script>
@endpush