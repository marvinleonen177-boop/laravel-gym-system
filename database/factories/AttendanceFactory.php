<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
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
            'checked_in_at' => now(),
            'checked_out_at' => now()->addHour(),
        ];
    }
}
