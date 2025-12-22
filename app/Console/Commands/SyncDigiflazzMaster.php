<?php

// app/Console/Commands/SyncDigiflazzMaster.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DigiflazzMasterSyncService;
use App\Models\DigiflazzSyncLog;

class SyncDigiflazzMaster extends Command
{
    protected $signature = 'digiflazz:sync-master {--force : Paksa sync walau belum 30 menit}';
    protected $description = 'Sync pricelist Digiflazz ke tabel master (digiflazz_variants)';

    public function handle(DigiflazzMasterSyncService $service): int
    {
        $force = $this->option('force');

        if (!$force && !$service->canSync(30)) { // limiter 30 menit :contentReference[oaicite:4]{index=4}
            $this->warn('Belum 30 menit sejak sync terakhir.');
            return self::FAILURE;
        }

        try {
            $result = $service->syncFromApi(); // update tabel digiflazz_variants :contentReference[oaicite:5]{index=5}

            DigiflazzSyncLog::create([
                'type' => 'master',
                'status' => 'success',
                'synced_count' => $result['count'] ?? 0,
                'trigger' => 'auto', // atau 'cron'
                'message' => "Auto sync master sukses. Total item: " . ($result['count'] ?? 0),
                'context' => ['last_sync_at' => (string) ($result['last_sync_at'] ?? now())],
            ]);

            return self::SUCCESS;
        } catch (\Throwable $e) {
            report($e);

            DigiflazzSyncLog::create([
                'type' => 'master',
                'status' => 'failed',
                'synced_count' => 0,
                'trigger' => 'auto',
                'message' => $e->getMessage(),
                'context' => [],
            ]);

            return self::FAILURE;
        }
    }
}

