@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-10">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="font-display text-xs font-semibold uppercase tracking-widest text-emerald-600/90">Today at a glance</p>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Inventory, sales, and low-stock alerts in one place.</p>
        </div>
        <a href="{{ route('sales.create') }}" class="btn-primary shadow-emerald-600/30">
            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New sale
        </a>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="stat-card">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-gradient-to-br from-slate-200/50 to-transparent"></div>
            <div class="relative flex items-start gap-4">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-slate-600 to-slate-800 text-white shadow-lg shadow-slate-900/25">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                </span>
                <div>
                    <p class="text-sm font-medium text-slate-500">Total items</p>
                    <p class="mt-1 font-display text-3xl font-bold tabular-nums tracking-tight text-slate-900">{{ number_format($totalItems) }}</p>
                </div>
            </div>
        </div>
        <div class="stat-card border-emerald-100/80">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-gradient-to-br from-emerald-300/25 to-transparent"></div>
            <div class="relative flex items-start gap-4">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-600/35">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                </span>
                <div>
                    <p class="text-sm font-medium text-slate-500">Today’s sales</p>
                    <p class="mt-1 font-display text-3xl font-bold tabular-nums tracking-tight text-emerald-700">{{ number_format($todaySales, 2) }} <span class="text-lg font-semibold text-slate-400">PKR</span></p>
                </div>
            </div>
        </div>
        <div class="stat-card border-teal-100/80">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-gradient-to-br from-teal-300/25 to-transparent"></div>
            <div class="relative flex items-start gap-4">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-teal-500 to-cyan-600 text-white shadow-lg shadow-teal-600/35">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </span>
                <div>
                    <p class="text-sm font-medium text-slate-500">Today’s profit</p>
                    <p class="mt-1 font-display text-3xl font-bold tabular-nums tracking-tight text-teal-700">{{ number_format($todayProfit, 2) }} <span class="text-lg font-semibold text-slate-400">PKR</span></p>
                </div>
            </div>
        </div>
        <div class="stat-card border-amber-200/80 bg-gradient-to-br from-amber-50/90 to-orange-50/50">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-gradient-to-br from-amber-300/40 to-transparent"></div>
            <div class="relative flex items-start gap-4">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-lg shadow-amber-600/35">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </span>
                <div>
                    <p class="text-sm font-medium text-amber-900/80">Low stock alerts</p>
                    <p class="mt-1 font-display text-3xl font-bold tabular-nums tracking-tight text-amber-950">{{ number_format($lowStockAlerts) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-8 lg:grid-cols-3">
        <div class="card-elevated lg:col-span-2 p-6 sm:p-8">
            <div class="mb-6 flex items-center justify-between gap-4">
                <div>
                    <h2 class="font-display text-xl font-bold text-slate-900">Last 7 days</h2>
                    <p class="text-sm text-slate-500">Sales vs profit trend</p>
                </div>
            </div>
            <div class="h-72">
                <canvas id="dashChart"></canvas>
            </div>
        </div>
        <div class="card-elevated flex flex-col p-6 sm:p-8">
            <h2 class="font-display text-xl font-bold text-slate-900">Low stock</h2>
            <p class="mt-1 text-sm text-slate-600">Below alert threshold — restock soon.</p>
            <ul class="mt-5 flex-1 divide-y divide-slate-100 overflow-auto rounded-xl border border-slate-100 bg-slate-50/50">
                @forelse ($lowStockItems as $item)
                    <li class="flex items-center justify-between gap-3 px-4 py-3.5 text-sm transition hover:bg-white">
                        <span class="font-medium text-slate-800">{{ $item->name }}</span>
                        <span class="shrink-0 rounded-lg bg-amber-100 px-2 py-0.5 text-xs font-semibold tabular-nums text-amber-900">{{ number_format((float) $item->current_stock, 3) }} {{ $item->unit->value }}</span>
                    </li>
                @empty
                    <li class="px-4 py-8 text-center text-sm text-slate-500">All items above threshold.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="card-elevated overflow-hidden">
        <div class="border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white px-6 py-5 sm:px-8">
            <h2 class="font-display text-xl font-bold text-slate-900">Recent sales</h2>
            <p class="text-sm text-slate-500">Latest receipts</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-slate-800 to-slate-900 text-left text-xs font-semibold uppercase tracking-wider text-white">
                        <th class="px-6 py-4 first:pl-8">Receipt</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4 text-right">Amount</th>
                        <th class="px-6 py-4 text-right">Profit</th>
                        <th class="px-6 py-4 text-right last:pr-8"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($recentSales as $sale)
                        <tr class="transition hover:bg-emerald-50/50">
                            <td class="px-6 py-3.5 pl-8 font-mono text-xs font-medium text-slate-800">{{ $sale->receipt_number }}</td>
                            <td class="px-6 py-3.5 text-slate-600">{{ $sale->created_at->format('M j, Y H:i') }}</td>
                            <td class="px-6 py-3.5 text-right tabular-nums font-medium text-slate-900">{{ number_format((float) $sale->total_amount, 2) }}</td>
                            <td class="px-6 py-3.5 text-right tabular-nums font-semibold text-emerald-700">{{ number_format((float) $sale->total_profit, 2) }}</td>
                            <td class="px-6 py-3.5 pr-8 text-right">
                                <a href="{{ route('sales.receipt', $sale) }}" class="inline-flex items-center rounded-lg font-semibold text-emerald-700 hover:text-emerald-900 hover:underline">Receipt →</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">No sales yet. Start with <span class="font-semibold text-emerald-700">New sale</span>.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('dashChart');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chartLabels),
            datasets: [
                {
                    label: 'Sales (PKR)',
                    data: @json($chartSales),
                    backgroundColor: 'rgba(16, 185, 129, 0.72)',
                    borderRadius: 8,
                },
                {
                    label: 'Profit (PKR)',
                    data: @json($chartProfit),
                    backgroundColor: 'rgba(13, 148, 136, 0.72)',
                    borderRadius: 8,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { labels: { font: { family: 'Instrument Sans' } } },
            },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(148, 163, 184, 0.15)' } },
                x: { grid: { display: false } },
            },
        },
    });
</script>
@endpush
@endsection
