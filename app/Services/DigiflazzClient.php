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
        $this->base     = rtrim(config('services.digiflazz.base'), '/');
        $this->username = (string) config('services.digiflazz.username');
        $this->apiKey   = (string) config('services.digiflazz.api_key');
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
            'cmd'      => 'prepaid',           // sesuai dokumen Digiflazz untuk produk prabayar
            'username' => $this->username,
            'sign'     => md5($this->username.$this->apiKey.'pricelist'),
        ];

        $res = $this->client()->post($url, $payload);
        if (!$res->ok()) {
            throw new \RuntimeException('Gagal mengambil pricelist dari Digiflazz');
        }
        $data = $res->json();

        // Normalisasi ringan: pastikan ada array data
        return is_array($data['data'] ?? null) ? $data['data'] : [];
    }
}
