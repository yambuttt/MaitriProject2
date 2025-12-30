<?php

namespace App\Http\Controllers;

use App\Models\AffiliateApplication;
use App\Models\AffiliateConversion;
use App\Models\AffiliateLevel;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\MarketplaceOrder;
class DashboardAffiliateController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $application = \App\Models\AffiliateApplication::where('user_id', $user->id)->first();

        // Kalau sudah affiliate: tampilkan statistik + conversions
        $level = null;
        if ($user->is_affiliate) {
            if ($user->affiliate_level_id) {
                $level = \App\Models\AffiliateLevel::where('id', $user->affiliate_level_id)->first();
            }
            $level ??= \App\Models\AffiliateLevel::where('is_active', true)->orderBy('id')->first();
        }

        $conversions = collect();
        $summary = [
            'total_points' => 0,
            'digiflazz_count' => 0,
            'marketplace_count' => 0,
        ];

        $affiliateLink = null;

        // ✅ tambahan: history redeem
        $redeems = collect();

        if ($user->is_affiliate) {
            $conversions = \App\Models\AffiliateConversion::where('affiliate_user_id', $user->id)
                ->latest()
                ->paginate(15);

            // summary
            $summary['total_points'] = (int) \App\Models\AffiliateConversion::where('affiliate_user_id', $user->id)->sum('points_awarded');
            $summary['digiflazz_count'] = (int) \App\Models\AffiliateConversion::where('affiliate_user_id', $user->id)->where('order_type', 'digiflazz')->count();
            $summary['marketplace_count'] = (int) \App\Models\AffiliateConversion::where('affiliate_user_id', $user->id)->where('order_type', 'marketplace')->count();

            // affiliate link
            if ($user->affiliate_code) {
                $affiliateLink = route('landing', ['ref' => $user->affiliate_code]);
            }

            /**
             * Decorate for UI:
             * - display_type: "Digital Goods" / "Marketplace"
             * - display_code: "MP-00147" / "MPM-00007"
             */
            $items = $conversions->getCollection();

            $digiflazzIds = $items->where('order_type', 'digiflazz')->pluck('order_id')->filter()->unique()->values();
            $marketplaceIds = $items->where('order_type', 'marketplace')->pluck('order_id')->filter()->unique()->values();

            $digiflazzMap = $digiflazzIds->isEmpty()
                ? collect()
                : \App\Models\Order::whereIn('id', $digiflazzIds)->pluck('code', 'id');

            // marketplace pakai invoice_number
            $marketplaceMap = $marketplaceIds->isEmpty()
                ? collect()
                : \App\Models\MarketplaceOrder::whereIn('id', $marketplaceIds)->pluck('invoice_number', 'id');

            $items = $items->map(function ($c) use ($digiflazzMap, $marketplaceMap) {
                $c->display_type = $c->order_type === 'digiflazz'
                    ? 'Digital Goods'
                    : 'Marketplace';

                if ($c->order_type === 'digiflazz') {
                    $c->display_code = $digiflazzMap[$c->order_id] ?? ('#' . $c->order_id);
                } else {
                    $c->display_code = $marketplaceMap[$c->order_id] ?? ('#' . $c->order_id);
                }

                return $c;
            });

            $conversions->setCollection($items);

            // ✅ history redeem (paginate sendiri, biar gak bentrok sama pagination conversions)
            $redeems = \App\Models\PointRedemption::where('user_id', $user->id)
                ->latest()
                ->paginate(10, ['*'], 'redeems_page');
        }

        return view('dashboard.user.affiliate', compact(
            'user',
            'application',
            'level',
            'conversions',
            'summary',
            'affiliateLink',
            'redeems'
        ));
    }


    public function apply(Request $request)
    {
        $user = $request->user();

        if ($user->is_affiliate) {
            return redirect()
                ->route('dashboard.affiliate')
                ->with('success', 'Akun kamu sudah menjadi affiliate.');
        }

        $existing = AffiliateApplication::where('user_id', $user->id)->first();
        if ($existing) {
            return redirect()
                ->route('dashboard.affiliate')
                ->with('success', 'Pengajuan affiliate kamu sudah tercatat. Silakan tunggu admin.');
        }

        AffiliateApplication::create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('dashboard.affiliate')
            ->with('success', 'Berhasil mengajukan affiliate. Menunggu persetujuan admin.');
    }
}
