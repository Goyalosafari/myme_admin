@extends('Layouts.master')

@section('title','Users-Food Delivery')

@push('styles')
<link rel="stylesheet" href="{{asset('vendors/simple-datatables/style.css')}}"></link>
@endpush

@section('content')

<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 user-last">
                <!-- <h3><a href="#pincode-form" class="btn btn-primary round">Create Pincode</a> </h3> -->
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class='breadcrumb-header'>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page"> Users </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="position:absolute;top:20px;right:20px;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <section class="section" id="users-data">
        <div class="card">
            <div class="card-header">Users List
            </div>
            <div class="card-body">
                <table class='table table-striped' id="table1">
                    <thead>
                        <tr>
                            <th> Name </th>
                            <th> Email </th>
                            <th> Mobile </th>
                            <th> Address </th>
                            <th> Pincode </th>
                            <th> Landmark </th>
                            <th> Loyalty Points </th>
                            <th> Wallet Balance </th>
                            <th> Status </th>
                            <th> Action </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($userData as $data)
                        <tr>
                            <td>{{$data->name}}</td>
                            <td>{{$data->email}}</td>
                            <td>{{$data->mobile}}</td>
                            <td>A1:{{$data->address1}},A2: {{$data->address2}}</td>
                            <td>P1:{{$data->pincode1}}, P2:{{$data->pincode2}}</td>
                            <td>L1: {{$data->landmark1}}, L2:{{$data->landmark2}}</td>
                            <td>{{ $data->loyalty_points ?? 0 }}</td>
                            <td>₹{{ number_format(($data->wallet_debit ?? 0) - ($data->wallet_credit ?? 0), 2) }}</td>
                            <td>
                                @if((int) $data->status === 2)
                                    <span class="badge bg-danger">Deactivated</span>
                                @else
                                    <span class="badge bg-success">Active</span>
                                @endif
                            </td>
                            <td>
                                @if((int) $data->status === 2)
                                <form method="POST" action="{{ route('users.reactivate', $data->id) }}"
                                    onsubmit="return confirm('Reactivate {{ $data->name }}\'s account?')">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-success">Reactivate</button>
                                </form>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-3">{{ $userData->links() }}</div>
            </div>
        </div>
    </section>
</div>

@endsection

@push('scripts')
    <script src="{{asset('vendors/simple-datatables/simple-datatables.js')}}"></script>
    <script src="{{asset('js/vendors.js')}}"></script>
@endpush

