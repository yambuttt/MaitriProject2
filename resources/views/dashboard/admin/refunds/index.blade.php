@extends('layouts.admin')
@section('title', 'Refund — Admin')

@push('head')
<style>
  .refund-table-card {
    background: rgba(17, 24, 39, 0.25);
    backdrop-filter: blur(25px);
    border: 1px solid rgba(255, 255, 255, 0.05);
    box-shadow: 0 20px 50px -15px rgba(0, 0, 0, 0.6);
  }
  .popup-glass {
    background: rgba(8, 15, 29, 0.92);
    backdrop-filter: blur(25px);
    border: 1px solid rgba(139, 92, 246, 0.25);
    box-shadow: 0 25px 70px -15px rgba(0, 0, 0, 0.8), 0 0 50px -10px rgba(139, 92, 246, 0.15);
  }
</style>
@endpush

@section('content')
  {{-- Header --}}
  <div class="reveal flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div class="space-y-1">
      <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/5 border border-white/10 text-[9px] font-extrabold uppercase tracking-widest text-violet-300">
        💸 Refund Control Center
      </div>
      <h1 class="text-3xl font-extrabold text-white tracking-tight">Refund</h1>
      <p class="text-sm text-slate-400 font-medium">Daftar refund yang sudah diproses oleh administrator toko digital Anda.</p>
    </div>

    <a href="{{ route('admin.refunds.create') }}"
      class="inline-flex items-center justify-center h-12 px-6 rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-sm font-extrabold text-white tracking-wide transition-all shadow-[0_0_15px_rgba(139,92,246,0.3)] shrink-0">
      + Buat Refund Baru
    </a>
  </div>

  {{-- Table Container --}}
  <section class="mt-8 rounded-3xl refund-table-card overflow-hidden reveal">
    
    {{-- Table Header --}}
    <div class="p-5 border-b border-white/5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h2 class="font-extrabold text-white text-base tracking-tight">Riwayat Refund</h2>
        <p class="text-xs text-slate-500 font-medium">Catatan pengembalian dana saldo dompet digital & transfer manual.</p>
      </div>

      <form method="GET" class="flex items-center gap-2">
        <input name="q" value="{{ $q ?? '' }}" placeholder="Cari kode order, admin, user..."
          class="h-9 w-64 max-w-full rounded-xl bg-black/40 border border-white/10 px-3.5 text-xs text-white placeholder:text-slate-600 outline-none focus:border-violet-500/50 transition-all">
        <button class="h-9 px-4 rounded-xl bg-violet-600 hover:bg-violet-500 text-xs font-bold text-white transition-all shadow-md">
          Cari
        </button>
      </form>
    </div>

    {{-- Responsive Table Grid --}}
    <div class="overflow-x-auto p-4 md:p-0">
      <table class="w-full block md:table text-xs font-medium border-collapse">
        <thead class="hidden md:table-header-group">
          <tr class="bg-black/30 border-b border-white/5 font-extrabold text-slate-400 uppercase tracking-widest text-[10px]">
            <th class="text-left px-5 py-4">Waktu</th>
            <th class="text-left px-5 py-4">Kode</th>
            <th class="text-left px-5 py-4">Produk</th>
            <th class="text-left px-5 py-4">Metode</th>
            <th class="text-left px-5 py-4">Jumlah</th>
            <th class="text-left px-5 py-4">Target</th>
            <th class="text-left px-5 py-4">Admin</th>
            <th class="text-right px-5 py-4">Bukti</th>
            <th class="text-right px-5 py-4">Aksi</th>
          </tr>
        </thead>
        <tbody class="w-full block md:table-row-group divide-y divide-white/[0.03] md:divide-y-0 space-y-4 md:space-y-0">
          @forelse($refunds as $r)
            <tr class="flex flex-col md:table-row border-b border-white/[0.03] hover:bg-white/[0.015] transition-all duration-300 p-5 md:p-0 gap-3.5 md:gap-0 bg-white/[0.01] md:bg-transparent rounded-2xl md:rounded-none mb-4 md:mb-0 shadow-lg md:shadow-none border border-white/5 md:border-none">
              
              {{-- Waktu --}}
              <td class="block md:table-cell px-0 md:px-5 py-0 md:py-4">
                <div class="flex items-center justify-between md:block">
                  <span class="md:hidden text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Waktu</span>
                  <span class="text-xs text-slate-400 font-semibold whitespace-nowrap">
                    {{ $r->created_at->format('d M Y, H:i') }}
                  </span>
                </div>
              </td>

              {{-- Kode Order --}}
              <td class="block md:table-cell px-0 md:px-5 py-0 md:py-4 border-t border-dashed border-white/5 md:border-none pt-3 md:pt-4">
                <div class="flex items-center justify-between md:block">
                  <span class="md:hidden text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Kode Pesanan</span>
                  <span class="font-mono text-xs text-violet-300 font-bold bg-violet-600/10 border border-violet-500/15 px-2.5 py-1 rounded-lg md:bg-transparent md:border-none md:p-0">
                    {{ $r->order?->code }}
                  </span>
                </div>
              </td>

              {{-- Produk --}}
              <td class="block md:table-cell px-0 md:px-5 py-0 md:py-4 border-t border-dashed border-white/5 md:border-none pt-3 md:pt-4">
                <div class="md:hidden text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">Info Produk</div>
                <div class="font-bold text-white text-sm tracking-tight leading-snug">
                  {{ $r->order?->product?->name ?? '-' }}
                </div>
                <div class="text-[11px] text-slate-400 mt-0.5">
                  Varian: <span class="text-slate-300 font-semibold">{{ $r->order?->variant?->name ?? '-' }}</span>
                </div>
              </td>

              {{-- Metode --}}
              <td class="block md:table-cell px-0 md:px-5 py-0 md:py-4 border-t border-dashed border-white/5 md:border-none pt-3 md:pt-4">
                <div class="flex items-center justify-between md:block">
                  <span class="md:hidden text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Metode Refund</span>
                  <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[9px] font-extrabold uppercase tracking-wider border @if($r->refund_method === 'wallet') bg-violet-500/10 text-violet-400 border-violet-500/20 @else bg-sky-500/10 text-sky-400 border-sky-500/20 @endif">
                    {{ $r->refund_method === 'wallet' ? 'Saldo Dompet' : 'Transfer Manual' }}
                  </span>
                </div>
              </td>

              {{-- Jumlah --}}
              <td class="block md:table-cell px-0 md:px-5 py-0 md:py-4 border-t border-dashed border-white/5 md:border-none pt-3 md:pt-4">
                <div class="flex items-center justify-between md:block">
                  <span class="md:hidden text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Jumlah Refund</span>
                  <span class="text-violet-300 font-extrabold text-sm md:text-white">
                    Rp {{ number_format($r->amount, 0, ',', '.') }}
                  </span>
                </div>
              </td>

              {{-- Target --}}
              <td class="block md:table-cell px-0 md:px-5 py-0 md:py-4 border-t border-dashed border-white/5 md:border-none pt-3 md:pt-4">
                <div class="md:hidden text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">Target Wallet</div>
                @if($r->refund_method === 'wallet')
                  <div class="text-white font-bold">{{ $r->targetUser?->name ?? '-' }}</div>
                  <div class="text-[11px] text-slate-400">{{ $r->targetUser?->email ?? '-' }}</div>
                @else
                  <span class="text-xs text-slate-500 font-bold">—</span>
                @endif
              </td>

              {{-- Admin --}}
              <td class="block md:table-cell px-0 md:px-5 py-0 md:py-4 border-t border-dashed border-white/5 md:border-none pt-3 md:pt-4">
                <div class="md:hidden text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">Diproses Oleh</div>
                <div class="text-white font-bold">{{ $r->admin?->name ?? '-' }}</div>
                <div class="text-[11px] text-slate-400">{{ $r->admin?->email ?? '-' }}</div>
              </td>

              {{-- Bukti --}}
              <td class="block md:table-cell px-0 md:px-5 py-0 md:py-4 border-t border-dashed border-white/5 md:border-none pt-3 md:pt-4 text-right">
                <div class="flex items-center justify-between md:block">
                  <span class="md:hidden text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Bukti Transfer</span>
                  @if($r->refund_method === 'manual_transfer' && $r->manual_proof_path)
                    <a class="h-8 px-4 inline-flex items-center justify-center rounded-xl bg-violet-600/10 border border-violet-500/20 text-violet-300 hover:text-white font-bold text-xs"
                      href="{{ asset('storage/' . $r->manual_proof_path) }}" target="_blank" rel="noopener">
                      Lihat Bukti
                    </a>
                  @else
                    <span class="text-xs text-slate-500 font-bold">—</span>
                  @endif
                </div>
              </td>

              {{-- Tombol Detail --}}
              <td class="block md:table-cell px-0 md:px-5 py-0 md:py-4 border-t border-dashed border-white/5 md:border-none pt-3 md:pt-4 text-right">
                <button type="button"
                  class="w-full md:w-auto inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-xs font-bold bg-white/5 border border-white/10 hover:border-violet-500/50 hover:bg-violet-600 hover:text-white transition-all shadow-sm"
                  onclick="openRefundDetail(this)" data-order-code="{{ $r->order?->code }}"
                  data-product="{{ $r->order?->product?->name }}" data-variant="{{ $r->order?->variant?->name }}"
                  data-target="{{ $r->order?->target }}" data-payment-method="{{ $r->order?->payment_method }}"
                  data-total="{{ (int) ($r->order?->total ?? 0) }}" data-status="{{ $r->order?->status }}"
                  data-payment-status="{{ $r->order?->payment_status }}" data-email="{{ $r->order?->email }}"
                  data-phone="{{ $r->order?->phone }}"
                  data-refund-method="{{ $r->refund_method === 'wallet' ? 'Saldo Dompet' : 'Transfer Manual' }}"
                  data-refund-amount="{{ (int) $r->amount }}" data-refund-time="{{ $r->created_at->format('d M Y H:i') }}"
                  data-refund-note="{{ $r->note }}" data-admin-name="{{ $r->admin?->name }}"
                  data-admin-email="{{ $r->admin?->email }}" data-target-user="{{ $r->targetUser?->name }}"
                  data-target-user-email="{{ $r->targetUser?->email }}"
                  data-proof-url="{{ $r->manual_proof_path ? asset('storage/' . $r->manual_proof_path) : '' }}">
                  Detail Refund
                </button>
              </td>

            </tr>
          @empty
            <tr class="flex flex-col md:table-row">
              <td colspan="9" class="px-5 py-10 text-center text-xs font-bold uppercase tracking-wider text-slate-500">
                Belum ada transaksi refund saat ini.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination links wrapper --}}
    <div class="p-4 border-t border-white/5 flex justify-center sm:justify-end">
      {{ $refunds->links() }}
    </div>
  </section>

  {{-- Modal Detail Refund Redesign --}}
  <div id="refundModal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/70 backdrop-blur-md p-4 transition-all duration-300">
    <div class="relative w-full max-w-3xl popup-glass rounded-[2rem] flex flex-col max-h-[85vh] md:max-h-[80vh] overflow-hidden animate-in fade-in zoom-in-95 duration-200">
      
      {{-- Sticky Header --}}
      <div class="p-6 pb-4 border-b border-white/5 shrink-0 pr-24 flex items-start justify-between gap-4">
        <div>
          <div class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-violet-600/10 border border-violet-500/20 text-[9px] font-extrabold uppercase tracking-widest text-violet-300">
            💸 Refund Info
          </div>
          <h3 class="text-xl font-extrabold text-white tracking-tight mt-1.5">Detail Refund</h3>
          <p id="modalSub" class="text-[10px] text-slate-400 font-bold uppercase tracking-wide mt-1">—</p>
        </div>

        <div class="absolute top-5 right-5 flex items-center gap-2 z-10">
          <button type="button" onclick="copyRefundDetail()"
            class="h-8 px-3 rounded-lg border border-white/10 bg-white/5 hover:border-violet-500/50 hover:bg-violet-600 hover:text-white text-xs font-bold transition-all">
            Salin Bukti
          </button>
          <button type="button" onclick="closeRefundDetail()"
            class="size-8 rounded-lg bg-white/5 border border-white/10 hover:border-rose-500/50 hover:bg-rose-600/5 flex items-center justify-center text-slate-400 hover:text-white transition-all">
            ✕
          </button>
        </div>
      </div>

      {{-- Scrollable content body --}}
      <div class="flex-1 overflow-y-auto p-6 space-y-5 no-scrollbar">
        <div id="modalBody" class="space-y-4 text-xs font-semibold text-slate-300">
          {{-- loaded dynamic content --}}
        </div>

        {{-- Copy block --}}
        <div class="rounded-2xl border border-white/5 bg-black/30 p-4 space-y-2">
          <div class="text-[9px] font-extrabold uppercase tracking-wider text-violet-400">📋 Format Salinan Bukti (Siap Kirim)</div>
          <pre id="modalProofText" class="whitespace-pre-wrap text-xs text-white font-mono leading-relaxed bg-[#050810]/50 p-3 rounded-xl border border-white/5 max-h-40 overflow-y-auto no-scrollbar"></pre>
        </div>
      </div>

    </div>
  </div>

@endsection

@push('scripts')
<script>
  function formatRupiah(n) {
    return 'Rp ' + Number(n || 0).toLocaleString('id-ID');
  }

  function val(el, name) {
    return el.dataset[name] || '-';
  }

  function openRefundDetail(btn) {
    const modal = document.getElementById('refundModal');
    const body = document.getElementById('modalBody');
    const proofText = document.getElementById('modalProofText');
    const sub = document.getElementById('modalSub');

    const orderCode = val(btn, 'orderCode');

    sub.textContent =
      `Order ${orderCode} • ${val(btn, 'refundMethod')} • ${formatRupiah(val(btn, 'refundAmount'))}`;

    body.innerHTML = `
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      
      {{-- Card Order --}}
      <div class="bg-white/[0.02] border border-white/5 p-4 rounded-2xl space-y-2.5">
        <span class="text-[9px] font-extrabold uppercase tracking-widest text-slate-500 block">📦 Informasi Pesanan</span>
        <div class="space-y-1.5 text-[11px]">
          <div class="flex justify-between"><span class="text-slate-400">Kode</span><span class="text-white font-mono font-medium">${orderCode}</span></div>
          <div class="flex justify-between"><span class="text-slate-400">Produk</span><span class="text-white font-bold text-right max-w-[150px] truncate">${val(btn, 'product')}</span></div>
          <div class="flex justify-between"><span class="text-slate-400">Varian</span><span class="text-slate-300 font-semibold">${val(btn, 'variant')}</span></div>
          <div class="flex justify-between"><span class="text-slate-400">Target</span><span class="text-white font-mono">${val(btn, 'target')}</span></div>
          <div class="flex justify-between"><span class="text-slate-400">Metode Bayar</span><span class="text-slate-300">${val(btn, 'paymentMethod')}</span></div>
          <div class="flex justify-between pb-1.5 border-b border-white/[0.03]"><span class="text-slate-400">Total</span><span class="text-white font-extrabold">${formatRupiah(val(btn, 'total'))}</span></div>
          <div class="flex justify-between"><span class="text-slate-400">Status</span><span class="text-violet-400 font-bold uppercase">${val(btn, 'status')} / ${val(btn, 'paymentStatus')}</span></div>
        </div>
      </div>

      {{-- Card Customer --}}
      <div class="bg-white/[0.02] border border-white/5 p-4 rounded-2xl space-y-2.5 flex flex-col justify-between">
        <div class="space-y-2.5">
          <span class="text-[9px] font-extrabold uppercase tracking-widest text-slate-500 block">👤 Informasi Pelanggan</span>
          <div class="space-y-1.5 text-[11px]">
            <div class="flex justify-between"><span class="text-slate-400">Email</span><span class="text-white font-bold">${val(btn, 'email')}</span></div>
            <div class="flex justify-between"><span class="text-slate-400">No HP</span><span class="text-slate-300 font-semibold">${val(btn, 'phone')}</span></div>
          </div>
        </div>

        <div class="bg-white/[0.01] border border-white/5 p-3 rounded-xl space-y-1 mt-3">
          <span class="text-[8px] font-extrabold uppercase tracking-wider text-slate-500 block">💬 Waktu Refund</span>
          <span class="text-slate-300 text-[11px] font-bold block">${val(btn, 'refundTime')}</span>
        </div>
      </div>

    </div>

    {{-- Detail Refund Summary Card --}}
    <div class="bg-white/[0.02] border border-white/5 p-4 rounded-2xl space-y-3">
      <span class="text-[9px] font-extrabold uppercase tracking-widest text-slate-500 block">💰 Rincian Refund</span>
      
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-[11px]">
        <div class="space-y-1.5">
          <div class="flex justify-between"><span class="text-slate-400">Metode Refund</span><span class="text-white font-bold">${val(btn, 'refundMethod')}</span></div>
          <div class="flex justify-between"><span class="text-slate-400">Target Wallet</span><span class="text-white font-bold">${val(btn, 'targetUser')} (${val(btn, 'targetUserEmail')})</span></div>
        </div>
        <div class="space-y-1.5">
          <div class="flex justify-between"><span class="text-slate-400">Jumlah Dana</span><span class="text-violet-300 font-extrabold text-sm">${formatRupiah(val(btn, 'refundAmount'))}</span></div>
          <div class="flex justify-between"><span class="text-slate-400">Admin</span><span class="text-white font-bold">${val(btn, 'adminName')}</span></div>
        </div>
      </div>

      <div class="pt-2.5 border-t border-white/[0.03] space-y-1">
        <span class="text-[8px] font-extrabold uppercase tracking-wider text-slate-500 block">Catatan Admin</span>
        <p class="text-slate-300 text-[11px] font-semibold italic">"${val(btn, 'refundNote')}"</p>
      </div>

      ${val(btn, 'proofUrl') && val(btn, 'proofUrl') !== '-'
      ? `<div class="pt-2"><a href="${val(btn, 'proofUrl')}" target="_blank" class="h-9 px-4 inline-flex items-center rounded-xl bg-violet-600 hover:bg-violet-500 text-xs font-bold text-white transition-all shadow-sm">Lihat Bukti Transfer Manual</a></div>`
      : ''
      }
    </div>
  `;

    proofText.textContent =
      `BUKTI REFUND MAITRI
Kode Pesanan: ${orderCode}
Produk: ${val(btn, 'product')} - ${val(btn, 'variant')}
Target: ${val(btn, 'target')}
Metode Bayar: ${val(btn, 'paymentMethod')}
Total: ${formatRupiah(val(btn, 'total'))}
Metode Refund: ${val(btn, 'refundMethod')}
Jumlah Refund: ${formatRupiah(val(btn, 'refundAmount'))}
Target Refund: ${val(btn, 'targetUser')} (${val(btn, 'targetUserEmail')})
Admin: ${val(btn, 'adminName')}
Catatan: ${val(btn, 'refundNote')}`;

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
  }

  function closeRefundDetail() {
    const modal = document.getElementById('refundModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
  }

  function copyRefundDetail() {
    const txt = document.getElementById('modalProofText');
    if (!txt) return;
    navigator.clipboard.writeText(txt.textContent).then(() => {
      alert('Teks format bukti berhasil disalin ke clipboard!');
    }).catch(e => console.error(e));
  }
</script>
@endpush