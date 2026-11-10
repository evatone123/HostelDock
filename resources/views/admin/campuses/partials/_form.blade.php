<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">Campus Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="name" value="{{ old('name', $campus->name ?? '') }}" placeholder="e.g. University of Cape Coast" required>
        <small class="text-muted">Shown in the hostel "Institution" dropdown and hostel listings.</small>
    </div>
    <div class="col-md-4">
        <label class="form-label">Short Name <small class="text-muted">(optional)</small></label>
        <input type="text" class="form-control" name="short_name" value="{{ old('short_name', $campus->short_name ?? '') }}" placeholder="e.g. UCC">
    </div>
    <div class="col-12">
        <label class="form-label d-block">Map Coordinates <small class="text-muted">(optional — used for the campus marker on hostel pages)</small></label>
        <div class="row g-3">
            <div class="col-md-6">
                <input type="number" class="form-control" name="latitude" step="any" min="-90" max="90" placeholder="Latitude e.g. 5.650830" value="{{ old('latitude', $campus->latitude ?? '') }}">
            </div>
            <div class="col-md-6">
                <input type="number" class="form-control" name="longitude" step="any" min="-180" max="180" placeholder="Longitude e.g. -0.186940" value="{{ old('longitude', $campus->longitude ?? '') }}">
            </div>
        </div>
    </div>
    <div class="col-12">
        <label class="form-label d-block">Status</label>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                @checked(old('is_active', $campus->is_active ?? true))>
            <label class="form-check-label" for="is_active">Active &amp; selectable on hostel forms</label>
        </div>
    </div>

    <div class="col-12 d-flex gap-2">
        <button type="submit" class="btn btn-primary mb-0">
            <i class="bi bi-check-lg me-1"></i>{{ isset($campus) ? 'Save Changes' : 'Add Campus' }}
        </button>
        <a href="{{ route('admin.campuses.index') }}" class="btn btn-light mb-0">Cancel</a>
    </div>
</div>