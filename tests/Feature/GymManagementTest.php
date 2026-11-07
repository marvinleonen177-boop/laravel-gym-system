<?php

use App\Models\Attendance;
use App\Models\Member;
use App\Models\MembershipPlan;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

it('does not allow a non-admin user to view the gym dashboard', function () {
    $user = User::factory()->create(['role' => 'member']);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertForbidden();
});

it('allows an admin to sign in and see current gym metrics', function () {
    $admin = User::factory()->create([
        'email' => 'admin@example.test',
        'password' => 'password',
        'role' => 'admin',
    ]);
    $plan = MembershipPlan::factory()->create(['name' => 'Monthly Membership']);
    Member::factory()->create([
        'full_name' => 'Mia Santos',
        'membership_plan_id' => $plan->id,
        'expires_at' => today()->addDays(15),
    ]);
    Member::factory()->create([
        'full_name' => 'Noah Garcia',
        'membership_plan_id' => $plan->id,
        'expires_at' => today()->subDay(),
    ]);

    $this->post(route('login.store'), [
        'email' => 'admin@example.test',
        'password' => 'password',
    ])->assertRedirect(route('dashboard'));

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Mia Santos')
        ->assertSee('Noah Garcia')
        ->assertSee('Active memberships');

    $this->assertAuthenticatedAs($admin);
});

it('creates a member and calculates membership expiry from the selected plan', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $plan = MembershipPlan::factory()->create(['duration_days' => 30]);

    $this->actingAs($admin)->post(route('members.store'), [
        'full_name' => 'Jordan Lee',
        'email' => 'jordan@example.test',
        'phone' => '09171234567',
        'gender' => 'Prefer not to say',
        'age' => 28,
        'address' => 'Makati City',
        'membership_plan_id' => $plan->id,
        'started_at' => '2030-01-01',
    ])->assertRedirect(route('dashboard', ['section' => 'members']));

    $this->assertDatabaseHas('members', [
        'email' => 'jordan@example.test',
        'expires_at' => '2030-01-30 00:00:00',
    ]);
});

it('rejects invalid member data without creating a member', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->post(route('members.store'), [
        'full_name' => '',
        'email' => 'not-an-email',
    ])->assertSessionHasErrors(['full_name', 'email', 'membership_plan_id', 'started_at']);

    $this->assertDatabaseCount('members', 0);
});

it('allows an existing member to keep an inactive plan when edited', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $plan = MembershipPlan::factory()->create();
    $member = Member::factory()->create(['membership_plan_id' => $plan->id]);

    $this->actingAs($admin)->patch(route('plans.update', $plan), [
        'name' => $plan->name,
        'duration_days' => $plan->duration_days,
        'price' => $plan->price,
        'is_active' => 0,
    ])->assertRedirect(route('dashboard', ['section' => 'memberships']));

    $this->patch(route('members.update', $member), [
        'full_name' => $member->full_name,
        'email' => $member->email,
        'phone' => $member->phone,
        'gender' => $member->gender,
        'age' => $member->age,
        'address' => $member->address,
        'membership_plan_id' => $plan->id,
        'started_at' => $member->started_at->toDateString(),
    ])->assertRedirect(route('dashboard', ['section' => 'members']));
});

it('does not allow a new member to join an inactive plan', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $plan = MembershipPlan::factory()->create(['is_active' => false]);

    $this->actingAs($admin)->post(route('members.store'), [
        'full_name' => 'Taylor Park',
        'email' => 'taylor@example.test',
        'membership_plan_id' => $plan->id,
        'started_at' => today()->toDateString(),
    ])->assertSessionHasErrors(['membership_plan_id']);

    $this->assertDatabaseCount('members', 0);
});

it('prevents expired members from checking in', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $member = Member::factory()->create(['expires_at' => today()->subDay()]);

    $this->actingAs($admin)->post(route('attendance.check-in'), [
        'member_id' => $member->id,
    ])->assertSessionHasErrors([
        'member_id' => 'This membership has expired. Renew the membership before check-in.',
    ]);

    $this->assertDatabaseCount('attendances', 0);
});

it('records payment details and makes the receipt available', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $member = Member::factory()->create();

    $response = $this->actingAs($admin)->post(route('payments.store'), [
        'member_id' => $member->id,
        'amount' => 1800,
        'payment_method' => 'GCash',
        'status' => 'paid',
    ]);

    $response->assertRedirect(route('dashboard', ['section' => 'payments']));

    $payment = Payment::query()->firstOrFail();
    $this->assertDatabaseHas('payments', [
        'id' => $payment->id,
        'member_id' => $member->id,
        'status' => 'paid',
        'payment_method' => 'GCash',
    ]);
    $this->actingAs($admin)->get(route('payments.receipt', $payment))
        ->assertOk()
        ->assertSee('Payment receipt')
        ->assertSee($member->full_name);
});

it('checks in an active member once and records their checkout', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $member = Member::factory()->create(['expires_at' => today()->addDay()]);

    $this->actingAs($admin)->post(route('attendance.check-in'), [
        'member_id' => $member->id,
    ])->assertRedirect(route('dashboard', ['section' => 'attendance']));

    $attendance = Attendance::query()->firstOrFail();
    expect($attendance->checked_out_at)->toBeNull();

    $this->actingAs($admin)->patch(route('attendance.check-out', $attendance))
        ->assertRedirect(route('dashboard', ['section' => 'attendance']));

    expect($attendance->fresh()->checked_out_at)->not->toBeNull();
});
