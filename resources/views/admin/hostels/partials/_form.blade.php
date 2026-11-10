<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">Hostel Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="name" value="{{ old('name', $hostel->name ?? '') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Institution</label>
        <select class="form-select" name="institution">
            @php $institutionOptions = \App\Models\Hostel::institutions(); @endphp
            @if (($hostel->institution ?? null) && !isset($institutionOptions[$hostel->institution]))
                <option value="{{ $hostel->institution }}" selected disabled>{{ $hostel->institutionLabel() ?? $hostel->institution }} (deactivated)</option>
            @endif
            @foreach ($institutionOptions as $key => $label)
                <option value="{{ $key }}" @selected(old('institution', $hostel->institution ?? '') === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Location <span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="location" value="{{ old('location', $hostel->location ?? '') }}" placeholder="e.g. Ayeduase, Kumasi" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Distance from Campus</label>
        <input type="text" class="form-control" name="distance" value="{{ old('distance', $hostel->distance ?? '') }}" placeholder="e.g. 1.0km from KNUST">
    </div>
    <div class="col-md-4">
        <label class="form-label">Rating <small class="text-muted">(0-5)</small></label>
        <input type="number" class="form-control" name="rating" min="0" max="5" step="0.1" value="{{ old('rating', $hostel->rating ?? 0) }}">
    </div>
    <div class="col-12">
        <label class="form-label d-block">Map Coordinates <small class="text-muted">(optional — used for the location map &amp; directions)</small></label>
        <div class="row g-3">
            <div class="col-md-6">
                <input type="number" class="form-control" name="latitude" step="any" min="-90" max="90" placeholder="Latitude e.g. 6.675090" value="{{ old('latitude', $hostel->latitude ?? '') }}">
            </div>
            <div class="col-md-6">
                <input type="number" class="form-control" name="longitude" step="any" min="-180" max="180" placeholder="Longitude e.g. -1.559650" value="{{ old('longitude', $hostel->longitude ?? '') }}">
            </div>
        </div>
    </div>
    <div class="col-12">
        <label class="form-label">Description</label>
        <textarea class="form-control" name="description" rows="5">{{ old('description', $hostel->description ?? '') }}</textarea>
    </div>
    <div class="col-12">
        <label class="form-label d-block">Status</label>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                @checked(old('is_active', $hostel->is_active ?? true))>
            <label class="form-check-label" for="is_active">Active &amp; visible on the website</label>
        </div>
    </div>

    <div class="col-12">
        <label class="form-label d-block">Upload Pictures <small class="text-muted">(jpeg, png, webp or gif &middot; max 5MB each)</small></label>
        <input type="file" class="form-control" name="images_upload[]" multiple accept="image/jpeg,image/png,image/webp,image/gif">
        <div class="small text-muted mt-1">Newly uploaded pictures come first, so the first one becomes the cover.</div>
        <div id="upload-preview" class="row g-2 mt-2"></div>
    </div>

    <div class="col-12">
        <label class="form-label d-block">Gallery</label>
        <p class="small text-muted">Tick the images below to include them on the hostel page. The first selected image is used as the cover.</p>
        <div class="row g-2">
            @foreach ($thumbnails as $path => $file)
                <div class="col-3 col-sm-2">
                    <label class="d-block text-center p-1 border rounded-2 cursor-pointer gallery-option" data-path="{{ $path }}">
                        <input type="checkbox" class="d-none gallery-check" name="images[]" value="{{ $path }}"
                            @checked(in_array($path, $selected ?? []))>
                        <img src="{{ asset($path) }}" class="img-fluid rounded-1" alt="">
                        <small class="text-muted text-truncate d-block">{{ $file }}</small>
                    </label>
                </div>
            @endforeach
        </div>
    </div>

    <div class="col-12 d-flex gap-2">
        <button type="submit" class="btn btn-primary mb-0">
            <i class="bi bi-check-lg me-1"></i>{{ isset($hostel) ? 'Save Changes' : 'Create Hostel' }}
        </button>
        <a href="{{ route('admin.hostels.index') }}" class="btn btn-light mb-0">Cancel</a>
    </div>
</div>

@push('scripts')
    <script>
        document.querySelectorAll('.gallery-option').forEach(label => {
            const cb = label.querySelector('.gallery-check');
            if (cb.checked) label.classList.add('bg-primary-soft', 'border-primary');
            cb.addEventListener('change', () => {
                label.classList.toggle('bg-primary-soft', cb.checked);
                label.classList.toggle('border-primary', cb.checked);
            });
        });

        const fileInput = document.querySelector('input[name="images_upload[]"]');
        const preview = document.getElementById('upload-preview');
        if (fileInput && preview) {
            fileInput.addEventListener('change', () => {
                preview.innerHTML = '';
                [...fileInput.files].forEach(file => {
                    if (!file.type.startsWith('image/')) return;
                    const col = document.createElement('div');
                    col.className = 'col-3 col-sm-2';
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.className = 'img-fluid rounded-1 border p-1';
                    img.alt = file.name;
                    img.title = file.name;
                    col.appendChild(img);
                    preview.appendChild(col);
                });
            });
        }
    </script>
@endpush