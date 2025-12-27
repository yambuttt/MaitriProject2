<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceProduct;
use App\Models\MarketplaceVariant;
use Illuminate\Http\Request;

class AdminMarketplaceVariantController extends Controller
{
    public function index(MarketplaceProduct $product)
    {
        $variants = $product->variants()->orderBy('price')->get();

        return view('dashboard.admin.marketplace.variants.index', [
            'product' => $product,
            'variants' => $variants,
        ]);
    }

    public function create(MarketplaceProduct $product)
    {
        return view('dashboard.admin.marketplace.variants.create', [
            'product' => $product,
        ]);
    }

    public function store(Request $request, MarketplaceProduct $product)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'duration_days' => ['nullable', 'integer', 'min:1'],
            'price' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['marketplace_product_id'] = $product->id;
        $data['is_active'] = $request->boolean('is_active');

        MarketplaceVariant::create($data);

        return redirect()
            ->route('admin.marketplace.variants.index', $product)
            ->with('ok', 'Varian berhasil dibuat.');
    }

    public function edit(MarketplaceProduct $product, MarketplaceVariant $variant)
    {
        return view('dashboard.admin.marketplace.variants.edit', [
            'product' => $product,
            'variant' => $variant,
        ]);
    }

    public function update(Request $request, MarketplaceProduct $product, MarketplaceVariant $variant)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'duration_days' => ['nullable', 'integer', 'min:1'],
            'price' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $variant->name = $data['name'];
        $variant->duration_days = $data['duration_days'] ?? null;
        $variant->price = $data['price'];
        $variant->is_active = $request->boolean('is_active');

        $variant->save();

        return redirect()
            ->route('admin.marketplace.variants.index', $product)
            ->with('ok', 'Varian berhasil diperbarui.');
    }

    public function destroy(MarketplaceProduct $product, MarketplaceVariant $variant)
    {
        // pastikan variant memang milik product tsb
        if ($variant->marketplace_product_id !== $product->id) {
            abort(404);
        }

        // (Opsional tapi disarankan)
        // if (\App\Models\MarketplaceOrder::where('marketplace_variant_id', $variant->id)->exists()) {
        //     return back()->with('error', 'Varian tidak bisa dihapus karena sudah memiliki order.');
        // }

        $variant->delete();

        return redirect()
            ->route('admin.marketplace.variants.index', $product)
            ->with('ok', 'Varian berhasil dihapus.');
    }

}
