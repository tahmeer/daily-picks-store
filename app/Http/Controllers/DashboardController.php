<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();

        $totalItems = Item::query()->count();

        $todaySales = Sale::query()
            ->whereDate('created_at', $today)
            ->sum('total_amount');

        $todayProfit = Sale::query()
            ->whereDate('created_at', $today)
            ->sum('total_profit');

        $lowStockAlerts = Item::query()
            ->whereColumn('current_stock', '<=', 'low_stock_alert')
            ->count();

        $recentSales = Sale::query()
            ->withCount('saleItems')
            ->latest()
            ->limit(10)
            ->get();

        $startChart = Carbon::today()->subDays(6)->startOfDay();
        $chartRows = Sale::query()
            ->where('created_at', '>=', $startChart)
            ->selectRaw('DATE(created_at) as d')
            ->selectRaw('SUM(total_amount) as sales_total')
            ->selectRaw('SUM(total_profit) as profit_total')
            ->groupBy('d')
            ->orderBy('d')
            ->get()
            ->keyBy('d');

        $chartLabels = [];
        $chartSales = [];
        $chartProfit = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = Carbon::today()->subDays($i)->format('Y-m-d');
            $chartLabels[] = Carbon::parse($d)->format('M j');
            $row = $chartRows->get($d);
            $chartSales[] = $row ? (float) $row->sales_total : 0;
            $chartProfit[] = $row ? (float) $row->profit_total : 0;
        }

        $lowStockItems = Item::query()
            ->whereColumn('current_stock', '<=', 'low_stock_alert')
            ->orderBy('current_stock')
            ->limit(15)
            ->get();

        return view('dashboard', compact(
            'totalItems',
            'todaySales',
            'todayProfit',
            'lowStockAlerts',
            'recentSales',
            'chartLabels',
            'chartSales',
            'chartProfit',
            'lowStockItems'
        ));
    }
}
