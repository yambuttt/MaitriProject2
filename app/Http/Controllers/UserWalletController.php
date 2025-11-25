<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class UserWalletController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth'); // pastikan user login
    // }

    /**
     * Halaman saldo & PIN
     */
    public function index()
    {
        $user = Auth::user();

        return view('dashboard.user.wallet', [
            'user' => $user,
        ]);
    }

    /**
     * Set / update PIN pembayaran Maitri
     */
    public function updatePin(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'pin'              => ['required', 'digits_between:4,6'],
            'pin_confirmation' => ['required', 'same:pin'],
        ], [
            'pin.digits_between' => 'PIN harus 4–6 digit angka.',
        ]);

        $user->setPaymentPin($request->pin);

        return back()->with('success', 'PIN pembayaran berhasil disimpan.');
    }

    /**
     * (Opsional) Topup saldo manual untuk testing.
     * Nanti dihapus kalau sudah ada mekanisme topup beneran.
     */
    public function topup(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'amount' => ['required', 'integer', 'min:1000'],
        ]);

        $user->incrementBalance($request->amount, 'Topup manual (dev)');

        return back()->with('success', 'Saldo bertambah Rp ' . number_format($request->amount, 0, ',', '.'));
    }
}
