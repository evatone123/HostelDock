@extends('admin.layouts.admin')

@section('title', 'Edit Amenity')
@section('heading', 'Edit Amenity')
@section('subheading', 'Update the details for this amenity')

@section('content')
    <div class="card shadow rounded-3 p-4 col-lg-8">
        <form method="POST" action="{{ route('admin.amenities.update', $amenity) }}">
            @csrf
            @method('PUT')
            @include('admin.amenities.partials._form')
        </form>
    </div>
@endsection