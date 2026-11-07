<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MembershipPlan;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['expires_at'] = Carbon::parse($data['started_at'])->addDays(
            MembershipPlan::findOrFail($data['membership_plan_id'])->duration_days - 1,
        )->toDateString();

        Member::create($data);

        return redirect()->route('dashboard', ['section' => 'members'])->with('success', 'Member added successfully.');
    }

    public function update(Request $request, Member $member): RedirectResponse
    {
        $data = $this->validatedData($request, $member);
        $data['expires_at'] = Carbon::parse($data['started_at'])->addDays(
            MembershipPlan::findOrFail($data['membership_plan_id'])->duration_days - 1,
        )->toDateString();

        $member->update($data);

        return redirect()->route('dashboard', ['section' => 'members'])->with('success', 'Member details updated.');
    }

    public function destroy(Member $member): RedirectResponse
    {
        $member->delete();

        return redirect()->route('dashboard', ['section' => 'members'])->with('success', 'Member removed.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, ?Member $member = null): array
    {
        $emailRule = Rule::unique('members', 'email');
        if ($member !== null) {
            $emailRule->ignore($member);
        }

        $planRule = Rule::exists('membership_plans', 'id')
            ->where(function (Builder $query) use ($member): void {
                $query->where('is_active', true);
                if ($member !== null) {
                    $query->orWhere('id', $member->membership_plan_id);
                }
            });

        return $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', $emailRule],
            'phone' => ['nullable', 'string', 'max:40'],
            'gender' => ['nullable', Rule::in(['Female', 'Male', 'Non-binary', 'Prefer not to say'])],
            'age' => ['nullable', 'integer', 'between:13,100'],
            'address' => ['nullable', 'string', 'max:1000'],
            'membership_plan_id' => ['required', 'integer', $planRule],
            'started_at' => ['required', 'date'],
        ]);
    }
}
