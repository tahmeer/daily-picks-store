@extends('layouts.app')

@section('title', 'Sales History')

@section('content')
<div class="space-y-8">
    <div>
        <p class="font-display text-xs font-semibold uppercase tracking-widest text-emerald-600/90">Transactions</p>
        <h1 class="page-title">Sales history</h1>
        <p class="page-subtitle">Filter by date or search by receipt number / customer phone.</p>
    </div>

    <div class="card-elevated p-5 sm:p-6">
        <form method="get" action="{{ route('sales.index') }}" class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-end">
            <div>
                <label for="from" class="block text-xs font-medium text-slate-600">From</label>
                <input type="date" name="from" id="from" value="{{ request('from') }}" class="input-store mt-2">
            </div>
            <div>
                <label for="to" class="block text-xs font-medium text-slate-600">To</label>
                <input type="date" name="to" id="to" value="{{ request('to') }}" class="input-store mt-2">
            </div>
            <div class="min-w-[220px] flex-1">
                <label for="q" class="block text-xs font-medium text-slate-600">Receipt or phone</label>
                <input type="search" name="q" id="q" value="{{ request('q') }}" placeholder="RCP-… or phone"
                    class="input-store mt-2 w-full">
            </div>
            <button type="submit" class="btn-primary h-[42px] shrink-0 px-6">Apply</button>
            <a href="{{ route('sales.index') }}" class="btn-secondary h-[42px] shrink-0 px-6">Reset</a>
        </form>
    </div>

    <div class="card-elevated overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-slate-800 to-slate-900 text-left text-xs font-semibold uppercase tracking-wider text-white">
                        <th class="px-5 py-4 first:pl-8">Receipt</th>
                        <th class="px-5 py-4">Date</th>
                        <th class="px-5 py-4">Phone</th>
                        <th class="px-5 py-4 text-right">Lines</th>
                        <th class="px-5 py-4 text-right">Amount</th>
                        <th class="px-5 py-4 text-right">Cost</th>
                        <th class="px-5 py-4 text-right">Profit</th>
                        <th class="px-5 py-4 text-right last:pr-8"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach ($sales as $sale)
                        <tr class="transition hover:bg-emerald-50/40">
                            <td class="px-5 py-3.5 pl-8 font-mono text-xs font-semibold text-slate-800">{{ $sale->receipt_number }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $sale->created_at->format('M j, Y H:i') }}</td>
                            <td class="px-5 py-3.5">{{ $sale->customer_phone ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-right tabular-nums">{{ $sale->sale_items_count }}</td>
                            <td class="px-5 py-3.5 text-right tabular-nums font-medium">{{ number_format((float) $sale->total_amount, 2) }}</td>
                            <td class="px-5 py-3.5 text-right tabular-nums text-slate-600">{{ number_format((float) $sale->total_cost, 2) }}</td>
                            <td class="px-5 py-3.5 text-right tabular-nums font-semibold text-emerald-700">{{ number_format((float) $sale->total_profit, 2) }}</td>
                            <td class="px-5 py-3.5 pr-8 text-right">
                                <a href="{{ route('sales.receipt', $sale) }}" class="font-semibold text-emerald-700 hover:text-emerald-900 hover:underline">View →</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($sales->hasPages())
            <div class="border-t border-slate-100 bg-slate-50/80 px-5 py-4">{{ $sales->links() }}</div>
        @endif
    </div>
</div>
@endsection
