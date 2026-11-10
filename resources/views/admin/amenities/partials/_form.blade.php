<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Amenity Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="name" value="{{ old('name', $amenity->name ?? '') }}" placeholder="e.g. 24/7 Security" required>
        <small class="text-muted">Shown on room forms so admins and managers can apply it to rooms.</small>
    </div>
    <div class="col-md-6">
        <label class="form-label">Icon <small class="text-muted">(optional)</small></label>
        <select class="form-select" name="icon">
            <option value="">— None —</option>
            @foreach ($icons as $class => $label)
                <option value="{{ $class }}" @selected(old('icon', $amenity->icon ?? '') === $class)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label d-block">Status</label>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                @checked(old('is_active', $amenity->is_active ?? true))>
            <label class="form-check-label" for="is_active">Active &amp; selectable on room forms</label>
        </div>
    </div>

    <div class="col-12 d-flex gap-2">
        <button type="submit" class="btn btn-primary mb-0">
            <i class="bi bi-check-lg me-1"></i>{{ isset($amenity) ? 'Save Changes' : 'Add Amenity' }}
        </button>
        <a href="{{ route('admin.amenities.index') }}" class="btn btn-light mb-0">Cancel</a>
    </div>
</div>