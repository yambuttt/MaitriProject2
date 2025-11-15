<?php 
// app/Http/Controllers/AdminDigiflazzController.php

namespace App\Http\Controllers;

use App\Services\DigiflazzMasterSyncService;
use App\Services\DigiflazzVariantPriceSyncService;

class AdminDigiflazzController extends Controller
{
    public function syncMaster(DigiflazzMasterSyncService $masterSync)
    {
        if (! $masterSync->canSync(30)) {
            return back()->with('error', 'Belum 30 menit sejak sync terakhir.');
        }

        try {
            $result = $masterSync->syncFromApi();
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Sync master gagal: '.$e->getMessage());
        }

        return back()->with('success', 'Sync master berhasil. Total item: '.$result['count']);
    }

    public function syncVariantPrices(DigiflazzVariantPriceSyncService $priceSync)
    {
        try {
            $updated = $priceSync->syncAll(); // bisa nanti ditambah opsi per-product
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Sync harga gagal: '.$e->getMessage());
        }

        return back()->with('success', "Sync harga berhasil. Varian terupdate: {$updated}");
    }
}
