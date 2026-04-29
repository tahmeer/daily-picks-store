@extends('layouts.app')

@section('title', 'Restock logs')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <p class="font-display text-xs font-semibold uppercase tracking-widest text-emerald-600/90">Audit trail</p>
            <h1 class="page-title">Restock logs</h1>
            <p class="page-subtitle">Every purchase batch: qty, rate, total cost, notes. Also written to <code class="rounded-md bg-slate-100 px-1.5 py-0.5 font-mono text-xs">storage/logs/laravel.log</code>.</p>
        </div>
        <a href="{{ route('items.index') }}" class="btn-secondary shrink-0">
            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Inventory
        </a>
    </div>

    <div class="card-elevated p-5 sm:p-6">
        <form method="get" action="{{ route('restock-logs.index') }}" class="flex flex-col gap-4 lg:flex-row lg:flex-wrap lg:items-end">
            <div class="min-w-[180px] flex-1">
                <label for="q" class="block text-xs font-medium text-slate-600">Item name</label>
                <input type="search" name="q" id="q" value="{{ request('q') }}" placeholder="Search…"
                    class="input-store mt-2 w-full">
            </div>
            <div class="min-w-[220px]">
                <label for="item_id" class="block text-xs font-medium text-slate-600">Item</label>
                <select name="item_id" id="item_id" class="input-store mt-2 w-full">
                    <option value="">All items</option>
                    @foreach ($itemsForFilter as $it)
                        <option value="{{ $it->id }}" @selected(request('item_id') == $it->id)>{{ $it->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="from" class="block text-xs font-medium text-slate-600">From</label>
                <input type="date" name="from" id="from" value="{{ request('from') }}" class="input-store mt-2">
            </div>
            <div>
                <label for="to" class="block text-xs font-medium text-slate-600">To</label>
                <input type="date" name="to" id="to" value="{{ request('to') }}" class="input-store mt-2">
            </div>
            <button type="submit" class="btn-primary h-[42px] px-6">Filter</button>
            <a href="{{ route('restock-logs.index') }}" class="btn-secondary h-[42px] px-6">Clear</a>
        </form>
    </div>

    <div class="card-elevated overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-slate-800 to-slate-900 text-left text-xs font-semibold uppercase tracking-wider text-white">
                        <th class="px-5 py-4 first:pl-8">#</th>
                        <th class="px-5 py-4">When</th>
                        <th class="px-5 py-4">Item</th>
                        <th class="px-5 py-4 text-right">Qty</th>
                        <th class="px-5 py-4 text-right">Rate</th>
                        <th class="px-5 py-4 text-right">Cost</th>
                        <th class="px-5 py-4 last:pr-8">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($entries as $entry)
                        <tr class="align-top transition hover:bg-emerald-50/40">
                            <td class="px-5 py-3.5 pl-8 font-mono text-xs text-slate-500">{{ $entry->id }}</td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-slate-700">{{ $entry->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-5 py-3.5">
                                <span class="font-semibold text-slate-900">{{ $entry->item->name }}</span>
                                <span class="block text-xs text-slate-500">{{ $entry->item->unit->value }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-right tabular-nums font-medium">{{ number_format((float) $entry->quantity, 3) }}</td>
                            <td class="px-5 py-3.5 text-right tabular-nums">{{ number_format((float) $entry->purchase_price_per_unit, 2) }}</td>
                            <td class="px-5 py-3.5 text-right tabular-nums font-semibold text-emerald-800">{{ number_format((float) $entry->total_cost, 2) }}</td>
                            <td class="px-5 py-3.5 pr-8 max-w-xs text-slate-600"><span class="line-clamp-3 whitespace-pre-wrap">{{ $entry->notes ?: '—' }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-14 text-center text-slate-500">No restock entries yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($entries->hasPages())
            <div class="border-t border-slate-100 bg-slate-50/80 px-5 py-4">{{ $entries->links() }}</div>
        @endif
    </div>

    <p class="text-center text-xs text-slate-500">
        Server file log channel: <code class="rounded bg-slate-100 px-1.5 py-0.5 font-mono">Restock recorded</code>
    </p>
</div>
@endsection
