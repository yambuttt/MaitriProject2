<?php

namespace App\Http\Controllers;

use App\Models\WalletTopup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\OrderPayment;
use App\Services\DigiflazzService;

use Illuminate\Support\Str;

class PaydisiniCallbackController extends Controller
{
    public function handle(Request $request, DigiflazzService $digiflazzService)
    {
        Log::info('Paydisini callback received', $request->all());

        $apiKey = env('PAYDISINI_API_KEY');

        $key = $request->input('key');
        $payId = $request->input('pay_id');
        $unique = $request->input('unique_code');
        $status = $request->input('status');   // "Success" / "Canceled"
        $signature = $request->input('signature');

        // 1. Validasi key
        if ($key !== $apiKey) {
            Log::warning('Paydisini callback invalid key');
            return response()->json(['success' => false, 'message' => 'Invalid key'], 400);
        }

        // 2. Validasi signature: md5(key . unique_code . 'CallbackStatus')
        $expectedSignature = md5($apiKey . $unique . 'CallbackStatus');
        if ($signature !== $expectedSignature) {
            Log::warning('Paydisini callback invalid signature', [
                'expected' => $expectedSignature,
                'got' => $signature,
            ]);
            return response()->json(['success' => false, 'message' => 'Invalid signature'], 400);
        }

        /**
         * CASE 1: TOPUP SALDO (unique_code diawali "TOPUP")
         */
        if (Str::startsWith($unique, 'TOPUP')) {
            $topup = WalletTopup::where('unique_code', $unique)->first();

            if (!$topup) {
                Log::warning('Paydisini callback: topup not found', ['unique_code' => $unique]);
                return response()->json(['success' => false, 'message' => 'Topup not found'], 404);
            }

            // Hindari proses ulang
            if (in_array($topup->status, ['success', 'canceled'], true)) {
                return response()->json(['success' => true]);
            }

            $topup->callback_payload = $request->all();

            if (strtolower($status) === 'success') {
                $user = $topup->user;

                // Helper di model User
                $user->incrementBalance($topup->amount, 'Topup via Paydisini (' . $topup->method . ')');

                $topup->status = 'success';
                $topup->paid_at = now();
                $topup->pay_id = $payId;
            } else {
                $topup->status = 'canceled';
                $topup->pay_id = $payId;
            }

            $topup->save();

            return response()->json(['success' => true]);
        }

        /**
         * CASE 2: PEMBAYARAN ORDER (unique_code diawali "ORDPAY")
         */
        if (Str::startsWith($unique, 'ORDPAY')) {
            $payment = OrderPayment::where('paydisini_unique_code', $unique)->first();

            if (!$payment) {
                Log::warning('Paydisini callback: order payment not found', ['unique_code' => $unique]);
                return response()->json(['success' => false, 'message' => 'Payment not found'], 404);
            }

            // Hindari double process
            if (in_array($payment->status, ['paid', 'canceled', 'expired'], true)) {
                return response()->json(['success' => true]);
            }

            $order = $payment->order;

            if (!$order) {
                Log::warning('Paydisini callback: order not found for payment', ['payment_id' => $payment->id]);
                return response()->json(['success' => false, 'message' => 'Order not found'], 404);
            }

            $payment->callback_payload = $request->all();
            $payment->paydisini_pay_id = $payId;

            if (strtolower($status) === 'success') {
                // tandai payment sudah dibayar
                $payment->status = 'paid';
                $payment->paid_at = now();
                $payment->save();

                // update order: payment sudah lunas
                $order->payment_status = 'paid';
                $order->save();

                // kalau order masih menunggu pembayaran, kirim ke Digiflazz
                if ($order->status === 'waiting_payment') {
                    // panggil logic yang sama dengan AJAX checkPaymentStatus
                    app(CheckoutController::class)->processOrderAfterPayment($order, $digiflazzService);
                }
            } else {
                // pembayaran gagal / dibatalkan
                $payment->status = 'canceled';
                $payment->save();

                $order->payment_status = 'canceled';
                $order->status = 'failed';
                $order->failed_at = now();
                $order->save();
            }

            return response()->json(['success' => true]);
        }

        // Kalau prefix-nya bukan TOPUP atau ORDPAY
        Log::warning('Paydisini callback: unknown unique_code prefix', ['unique_code' => $unique]);

        return response()->json(['success' => false, 'message' => 'Unknown unique_code'], 400);
    }

}
