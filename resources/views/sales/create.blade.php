@extends('layouts.app')

@section('title', 'New Sale')

@section('content')
<div
    class="space-y-8"
    x-data="{
        query: '',
        results: [],
        cart: [],
        customer_phone: '',
        submitting: false,
        searchTimer: null,
        scheduleSearch() {
            clearTimeout(this.searchTimer);
            this.searchTimer = setTimeout(() => this.searchItems(), 300);
        },
        async searchItems() {
            const q = this.query.trim();
            if (q.length < 1) {
                this.results = [];
                return;
            }
            try {
                const res = await fetch('{{ route("items.search") }}?q=' + encodeURIComponent(q), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                this.results = await res.json();
            } catch (e) {
                this.results = [];
            }
        },
        addItem(row) {
            const existing = this.cart.find((c) => c.item_id === row.id);
            if (existing) {
                existing.quantity = (parseFloat(existing.quantity) || 0) + 1;
                return;
            }
            this.cart.push({
                item_id: row.id,
                name: row.name,
                unit: row.unit,
                selling_price: parseFloat(row.selling_price),
                quantity: 1,
            });
            this.query = '';
            this.results = [];
        },
        removeLine(i) {
            this.cart.splice(i, 1);
        },
        lineTotal(line) {
            const q = parseFloat(line.quantity) || 0;
            const p = parseFloat(line.selling_price) || 0;
            return Math.round(q * p * 100) / 100;
        },
        subtotal() {
            return this.cart.reduce((s, line) => s + this.lineTotal(line), 0);
        },
    }"
>
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="font-display text-xs font-semibold uppercase tracking-widest text-emerald-600/90">Point of sale</p>
            <h1 class="page-title">New sale</h1>
            <p class="page-subtitle">Search by name, tap to add — adjust qty and price in the cart.</p>
        </div>
    </div>

    <form method="post" action="{{ route('sales.store') }}" class="grid gap-8 lg:grid-cols-2" @submit="submitting = true">
        @csrf

        <div class="card-elevated flex flex-col space-y-5 p-6 sm:p-8">
            <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <div>
                    <h2 class="font-display text-lg font-bold text-slate-900">Find items</h2>
                    <p class="text-xs text-slate-500">Live search</p>
                </div>
            </div>
            <div>
                <label for="search" class="block text-sm font-medium text-slate-700">Search</label>
                <input
                    id="search"
                    type="search"
                    x-model="query"
                    @input="scheduleSearch()"
                    placeholder="Type name…"
                    class="input-store mt-2 w-full"
                    autocomplete="off"
                >
            </div>
            <ul class="max-h-72 divide-y divide-slate-100 overflow-auto rounded-xl border border-slate-200 bg-slate-50/50 shadow-inner" x-show="results.length" x-cloak>
                <template x-for="row in results" :key="row.id">
                    <li>
                        <button
                            type="button"
                            class="flex w-full items-center justify-between gap-3 px-4 py-3.5 text-left text-sm font-medium transition hover:bg-emerald-50"
                            @click="addItem(row)"
                        >
                            <span class="text-slate-900" x-text="row.name"></span>
                            <span class="shrink-0 rounded-lg bg-white px-2 py-1 text-xs tabular-nums text-slate-600 shadow-sm ring-1 ring-slate-200">
                                <span x-text="parseFloat(row.selling_price).toFixed(2)"></span> PKR / <span x-text="row.unit"></span>
                            </span>
                        </button>
                    </li>
                </template>
            </ul>
            <p class="text-xs text-slate-500" x-show="query && !results.length">No matches — try another spelling.</p>
        </div>

        <div class="card-elevated flex flex-col space-y-5 p-6 sm:p-8">
            <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-100 text-teal-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </span>
                <div>
                    <h2 class="font-display text-lg font-bold text-slate-900">Cart</h2>
                    <p class="text-xs text-slate-500">Edit qty &amp; price</p>
                </div>
            </div>

            <div>
                <label for="customer_phone" class="block text-sm font-medium text-slate-700">Customer phone (optional)</label>
                <input type="text" name="customer_phone" id="customer_phone" x-model="customer_phone" placeholder="03xx…"
                    class="input-store mt-2 w-full">
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-100">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-slate-800 text-left text-xs font-semibold uppercase tracking-wide text-white">
                            <th class="px-3 py-3">Item</th>
                            <th class="px-3 py-3">Qty</th>
                            <th class="px-3 py-3">Price</th>
                            <th class="px-3 py-3 text-right">Total</th>
                            <th class="w-10 px-2 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(line, idx) in cart" :key="idx">
                            <tr class="border-b border-slate-100 align-top transition hover:bg-emerald-50/30">
                                <td class="px-3 py-3">
                                    <span class="font-medium text-slate-900" x-text="line.name"></span>
                                    <span class="block text-xs text-slate-500" x-text="line.unit"></span>
                                    <input type="hidden" :name="'lines[' + idx + '][item_id]'" :value="line.item_id">
                                </td>
                                <td class="px-3 py-3">
                                    <input type="number" step="0.001" min="0.001" class="input-store w-24 px-2 py-1.5 tabular-nums"
                                        :name="'lines[' + idx + '][quantity]'" x-model="line.quantity">
                                </td>
                                <td class="px-3 py-3">
                                    <input type="number" step="0.01" min="0" class="input-store w-28 px-2 py-1.5 tabular-nums"
                                        :name="'lines[' + idx + '][selling_price_per_unit]'" x-model.number="line.selling_price">
                                </td>
                                <td class="px-3 py-3 text-right tabular-nums font-semibold text-slate-900" x-text="lineTotal(line).toFixed(2)"></td>
                                <td class="px-2 py-3">
                                    <button type="button" class="text-sm font-semibold text-red-600 hover:text-red-800" @click="removeLine(idx)">×</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <template x-if="cart.length === 0">
                <p class="rounded-xl border border-dashed border-slate-200 bg-slate-50/80 py-8 text-center text-sm text-slate-500">Cart is empty — search and add items.</p>
            </template>

            <div class="flex items-center justify-between rounded-xl bg-gradient-to-r from-emerald-50 to-teal-50 px-4 py-4 ring-1 ring-emerald-100/80">
                <span class="font-semibold text-slate-800">Total</span>
                <span class="font-display text-2xl font-bold tabular-nums text-emerald-800"><span x-text="subtotal().toFixed(2)"></span> <span class="text-base font-semibold text-slate-500">PKR</span></span>
            </div>

            <button
                type="submit"
                class="btn-primary w-full justify-center py-3.5 text-base shadow-lg disabled:cursor-not-allowed disabled:opacity-40"
                :disabled="cart.length === 0 || submitting"
            >
                <span x-show="!submitting">Generate receipt</span>
                <span x-show="submitting" x-cloak>Processing…</span>
            </button>
        </div>
    </form>
</div>
@endsection
