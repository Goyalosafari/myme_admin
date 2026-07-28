@extends('Layouts.master')

@section('title', 'Revenue Report — Myme Admin')

@section('content')
<div class="main-content container-fluid">

    <div class="page-title d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h3>Revenue Report</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Revenue Report</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('revenue-report.export', request()->query()) }}" class="btn btn-outline-secondary btn-sm">
            <i data-feather="download" style="width:14px;height:14px;"></i> Export CSV
        </a>
    </div>

    {{-- Date filter --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('revenue-report.index') }}" class="row g-2 align-items-end">
                <div class="col-auto">
                    <label class="form-label mb-1">Quick range</label>
                    <select name="range" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="today" {{ $preset === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="week" {{ $preset === 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ $preset === 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="year" {{ $preset === 'year' ? 'selected' : '' }}>This Year</option>
                        <option value="custom" {{ $preset === 'custom' ? 'selected' : '' }}>Custom</option>
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label mb-1">From</label>
                    <input type="date" name="from" value="{{ $from->toDateString() }}" class="form-control form-control-sm">
                </div>
                <div class="col-auto">
                    <label class="form-label mb-1">To</label>
                    <input type="date" name="to" value="{{ $to->toDateString() }}" class="form-control form-control-sm">
                </div>
                <div class="col-auto">
                    <input type="hidden" name="range" value="custom">
                    <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Summary cards --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="stat-card stat-green">
                <div class="stat-icon"><i data-feather="dollar-sign"></i></div>
                <div>
                    <div class="stat-label">Total Revenue</div>
                    <div class="stat-value">₹{{ number_format($totalRevenue, 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-orange">
                <div class="stat-icon"><i data-feather="shopping-bag"></i></div>
                <div>
                    <div class="stat-label">Total Orders</div>
                    <div class="stat-value">{{ $totalOrders }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-blue">
                <div class="stat-icon"><i data-feather="trending-up"></i></div>
                <div>
                    <div class="stat-label">Avg Order Value</div>
                    <div class="stat-value">₹{{ number_format($avgOrderValue, 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-purple">
                <div class="stat-icon"><i data-feather="x-circle"></i></div>
                <div>
                    <div class="stat-label">Cancelled ({{ $cancelledCount }})</div>
                    <div class="stat-value">₹{{ number_format($cancelledAmount, 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Revenue trend chart --}}
    <div class="row g-3 mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">Revenue Trend — {{ $from->format('d M Y') }} to {{ $to->format('d M Y') }}</div>
                <div class="card-body">
                    @if($totalOrders === 0)
                        <p class="text-muted mb-0">No revenue in this period.</p>
                    @else
                        <div style="height:300px;">
                            <canvas id="revenueTrendChart"></canvas>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header">Revenue by Payment Mode</div>
                <div class="card-body">
                    @if($paymentModeBreakdown->isEmpty())
                        <p class="text-muted mb-0">No data for this period.</p>
                    @else
                        <table class="table table-sm mb-0">
                            <thead><tr><th>Mode</th><th>Orders</th><th class="text-end">Revenue</th></tr></thead>
                            <tbody>
                                @foreach($paymentModeBreakdown as $row)
                                <tr>
                                    <td>{{ ucfirst($row->mode) }}</td>
                                    <td>{{ $row->orders }}</td>
                                    <td class="text-end">₹{{ number_format($row->revenue, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header">Top 10 Selling Foods (by revenue)</div>
                <div class="card-body">
                    @if($topFoods->isEmpty())
                        <p class="text-muted mb-0">No data for this period.</p>
                    @else
                        <table class="table table-sm mb-0">
                            <thead><tr><th>Food</th><th>Qty Sold</th><th class="text-end">Revenue</th></tr></thead>
                            <tbody>
                                @foreach($topFoods as $row)
                                <tr>
                                    <td>{{ $row->title }}</td>
                                    <td>{{ $row->qty_sold }}</td>
                                    <td class="text-end">₹{{ number_format($row->revenue, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
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
    var labels  = @json($trendLabels);
    var revenue = @json($trendRevenue);
    var orders  = @json($trendOrders);

    var canvas = document.getElementById('revenueTrendChart');
    if (!canvas) return;

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    type: 'line',
                    label: 'Revenue (₹)',
                    data: revenue,
                    borderColor: '#39da8a',
                    backgroundColor: 'rgba(57,218,138,.1)',
                    yAxisID: 'y',
                    tension: 0.3,
                    fill: true,
                },
                {
                    type: 'bar',
                    label: 'Orders',
                    data: orders,
                    backgroundColor: 'rgba(90,141,238,.5)',
                    yAxisID: 'y1',
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            scales: {
                y:  { position: 'left', beginAtZero: true, title: { display: true, text: 'Revenue (₹)' } },
                y1: { position: 'right', beginAtZero: true, grid: { drawOnChartArea: false }, title: { display: true, text: 'Orders' }, ticks: { precision: 0 } },
            },
        },
    });
});
</script>
@endpush
