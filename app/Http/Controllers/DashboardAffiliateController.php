<?php

namespace App\Http\Controllers;

use App\Models\AffiliateApplication;
use App\Models\AffiliateConversion;
use App\Models\AffiliateLevel;
use Illuminate\Http\Request;

class DashboardAffiliateController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $application = AffiliateApplication::where('user_id', $user->id)->first();

        // Kalau sudah affiliate: tampilkan statistik + conversions
        $level = null;
        if ($user->is_affiliate) {
            if ($user->affiliate_level_id) {
                $level = AffiliateLevel::where('id', $user->affiliate_level_id)->first();
            }
            $level ??= AffiliateLevel::where('is_active', true)->orderBy('id')->first();
        }

        $conversions = collect();
        $summary = [
            'total_points' => 0,
            'digiflazz_count' => 0,
            'marketplace_count' => 0,
        ];

        if ($user->is_affiliate) {
            $conversions = AffiliateConversion::where('affiliate_user_id', $user->id)
                ->latest()
                ->paginate(15);

            $summary['total_points'] = (int) AffiliateConversion::where('affiliate_user_id', $user->id)->sum('points_awarded');
            $summary['digiflazz_count'] = (int) AffiliateConversion::where('affiliate_user_id', $user->id)->where('order_type', 'digiflazz')->count();
            $summary['marketplace_count'] = (int) AffiliateConversion::where('affiliate_user_id', $user->id)->where('order_type', 'marketplace')->count();
        }

        $affiliateLink = null;
        if ($user->is_affiliate && $user->affiliate_code) {
            // landing route kamu: Route::get('/', ...)->name('landing') :contentReference[oaicite:1]{index=1}
            $affiliateLink = route('landing', ['ref' => $user->affiliate_code]);
        }

        return view('dashboard.user.affiliate', compact(
            'user',
            'application',
            'level',
            'conversions',
            'summary',
            'affiliateLink'
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
