@extends('gym.layout')

@php
    $sectionTitles = [
        'dashboard' => 'Your gym, at a glance',
        'members' => 'Members',
        'memberships' => 'Membership plans',
        'payments' => 'Payments',
        'attendance' => 'Attendance',
        'reports' => 'Reports & analytics',
    ];
@endphp

@section('title', $sectionTitles[$section])
@section('page-title', $sectionTitles[$section])

@section('content')
    @if ($section === 'dashboard')
        <div class="mb-7 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[.18em] text-[#c6f45b]">IRONCORE FITNESS</p>
                <h1 class="mt-2 text-2xl font-semibold tracking-tight sm:text-[30px]">Welcome back, {{ explode(' ', auth()->user()->name)[0] }} <span aria-hidden="true">✳</span></h1>
                <p class="mt-2 text-sm text-stone-500">Here's what's happening at your gym today.</p>
            </div>
            <button data-add-member class="inline-flex items-center gap-2 rounded-xl bg-[#c6f45b] px-4 py-2.5 text-xs font-bold text-[#151810] shadow-[0_8px_24px_rgba(198,244,91,.12)] transition hover:bg-[#d5ff70]">
                <span class="text-base leading-none">+</span> Add a member
            </button>
        </div>
    @else
        <div class="mb-7 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[.18em] text-[#c6f45b]">GYM MANAGEMENT</p>
                <h1 class="mt-2 text-2xl font-semibold tracking-tight sm:text-[30px]">{{ $sectionTitles[$section] }}</h1>
                <p class="mt-2 text-sm text-stone-500">
                    @if ($section === 'members') Keep your member directory up to date.
                    @elseif ($section === 'memberships') Manage plans, pricing and duration.
                    @elseif ($section === 'payments') Track transactions and print receipts.
                    @elseif ($section === 'attendance') Check members in and follow today's visits.
                    @else A clear view of your gym's performance.
                    @endif
                </p>
            </div>
            @if ($section === 'members')<button data-add-member class="rounded-xl bg-[#c6f45b] px-4 py-2.5 text-xs font-bold text-[#151810]">+ Add a member</button>@endif
            @if ($section === 'memberships')<button data-add-plan class="rounded-xl bg-[#c6f45b] px-4 py-2.5 text-xs font-bold text-[#151810]">+ New plan</button>@endif
            @if ($section === 'payments')<button onclick="document.querySelector('#payment-dialog').showModal()" class="rounded-xl bg-[#c6f45b] px-4 py-2.5 text-xs font-bold text-[#151810]">+ Record payment</button>@endif
        </div>
    @endif

    @if (in_array($section, ['dashboard', 'reports'], true))
        <section aria-label="Gym statistics" class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['Total members', number_format($totalMembers), '↗', $newMembersThisMonth.' new this month', 'text-[#c6f45b]', 'bg-[#c6f45b]/[.08]'],
                ['Active memberships', number_format($activeMembersCount), '◉', number_format($expiredMembersCount).' expired', 'text-emerald-300', 'bg-emerald-300/[.08]'],
                ['Monthly revenue', '₱'.number_format((float) $monthlyRevenue), '₱', 'Collected this month', 'text-sky-300', 'bg-sky-300/[.08]'],
                ["Today's visits", number_format($todayVisits), '◷', 'Member check-ins today', 'text-violet-300', 'bg-violet-300/[.08]'],
            ] as [$label, $value, $icon, $caption, $tone, $surface])
                <article class="rounded-2xl border border-white/[.07] bg-[#141612] p-4 transition hover:border-white/[.12] sm:p-5">
                    <div class="flex items-start justify-between">
                        <p class="text-xs font-medium text-stone-500">{{ $label }}</p>
                        <span class="grid size-8 place-items-center rounded-lg {{ $surface }} {{ $tone }} text-sm font-bold">{{ $icon }}</span>
                    </div>
                    <p class="mt-5 text-[27px] font-semibold tracking-[-.04em]">{{ $value }}</p>
                    <p class="mt-1.5 text-[11px] text-stone-600">{{ $caption }}</p>
                </article>
            @endforeach
        </section>
    @endif

    @if ($section === 'dashboard')
        <section class="mt-5 grid gap-5 xl:grid-cols-[1.55fr_1fr]">
            <article class="rounded-2xl border border-white/[.07] bg-[#141612] p-5 sm:p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold">Revenue overview</p>
                        <p class="mt-1 text-xs text-stone-600">Paid transactions · last 6 months</p>
                    </div>
                    <span class="rounded-lg border border-white/[.07] px-2.5 py-1.5 text-[10px] text-stone-500">This year</span>
                </div>
                <div class="mt-6 flex h-48 items-end gap-3 border-b border-l border-white/[.06] px-2 sm:gap-5">
                    @foreach ($revenueChart as $bar)
                        <div class="flex h-full flex-1 flex-col items-center justify-end gap-2">
                            <span class="text-[9px] text-stone-600">{{ $bar['amount'] > 0 ? '₱'.number_format($bar['amount'] / 1000, 1).'k' : '' }}</span>
                            <div class="bar-grow w-full max-w-12 rounded-t-md bg-gradient-to-t from-[#9cca3f] to-[#d7ff7c]" style="height: {{ max(4, ($bar['amount'] / $maxRevenue) * 100) }}%"></div>
                            <span class="absolute translate-y-6 text-[10px] text-stone-600">{{ $bar['label'] }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="mt-8 flex items-center gap-2 text-[11px] text-stone-600"><span class="size-2 rounded-full bg-[#c6f45b]"></span> Total revenue in {{ now()->format('Y') }}</div>
            </article>

            <article class="rounded-2xl border border-white/[.07] bg-[#141612] p-5 sm:p-6">
                <div class="flex items-start justify-between">
                    <div><p class="text-sm font-semibold">Membership overview</p><p class="mt-1 text-xs text-stone-600">Active members by plan</p></div>
                    <a href="{{ route('dashboard', ['section' => 'memberships']) }}" class="text-[11px] font-medium text-[#c6f45b] hover:text-[#e3ffae]">Manage plans →</a>
                </div>
                <div class="mt-6 space-y-5">
                    @forelse ($planStats as $stat)
                        <div>
                            <div class="mb-2 flex items-center justify-between text-xs">
                                <span class="text-stone-400">{{ $stat['name'] }}</span><span class="font-semibold text-stone-200">{{ $stat['count'] }}</span>
                            </div>
                            <div class="h-1.5 overflow-hidden rounded-full bg-white/[.06]"><div class="h-full rounded-full bg-[#c6f45b]" style="width: {{ min(100, ($stat['count'] / $totalActiveByPlan) * 100) }}%"></div></div>
                        </div>
                    @empty
                        <p class="rounded-xl border border-dashed border-white/10 px-4 py-8 text-center text-xs text-stone-600">Add a membership plan to see your breakdown.</p>
                    @endforelse
                </div>
                <div class="mt-6 flex items-center justify-between border-t border-white/[.06] pt-4 text-xs"><span class="text-stone-500">Active memberships</span><strong class="text-[#c6f45b]">{{ number_format($activeMembersCount) }} <span class="font-normal text-stone-600">members</span></strong></div>
            </article>
        </section>

        <section class="mt-5 grid gap-5 xl:grid-cols-[1.15fr_.85fr]">
            <article class="rounded-2xl border border-white/[.07] bg-[#141612] p-5 sm:p-6">
                <div class="flex items-start justify-between">
                    <div><p class="text-sm font-semibold">Recent members</p><p class="mt-1 text-xs text-stone-600">Your newest sign-ups</p></div>
                    <a href="{{ route('dashboard', ['section' => 'members']) }}" class="text-[11px] font-medium text-[#c6f45b] hover:text-[#e3ffae]">View all →</a>
                </div>
                <div class="mt-5 divide-y divide-white/[.05]">
                    @forelse ($recentMembers as $member)
                        <div class="flex items-center gap-3 py-3 first:pt-0 last:pb-0">
                            <span class="grid size-9 shrink-0 place-items-center rounded-full bg-gradient-to-br from-stone-700 to-stone-800 text-[11px] font-bold text-[#d9f99d]">{{ strtoupper(substr($member->full_name, 0, 1)) }}</span>
                            <div class="min-w-0 flex-1"><p class="truncate text-xs font-semibold">{{ $member->full_name }}</p><p class="mt-1 truncate text-[10px] text-stone-600">{{ $member->membershipPlan->name }} · {{ $member->created_at->diffForHumans() }}</p></div>
                            <span @class(['rounded-full px-2 py-1 text-[9px] font-semibold capitalize', 'bg-emerald-300/10 text-emerald-200' => $member->status === 'active', 'bg-rose-300/10 text-rose-200' => $member->status === 'expired'])>{{ $member->status }}</span>
                        </div>
                    @empty
                        <p class="py-8 text-center text-xs text-stone-600">Your first member is waiting to join.</p>
                    @endforelse
                </div>
            </article>
            <article class="rounded-2xl border border-white/[.07] bg-[#141612] p-5 sm:p-6">
                <div class="flex items-start justify-between"><div><p class="text-sm font-semibold">Today's activity</p><p class="mt-1 text-xs text-stone-600">Member check-ins</p></div><a href="{{ route('dashboard', ['section' => 'attendance']) }}" class="text-[11px] font-medium text-[#c6f45b]">See attendance →</a></div>
                <div class="mt-6 flex h-28 items-end gap-2">
                    @foreach ($dailyVisits as $day)
                        <div class="flex h-full flex-1 flex-col items-center justify-end gap-2">
                            <span class="text-[9px] text-stone-600">{{ $day['count'] ?: '' }}</span>
                            <div class="bar-grow w-full max-w-8 rounded-t bg-gradient-to-t from-violet-500/40 to-violet-300" style="height: {{ max(5, ($day['count'] / max(1, $dailyVisits->max('count'))) * 100) }}%"></div>
                            <span class="text-[9px] text-stone-600">{{ $day['label'] }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="mt-5 rounded-xl border border-white/[.05] bg-white/[.02] px-4 py-3">
                    <div class="flex items-center justify-between"><span class="text-xs text-stone-500">Visitors today</span><span class="text-sm font-semibold">{{ $todayVisits }}</span></div>
                </div>
            </article>
        </section>

        <section class="mt-5 rounded-2xl border border-white/[.07] bg-[#141612] p-5 sm:p-6">
            <div class="mb-5 flex items-center justify-between"><div><p class="text-sm font-semibold">Recent payments</p><p class="mt-1 text-xs text-stone-600">Latest transactions</p></div><a href="{{ route('dashboard', ['section' => 'payments']) }}" class="text-[11px] font-medium text-[#c6f45b]">All payments →</a></div>
            @include('gym.partials.payment-table')
        </section>
    @elseif ($section === 'members')
        <section class="rounded-2xl border border-white/[.07] bg-[#141612]">
            <form method="GET" action="{{ route('dashboard') }}" class="flex flex-col gap-3 border-b border-white/[.06] p-4 sm:flex-row sm:items-center">
                <input type="hidden" name="section" value="members">
                <label class="relative flex-1"><span class="sr-only">Search members</span><span class="pointer-events-none absolute left-3 top-2.5 text-stone-600">⌕</span><input name="q" value="{{ request('q') }}" placeholder="Search name, email or phone..." class="w-full rounded-lg border border-white/[.08] bg-[#0d0f0c] py-2.5 pl-9 pr-3 text-xs outline-none placeholder:text-stone-700 focus:border-[#c6f45b]/40"></label>
                <select name="status" class="rounded-lg border border-white/[.08] bg-[#0d0f0c] px-3 py-2.5 text-xs text-stone-400 outline-none focus:border-[#c6f45b]/40"><option value="">All statuses</option><option value="active" @selected(request('status') === 'active')>Active</option><option value="expired" @selected(request('status') === 'expired')>Expired</option></select>
                <button class="rounded-lg border border-white/[.08] px-4 py-2.5 text-xs font-semibold text-stone-300 hover:bg-white/[.04]">Filter</button>
            </form>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-xs">
                    <thead class="bg-white/[.02] text-[10px] uppercase tracking-[.12em] text-stone-600"><tr><th class="px-5 py-3 font-semibold">Member</th><th class="px-4 py-3 font-semibold">Plan</th><th class="px-4 py-3 font-semibold">Start date</th><th class="px-4 py-3 font-semibold">Expires</th><th class="px-4 py-3 font-semibold">Status</th><th class="px-5 py-3 text-right font-semibold">Actions</th></tr></thead>
                    <tbody class="divide-y divide-white/[.04]">
                        @forelse ($members as $member)
                            <tr class="transition hover:bg-white/[.02]">
                                <td class="px-5 py-3.5"><div class="flex items-center gap-3"><span class="grid size-8 shrink-0 place-items-center rounded-full bg-stone-800 text-[10px] font-bold text-[#d9f99d]">{{ strtoupper(substr($member->full_name, 0, 1)) }}</span><div><p class="font-semibold text-stone-200">{{ $member->full_name }}</p><p class="mt-1 text-[10px] text-stone-600">{{ $member->email }}</p></div></div></td>
                                <td class="px-4 py-3.5 text-stone-400">{{ $member->membershipPlan->name }}</td>
                                <td class="px-4 py-3.5 text-stone-500">{{ $member->started_at->format('M d, Y') }}</td>
                                <td class="px-4 py-3.5 text-stone-500">{{ $member->expires_at->format('M d, Y') }}</td>
                                <td class="px-4 py-3.5"><span @class(['rounded-full px-2 py-1 text-[9px] font-semibold capitalize', 'bg-emerald-300/10 text-emerald-200' => $member->status === 'active', 'bg-rose-300/10 text-rose-200' => $member->status === 'expired'])>{{ $member->status }}</span></td>
                                <td class="px-5 py-3.5"><div class="flex justify-end gap-2">
                                    <button data-edit-member data-id="{{ $member->id }}" data-full_name="{{ $member->full_name }}" data-email="{{ $member->email }}" data-phone="{{ $member->phone }}" data-gender="{{ $member->gender }}" data-age="{{ $member->age }}" data-address="{{ $member->address }}" data-membership_plan_id="{{ $member->membership_plan_id }}" data-started_at="{{ $member->started_at->toDateString() }}" class="rounded-md border border-white/[.08] px-2.5 py-1.5 text-[10px] text-stone-400 hover:border-white/20 hover:text-white">Edit</button>
                                    <form method="POST" action="{{ route('members.destroy', $member) }}" data-confirm="Remove {{ $member->full_name }} and their related records?">
                                        @csrf @method('DELETE')
                                        <button class="rounded-md border border-rose-300/10 px-2.5 py-1.5 text-[10px] text-rose-300/70 hover:border-rose-300/30 hover:text-rose-200">Remove</button>
                                    </form>
                                </div></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-16 text-center"><span class="mx-auto grid size-10 place-items-center rounded-full bg-white/[.04] text-lg text-stone-500">◎</span><p class="mt-3 text-sm font-medium text-stone-300">No members found</p><p class="mt-1 text-xs text-stone-600">Adjust your search or add your first member.</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($members->hasPages())<div class="border-t border-white/[.06] px-5 py-3 text-xs text-stone-500">{{ $members->links() }}</div>@endif
        </section>
    @elseif ($section === 'memberships')
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @forelse ($plans as $plan)
                <article class="rounded-2xl border border-white/[.07] bg-[#141612] p-5">
                    <div class="flex items-start justify-between gap-3">
                        <span class="grid size-10 place-items-center rounded-xl bg-[#c6f45b]/[.09] text-lg text-[#c6f45b]">◇</span>
                        <div class="flex gap-2">
                            <button data-edit-plan data-id="{{ $plan->id }}" data-name="{{ $plan->name }}" data-duration_days="{{ $plan->duration_days }}" data-price="{{ $plan->price }}" data-active="{{ $plan->is_active ? 1 : 0 }}" class="rounded-md border border-white/[.08] px-2.5 py-1.5 text-[10px] text-stone-400 hover:text-white">Edit</button>
                            <form method="POST" action="{{ route('plans.destroy', $plan) }}" data-confirm="Delete the {{ $plan->name }} plan?">
                                @csrf @method('DELETE')
                                <button class="rounded-md border border-rose-300/10 px-2.5 py-1.5 text-[10px] text-rose-300/70 hover:text-rose-200">Delete</button>
                            </form>
                        </div>
                    </div>
                    <p class="mt-5 text-base font-semibold">{{ $plan->name }}</p>
                    <p class="mt-1 text-xs text-stone-600">{{ number_format($plan->duration_days) }} {{ $plan->duration_days === 1 ? 'day' : 'days' }}</p>
                    <div class="mt-5 flex items-end justify-between border-t border-white/[.06] pt-4">
                        <p class="text-2xl font-semibold tracking-tight">₱{{ number_format((float) $plan->price) }}<span class="ml-1 text-xs font-normal text-stone-600">/ plan</span></p>
                        <span class="text-xs text-stone-500">{{ $plan->active_members_count }} active</span>
                    </div>
                    <span @class(['mt-4 inline-flex rounded-full px-2.5 py-1 text-[9px] font-semibold', 'bg-emerald-300/10 text-emerald-200' => $plan->is_active, 'bg-stone-700/40 text-stone-500' => ! $plan->is_active])>{{ $plan->is_active ? 'Available' : 'Hidden' }}</span>
                </article>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-white/10 p-12 text-center"><p class="text-sm font-semibold">No membership plans yet</p><p class="mt-1 text-xs text-stone-600">Create your first plan to start assigning memberships.</p><button data-add-plan class="mt-4 rounded-lg bg-[#c6f45b] px-4 py-2 text-xs font-bold text-[#151810]">Create a plan</button></div>
            @endforelse
        </section>
    @elseif ($section === 'payments')
        <section class="rounded-2xl border border-white/[.07] bg-[#141612] p-5 sm:p-6">
            <div class="mb-5"><p class="text-sm font-semibold">Latest transactions</p><p class="mt-1 text-xs text-stone-600">Recently recorded member payments.</p></div>
            @include('gym.partials.payment-table')
        </section>
    @elseif ($section === 'attendance')
        <section class="grid gap-5 xl:grid-cols-[.8fr_1.2fr]">
            <article class="h-fit rounded-2xl border border-white/[.07] bg-[#141612] p-5 sm:p-6">
                <span class="grid size-10 place-items-center rounded-xl bg-violet-300/10 text-lg text-violet-200">◷</span>
                <h2 class="mt-4 text-base font-semibold">Quick check-in</h2>
                <p class="mt-1 text-xs leading-5 text-stone-600">Only members with an active membership can check in.</p>
                <form method="POST" action="{{ route('attendance.check-in') }}" class="mt-5 space-y-3">
                    @csrf
                    <label class="block text-xs font-medium text-stone-400">Select member
                        <select name="member_id" required class="mt-2 w-full rounded-lg border border-white/[.08] bg-[#0d0f0c] px-3 py-2.5 text-xs text-stone-300 outline-none focus:border-[#c6f45b]/40"><option value="">Choose an active member</option>@foreach ($activeMembers as $activeMember)<option value="{{ $activeMember->id }}" @selected(old('member_id') == $activeMember->id)>{{ $activeMember->full_name }}</option>@endforeach</select>
                    </label>
                    <button class="w-full rounded-lg bg-[#c6f45b] px-4 py-2.5 text-xs font-bold text-[#151810] hover:bg-[#d5ff70]">Check in member</button>
                </form>
                <div class="mt-5 rounded-xl border border-white/[.05] bg-white/[.02] p-4"><p class="text-[10px] uppercase tracking-[.14em] text-stone-600">Today's visitors</p><p class="mt-2 text-2xl font-semibold">{{ $todayVisits }}<span class="ml-1 text-xs font-normal text-stone-600">check-ins</span></p></div>
            </article>
            <article class="rounded-2xl border border-white/[.07] bg-[#141612]">
                <div class="flex items-center justify-between border-b border-white/[.06] px-5 py-4"><div><p class="text-sm font-semibold">Today's attendance</p><p class="mt-1 text-xs text-stone-600">{{ now()->format('F j, Y') }}</p></div><span class="rounded-full bg-violet-300/10 px-2.5 py-1 text-[10px] font-semibold text-violet-200">{{ $todayVisits }} visitors</span></div>
                <div class="divide-y divide-white/[.04]">
                    @forelse ($todayAttendance as $attendance)
                        <div class="flex items-center gap-3 px-5 py-3.5">
                            <span class="grid size-9 shrink-0 place-items-center rounded-full bg-violet-300/10 text-[10px] font-bold text-violet-200">{{ strtoupper(substr($attendance->member->full_name, 0, 1)) }}</span>
                            <div class="min-w-0 flex-1"><p class="truncate text-xs font-semibold">{{ $attendance->member->full_name }}</p><p class="mt-1 text-[10px] text-stone-600">In {{ $attendance->checked_in_at->format('h:i A') }}@if ($attendance->checked_out_at) · Out {{ $attendance->checked_out_at->format('h:i A') }}@endif</p></div>
                            @if (! $attendance->checked_out_at)
                                <form method="POST" action="{{ route('attendance.check-out', $attendance) }}">@csrf @method('PATCH')<button class="rounded-lg border border-white/[.08] px-3 py-2 text-[10px] font-semibold text-stone-300 hover:border-[#c6f45b]/30 hover:text-[#c6f45b]">Check out</button></form>
                            @else
                                <span class="rounded-full bg-stone-700/40 px-2.5 py-1 text-[9px] text-stone-500">Completed</span>
                            @endif
                        </div>
                    @empty
                        <div class="px-5 py-14 text-center"><p class="text-sm font-medium text-stone-300">No visits yet today</p><p class="mt-1 text-xs text-stone-600">Check in a member to start the attendance log.</p></div>
                    @endforelse
                </div>
            </article>
        </section>
    @elseif ($section === 'reports')
        <section class="grid gap-5 xl:grid-cols-2">
            <article class="rounded-2xl border border-white/[.07] bg-[#141612] p-5 sm:p-6">
                <p class="text-sm font-semibold">Revenue by month</p><p class="mt-1 text-xs text-stone-600">Paid transactions over the last six months</p>
                <div class="mt-8 flex h-56 items-end gap-4 border-b border-l border-white/[.06] px-3">
                    @foreach ($revenueChart as $bar)<div class="flex h-full flex-1 flex-col items-center justify-end gap-2"><span class="text-[9px] text-stone-600">{{ $bar['amount'] > 0 ? '₱'.number_format($bar['amount'] / 1000, 1).'k' : '—' }}</span><div class="bar-grow w-full max-w-14 rounded-t-md bg-gradient-to-t from-[#9cca3f] to-[#d7ff7c]" style="height: {{ max(4, ($bar['amount'] / $maxRevenue) * 100) }}%"></div><span class="absolute translate-y-6 text-[10px] text-stone-600">{{ $bar['label'] }}</span></div>@endforeach
                </div>
            </article>
            <article class="rounded-2xl border border-white/[.07] bg-[#141612] p-5 sm:p-6">
                <p class="text-sm font-semibold">Plan popularity</p><p class="mt-1 text-xs text-stone-600">Current active members by plan</p>
                <div class="mt-7 space-y-5">@forelse ($planStats as $stat)<div><div class="mb-2 flex justify-between text-xs"><span class="text-stone-400">{{ $stat['name'] }}</span><span>{{ $stat['count'] }}</span></div><div class="h-2 overflow-hidden rounded-full bg-white/[.06]"><div class="h-full rounded-full bg-[#c6f45b]" style="width: {{ min(100, ($stat['count'] / $totalActiveByPlan) * 100) }}%"></div></div></div>@empty<p class="text-xs text-stone-600">No membership plans yet.</p>@endforelse</div>
            </article>
            <article class="rounded-2xl border border-white/[.07] bg-[#141612] p-5 sm:p-6 xl:col-span-2">
                <p class="text-sm font-semibold">Business snapshot</p><p class="mt-1 text-xs text-stone-600">Current membership and revenue summary</p>
                <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-xl bg-white/[.03] p-4"><p class="text-[10px] text-stone-600">All members</p><p class="mt-2 text-xl font-semibold">{{ number_format($totalMembers) }}</p></div>
                    <div class="rounded-xl bg-white/[.03] p-4"><p class="text-[10px] text-stone-600">Active memberships</p><p class="mt-2 text-xl font-semibold text-emerald-200">{{ number_format($activeMembersCount) }}</p></div>
                    <div class="rounded-xl bg-white/[.03] p-4"><p class="text-[10px] text-stone-600">Expired memberships</p><p class="mt-2 text-xl font-semibold text-rose-200">{{ number_format($expiredMembersCount) }}</p></div>
                    <div class="rounded-xl bg-white/[.03] p-4"><p class="text-[10px] text-stone-600">Income this month</p><p class="mt-2 text-xl font-semibold text-[#c6f45b]">₱{{ number_format((float) $monthlyRevenue) }}</p></div>
                </div>
            </article>
        </section>
    @endif

    @if (in_array($section, ['dashboard', 'members'], true))
        <dialog id="member-dialog" class="w-[min(94vw,620px)] rounded-2xl border border-white/10 bg-[#151813] p-0 shadow-2xl">
            <form method="POST" action="{{ route('members.store') }}" data-member-form>
                @csrf
                <div class="flex items-center justify-between border-b border-white/[.07] px-5 py-4"><div><p data-dialog-title class="text-sm font-semibold">Add a member</p><p class="mt-1 text-[10px] text-stone-600">Membership expiry is calculated from the selected plan.</p></div><button type="button" data-close-modal aria-label="Close dialog" class="grid size-8 place-items-center rounded-lg text-stone-500 hover:bg-white/5 hover:text-white">×</button></div>
                <div class="grid gap-4 p-5 sm:grid-cols-2">
                    <label class="text-xs text-stone-400 sm:col-span-2">Full name<input name="full_name" value="{{ old('full_name') }}" required maxlength="255" class="mt-2 w-full rounded-lg border border-white/[.08] bg-[#0d0f0c] px-3 py-2.5 text-xs text-white outline-none focus:border-[#c6f45b]/40"></label>
                    <label class="text-xs text-stone-400">Email<input type="email" name="email" value="{{ old('email') }}" required class="mt-2 w-full rounded-lg border border-white/[.08] bg-[#0d0f0c] px-3 py-2.5 text-xs text-white outline-none focus:border-[#c6f45b]/40"></label>
                    <label class="text-xs text-stone-400">Phone<input name="phone" value="{{ old('phone') }}" class="mt-2 w-full rounded-lg border border-white/[.08] bg-[#0d0f0c] px-3 py-2.5 text-xs text-white outline-none focus:border-[#c6f45b]/40"></label>
                    <label class="text-xs text-stone-400">Gender<select name="gender" class="mt-2 w-full rounded-lg border border-white/[.08] bg-[#0d0f0c] px-3 py-2.5 text-xs text-stone-300 outline-none focus:border-[#c6f45b]/40"><option value="">Choose one</option>@foreach (['Female', 'Male', 'Non-binary', 'Prefer not to say'] as $gender)<option value="{{ $gender }}" @selected(old('gender') === $gender)>{{ $gender }}</option>@endforeach</select></label>
                    <label class="text-xs text-stone-400">Age<input type="number" min="13" max="100" name="age" value="{{ old('age') }}" class="mt-2 w-full rounded-lg border border-white/[.08] bg-[#0d0f0c] px-3 py-2.5 text-xs text-white outline-none focus:border-[#c6f45b]/40"></label>
                    <label class="text-xs text-stone-400">Membership plan<select name="membership_plan_id" required class="mt-2 w-full rounded-lg border border-white/[.08] bg-[#0d0f0c] px-3 py-2.5 text-xs text-stone-300 outline-none focus:border-[#c6f45b]/40"><option value="">Select a plan</option>@foreach ($plans as $plan)<option value="{{ $plan->id }}" data-active="{{ $plan->is_active ? 1 : 0 }}" @disabled(! $plan->is_active) @selected(old('membership_plan_id') == $plan->id)>{{ $plan->name }} · ₱{{ number_format((float) $plan->price) }}{{ $plan->is_active ? '' : ' · inactive' }}</option>@endforeach</select></label>
                    <label class="text-xs text-stone-400">Start date<input type="date" name="started_at" value="{{ old('started_at', today()->toDateString()) }}" required class="mt-2 w-full rounded-lg border border-white/[.08] bg-[#0d0f0c] px-3 py-2.5 text-xs text-white outline-none focus:border-[#c6f45b]/40"></label>
                    <label class="text-xs text-stone-400 sm:col-span-2">Address<textarea name="address" rows="2" class="mt-2 w-full resize-none rounded-lg border border-white/[.08] bg-[#0d0f0c] px-3 py-2.5 text-xs text-white outline-none focus:border-[#c6f45b]/40">{{ old('address') }}</textarea></label>
                </div>
                <div class="flex justify-end gap-2 border-t border-white/[.07] px-5 py-4"><button type="button" data-close-modal class="rounded-lg px-4 py-2 text-xs text-stone-400 hover:bg-white/[.04]">Cancel</button><button class="rounded-lg bg-[#c6f45b] px-4 py-2 text-xs font-bold text-[#151810]">Save member</button></div>
            </form>
        </dialog>
    @endif

    @if ($section === 'memberships')
        <dialog id="plan-dialog" class="w-[min(94vw,480px)] rounded-2xl border border-white/10 bg-[#151813] p-0 shadow-2xl">
            <form method="POST" action="{{ route('plans.store') }}" data-plan-form>
                @csrf
                <div class="flex items-center justify-between border-b border-white/[.07] px-5 py-4"><p data-dialog-title class="text-sm font-semibold">Create a plan</p><button type="button" data-close-modal aria-label="Close dialog" class="grid size-8 place-items-center rounded-lg text-stone-500 hover:bg-white/5">×</button></div>
                <div class="space-y-4 p-5">
                    <label class="block text-xs text-stone-400">Plan name<input name="name" value="{{ old('name') }}" required class="mt-2 w-full rounded-lg border border-white/[.08] bg-[#0d0f0c] px-3 py-2.5 text-xs text-white outline-none focus:border-[#c6f45b]/40"></label>
                    <div class="grid grid-cols-2 gap-3"><label class="text-xs text-stone-400">Duration (days)<input type="number" name="duration_days" min="1" max="3650" value="{{ old('duration_days') }}" required class="mt-2 w-full rounded-lg border border-white/[.08] bg-[#0d0f0c] px-3 py-2.5 text-xs text-white outline-none focus:border-[#c6f45b]/40"></label><label class="text-xs text-stone-400">Price (₱)<input type="number" name="price" min="0" step="0.01" value="{{ old('price') }}" required class="mt-2 w-full rounded-lg border border-white/[.08] bg-[#0d0f0c] px-3 py-2.5 text-xs text-white outline-none focus:border-[#c6f45b]/40"></label></div>
                    <label class="flex items-center gap-2 text-xs text-stone-400"><input type="checkbox" name="is_active" value="1" checked class="size-4 accent-[#c6f45b]"> Available for new members</label>
                </div>
                <div class="flex justify-end gap-2 border-t border-white/[.07] px-5 py-4"><button type="button" data-close-modal class="rounded-lg px-4 py-2 text-xs text-stone-400">Cancel</button><button class="rounded-lg bg-[#c6f45b] px-4 py-2 text-xs font-bold text-[#151810]">Save plan</button></div>
            </form>
        </dialog>
    @endif

    @if ($section === 'payments')
        <dialog id="payment-dialog" class="w-[min(94vw,480px)] rounded-2xl border border-white/10 bg-[#151813] p-0 shadow-2xl">
            <form method="POST" action="{{ route('payments.store') }}">
                @csrf
                <div class="flex items-center justify-between border-b border-white/[.07] px-5 py-4"><div><p class="text-sm font-semibold">Record a payment</p><p class="mt-1 text-[10px] text-stone-600">A receipt is available after saving.</p></div><button type="button" data-close-modal aria-label="Close dialog" class="grid size-8 place-items-center rounded-lg text-stone-500 hover:bg-white/5">×</button></div>
                <div class="space-y-4 p-5">
                    <label class="block text-xs text-stone-400">Member<select name="member_id" required class="mt-2 w-full rounded-lg border border-white/[.08] bg-[#0d0f0c] px-3 py-2.5 text-xs text-stone-300 outline-none focus:border-[#c6f45b]/40"><option value="">Choose a member</option>@foreach ($allMembers as $member)<option value="{{ $member->id }}">{{ $member->full_name }}</option>@endforeach</select></label>
                    <label class="block text-xs text-stone-400">Amount (₱)<input type="number" name="amount" min="0.01" step="0.01" required class="mt-2 w-full rounded-lg border border-white/[.08] bg-[#0d0f0c] px-3 py-2.5 text-xs text-white outline-none focus:border-[#c6f45b]/40"></label>
                    <div class="grid grid-cols-2 gap-3"><label class="text-xs text-stone-400">Method<select name="payment_method" required class="mt-2 w-full rounded-lg border border-white/[.08] bg-[#0d0f0c] px-3 py-2.5 text-xs text-stone-300 outline-none"><option>Cash</option><option>GCash</option><option>Bank Transfer</option></select></label><label class="text-xs text-stone-400">Status<select name="status" required class="mt-2 w-full rounded-lg border border-white/[.08] bg-[#0d0f0c] px-3 py-2.5 text-xs text-stone-300 outline-none"><option value="paid">Paid</option><option value="pending">Pending</option><option value="overdue">Overdue</option></select></label></div>
                </div>
                <div class="flex justify-end gap-2 border-t border-white/[.07] px-5 py-4"><button type="button" data-close-modal class="rounded-lg px-4 py-2 text-xs text-stone-400">Cancel</button><button class="rounded-lg bg-[#c6f45b] px-4 py-2 text-xs font-bold text-[#151810]">Save payment</button></div>
            </form>
        </dialog>
    @endif
@endsection
