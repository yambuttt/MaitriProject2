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

        // asumsi 1 point = 1 rupiah (kalau mau beda, tinggal ubah rumusnya)
        $amount = $points;

        if ($method === 'cash' && empty($phone)) {
            return back()->with('error', 'Nomor WhatsApp wajib diisi untuk redeem Uang Cash.');
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($user, $method, $points, $amount, $phone) {

            // Lock row user biar aman dari double submit
            $lockedUser = \App\Models\User::query()
                ->whereKey($user->id)
                ->lockForUpdate()
                ->first();

            if ((int) $lockedUser->maitri_points < $points) {
                return back()->with('error', 'Point kamu tidak cukup.');
            }

            // 1) Redeem ke Saldo Maitri => langsung potong points + tambah saldo
            if ($method === 'wallet') {
                // potong point
                $lockedUser->maitri_points = (int) $lockedUser->maitri_points - $points;

                // tambahkan saldo (sesuaikan field saldo kamu: saldo_maitri / balance / dll)
                // NOTE: di project kamu sebelumnya ada "saldo_maitri" dan juga dipakai buat bayar digiflazz
                $lockedUser->saldo_maitri = (int) $lockedUser->saldo_maitri + $amount;

                $lockedUser->save();

                // (opsional) catat log redeem di tabel redeem_requests kalau kamu pakai
                // \App\Models\PointRedeem::create([...]);

                return back()->with('ok', 'Redeem berhasil. Point sudah masuk ke Saldo Maitri.');
            }

            // 2) Redeem Cash => buat request status pending, point belum dipotong dulu
            PointRedeemption::create([
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
