{{-- resources/views/dashboard/admin/partials/latest-orders-rows.blade.php --}}

@forelse($orders as $row)
    @php
        // mapping label + warna, beda antara marketplace & digiflazz
        if ($row['source'] === 'marketplace') {
            $label = match ($row['status']) {
                'not_paid' => 'NOT PAID',
                'paid_received' => 'PAID & PESANAN DITERIMA',
                'paid_processing' => 'PAID & PESANAN DIPROSES',
                'paid_rejected' => 'PAID & PESANAN DITOLAK',
                'paid_finished' => 'PAID & PESANAN SELESAI',
                default => strtoupper($row['status']),
            };

            $badgeClass = match ($row['status']) {
                'paid_finished' => 'bg-emerald-500/15 text-emerald-300 border-emerald-600/40',
                'paid_rejected' => 'bg-rose-500/15 text-rose-300 border-rose-600/40',
                'not_paid' => 'bg-amber-500/15 text-amber-300 border-amber-600/40',
                default => 'bg-sky-500/15 text-sky-300 border-sky-600/40',
            };
        } else {
            // order Digiflazz
            $label = match ($row['status']) {
                'success' => 'SUCCESS',
                'failed' => 'FAILED',
                'processing' => 'PROCESSING',
                'waiting_payment' => 'WAITING PAYMENT',
                default => strtoupper($row['status']),
            };

            $badgeClass = match ($row['status']) {
                'success' => 'bg-emerald-500/15 text-emerald-300 border-emerald-600/40',
                'failed' => 'bg-rose-500/15 text-rose-300 border-rose-600/40',
                'waiting_payment' => 'bg-amber-500/15 text-amber-300 border-amber-600/40',
                default => 'bg-sky-500/15 text-sky-300 border-sky-600/40',
            };
        }

        $createdAt = \Illuminate\Support\Carbon::parse($row['created_at']);
    @endphp

    <tr class="border-t border-slate-800/70">
        {{-- Order ID --}}
        <td class="px-4 py-3 font-mono text-xs text-slate-300">
            {{ $row['code'] }}
        </td>

        {{-- Produk & varian --}}
        <td class="px-4 py-3">
            <div class="text-slate-100">
                {{ $row['product'] }}
            </div>
            <div class="text-xs text-slate-400">
                Varian: {{ $row['variant'] }}
            </div>
            <div class="mt-1 text-[10px] uppercase text-slate-500">
                {{ $row['source'] === 'marketplace' ? 'Marketplace' : 'Produk Digiflazz' }}
            </div>
        </td>

        {{-- User / email --}}
        <td class="px-4 py-3 text-xs text-slate-300">
            {{ $row['customer'] ?? '-' }}
        </td>

        {{-- Total --}}
        <td class="px-4 py-3">
            Rp {{ number_format($row['total'] ?? 0, 0, ',', '.') }}
        </td>

        {{-- Status --}}
        <td class="px-4 py-3">
            <span class="inline-flex items-center px-2 py-1 rounded-lg text-[11px] font-medium border {{ $badgeClass }}">
                {{ $label }}
            </span>
        </td>

        {{-- Waktu --}}
        <td class="px-4 py-3 text-xs text-slate-400">
            {{ $createdAt->diffForHumans() }}
        </td>
        {{-- Waktu --}}
        <td class="px-4 py-3 text-xs text-slate-400">
            {{ $createdAt->diffForHumans() }}
        </td>

        {{-- Tombol Detail --}}
        <td class="px-4 py-3 text-right">
            <button type="button"
                class="inline-flex items-center px-3 py-1.5 rounded-lg text-[11px] border border-slate-700/70 text-slate-200 hover:border-violet-400 hover:text-violet-100"
                data-order-detail-btn="1" data-order-code="{{ $row['code'] }}">
                Detail
            </button>
        </td>
    </tr>

    </tr>
@empty
    <tr>
        <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-400">
            Belum ada transaksi.
        </td>
    </tr>
@endforelse