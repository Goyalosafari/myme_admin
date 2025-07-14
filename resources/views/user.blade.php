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
                        <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page"> Users </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    
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
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($userData as $data)
                        <tr>
                            <td>{{$data->name}}</td>
                            <td>{{$data->email}}</td>
                            <td>{{$data->mobile}}</td>
                            <td>
                                @if($data->addresses && count($data->addresses))
                                    {{ $data->addresses[0]->address }}, {{ $data->addresses[0]->pincode }}, {{ $data->addresses[0]->landmark }} ({{ $data->addresses[0]->type }})
                                @else
                                    <span>No addresses</span>
                                @endif
                            </td>
                            <td>P1:{{$data->pincode1}}, P2:{{$data->pincode2}}</td>
                            <td>L1: {{$data->landmark1}}, L2:{{$data->landmark2}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

@endsection

@push('scripts')
    <script src="{{asset('vendors/simple-datatables/simple-datatables.js')}}"></script>
    <script src="{{asset('js/vendors.js')}}"></script>
@endpush

