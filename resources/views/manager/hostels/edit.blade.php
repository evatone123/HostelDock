@extends('admin.layouts.admin')

@section('title', 'Edit Hostel')
@section('heading', 'Edit Hostel')
@section('subheading', $hostel->displayName())

@section('content')
    <div class="card shadow rounded-3 p-4">
        <form method="POST" action="{{ route('manager.hostels.update', $hostel) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('manager.hostels.partials._form', ['selected' => old('images', $selected)])
        </form>
    </div>
@endsection