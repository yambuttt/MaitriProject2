<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $catSlug = $request->query('category');
        $subSlug = $request->query('subcategory');
        $pp = (int) $request->query('per_page', 9);
        $pp = $pp > 0 && $pp <= 48 ? $pp : 9;

        $category = $catSlug ? Category::where('is_active', true)->where('slug', $catSlug)->first() : null;
        $subcategory = null;
        if ($category && $subSlug) {
            $subcategory = Subcategory::where('is_active', true)
                ->where('category_id', $category->id)
                ->where('slug', $subSlug)->first();
        }

        $products = Product::query()
            ->with([
                'category:id,name,slug',
                'subcategory:id,name,slug,category_id',
                'variants' => function ($q) {
                    $q->where('is_active', true)->orderBy('base_price');
                }
            ])
            ->where('is_active', true)
            ->when($category, fn($qb) => $qb->where('category_id', $category->id))
            ->when($subcategory, fn($qb) => $qb->where('subcategory_id', $subcategory->id))
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                        ->orWhere('provider', 'like', "%{$q}%")
                        ->orWhere('slug', 'like', "%{$q}%");
                });
            })
            // hanya yang punya varian aktif
            ->whereHas('variants', fn($v) => $v->where('is_active', true))
            ->orderBy('name')
            ->paginate($pp)
            ->withQueryString();

        // kategori untuk filter (aktif & punya produk aktif)
        // kategori untuk filter (tidak diubah)
        $categories = Category::where('is_active', true)
            ->whereHas('products', fn($p) => $p->where('is_active', true)->whereHas('variants', fn($v) => $v->where('is_active', true)))
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        // subkategori: TAMPILKAN SEMUA yang aktif di kategori terpilih,
// plus hitung berapa produk "live" (aktif & punya varian aktif)
        $subcategories = collect();
        if ($category) {
            $subcategories = Subcategory::where('is_active', true)
                ->where('category_id', $category->id)
                ->withCount([
                    'products as live_products_count' => function ($q) {
                        $q->where('is_active', true)
                            ->whereHas('variants', fn($v) => $v->where('is_active', true));
                    }
                ])
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'category_id']);
        }


        return view('pages.catalog', compact('products', 'categories', 'subcategories', 'category', 'subcategory', 'q', 'pp'));
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        // Load relasi dulu
        $product->load([
            'category:id,name,slug',
            'subcategory:id,name,slug,category_id',
            'variants' => function ($q) {
                $q->where('is_active', true)
                    ->with('digiflazzVariant');   // penting biar base_price accessor bisa baca master
            },
        ]);

        // Urutkan varian di memory berdasarkan accessor base_price
        $product->setRelation(
            'variants',
            $product->variants
                ->sortBy(fn($v) => $v->base_price)
                ->values()
        );

        abort_if($product->variants->isEmpty(), 404);

        return view('pages.product', compact('product'));
    }

}
