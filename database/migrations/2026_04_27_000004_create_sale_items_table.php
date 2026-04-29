<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->restrictOnDelete();
            $table->string('item_name');
            $table->string('unit');
            $table->decimal('quantity', 10, 3);
            $table->decimal('purchase_price_per_unit', 10, 2);
            $table->decimal('selling_price_per_unit', 10, 2);
            $table->decimal('total_selling_price', 10, 2);
            $table->decimal('total_cost_price', 10, 2);
            $table->decimal('profit', 10, 2);
            $table->timestamps();

            $table->index('sale_id');
            $table->index('item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
