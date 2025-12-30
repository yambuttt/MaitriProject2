<?php

namespace App\Services;

use App\Models\AffiliateConversion;
use App\Models\AffiliateLevel;
use App\Models\Order;
use App\Models\MarketplaceOrder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AffiliateRewardService
{
    public function awardForDigiflazzSuccess(Order $order): void
    {
        if ($order->status !== 'success') return;
        if (!$order->affiliate_user_id) return;

        $this->award(
            affiliateUserId: $order->affiliate_user_id,
            buyerUserId: $order->user_id,
            orderType: 'digiflazz',
            orderId: $order->id
        );
    }

    public function awardForMarketplaceFinished(MarketplaceOrder $order): void
    {
        if ($order->status !== 'paid_finished') return;
        if (!$order->affiliate_user_id) return;

        $this->award(
            affiliateUserId: $order->affiliate_user_id,
            buyerUserId: $order->user_id,
            orderType: 'marketplace',
            orderId: $order->id
        );
    }

    private function award(int $affiliateUserId, ?int $buyerUserId, string $orderType, int $orderId): void
    {
        DB::transaction(function () use ($affiliateUserId, $buyerUserId, $orderType, $orderId) {
            // idempotent: kalau sudah pernah, stop
            $exists = AffiliateConversion::where('order_type', $orderType)
                ->where('order_id', $orderId)
                ->exists();
            if ($exists) return;

            $affiliate = User::lockForUpdate()->findOrFail($affiliateUserId);

            // Ambil level affiliate (fallback ke Default level id=1, atau ambil pertama yang active)
            $level = null;
            if ($affiliate->affiliate_level_id) {
                $level = AffiliateLevel::where('id', $affiliate->affiliate_level_id)->where('is_active', true)->first();
            }
            $level ??= AffiliateLevel::where('is_active', true)->orderBy('id')->first();

            $points = $orderType === 'digiflazz'
                ? (int) ($level->digiflazz_points ?? 50)
                : (int) ($level->marketplace_points ?? 2000);

            AffiliateConversion::create([
                'affiliate_user_id' => $affiliateUserId,
                'buyer_user_id' => $buyerUserId,
                'order_type' => $orderType,
                'order_id' => $orderId,
                'points_awarded' => $points,
                'awarded_at' => now(),
            ]);

            // tambah point affiliate
            $affiliate->maitri_points = (int) $affiliate->maitri_points + $points;
            $affiliate->save();
        });
    }
}
