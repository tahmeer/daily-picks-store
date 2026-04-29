@extends('layouts.app')

@section('title', 'Receipt '.$sale->receipt_number)

@push('head')
<style media="print">
    .no-print { display: none !important; }
    body { background: white !important; }
    main { padding: 0 !important; max-width: none !important; }
    .receipt-print { box-shadow: none !important; border: none !important; }
</style>
@endpush

@section('content')
<div class="no-print mx-auto mb-8 flex max-w-lg flex-wrap gap-3">
    <button type="button" onclick="window.print()" class="btn-primary shadow-lg">
        <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Print
    </button>
    <a href="{{ route('sales.create') }}" class="btn-secondary">New sale</a>
    <a href="{{ route('sales.index') }}" class="btn-secondary">Sales history</a>
</div>

<div
    class="receipt-print card-elevated mx-auto max-w-lg overflow-hidden p-0 sm:p-0"
    x-data="{ showProfit: false }"
>
    <div class="bg-gradient-to-r from-emerald-700 to-teal-700 px-8 py-8 text-center text-white print:bg-white print:text-slate-900">
        <p class="font-display text-xs font-semibold uppercase tracking-[0.2em] text-emerald-100 print:text-slate-500">Thank you</p>
        <h1 class="font-display mt-2 text-2xl font-bold print:text-slate-900">{{ config('app.name') }}</h1>
        <p class="mt-3 font-mono text-sm text-emerald-100 print:text-slate-600">{{ $sale->receipt_number }}</p>
        <p class="mt-1 text-sm text-emerald-50 print:text-slate-600">{{ $sale->created_at->format('d M Y, H:i') }}</p>
        @if ($sale->customer_phone)
            <p class="mt-3 text-sm text-emerald-100 print:text-slate-600">Phone: {{ $sale->customer_phone }}</p>
        @endif
    </div>

    <div class="px-8 pb-8 pt-6">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b-2 border-slate-200 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <th class="pb-3">Item</th>
                    <th class="pb-3 text-right">Qty</th>
                    <th class="pb-3 text-right">Unit</th>
                    <th class="pb-3 text-right">Price</th>
                    <th class="pb-3 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($sale->saleItems as $line)
                    <tr>
                        <td class="py-3 font-medium text-slate-900">{{ $line->item_name }}</td>
                        <td class="py-3 text-right tabular-nums text-slate-700">{{ number_format((float) $line->quantity, 3) }}</td>
                        <td class="py-3 text-right text-slate-600">{{ $line->unit }}</td>
                        <td class="py-3 text-right tabular-nums text-slate-600">{{ number_format((float) $line->selling_price_per_unit, 2) }}</td>
                        <td class="py-3 text-right tabular-nums font-semibold text-slate-900">{{ number_format((float) $line->total_selling_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-8 space-y-3 rounded-2xl bg-slate-50 px-5 py-4 ring-1 ring-slate-100">
            <div class="flex justify-between text-sm text-slate-600">
                <span>Line items</span>
                <span class="font-semibold text-slate-900">{{ $sale->saleItems->count() }}</span>
            </div>
            <div class="flex justify-between border-t border-slate-200 pt-3 font-display text-xl font-bold text-slate-900">
                <span>Total</span>
                <span class="tabular-nums">{{ number_format((float) $sale->total_amount, 2) }} PKR</span>
            </div>
        </div>

        <div class="no-print mt-8 border-t border-slate-100 pt-6">
            <button
                type="button"
                class="text-sm font-semibold text-emerald-700 hover:text-emerald-900 hover:underline"
                @click="showProfit = !showProfit"
            >
                <span x-show="!showProfit">Show owner profit details</span>
                <span x-show="showProfit" x-cloak>Hide owner profit details</span>
            </button>
            <div class="mt-4 space-y-2 rounded-2xl border border-emerald-100 bg-gradient-to-br from-emerald-50 to-teal-50 p-5 text-sm ring-1 ring-emerald-100/80" x-show="showProfit" x-cloak>
                <div class="flex justify-between text-slate-700">
                    <span>Cost of goods sold</span>
                    <span class="tabular-nums font-medium">{{ number_format((float) $sale->total_cost, 2) }} PKR</span>
                </div>
                <div class="flex justify-between border-t border-emerald-200/80 pt-2 text-base font-bold text-emerald-900">
                    <span>Profit</span>
                    <span class="tabular-nums">{{ number_format((float) $sale->total_profit, 2) }} PKR</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
