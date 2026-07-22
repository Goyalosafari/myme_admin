@extends('Layouts.master')

@section('title','Loyalty Points - Food Delivery')

@section('content')

<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Loyalty Points</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Loyalty Points</li>
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

    <section class="section">
        <div class="card">
            <div class="card-header">Loyalty Point Settings</div>
            <div class="card-body">
                <form method="POST" action="{{ route('loyalty.settings.update') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Rupees spent per 1 point earned</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" min="1" name="points_per_amount" class="form-control"
                                    value="{{ old('points_per_amount', $settings->points_per_amount) }}" required>
                                <span class="input-group-text">= 1 point</span>
                            </div>
                            @error('points_per_amount')<div class="text-danger small">{{ $message }}</div>@enderror
                            <div class="form-text">E.g. 100 means every ₹100 spent on a delivered order earns 1 point.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Minimum points required to convert to wallet</label>
                            <input type="number" min="0" name="min_points_to_convert" class="form-control"
                                value="{{ old('min_points_to_convert', $settings->min_points_to_convert) }}" required>
                            @error('min_points_to_convert')<div class="text-danger small">{{ $message }}</div>@enderror
                            <div class="form-text">Customers must reach this many points before they can convert them to wallet balance. 1 point = ₹1.</div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </form>
            </div>
        </div>
    </section>
</div>

@endsection
