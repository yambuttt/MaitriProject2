<?php

namespace App\Http\Controllers;

use App\Models\WalletTopup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaydisiniCallbackController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('Paydisini callback received', $request->all());

        $apiKey = env('PAYDISINI_API_KEY');

        $key       = $request->input('key');
        $payId     = $request->input('pay_id');
        $unique    = $request->input('unique_code');
        $status    = $request->input('status');   // "Success" / "Canceled"
        $signature = $request->input('signature');

        // 1. Validasi key
        if ($key !== $apiKey) {
            Log::warning('Paydisini callback invalid key');
            return response()->json(['success' => false, 'message' => 'Invalid key'], 400);
        }

        // 2. Validasi signature: md5(key . unique_code . 'CallbackStatus')
        $expectedSignature = md5($apiKey . $unique . 'CallbackStatus');
        if ($signature !== $expectedSignature) {
            Log::warning('Paydisini callback invalid signature', [
                'expected' => $expectedSignature,
                'got'      => $signature,
            ]);
            return response()->json(['success' => false, 'message' => 'Invalid signature'], 400);
        }

        // 3. Cari record topup
        $topup = WalletTopup::where('unique_code', $unique)->first();

        if (! $topup) {
            Log::warning('Paydisini callback: topup not found', ['unique_code' => $unique]);
            return response()->json(['success' => false, 'message' => 'Topup not found'], 404);
        }

        // 4. Simpan payload callback
        $topup->callback_payload = $request->all();

        // Kalau sudah diproses sebelumnya, jangan dobel
        if (in_array($topup->status, ['success', 'canceled'], true)) {
            $topup->save();
            return response()->json(['success' => true]);
        }

        // 5. Update saldo & status
        if (strtolower($status) === 'success') {
            $user = $topup->user;

            // gunakan helper di model User
            $user->incrementBalance($topup->amount, 'Topup via Paydisini ('.$topup->method.')');

            $topup->status  = 'success';
            $topup->paid_at = now();
            $topup->pay_id  = $payId;
        } else {
            $topup->status = 'canceled';
            $topup->pay_id = $payId;
        }

        $topup->save();

        return response()->json(['success' => true]);
    }
}
