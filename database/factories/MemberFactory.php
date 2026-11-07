<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\MembershipPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'gender' => fake()->randomElement(['Male', 'Female', 'Non-binary', 'Prefer not to say']),
            'age' => fake()->numberBetween(18, 65),
            'address' => fake()->address(),
            'membership_plan_id' => MembershipPlan::factory(),
            'started_at' => today()->subDays(10),
            'expires_at' => today()->addDays(20),
        ];
    }
}
