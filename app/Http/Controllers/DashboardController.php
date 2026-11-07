<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Member;
use App\Models\MembershipPlan;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $section = $request->query('section', 'dashboard');
        abort_unless(in_array($section, ['dashboard', 'members', 'memberships', 'payments', 'attendance', 'reports'], true), 404);

        $months = collect(range(5, 0))
            ->map(fn (int $offset) => now()->startOfMonth()->subMonths($offset));
        $monthlyPayments = Payment::query()
            ->where('status', 'paid')
            ->whereNotNull('paid_at')
            ->whereBetween('paid_at', [$months->first()->copy()->startOfMonth(), now()->endOfMonth()])
            ->get(['amount', 'paid_at'])
            ->groupBy(fn (Payment $payment): string => $payment->paid_at->format('Y-m'));
        $revenueChart = $months->map(fn ($month): array => [
            'label' => $month->format('M'),
            'amount' => (float) ($monthlyPayments->get($month->format('Y-m'))?->sum('amount') ?? 0),
        ]);
        $maxRevenue = max(1, (float) $revenueChart->max('amount'));

        $members = Member::query()
            ->with('membershipPlan')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $search = $request->string('q')->toString();
                $query->where(fn ($query) => $query
                    ->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%"));
            })
            ->when($request->query('status') === 'active', fn ($query) => $query->whereDate('expires_at', '>=', today()))
            ->when($request->query('status') === 'expired', fn ($query) => $query->whereDate('expires_at', '<', today()))
            ->orderByDesc('created_at')
            ->paginate(8)
            ->withQueryString();

        $plans = MembershipPlan::query()->withCount([
            'members as active_members_count' => fn ($query) => $query->whereDate('expires_at', '>=', today()),
        ])->orderBy('duration_days')->get();

        $payments = Payment::query()
            ->with(['member', 'membershipPlan'])
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $todayAttendance = Attendance::query()
            ->with('member')
            ->whereDate('checked_in_at', today())
            ->latest('checked_in_at')
            ->get();

        $activeMembers = Member::query()
            ->whereDate('expires_at', '>=', today())
            ->orderBy('full_name')
            ->get(['id', 'full_name']);
        $allMembers = Member::query()->orderBy('full_name')->get(['id', 'full_name']);

        $planStats = $plans->map(fn (MembershipPlan $plan): array => [
            'name' => $plan->name,
            'count' => $plan->active_members_count,
        ]);
        $totalActiveByPlan = max(1, (int) $planStats->sum('count'));

        $recentMembers = Member::query()->with('membershipPlan')->latest()->limit(5)->get();
        $newMembersThisMonth = Member::query()->whereBetween('created_at', [now()->startOfMonth(), now()])->count();
        $monthlyRevenue = Payment::query()
            ->where('status', 'paid')
            ->whereBetween('paid_at', [now()->startOfMonth(), now()])
            ->sum('amount');

        return view('gym.dashboard', [
            'section' => $section,
            'members' => $members,
            'plans' => $plans,
            'payments' => $payments,
            'todayAttendance' => $todayAttendance,
            'activeMembers' => $activeMembers,
            'allMembers' => $allMembers,
            'recentMembers' => $recentMembers,
            'newMembersThisMonth' => $newMembersThisMonth,
            'monthlyRevenue' => $monthlyRevenue,
            'totalMembers' => Member::count(),
            'activeMembersCount' => Member::whereDate('expires_at', '>=', today())->count(),
            'expiredMembersCount' => Member::whereDate('expires_at', '<', today())->count(),
            'todayVisits' => $todayAttendance->count(),
            'revenueChart' => $revenueChart,
            'maxRevenue' => $maxRevenue,
            'planStats' => $planStats,
            'totalActiveByPlan' => $totalActiveByPlan,
            'dailyVisits' => $this->dailyVisits(),
        ]);
    }

    /**
     * @return Collection<int, array{label: string, count: int}>
     */
    private function dailyVisits(): Collection
    {
        $days = collect(range(6, 0))
            ->map(fn (int $offset) => today()->subDays($offset));
        $visitsByDay = Attendance::query()
            ->whereBetween('checked_in_at', [$days->first()->copy()->startOfDay(), now()])
            ->get(['checked_in_at'])
            ->groupBy(fn (Attendance $attendance): string => $attendance->checked_in_at->toDateString());

        return $days->map(fn ($day): array => [
            'label' => $day->format('D'),
            'count' => $visitsByDay->get($day->toDateString(), collect())->count(),
        ]);
    }
}
