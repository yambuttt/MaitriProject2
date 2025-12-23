<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceOrderPayment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\MarketplaceOrderPaidMail;
use App\Services\MarketplaceOrderService;


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
            $seconds = (int) config('services.paydisini.valid_time', 1800);
            $expiresAt = now()->addSeconds($seconds);
        }

        return view('marketplace.payment', [
            'payment' => $payment,
            'data' => $data,
            'expiresAt' => $expiresAt,
        ]);
    }

    /**
     * AJAX: cek status pembayaran ke Paydisini.
     * Route: GET /marketplace/payment/{payment}/status
     */
    public function checkPaymentStatus(MarketplaceOrderPayment $payment): JsonResponse
    {
        $payment->refresh();
        $order = $payment->order()->first();

        $isFinal = in_array($payment->status, ['paid', 'canceled', 'expired'], true);

        return response()->json([
            'ok' => true,
            'status' => $payment->status,
            'order_status' => $order?->status,
            'redirect_url' => ($payment->status === 'paid' && $order)
                ? route('marketplace.invoice.show', $order)
                : null,
            'final' => $isFinal,
        ]);
    }

}
