<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Member;
use App\Models\MembershipPlan;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminPassword = config('gym.admin_password');
        if (app()->isProduction() && blank($adminPassword)) {
            throw new RuntimeException('Set GYM_ADMIN_PASSWORD before seeding the production administrator account.');
        }

        $adminPassword ??= Str::password(20);
        $admin = User::firstOrCreate(
            ['email' => config('gym.admin_email')],
            [
                'name' => 'IRONCORE Admin',
                'password' => Hash::make($adminPassword),
                'role' => 'admin',
            ],
        );

        if ($admin->wasRecentlyCreated && ! app()->isProduction()) {
            $this->command?->info('Demo admin login: '.$admin->email.' / '.$adminPassword);
        }

        if (app()->isProduction()) {
            return;
        }

        $plans = collect([
            ['name' => 'Daily Pass', 'duration_days' => 1, 'price' => 250],
            ['name' => 'Weekly Membership', 'duration_days' => 7, 'price' => 800],
            ['name' => 'Monthly Membership', 'duration_days' => 30, 'price' => 1800],
            ['name' => 'Quarterly Membership', 'duration_days' => 90, 'price' => 4800],
            ['name' => 'Annual Membership', 'duration_days' => 365, 'price' => 16000],
        ])->mapWithKeys(fn (array $plan): array => [
            $plan['name'] => MembershipPlan::firstOrCreate(['name' => $plan['name']], $plan),
        ]);

        foreach ([
            ['Mia Santos', 'mia.santos@example.test', 'Monthly Membership', -20],
            ['Ethan Reyes', 'ethan.reyes@example.test', 'Quarterly Membership', -48],
            ['Sofia Cruz', 'sofia.cruz@example.test', 'Annual Membership', -100],
            ['Noah Garcia', 'noah.garcia@example.test', 'Monthly Membership', -35],
            ['Isabella Lim', 'isabella.lim@example.test', 'Weekly Membership', -10],
            ['Liam Flores', 'liam.flores@example.test', 'Quarterly Membership', -75],
            ['Ava Mendoza', 'ava.mendoza@example.test', 'Monthly Membership', -50],
            ['Lucas Aquino', 'lucas.aquino@example.test', 'Annual Membership', -160],
            ['Chloe Tan', 'chloe.tan@example.test', 'Monthly Membership', -45],
            ['Caleb Rivera', 'caleb.rivera@example.test', 'Weekly Membership', -18],
        ] as $index => [$name, $email, $planName, $daysAgo]) {
            $plan = $plans->get($planName);
            $startedAt = today()->addDays($daysAgo);
            $expiresAt = $startedAt->copy()->addDays($plan->duration_days - 1);
            $member = Member::firstOrCreate(
                ['email' => $email],
                [
                    'full_name' => $name,
                    'phone' => fake()->phoneNumber(),
                    'gender' => fake()->randomElement(['Female', 'Male']),
                    'age' => fake()->numberBetween(20, 45),
                    'address' => fake()->city().', Philippines',
                    'membership_plan_id' => $plan->id,
                    'started_at' => $startedAt,
                    'expires_at' => $expiresAt,
                ],
            );

            if (! $member->payments()->exists()) {
                Payment::create([
                    'member_id' => $member->id,
                    'membership_plan_id' => $plan->id,
                    'amount' => $plan->price,
                    'status' => 'paid',
                    'payment_method' => fake()->randomElement(['Cash', 'GCash', 'Bank Transfer']),
                    'paid_at' => now()->subDays(abs($daysAgo) % 20),
                ]);
            }

            if ($index < 4 && ! $member->attendances()->whereDate('checked_in_at', today())->exists()) {
                Attendance::create([
                    'member_id' => $member->id,
                    'checked_in_at' => now()->subMinutes(($index + 1) * 55),
                    'checked_out_at' => $index === 0 ? null : now()->subMinutes(($index + 1) * 15),
                ]);
            }
        }
    }
}
