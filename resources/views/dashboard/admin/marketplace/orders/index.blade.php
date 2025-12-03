@extends('layouts.admin')
@section('title', 'Marketplace Orders — Admin')

@section('content')
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div>
            <h1 class="text-2xl md:text-3xl font-semibold">Marketplace Orders</h1>
            <p class="text-slate-400 mt-1">Pesanan produk manual seperti akun Canva, dll.</p>
        </div>
        <div class="text-sm text-slate-400">
            Total: <span class="font-medium text-slate-200">{{ $orders->total() }}</span> pesanan
        </div>
    </div>

    {{-- Filter bar --}}
    <form method="get" class="mt-4 grid sm:grid-cols-2 lg:grid-cols-[minmax(0,2fr)_180px_140px] gap-3">
        <div class="sm:col-span-2 lg:col-span-1">
            <div class="relative">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari invoice, email, atau no HP..."
                    class="w-full h-11 rounded-xl bg-[#020617] border border-slate-800/80 px-3 ps-9 text-sm text-slate-100 placeholder:text-slate-500 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500/60">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-slate-500" viewBox="0 0 24 24" fill="none">
                    <path d="M21 21l-4.3-4.3M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" stroke="currentColor"
                        stroke-width="1.5" />
                </svg>
            </div>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-400 mb-1">Status</label>
            <select name="status"
                class="w-full h-11 rounded-xl bg-[#020617] border border-slate-800/80 px-3 text-sm text-slate-100 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500/60">
                <option value="">Semua status</option>
                <option value="not_paid" @selected(request('status') === 'not_paid')>Not paid</option>
                <option value="paid_received" @selected(request('status') === 'paid_received')>Paid & diterima</option>
                <option value="paid_processing" @selected(request('status') === 'paid_processing')>Paid & diproses</option>
                <option value="paid_finished" @selected(request('status') === 'paid_finished')>Paid & selesai</option>
                <option value="paid_rejected" @selected(request('status') === 'paid_rejected')>Paid & ditolak</option>
            </select>
        </div>

        <div class="flex items-end">
            <button type="submit" class="w-full h-11 rounded-xl bg-violet-600 hover:bg-violet-500 text-sm font-medium">
                Filter
            </button>
        </div>
    </form>

    <div class="mt-5 overflow-x-auto border border-slate-800/70 rounded-2xl bg-[#0E1524]">
        <table class="min-w-full text-sm table-fixed">
            <thead class="text-xs uppercase text-slate-400 border-b border-slate-800/70">
                <tr class="[&>th]:px-3 [&>th]:py-2.5">
                    <th class="text-left">Invoice</th>
                    <th class="text-left">Tanggal</th>
                    <th class="text-left">Produk</th>
                    <th class="text-left">Pelanggan</th>
                    <th class="text-left">Metode</th>
                    <th class="text-left">Status</th>
                    <th class="text-right pr-4">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-800/70">
                @forelse($orders as $order)
                    @php
                        $label = match ($order->status) {
                            'not_paid' => 'NOT PAID',
                            'paid_received' => 'PAID & PESANAN DITERIMA',
                            'paid_processing' => 'PAID & PESANAN DIPROSES',
                            'paid_rejected' => 'PAID & PESANAN DITOLAK',
                            'paid_finished' => 'PAID & PESANAN SELESAI',
                            default => strtoupper($order->status),
                        };
                    @endphp
                    <tr class="[&>td]:px-3 [&>td]:py-2.5">
                        <td class="font-mono text-xs text-slate-200">
                            {{ $order->invoice_number }}
                        </td>
                        <td class="text-xs text-slate-400">
                            {{ $order->created_at?->format('d M Y H:i') }}
                        </td>
                        <td>
                            <div class="text-slate-100">{{ $order->product->name ?? '-' }}</div>
                            <div class="text-xs text-slate-400">
                                Varian: {{ $order->variant->name ?? '-' }}
                            </div>
                        </td>
                        <td class="text-xs text-slate-300">
                            {{ $order->customer_email }}<br>
                            <span class="text-slate-500">{{ $order->customer_phone ?: '-' }}</span>
                        </td>
                        <td class="text-xs text-slate-300">
                            @php
                                $pm = $order->payment_method;
                                $pmLabel = match ($pm) {
                                    'wallet' => 'Saldo Maitri',
                                    'paydisini_qris' => 'QRIS',
                                    'paydisini_va_mandiri' => 'VA Mandiri',
                                    'paydisini_alfamart' => 'Alfamart',
                                    'paydisini_indomaret' => 'Indomaret',
                                    default => $pm,
                                };
                            @endphp
                            {{ $pmLabel ?? '-' }}
                        </td>
                        <td>
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-medium
                        @if($order->status === 'paid_finished') bg-emerald-500/20 text-emerald-300
                        @elseif($order->status === 'paid_rejected') bg-rose-500/20 text-rose-300
                        @elseif($order->status === 'not_paid') bg-amber-500/20 text-amber-300
                        @else bg-sky-500/20 text-sky-300 @endif">
                                {{ $label }}
                            </span>
                        </td>
                        <td class="text-right pr-4 w-[120px]">
                            <a href="{{ route('admin.marketplace.orders.show', $order) }}"
                                class="inline-flex items-center justify-center h-8 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs whitespace-nowrap">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-3 py-6 text-center text-slate-400 text-sm">
                            Belum ada pesanan marketplace.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
@endsection