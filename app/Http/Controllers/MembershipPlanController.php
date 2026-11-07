<?php

namespace App\Http\Controllers;

use App\Models\MembershipPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MembershipPlanController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        MembershipPlan::create($this->validatedData($request));

        return redirect()->route('dashboard', ['section' => 'memberships'])->with('success', 'Membership plan created.');
    }

    public function update(Request $request, MembershipPlan $membershipPlan): RedirectResponse
    {
        $membershipPlan->update($this->validatedData($request, $membershipPlan));

        return redirect()->route('dashboard', ['section' => 'memberships'])->with('success', 'Membership plan updated.');
    }

    public function destroy(MembershipPlan $membershipPlan): RedirectResponse
    {
        if ($membershipPlan->members()->exists()) {
            return back()->withErrors(['plan' => 'This plan is assigned to members and cannot be deleted.']);
        }

        $membershipPlan->delete();

        return redirect()->route('dashboard', ['section' => 'memberships'])->with('success', 'Membership plan removed.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, ?MembershipPlan $membershipPlan = null): array
    {
        $nameRule = Rule::unique('membership_plans', 'name');
        if ($membershipPlan !== null) {
            $nameRule->ignore($membershipPlan);
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:100', $nameRule],
            'duration_days' => ['required', 'integer', 'between:1,3650'],
            'price' => ['required', 'numeric', 'min:0', 'max:1000000'],
            'is_active' => ['sometimes', 'boolean'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }
}
