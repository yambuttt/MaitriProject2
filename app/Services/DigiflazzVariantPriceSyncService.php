<?php
// app/Services/DigiflazzVariantPriceSyncService.php

namespace App\Services;

use App\Models\ProductVariant;

class DigiflazzVariantPriceSyncService
{
    /**
     * Sync semua product variants yang terhubung ke master.
     * Bisa difilter per product kalau mau.
     */
    public function syncAll(?int $productId = null): int
    {
        $query = ProductVariant::with('digiflazzVariant')
            ->whereNotNull('digiflazz_variant_id');

        if ($productId) {
            $query->where('product_id', $productId);
        }

        $updated = 0;

        $query->chunkById(200, function ($variants) use (&$updated) {
            foreach ($variants as $variant) {
                if (!$variant->digiflazzVariant) {
                    continue;
                }

                $master = $variant->digiflazzVariant;

                // kalau status master nonaktif, boleh kamu matikan varian
                if ($master->status === 'nonaktif') {
                    $variant->is_active = false;
                }

                // base_price SELALU ikut master (sesuai keputusan kita)
                $variant->base_price = $master->base_price;
                $variant->save();

                $updated++;
            }
        });

        return $updated;
    }
}
