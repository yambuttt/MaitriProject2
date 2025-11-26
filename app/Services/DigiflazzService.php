<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Http;

class DigiflazzService
{
    /**
     * Membuat transaksi ke Digiflazz untuk 1 order + 1 variant.
     *
     * @throws \RuntimeException kalau response dari Digiflazz tidak valid
     */
    public function createTransaction(Order $order, ProductVariant $variant): array
    {
        $username = config('services.digiflazz.username');
        $apiKey   = config('services.digiflazz.api_key');   // <- pakai api_key (bukan apikey)
        $endpoint = config('services.digiflazz.endpoint');

        // Ref ID kita pakai kode invoice internal, misal "MP-00011"
        $refId = $order->code;

        // Sign sesuai docs: md5(username + apiKey + ref_id)
        $sign = md5($username . $apiKey . $refId);

        // Payload sesuai dokumentasi Digiflazz
        $payload = [
            'username'       => $username,
            'buyer_sku_code' => $variant->buyer_sku_code,
            'customer_no'    => $order->target,  // nomor tujuan disimpan di kolom "target"
            'ref_id'         => $refId,
            'sign'           => $sign,
            // opsional: 'testing' => true, // kalau mau pakai mode testing Digiflazz
        ];

        // Kirim request ke endpoint transaction
        $response = Http::acceptJson()->post($endpoint, $payload);

        // Ambil JSON apa adanya (walaupun HTTP 4xx/5xx, Digiflazz masih kirim "data")
        $json = $response->json();

        if (is_array($json) && isset($json['data']) && is_array($json['data'])) {
            // Contoh isi data:
            // [
            //   "ref_id"  => "MP-00011",
            //   "customer_no" => "08xxxx",
            //   "buyer_sku_code" => "DAN1",
            //   "message" => "IP Anda tidak kami kenali: ...",
            //   "status"  => "Gagal" / "Sukses" / "Pending",
            //   "rc"      => "45",
            //   ...
            // ]
            return $json['data'];
        }

        // Kalau tidak ada key "data", berarti response-nya benar2 tidak sesuai ekspektasi
        throw new \RuntimeException(
            'Invalid response from Digiflazz: HTTP '
            . $response->status() . ' ' . $response->body()
        );
    }
}
