@extends('admin.layouts.admin')

@section('title', 'Add Room')
@section('heading', 'Add Room')
@section('subheading', $hostel->displayName())

@section('content')
    <div class="card shadow rounded-3 p-4 col-lg-8">
        <form method="POST" action="{{ route('admin.hostels.rooms.store', $hostel) }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Room Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="e.g. Room 1" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Room Type <span class="text-danger">*</span></label>
                    <select class="form-select" name="room_type">
                        @foreach ($roomTypes as $key => $label)
                            <option value="{{ $key }}" @selected(old('room_type') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gender Category <span class="text-danger">*</span></label>
                    <select class="form-select" name="gender_category">
                        @foreach ($genders as $key => $label)
                            <option value="{{ $key }}" @selected(old('gender_category') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Price Per Academic Year (GHS) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="price" value="{{ old('price') }}" min="0" step="0.01" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Total Rooms <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="total_rooms" value="{{ old('total_rooms', 1) }}" min="1" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label d-block">Beds</label>
                    <div class="form-control-plaintext">
                        <strong>{{ old('total_rooms', 1) }} rooms &times; people per room</strong>
                    </div>
                    <small class="text-muted">Availability is calculated from bookings, so there is nothing to set here.</small>
                </div>
                <div class="col-12">
                    <label class="form-label d-block">Room Amenities <small class="text-muted">(optional)</small></label>
                    @if ($amenities->isEmpty())
                        <div class="alert alert-warning py-2 mb-0">
                            No amenities available yet. An admin can add them on the
                            <a class="text-primary" href="{{ route('admin.amenities.index') }}">Amenities</a> page.
                        </div>
                    @else
                        <div class="border rounded-2 p-3" style="max-height: 220px; overflow-y: auto;">
                            <div class="row g-2">
                                @foreach ($amenities as $amenity)
                                    <div class="col-md-4">
                                        <div class="form-check py-1 m-0">
                                            <input class="form-check-input" type="checkbox" name="amenities[]" value="{{ $amenity->id }}"
                                                id="amenity_{{ $amenity->id }}"
                                                @checked(in_array($amenity->id, old('amenities', $selected ?? [])))>
                                            <label class="form-check-label" for="amenity_{{ $amenity->id }}">
                                                @if ($amenity->icon)
                                                    <i class="bi {{ $amenity->icon }} me-1 text-primary"></i>
                                                @endif
                                                {{ $amenity->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', true))>
                        <label class="form-check-label" for="is_active">Active &amp; bookable by students</label>
                    </div>
                </div>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary mb-0"><i class="bi bi-check-lg me-1"></i>Add Room</button>
                    <a href="{{ route('admin.hostels.rooms.index', $hostel) }}" class="btn btn-light mb-0">Cancel</a>
                </div>
            </div>
        </form>
    </div>
@endsection