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
        $userNote = $order->user_note;
        $adminNote = $order->admin_note;
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

    // Badge styling mapping
    if ($status === 'success' || $status === 'paid_finished') {
        $statusBadge = 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20 shadow-[0_0_10px_rgba(16,185,129,0.1)]';
    } elseif ($status === 'failed' || $status === 'paid_rejected') {
        $statusBadge = 'bg-rose-500/10 text-rose-400 border-rose-500/20 shadow-[0_0_10px_rgba(244,63,94,0.1)]';
    } else {
        $statusBadge = 'bg-sky-500/10 text-sky-400 border-sky-500/20 shadow-[0_0_10px_rgba(14,165,233,0.1)]';
    }
@endphp

<div class="space-y-5 animate-in fade-in slide-in-from-bottom-2 duration-200">
    
    {{-- Header ID Tag --}}
    <div class="flex items-center justify-between gap-4 bg-white/[0.02] border border-white/5 px-4 py-3 rounded-2xl">
        <div class="flex items-center gap-2">
            <span class="size-2 rounded-full bg-violet-500 animate-ping"></span>
            <span class="font-mono text-xs text-violet-300 font-extrabold">{{ $code }}</span>
        </div>
        <span class="text-[8px] font-extrabold uppercase tracking-widest px-2.5 py-1 rounded-lg bg-white/5 border border-white/5 text-slate-400">
            {{ $isMarketplace ? 'Marketplace' : 'Digiflazz' }}
        </span>
    </div>

    {{-- Detail Grid Columns --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        
        {{-- Left: Customer & Product Info --}}
        <div class="space-y-4">
            
            {{-- Info Produk --}}
            <div class="bg-white/[0.02] border border-white/5 p-4 rounded-2xl space-y-2">
                <span class="text-[9px] font-extrabold uppercase tracking-widest text-slate-500 block">📦 Informasi Produk</span>
                <div class="text-white font-bold text-sm tracking-tight leading-snug">
                    {{ $productName }}
                </div>
                <div class="text-[11px] text-slate-400">
                    Varian: <span class="text-slate-300 font-semibold">{{ $variantName }}</span>
                </div>
            </div>

            {{-- Info Customer --}}
            <div class="bg-white/[0.02] border border-white/5 p-4 rounded-2xl space-y-2">
                <span class="text-[9px] font-extrabold uppercase tracking-widest text-slate-500 block">👤 Informasi Pelanggan</span>
                <div class="text-xs text-white font-bold tracking-tight">
                    {{ $customerEmail }}
                </div>
                <div class="text-[11px] text-slate-400">
                    No. HP: <span class="text-slate-300 font-semibold">{{ $customerPhone ?: '-' }}</span>
                </div>
            </div>

            {{-- Info Waktu --}}
            <div class="bg-white/[0.02] border border-white/5 p-4 rounded-2xl space-y-2.5">
                <span class="text-[9px] font-extrabold uppercase tracking-widest text-slate-500 block">📅 Riwayat Waktu</span>
                <div class="flex items-center justify-between text-[11px]">
                    <span class="text-slate-400">Dibuat</span>
                    <span class="text-white font-semibold">{{ $createdAt->format('d M Y, H:i') }}</span>
                </div>
                @if($paidAt)
                    <div class="flex items-center justify-between text-[11px] pt-1.5 border-t border-white/[0.03]">
                        <span class="text-slate-400">Dibayar</span>
                        <span class="text-emerald-400 font-semibold">{{ $paidAt->format('d M Y, H:i') }}</span>
                    </div>
                @endif
            </div>

        </div>

        {{-- Right: Status & Billing Info --}}
        <div class="space-y-4">
            
            {{-- Status & Payment --}}
            <div class="bg-white/[0.02] border border-white/5 p-4 rounded-2xl space-y-3">
                <span class="text-[9px] font-extrabold uppercase tracking-widest text-slate-500 block">📊 Status Transaksi</span>
                <div class="flex flex-wrap gap-2 pt-1">
                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wider border {{ $statusBadge }}">
                        {{ strtoupper(str_replace('_', ' ', $status)) }}
                    </span>
                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wider border border-white/10 bg-white/5 text-slate-300">
                        {{ strtoupper($paymentStatus ?? '-') }}
                    </span>
                </div>
                
                <div class="pt-2.5 border-t border-white/[0.03] text-[11px]">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Metode Pembayaran</span>
                        <span class="text-white font-bold">{{ $method ? strtoupper(str_replace('_', ' ', $method)) : '-' }}</span>
                    </div>
                    @if($payment && $payment->provider)
                        <div class="flex justify-between mt-1.5">
                            <span class="text-slate-400">Provider</span>
                            <span class="text-violet-400 font-semibold">{{ strtoupper($payment->provider) }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Billing Breakdown --}}
            <div class="bg-white/[0.02] border border-white/5 p-4 rounded-2xl space-y-2.5">
                <span class="text-[9px] font-extrabold uppercase tracking-widest text-slate-500 block">💰 Rincian Pembayaran</span>
                
                <div class="flex justify-between text-[11px] text-slate-400">
                    <span>Subtotal</span>
                    <span class="text-white font-semibold">Rp {{ number_format($total - $fee, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-[11px] text-slate-400 pb-2 border-b border-dashed border-white/10">
                    <span>Biaya Admin</span>
                    <span class="text-white font-semibold">Rp {{ number_format($fee, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center pt-1">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-300">Total Akhir</span>
                    <span class="text-lg font-extrabold text-violet-300 tracking-tight">
                        Rp {{ number_format($total, 0, ',', '.') }}
                    </span>
                </div>
            </div>

        </div>

    </div>

    {{-- Type Specific Integration Details Panel --}}
    @if(!$isMarketplace)
        {{-- Digiflazz API Details --}}
        <div class="bg-black/30 border border-white/5 rounded-2xl p-4 space-y-2.5">
            <span class="text-[9px] font-extrabold uppercase tracking-widest text-violet-400 block">⚡ RESPONS DIGIFLAZZ API</span>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-[11px] font-semibold text-slate-400">
                <div class="flex justify-between py-1 border-b border-white/[0.02] sm:border-none">
                    <span>Ref ID Provider</span>
                    <span class="text-white font-mono font-medium">{{ $order->provider_ref_id ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-white/[0.02] sm:border-none">
                    <span>Status Provider</span>
                    <span class="text-white font-medium">{{ $order->provider_status ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-white/[0.02] sm:border-none">
                    <span>Serial Number (SN)</span>
                    <span class="text-white font-mono font-medium">{{ $order->provider_sn ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1">
                    <span>Pesan Provider</span>
                    <span class="text-white font-medium text-right max-w-[200px] truncate" title="{{ $order->provider_message }}">
                        {{ $order->provider_message ?? '-' }}
                    </span>
                </div>
            </div>
        </div>

    @else
        {{-- Marketplace Client Details --}}
        <div class="bg-black/30 border border-white/5 rounded-2xl p-4 space-y-3">
            <span class="text-[9px] font-extrabold uppercase tracking-widest text-violet-400 block">📝 CATATAN TRANSAKSI</span>
            
            <div class="space-y-3.5 text-[11px]">
                @if(!empty($userNote))
                    <div class="bg-white/[0.01] border border-white/5 p-3 rounded-xl">
                        <span class="text-[8px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">Catatan Pembeli</span>
                        <p class="text-white font-semibold leading-relaxed whitespace-pre-line">
                            {!! nl2br(e($userNote)) !!}
                        </p>
                    </div>
                @endif

                @if(!empty($adminNote))
                    <div class="bg-white/[0.01] border border-white/5 p-3 rounded-xl">
                        <span class="text-[8px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">Catatan Admin</span>
                        <p class="text-violet-300 font-semibold leading-relaxed whitespace-pre-line">
                            {!! nl2br(e($adminNote)) !!}
                        </p>
                    </div>
                @endif

                @if(empty($userNote) && empty($adminNote))
                    <div class="text-center text-slate-500 py-2 font-bold uppercase tracking-wider text-[10px]">
                        Belum ada catatan dari pembeli maupun admin.
                    </div>
                @endif
            </div>
        </div>

    @endif
</div>