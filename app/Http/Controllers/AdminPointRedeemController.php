<?php

namespace App\Http\Controllers;

use App\Models\PointRedemption;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminPointRedeemController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $q = PointRedemption::query()->with(['user', 'processor'])
            ->orderByDesc('created_at');

        if ($status) {
            $q->where('status', $status);
        }

        $items = $q->paginate(30)->withQueryString();

        return view('dashboard.admin.point-redeems.index', [
            'items' => $items,
            'status' => $status,
        ]);
    }

    public function show(PointRedemption $redeem)
    {
        $redeem->load(['user', 'processor']);

        return view('dashboard.admin.point-redeems.show', [
            'redeem' => $redeem,
        ]);
    }

    public function approve(Request $request, PointRedemption $redeem)
    {
        $request->validate([
            'admin_note' => ['nullable', 'string'],
            'proof' => ['nullable', 'file', 'max:4096', 'mimes:jpg,jpeg,png,pdf,webp'],
        ]);

        if ($redeem->method !== 'cash') {
            return back()->with('error', 'Approve hanya untuk redeem Cash.');
        }

        if ($redeem->status !== 'pending') {
            return back()->with('error', 'Redeem ini sudah diproses.');
        }

        return DB::transaction(function () use ($request, $redeem) {
            $redeem = PointRedemption::where('id', $redeem->id)->lockForUpdate()->first();
            if (!$redeem || $redeem->status !== 'pending') {
                return back()->with('error', 'Redeem sudah berubah status.');
            }

            /** @var User $user */
            $user = User::where('id', $redeem->user_id)->lockForUpdate()->first();

            // cek point cukup saat mau dipotong (sesuai requirement: potong saat approve)
            if ((int) $user->maitri_points < (int) $redeem->points) {
                return back()->with('error', 'Point user tidak cukup (mungkin sudah dipakai redeem saldo).');
            }

            $proofPath = $redeem->proof_path;

            if ($request->hasFile('proof')) {
                $proofPath = $request->file('proof')->store('redeem_proofs', 'public');
            }

            // potong point di saat approve
            $user->maitri_points = (int) $user->maitri_points - (int) $redeem->points;
            $user->save();

            $redeem->update([
                'status' => 'approved',
                'admin_note' => $request->admin_note,
                'proof_path' => $proofPath,
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);

            return back()->with('success', 'Redeem cash berhasil di-approve dan point sudah dipotong.');
        });
    }

    public function reject(Request $request, PointRedemption $redeem)
    {
        $request->validate([
            'admin_note' => ['nullable', 'string'],
        ]);

        if ($redeem->status !== 'pending') {
            return back()->with('error', 'Redeem ini sudah diproses.');
        }

        $redeem->update([
            'status' => 'rejected',
            'admin_note' => $request->admin_note,
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Redeem berhasil ditolak (point tidak dipotong).');
    }
}
