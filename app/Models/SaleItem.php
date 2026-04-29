<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'item_id',
        'item_name',
        'quantity',
        'unit',
        'purchase_price_per_unit',
        'selling_price_per_unit',
        'total_selling_price',
        'total_cost_price',
        'profit',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
            'purchase_price_per_unit' => 'decimal:2',
            'selling_price_per_unit' => 'decimal:2',
            'total_selling_price' => 'decimal:2',
            'total_cost_price' => 'decimal:2',
            'profit' => 'decimal:2',
        ];
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
