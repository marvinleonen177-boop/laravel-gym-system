<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'member_id' => ['required', 'integer', 'exists:members,id'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:1000000'],
            'payment_method' => ['required', Rule::in(['Cash', 'GCash', 'Bank Transfer'])],
            'status' => ['required', Rule::in(['paid', 'pending', 'overdue'])],
        ]);
        $member = Member::findOrFail($data['member_id']);

        Payment::create([
            ...$data,
            'membership_plan_id' => $member->membership_plan_id,
            'paid_at' => $data['status'] === 'paid' ? now() : null,
        ]);

        return redirect()->route('dashboard', ['section' => 'payments'])->with('success', 'Payment recorded.');
    }

    public function receipt(Payment $payment): View
    {
        return view('gym.receipt', [
            'payment' => $payment->load(['member', 'membershipPlan']),
        ]);
    }
}
