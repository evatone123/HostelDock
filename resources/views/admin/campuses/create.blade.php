@extends('admin.layouts.admin')

@section('title', 'Add Campus')
@section('heading', 'Add Campus')
@section('subheading', 'Register a new campus hostels can link to')

@section('content')
    <div class="card shadow rounded-3 p-4 col-lg-8">
        <form method="POST" action="{{ route('admin.campuses.store') }}">
            @csrf
            @include('admin.campuses.partials._form')
        </form>
    </div>
@endsection