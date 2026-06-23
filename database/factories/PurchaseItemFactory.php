<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseItem>
 */
class PurchaseItemFactory extends Factory
{
    public function definition(): array
    {
        $qty      = fake()->numberBetween(1, 20);
        $buyPrice = fake()->numberBetween(50000, 5000000);

        return [
            'purchase_id' => Purchase::factory(),
            'product_id'  => Product::factory(),
            'qty'         => $qty,
            'buy_price'   => $buyPrice,
            'subtotal'    => $qty * $buyPrice,
        ];
    }
}
