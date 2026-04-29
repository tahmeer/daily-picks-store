@extends('layouts.app')

@section('title', 'Add Item')

@section('content')
<div class="mx-auto max-w-2xl space-y-8">
    <div>
        <p class="font-display text-xs font-semibold uppercase tracking-widest text-emerald-600/90">Inventory</p>
        <h1 class="page-title">Add item</h1>
        <p class="page-subtitle">Product details and opening stock.</p>
    </div>

    <form
        method="post"
        action="{{ route('items.store') }}"
        class="card-elevated space-y-8 p-6 sm:p-8"
        x-data="{ submitting: false }"
        @submit="submitting = true"
    >
        @csrf
        @include('items.form-fields', ['units' => $units])
        <div class="flex flex-wrap justify-end gap-3 border-t border-slate-100 pt-6">
            <a href="{{ route('items.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">
                <span x-show="!submitting">Save item</span>
                <span x-show="submitting" x-cloak>Saving…</span>
            </button>
        </div>
    </form>
</div>
@endsection
