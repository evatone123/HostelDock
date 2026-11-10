@extends('admin.layouts.admin')

@section('title', 'Edit Campus')
@section('heading', 'Edit Campus')
@section('subheading', 'Update the details for this campus')

@section('content')
    <div class="card shadow rounded-3 p-4 col-lg-8">
        <form method="POST" action="{{ route('admin.campuses.update', $campus) }}">
            @csrf
            @method('PUT')
            @include('admin.campuses.partials._form')
        </form>
    </div>
@endsection