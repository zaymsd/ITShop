<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SaleItem>
 */
class SaleItemFactory extends Factory
{
    public function definition(): array
    {
        $qty      = fake()->numberBetween(1, 5);
        $price    = fake()->numberBetween(50000, 2000000);
        $discount = fake()->numberBetween(0, (int)($price * 0.05));
        $subtotal = ($price * $qty) - $discount;

        return [
            'sale_id'    => Sale::factory(),
            'product_id' => Product::factory(),
            'qty'        => $qty,
            'price'      => $price,
            'discount'   => $discount,
            'subtotal'   => $subtotal,
        ];
    }
}
