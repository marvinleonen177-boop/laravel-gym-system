<?php

namespace Database\Factories;

use App\Models\MembershipPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MembershipPlan>
 */
class MembershipPlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'duration_days' => fake()->randomElement([1, 7, 30, 90, 365]),
            'price' => fake()->randomElement([250, 800, 1800, 4800, 16000]),
            'is_active' => true,
        ];
    }
}
