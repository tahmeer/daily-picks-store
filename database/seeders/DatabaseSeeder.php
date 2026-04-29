<?php

namespace Database\Seeders;

use App\Enums\ItemUnit;
use App\Models\Item;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['name' => 'Aata (Flour)', 'unit' => ItemUnit::Kg, 'purchase_price' => 52, 'selling_price' => 60, 'current_stock' => 50, 'low_stock_alert' => 10],
            ['name' => 'Chawal (Rice)', 'unit' => ItemUnit::Kg, 'purchase_price' => 120, 'selling_price' => 140, 'current_stock' => 30, 'low_stock_alert' => 8],
            ['name' => 'Daal Mash', 'unit' => ItemUnit::Kg, 'purchase_price' => 180, 'selling_price' => 210, 'current_stock' => 15, 'low_stock_alert' => 5],
            ['name' => 'Cheeni (Sugar)', 'unit' => ItemUnit::Kg, 'purchase_price' => 90, 'selling_price' => 100, 'current_stock' => 40, 'low_stock_alert' => 10],
            ['name' => 'Ghee', 'unit' => ItemUnit::Kg, 'purchase_price' => 480, 'selling_price' => 520, 'current_stock' => 10, 'low_stock_alert' => 3],
            ['name' => 'Tel (Oil)', 'unit' => ItemUnit::Litre, 'purchase_price' => 300, 'selling_price' => 330, 'current_stock' => 20, 'low_stock_alert' => 5],
            ['name' => 'Namak (Salt)', 'unit' => ItemUnit::Kg, 'purchase_price' => 25, 'selling_price' => 35, 'current_stock' => 20, 'low_stock_alert' => 5],
            ['name' => 'Chai Patti (Tea)', 'unit' => ItemUnit::Kg, 'purchase_price' => 1200, 'selling_price' => 1400, 'current_stock' => 5, 'low_stock_alert' => 1],
        ];

        foreach ($rows as $row) {
            Item::query()->updateOrCreate(
                ['name' => $row['name']],
                [
                    'unit' => $row['unit'],
                    'purchase_price' => $row['purchase_price'],
                    'selling_price' => $row['selling_price'],
                    'current_stock' => $row['current_stock'],
                    'low_stock_alert' => $row['low_stock_alert'],
                ]
            );
        }
    }
}
