@extends('layouts.app')

@section('title', 'Restock')

@section('content')
<div class="mx-auto max-w-lg space-y-8">
    <div>
        <p class="font-display text-xs font-semibold uppercase tracking-widest text-emerald-600/90">Stock in</p>
        <h1 class="page-title">Restock</h1>
        <p class="page-subtitle">{{ $item->name }} — current: {{ number_format((float) $item->current_stock, 3) }} {{ $item->unit->value }}</p>
    </div>

    <form
        method="post"
        action="{{ route('items.restock', $item) }}"
        class="card-elevated space-y-6 p-6 sm:p-8"
        x-data="{ submitting: false }"
        @submit="submitting = true"
    >
        @csrf
        <div>
            <label for="quantity" class="block text-sm font-medium text-slate-700">Quantity to add</label>
            <input type="number" step="0.001" min="0.001" name="quantity" id="quantity" required value="{{ old('quantity') }}"
                class="input-store mt-2 w-full">
        </div>
        <div>
            <label for="purchase_price_per_unit" class="block text-sm font-medium text-slate-700">Purchase price per unit (PKR)</label>
            <input type="number" step="0.01" min="0" name="purchase_price_per_unit" id="purchase_price_per_unit" required value="{{ old('purchase_price_per_unit', $item->purchase_price) }}"
                class="input-store mt-2 w-full">
            <p class="mt-2 text-xs text-slate-500">Updates this item’s default purchase price.</p>
        </div>
        <div>
            <label for="notes" class="block text-sm font-medium text-slate-700">Notes (optional)</label>
            <textarea name="notes" id="notes" rows="3" class="input-store mt-2 w-full">{{ old('notes') }}</textarea>
        </div>
        <div class="flex flex-wrap justify-end gap-3 border-t border-slate-100 pt-6">
            <a href="{{ route('items.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">
                <span x-show="!submitting">Apply restock</span>
                <span x-show="submitting" x-cloak>Processing…</span>
            </button>
        </div>
    </form>
</div>
@endsection
