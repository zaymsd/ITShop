<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $buyPrice  = fake()->numberBetween(100000, 10000000);
        $sellPrice = (int) ($buyPrice * fake()->randomFloat(2, 1.1, 1.4)); // 10-40% margin

        return [
            'name'        => fake()->words(3, true),
            'sku'         => strtoupper(Str::random(3)) . '-' . fake()->unique()->numerify('####'),
            'barcode'     => fake()->optional()->ean13(),
            'buy_price'   => $buyPrice,
            'sell_price'  => $sellPrice,
            'stock'       => fake()->numberBetween(0, 100),
            'min_stock'   => fake()->numberBetween(3, 10),
            'specs'       => fake()->optional()->sentence(),
            'image'       => null,
            'category_id' => Category::factory(),
            'brand_id'    => Brand::factory(),
            'supplier_id' => Supplier::factory(),
        ];
    }

    /** State: low stock (at or below min_stock) */
    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock'     => 1,
            'min_stock' => 5,
        ]);
    }
}
