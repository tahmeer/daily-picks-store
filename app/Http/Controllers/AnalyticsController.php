<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(Request $request): View
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : Carbon::now()->startOfMonth();

        $to = $request->filled('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : Carbon::now()->endOfDay();

        $summary = DB::table('sales')
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('COALESCE(SUM(total_amount), 0) as revenue')
            ->selectRaw('COALESCE(SUM(total_cost), 0) as cost')
            ->selectRaw('COALESCE(SUM(total_profit), 0) as profit')
            ->first();

        $revenue = (float) $summary->revenue;
        $cost = (float) $summary->cost;
        $profit = (float) $summary->profit;
        $marginPct = $revenue > 0 ? round(($profit / $revenue) * 100, 2) : 0;

        $perItem = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$from, $to])
            ->groupBy('sale_items.item_id', 'sale_items.item_name')
            ->select('sale_items.item_id')
            ->selectRaw('MAX(sale_items.item_name) as item_name')
            ->selectRaw('SUM(sale_items.quantity) as qty_sold')
            ->selectRaw('SUM(sale_items.total_selling_price) as total_revenue')
            ->selectRaw('SUM(sale_items.total_cost_price) as total_cost')
            ->selectRaw('SUM(sale_items.profit) as total_profit')
            ->orderByDesc('total_profit')
            ->get()
            ->map(function ($row) {
                $rev = (float) $row->total_revenue;

                return [
                    'item_id' => $row->item_id,
                    'item_name' => $row->item_name,
                    'qty_sold' => (float) $row->qty_sold,
                    'total_revenue' => $rev,
                    'total_cost' => (float) $row->total_cost,
                    'total_profit' => (float) $row->total_profit,
                    'avg_margin_pct' => $rev > 0 ? round(((float) $row->total_profit / $rev) * 100, 2) : 0,
                ];
            });

        $top5 = $perItem->take(5)->values();

        $dailyTrend = DB::table('sales')
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as d')
            ->selectRaw('SUM(total_amount) as sales_total')
            ->selectRaw('SUM(total_profit) as profit_total')
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        $trendLabels = $dailyTrend->pluck('d')->map(fn ($d) => Carbon::parse($d)->format('M j'))->all();
        $trendSales = $dailyTrend->pluck('sales_total')->map(fn ($v) => (float) $v)->all();
        $trendProfit = $dailyTrend->pluck('profit_total')->map(fn ($v) => (float) $v)->all();

        return view('analytics.index', compact(
            'from',
            'to',
            'revenue',
            'cost',
            'profit',
            'marginPct',
            'perItem',
            'top5',
            'trendLabels',
            'trendSales',
            'trendProfit'
        ));
    }
}
