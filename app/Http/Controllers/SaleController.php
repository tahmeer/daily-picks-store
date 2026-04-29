<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Support\ReceiptNumberGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function create(): View
    {
        return view('sales.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.item_id' => ['required', 'integer', 'exists:items,id'],
            'lines.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'lines.*.selling_price_per_unit' => ['required', 'numeric', 'min:0'],
        ]);

        $mergedLines = $this->mergedLines($validated['lines']);

        $sale = DB::transaction(function () use ($mergedLines, $validated) {
                $totalAmount = 0;
                $totalCost = 0;
                $totalProfit = 0;
                $lineRows = [];

                foreach ($mergedLines as $line) {
                    /** @var Item|null $item */
                    $item = Item::query()->lockForUpdate()->find($line['item_id']);

                    if ($item === null) {
                        throw ValidationException::withMessages([
                            'lines' => ['One or more items no longer exist.'],
                        ]);
                    }

                    $qty = (float) $line['quantity'];
                    $sellPrice = round((float) $line['selling_price_per_unit'], 2);
                    $purchasePrice = round((float) $item->purchase_price, 2);

                    if ($qty > (float) $item->current_stock) {
                        throw ValidationException::withMessages([
                            'lines' => ['Insufficient stock for '.$item->name.' (available: '.number_format((float) $item->current_stock, 3).').'],
                        ]);
                    }

                    $lineSelling = round($qty * $sellPrice, 2);
                    $lineCost = round($qty * $purchasePrice, 2);
                    $lineProfit = round($lineSelling - $lineCost, 2);

                    $totalAmount += $lineSelling;
                    $totalCost += $lineCost;
                    $totalProfit += $lineProfit;

                    $lineRows[] = [
                        'item' => $item,
                        'qty' => $qty,
                        'sell_price' => $sellPrice,
                        'purchase_price' => $purchasePrice,
                        'line_selling' => $lineSelling,
                        'line_cost' => $lineCost,
                        'line_profit' => $lineProfit,
                    ];
                }

                $receiptNumber = ReceiptNumberGenerator::next();

                $sale = Sale::query()->create([
                    'receipt_number' => $receiptNumber,
                    'customer_phone' => $validated['customer_phone'] ?? null,
                    'total_amount' => round($totalAmount, 2),
                    'total_cost' => round($totalCost, 2),
                    'total_profit' => round($totalProfit, 2),
                ]);

                foreach ($lineRows as $row) {
                    $item = $row['item'];

                    SaleItem::query()->create([
                        'sale_id' => $sale->id,
                        'item_id' => $item->id,
                        'item_name' => $item->name,
                        'quantity' => $row['qty'],
                        'unit' => $item->unit->value,
                        'purchase_price_per_unit' => $row['purchase_price'],
                        'selling_price_per_unit' => $row['sell_price'],
                        'total_selling_price' => $row['line_selling'],
                        'total_cost_price' => $row['line_cost'],
                        'profit' => $row['line_profit'],
                    ]);

                    $item->current_stock = round((float) $item->current_stock - $row['qty'], 3);
                    $item->save();
                }

            return $sale->fresh(['saleItems']);
        });

        return redirect()
            ->route('sales.receipt', $sale)
            ->with('success', 'Sale recorded successfully.');
    }

    /**
     * @param  array<int, array{item_id:int|string, quantity:numeric-string|float|int, selling_price_per_unit:numeric-string|float}>  $lines
     * @return array<int, array{item_id:int, quantity:float, selling_price_per_unit:float}>
     */
    private function mergedLines(array $lines): array
    {
        $map = [];

        foreach ($lines as $line) {
            $id = (int) $line['item_id'];

            if (! isset($map[$id])) {
                $map[$id] = [
                    'item_id' => $id,
                    'quantity' => (float) $line['quantity'],
                    'selling_price_per_unit' => (float) $line['selling_price_per_unit'],
                ];

                continue;
            }

            $map[$id]['quantity'] += (float) $line['quantity'];
            $map[$id]['selling_price_per_unit'] = (float) $line['selling_price_per_unit'];
        }

        return array_values($map);
    }

    public function index(Request $request): View
    {
        $query = Sale::query()->withCount('saleItems')->latest();

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->input('to'));
        }

        if ($request->filled('q')) {
            $q = $request->string('q')->trim();
            $query->where(function ($qry) use ($q) {
                $qry->where('receipt_number', 'like', '%'.$q.'%')
                    ->orWhere('customer_phone', 'like', '%'.$q.'%');
            });
        }

        $sales = $query->paginate(15)->withQueryString();

        return view('sales.index', compact('sales'));
    }

    public function receipt(Sale $sale): View
    {
        $sale->load('saleItems');

        return view('sales.receipt', compact('sale'));
    }
}
