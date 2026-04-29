@php
    $item = $item ?? null;
    $isEdit = $item !== null;
@endphp

<div
    class="grid gap-6 sm:grid-cols-2"
    x-data="{
        purchase: {{ old('purchase_price', $item?->purchase_price ?? 0) }},
        selling: {{ old('selling_price', $item?->selling_price ?? 0) }},
        margin() {
            let s = parseFloat(this.selling);
            let p = parseFloat(this.purchase);
            if (!isFinite(s) || s <= 0) return '0.00';
            return (((s - p) / s) * 100).toFixed(2);
        }
    }"
>
    <div class="sm:col-span-2">
        <label for="name" class="block text-sm font-medium text-slate-700">Item name</label>
        <input type="text" name="name" id="name" required value="{{ old('name', $item?->name) }}"
            class="input-store mt-2 w-full"
            placeholder="e.g. Aata, چاول">
    </div>

    <div>
        <label for="unit" class="block text-sm font-medium text-slate-700">Unit</label>
        <select name="unit" id="unit" required
            class="input-store mt-2 w-full">
            @foreach ($units as $u)
                <option value="{{ $u->value }}" @selected(old('unit', $item?->unit?->value) === $u->value)>{{ $u->value }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="low_stock_alert" class="block text-sm font-medium text-slate-700">Low stock alert</label>
        <input type="number" step="0.001" min="0" name="low_stock_alert" id="low_stock_alert" required
            value="{{ old('low_stock_alert', $item?->low_stock_alert ?? 0) }}"
            class="input-store mt-2 w-full">
    </div>

    <div>
        <label for="purchase_price" class="block text-sm font-medium text-slate-700">Purchase price per unit (PKR)</label>
        <input type="number" step="0.01" min="0" name="purchase_price" id="purchase_price" required
            x-model.number="purchase"
            class="input-store mt-2 w-full">
    </div>

    <div>
        <label for="selling_price" class="block text-sm font-medium text-slate-700">Selling price per unit (PKR)</label>
        <input type="number" step="0.01" min="0" name="selling_price" id="selling_price" required
            x-model.number="selling"
            class="input-store mt-2 w-full">
    </div>

    <div class="rounded-xl border border-emerald-200/80 bg-gradient-to-r from-emerald-50 to-teal-50 px-4 py-3 sm:col-span-2 ring-1 ring-emerald-100/80">
        <p class="text-sm font-medium text-emerald-950">Profit margin (on selling price): <span class="font-bold tabular-nums text-emerald-800" x-text="margin() + '%'"></span></p>
    </div>

    <div class="sm:col-span-2">
        <label for="current_stock" class="block text-sm font-medium text-slate-700">{{ $isEdit ? 'Current stock' : 'Opening stock' }}</label>
        <input type="number" step="0.001" min="0" name="current_stock" id="current_stock" required
            value="{{ old('current_stock', $item?->current_stock ?? 0) }}"
            class="input-store mt-2 w-full">
    </div>
</div>
