@extends('admin.layouts.admin')

@section('title', 'Edit User')
@section('heading', 'Edit User')
@section('subheading', $user->name)

@section('content')
    <div class="card shadow rounded-3 p-4 col-lg-9">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')
            @include('admin.users.partials._form', [
                'requirePassword' => false,
                'submitLabel' => 'Save Changes',
            ])
        </form>
    </div>
@endsection