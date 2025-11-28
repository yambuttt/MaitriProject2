<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use App\Models\WalletTopup;
use App\Services\PaydisiniClient;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Models\WalletTransaction;
use Carbon\Carbon;



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

        $topups = WalletTopup::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('dashboard.user.wallet', [
            'user' => $user,
            'topups' => $topups,
        ]);
    }




    /**
     * Set / update PIN pembayaran Maitri
     */
    public function updatePin(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'pin' => ['required', 'digits_between:4,6'],
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
    public function topupPaydisini(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'amount' => ['required', 'integer', 'min:1000'],
            'method' => ['required', 'in:qris,va_mandiri'],
        ]);

        $amount = (int) $request->input('amount');
        $method = $request->input('method');

        $serviceId = $method === 'qris' ? 11 : 5;

        $apiKey = env('PAYDISINI_API_KEY');
        $baseUrl = rtrim(env('PAYDISINI_BASE_URL', 'https://api.paydisini.co.id/v1/'), '/');
        $validTime = (int) env('PAYDISINI_VALID_TIME', 1800); // ⬅️ SATU-SATUNYA sumber valid_time

        $uniqueCode = 'TOPUP' . $user->id . now()->format('ymdHis');

        $topup = WalletTopup::create([
            'user_id' => $user->id,
            'unique_code' => $uniqueCode,
            'method' => $method,
            'service_id' => $serviceId,
            'amount' => $amount,
            'status' => 'pending',
            'note' => 'Topup saldo user ' . $user->id,
        ]);

        $signature = md5($apiKey . $uniqueCode . $serviceId . $amount . $validTime . 'NewTransaction');

        $payload = [
            'key' => $apiKey,
            'request' => 'new',
            'unique_code' => $uniqueCode,
            'service' => $serviceId,
            'amount' => $amount,
            'note' => 'Topup saldo user ' . $user->id,
            'valid_time' => $validTime,      // ⬅️ ke Paydisini
            'type_fee' => 1,
            'payment_guide' => false,
            'callback_count' => 3,
            'signature' => $signature,
        ];

        $response = Http::asForm()->post($baseUrl . '/', $payload);

        if (!$response->successful()) {
            $topup->update(['status' => 'canceled']);
            return back()->with('error', 'Gagal menghubungi Paydisini (HTTP ' . $response->status() . ')');
        }

        $json = $response->json();

        if (!($json['success'] ?? false)) {
            $topup->update([
                'status' => 'canceled',
                'response_payload' => $json,
            ]);
            return back()->with('error', 'Transaksi gagal: ' . ($json['msg'] ?? 'Unknown'));
        }

        $data = $json['data'] ?? [];

        $topup->update([
            'pay_id' => $data['pay_id'] ?? null,
            'response_payload' => $data,
            // expired_at boleh disimpan, tapi countdown kita tetap ikut env
            'expired_at' => null,
        ]);

        return redirect()->route('dashboard.wallet.topup.show', $topup);
    }

    public function showTopup(WalletTopup $topup)
    {
        $user = Auth::user();
        if ($topup->user_id !== $user->id) {
            abort(403);
        }

        $data = $topup->response_payload ?? [];

        // SATU-SATUNYA patokan waktu: env
        $validSeconds = (int) env('PAYDISINI_VALID_TIME', 1800);

        // hitung expire dari created_at + validSeconds
        $expiresAt = $topup->created_at->copy()->addSeconds($validSeconds);

        return view('dashboard.user.wallet-topup-show', [
            'user' => $user,
            'topup' => $topup,
            'data' => $data,
            'expiresAt' => $expiresAt,   // ⬅️ inilah yang dipakai JS
        ]);
    }


    public function expireTopup(Request $request, WalletTopup $topup)
    {
        $user = Auth::user();
        if ($topup->user_id !== $user->id) {
            abort(403);
        }

        // hanya ubah kalau masih pending
        if ($topup->status === 'pending') {
            $topup->status = 'canceled';
            $topup->save();
        }

        return response()->json([
            'ok' => true,
            'status' => $topup->status,
        ]);
    }





    /**
     * Cek status topup ke Paydisini berdasarkan unique_code.
     * Dipakai oleh AJAX polling dari halaman QR/VA.
     */
    public function checkTopupStatus(Request $request, WalletTopup $topup)
    {
        $user = Auth::user();

        // keamanan: pastikan topup milik user yg lagi login
        if ($topup->user_id !== $user->id) {
            abort(403);
        }

        // kalau sudah sukses / canceled, tidak usah call API lagi
        if (in_array($topup->status, ['success', 'canceled'], true)) {
            return response()->json([
                'ok' => true,
                'status' => $topup->status,
                'message' => 'local-only',
            ]);
        }

        $apiKey = env('PAYDISINI_API_KEY');
        $baseUrl = rtrim(env('PAYDISINI_BASE_URL', 'https://api.paydisini.co.id/v1/'), '/');

        $uniqueCode = $topup->unique_code;

        // sesuai dokumentasi:
        // request = 'status'
        // signature = md5(key . unique_code . 'StatusTransaction')
        $signature = md5($apiKey . $uniqueCode . 'StatusTransaction');

        $payload = [
            'key' => $apiKey,
            'request' => 'status',
            'unique_code' => $uniqueCode,
            'signature' => $signature,
        ];

        $response = Http::asForm()->post($baseUrl . '/', $payload);

        if (!$response->successful()) {
            return response()->json([
                'ok' => false,
                'status' => $topup->status,
                'message' => 'HTTP error ' . $response->status(),
            ], 500);
        }

        $json = $response->json();

        // contoh gagal di dokumentasi:
        // { "success": false, "msg": "Kode unik tidak ditemukan." }
        if (!($json['success'] ?? false)) {
            return response()->json([
                'ok' => false,
                'status' => $topup->status,
                'message' => $json['msg'] ?? 'Status API error',
            ], 400);
        }

        // Struktur tepatnya nggak kelihatan di screenshot,
        // jadi kita ambil defensif: coba dari data.status dulu.
        $data = $json['data'] ?? $json;
        $status = strtolower($data['status'] ?? '');

        if (!in_array($status, ['pending', 'success', 'canceled'], true)) {
            // kalau fieldnya beda, kirim raw json buat debugging
            return response()->json([
                'ok' => false,
                'status' => $topup->status,
                'message' => 'Unknown status format',
                'raw' => $json,
            ], 500);
        }

        // sinkronkan status lokal dengan status dari Paydisini
        if ($status === 'success' && $topup->status !== 'success') {
            // CREDIT saldo user sekali saja
            $user->incrementBalance($topup->amount, 'Topup via Paydisini (' . $topup->method . ')');

            $topup->status = 'success';
            $topup->paid_at = now();
            $topup->callback_payload = $json; // simpan respon status terakhir
            $topup->save();
        } elseif ($status === 'canceled' && $topup->status !== 'canceled') {
            $topup->status = 'canceled';
            $topup->callback_payload = $json;
            $topup->save();
        }

        return response()->json([
            'ok' => true,
            'status' => $topup->status,
            'message' => 'Status updated',
        ]);
    }





}
