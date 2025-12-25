<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\OrderRefundService;

use App\Services\OrderNotificationService;
class DigiflazzWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Log dulu buat debugging (boleh dihapus nanti)
        Log::info('Digiflazz webhook masuk', [
            'headers' => $request->headers->all(),
            'body' => $request->getContent(),
        ]);

        // Payload utama sesuai docs Digiflazz
        $data = $request->input('data', []);

        if (!is_array($data) || empty($data['ref_id'])) {
            return response()->json(['message' => 'invalid payload'], 400);
        }

        $refId = $data['ref_id'];
        $status = strtolower($data['status'] ?? '');
        $rc = $data['rc'] ?? null;

        // Cari order berdasarkan provider_ref_id (ref_id Digiflazz)
        $order = Order::where('provider_ref_id', $refId)->first();


        if (!$order) {
            Log::warning('Digiflazz webhook: order tidak ditemukan', ['ref_id' => $refId]);
            // tetap balas 200 supaya Digiflazz tidak retry terus
            return response()->json(['message' => 'order not found'], 200);
        }

        // Mapping status Digiflazz -> status di sistem kita
        if ($rc === '00' || in_array($status, ['sukses', 'success'])) {
            $mappedStatus = 'success';
            $paidAt = now();
            $completedAt = now();
            $failedAt = null;
        } elseif (in_array($status, ['pending', 'process', 'processing'])) {
            $mappedStatus = 'processing';
            $paidAt = $order->paid_at;
            $completedAt = $order->completed_at;
            $failedAt = null;
        } else {
            $mappedStatus = 'failed';
            $paidAt = $order->paid_at;
            $completedAt = $order->completed_at;
            $failedAt = now();
        }


        $sn = $data['sn'] ?? $data['sn_customer'] ?? null;

        // Update kolom-kolom terkait Digiflazz + status internal
        $order->update([
            'status' => $mappedStatus,

            // kolom khusus Digiflazz
            'digiflazz_ref' => $data['ref_id'] ?? $order->digiflazz_ref,
            'digiflazz_status' => $data['status'] ?? null,
            'response_payload' => $data,

            // kolom generik provider
            'provider' => $order->provider ?: 'digiflazz',
            'provider_ref_id' => $data['ref_id'] ?? $order->provider_ref_id,
            'provider_status' => $data['status'] ?? $order->provider_status,
            'provider_message' => $data['message'] ?? $order->provider_message,
            'provider_rc' => $rc ?? $order->provider_rc,
            'provider_sn' => $sn ?? $order->provider_sn,
            'provider_price' => $data['sell_price'] ?? $data['price'] ?? $order->provider_price,
            'provider_raw' => $data,

            'paid_at' => $paidAt,
            'completed_at' => $completedAt,
            'failed_at' => $failedAt,
        ]);
        if ($mappedStatus === 'success') {
            app(OrderNotificationService::class)->notifySuccess($order);
        }
        if ($mappedStatus === 'failed') {
            app(\App\Services\OrderRefundService::class)->refundToWalletIfEligible(
                $order,
                'Digiflazz webhook failed'
            );
        }

        Log::info('Digiflazz webhook: order updated', [
            'code' => $order->code,
            'status' => $order->status,
            'digiflazz_rc' => $rc,
            'digiflazz_msg' => $data['message'] ?? null,
        ]);

        return response()->json(['message' => 'ok']);
    }
}
