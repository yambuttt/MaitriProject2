{{-- resources/views/dashboard/admin/partials/order-detail-modal.blade.php --}}

@php
    $isMarketplace = $type === 'marketplace';

    if ($isMarketplace) {
        $code = $order->invoice_number;
        $status = $order->status;
        $paymentStatus = $order->payment_status;
        $productName = $order->product->name ?? '-';
        $variantName = $order->variant->name ?? '-';
        $customerEmail = $order->customer_email ?? $order->user->email ?? '-';
        $customerPhone = $order->customer_phone ?? '-';
        $total = $order->total_amount;
        $fee = $order->fee ?? 0;
        $userNote = $order->user_note;   // catatan dari pembeli
        $adminNote = $order->admin_note;  // catatan dari admin
        $createdAt = $order->created_at;
        $paidAt = $order->paid_at;
        $method = $order->payment_method;
        $payment = $order->payment ?? null;
    } else {
        $code = $order->code;
        $status = $order->status;
        $paymentStatus = $order->payment_status;
        $productName = $order->product->name ?? '-';
        $variantName = $order->variant->name ?? '-';
        $customerEmail = $order->email ?? $order->user->email ?? '-';
        $customerPhone = $order->phone ?? '-';
        $total = $order->total;
        $fee = $order->admin_fee ?? 0;
        $notes = $order->note ?? null;
        $createdAt = $order->created_at;
        $paidAt = $order->paid_at;
        $method = $order->payment_method;
        $payment = $order->payments->first();
    }
@endphp

<div class="space-y-4">
    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <h2 class="text-base font-semibold text-slate-50">
                Detail Pesanan
            </h2>
            <p class="text-xs text-slate-400 mt-1">
                {{ $isMarketplace ? 'Marketplace Order' : 'Produk Digiflazz' }} •
                <span class="font-mono">{{ $code }}</span>
            </p>
        </div>
    </div>

    {{-- Info utama --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
        <div class="space-y-2">
            <div>
                <div class="text-slate-400">Produk</div>
                <div class="text-slate-100 font-medium">
                    {{ $productName }}
                </div>
                <div class="text-slate-400">
                    Varian: {{ $variantName }}
                </div>
            </div>

            <div>
                <div class="text-slate-400">Customer</div>
                <div class="text-slate-100">{{ $customerEmail }}</div>
                <div class="text-slate-400">{{ $customerPhone ?: '-' }}</div>
            </div>

            <div>
                <div class="text-slate-400">Waktu</div>
                <div class="text-slate-100">
                    {{ $createdAt->format('d M Y H:i') }}
                </div>
                @if($paidAt)
                    <div class="text-slate-400">
                        Dibayar: {{ $paidAt->format('d M Y H:i') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-2">
            <div>
                <div class="text-slate-400">Status Pesanan</div>
                <div class="mt-1 flex flex-wrap gap-2">
                    <span class="px-2 py-1 rounded-lg text-[11px] border
                        @if($status === 'success' || $status === 'paid_finished') border-emerald-500/60 text-emerald-300 bg-emerald-500/10
                        @elseif($status === 'failed' || $status === 'paid_rejected') border-rose-500/60 text-rose-300 bg-rose-500/10
                        @else border-sky-500/60 text-sky-300 bg-sky-500/10 @endif">
                        {{ strtoupper(str_replace('_', ' ', $status)) }}
                    </span>

                    <span
                        class="px-2 py-1 rounded-lg text-[11px] border border-slate-600 text-slate-200 bg-slate-700/20">
                        PAYMENT: {{ strtoupper($paymentStatus ?? '-') }}
                    </span>
                </div>
            </div>

            <div>
                <div class="text-slate-400">Metode Pembayaran</div>
                <div class="text-slate-100">
                    {{ $method ? strtoupper(str_replace('_', ' ', $method)) : '-' }}
                </div>
                @if($payment && $payment->provider)
                    <div class="text-slate-400">
                        Provider: {{ strtoupper($payment->provider) }}
                    </div>
                @endif
            </div>

            <div class="border border-slate-700/70 rounded-xl px-3 py-2 bg-slate-900/50">
                <div class="flex justify-between text-slate-300">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($total - $fee, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-slate-400 text-[11px] mt-1">
                    <span>Biaya Admin</span>
                    <span>Rp {{ number_format($fee, 0, ',', '.') }}</span>
                </div>
                <div
                    class="border-t border-slate-700/70 mt-2 pt-2 flex justify-between text-sm font-semibold text-slate-50">
                    <span>Total</span>
                    <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Bagian khusus berdasarkan tipe --}}
    @if(!$isMarketplace)
        {{-- Detail Digiflazz --}}
        <div class="border border-slate-800/80 rounded-xl px-3 py-2 bg-slate-950/50 text-xs space-y-1">
            <div class="text-slate-400 font-medium mb-1">Detail Digiflazz</div>

            <div class="flex justify-between gap-3">
                <span class="text-slate-400">Ref ID Provider</span>
                <span class="text-slate-100 font-mono">
                    {{ $order->provider_ref_id ?? '-' }}
                </span>
            </div>

            <div class="flex justify-between gap-3">
                <span class="text-slate-400">Status Provider</span>
                <span class="text-slate-100">
                    {{ $order->provider_status ?? '-' }}
                </span>
            </div>

            {{-- 🔽 Provider message (alasan gagal / pesan sukses dari Digiflazz) --}}
            <div class="flex justify-between gap-3">
                <span class="text-slate-400">Pesan Provider</span>
                <span class="text-right text-slate-100 max-w-[260px]">
                    {{ $order->provider_message ?? '-' }}
                </span>
            </div>

            <div class="flex justify-between gap-3">
                <span class="text-slate-400">SN</span>
                <span class="text-slate-100">
                    {{ $order->provider_sn ?? '-' }}
                </span>
            </div>
        </div>

    @else
        {{-- Detail Marketplace --}}
        {{-- Detail Marketplace --}}
        <div class="border border-slate-800/80 rounded-xl px-3 py-2 bg-slate-950/50 text-xs space-y-2">
            <div class="text-slate-400 font-medium mb-1">Detail Marketplace</div>

            @if(!empty($userNote))
                <div class="text-slate-100">
                    <span class="text-slate-400 block mb-0.5">Catatan dari pembeli</span>
                    <div class="whitespace-pre-line">
                        {!! nl2br(e($userNote)) !!}
                    </div>
                </div>
            @endif

            @if(!empty($adminNote))
                <div class="text-slate-100">
                    <span class="text-slate-400 block mb-0.5">Catatan Admin</span>
                    <div class="whitespace-pre-line">
                        {!! nl2br(e($adminNote)) !!}
                    </div>
                </div>
            @endif

            @if(empty($userNote) && empty($adminNote))
                <div class="text-slate-500">
                    Belum ada catatan dari pembeli maupun admin.
                </div>
            @endif
        </div>

    @endif
</div>