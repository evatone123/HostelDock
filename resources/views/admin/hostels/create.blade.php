@extends('admin.layouts.admin')

@section('title', 'Add Hostel')
@section('heading', 'Add Hostel')
@section('subheading', 'Register a new hostel on the platform')

@section('content')
    <div class="card shadow rounded-3 p-4">
        <form method="POST" action="{{ route('admin.hostels.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.hostels.partials._form', ['selected' => []])
        </form>
    </div>
@endsection