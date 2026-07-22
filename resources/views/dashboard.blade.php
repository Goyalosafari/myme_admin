@extends('Layouts.master')

@section('title', 'Dashboard — Myme Admin')
@section('page-title', 'Dashboard')

@section('content')
<div class="main-content container-fluid">

    <div class="page-title d-flex justify-content-between align-items-center">
        <div>
            <h3>Dashboard</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active">Overview</li>
                </ol>
            </nav>
        </div>
        <small class="text-muted">{{ now()->format('D, d M Y') }}</small>
    </div>

    {{-- Stat Cards --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="stat-card stat-orange">
                <div class="stat-icon"><i data-feather="shopping-bag"></i></div>
                <div>
                    <div class="stat-label">Today's Orders</div>
                    <div class="stat-value">{{ $orderToday }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-green">
                <div class="stat-icon"><i data-feather="dollar-sign"></i></div>
                <div>
                    <div class="stat-label">Today's Revenue</div>
                    <div class="stat-value">₹{{ number_format($orderTotalAmt, 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-blue">
                <div class="stat-icon"><i data-feather="users"></i></div>
                <div>
                    <div class="stat-label">Total Customers</div>
                    <div class="stat-value">{{ $totalCustomers }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-purple">
                <div class="stat-icon"><i data-feather="trending-up"></i></div>
                <div>
                    <div class="stat-label">Avg Order Value</div>
                    <div class="stat-value">₹{{ $orderToday > 0 ? number_format($orderTotalAmt / $orderToday, 0) : 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="row g-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">Quick Access</div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('orderbook.index') }}" class="btn btn-primary btn-sm">
                            <i data-feather="shopping-bag" style="width:13px;height:13px;"></i> Orders
                        </a>
                        <a href="{{ route('food.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i data-feather="grid" style="width:13px;height:13px;"></i> Food Items
                        </a>
                        <a href="{{ route('category.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i data-feather="layers" style="width:13px;height:13px;"></i> Categories
                        </a>
                        <a href="{{ route('banner.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i data-feather="image" style="width:13px;height:13px;"></i> Banners
                        </a>
                        <a href="{{ route('coupen.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i data-feather="tag" style="width:13px;height:13px;"></i> Coupons
                        </a>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i data-feather="users" style="width:13px;height:13px;"></i> Customers
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
