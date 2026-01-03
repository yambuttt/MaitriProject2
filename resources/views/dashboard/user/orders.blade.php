@extends('layouts.dashboard')
@section('title','Riwayat Produk')
@section('breadcrumb','Dashboard • Riwayat Produk')

@section('content')
  <div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3">
      <div>
        <h1 class="text-xl md:text-2xl font-semibold">Riwayat Produk</h1>
        <p class="text-sm text-slate-400 mt-1">Transaksi Digital Goods yang pernah kamu beli.</p>
      </div>
      <div class="text-xs text-slate-500">
        Total: <span class="text-slate-200 font-medium">{{ $orders->total() }}</span> transaksi
      </div>
    </div>

    {{-- Filter bar --}}
    <form method="get" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-[minmax(0,2fr)_200px_140px]">
      <div class="sm:col-span-2 lg:col-span-1">
        <div class="relative">
          <input name="q" value="{{ $q ?? '' }}" placeholder="Cari kode MP-, nomor tujuan, atau SKU..."
            class="w-full h-11 rounded-2xl bg-slate-950/40 border border-slate-800/70 px-4 ps-10 text-sm
                   placeholder:text-slate-500 focus:outline-none focus:border-violet-500/60 focus:ring-1 focus:ring-violet-500/40">
          <svg class="absolute left-4 top-1/2 -translate-y-1/2 size-4 text-slate-500" viewBox="0 0 24 24" fill="none">
            <path d="M21 21l-4.3-4.3M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" stroke="currentColor" stroke-width="1.5"/>
          </svg>
        </div>
      </div>

      <div>
        <select name="status"
          class="w-full h-11 rounded-2xl bg-slate-950/40 border border-slate-800/70 px-4 text-sm
                 focus:outline-none focus:border-violet-500/60 focus:ring-1 focus:ring-violet-500/40">
          <option value="">Semua status</option>
          <option value="success" @selected(($status ?? '') === 'success')>Success</option>
          <option value="processing" @selected(($status ?? '') === 'processing')>Processing</option>
          <option value="pending" @selected(($status ?? '') === 'pending')>Pending</option>
          <option value="waiting_payment" @selected(($status ?? '') === 'waiting_payment')>Waiting payment</option>
          <option value="failed" @selected(($status ?? '') === 'failed')>Failed</option>
        </select>
      </div>

      <button class="h-11 rounded-2xl bg-violet-600 hover:bg-violet-500 text-sm font-medium transition">
        Filter
      </button>
    </form>

    {{-- List --}}
    <div class="space-y-3">
      @forelse($orders as $order)
        @php
          $statusRaw = strtolower($order->status ?? '');
          $statusLabel = match ($statusRaw) {
            'success' => 'Sukses',
            'failed' => 'Gagal',
            'processing' => 'Diproses',
            'pending' => 'Menunggu',
            'waiting_payment' => 'Menunggu Pembayaran',
            default => strtoupper($statusRaw),
          };

          $statusCls = match ($statusRaw) {
            'success' => 'bg-emerald-500/10 border-emerald-500/30 text-emerald-300',
            'failed' => 'bg-rose-500/10 border-rose-500/30 text-rose-300',
            'pending', 'processing', 'waiting_payment' => 'bg-amber-500/10 border-amber-500/30 text-amber-300',
            default => 'bg-slate-500/10 border-slate-500/30 text-slate-200',
          };

          $pay = $order->latestPayment;
          $canContinuePay = $pay
            && ($pay->status === 'pending')
            && (!$pay->expired_at || $pay->expired_at->isFuture());
        @endphp

        <div class="rounded-3xl border border-slate-800/70 bg-slate-950/35 hover:bg-slate-950/45 transition overflow-hidden">
          <div class="p-5">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
              <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                  <span class="text-[11px] px-2 py-0.5 rounded-full border border-violet-500/20 bg-violet-500/10 text-violet-200">
                    Digital Goods
                  </span>
                  <span class="font-mono text-xs text-slate-300">{{ $order->code }}</span>
                  <span class="text-xs text-slate-600">•</span>
                  <span class="text-xs text-slate-400">{{ $order->created_at?->format('d M Y, H:i') }}</span>
                </div>

                <div class="mt-2 text-base font-semibold truncate">
                  {{ $order->product->name ?? '-' }}
                  <span class="text-slate-500 font-medium">— {{ $order->variant->name ?? '-' }}</span>
                </div>

                <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-slate-400">
                  <span class="px-2 py-1 rounded-xl border border-slate-800/70 bg-[#0B1222]/40 font-mono">
                    {{ $order->target }}
                  </span>
                  <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[11px] font-medium {{ $statusCls }}">
                    {{ $statusLabel }}
                  </span>
                </div>
              </div>

              <div class="shrink-0 text-right space-y-2">
                <div class="text-lg font-semibold text-slate-100">
                  Rp {{ number_format((int) $order->total, 0, ',', '.') }}
                </div>

                <div class="flex flex-wrap md:justify-end gap-2">
                  <button type="button"
                    class="h-9 px-3 rounded-2xl border border-slate-800/70 hover:bg-slate-900/40 text-xs transition open-order-detail"
                    data-code="{{ $order->code }}"
                    data-product="{{ $order->product->name ?? '-' }}"
                    data-variant="{{ $order->variant->name ?? '-' }}"
                    data-target="{{ $order->target }}"
                    data-total="Rp {{ number_format($order->total, 0, ',', '.') }}"
                    data-subtotal="Rp {{ number_format($order->subtotal, 0, ',', '.') }}"
                    data-adminfee="{{ $order->admin_fee > 0 ? 'Rp ' . number_format($order->admin_fee, 0, ',', '.') : 'Gratis' }}"
                    data-payment="{{ $order->payment_method_label ?? '-' }}"
                    data-status="{{ $order->status }}"
                    data-status-text="{{ strtoupper($order->status) }}"
                    data-email="{{ $order->email ?? '-' }}"
                    data-phone="{{ $order->phone ?? '-' }}"
                    data-created="{{ $order->created_at->format('d M Y, H:i') }}"
                    data-sn="{{ $order->provider_sn ?? '' }}">
                    Detail
                  </button>

                  <a href="{{ route('invoices.show', $order->code) }}"
                    class="h-9 px-3 rounded-2xl border border-slate-800/70 hover:bg-slate-900/40 text-xs transition">
                    Invoice
                  </a>

                  @if($canContinuePay)
                    <a href="{{ route('orders.payment.show', ['order' => $order, 'payment' => $pay]) }}"
                      class="h-9 px-3 rounded-2xl bg-violet-600 hover:bg-violet-500 text-xs font-medium transition">
                      Lanjutkan Bayar
                    </a>
                  @endif
                </div>
              </div>
            </div>
          </div>

          {{-- divider halus --}}
          <div class="h-px bg-gradient-to-r from-transparent via-slate-800/80 to-transparent"></div>
        </div>
      @empty
        <div class="rounded-3xl border border-slate-800/70 bg-slate-950/35 p-6 text-sm text-slate-400">
          Belum ada transaksi Digital Goods. Coba beli produk dari katalog dulu.
        </div>
      @endforelse
    </div>

    <div>
      {{ $orders->links() }}
    </div>

    
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
  </div>
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
