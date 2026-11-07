<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt {{ str_pad((string) $payment->id, 6, '0', STR_PAD_LEFT) }} · IRONCORE FITNESS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#0c0e0b] px-5 py-10 text-stone-100 antialiased">
    <main class="mx-auto max-w-xl rounded-3xl border border-white/10 bg-[#151813] p-8 shadow-2xl sm:p-12">
        <div class="no-print mb-8 flex justify-between">
            <a href="{{ route('dashboard', ['section' => 'payments']) }}" class="text-sm text-stone-400 hover:text-white">← Payments</a>
            <button onclick="window.print()" class="rounded-lg bg-[#c6f45b] px-4 py-2 text-sm font-bold text-[#13160f]">Print receipt</button>
        </div>
        <div class="flex items-start justify-between border-b border-dashed border-white/15 pb-8">
            <div>
                <p class="text-xs font-black tracking-[.22em] text-[#c6f45b]">IRONCORE FITNESS</p>
                <h1 class="mt-6 text-2xl font-semibold">Payment receipt</h1>
                <p class="mt-1 text-sm text-stone-500">Thank you for showing up.</p>
            </div>
            <span class="rounded-full border border-emerald-300/20 bg-emerald-300/10 px-3 py-1 text-xs font-semibold capitalize text-emerald-200">{{ $payment->status }}</span>
        </div>
        <dl class="grid grid-cols-2 gap-x-4 gap-y-6 border-b border-dashed border-white/15 py-8 text-sm">
            <div><dt class="text-stone-500">Receipt no.</dt><dd class="mt-1 font-medium">IC-{{ str_pad((string) $payment->id, 6, '0', STR_PAD_LEFT) }}</dd></div>
            <div><dt class="text-stone-500">Date</dt><dd class="mt-1 font-medium">{{ ($payment->paid_at ?? $payment->created_at)->format('M d, Y · h:i A') }}</dd></div>
            <div><dt class="text-stone-500">Member</dt><dd class="mt-1 font-medium">{{ $payment->member->full_name }}</dd></div>
            <div><dt class="text-stone-500">Payment method</dt><dd class="mt-1 font-medium">{{ $payment->payment_method }}</dd></div>
            <div><dt class="text-stone-500">Membership plan</dt><dd class="mt-1 font-medium">{{ $payment->membershipPlan?->name ?? '—' }}</dd></div>
            <div><dt class="text-stone-500">Email</dt><dd class="mt-1 break-all font-medium">{{ $payment->member->email }}</dd></div>
        </dl>
        <div class="flex items-center justify-between pt-7">
            <span class="text-sm text-stone-400">Total paid</span>
            <span class="text-3xl font-semibold tracking-tight text-[#c6f45b]">₱{{ number_format((float) $payment->amount, 2) }}</span>
        </div>
        <p class="mt-10 text-center text-xs text-stone-600">Keep this receipt for your records.</p>
    </main>
</body>
</html>
