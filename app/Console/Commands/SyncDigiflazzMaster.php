<?php

// app/Console/Commands/SyncDigiflazzMaster.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DigiflazzMasterSyncService;

class SyncDigiflazzMaster extends Command
{
    protected $signature = 'digiflazz:sync-master {--force : Paksa sync walau belum 30 menit}';
    protected $description = 'Sync pricelist Digiflazz ke tabel master (digiflazz_variants)';

    public function handle(DigiflazzMasterSyncService $service): int
    {
        $force = $this->option('force');

        if (! $force && ! $service->canSync(30)) {
            $this->warn('Belum 30 menit sejak sync terakhir. Tambahkan --force kalau mau memaksa.');
            return self::FAILURE;
        }

        $this->info('Mengambil pricelist dari Digiflazz...');

        try {
            $result = $service->syncFromApi();
        } catch (\Throwable $e) {
            $this->error('Sync gagal: '.$e->getMessage());
            report($e);
            return self::FAILURE;
        }

        $this->info("Sync selesai. Total item: {$result['count']}");
        $this->info("Last sync at: {$result['last_sync_at']}");

        return self::SUCCESS;
    }
}

