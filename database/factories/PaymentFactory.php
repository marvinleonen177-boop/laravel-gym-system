<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'member_id' => Member::factory(),
            'membership_plan_id' => fn (array $attributes): int => Member::find($attributes['member_id'])->membership_plan_id,
            'amount' => fake()->randomElement([250, 800, 1800, 4800]),
            'status' => 'paid',
            'payment_method' => fake()->randomElement(['Cash', 'GCash', 'Bank Transfer']),
            'paid_at' => now(),
        ];
    }
}
