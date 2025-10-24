<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\DigiflazzClient;
use Illuminate\Support\Arr;


class AdminProductVariantController extends Controller
{
    // List varian per produk + form tambah cepat
    public function index(Product $product, Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $pp = (int) $request->query('per_page', 20);
        $pp = $pp > 0 && $pp <= 200 ? $pp : 20;

        $variants = ProductVariant::query()
            ->where('product_id', $product->id)
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                        ->orWhere('buyer_sku_code', 'like', "%{$q}%");
                });
            })
            ->orderBy('name')
            ->paginate($pp)
            ->withQueryString();

        return view('dashboard.admin.products.variants.index', compact('product', 'variants', 'q', 'pp'));
    }

    public function store(Product $product, Request $request)
    {
        $data = $request->validate([
            'buyer_sku_code' => [
                'required',
                'string',
                'max:120',
                Rule::unique('product_variants', 'buyer_sku_code')->where(fn($q) => $q->where('product_id', $product->id))
            ],
            'name' => ['required', 'string', 'max:180'],
            'base_price' => ['required', 'integer', 'min:0'],
            'markup_rp' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['product_id'] = $product->id;
        $data['is_active'] = (bool) ($data['is_active'] ?? true);

        ProductVariant::create($data);

        return redirect()->route('admin.products.variants.index', $product)->with('ok', 'Varian ditambahkan.');
    }

    public function edit(Product $product, ProductVariant $variant)
    {
        abort_unless($variant->product_id === $product->id, 404);
        return view('dashboard.admin.products.variants.edit', compact('product', 'variant'));
    }

    public function update(Product $product, ProductVariant $variant, Request $request)
    {
        abort_unless($variant->product_id === $product->id, 404);

        $data = $request->validate([
            'buyer_sku_code' => [
                'required',
                'string',
                'max:120',
                Rule::unique('product_variants', 'buyer_sku_code')
                    ->where(fn($q) => $q->where('product_id', $product->id))
                    ->ignore($variant->id)
            ],
            'name' => ['required', 'string', 'max:180'],
            'base_price' => ['required', 'integer', 'min:0'],
            'markup_rp' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        $variant->update($data);

        return redirect()->route('admin.products.variants.index', $product)->with('ok', 'Varian diperbarui.');
    }

    public function toggle(Product $product, ProductVariant $variant)
    {
        abort_unless($variant->product_id === $product->id, 404);
        $variant->update(['is_active' => !$variant->is_active]);
        return back()->with('ok', 'Status varian diperbarui.');
    }

    public function destroy(Product $product, ProductVariant $variant)
    {
        abort_unless($variant->product_id === $product->id, 404);
        $variant->delete();
        return back()->with('ok', 'Varian dihapus.');
    }


    // Cari SKU dari Digiflazz (AJAX) dengan query q
    public function searchDigiflazz(Product $product, Request $request, DigiflazzClient $client)
    {
        $q = trim((string) $request->query('q', ''));
        if ($q === '') {
            return response()->json(['items' => []]);
        }

        $rows = $client->pricelist();

        // filter sederhana di sisi kita: cocokkan nama/brand/buyer_sku_code
        $filtered = array_values(array_filter($rows, function ($r) use ($q) {
            $hay = mb_strtolower(
                ($r['product_name'] ?? '')
                . ' ' . ($r['brand'] ?? '')
                . ' ' . ($r['buyer_sku_code'] ?? '')
            );
            return str_contains($hay, mb_strtolower($q));
        }));

        // map hanya field yang diperlukan UI
        $items = array_map(function ($r) {
            return [
                'buyer_sku_code' => (string) ($r['buyer_sku_code'] ?? ''),
                'name' => (string) ($r['product_name'] ?? ''),
                'brand' => (string) ($r['brand'] ?? ''),
                'category' => (string) ($r['category'] ?? ''),
                'price' => (int) ($r['price'] ?? 0), // base price / modal
                'seller_name' => (string) ($r['seller_name'] ?? ''),
                'status' => (string) ($r['status'] ?? ''), // active / gangguan
            ];
        }, array_slice($filtered, 0, 50)); // batasi 50 hasil

        return response()->json(['items' => $items]);
    }

    // Import SKU terpilih menjadi varian produk
    public function importFromDigiflazz(Product $product, Request $request)
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.buyer_sku_code' => ['required', 'string', 'max:120'],
            'items.*.name' => ['required', 'string', 'max:180'],
            'items.*.price' => ['required', 'integer', 'min:0'],
        ]);

        $created = 0;
        $skipped = 0;
        foreach ($data['items'] as $row) {
            $exists = \App\Models\ProductVariant::where('product_id', $product->id)
                ->where('buyer_sku_code', $row['buyer_sku_code'])
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            \App\Models\ProductVariant::create([
                'product_id' => $product->id,
                'buyer_sku_code' => $row['buyer_sku_code'],
                'name' => $row['name'],
                'base_price' => (int) $row['price'],
                'markup_rp' => null,        // pakai markup produk sebagai default
                'is_active' => true,        // aktifkan langsung
            ]);

            $created++;
        }

        return redirect()->route('admin.products.variants.index', $product)
            ->with('ok', "Import selesai: {$created} ditambahkan, {$skipped} dilewati.");
    }

    // Sinkronkan base_price & status varian terhadap pricelist terbaru
    public function syncFromDigiflazz(Product $product, DigiflazzClient $client)
    {
        $rows = $client->pricelist();
        // index by buyer_sku_code untuk cepat
        $bySku = [];
        foreach ($rows as $r) {
            if (!empty($r['buyer_sku_code'])) {
                $bySku[$r['buyer_sku_code']] = $r;
            }
        }

        $updated = 0;
        $disabled = 0;
        foreach ($product->variants as $v) {
            $remote = $bySku[$v->buyer_sku_code] ?? null;
            if (!$remote) {
                // SKU tidak ada → nonaktifkan varian
                if ($v->is_active) {
                    $v->is_active = false;
                    $v->save();
                    $disabled++;
                }
                continue;
            }

            // update base_price & status (gangguan → nonaktifkan)
            $newBase = (int) ($remote['price'] ?? $v->base_price);
            $newActive = (($remote['status'] ?? '') === 'Active');

            $changed = false;
            if ($newBase !== (int) $v->base_price) {
                $v->base_price = $newBase;
                $changed = true;
            }
            if ((bool) $newActive !== (bool) $v->is_active) {
                $v->is_active = $newActive;
                $changed = true;
            }
            if ($changed) {
                $v->save();
                $updated++;
            }
        }

        return redirect()->route('admin.products.variants.index', $product)
            ->with('ok', "Sinkron: {$updated} diperbarui, {$disabled} dinonaktifkan.");
    }

    public function bulkUpdate(Product $product, Request $request)
    {
        $validated = $request->validate([
            'action' => ['required', 'in:set_markup,clear_to_product,activate'],
            'variant_ids' => ['required', 'array', 'min:1'],
            'variant_ids.*' => ['integer'],
            'markup_rp' => ['nullable', 'integer', 'min:0'],
        ]);

        // pastikan hanya varian milik product ini
        $ids = \App\Models\ProductVariant::where('product_id', $product->id)
            ->whereIn('id', $validated['variant_ids'])
            ->pluck('id')->all();

        if (empty($ids)) {
            return back()->with('ok', 'Tidak ada varian yang valid untuk diperbarui.');
        }

        $affected = 0;

        switch ($validated['action']) {
            case 'set_markup':
                $markup = (int) ($validated['markup_rp'] ?? 0);
                $affected = \App\Models\ProductVariant::whereIn('id', $ids)
                    ->update(['markup_rp' => $markup]);
                break;

            case 'clear_to_product':
                $affected = \App\Models\ProductVariant::whereIn('id', $ids)
                    ->update(['markup_rp' => null]);
                break;

            case 'activate':
                $affected = \App\Models\ProductVariant::whereIn('id', $ids)
                    ->update(['is_active' => true]);
                break;
        }

        return back()->with('ok', "Berhasil memperbarui {$affected} varian.");
    }


}
