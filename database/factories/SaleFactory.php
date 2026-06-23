<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sale>
 */
class SaleFactory extends Factory
{
    public function definition(): array
    {
        $total      = fake()->numberBetween(100000, 5000000);
        $discount   = fake()->numberBetween(0, (int)($total * 0.1));
        $tax        = (int)(($total - $discount) * 0.11); // PPN 11%
        $grandTotal = $total - $discount + $tax;
        $paid       = $grandTotal + fake()->numberBetween(0, 50000);

        $date   = fake()->dateTimeBetween('-30 days', 'now')->format('Ymd');
        $seq    = str_pad(fake()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT);

        return [
            'invoice_no'     => "INV-{$date}-{$seq}",
            'user_id'        => User::factory()->petugas(),
            'customer_name'  => fake()->optional()->name(),
            'total'          => $total,
            'discount'       => $discount,
            'tax'            => $tax,
            'grand_total'    => $grandTotal,
            'payment_method' => fake()->randomElement(['cash', 'non-cash']),
            'paid_amount'    => $paid,
            'change_amount'  => $paid - $grandTotal,
            'status'         => 'completed',
        ];
    }
}
