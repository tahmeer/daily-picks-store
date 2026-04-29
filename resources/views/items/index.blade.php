@extends('layouts.app')

@section('title', 'Inventory')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="font-display text-xs font-semibold uppercase tracking-widest text-emerald-600/90">Products</p>
            <h1 class="page-title">Inventory</h1>
            <p class="page-subtitle">Prices, stock levels, and margins — Urdu names supported in search.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('restock-logs.index') }}" class="btn-secondary">
                <svg class="mr-2 h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Restock logs
            </a>
            <a href="{{ route('items.create') }}" class="btn-primary">
                <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add item
            </a>
        </div>
    </div>

    <div class="card-elevated p-5">
        <form method="get" action="{{ route('items.index') }}" class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-end">
            <div class="min-w-[200px] flex-1">
                <label for="q" class="block text-sm font-medium text-slate-700">Search by name</label>
                <input type="search" name="q" id="q" value="{{ request('q') }}" placeholder="e.g. Aata, چاول"
                    class="input-store mt-2 w-full">
            </div>
            <button type="submit" class="btn-secondary h-[42px] shrink-0 px-6">Search</button>
        </form>
    </div>

    <div class="card-elevated overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-slate-800 to-slate-900 text-left text-xs font-semibold uppercase tracking-wider text-white">
                        <th class="px-5 py-4 first:pl-8">Name</th>
                        <th class="px-5 py-4">Unit</th>
                        <th class="px-5 py-4 text-right">Purchase</th>
                        <th class="px-5 py-4 text-right">Selling</th>
                        <th class="px-5 py-4 text-right">Stock</th>
                        <th class="px-5 py-4 text-right">Margin</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4 text-right last:pr-8">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach ($items as $item)
                        @php
                            $status = $item->stockStatus();
                            $badge = match ($status) {
                                'out' => ['label' => 'Out', 'class' => 'bg-red-100 text-red-800 ring-red-200/80'],
                                'low' => ['label' => 'Low', 'class' => 'bg-amber-100 text-amber-950 ring-amber-200/80'],
                                default => ['label' => 'OK', 'class' => 'bg-emerald-100 text-emerald-900 ring-emerald-200/80'],
                            };
                        @endphp
                        <tr class="transition hover:bg-emerald-50/40">
                            <td class="px-5 py-3.5 pl-8 font-semibold text-slate-900">{{ $item->name }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $item->unit->value }}</td>
                            <td class="px-5 py-3.5 text-right tabular-nums text-slate-700">{{ number_format((float) $item->purchase_price, 2) }}</td>
                            <td class="px-5 py-3.5 text-right tabular-nums font-medium text-slate-900">{{ number_format((float) $item->selling_price, 2) }}</td>
                            <td class="px-5 py-3.5 text-right tabular-nums">{{ number_format((float) $item->current_stock, 3) }}</td>
                            <td class="px-5 py-3.5 text-right tabular-nums text-emerald-800">{{ number_format($item->profitMarginPercent(), 2) }}%</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                            </td>
                            <td class="px-5 py-3.5 pr-8 text-right">
                                <div class="flex flex-wrap justify-end gap-2 text-sm font-semibold">
                                    <a href="{{ route('items.edit', $item) }}" class="text-emerald-700 hover:text-emerald-900 hover:underline">Edit</a>
                                    <span class="text-slate-300">|</span>
                                    <a href="{{ route('items.restock.form', $item) }}" class="text-slate-700 hover:text-slate-900 hover:underline">Restock</a>
                                    <span class="text-slate-300">|</span>
                                    <form action="{{ route('items.destroy', $item) }}" method="post" class="inline" onsubmit="return confirm('Delete this item?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 hover:underline">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($items->hasPages())
            <div class="border-t border-slate-100 bg-slate-50/80 px-5 py-4">{{ $items->links() }}</div>
        @endif
    </div>
</div>
@endsection
