@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
    <div class="max-w-4xl mx-auto py-8 space-y-6">

        {{-- Breadcrumb kecil --}}
        <div class="text-xs text-slate-400">
            <a href="{{ url('/catalog') }}" class="hover:text-slate-200">Katalog</a>
            <span class="px-1.5">/</span>
            <span class="text-slate-300">Invoice</span>
            <span class="px-1.5">/</span>
            <span class="font-mono text-slate-100">{{ $order->code }}</span>
        </div>

        {{-- Header --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-slate-100">Detail Transaksi</h1>
                <p class="text-sm text-slate-400">
                    Invoice untuk pembelian produk digital melalui MaitriProject.
                </p>
            </div>

            <div class="flex flex-col items-start sm:items-end gap-1">
                <div class="inline-flex items-center gap-2 rounded-full border border-slate-700 bg-slate-900/70 px-3 py-1">
                    <span class="text-xs text-slate-400">Kode</span>
                    <span class="font-mono text-sm text-slate-100">{{ $order->code }}</span>
                </div>

                @php
                    $status = strtoupper($order->status);
                    $statusColor = match ($order->status) {
                        'success' => 'bg-emerald-500/10 border-emerald-500/40 text-emerald-300',
                        'pending', 'processing' => 'bg-amber-500/10 border-amber-500/40 text-amber-300',
                        'failed' => 'bg-rose-500/10 border-rose-500/40 text-rose-300',
                        default => 'bg-slate-500/10 border-slate-500/40 text-slate-200',
                    };
                @endphp

                <span
                    class="inline-flex items-center rounded-full border px-3 py-0.5 text-xs font-medium {{ $statusColor }}">
                    {{ $status }}
                </span>
            </div>
        </div>

        {{-- Alert sukses kalau baru bayar --}}
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
                {{ session('success') }}
            </div>
        @endif
        @php
            $paymentLabel = match ($order->payment_method) {
                'wallet' => 'Saldo Maitri',
                'paydisini_qris' => 'QRIS PayDisini',
                'paydisini_va_mandiri' => 'VA Mandiri (PayDisini)',
                'paydisini_alfamart' => 'Alfamart (PayDisini)',
                'paydisini_indomaret' => 'Indomaret (PayDisini)',
                default => 'Metode lain',
            };
        @endphp
        {{-- Kartu utama --}}
        <div
            class="rounded-3xl border border-slate-800/80 bg-slate-900/80 p-5 sm:p-6 space-y-6 shadow-xl shadow-slate-950/40">

            {{-- Top section: produk + total --}}
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="space-y-1.5">
                    <p class="text-xs font-medium tracking-wide text-slate-400 uppercase">Produk</p>
                    <p class="text-sm font-semibold text-slate-100">{{ $order->product->name }}</p>
                    <p class="text-xs text-slate-400">{{ $order->variant->name }}</p>

                    <p class="mt-3 text-xs font-medium tracking-wide text-slate-400 uppercase">Nomor Tujuan</p>
                    <p class="text-sm font-mono text-slate-100">{{ $order->target }}</p>
                </div>

                <div class="space-y-1 text-right">
                    <p class="text-xs font-medium tracking-wide text-slate-400 uppercase">Total Bayar</p>
                    <p class="text-2xl font-semibold text-slate-50">
                        Rp {{ number_format($order->total, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-slate-400">
                        Dibayar dengan <span class="font-medium text-slate-200">{{ $paymentLabel }}</span>
                    </p>
                </div>
            </div>

            <div class="h-px bg-gradient-to-r from-slate-800 via-slate-700/60 to-slate-800"></div>

            {{-- Grid info --}}
            <div class="grid gap-6 md:grid-cols-2">
                {{-- Kolom kiri: detail transaksi --}}
                {{-- Kolom kiri: detail transaksi --}}
                <div class="space-y-3">
                    <h2 class="text-sm font-semibold text-slate-200">Ringkasan Transaksi</h2>
                    <dl class="space-y-2 text-sm text-slate-200">
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-400">Tanggal</dt>
                            <dd class="text-right">
                                {{ $order->created_at->format('d M Y, H:i') }}
                            </dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-400">Metode Pembayaran</dt>
                            <dd class="text-right">{{ $paymentLabel }}</dd>
                        </div>

                        @if($order->email)
                            <div class="flex justify-between gap-4">
                                <dt class="text-slate-400">Email Bukti</dt>
                                <dd class="text-right">{{ $order->email }}</dd>
                            </div>
                        @endif

                        @if($order->phone)
                            <div class="flex justify-between gap-4">
                                <dt class="text-slate-400">No. HP Pembeli</dt>
                                <dd class="text-right">{{ $order->phone }}</dd>
                            </div>
                        @endif

                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-400">Status</dt>
                            <dd class="text-right">{{ $status }}</dd>
                        </div>
                    </dl>
                </div>


                {{-- Kolom kanan: breakdown harga --}}
                <div class="space-y-3">
                    <h2 class="text-sm font-semibold text-slate-200">Rincian Pembayaran</h2>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-400">Harga Produk</dt>
                            <dd class="text-slate-100">
                                Rp {{ number_format($order->subtotal, 0, ',', '.') }}
                            </dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-400">Biaya Admin</dt>
                            <dd class="text-slate-100">
                                @if($order->admin_fee > 0)
                                    Rp {{ number_format($order->admin_fee, 0, ',', '.') }}
                                @else
                                    <span class="text-emerald-300">Gratis</span>
                                @endif
                            </dd>
                        </div>
                        <div class="flex justify-between gap-4 pt-2 border-t border-slate-800 mt-2">
                            <dt class="text-slate-400">Total Bayar</dt>
                            <dd class="font-semibold text-slate-50">
                                Rp {{ number_format($order->total, 0, ',', '.') }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Info catatan --}}
            <div class="rounded-2xl border border-slate-800 bg-slate-950/60 px-4 py-3 text-xs text-slate-400 flex gap-3">
                <div
                    class="mt-0.5 h-5 w-5 flex items-center justify-center rounded-full border border-slate-600 text-[10px] text-slate-200">
                    i
                </div>
                <p>
                    Simpan kode transaksi <span class="font-mono text-slate-200">{{ $order->code }}</span> sebagai bukti
                    pembayaran.
                    Jika terjadi kendala, kamu bisa menyertakan kode ini saat menghubungi bantuan MaitriProject.
                </p>
            </div>
        </div>

        {{-- Tombol kembali --}}
        <div class="flex justify-center">
            <a href="{{ url('/catalog') }}"
                class="inline-flex items-center rounded-2xl border border-slate-700 bg-slate-900/80 px-4 py-2 text-sm font-medium text-slate-100 hover:bg-slate-800">
                Kembali ke katalog
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const orderCode = @json($order->code);
            const initialStatus = @json($order->status);

            // Kalau status sudah bukan processing, gak usah polling
            if (initialStatus !== 'processing') return;

            const POLL_INTERVAL = 5000;     // 5 detik sekali
            const POLL_TIMEOUT = 5 * 60e3; // stop setelah 5 menit

            const checkStatus = async () => {
                try {
                    const res = await fetch(`/api/orders/${encodeURIComponent(orderCode)}/status`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    if (!res.ok) return;

                    const data = await res.json();

                    if (data.status && data.status !== 'processing') {
                        // Cara paling simple: reload halaman supaya semua data ikut update
                        window.location.reload();
                    }
                } catch (e) {
                    console.error('Error polling order status', e);
                }
            };

            const intervalId = setInterval(checkStatus, POLL_INTERVAL);

            // Safety: berhenti polling setelah beberapa menit
            setTimeout(() => clearInterval(intervalId), POLL_TIMEOUT);
        });
    </script>

@endsection