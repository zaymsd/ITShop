<?php

namespace Database\Factories;

use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Purchase>
 */
class PurchaseFactory extends Factory
{
    public function definition(): array
    {
        $date = fake()->dateTimeBetween('-60 days', 'now')->format('Ymd');
        $seq  = str_pad(fake()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT);

        return [
            'supplier_id'   => Supplier::factory(),
            'user_id'       => User::factory()->admin(),
            'invoice_no'    => "PO-{$date}-{$seq}",
            'total'         => fake()->numberBetween(500000, 20000000),
            'purchase_date' => fake()->dateTimeBetween('-60 days', 'now')->format('Y-m-d'),
        ];
    }
}
