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

    public function show(\App\Models\User $user)
    {
        abort_unless($user->is_affiliate, 404);

        // relasi ini sekarang sudah ada di User.php
        $user->load('affiliateLevel');

        // ✅ penting: kirim levels untuk dropdown
        $levels = \App\Models\AffiliateLevel::orderBy('id')->get();

        $summary = [
            'total_points_ledger' => (int) \App\Models\AffiliateConversion::where('affiliate_user_id', $user->id)->sum('points_awarded'),
            'digiflazz_count' => (int) \App\Models\AffiliateConversion::where('affiliate_user_id', $user->id)->where('order_type', 'digiflazz')->count(),
            'marketplace_count' => (int) \App\Models\AffiliateConversion::where('affiliate_user_id', $user->id)->where('order_type', 'marketplace')->count(),
        ];

        $conversions = \App\Models\AffiliateConversion::where('affiliate_user_id', $user->id)
            ->latest()
            ->paginate(30);

        $affiliateLink = $user->affiliate_code
            ? route('landing', ['ref' => $user->affiliate_code])
            : null;

        // ====== Decorate conversions (buat tampilan "Digital Goods" + kode transaksi) ======
        $items = $conversions->getCollection();

        $digiflazzIds = $items->where('order_type', 'digiflazz')->pluck('order_id')->filter()->unique()->values();
        $marketplaceIds = $items->where('order_type', 'marketplace')->pluck('order_id')->filter()->unique()->values();

        $digiflazzMap = $digiflazzIds->isEmpty()
            ? collect()
            : \App\Models\Order::whereIn('id', $digiflazzIds)->pluck('code', 'id');

        // marketplace kamu pakai kolom invoice_number
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

        return view('dashboard.admin.affiliates.show', compact(
            'user',
            'levels',       // ✅ ini yang bikin error hilang
            'summary',
            'conversions',
            'affiliateLink'
        ));
    }


    private function generateUniqueAffiliateCode(): string
    {
        // format: MTR-XXXXXX
        do {
            $code = 'MTR-' . strtoupper(Str::random(6));
        } while (User::where('affiliate_code', $code)->exists());

        return $code;
    }

    public function updateLevel(Request $request, User $user)
    {
        abort_unless($user->is_affiliate, 404);

        $data = $request->validate([
            'affiliate_level_id' => ['nullable', 'integer', 'exists:affiliate_levels,id'],
        ]);

        $user->affiliate_level_id = $data['affiliate_level_id'] ?? null;
        $user->save();

        return back()->with('ok', 'Level affiliate berhasil diubah.');
    }

}
