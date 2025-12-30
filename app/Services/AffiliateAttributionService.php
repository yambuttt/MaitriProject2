<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AffiliateAttributionService
{
    public function resolveAffiliateUserId(Request $request): ?int
    {
        $authUser = $request->user();

        // Jika yang beli adalah affiliate: kreditkan ke dirinya sendiri (sesuai requirement kamu)
        if ($authUser && $authUser->is_affiliate) {
            return $authUser->id;
        }

        // selain itu: ambil dari cookie referral
        $code = (string) $request->cookie('affiliate_ref', '');
        $code = trim($code);
        if ($code === '') {
            return null;
        }

        $affiliate = User::where('affiliate_code', $code)
            ->where('is_affiliate', true)
            ->first();

        return $affiliate?->id;
    }
}
