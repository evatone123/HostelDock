@extends('admin.layouts.admin')

@section('title', 'Add Room Category')
@section('heading', 'Room Categories')
@section('subheading', 'Register a category that admins and managers can apply to rooms')

@section('content')
    <div class="card shadow rounded-3 p-4 col-lg-8">
        <form method="POST" action="{{ route('admin.room-categories.store') }}">
            @csrf
            @include('admin.room-categories.partials._form')
        </form>
    </div>
@endsection
