<div class="overflow-x-auto">
    <table class="w-full min-w-[650px] text-left text-xs">
        <thead class="text-[10px] uppercase tracking-[.12em] text-stone-600"><tr><th class="pb-3 font-semibold">Member</th><th class="pb-3 font-semibold">Plan</th><th class="pb-3 font-semibold">Date</th><th class="pb-3 font-semibold">Method</th><th class="pb-3 font-semibold">Status</th><th class="pb-3 text-right font-semibold">Amount</th><th class="pb-3 text-right font-semibold">Receipt</th></tr></thead>
        <tbody class="divide-y divide-white/[.04]">
            @forelse ($payments as $payment)
                <tr>
                    <td class="py-3"><p class="font-semibold text-stone-200">{{ $payment->member->full_name }}</p><p class="mt-1 text-[10px] text-stone-600">IC-{{ str_pad((string) $payment->id, 6, '0', STR_PAD_LEFT) }}</p></td>
                    <td class="py-3 text-stone-500">{{ $payment->membershipPlan?->name ?? '—' }}</td>
                    <td class="py-3 text-stone-500">{{ ($payment->paid_at ?? $payment->created_at)->format('M d, Y') }}</td>
                    <td class="py-3 text-stone-500">{{ $payment->payment_method }}</td>
                    <td class="py-3"><span @class(['rounded-full px-2 py-1 text-[9px] font-semibold capitalize', 'bg-emerald-300/10 text-emerald-200' => $payment->status === 'paid', 'bg-amber-300/10 text-amber-200' => $payment->status === 'pending', 'bg-rose-300/10 text-rose-200' => $payment->status === 'overdue'])>{{ $payment->status }}</span></td>
                    <td class="py-3 text-right font-semibold text-stone-200">₱{{ number_format((float) $payment->amount, 2) }}</td>
                    <td class="py-3 text-right"><a href="{{ route('payments.receipt', $payment) }}" target="_blank" rel="noopener" class="text-[10px] font-medium text-[#c6f45b] hover:text-[#e3ffae]">Print</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="py-12 text-center text-xs text-stone-600">No payments have been recorded yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
