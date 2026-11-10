@extends('admin.layouts.admin')

@section('title', 'Add User')
@section('heading', 'Add User')
@section('subheading', 'Create a student, hostel manager, or admin account')

@section('content')
    <div class="card shadow rounded-3 p-4 col-lg-9">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            @include('admin.users.partials._form', [
                'requirePassword' => true,
                'submitLabel' => 'Create User',
            ])
        </form>
    </div>
@endsection