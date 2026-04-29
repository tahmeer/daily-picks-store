<?php

namespace App\Models;

use App\Enums\ItemUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected $fillable = [
        'name',
        'unit',
        'purchase_price',
        'selling_price',
        'current_stock',
        'low_stock_alert',
    ];

    protected function casts(): array
    {
        return [
            'unit' => ItemUnit::class,
            'purchase_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'current_stock' => 'decimal:3',
            'low_stock_alert' => 'decimal:3',
        ];
    }

    public function stockEntries(): HasMany
    {
        return $this->hasMany(StockEntry::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function profitMarginPercent(): float
    {
        if ((float) $this->selling_price <= 0) {
            return 0;
        }

        return round(
            (((float) $this->selling_price - (float) $this->purchase_price) / (float) $this->selling_price) * 100,
            2
        );
    }

    public function stockStatus(): string
    {
        $stock = (float) $this->current_stock;
        $threshold = (float) $this->low_stock_alert;

        if ($stock <= 0) {
            return 'out';
        }
        if ($stock <= $threshold) {
            return 'low';
        }

        return 'ok';
    }
}
