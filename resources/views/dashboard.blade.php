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

    {{-- Analysis Charts --}}
    <div class="row g-3 mb-3">
        <div class="col-12 col-lg-7">
            <div class="card h-100">
                <div class="card-header">Food Order Trend — Last 30 Days</div>
                <div class="card-body">
                    <div style="height:280px;">
                        <canvas id="ordersTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-5">
            <div class="card h-100">
                <div class="card-header">Customer Growth — Last 30 Days</div>
                <div class="card-body">
                    <div style="height:280px;">
                        <canvas id="customerGrowthChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-12 col-lg-7">
            <div class="card h-100">
                <div class="card-header">Top 5 Selling Foods — Last 30 Days</div>
                <div class="card-body">
                    @if($topFoods->isEmpty())
                        <p class="text-muted mb-0">No orders placed in the last 30 days.</p>
                    @else
                        <div style="height:240px;">
                            <canvas id="topFoodsChart"></canvas>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-5">
            <div class="card h-100">
                <div class="card-header">Order Status — Last 30 Days</div>
                <div class="card-body">
                    @if($statusBreakdown->isEmpty())
                        <p class="text-muted mb-0">No orders placed in the last 30 days.</p>
                    @else
                        <div style="height:240px;">
                            <canvas id="orderStatusChart"></canvas>
                        </div>
                    @endif
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var orderLabels    = @json($orderTrendLabels);
    var orderCounts     = @json($orderTrendCounts);
    var orderRevenue    = @json($orderTrendRevenue);
    var customerCounts  = @json($customerTrendCounts);
    var topFoodLabels   = @json($topFoods->pluck('title'));
    var topFoodQty      = @json($topFoods->pluck('total_qty'));
    var statusLabels    = @json($statusBreakdown->keys());
    var statusCounts    = @json($statusBreakdown->values());

    new Chart(document.getElementById('ordersTrendChart'), {
        type: 'line',
        data: {
            labels: orderLabels,
            datasets: [
                {
                    label: 'Orders',
                    data: orderCounts,
                    borderColor: '#5a8dee',
                    backgroundColor: 'rgba(90,141,238,.1)',
                    yAxisID: 'y',
                    tension: 0.3,
                    fill: true,
                },
                {
                    label: 'Revenue (₹)',
                    data: orderRevenue,
                    borderColor: '#39da8a',
                    backgroundColor: 'rgba(57,218,138,.1)',
                    yAxisID: 'y1',
                    tension: 0.3,
                    fill: true,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            scales: {
                y:  { position: 'left', beginAtZero: true, title: { display: true, text: 'Orders' } },
                y1: { position: 'right', beginAtZero: true, grid: { drawOnChartArea: false }, title: { display: true, text: 'Revenue (₹)' } },
            },
        },
    });

    new Chart(document.getElementById('customerGrowthChart'), {
        type: 'bar',
        data: {
            labels: orderLabels,
            datasets: [{
                label: 'New Customers',
                data: customerCounts,
                backgroundColor: '#2178d1',
                borderRadius: 4,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
        },
    });

    if (topFoodLabels.length) {
        new Chart(document.getElementById('topFoodsChart'), {
            type: 'bar',
            data: {
                labels: topFoodLabels,
                datasets: [{
                    label: 'Qty Sold',
                    data: topFoodQty,
                    backgroundColor: '#fdac41',
                    borderRadius: 4,
                }],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true, ticks: { precision: 0 } } },
            },
        });
    }

    if (statusLabels.length) {
        new Chart(document.getElementById('orderStatusChart'), {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusCounts,
                    backgroundColor: ['#39da8a', '#5a8dee', '#ff5b5c', '#fdac41'],
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
            },
        });
    }
});
</script>
@endpush
