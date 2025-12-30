<?php

namespace App\Http\Controllers;

use App\Models\PointRedemption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardPointRedeemController extends Controller
{
    public function store(\Illuminate\Http\Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        $data = $request->validate([
            'method' => ['required', 'in:wallet,cash'],
            'points' => ['required', 'integer', 'min:1'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $points = (int) $data['points'];
        $method = (string) $data['method'];
        $phone = $data['phone'] ?? null;

        // asumsi 1 point = 1 rupiah
        $amount = $points;

        if ($method === 'cash' && empty($phone)) {
            return back()->with('error', 'Nomor WhatsApp wajib diisi untuk redeem Uang Cash.');
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($user, $method, $points, $amount, $phone) {

            /** @var \App\Models\User $lockedUser */
            $lockedUser = \App\Models\User::query()
                ->whereKey($user->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ((int) $lockedUser->maitri_points < $points) {
                return back()->with('error', 'Point kamu tidak cukup.');
            }

            // 1) Redeem ke saldo maitri (instan)
            if ($method === 'wallet') {
                // potong point
                $lockedUser->maitri_points = (int) $lockedUser->maitri_points - $points;
                $lockedUser->save();

                // tambah saldo pakai helper project kamu (maitri_balance + wallet_transactions)
                $lockedUser->incrementBalance(
                    amount: $amount,
                    description: 'Redeem point ke Saldo Maitri'
                );

                // catat riwayat redeem (opsional tapi bagus)
                \App\Models\PointRedemption::create([
                    'user_id' => $lockedUser->id,
                    'method' => 'wallet',
                    'points' => $points,
                    'amount' => $amount,
                    'status' => 'instant',
                    'phone' => $phone,
                    'processed_at' => now(),
                ]);

                return back()->with('ok', 'Redeem berhasil. Point sudah masuk ke Saldo Maitri.');
            }

            // 2) Redeem cash (pending admin), point belum dipotong
            \App\Models\PointRedemption::create([
                'user_id' => $lockedUser->id,
                'method' => 'cash',
                'points' => $points,
                'amount' => $amount,
                'phone' => $phone,
                'status' => 'pending',
            ]);

            return back()->with('ok', 'Request redeem cash berhasil dibuat. Menunggu approval admin.');
        });
    }



}
