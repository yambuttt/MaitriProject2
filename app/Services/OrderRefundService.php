<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderRefundService
{
    /**
     * Refund order ke Saldo Maitri (wallet) dengan aturan:
     * - wajib order sudah PAID (payment_status === 'paid')
     * - wajib ada user_id (login). Jika guest => skip
     * - idempotent: kalau sudah refunded_at, tidak refund lagi
     */
    public function refundToWalletIfEligible(Order $order, string $reason = 'Digiflazz failed'): bool
    {
        $order->refresh();

        // sudah refund? stop
        if (!is_null($order->refunded_at) || $order->status === 'refunded' || $order->payment_status === 'refunded') {
            return false;
        }

        // harus paid dulu
        if (($order->payment_status ?? null) !== 'paid') {
            return false;
        }

        // harus login (punya user)
        if (empty($order->user_id)) {
            // guest => jangan refund otomatis
            return false;
        }

        $refundAmount = (int) ($order->total ?? 0);
        if ($refundAmount <= 0) {
            return false;
        }

        return DB::transaction(function () use ($order, $refundAmount, $reason) {
            /** @var User|null $user */
            $user = $order->user()->lockForUpdate()->first();
            if (!$user) {
                return false;
            }

            // CREDIT ke saldo maitri pakai helper yang sudah ada
            // (helper ini juga bikin WalletTransaction) :contentReference[oaicite:5]{index=5}
            $user->incrementBalance(
                $refundAmount,
                'Refund order ' . $order->code . ' - ' . $reason
            );

            // update order jadi refunded
            $order->update([
                'status' => 'refunded',
                'payment_status' => 'refunded',
                'refunded_at' => now(),
                'refund_amount' => $refundAmount,
                'refund_reason' => $reason,
                'refunded_to_user_id' => $user->id,
                'profit' => 0,
            ]);

            // optional: update latest payment status juga biar rapi
            $latestPayment = $order->latestPayment()->first();
            if ($latestPayment) {
                $latestPayment->update([
                    'status' => 'refunded',
                ]);
            }

            Log::info('Order refunded to wallet', [
                'order_code' => $order->code,
                'order_id' => $order->id,
                'user_id' => $user->id,
                'amount' => $refundAmount,
                'reason' => $reason,
            ]);

            return true;
        });
    }
}
