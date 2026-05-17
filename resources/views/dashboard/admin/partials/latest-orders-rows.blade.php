{{-- resources/views/dashboard/admin/partials/latest-orders-rows.blade.php --}}

@forelse($orders as $row)
    @php
        if ($row['source'] === 'marketplace') {
            $label = match ($row['status']) {
                'not_paid' => 'NOT PAID',
                'paid_received' => 'RECEIVED',
                'paid_processing' => 'PROCESSING',
                'paid_rejected' => 'REJECTED',
                'paid_finished' => 'COMPLETED',
                default => strtoupper($row['status']),
            };

            $badgeClass = match ($row['status']) {
                'paid_finished' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20 shadow-[0_0_10px_rgba(16,185,129,0.1)]',
                'paid_rejected' => 'bg-rose-500/10 text-rose-400 border-rose-500/20 shadow-[0_0_10px_rgba(244,63,94,0.1)]',
                'not_paid' => 'bg-amber-500/10 text-amber-400 border-amber-500/20 shadow-[0_0_10px_rgba(245,158,11,0.1)]',
                default => 'bg-violet-500/10 text-violet-400 border-violet-500/20 shadow-[0_0_10px_rgba(139,92,246,0.1)]',
            };
        } else {
            $label = match ($row['status']) {
                'success' => 'SUCCESS',
                'failed' => 'FAILED',
                'processing' => 'PROCESSING',
                'waiting_payment' => 'WAITING PAYMENT',
                default => strtoupper($row['status']),
            };

            $badgeClass = match ($row['status']) {
                'success' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20 shadow-[0_0_10px_rgba(16,185,129,0.1)]',
                'failed' => 'bg-rose-500/10 text-rose-400 border-rose-500/20 shadow-[0_0_10px_rgba(244,63,94,0.1)]',
                'waiting_payment' => 'bg-amber-500/10 text-amber-400 border-amber-500/20 shadow-[0_0_10px_rgba(245,158,11,0.1)]',
                default => 'bg-violet-500/10 text-violet-400 border-violet-500/20 shadow-[0_0_10px_rgba(139,92,246,0.1)]',
            };
        }

        $createdAt = \Illuminate\Support\Carbon::parse($row['created_at']);
    @endphp

    <tr class="flex flex-col md:table-row border-b border-white/[0.03] hover:bg-white/[0.015] transition-all duration-300 p-5 md:p-0 gap-3.5 md:gap-0 bg-white/[0.01] md:bg-transparent rounded-2xl md:rounded-none mb-4 md:mb-0 shadow-lg md:shadow-none border border-white/5 md:border-none">
        
        {{-- Order ID --}}
        <td class="block md:table-cell px-0 md:px-5 py-0 md:py-4">
            <div class="flex items-center justify-between md:block">
                <span class="md:hidden text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Order ID</span>
                <span class="font-mono text-xs text-violet-300 font-bold bg-violet-600/10 border border-violet-500/15 px-2.5 py-1 rounded-lg md:bg-transparent md:border-none md:p-0">
                    {{ $row['code'] }}
                </span>
            </div>
        </td>

        {{-- Produk & varian --}}
        <td class="block md:table-cell px-0 md:px-5 py-0 md:py-4 border-t border-dashed border-white/5 md:border-none pt-3 md:pt-4">
            <div class="md:hidden text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">Info Produk</div>
            <div class="text-white font-bold text-sm tracking-tight">
                {{ $row['product'] }}
            </div>
            <div class="text-[11px] text-slate-400 mt-0.5">
                Varian: <span class="text-slate-300 font-semibold">{{ $row['variant'] }}</span>
            </div>
            <div class="mt-2 flex items-center">
              <span class="text-[8px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded bg-white/5 border border-white/5 text-slate-400">
                  {{ $row['source'] === 'marketplace' ? 'Marketplace' : 'Digiflazz' }}
              </span>
            </div>
        </td>

        {{-- User / email --}}
        <td class="block md:table-cell px-0 md:px-5 py-0 md:py-4 border-t border-dashed border-white/5 md:border-none pt-3 md:pt-4">
            <div class="flex items-center justify-between md:block">
                <span class="md:hidden text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Customer</span>
                <span class="text-xs text-slate-300 font-semibold">
                    {{ $row['customer'] ?? '-' }}
                </span>
            </div>
        </td>

        {{-- Total --}}
        <td class="block md:table-cell px-0 md:px-5 py-0 md:py-4 border-t border-dashed border-white/5 md:border-none pt-3 md:pt-4">
            <div class="flex items-center justify-between md:block">
                <span class="md:hidden text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Total Tagihan</span>
                <span class="text-violet-300 font-extrabold text-sm md:text-white">
                    Rp {{ number_format($row['total'] ?? 0, 0, ',', '.') }}
                </span>
            </div>
        </td>

        {{-- Status --}}
        <td class="block md:table-cell px-0 md:px-5 py-0 md:py-4 border-t border-dashed border-white/5 md:border-none pt-3 md:pt-4">
            <div class="flex items-center justify-between md:block">
                <span class="md:hidden text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Status</span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[9px] font-extrabold uppercase tracking-wider border {{ $badgeClass }}">
                    {{ $label }}
                </span>
            </div>
        </td>

        {{-- Waktu --}}
        <td class="block md:table-cell px-0 md:px-5 py-0 md:py-4 border-t border-dashed border-white/5 md:border-none pt-3 md:pt-4">
            <div class="flex items-center justify-between md:block">
                <span class="md:hidden text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Waktu</span>
                <span class="text-xs text-slate-400 font-semibold">
                    {{ $createdAt->diffForHumans() }}
                </span>
            </div>
        </td>

        {{-- Tombol Detail --}}
        <td class="block md:table-cell px-0 md:px-5 py-0 md:py-4 border-t border-dashed border-white/5 md:border-none pt-3 md:pt-4 text-right">
            <button type="button"
                class="w-full md:w-auto inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-xs font-bold bg-white/5 border border-white/10 hover:border-violet-500/50 hover:bg-violet-600 hover:text-white transition-all shadow-sm"
                data-order-detail-btn="1" data-order-code="{{ $row['code'] }}">
                Detail Transaksi
            </button>
        </td>
    </tr>
@empty
    <tr class="flex flex-col md:table-row">
        <td colspan="7" class="px-5 py-8 text-center text-xs font-bold uppercase tracking-wider text-slate-500">
            Belum ada transaksi saat ini.
        </td>
    </tr>
@endforelse