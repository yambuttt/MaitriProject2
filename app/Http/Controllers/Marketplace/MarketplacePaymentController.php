<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceOrderPayment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MarketplacePaymentController extends Controller
{
    /**
     * Halaman untuk menampilkan QR / VA / kode minimarket.
     * Route: GET /marketplace/payment/{payment}
     */
    public function showPaymentPage(MarketplaceOrderPayment $payment)
    {
        $payment->load('order.product', 'order.variant');

        // decode payload dari Paydisini (sama patternnya dengan invoices/payment.blade.php)
        $raw = $payment->response_payload;
        if (is_string($raw)) {
            $payload = $raw !== '' ? (json_decode($raw, true) ?: []) : [];
        } elseif (is_array($raw)) {
            $payload = $raw;
        } else {
            $payload = [];
        }

        $data = $payload['data'] ?? [];

        // hitung expire time
        if ($payment->expired_at) {
            $expiresAt = $payment->expired_at->copy();
        } else {
            $seconds   = (int) config('services.paydisini.valid_time', 1800);
            $expiresAt = now()->addSeconds($seconds);
        }

        return view('marketplace.payment', [
            'payment'   => $payment,
            'data'      => $data,
            'expiresAt' => $expiresAt,
        ]);
    }

    /**
     * AJAX: cek status pembayaran ke Paydisini.
     * Route: GET /marketplace/payment/{payment}/status
     */
    public function checkPaymentStatus(MarketplaceOrderPayment $payment): JsonResponse
    {
        $order = $payment->order;

        // kalau sudah final, nggak usah call API lagi
        if (in_array($payment->status, ['paid', 'canceled', 'expired'], true)) {
            return response()->json([
                'ok'           => true,
                'status'       => $payment->status,
                'order_status' => $order->status,
                'redirect_url' => $payment->status === 'paid'
                    ? route('marketplace.invoice.show', $order)
                    : null,
            ]);
        }

        $apiKey  = env('PAYDISINI_API_KEY');
        $baseUrl = rtrim(env('PAYDISINI_BASE_URL', 'https://api.paydisini.co.id/v1/'), '/');

        $uniqueCode = $payment->paydisini_unique_code;
        $signature  = md5($apiKey . $uniqueCode . 'StatusTransaction');

        $payload = [
            'key'         => $apiKey,
            'request'     => 'status',
            'unique_code' => $uniqueCode,
            'signature'   => $signature,
        ];

        $response = Http::asForm()->post($baseUrl . '/', $payload);

        if (!$response->successful()) {
            return response()->json([
                'ok'     => false,
                'status' => $payment->status,
                'message'=> 'Gagal menghubungi Paydisini (HTTP '.$response->status().')',
            ], 500);
        }

        $json = $response->json();

        if (!isset($json['success']) || $json['success'] !== true) {
            return response()->json([
                'ok'     => false,
                'status' => $payment->status,
                'message'=> $json['msg'] ?? 'Paydisini error',
            ], 500);
        }

        $data   = $json['data'] ?? [];
        $status = strtolower($data['status'] ?? '');

        $newStatus = match ($status) {
            'success'  => 'paid',
            'canceled' => 'canceled',
            default    => 'pending',
        };

        // update payment
        $payment->update([
            'status'           => $newStatus,
            'callback_payload' => $json,
            'paid_at'          => $newStatus === 'paid' ? now() : $payment->paid_at,
        ]);

        // kalau sukses → tandai order sudah dibayar (PAID & PESANAN DI TERIMA)
        if ($newStatus === 'paid' && $order->payment_status !== 'paid') {
            $order->update([
                'payment_status' => 'paid',
                'status'         => 'paid_received',
                'paid_at'        => now(),
            ]);
        }

        return response()->json([
            'ok'           => true,
            'status'       => $payment->status,
            'order_status' => $order->status,
            'redirect_url' => $payment->status === 'paid'
                ? route('marketplace.invoice.show', $order)
                : null,
        ]);
    }
}
