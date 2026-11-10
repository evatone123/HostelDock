@extends('admin.layouts.admin')

@section('title', 'Add Amenity')
@section('heading', 'Add Amenity')
@section('subheading', 'Register a new amenity that can be applied to rooms')

@section('content')
    <div class="card shadow rounded-3 p-4 col-lg-8">
        <form method="POST" action="{{ route('admin.amenities.store') }}">
            @csrf
            @include('admin.amenities.partials._form')
        </form>
    </div>
@endsection