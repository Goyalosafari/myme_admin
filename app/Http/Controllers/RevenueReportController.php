<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderBook;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RevenueReportController extends Controller
{
    private function resolveRange(Request $request): array
    {
        $preset = $request->input('range', 'month');

        if ($preset === 'custom' && $request->filled('from') && $request->filled('to')) {
            $from = Carbon::parse($request->from)->startOfDay();
            $to   = Carbon::parse($request->to)->endOfDay();
        } else {
            [$from, $to] = match ($preset) {
                'today' => [Carbon::today(), Carbon::today()->endOfDay()],
                'week'  => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
                'year'  => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
                default => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            };
            $preset = in_array($preset, ['today', 'week', 'year']) ? $preset : 'month';
        }

        return [$from, $to, $preset];
    }

    public function index(Request $request)
    {
        [$from, $to, $preset] = $this->resolveRange($request);

        // Revenue only counts orders actually placed/delivered — a cancelled
        // order's money was refunded, so it shouldn't count as revenue.
        $revenueQuery = fn () => OrderBook::whereBetween('created_at', [$from, $to])
            ->whereIn('status', ['order', 'delivered']);

        $totalRevenue  = $revenueQuery()->sum('payment_amount');
        $totalOrders   = $revenueQuery()->count();
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        $cancelledCount  = OrderBook::whereBetween('created_at', [$from, $to])->where('status', 'cancel')->count();
        $cancelledAmount = OrderBook::whereBetween('created_at', [$from, $to])->where('status', 'cancel')->sum('payment_amount');

        $dailyRows = $revenueQuery()
            ->selectRaw('DATE(created_at) as d, COUNT(*) as orders, COALESCE(SUM(payment_amount),0) as revenue')
            ->groupBy('d')
            ->get()
            ->keyBy('d');

        $trendLabels  = [];
        $trendRevenue = [];
        $trendOrders  = [];
        $cursor = $from->copy();
        while ($cursor->lte($to)) {
            $key = $cursor->toDateString();
            $trendLabels[]  = $cursor->format('d M');
            $trendRevenue[] = (float) ($dailyRows[$key]->revenue ?? 0);
            $trendOrders[]  = (int) ($dailyRows[$key]->orders ?? 0);
            $cursor->addDay();
        }

        $paymentModeBreakdown = $revenueQuery()
            ->selectRaw("COALESCE(NULLIF(payment_mode, ''), 'Unknown') as mode, COUNT(*) as orders, COALESCE(SUM(payment_amount),0) as revenue")
            ->groupBy('mode')
            ->orderByDesc('revenue')
            ->get();

        $topFoods = Order::join('order_books', 'orders.order_book_id', '=', 'order_books.id')
            ->join('foods', 'orders.food_id', '=', 'foods.id')
            ->whereBetween('order_books.created_at', [$from, $to])
            ->whereIn('order_books.status', ['order', 'delivered'])
            ->selectRaw('foods.title as title, SUM(orders.qty) as qty_sold, COALESCE(SUM(orders.total),0) as revenue')
            ->groupBy('foods.id', 'foods.title')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        return view('revenue_report', compact(
            'from', 'to', 'preset',
            'totalRevenue', 'totalOrders', 'avgOrderValue',
            'cancelledCount', 'cancelledAmount',
            'trendLabels', 'trendRevenue', 'trendOrders',
            'paymentModeBreakdown', 'topFoods'
        ));
    }

    public function export(Request $request)
    {
        [$from, $to] = $this->resolveRange($request);

        $rows = OrderBook::whereBetween('created_at', [$from, $to])
            ->whereIn('status', ['order', 'delivered'])
            ->orderBy('created_at')
            ->get();

        $filename = 'revenue-report-' . $from->toDateString() . '-to-' . $to->toDateString() . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Order ID', 'Date', 'Customer ID', 'Payment Mode', 'Payment Status', 'Order Status', 'Amount']);
            foreach ($rows as $row) {
                fputcsv($out, [
                    $row->id,
                    $row->created_at,
                    $row->user_id,
                    $row->payment_mode,
                    $row->payment_status,
                    $row->status,
                    $row->payment_amount,
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
