<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceCategory;
use App\Models\MarketplaceProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminMarketplaceProductController extends Controller
{
    public function index(Request $request)
    {
        $categoryId = $request->query('category_id');

        $products = MarketplaceProduct::with('category')
            ->when($categoryId, fn($q) => $q->where('marketplace_category_id', $categoryId))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $categories = MarketplaceCategory::orderBy('name')->get();

        return view('dashboard.admin.marketplace.products.index', [
            'products'   => $products,
            'categories' => $categories,
            'categoryId' => $categoryId,
        ]);
    }

    public function create()
    {
        $categories = MarketplaceCategory::orderBy('name')->get();

        return view('dashboard.admin.marketplace.products.create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'marketplace_category_id' => ['required', 'exists:marketplace_categories,id'],
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        $slug = Str::slug($data['name']);
        $original = $slug;
        $i = 1;
        while (MarketplaceProduct::where('slug', $slug)->exists()) {
            $slug = $original.'-'.$i++;
        }

        $data['slug'] = $slug;
        $data['is_active'] = $request->boolean('is_active');

        MarketplaceProduct::create($data);

        return redirect()
            ->route('admin.marketplace.products.index')
            ->with('ok', 'Produk marketplace berhasil dibuat.');
    }

    public function edit(MarketplaceProduct $product)
    {
        $categories = MarketplaceCategory::orderBy('name')->get();

        return view('dashboard.admin.marketplace.products.edit', [
            'product'    => $product,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, MarketplaceProduct $product)
    {
        $data = $request->validate([
            'marketplace_category_id' => ['required', 'exists:marketplace_categories,id'],
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        $product->marketplace_category_id = $data['marketplace_category_id'];
        $product->name = $data['name'];
        $product->description = $data['description'] ?? null;
        $product->is_active = $request->boolean('is_active');

        $product->save();

        return redirect()
            ->route('admin.marketplace.products.index')
            ->with('ok', 'Produk marketplace berhasil diperbarui.');
    }
}
