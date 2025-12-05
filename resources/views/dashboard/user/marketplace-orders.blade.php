@extends('layouts.dashboard')

@section('content')
    <h1 class="text-xl font-semibold mb-4">Riwayat Pesanan Marketplace</h1>

    <div class="space-y-3">
        @foreach($orders as $order)
            <div class="p-4 rounded-xl bg-slate-800/40 border border-slate-700 flex items-center justify-between">

                <div>
                    <div class="font-medium text-slate-100">{{ $order->invoice_number }}</div>
                    <div class="text-xs text-slate-400">
                        {{ $order->product->name }} – {{ $order->variant->name }}
                    </div>
                    <div class="text-xs mt-1">
                        Status:
                        <span class="text-slate-300">{{ strtoupper($order->status) }}</span>
                    </div>
                </div>

                @php
                    $payment = $order->payment;
                @endphp

                <div class="text-right text-sm">
                    <div class="font-semibold">
                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                    </div>

                    {{-- Jika masih menunggu pembayaran gateway --}}
                    @if($payment && $payment->status === 'pending')
                        <a href="{{ route('marketplace.payment.show', $payment) }}" class="text-xs text-emerald-400 underline">
                            Lanjutkan pembayaran
                        </a>
                    @endif

                    {{-- Link invoice marketplace --}}
                    <a href="{{ route('marketplace.invoice.show', $order) }}"
                        class="block text-xs text-slate-400 underline mt-1">
                        Lihat Invoice
                    </a>
                </div>

            </div>
        @endforeach
    </div>

    {{ $orders->links() }}
@endsection