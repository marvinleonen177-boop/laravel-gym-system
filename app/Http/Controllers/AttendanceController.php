<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function checkIn(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'member_id' => ['required', 'integer', 'exists:members,id'],
        ]);
        $member = Member::findOrFail($data['member_id']);

        if ($member->expires_at->isBefore(today())) {
            return back()->withErrors(['member_id' => 'This membership has expired. Renew the membership before check-in.']);
        }

        if ($member->attendances()->whereNull('checked_out_at')->exists()) {
            return back()->withErrors(['member_id' => 'This member is already checked in.']);
        }

        Attendance::create([
            'member_id' => $member->id,
            'checked_in_at' => now(),
        ]);

        return redirect()->route('dashboard', ['section' => 'attendance'])->with('success', $member->full_name.' checked in.');
    }

    public function checkOut(Attendance $attendance): RedirectResponse
    {
        if ($attendance->checked_out_at !== null) {
            return back()->withErrors(['attendance' => 'This visit has already been checked out.']);
        }

        $attendance->update(['checked_out_at' => now()]);

        return redirect()->route('dashboard', ['section' => 'attendance'])->with('success', $attendance->member->full_name.' checked out.');
    }
}
