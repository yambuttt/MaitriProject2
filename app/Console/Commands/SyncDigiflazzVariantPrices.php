<?php

// app/Console/Commands/SyncDigiflazzVariantPrices.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DigiflazzVariantPriceSyncService;

class SyncDigiflazzVariantPrices extends Command
{
    protected $signature = 'digiflazz:sync-variant-prices {--product_id= : Hanya untuk 1 product tertentu}';
    protected $description = 'Sync base_price product_variants dari digiflazz_variants';

    public function handle(DigiflazzVariantPriceSyncService $service): int
    {
        $productId = $this->option('product_id');

        $this->info('Sync harga varian dari master Digiflazz...');

        try {
            $updated = $service->syncAll($productId ? (int) $productId : null);
        } catch (\Throwable $e) {
            $this->error('Sync harga gagal: '.$e->getMessage());
            report($e);
            return self::FAILURE;
        }

        $this->info("Berhasil update {$updated} varian.");

        return self::SUCCESS;
    }
}
