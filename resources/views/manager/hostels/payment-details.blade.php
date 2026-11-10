@extends('admin.layouts.admin')

@section('title', 'Payment Gateways')
@section('heading', 'Payment Gateways')
@section('subheading', $hostel->displayName())

@section('content')
    <div class="card shadow rounded-3 p-4">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('manager.hostels.payments.update', $hostel) }}">
            @csrf
            @method('PUT')
            @include('admin.hostels.partials._payment-details', [
                'detail' => $detail,
                'backRoute' => 'manager.hostels.index',
            ])
        </form>
    </div>
@endsection