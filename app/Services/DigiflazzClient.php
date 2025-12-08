<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DigiflazzClient
{
    protected string $base;
    protected string $username;
    protected string $apiKey;

    public function __construct()
    {
        $this->base = rtrim(config('services.digiflazz.base'), '/');
        $this->username = (string) config('services.digiflazz.username');
        $this->apiKey = (string) config('services.digiflazz.api_key');
    }

    protected function client()
    {
        return Http::timeout(15)->acceptJson();
    }

    /**
     * Pricelist produk (pulsa, data, game, dll).
     * Kamu bisa filter di sisi kita setelah data diterima.
     */
    public function pricelist(): array
    {
        $url = "{$this->base}/price-list";

        $payload = [
            'cmd' => 'prepaid',
            'username' => $this->username,
            'sign' => md5($this->username . $this->apiKey . 'pricelist'),
        ];

        $res = $this->client()->post($url, $payload);

        // 1) Kalau HTTP error (500, 403, dll) → lempar exception
        if (!$res->ok()) {
            throw new \RuntimeException(
                'Gagal mengambil pricelist dari Digiflazz (HTTP ' . $res->status() . ')'
            );
        }

        $data = $res->json();

        // 2) Kalau struktur JSON tidak punya field "data" atau bukan array → anggap error
        if (!isset($data['data']) || !is_array($data['data'])) {
            // coba ambil pesan error dari JSON
            $msg = $data['message'] ?? $data['msg'] ?? 'Struktur respons Digiflazz tidak sesuai (tidak ada field data).';

            throw new \RuntimeException(
                'Gagal mengambil pricelist dari Digiflazz: ' . $msg
            );
        }

        // 3) Kalau semuanya oke → balikin daftar produk (array)
        return $data['data'];
    }

}
