<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0c0e0b">
    <title>@yield('title', 'Dashboard') · IRONCORE FITNESS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#0c0e0b] font-sans text-stone-100 antialiased">
    <div class="min-h-screen">
        <aside data-sidebar class="fixed inset-y-0 left-0 z-40 flex w-[260px] -translate-x-full flex-col border-r border-white/[.06] bg-[#11130f] transition-transform duration-300 lg:translate-x-0">
            <a href="{{ route('dashboard') }}" class="flex h-[78px] items-center gap-3 border-b border-white/[.06] px-6">
                <span class="grid size-10 place-items-center rounded-xl bg-[#c6f45b] text-lg font-black text-[#14170f]">I</span>
                <span class="text-[13px] font-black tracking-[.19em]">IRONCORE <span class="font-medium tracking-[.12em] text-stone-500">FITNESS</span></span>
            </a>
            <div class="px-4 pt-7">
                <p class="px-3 text-[10px] font-bold uppercase tracking-[.2em] text-stone-600">Workspace</p>
                <nav aria-label="Main navigation" class="mt-3 space-y-1">
                    @foreach ([
                        'dashboard' => ['Dashboard', '◫'],
                        'members' => ['Members', '◎'],
                        'memberships' => ['Memberships', '◇'],
                        'payments' => ['Payments', '↗'],
                        'attendance' => ['Attendance', '◷'],
                        'reports' => ['Reports', '▥'],
                    ] as $key => [$label, $icon])
                        <a href="{{ route('dashboard', $key === 'dashboard' ? [] : ['section' => $key]) }}" @class([
                            'sidebar-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-[13px] font-medium',
                            'bg-[#c6f45b]/10 text-[#d3fa83]' => $section === $key,
                            'text-stone-500 hover:bg-white/[.04] hover:text-stone-200' => $section !== $key,
                        ])>
                            <span class="grid size-6 place-items-center text-base">{{ $icon }}</span>
                            {{ $label }}
                            @if ($key === 'members' && $expiredMembersCount > 0)
                                <span class="ml-auto rounded-full bg-amber-300/10 px-2 py-0.5 text-[10px] text-amber-200">{{ $expiredMembersCount }}</span>
                            @endif
                        </a>
                    @endforeach
                </nav>
            </div>
            <div class="mt-auto p-4">
                <div class="rounded-2xl border border-[#c6f45b]/10 bg-gradient-to-br from-[#c6f45b]/[.09] to-transparent p-4">
                    <div class="mb-3 flex size-8 items-center justify-center rounded-lg bg-[#c6f45b]/10 text-[#c6f45b]">✳</div>
                    <p class="text-sm font-semibold">Keep the momentum</p>
                    <p class="mt-1 text-xs leading-5 text-stone-500">Your members are counting on you. Make today a strong one.</p>
                </div>
                <div class="mt-4 flex items-center gap-3 border-t border-white/[.06] px-1 pt-4">
                    <span class="grid size-9 place-items-center rounded-full bg-stone-800 text-xs font-bold text-[#c6f45b]">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-xs font-semibold">{{ auth()->user()->name }}</p>
                        <p class="mt-0.5 text-[10px] capitalize text-stone-600">{{ auth()->user()->role }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button aria-label="Sign out" title="Sign out" class="grid size-8 place-items-center rounded-lg text-stone-500 hover:bg-white/[.05] hover:text-white">↗</button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="min-h-screen lg:pl-[260px]">
            <header class="sticky top-0 z-30 flex h-[78px] items-center justify-between border-b border-white/[.06] bg-[#0c0e0b]/90 px-5 backdrop-blur-xl sm:px-8">
                <div class="flex items-center gap-3">
                    <button data-menu-toggle aria-label="Open navigation" class="grid size-9 place-items-center rounded-lg border border-white/10 text-stone-300 lg:hidden">☰</button>
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-[.17em] text-stone-600">{{ now()->format('l, F j, Y') }}</p>
                        <p class="mt-1 text-sm font-semibold tracking-tight">@yield('page-title', 'Dashboard')</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="hidden rounded-full border border-white/[.07] bg-white/[.03] px-3 py-1.5 text-[11px] text-stone-500 sm:inline-flex"><span class="mr-2 size-1.5 self-center rounded-full bg-[#c6f45b]"></span> Gym is open</span>
                    <span class="grid size-9 place-items-center rounded-full border border-white/10 bg-white/[.03] text-xs font-bold text-[#c6f45b]">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                </div>
            </header>

            <main class="mx-auto max-w-[1480px] px-5 py-7 sm:px-8 sm:py-9">
                @if (session('success'))
                    <div role="status" class="mb-6 flex items-center gap-3 rounded-xl border border-emerald-300/15 bg-emerald-300/[.07] px-4 py-3 text-sm text-emerald-200">
                        <span class="grid size-5 place-items-center rounded-full bg-emerald-300/15 text-[11px]">✓</span>{{ session('success') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div role="alert" class="mb-6 rounded-xl border border-rose-300/15 bg-rose-300/[.07] px-4 py-3 text-sm text-rose-200">
                        <p class="font-semibold">{{ $errors->first() }}</p>
                        @if ($errors->count() > 1)
                            <ul class="mt-2 list-inside list-disc space-y-1 text-xs text-rose-200/80">
                                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                        @endif
                    </div>
                @endif
                @yield('content')
            </main>
            <footer class="px-5 pb-7 text-center text-[10px] tracking-wide text-stone-700 sm:px-8">IRONCORE FITNESS · BUILT FOR THE WORK</footer>
        </div>
    </div>
</body>
</html>
