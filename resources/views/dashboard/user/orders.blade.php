@extends('layouts.dashboard')

@section('content')
    <h1 class="text-xl font-semibold mb-4">Riwayat Pembelian Produk</h1>

    <div class="space-y-3">
        @foreach($orders as $order)
            <div class="p-4 rounded-xl bg-slate-800/40 border border-slate-700 flex items-center justify-between">

                <div>
                    <div class="font-medium text-slate-100">{{ $order->code }}</div>
                    <div class="text-xs text-slate-400">
                        {{ $order->buyer_sku_code }} • {{ $order->target }}
                    </div>
                    <div class="text-xs mt-1">
                        Status:
                        <span
                            class="text-{{ $order->status === 'success' ? 'emerald' : ($order->status === 'failed' ? 'rose' : 'yellow') }}-400">
                            {{ strtoupper($order->status) }}
                        </span>
                    </div>
                </div>

                <div class="text-right">
                    <div class="text-slate-200 font-semibold">
                        Rp {{ number_format($order->total) }}
                    </div>

                    @php
                        // label metode bayar (copas dari invoice show)
                        $paymentLabel = match ($order->payment_method) {
                            'wallet' => 'Saldo Maitri',
                            'paydisini_qris' => 'QRIS PayDisini',
                            'paydisini_va_mandiri' => 'VA Mandiri (PayDisini)',
                            'paydisini_alfamart' => 'Alfamart (PayDisini)',
                            'paydisini_indomaret' => 'Indomaret (PayDisini)',
                            default => 'Metode lain',
                        };

                        $statusUpper = strtoupper($order->status);
                    @endphp

                    {{-- tombol modal detail --}}
                    <button type="button" class="block text-xs text-sky-400 underline mb-1 open-order-detail"
                        data-code="{{ $order->code }}" data-product="{{ $order->product->name ?? '-' }}"
                        data-variant="{{ $order->variant->name ?? '-' }}" data-target="{{ $order->target }}"
                        data-total="Rp {{ number_format($order->total, 0, ',', '.') }}"
                        data-subtotal="Rp {{ number_format($order->subtotal, 0, ',', '.') }}"
                        data-adminfee="{{ $order->admin_fee > 0 ? 'Rp ' . number_format($order->admin_fee, 0, ',', '.') : 'Gratis' }}"
                        data-payment="{{ $paymentLabel }}" data-status="{{ $order->status }}"
                        data-status-text="{{ $statusUpper }}" data-email="{{ $order->email ?? '-' }}"
                        data-phone="{{ $order->phone ?? '-' }}" data-created="{{ $order->created_at->format('d M Y, H:i') }}"
                        data-sn="{{ $order->provider_sn ?? '' }}">
                        Lihat detail pesanan
                    </button>

                    @if($payment ?? false)
                        <a href="{{ route('orders.payment.show', ['order' => $order, 'payment' => $payment]) }}"
                            class="text-xs text-emerald-400 underline">
                            Lanjutkan Pembayaran
                        </a>
                    @else
                        <a href="{{ route('invoices.show', $order->code) }}" class="text-xs text-slate-400 underline">
                            Lihat Invoice
                        </a>
                    @endif
                </div>



            </div>
        @endforeach
    </div>
    {{-- Modal detail pesanan --}}
    <div id="orderDetailModal" class="fixed inset-0 z-40 hidden items-center justify-center bg-black/60">
        <div class="w-full max-w-2xl rounded-3xl bg-slate-900 border border-slate-800 p-5 md:p-6 space-y-5">
            {{-- Header --}}
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-slate-50">Detail Transaksi</h2>
                    <p class="text-xs text-slate-400">
                        Ringkasan pesanan dan status pembayaranmu.
                    </p>
                </div>
                <div class="text-right space-y-1">
                    <div
                        class="inline-flex items-center gap-2 rounded-full border border-slate-700 bg-slate-950/80 px-3 py-1">
                        <span class="text-[11px] text-slate-400">Kode</span>
                        <span id="mCode" class="font-mono text-sm text-slate-100">MP-00000</span>
                    </div>
                    <span id="mStatusBadge"
                        class="inline-flex items-center rounded-full border px-3 py-0.5 text-xs font-medium">
                        STATUS
                    </span>
                </div>
            </div>

            {{-- Isi utama --}}
            <div class="rounded-2xl border border-slate-800/80 bg-slate-950/80 p-4 space-y-4">
                <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                    <div class="space-y-1.5">
                        <p class="text-[11px] font-medium tracking-wide text-slate-400 uppercase">Produk</p>
                        <p id="mProduct" class="text-sm font-semibold text-slate-100">Nama Produk</p>
                        <p id="mVariant" class="text-xs text-slate-400">Varian</p>

                        <p class="mt-3 text-[11px] font-medium tracking-wide text-slate-400 uppercase">Nomor Tujuan</p>
                        <p id="mTarget" class="text-sm font-mono text-slate-100">08xxxxxxxxx</p>
                    </div>
                    <div class="space-y-1 text-right">
                        <p class="text-[11px] font-medium tracking-wide text-slate-400 uppercase">Total Bayar</p>
                        <p id="mTotal" class="text-2xl font-semibold text-slate-50">Rp 0</p>
                        <p class="text-xs text-slate-400">
                            Dibayar dengan <span id="mPayment" class="font-medium text-slate-200">-</span>
                        </p>
                    </div>
                </div>

                <div class="h-px bg-gradient-to-r from-slate-800 via-slate-700/60 to-slate-800"></div>

                <div class="grid gap-6 md:grid-cols-2">
                    {{-- Ringkasan transaksi --}}
                    <div class="space-y-3">
                        <h3 class="text-sm font-semibold text-slate-200">Ringkasan Transaksi</h3>
                        <dl class="space-y-2 text-sm text-slate-200">
                            <div class="flex justify-between gap-4">
                                <dt class="text-slate-400">Tanggal</dt>
                                <dd id="mCreated" class="text-right">-</dd>
                            </div>
                            <div class="flex justify-between gap-4">
                                <dt class="text-slate-400">Metode Pembayaran</dt>
                                <dd id="mPayment2" class="text-right">-</dd>
                            </div>
                            <div class="flex justify-between gap-4">
                                <dt class="text-slate-400">Email Bukti</dt>
                                <dd id="mEmail" class="text-right">-</dd>
                            </div>
                            <div class="flex justify-between gap-4">
                                <dt class="text-slate-400">No. HP Pembeli</dt>
                                <dd id="mPhone" class="text-right">-</dd>
                            </div>
                            <div class="flex justify-between gap-4">
                                <dt class="text-slate-400">Status</dt>
                                <dd id="mStatusText" class="text-right">-</dd>
                            </div>
                            <div id="mSnRow" class="flex justify-between gap-4 hidden">
                                <dt class="text-slate-400">SN / Token</dt>
                                <dd class="text-right">
                                    <span id="mSnText" class="font-mono text-emerald-200"></span>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Rincian pembayaran --}}
                    <div class="space-y-3">
                        <h3 class="text-sm font-semibold text-slate-200">Rincian Pembayaran</h3>
                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between gap-4">
                                <dt class="text-slate-400">Harga Produk</dt>
                                <dd id="mSubtotal" class="text-slate-100">Rp 0</dd>
                            </div>
                            <div class="flex justify-between gap-4">
                                <dt class="text-slate-400">Biaya Admin</dt>
                                <dd id="mAdminFee" class="text-slate-100">Rp 0</dd>
                            </div>
                            <div class="flex justify-between gap-4 pt-2 border-t border-slate-800 mt-2">
                                <dt class="text-slate-400">Total Bayar</dt>
                                <dd id="mTotal2" class="font-semibold text-slate-50">Rp 0</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            {{-- Footer modal --}}
            <div class="flex justify-end">
                <button type="button" id="orderDetailClose"
                    class="px-4 py-2 text-sm rounded-xl border border-slate-700 hover:bg-slate-800">
                    Tutup
                </button>
            </div>
        </div>
    </div>


    {{ $orders->links() }}
@endsection
@push('body')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('orderDetailModal');
    if (!modal) return;

    const closeBtn = document.getElementById('orderDetailClose');

    const codeEl     = document.getElementById('mCode');
    const productEl  = document.getElementById('mProduct');
    const variantEl  = document.getElementById('mVariant');
    const targetEl   = document.getElementById('mTarget');
    const totalEl    = document.getElementById('mTotal');
    const total2El   = document.getElementById('mTotal2');
    const subtotalEl = document.getElementById('mSubtotal');
    const adminEl    = document.getElementById('mAdminFee');
    const paymentEl  = document.getElementById('mPayment');
    const payment2El = document.getElementById('mPayment2');
    const createdEl  = document.getElementById('mCreated');
    const emailEl    = document.getElementById('mEmail');
    const phoneEl    = document.getElementById('mPhone');
    const statusText = document.getElementById('mStatusText');
    const statusBadge = document.getElementById('mStatusBadge');
    const snRow      = document.getElementById('mSnRow');
    const snText     = document.getElementById('mSnText');

    function openModal(btn) {
        const statusRaw = (btn.dataset.status || '').toLowerCase();

        codeEl.textContent     = btn.dataset.code;
        productEl.textContent  = btn.dataset.product;
        variantEl.textContent  = btn.dataset.variant;
        targetEl.textContent   = btn.dataset.target;
        totalEl.textContent    = btn.dataset.total;
        total2El.textContent   = btn.dataset.total;
        subtotalEl.textContent = btn.dataset.subtotal;
        adminEl.textContent    = btn.dataset.adminfee;
        paymentEl.textContent  = btn.dataset.payment;
        payment2El.textContent = btn.dataset.payment;
        createdEl.textContent  = btn.dataset.created;
        emailEl.textContent    = btn.dataset.email || '-';
        phoneEl.textContent    = btn.dataset.phone || '-';
        statusText.textContent = btn.dataset['statusText'] || statusRaw.toUpperCase();

        // status badge color (sama logic dengan invoice) :contentReference[oaicite:3]{index=3}
        let cls = 'inline-flex items-center rounded-full border px-3 py-0.5 text-xs font-medium ';
        if (statusRaw === 'success') {
            cls += 'bg-emerald-500/10 border-emerald-500/40 text-emerald-300';
        } else if (statusRaw === 'pending' || statusRaw === 'processing') {
            cls += 'bg-amber-500/10 border-amber-500/40 text-amber-300';
        } else if (statusRaw === 'failed') {
            cls += 'bg-rose-500/10 border-rose-500/40 text-rose-300';
        } else {
            cls += 'bg-slate-500/10 border-slate-500/40 text-slate-200';
        }
        statusBadge.className = cls;
        statusBadge.textContent = (btn.dataset['statusText'] || statusRaw.toUpperCase());

        const sn = btn.dataset.sn || '';
        if (sn) {
            snText.textContent = sn;
            snRow.classList.remove('hidden');
        } else {
            snRow.classList.add('hidden');
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.querySelectorAll('.open-order-detail').forEach(btn => {
        btn.addEventListener('click', () => openModal(btn));
    });

    closeBtn.addEventListener('click', closeModal);

    // klik luar card untuk tutup
    modal.addEventListener('click', e => {
        if (e.target === modal) closeModal();
    });

    // ESC untuk tutup
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeModal();
    });
});
</script>
@endpush
