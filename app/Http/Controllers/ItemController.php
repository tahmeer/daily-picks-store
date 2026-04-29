<?php

namespace App\Http\Controllers;

use App\Enums\ItemUnit;
use App\Models\Item;
use App\Models\StockEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function index(Request $request): View
    {
        $query = Item::query()->orderBy('name');

        if ($request->filled('q')) {
            $q = $request->string('q')->trim();
            $query->where('name', 'like', '%'.$q.'%');
        }

        $items = $query->paginate(15)->withQueryString();

        return view('items.index', compact('items'));
    }

    public function create(): View
    {
        return view('items.create', ['units' => ItemUnit::cases()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'in:kg,gram,litre,piece'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'current_stock' => ['required', 'numeric', 'min:0'],
            'low_stock_alert' => ['required', 'numeric', 'min:0'],
        ]);

        Item::query()->create($validated);

        return redirect()->route('items.index')->with('success', 'Item created successfully.');
    }

    public function edit(Item $item): View
    {
        return view('items.edit', [
            'item' => $item,
            'units' => ItemUnit::cases(),
        ]);
    }

    public function update(Request $request, Item $item): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'in:kg,gram,litre,piece'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'current_stock' => ['required', 'numeric', 'min:0'],
            'low_stock_alert' => ['required', 'numeric', 'min:0'],
        ]);

        $item->update($validated);

        return redirect()->route('items.index')->with('success', 'Item updated successfully.');
    }

    public function destroy(Item $item): RedirectResponse
    {
        if ($item->saleItems()->exists()) {
            return redirect()->route('items.index')->with('error', 'Cannot delete an item that has sale history.');
        }

        $item->delete();

        return redirect()->route('items.index')->with('success', 'Item deleted.');
    }

    public function restockForm(Item $item): View
    {
        return view('items.restock', compact('item'));
    }

    public function restock(Request $request, Item $item): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'numeric', 'min:0.001'],
            'purchase_price_per_unit' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $qty = (float) $validated['quantity'];
        $price = (float) $validated['purchase_price_per_unit'];
        $totalCost = round($qty * $price, 2);

        $entry = StockEntry::query()->create([
            'item_id' => $item->id,
            'quantity' => $qty,
            'purchase_price_per_unit' => $price,
            'total_cost' => $totalCost,
            'notes' => $validated['notes'] ?? null,
        ]);

        $stockBefore = (float) $item->current_stock;

        $item->purchase_price = $price;
        $item->current_stock = $stockBefore + $qty;
        $item->save();

        Log::info('Restock recorded', [
            'stock_entry_id' => $entry->id,
            'item_id' => $item->id,
            'item_name' => $item->name,
            'quantity_added' => $qty,
            'purchase_price_per_unit' => $price,
            'total_cost_pkr' => $totalCost,
            'stock_before' => $stockBefore,
            'stock_after' => (float) $item->current_stock,
            'notes' => $entry->notes,
        ]);

        return redirect()
            ->route('restock-logs.index')
            ->with('success', 'Stock updated successfully. Entry #'.$entry->id.' logged.');
    }

    public function search(Request $request): JsonResponse
    {
        $q = $request->string('q')->trim()->toString();

        if ($q === '') {
            return response()->json([]);
        }

        $items = Item::query()
            ->where('name', 'like', '%'.$q.'%')
            ->orderBy('name')
            ->limit(20)
            ->get()
            ->map(fn (Item $item) => [
                'id' => $item->id,
                'name' => $item->name,
                'unit' => $item->unit->value,
                'purchase_price' => (float) $item->purchase_price,
                'selling_price' => (float) $item->selling_price,
                'current_stock' => (float) $item->current_stock,
            ]);

        return response()->json($items);
    }

    public function restockLogs(Request $request): View
    {
        $query = StockEntry::query()->with('item')->latest();

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->integer('item_id'));
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->input('to'));
        }

        if ($request->filled('q')) {
            $needle = $request->string('q')->trim();
            $query->whereHas('item', function ($qry) use ($needle) {
                $qry->where('name', 'like', '%'.$needle.'%');
            });
        }

        $entries = $query->paginate(25)->withQueryString();

        $itemsForFilter = Item::query()->orderBy('name')->get(['id', 'name']);

        return view('items.restock-logs', compact('entries', 'itemsForFilter'));
    }
}
