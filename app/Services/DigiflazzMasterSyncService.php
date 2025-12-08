<?php
// app/Services/DigiflazzMasterSyncService.php

namespace App\Services;

use App\Services\DigiflazzClient;

use App\Models\DigiflazzVariant;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;


class DigiflazzMasterSyncService
{
    // optional: key buat catat kapan terakhir sync global
    const LAST_SYNC_CACHE_KEY = 'digiflazz_master_last_sync';

    protected $client;

    public function __construct(DigiflazzClient $client)
    {
        $this->client = $client;
    }

    public function canSync(int $minIntervalMinutes = 30): bool
    {
        $last = Cache::get(self::LAST_SYNC_CACHE_KEY);

        if (!$last) {
            return true;
        }

        return now()->diffInMinutes($last) >= $minIntervalMinutes;
    }

    public function syncFromApi(): array
    {
        // call ini DI SINI saja, jangan di tempat lain
        $pricelist = $this->client->pricelist();

        $now = now();
        $seenSku = [];

        DB::transaction(function () use ($pricelist, $now, &$seenSku) {
            foreach ($pricelist as $item) {
                $sku = $item['buyer_sku_code'] ?? null;
                if (!$sku) {
                    continue;
                }

                $seenSku[] = $sku;

                DigiflazzVariant::updateOrCreate(
                    ['buyer_sku_code' => $sku],
                    [
                        'product_name' => $item['product_name'] ?? '',
                        'brand' => $item['brand'] ?? null,
                        'category' => $item['category'] ?? null,
                        'base_price' => $item['price'] ?? 0,
                        'status' => $item['status'] ?? null,
                        'buyer_product_status' => (bool) ($item['buyer_product_status'] ?? true),
                        'seller_product_status' => (bool) ($item['seller_product_status'] ?? true),
                        'last_synced_at' => $now,
                        'raw' => $item,
                    ]
                );
            }

            // optional: tandai yang tidak lagi muncul sebagai nonaktif
            // optional: tandai yang tidak lagi muncul sebagai nonaktif
// dan nonaktifkan semua product_variants yang terhubung ke master tsb
            // cari master yang sudah tidak muncul lagi di pricelist terbaru
            $removedIds = DigiflazzVariant::whereNotIn('buyer_sku_code', $seenSku)
                ->pluck('id');

            if ($removedIds->isNotEmpty()) {
                // 1) nonaktifkan dulu semua product_variants yang pakai master ini
                ProductVariant::whereIn('digiflazz_variant_id', $removedIds)
                    ->update(['is_active' => false]);

                // 2) hapus master-nya
                DigiflazzVariant::whereIn('id', $removedIds)->delete();
            }


        });

        Cache::put(self::LAST_SYNC_CACHE_KEY, $now, 60 * 24); // simpan 1 hari

        return [
            'count' => count($pricelist),
            'last_sync_at' => $now,
        ];
    }
}
