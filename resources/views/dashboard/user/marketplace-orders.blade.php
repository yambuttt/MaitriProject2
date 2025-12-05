@extends('layouts.dashboard')

@section('title', 'Riwayat Pesanan Marketplace')
@section('breadcrumb', 'Riwayat Pesanan Marketplace')

@section('content')
    <div class="max-w-5xl">
        <h1 class="text-xl md:text-2xl font-semibold mb-4 text-slate-100">
            Riwayat Pesanan Marketplace
        </h1>

        @forelse($orders as $order)
            @php
                $statusLabel = match ($order->status) {
                    'not_paid'        => 'NOT PAID',
                    'paid_received'   => 'PAID & PESANAN DITERIMA',
                    'paid_processing' => 'PAID & PESANAN DIPROSES',
                    'paid_rejected'   => 'PAID & PESANAN DITOLAK',
                    'paid_finished'   => 'PAID & PESANAN SELESAI',
                    default           => strtoupper($order->status),
                };

                $paymentMethodLabel = match ($order->payment_method) {
                    'wallet'                 => 'Saldo Maitri',
                    'paydisini_qris'         => 'QRIS',
                    'paydisini_va_mandiri'   => 'VA Mandiri',
                    'paydisini_alfamart'     => 'Alfamart',
                    'paydisini_indomaret'    => 'Indomaret',
                    default                  => $order->payment_method,
                };
            @endphp

            <div class="mb-4 rounded-2xl border border-slate-800/70 bg-slate-950/70 px-4 py-3 md:px-5 md:py-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="space-y-1 text-sm">
                        <div class="font-mono text-xs text-slate-300">
                            {{ $order->invoice_number }}
                        </div>
                        <div class="text-slate-100 text-sm">
                            {{ $order->product->name ?? 'Produk marketplace' }}
                        </div>
                        <div class="text-xs text-slate-400">
                            {{ $order->variant->name ?? '-' }}
                        </div>
                        <div class="text-[11px] text-slate-500">
                            Status: {{ $statusLabel }}
                        </div>
                    </div>

                    <div class="text-right text-sm space-y-1">
                        <div class="font-semibold text-slate-100">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </div>

                        {{-- Kalau masih belum dibayar & ada payment pending → lanjutkan pembayaran --}}
                        @if($order->payment && $order->payment->status === 'pending')
                            <a href="{{ route('marketplace.payment.show', $order->payment) }}"
                               class="block text-xs text-emerald-400 underline">
                                Lanjutkan pembayaran
                            </a>
                        @endif

                        {{-- Kalau sudah dibayar / sedang diproses admin → tampilkan popup status pesanan --}}
                        @if(in_array($order->status, ['paid_received','paid_processing','paid_finished','paid_rejected']))
                            <button type="button"
                                    class="block text-xs text-sky-400 underline open-mp-order-detail">
                                Lihat status pesanan
                            </button>
                        @endif

                        <a href="{{ route('marketplace.invoice.show', $order) }}"
                           class="block text-xs text-slate-400 underline">
                            Lihat Invoice
                        </a>
                    </div>
                </div>

                {{-- TEMPLATE DETAIL (disalin ke dalam modal saat tombol diklik) --}}
                <div class="hidden mp-order-detail-template">
                    <div class="space-y-5">
                        {{-- Header --}}
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-50">
                                    Detail Pesanan Marketplace
                                </h2>
                                <p class="text-xs text-slate-400 mt-1">
                                    Ringkasan pesanan marketplace dan status pembayaranmu.
                                </p>
                            </div>

                            <div class="text-right space-y-1">
                                <p class="text-[11px] text-slate-400">Status pesanan</p>
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-medium
                                    @if($order->status === 'paid_finished') bg-emerald-500/20 text-emerald-300
                                    @elseif($order->status === 'paid_rejected') bg-rose-500/20 text-rose-300
                                    @elseif($order->status === 'not_paid') bg-amber-500/20 text-amber-300
                                    @else bg-sky-500/20 text-sky-300 @endif">
                                    {{ $statusLabel }}
                                </span>
                                <div class="text-[11px] text-slate-500">
                                    Invoice {{ $order->created_at?->format('d M Y H:i') }}
                                </div>
                            </div>
                        </div>

                        {{-- Card utama --}}
                        <div class="rounded-2xl border border-slate-800/80 bg-slate-900/80 divide-y divide-slate-800/70">
                            {{-- Billed to + invoice info --}}
                            <div class="p-4 md:p-5 space-y-4">
                                <div class="flex items-center justify-between gap-4 flex-wrap">
                                    <div class="space-y-2">
                                        <p class="text-[11px] text-slate-400 uppercase tracking-wide">Billed To</p>
                                        <div class="flex items-center gap-3">
                                            <div class="h-9 w-9 rounded-full bg-violet-600 text-xs flex items-center justify-center text-slate-50 font-semibold">
                                                @php
                                                    $name = $order->user?->name ?: ($order->customer_email ?: 'User');
                                                    $initials = trim(collect(explode(' ', $name))->map(fn($p) => mb_substr($p, 0, 1))->join(''));
                                                @endphp
                                                {{ $initials }}
                                            </div>
                                            <div class="text-sm">
                                                <p class="font-medium text-slate-100">
                                                    {{ $order->user?->name ?? ($order->customer_email ?: 'Pengguna Marketplace') }}
                                                </p>
                                                <p class="text-xs text-slate-400">
                                                    {{ $order->customer_email ?: '-' }}
                                                </p>
                                                <p class="text-xs text-slate-500">
                                                    {{ $order->customer_phone ?: 'No HP belum diisi' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-2 text-sm min-w-[220px]">
                                        <div class="grid grid-cols-2 gap-2">
                                            <div class="space-y-1">
                                                <p class="text-[11px] text-slate-400">Invoice Number</p>
                                                <div class="h-9 px-3 flex items-center rounded-xl bg-slate-950/80 border border-slate-800 text-xs font-mono text-slate-100">
                                                    {{ $order->invoice_number }}
                                                </div>
                                            </div>
                                            <div class="space-y-1">
                                                <p class="text-[11px] text-slate-400">Currency</p>
                                                <div class="h-9 px-3 flex items-center rounded-xl bg-slate-950/80 border border-slate-800 text-xs text-slate-100">
                                                    IDR — Rupiah
                                                </div>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-2">
                                            <div class="space-y-1">
                                                <p class="text-[11px] text-slate-400">Issued Date</p>
                                                <div class="h-9 px-3 flex items-center rounded-xl bg-slate-950/80 border border-slate-800 text-xs text-slate-100">
                                                    {{ $order->created_at?->format('d M Y') }}
                                                </div>
                                            </div>
                                            <div class="space-y-1">
                                                <p class="text-[11px] text-slate-400">Paid / Due</p>
                                                <div class="h-9 px-3 flex items-center rounded-xl bg-slate-950/80 border border-slate-800 text-xs text-slate-100">
                                                    @if($order->paid_at)
                                                        Dibayar {{ $order->paid_at->format('d M Y') }}
                                                    @else
                                                        Belum dibayar
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Detail item + total --}}
                            <div class="p-4 md:p-5 space-y-3">
                                <div class="space-y-1">
                                    <p class="text-[11px] text-slate-400 uppercase tracking-wide">Project / Order Detail</p>
                                    <p class="text-sm text-slate-200">
                                        {{ $order->product?->name ?? 'Produk marketplace' }}
                                    </p>
                                    <p class="text-xs text-slate-400">
                                        Varian: {{ $order->variant?->name ?? '-' }}
                                        @if($order->variant?->duration_days)
                                            ({{ $order->variant->duration_days }} hari)
                                        @endif
                                    </p>
                                </div>

                                <div class="mt-3 rounded-2xl border border-slate-800/80 bg-slate-950/70 overflow-hidden">
                                    <table class="w-full text-xs md:text-sm">
                                        <thead class="bg-slate-900/80 text-slate-400">
                                        <tr>
                                            <th class="text-left px-4 py-2.5">Item</th>
                                            <th class="text-center px-3 py-2.5 w-24">QTY</th>
                                            <th class="text-right px-3 py-2.5 w-32">Cost</th>
                                            <th class="text-right px-4 py-2.5 w-32">Amount</th>
                                        </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-800/70 text-slate-100">
                                        <tr>
                                            <td class="px-4 py-2.5">
                                                <div class="font-medium">{{ $order->product?->name ?? '-' }}</div>
                                                <div class="text-[11px] text-slate-400">
                                                    {{ $order->variant?->name ?? '-' }}
                                                </div>
                                            </td>
                                            <td class="px-3 py-2.5 text-center">1</td>
                                            <td class="px-3 py-2.5 text-right">
                                                Rp {{ number_format($order->price, 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-2.5 text-right font-semibold">
                                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="flex justify-end mt-2 text-sm">
                                    <div class="space-y-1 w-full max-w-xs">
                                        <div class="flex justify-between text-slate-400">
                                            <span>Subtotal</span>
                                            <span>Rp {{ number_format($order->price, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between text-slate-400">
                                            <span>Fee</span>
                                            <span>
                                                @if($order->fee && $order->fee > 0)
                                                    Rp {{ number_format($order->fee, 0, ',', '.') }}
                                                @else
                                                    Gratis
                                                @endif
                                            </span>
                                        </div>
                                        <div class="flex justify-between pt-2 border-t border-slate-800/70 text-slate-50 font-semibold">
                                            <span>Total</span>
                                            <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between text-xs text-slate-400 pt-1">
                                            <span>Metode bayar</span>
                                            <span>{{ $paymentMethodLabel ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Notes --}}
                            <div class="p-4 md:p-5 space-y-4">
                                @if($order->user_note)
                                    <div>
                                        <p class="text-[11px] text-slate-400 mb-1 uppercase tracking-wide">Notes from you</p>
                                        <div class="rounded-xl border border-slate-800/80 bg-slate-950/80 px-3 py-2 text-sm text-slate-200">
                                            {!! nl2br(e($order->user_note)) !!}
                                        </div>
                                    </div>
                                @endif

                                <div>
                                    <p class="text-[11px] text-slate-400 mb-1 uppercase tracking-wide">Notes from admin</p>
                                    <div class="rounded-xl border border-slate-800/80 bg-slate-950/80 px-3 py-2 text-sm text-slate-200">
                                        @if($order->admin_note)
                                            {!! nl2br(e($order->admin_note)) !!}
                                        @else
                                            <span class="text-slate-500">
                                                Belum ada catatan. Admin akan mengisi di sini setelah pesanan selesai
                                                (misalnya detail akun atau informasi penting lainnya).
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> {{-- /space-y-5 --}}
                </div> {{-- /template --}}
            </div>
        @empty
            <p class="text-sm text-slate-400">
                Belum ada pesanan marketplace.
            </p>
        @endforelse

        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </div>

    {{-- Modal detail pesanan marketplace --}}
    <div id="mpOrderModal"
         class="fixed inset-0 z-40 hidden items-center justify-center bg-black/70 px-3">
        <div class="w-full max-w-3xl rounded-3xl bg-slate-950 border border-slate-800 p-4 md:p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-3">
                <h2 class="text-sm font-semibold text-slate-100">Detail Pesanan Marketplace</h2>
                <button type="button" id="mpOrderModalClose"
                        class="inline-flex items-center justify-center rounded-xl p-2 border border-slate-700 hover:bg-slate-800">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                        <path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                    </svg>
                </button>
            </div>

            <div id="mpOrderModalBody" class="space-y-4 text-sm text-slate-200">
                {{-- konten akan diisi via JS --}}
            </div>
        </div>
    </div>
@endsection

@push('body')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal      = document.getElementById('mpOrderModal');
        const modalBody  = document.getElementById('mpOrderModalBody');
        const closeBtn   = document.getElementById('mpOrderModalClose');

        if (!modal || !modalBody || !closeBtn) return;

        function openModal(templateEl) {
            modalBody.innerHTML = templateEl.innerHTML;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modalBody.innerHTML = '';
        }

        document.querySelectorAll('.open-mp-order-detail').forEach(btn => {
            btn.addEventListener('click', () => {
                const card = btn.closest('.mb-4');
                if (!card) return;
                const template = card.querySelector('.mp-order-detail-template');
                if (!template) return;
                openModal(template);
            });
        });

        closeBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', e => {
            if (e.target === modal) closeModal();
        });
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeModal();
        });
    });
</script>
@endpush
