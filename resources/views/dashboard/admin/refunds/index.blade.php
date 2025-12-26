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
                  onclick="openRefundDetail(this)" data-refund='@json([
                    "refund" => [
                      "id" => $r->id,
                      "created_at" => $r->created_at->format("d M Y H:i"),
                      "method" => $r->refund_method === "wallet" ? "Saldo Maitri" : "Transfer Manual",
                      "amount" => (int) $r->amount,
                      "note" => $r->note,
                      "proof_url" => ($r->refund_method === "manual_transfer" && $r->manual_proof_path)
                        ? asset("storage/" . $r->manual_proof_path)
                        : null,
                    ],
                    "order" => [
                      "code" => $r->order?->code,
                      "status" => $r->order?->status,
                      "payment_status" => $r->order?->payment_status,
                      "payment_method" => $r->order?->payment_method,
                      "total" => (int) ($r->order?->total ?? 0),
                      "target" => $r->order?->target,
                      "email" => $r->order?->email,
                      "phone" => $r->order?->phone,
                      "product" => $r->order?->product?->name,
                      "variant" => $r->order?->variant?->name,
                      "paid_at" => optional($r->order?->paid_at)->format("d M Y H:i"),
                      "failed_at" => optional($r->order?->failed_at)->format("d M Y H:i"),
                      "refunded_at" => optional($r->order?->refunded_at)->format("d M Y H:i"),
                      "refund_amount" => (int) ($r->order?->refund_amount ?? 0),
                      "refund_reason" => $r->order?->refund_reason,
                    ],
                    "admin" => [
                      "name" => $r->admin?->name,
                      "email" => $r->admin?->email,
                    ],
                    "target_user" => [
                      "name" => $r->targetUser?->name,
                      "email" => $r->targetUser?->email,
                    ],
                  ]) }}'>
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

  <script>
    let __lastRefundPayload = null;

    function formatRupiah(n) {
      try { return 'Rp ' + Number(n || 0).toLocaleString('id-ID'); }
      catch (e) { return 'Rp ' + (n || 0); }
    }

    function safe(v) { return (v === null || v === undefined || v === '') ? '-' : v; }

    function openRefundDetail(btn) {
      const raw = btn.getAttribute('data-refund');
      if (!raw) return;

      const payload = JSON.parse(raw);
      __lastRefundPayload = payload;

      const r = payload.refund || {};
      const o = payload.order || {};
      const a = payload.admin || {};
      const t = payload.target_user || {};

      // header sub
      document.getElementById('modalSub').textContent =
        `Order ${safe(o.code)} • ${safe(r.method)} • ${formatRupiah(r.amount)} • ${safe(r.created_at)}`;

      // body html
      const body = `
        <div class="grid md:grid-cols-2 gap-3">
          <div class="rounded-xl border border-slate-800/70 bg-slate-950/30 p-3">
            <div class="text-xs text-slate-400 mb-2">Detail Order</div>
            <div class="space-y-1 text-sm">
              <div><span class="text-slate-400">Kode:</span> <span class="font-mono text-slate-100">${safe(o.code)}</span></div>
              <div><span class="text-slate-400">Produk:</span> <span class="text-slate-100">${safe(o.product)} - ${safe(o.variant)}</span></div>
              <div><span class="text-slate-400">Target:</span> <span class="font-mono text-slate-100">${safe(o.target)}</span></div>
              <div><span class="text-slate-400">Metode Bayar:</span> <span class="text-slate-100">${safe(o.payment_method)}</span></div>
              <div><span class="text-slate-400">Total:</span> <span class="text-slate-100">${formatRupiah(o.total)}</span></div>
              <div><span class="text-slate-400">Status:</span> <span class="text-slate-100">${safe(o.status)} / ${safe(o.payment_status)}</span></div>
            </div>
          </div>

          <div class="rounded-xl border border-slate-800/70 bg-slate-950/30 p-3">
            <div class="text-xs text-slate-400 mb-2">Data Pembeli</div>
            <div class="space-y-1 text-sm">
              <div><span class="text-slate-400">Email:</span> <span class="text-slate-100">${safe(o.email)}</span></div>
              <div><span class="text-slate-400">No HP:</span> <span class="text-slate-100">${safe(o.phone)}</span></div>
            </div>

            <div class="text-xs text-slate-400 mt-3 mb-2">Timeline</div>
            <div class="space-y-1 text-sm">
              <div><span class="text-slate-400">Paid at:</span> <span class="text-slate-100">${safe(o.paid_at)}</span></div>
              <div><span class="text-slate-400">Failed at:</span> <span class="text-slate-100">${safe(o.failed_at)}</span></div>
              <div><span class="text-slate-400">Refunded at:</span> <span class="text-slate-100">${safe(o.refunded_at)}</span></div>
            </div>
          </div>
        </div>

        <div class="rounded-xl border border-slate-800/70 bg-slate-950/30 p-3">
          <div class="text-xs text-slate-400 mb-2">Detail Refund</div>
          <div class="grid md:grid-cols-2 gap-2 text-sm">
            <div><span class="text-slate-400">Metode Refund:</span> <span class="text-slate-100">${safe(r.method)}</span></div>
            <div><span class="text-slate-400">Jumlah Refund:</span> <span class="text-slate-100">${formatRupiah(r.amount)}</span></div>
            <div><span class="text-slate-400">Admin:</span> <span class="text-slate-100">${safe(a.name)} (${safe(a.email)})</span></div>
            <div><span class="text-slate-400">Target (wallet):</span> <span class="text-slate-100">${safe(t.name)} ${t.email ? '(' + t.email + ')' : ''}</span></div>
            <div class="md:col-span-2"><span class="text-slate-400">Alasan:</span> <span class="text-slate-100">${safe(o.refund_reason)}</span></div>
            <div class="md:col-span-2"><span class="text-slate-400">Catatan:</span> <span class="text-slate-100">${safe(r.note)}</span></div>
            <div class="md:col-span-2">
              <span class="text-slate-400">Bukti:</span>
              ${r.proof_url ? `<a class="text-violet-300 underline" href="${r.proof_url}" target="_blank" rel="noopener">Lihat bukti</a>` : `<span class="text-slate-500">—</span>`}
            </div>
          </div>
        </div>
      `;
      document.getElementById('modalBody').innerHTML = body;

      // proof text for screenshot/copy
      const text =
        `BUKTI REFUND — MAITRI
  Tanggal Refund: ${safe(r.created_at)}
  Kode Pesanan: ${safe(o.code)}
  Produk: ${safe(o.product)} - ${safe(o.variant)}
  Target: ${safe(o.target)}
  Metode Bayar: ${safe(o.payment_method)}
  Email Pembeli: ${safe(o.email)}
  No HP Pembeli: ${safe(o.phone)}
  Total Bayar: ${formatRupiah(o.total)}
  Status Awal: FAILED
  Metode Refund: ${safe(r.method)}
  Jumlah Refund: ${formatRupiah(r.amount)}
  Target Refund (jika wallet): ${safe(t.name)} ${t.email ? '(' + t.email + ')' : ''}
  Admin Proses: ${safe(a.name)} (${safe(a.email)})
  Catatan: ${safe(r.note)}
  Bukti Transfer: ${r.proof_url ? r.proof_url : '-'}

  Keterangan: Refund sudah diproses oleh admin.`;
      document.getElementById('modalProofText').textContent = text;

      // show modal
      document.getElementById('refundModal').classList.remove('hidden');
      document.body.style.overflow = 'hidden';
    }

    function closeRefundDetail() {
      document.getElementById('refundModal').classList.add('hidden');
      document.body.style.overflow = '';
    }

    async function copyRefundDetail() {
      const pre = document.getElementById('modalProofText');
      const text = pre ? pre.textContent : '';
      if (!text) return;

      try {
        await navigator.clipboard.writeText(text);
        // feedback kecil
        const sub = document.getElementById('modalSub');
        const old = sub.textContent;
        sub.textContent = old + ' • (Copied)';
        setTimeout(() => { sub.textContent = old; }, 900);
      } catch (e) {
        // fallback
        const ta = document.createElement('textarea');
        ta.value = text;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
      }
    }

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeRefundDetail();
    });
  </script>

@endsection