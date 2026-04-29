@extends('layouts.app')

@section('title', 'Analytics')

@section('content')
<div class="space-y-10">
    <div>
        <p class="font-display text-xs font-semibold uppercase tracking-widest text-emerald-600/90">Insights</p>
        <h1 class="page-title">Profit &amp; analytics</h1>
        <p class="page-subtitle">Pick a date range — charts and per-item breakdown update below.</p>
    </div>

    <div class="card-elevated p-5 sm:p-6">
        <form method="get" action="{{ route('analytics.index') }}" class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-end">
            <div>
                <label for="from" class="block text-xs font-medium text-slate-600">From</label>
                <input type="date" name="from" id="from" value="{{ $from->format('Y-m-d') }}" class="input-store mt-2">
            </div>
            <div>
                <label for="to" class="block text-xs font-medium text-slate-600">To</label>
                <input type="date" name="to" id="to" value="{{ $to->format('Y-m-d') }}" class="input-store mt-2">
            </div>
            <button type="submit" class="btn-primary h-[42px] px-8">Apply range</button>
        </form>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="stat-card border-blue-100/80">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-blue-400/10"></div>
            <p class="text-sm font-medium text-slate-500">Total revenue</p>
            <p class="mt-2 font-display text-2xl font-bold tabular-nums text-slate-900">{{ number_format($revenue, 2) }} <span class="text-lg font-semibold text-slate-400">PKR</span></p>
        </div>
        <div class="stat-card">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-slate-400/10"></div>
            <p class="text-sm font-medium text-slate-500">Total cost</p>
            <p class="mt-2 font-display text-2xl font-bold tabular-nums text-slate-700">{{ number_format($cost, 2) }} <span class="text-lg font-semibold text-slate-400">PKR</span></p>
        </div>
        <div class="stat-card border-emerald-100/80">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-emerald-400/15"></div>
            <p class="text-sm font-medium text-slate-500">Total profit</p>
            <p class="mt-2 font-display text-2xl font-bold tabular-nums text-emerald-700">{{ number_format($profit, 2) }} <span class="text-lg font-semibold text-slate-400">PKR</span></p>
        </div>
        <div class="stat-card border-violet-100/80 bg-gradient-to-br from-violet-50/80 to-white">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-violet-400/15"></div>
            <p class="text-sm font-medium text-slate-500">Profit margin</p>
            <p class="mt-2 font-display text-2xl font-bold tabular-nums text-violet-900">{{ number_format($marginPct, 2) }}%</p>
        </div>
    </div>

    <div class="grid gap-8 lg:grid-cols-2">
        <div class="card-elevated p-6 sm:p-8">
            <h2 class="font-display text-xl font-bold text-slate-900">Top 5 items by profit</h2>
            <p class="mt-1 text-sm text-slate-500">Horizontal bars — PKR profit</p>
            <div class="mt-6 h-72">
                <canvas id="topItemsChart"></canvas>
            </div>
        </div>
        <div class="card-elevated p-6 sm:p-8">
            <h2 class="font-display text-xl font-bold text-slate-900">Daily trend</h2>
            <p class="mt-1 text-sm text-slate-500">Sales vs profit by day</p>
            <div class="mt-6 h-72">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>

    <div class="card-elevated overflow-hidden">
        <div class="border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white px-6 py-5 sm:px-8">
            <h2 class="font-display text-xl font-bold text-slate-900">Per-item profit</h2>
            <p class="text-sm text-slate-500">Sorted by total profit (highest first).</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-slate-800 to-slate-900 text-left text-xs font-semibold uppercase tracking-wider text-white">
                        <th class="px-5 py-4 first:pl-8">Item</th>
                        <th class="px-5 py-4 text-right">Qty sold</th>
                        <th class="px-5 py-4 text-right">Revenue</th>
                        <th class="px-5 py-4 text-right">Cost</th>
                        <th class="px-5 py-4 text-right">Profit</th>
                        <th class="px-5 py-4 text-right last:pr-8">Avg margin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach ($perItem as $row)
                        <tr class="transition hover:bg-emerald-50/40">
                            <td class="px-5 py-3.5 pl-8 font-semibold text-slate-900">{{ $row['item_name'] }}</td>
                            <td class="px-5 py-3.5 text-right tabular-nums">{{ number_format($row['qty_sold'], 3) }}</td>
                            <td class="px-5 py-3.5 text-right tabular-nums">{{ number_format($row['total_revenue'], 2) }}</td>
                            <td class="px-5 py-3.5 text-right tabular-nums text-slate-600">{{ number_format($row['total_cost'], 2) }}</td>
                            <td class="px-5 py-3.5 text-right tabular-nums font-semibold text-emerald-800">{{ number_format($row['total_profit'], 2) }}</td>
                            <td class="px-5 py-3.5 pr-8 text-right tabular-nums font-medium text-slate-800">{{ number_format($row['avg_margin_pct'], 2) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const topLabels = @json($top5->pluck('item_name'));
    const topProfits = @json($top5->pluck('total_profit'));

    const ctxTop = document.getElementById('topItemsChart');
    new Chart(ctxTop, {
        type: 'bar',
        data: {
            labels: topLabels,
            datasets: [{
                label: 'Profit (PKR)',
                data: topProfits,
                backgroundColor: 'rgba(16, 185, 129, 0.75)',
                borderRadius: 8,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            scales: {
                x: { beginAtZero: true, grid: { color: 'rgba(148, 163, 184, 0.15)' } },
                y: { grid: { display: false } },
            },
        },
    });

    const ctxTrend = document.getElementById('trendChart');
    new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: @json($trendLabels),
            datasets: [
                {
                    label: 'Sales (PKR)',
                    data: @json($trendSales),
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.12)',
                    tension: 0.3,
                    fill: true,
                },
                {
                    label: 'Profit (PKR)',
                    data: @json($trendProfit),
                    borderColor: 'rgb(13, 148, 136)',
                    backgroundColor: 'rgba(13, 148, 136, 0.08)',
                    tension: 0.3,
                    fill: true,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(148, 163, 184, 0.15)' } },
                x: { grid: { display: false } },
            },
        },
    });
</script>
@endpush
@endsection
