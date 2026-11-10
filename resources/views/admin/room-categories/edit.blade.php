@extends('admin.layouts.admin')

@section('title', 'Edit Room Category')
@section('heading', 'Room Categories')
@section('subheading', 'Update the details for this category')

@section('content')
    <div class="card shadow rounded-3 p-4 col-lg-8">
        <form method="POST" action="{{ route('admin.room-categories.update', $roomCategory) }}">
            @csrf
            @method('PUT')
            @include('admin.room-categories.partials._form')
        </form>
    </div>
@endsection
