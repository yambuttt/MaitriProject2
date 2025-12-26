@extends('layouts.admin')
@section('title', 'Refund — Admin')

@section('content')
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
    <div>
      <h1 class="text-2xl md:text-3xl font-semibold">Refund</h1>
      <p class="mt-1 text-slate-400 text-sm">Daftar refund yang sudah diproses dan oleh admin siapa.</p>
    </div>

    <a href="{{ route('admin.refunds.create') }}"
      class="inline-flex items-center justify-center rounded-2xl border border-violet-700/50 bg-violet-600/10 px-4 py-2 text-sm font-medium text-violet-200 hover:bg-violet-600/15">
      + Buat Refund
    </a>
  </div>

  <section class="mt-6 rounded-2xl border border-slate-800/70 bg-[#0E1524] overflow-hidden">
    <div class="p-4 border-b border-slate-800/70 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
      <h2 class="font-medium">Riwayat Refund</h2>

      <form method="GET" class="flex items-center gap-2">
        <input name="q" value="{{ $q ?? '' }}" placeholder="Cari kode order / admin / user..."
          class="bg-slate-900/60 border border-slate-700/70 rounded-xl px-3 py-2 text-xs text-slate-100 focus:outline-none focus:border-violet-400 w-64 max-w-full">
        <button class="text-sm text-violet-300 hover:text-violet-200">Cari</button>
      </form>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-[#0c1222] text-slate-400">
          <tr>
            <th class="text-left px-4 py-3">Waktu</th>
            <th class="text-left px-4 py-3">Kode</th>
            <th class="text-left px-4 py-3">Produk</th>
            <th class="text-left px-4 py-3">Metode</th>
            <th class="text-left px-4 py-3">Jumlah</th>
            <th class="text-left px-4 py-3">Target</th>
            <th class="text-left px-4 py-3">Admin</th>
            <th class="text-right px-4 py-3">Bukti</th>
            <th class="text-right px-4 py-3">Aksi</th>

          </tr>
        </thead>
        <tbody class="divide-y divide-slate-800/70">
          @forelse($refunds as $r)
            <tr class="hover:bg-white/5">
              <td class="px-4 py-3 text-xs text-slate-400 whitespace-nowrap">
                {{ $r->created_at->format('d M Y H:i') }}
              </td>
              <td class="px-4 py-3 font-mono whitespace-nowrap">{{ $r->order?->code }}</td>
              <td class="px-4 py-3">
                <div class="font-medium text-slate-100">{{ $r->order?->product?->name ?? '-' }}</div>
                <div class="text-xs text-slate-400">{{ $r->order?->variant?->name ?? '-' }}</div>
              </td>
              <td class="px-4 py-3">
                <span class="inline-flex px-2 py-1 rounded-lg text-[11px] border border-slate-700/70">
                  {{ $r->refund_method === 'wallet' ? 'Saldo Maitri' : 'Transfer Manual' }}
                </span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                Rp {{ number_format($r->amount, 0, ',', '.') }}
              </td>
              <td class="px-4 py-3">
                @if($r->refund_method === 'wallet')
                  <div class="text-slate-100">{{ $r->targetUser?->name ?? '-' }}</div>
                  <div class="text-xs text-slate-400">{{ $r->targetUser?->email ?? '-' }}</div>
                @else
                  <span class="text-xs text-slate-400">—</span>
                @endif
              </td>
              <td class="px-4 py-3">
                <div class="text-slate-100">{{ $r->admin?->name ?? '-' }}</div>
                <div class="text-xs text-slate-400">{{ $r->admin?->email ?? '-' }}</div>
              </td>
              <td class="px-4 py-3 text-right">
                @if($r->refund_method === 'manual_transfer' && $r->manual_proof_path)
                  <a class="text-violet-300 hover:text-violet-200 text-xs underline"
                    href="{{ asset('storage/' . $r->manual_proof_path) }}" target="_blank" rel="noopener">
                    Lihat
                  </a>
                @else
                  <span class="text-xs text-slate-500">—</span>
                @endif
              </td>
              <td class="px-4 py-3 text-right">
                <button type="button"
                  class="inline-flex items-center justify-center rounded-xl border border-slate-700/70 px-3 py-1.5 text-xs text-slate-200 hover:border-violet-400 hover:text-violet-200"
                  onclick="openRefundDetail(this)" data-order-code="{{ $r->order?->code }}"
                  data-product="{{ $r->order?->product?->name }}" data-variant="{{ $r->order?->variant?->name }}"
                  data-target="{{ $r->order?->target }}" data-payment-method="{{ $r->order?->payment_method }}"
                  data-total="{{ (int) ($r->order?->total ?? 0) }}" data-status="{{ $r->order?->status }}"
                  data-payment-status="{{ $r->order?->payment_status }}" data-email="{{ $r->order?->email }}"
                  data-phone="{{ $r->order?->phone }}"
                  data-refund-method="{{ $r->refund_method === 'wallet' ? 'Saldo Maitri' : 'Transfer Manual' }}"
                  data-refund-amount="{{ (int) $r->amount }}" data-refund-time="{{ $r->created_at->format('d M Y H:i') }}"
                  data-refund-note="{{ $r->note }}" data-admin-name="{{ $r->admin?->name }}"
                  data-admin-email="{{ $r->admin?->email }}" data-target-user="{{ $r->targetUser?->name }}"
                  data-target-user-email="{{ $r->targetUser?->email }}"
                  data-proof-url="{{ $r->manual_proof_path ? asset('storage/' . $r->manual_proof_path) : '' }}">
                  Detail
                </button>

              </td>

            </tr>
          @empty
            <tr>
              <td colspan="8" class="px-4 py-10 text-center text-sm text-slate-400">
                Belum ada refund.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="p-4 border-t border-slate-800/70">
      {{ $refunds->links() }}
    </div>
  </section>
  {{-- Modal Detail Refund --}}
  <div id="refundModal" class="hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/60" onclick="closeRefundDetail()"></div>

    <div class="relative mx-auto my-6 w-[92%] max-w-3xl">
      <div class="rounded-2xl border border-slate-800/70 bg-[#0B1120] shadow-xl overflow-hidden">
        <div class="flex items-start justify-between gap-3 p-4 border-b border-slate-800/70">
          <div>
            <h3 class="text-lg font-semibold text-slate-100">Detail Refund</h3>
            <p id="modalSub" class="text-xs text-slate-400 mt-0.5">—</p>
          </div>

          <div class="flex items-center gap-2">
            <button type="button" onclick="copyRefundDetail()"
              class="rounded-xl border border-slate-700/70 px-3 py-2 text-xs text-slate-200 hover:border-violet-400">
              Copy Text
            </button>
            <button type="button" onclick="closeRefundDetail()"
              class="rounded-xl border border-slate-700/70 px-3 py-2 text-xs text-slate-200 hover:border-rose-400">
              Tutup
            </button>
          </div>
        </div>

        <div class="p-4 md:p-5">
          <div id="modalBody" class="space-y-4 text-sm">
            {{-- diisi via JS --}}
          </div>

          <div class="mt-5 rounded-xl border border-slate-800/70 bg-slate-950/40 p-3">
            <div class="text-xs text-slate-400 mb-2">Format bukti (siap SS / copy)</div>
            <pre id="modalProofText" class="whitespace-pre-wrap text-xs text-slate-100 leading-relaxed"></pre>
          </div>
        </div>
      </div>
    </div>
  </div>



@endsection
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
      `Order ${orderCode} • ${val(btn, 'refundMethod')} • ${formatRupiah(val(btn, 'refundAmount'))} • ${val(btn, 'refundTime')}`;

    body.innerHTML = `
    <div class="grid md:grid-cols-2 gap-3">
      <div class="rounded-xl border border-slate-800/70 bg-slate-950/30 p-3">
        <div class="text-xs text-slate-400 mb-2">Detail Order</div>
        <div class="space-y-1">
          <div><b>Kode:</b> ${orderCode}</div>
          <div><b>Produk:</b> ${val(btn, 'product')} - ${val(btn, 'variant')}</div>
          <div><b>Target:</b> ${val(btn, 'target')}</div>
          <div><b>Metode Bayar:</b> ${val(btn, 'paymentMethod')}</div>
          <div><b>Total:</b> ${formatRupiah(val(btn, 'total'))}</div>
          <div><b>Status:</b> ${val(btn, 'status')} / ${val(btn, 'paymentStatus')}</div>
        </div>
      </div>

      <div class="rounded-xl border border-slate-800/70 bg-slate-950/30 p-3">
        <div class="text-xs text-slate-400 mb-2">Pembeli</div>
        <div class="space-y-1">
          <div><b>Email:</b> ${val(btn, 'email')}</div>
          <div><b>No HP:</b> ${val(btn, 'phone')}</div>
        </div>
      </div>
    </div>

    <div class="rounded-xl border border-slate-800/70 bg-slate-950/30 p-3">
      <div class="text-xs text-slate-400 mb-2">Detail Refund</div>
      <div class="space-y-1">
        <div><b>Metode Refund:</b> ${val(btn, 'refundMethod')}</div>
        <div><b>Jumlah:</b> ${formatRupiah(val(btn, 'refundAmount'))}</div>
        <div><b>Target Wallet:</b> ${val(btn, 'targetUser')} (${val(btn, 'targetUserEmail')})</div>
        <div><b>Admin:</b> ${val(btn, 'adminName')} (${val(btn, 'adminEmail')})</div>
        <div><b>Catatan:</b> ${val(btn, 'refundNote')}</div>
        ${val(btn, 'proofUrl') !== '-'
        ? `<div><a href="${val(btn, 'proofUrl')}" target="_blank" class="text-violet-300 underline">Lihat Bukti Transfer</a></div>`
        : ''
      }
      </div>
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
    document.body.style.overflow = 'hidden';
  }

  function closeRefundDetail() {
    document.getElementById('refundModal').classList.add('hidden');
    document.body.style.overflow = '';
  }
</script>