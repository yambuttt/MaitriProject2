<?php

namespace App\Http\Controllers;

use App\Models\DigiflazzVariant;
use App\Models\DigiflazzSyncLog;
use App\Services\DigiflazzMasterSyncService;
use Illuminate\Support\Facades\Artisan;


use Illuminate\Http\Request;
use Throwable;

class AdminDigiflazzController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->input('q', ''));
        $brand = trim($request->input('brand', ''));
        $category = trim($request->input('category', ''));
        $status = trim($request->input('status', ''));
        $pp = (int) $request->input('per_page', 20);

        $variantsQuery = DigiflazzVariant::query();

        if ($q !== '') {
            $like = "%{$q}%";
            $variantsQuery->where(function ($query) use ($like) {
                $query->where('buyer_sku_code', 'like', $like)
                    ->orWhere('product_name', 'like', $like);
            });
        }

        if ($brand !== '') {
            $variantsQuery->where('brand', $brand);
        }

        if ($category !== '') {
            $variantsQuery->where('category', $category);
        }

        if ($status !== '') {
            $variantsQuery->where('status', $status);
        }

        $variants = $variantsQuery
            ->orderBy('brand')
            ->orderBy('product_name')
            ->paginate($pp)
            ->withQueryString();

        // Log terbaru (misal 20 terakhir)
        $logs = DigiflazzSyncLog::orderByDesc('created_at')
            ->limit(20)
            ->get();

        // Untuk pilihan filter brand / kategori di dropdown
        $brands = DigiflazzVariant::select('brand')->distinct()->orderBy('brand')->pluck('brand');
        $categories = DigiflazzVariant::select('category')->distinct()->orderBy('category')->pluck('category');

        return view('dashboard.admin.digiflazz.index', compact(
            'variants',
            'logs',
            'brands',
            'categories',
            'q',
            'brand',
            'category',
            'status',
            'pp'
        ));
    }

    public function syncMaster()
    {
        try {
            // Panggil command artisan yang sudah kamu pakai secara manual
            Artisan::call('digiflazz:sync-master', [
                '--force' => true,   // sama seperti saat kamu jalankan di CLI
            ]);

            $output = trim(Artisan::output()); // kalau command-mu ngeluarin teks ringkasan

            // Simpan log
            DigiflazzSyncLog::create([
                'type' => 'master',
                'status' => 'success',
                'synced_count' => 0,  // kalau mau, nanti kita parse dari $output
                'trigger' => 'manual',
                'message' => $output ?: 'Sinkron master Digiflazz berhasil dijalankan dari panel admin.',
                'context' => [],
            ]);

            return back()->with(
                'success',
                $output ?: 'Sinkron master Digiflazz berhasil dijalankan.'
            );
        } catch (\Throwable $e) {
            report($e);

            DigiflazzSyncLog::create([
                'type' => 'master',
                'status' => 'failed',
                'synced_count' => 0,
                'trigger' => 'manual',
                'message' => $e->getMessage(),
                'context' => [],
            ]);

            return back()->with('error', 'Gagal sinkron master Digiflazz: ' . $e->getMessage());
        }
    }





}
