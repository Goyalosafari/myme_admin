@extends('Layouts.master')

@section('title','Users-Food Delivery')

@push('styles')
<link rel="stylesheet" href="{{asset('vendors/simple-datatables/style.css')}}"></link>
<style>
    /* Long values (e.g. deactivated-mobile_...@myme.local) wrap instead of widening the table */
    .users-table td { word-break: break-word; }
    .users-table th { white-space: nowrap; }
    .users-table td:nth-child(2) { min-width: 100px; }
    .users-table td:nth-child(3) { min-width: 170px; max-width: 220px; }
    .users-table .address-cell { min-width: 140px; max-width: 180px; }
    .users-table td:nth-child(4),
    .users-table td:nth-child(7),
    .users-table td:nth-child(8),
    .users-table td:nth-child(9) { white-space: nowrap; }
</style>
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
                <div class="table-responsive">
                <table class='table table-striped align-middle users-table' id="table1">
                    <thead>
                        <tr>
                            <th> User ID </th>
                            <th> Name </th>
                            <th> Email </th>
                            <th> Mobile </th>
                            <th> Address </th>
                            <th> Points </th>
                            <th> Wallet </th>
                            <th> Status </th>
                            <th> Action </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($userData as $data)
                        <tr>
                            <td>{{$data->id}}</td>
                            <td>{{$data->name}}</td>
                            <td>{{$data->email}}</td>
                            <td>{{$data->mobile}}</td>
                            @php
                                // Profile holds up to two addresses; show only the parts that are filled in.
                                $addresses = collect([1, 2])
                                    ->map(fn ($n) => collect([$data->{"address$n"}, $data->{"landmark$n"}, $data->{"pincode$n"}])->filter()->implode(', '))
                                    ->filter();
                            @endphp
                            <td class="address-cell">
                                @forelse($addresses as $address)
                                    <div>{{ $address }}</div>
                                @empty
                                    <span class="text-muted">—</span>
                                @endforelse
                            </td>
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
                </div>
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

