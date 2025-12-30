<?php

namespace App\Http\Controllers;

use App\Models\AffiliateApplication;
use App\Models\AffiliateConversion;
use App\Models\AffiliateLevel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminAffiliateController extends Controller
{
    public function applications(Request $request)
    {
        $status = $request->query('status', 'pending'); // pending|approved|rejected
        if (!in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $status = 'pending';
        }

        $apps = AffiliateApplication::with('user')
            ->where('status', $status)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('dashboard.admin.affiliates.applications', compact('apps', 'status'));
    }


    public function approve(Request $request, AffiliateApplication $application)
    {
        $user = $application->user;

        // set default level (first active)
        $defaultLevel = AffiliateLevel::where('is_active', true)->orderBy('id')->first();

        // generate affiliate_code unik
        if (!$user->affiliate_code) {
            $user->affiliate_code = $this->generateUniqueAffiliateCode();
        }

        $user->is_affiliate = true;
        $user->affiliate_level_id = $user->affiliate_level_id ?? ($defaultLevel?->id);
        $user->save();

        $application->status = 'approved';
        $application->reviewed_at = now();
        $application->save();

        return back()->with('ok', 'Affiliate disetujui & kode berhasil dibuat.');
    }

    public function reject(Request $request, AffiliateApplication $application)
    {
        $request->validate([
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $application->status = 'rejected';
        $application->note = $request->note;
        $application->reviewed_at = now();
        $application->save();

        return back()->with('ok', 'Pengajuan affiliate ditolak.');
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $affiliates = User::query()
            ->where('is_affiliate', true)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($q2) use ($q) {
                    $q2->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('affiliate_code', 'like', "%{$q}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('dashboard.admin.affiliates.index', compact('affiliates', 'q'));
    }

    public function show(User $user)
    {
        abort_unless($user->is_affiliate, 404);

        $level = $user->affiliateLevel;

        $summary = [
            'total_points_ledger' => (int) AffiliateConversion::where('affiliate_user_id', $user->id)->sum('points_awarded'),
            'digiflazz_count' => (int) AffiliateConversion::where('affiliate_user_id', $user->id)->where('order_type', 'digiflazz')->count(),
            'marketplace_count' => (int) AffiliateConversion::where('affiliate_user_id', $user->id)->where('order_type', 'marketplace')->count(),
        ];

        $conversions = AffiliateConversion::where('affiliate_user_id', $user->id)
            ->latest()
            ->paginate(30);

        $affiliateLink = $user->affiliate_code ? route('landing', ['ref' => $user->affiliate_code]) : null;

        return view('dashboard.admin.affiliates.show', compact('user', 'level', 'summary', 'conversions', 'affiliateLink'));
    }

    private function generateUniqueAffiliateCode(): string
    {
        // format: MTR-XXXXXX
        do {
            $code = 'MTR-' . strtoupper(Str::random(6));
        } while (User::where('affiliate_code', $code)->exists());

        return $code;
    }
}
