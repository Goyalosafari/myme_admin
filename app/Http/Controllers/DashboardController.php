<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderBook;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $orders = OrderBook::whereDate('created_at', $today)->get();
        $orderToday = $orders->count();
        $orderTotalAmt = $orders->sum('payment_amount');
        $totalCustomers = User::count();

        $rangeStart = Carbon::today()->subDays(29);

        $orderRows = OrderBook::where('created_at', '>=', $rangeStart)
            ->where('status', '!=', 'cancel')
            ->selectRaw('DATE(created_at) as d, COUNT(*) as order_count, COALESCE(SUM(payment_amount),0) as revenue')
            ->groupBy('d')
            ->get()
            ->keyBy('d');

        $userRows = User::where('created_at', '>=', $rangeStart)
            ->selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->groupBy('d')
            ->get()
            ->keyBy('d');

        $orderTrendLabels  = [];
        $orderTrendCounts  = [];
        $orderTrendRevenue = [];
        $customerTrendCounts = [];

        for ($i = 0; $i < 30; $i++) {
            $date = $rangeStart->copy()->addDays($i)->toDateString();

            $orderTrendLabels[]    = Carbon::parse($date)->format('d M');
            $orderTrendCounts[]    = (int) ($orderRows[$date]->order_count ?? 0);
            $orderTrendRevenue[]   = (float) ($orderRows[$date]->revenue ?? 0);
            $customerTrendCounts[] = (int) ($userRows[$date]->c ?? 0);
        }

        $topFoods = Order::join('foods', 'orders.food_id', '=', 'foods.id')
            ->where('orders.status', '!=', 'cart')
            ->where('orders.created_at', '>=', $rangeStart)
            ->selectRaw('foods.title as title, SUM(orders.qty) as total_qty')
            ->groupBy('foods.id', 'foods.title')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $statusBreakdown = OrderBook::where('created_at', '>=', $rangeStart)
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        return view('dashboard', compact(
            'orderToday',
            'totalCustomers',
            'orderTotalAmt',
            'orderTrendLabels',
            'orderTrendCounts',
            'orderTrendRevenue',
            'customerTrendCounts',
            'topFoods',
            'statusBreakdown'
        ));
    }
}
