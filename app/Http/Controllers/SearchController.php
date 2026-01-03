<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\MarketplaceProduct;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $tab = $request->query('tab', 'all'); // all | digital | marketplace

        if ($q === '') {
            // kalau kosong, arahkan aja ke catalog biar aman
            return redirect()->route('catalog');
        }

        // =========================
        // Digital Goods (Digiflazz)
        // =========================
        $digitalQuery = Product::query()
            ->with([
                'category:id,name,slug',
                'subcategory:id,name,slug,category_id',
                'variants' => function ($v) {
                    $v->where('is_active', true)->with('digiflazzVariant');
                },
            ])
            ->where('is_active', true)
            ->whereHas('variants', fn($v) => $v->where('is_active', true))
            ->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('provider', 'like', "%{$q}%")
                  ->orWhere('slug', 'like', "%{$q}%");
            })
            ->orderBy('name');

        // =========================
        // Marketplace
        // =========================
        $marketQuery = MarketplaceProduct::query()
            ->with([
                'category:id,name,slug',
                'images',
                'variants' => fn($v) => $v->where('is_active', true)->orderBy('price'),
            ])
            ->where('is_active', true)
            ->whereHas('variants', fn($v) => $v->where('is_active', true))
            ->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('slug', 'like', "%{$q}%")
                  ->orWhere('description', 'like', "%{$q}%");
            })
            ->orderBy('name');

        // paging
        $perPage = 12;

        // tab logic biar hemat query
        $digital = null;
        $marketplace = null;

        if ($tab === 'digital') {
            $digital = $digitalQuery->paginate($perPage)->withQueryString();
        } elseif ($tab === 'marketplace') {
            $marketplace = $marketQuery->paginate($perPage)->withQueryString();
        } else {
            // all: paginasi masing-masing kecil biar enak
            $digital = $digitalQuery->limit(8)->get();
            $marketplace = $marketQuery->limit(8)->get();
        }

        return view('pages.search', [
            'q' => $q,
            'tab' => $tab,
            'digital' => $digital,
            'marketplace' => $marketplace,
        ]);
    }
}
