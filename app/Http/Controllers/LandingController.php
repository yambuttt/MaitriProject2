<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\MarketplaceProduct;
use App\Models\Order;
use App\Models\MarketplaceOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class LandingController extends Controller
{
    public function index()
    {
        // HERO (sudah ada)
        $heroProducts = Product::where('is_active', true)
            ->with(['variants' => function ($q) {
                $q->active()->with('digiflazzVariant');
            }])
            ->has('variants')
            ->inRandomOrder()
            ->take(5)
            ->get();

        $heroMarketplaceProducts = MarketplaceProduct::where('is_active', true)
            ->with(['variants' => function ($q) {
                $q->where('is_active', true)->orderBy('price');
            }])
            ->has('variants')
            ->latest('id')
            ->take(4)
            ->get();

        // =========================
        // LAGI RAMAI DIBELI (DINAMIS)
        // =========================
        $since = Carbon::now()->subDays(14);

        // Digital Goods (Top Up / Digiflazz)
        $popularDigitalAgg = Order::query()
            ->select('product_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('product_id')
            ->where('status', 'success') // sesuaikan kalau status kamu beda
            ->where('created_at', '>=', $since)
            ->groupBy('product_id')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $popularDigitalIds = $popularDigitalAgg->pluck('product_id')->all();

        $popularDigitalProducts = Product::whereIn('id', $popularDigitalIds)
            ->with(['variants' => function ($q) {
                $q->active();
            }])
            ->get()
            ->keyBy('id');

        $popularDigital = $popularDigitalAgg->map(function ($row) use ($popularDigitalProducts) {
            $p = $popularDigitalProducts->get($row->product_id);
            if (!$p) return null;

            $minPrice = $p->variants->min(fn ($v) => $v->final_price);

            return (object) [
                'type' => 'digital',
                'label' => 'Digital Goods',
                'slug' => $p->slug,
                'name' => $p->name,
                'thumbnail' => $p->thumbnail ?? null,
                'min_price' => $minPrice,
                'total' => (int) $row->total,
                'url' => route('catalog.product.show', $p->slug),
            ];
        })->filter()->values();

        // Marketplace
        $popularMarketplaceAgg = MarketplaceOrder::query()
            ->select('marketplace_product_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('marketplace_product_id')
            ->where('created_at', '>=', $since)
            ->groupBy('marketplace_product_id')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $popularMarketplaceIds = $popularMarketplaceAgg->pluck('marketplace_product_id')->all();

        $popularMarketplaceProducts = MarketplaceProduct::whereIn('id', $popularMarketplaceIds)
            ->with(['variants' => function ($q) {
                $q->where('is_active', true);
            }])
            ->get()
            ->keyBy('id');

        $popularMarketplace = $popularMarketplaceAgg->map(function ($row) use ($popularMarketplaceProducts) {
            $p = $popularMarketplaceProducts->get($row->marketplace_product_id);
            if (!$p) return null;

            $minPrice = $p->variants->where('is_active', true)->min('price');

            return (object) [
                'type' => 'marketplace',
                'label' => 'Marketplace',
                'slug' => $p->slug,
                'name' => $p->name,
                'thumbnail' => $p->thumbnail ?? null,
                'min_price' => $minPrice,
                'total' => (int) $row->total,
                'url' => route('marketplace.product.show', $p->slug),
            ];
        })->filter()->values();

        // fallback kalau belum ada transaksi sama sekali:
        if ($popularDigital->isEmpty()) {
            $popularDigital = Product::where('is_active', true)
                ->with(['variants' => fn($q) => $q->active()])
                ->has('variants')
                ->latest('id')
                ->take(8)
                ->get()
                ->map(function ($p) {
                    $minPrice = $p->variants->min(fn ($v) => $v->final_price);
                    return (object) [
                        'type' => 'digital',
                        'label' => 'Digital Goods',
                        'slug' => $p->slug,
                        'name' => $p->name,
                        'thumbnail' => $p->thumbnail ?? null,
                        'min_price' => $minPrice,
                        'total' => null,
                        'url' => route('catalog.product.show', $p->slug),
                    ];
                });
        }

        if ($popularMarketplace->isEmpty()) {
            $popularMarketplace = MarketplaceProduct::where('is_active', true)
                ->with(['variants' => fn($q) => $q->where('is_active', true)])
                ->has('variants')
                ->latest('id')
                ->take(8)
                ->get()
                ->map(function ($p) {
                    $minPrice = $p->variants->where('is_active', true)->min('price');
                    return (object) [
                        'type' => 'marketplace',
                        'label' => 'Marketplace',
                        'slug' => $p->slug,
                        'name' => $p->name,
                        'thumbnail' => $p->thumbnail ?? null,
                        'min_price' => $minPrice,
                        'total' => null,
                        'url' => route('marketplace.product.show', $p->slug),
                    ];
                });
        }

        return view('pages.landing', [
            'heroProducts'            => $heroProducts,
            'heroMarketplaceProducts' => $heroMarketplaceProducts,

            // NEW
            'popularDigital'          => $popularDigital,
            'popularMarketplace'      => $popularMarketplace,
        ]);
    }
}
