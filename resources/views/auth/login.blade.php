<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in · IRONCORE FITNESS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen text-stone-100 antialiased">
    <main class="grid min-h-screen lg:grid-cols-[1.1fr_.9fr]">
        <section class="relative hidden overflow-hidden border-r border-white/5 bg-[#11140e] p-12 lg:flex lg:flex-col lg:justify-between">
            <div class="absolute -right-32 top-20 size-[34rem] rounded-full bg-lime-300/10 blur-[120px]"></div>
            <a href="{{ route('login') }}" class="relative flex items-center gap-3 text-sm font-black tracking-[.22em]">
                <span class="grid size-10 place-items-center rounded-xl bg-[#c6f45b] text-lg text-[#11140e]">I</span>
                IRONCORE <span class="font-medium tracking-[.16em] text-stone-500">FITNESS</span>
            </a>
            <div class="relative max-w-xl">
                <p class="mb-5 text-xs font-bold uppercase tracking-[.25em] text-[#c6f45b]">Train with purpose</p>
                <h1 class="text-6xl font-semibold leading-[1.02] tracking-[-.05em] xl:text-7xl">Stronger<br>starts <span class="text-[#c6f45b]">here.</span></h1>
                <p class="mt-6 max-w-sm text-base leading-7 text-stone-400">One focused space to keep your members, memberships and momentum moving.</p>
            </div>
            <p class="relative text-xs text-stone-600">IRONCORE FITNESS · MANAGEMENT PORTAL</p>
        </section>

        <section class="grid place-items-center px-6 py-12">
            <div class="w-full max-w-sm">
                <div class="mb-10 flex items-center gap-3 text-xs font-black tracking-[.2em] lg:hidden">
                    <span class="grid size-9 place-items-center rounded-xl bg-[#c6f45b] text-base text-[#11140e]">I</span> IRONCORE FITNESS
                </div>
                <p class="text-xs font-bold uppercase tracking-[.2em] text-[#c6f45b]">Admin workspace</p>
                <h2 class="mt-3 text-3xl font-semibold tracking-tight">Welcome back</h2>
                <p class="mt-2 text-sm text-stone-500">Sign in to manage your gym.</p>

                @if ($errors->any())
                    <div class="mt-6 rounded-xl border border-rose-400/20 bg-rose-400/10 px-4 py-3 text-sm text-rose-200" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5">
                    @csrf
                    <label class="block text-sm font-medium text-stone-300">
                        Email address
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="mt-2 w-full rounded-xl border border-white/10 bg-white/[.04] px-4 py-3 text-sm text-white outline-none transition focus:border-[#c6f45b]/60 focus:ring-2 focus:ring-[#c6f45b]/10">
                    </label>
                    <label class="block text-sm font-medium text-stone-300">
                        Password
                        <input type="password" name="password" required autocomplete="current-password" class="mt-2 w-full rounded-xl border border-white/10 bg-white/[.04] px-4 py-3 text-sm text-white outline-none transition focus:border-[#c6f45b]/60 focus:ring-2 focus:ring-[#c6f45b]/10">
                    </label>
                    <label class="flex items-center gap-2 text-sm text-stone-400">
                        <input type="checkbox" name="remember" value="1" class="size-4 rounded border-white/20 bg-white/5 accent-[#c6f45b]"> Keep me signed in
                    </label>
                    <button class="w-full rounded-xl bg-[#c6f45b] px-4 py-3.5 text-sm font-bold text-[#151810] transition hover:bg-[#d5ff70] focus:outline-none focus:ring-2 focus:ring-[#c6f45b]/60 focus:ring-offset-2 focus:ring-offset-[#0c0e0b]">
                        Sign in to dashboard <span aria-hidden="true">→</span>
                    </button>
                </form>

                <p class="mt-8 text-center text-xs leading-5 text-stone-600">Access is limited to authorized gym administrators.<br>Seed a local demo account with <code class="text-stone-400">php artisan db:seed</code>.</p>
            </div>
        </section>
    </main>
</body>
</html>
