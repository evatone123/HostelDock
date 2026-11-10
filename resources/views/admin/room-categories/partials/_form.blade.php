<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Category Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="name" value="{{ old('name', $roomCategory->name ?? '') }}" placeholder="e.g. Two in a room" required>
        <small class="text-muted">Shown on room forms so admins and managers can apply it to rooms.</small>
    </div>
    <div class="col-md-3">
        <label class="form-label">People Per Room <span class="text-danger">*</span></label>
        <input type="number" class="form-control" name="capacity" value="{{ old('capacity', $roomCategory->capacity ?? 1) }}" min="1" max="100" required>
        <small class="text-muted">Beds in each room of this type.</small>
    </div>
    <div class="col-md-3">
        <label class="form-label">Sort Order <small class="text-muted">(optional)</small></label>
        <input type="number" class="form-control" name="sort_order" value="{{ old('sort_order', $roomCategory->sort_order ?? null) }}" min="0" max="1000">
        <small class="text-muted">Defaults to the people-per-room value.</small>
    </div>
    <div class="col-12">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                @checked(old('is_active', $roomCategory->is_active ?? true))>
            <label class="form-check-label" for="is_active">Active &amp; selectable on room forms</label>
        </div>
    </div>

    <div class="col-12 d-flex gap-2">
        <button type="submit" class="btn btn-primary mb-0">
            <i class="bi bi-check-lg me-1"></i>{{ isset($roomCategory) ? 'Save Changes' : 'Add Category' }}
        </button>
        <a href="{{ route('admin.room-categories.index') }}" class="btn btn-light mb-0">Cancel</a>
    </div>
</div>
