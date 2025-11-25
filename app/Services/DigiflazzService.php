<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Http;

class DigiflazzService
{
    public function createTransaction(Order $order, ProductVariant $variant): array
    {
        $username = config('services.digiflazz.username');
        $apiKey   = config('services.digiflazz.apikey');
        $endpoint = config('services.digiflazz.endpoint');
       

        // ref_id -> pakai kode invoice internal, misal "MP-00001"
        $refId = $order->code;

        // sign = md5(username + apiKey + ref_id)
        $sign = md5($username . $apiKey . $refId);

        $payload = [
            'username'      => $username,
            'buyer_sku_code'=> $variant->buyer_sku_code,
            'customer_no'   => $order->customer_no,   // atau $order->target, sesuaikan dengan field kamu
            'ref_id'        => $refId,
            'sign'          => $sign,

            // 'max_price'   => ... (optional)
            // 'cb_url'      => ... (optional, pakai kalau punya dynamic webhook)
            // 'allow_dot'   => ... (optional)
        ];

        $response = Http::acceptJson()->post($endpoint, $payload);

        if (! $response->successful()) {
            throw new \RuntimeException(
                'HTTP error from Digiflazz: ' . $response->status() . ' ' . $response->body()
            );
        }

        $json = $response->json();

        // Sesuai docs: response dibungkus di key "data"
        if (! isset($json['data']) || ! is_array($json['data'])) {
            throw new \RuntimeException('Invalid Digiflazz response: ' . json_encode($json));
        }

        return $json['data'];
    }
}
