@extends('Layouts.master')

@section('title','Dashboard-Food Delivery Application')

@section('content')
<div class="main-content container-fluid" style="padding-bottom:250px;">
    <div class="page-title">
        <h3>Dashboard</h3>
        <p class="text-subtitle text-muted"></p>
    </div>
    <section class="section">
        <div class="row mb-2">
            <div class="col-12 col-md-3">
                <div class="card card-statistic">
                    <div class="card-body p-0">
                        <div class="d-flex flex-column">
                            <div class='px-3 py-3 d-flex justify-content-between'>
                                <h3 class='card-title'> Amount</h3>
                                <div class="card-right d-flex align-items-center">
                                    <p> {{ $orderTotalAmt }} </p>
                                </div>
                            </div>
                            <div class="chart-wrapper">
                                <canvas id="canvas1" style="height:100px !important"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card card-statistic">
                    <div class="card-body p-0">
                        <div class="d-flex flex-column">
                            <div class='px-3 py-3 d-flex justify-content-between'>
                                <h3 class='card-title'>Orders Today</h3>
                                <div class="card-right d-flex align-items-center">
                                    <p>{{ $orderToday }} </p>
                                </div>
                            </div>
                            <div class="chart-wrapper">
                                <canvas id="canvas2" style="height:100px !important"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card card-statistic">
                    <div class="card-body p-0">
                        <div class="d-flex flex-column">
                            <div class='px-3 py-3 d-flex justify-content-between'>
                                <h3 class='card-title'> Total ORDERS</h3>
                                <div class="card-right d-flex align-items-center">
                                    <p> {{ $orderToday }}</p>
                                </div>
                            </div>
                            <div class="chart-wrapper">
                                <canvas id="canvas3" style="height:100px !important"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card card-statistic">
                    <div class="card-body p-0">
                        <div class="d-flex flex-column">
                            <div class='px-3 py-3 d-flex justify-content-between'>
                                <h3 class='card-title'>Total Users</h3>
                                <div class="card-right d-flex align-items-center">
                                    <p> {{ $totalCustomers }} </p>
                                </div>
                            </div>
                            <div class="chart-wrapper">
                                <canvas id="canvas4" style="height:100px !important"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
    <script src="{{asset('vendors/chartjs/Chart.min.js')}}"></script>
    <script src="{{asset('vendors/apexcharts/apexcharts.min.js')}}"></script>
    <script src="{{asset('js/dashboard.js')}}"></script>
@endpush