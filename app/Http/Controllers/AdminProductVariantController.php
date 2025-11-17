<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\DigiflazzClient;
use Illuminate\Support\Arr;
use App\Models\DigiflazzVariant;




class AdminProductVariantController extends Controller
{
    // List varian per produk + form tambah cepat
    public function index(Product $product, Request $request)
    {
        $pp = (int) $request->input('per_page', 20);
        $q = trim($request->input('q', ''));

        $variantsQuery = $product->variants()
            ->with(['product', 'digiflazzVariant'])   // <— penting
            ->orderBy('buyer_sku_code');

        if ($q !== '') {
            $like = "%{$q}%";
            $variantsQuery->where(function ($query) use ($like) {
                $query->where('buyer_sku_code', 'like', $like)
                    ->orWhere('name', 'like', $like);
            });
        }

        $variants = $variantsQuery->paginate($pp)->withQueryString();

        return view('dashboard.admin.products.variants.index', [
            'product' => $product,
            'variants' => $variants,
            'q' => $q,
            'pp' => $pp,
        ]);
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
    public function searchDigiflazz(Request $request, Product $product)
    {
        $search = trim($request->input('q', ''));

        $query = DigiflazzVariant::query();

        if ($search !== '') {
            $like = "%{$search}%";

            $query->where(function ($q) use ($like) {
                $q->where('product_name', 'like', $like)
                    ->orWhere('brand', 'like', $like)
                    ->orWhere('buyer_sku_code', 'like', $like);
            });
        }

        $variants = $query
            ->orderBy('brand')
            ->orderBy('product_name')
            ->limit(100)
            ->get();

        // KIRIM ARRAY LANGSUNG, TANPA BUNGKUS "data"
        return response()->json([
            'items' => $variants->map(function (DigiflazzVariant $v) {
                return [
                    'id' => $v->id,
                    'buyer_sku_code' => $v->buyer_sku_code,
                    'name' => $v->product_name,
                    'brand' => $v->brand,
                    'category' => $v->category,
                    'price' => $v->base_price,
                    'status' => $v->status,
                ];
            })->values(),
        ]);

    }


    public function importFromDigiflazz(Request $request, Product $product)
    {
        $items = $request->input('items', []);

        if (!is_array($items)) {
            return back()->with('error', 'Data import tidak valid.');
        }

        $created = 0;

        foreach ($items as $item) {
            $masterId = $item['digiflazz_variant_id'] ?? $item['id'] ?? null;
            if (!$masterId) {
                continue;
            }

            /** @var \App\Models\DigiflazzVariant|null $master */
            $master = DigiflazzVariant::find($masterId);
            if (!$master) {
                continue;
            }

            $alreadyExists = ProductVariant::where('product_id', $product->id)
                ->where('digiflazz_variant_id', $master->id)
                ->exists();

            if ($alreadyExists) {
                continue;
            }

            ProductVariant::create([
                'product_id' => $product->id,
                'digiflazz_variant_id' => $master->id,
                'buyer_sku_code' => $master->buyer_sku_code,
                'name' => $master->product_name,
                // TIDAK ADA base_price di sini
                'markup_rp' => null,
                'is_active' => true,
            ]);

            $created++;
        }

        if ($created === 0) {
            return back()->with('warning', 'Tidak ada varian baru yang diimport.');
        }

        return back()->with('success', "{$created} varian berhasil diimport dari Digiflazz.");
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
