<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceCategory;
use App\Models\MarketplaceProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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
            'products' => $products,
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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'images.*' => ['nullable', 'image', 'max:2048'], // <— baru
        ]);


        $slug = Str::slug($data['name']);
        $original = $slug;
        $i = 1;
        while (MarketplaceProduct::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $i++;
        }

        $data['slug'] = $slug;
        $data['is_active'] = $request->boolean('is_active');
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('marketplace_thumbnails', 'public');
            $data['thumbnail'] = $path;
        }


        $product = MarketplaceProduct::create($data);

        // simpan multiple images kalau ada
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                if (!$file) {
                    continue;
                }

                $path = $file->store('marketplace_images', 'public');

                $product->images()->create([
                    'path' => $path,
                    'sort_order' => $index,
                ]);
            }
        }


        return redirect()
            ->route('admin.marketplace.products.index')
            ->with('ok', 'Produk marketplace berhasil dibuat.');
    }

    public function edit(MarketplaceProduct $product)
    {
        $categories = MarketplaceCategory::orderBy('name')->get();

        return view('dashboard.admin.marketplace.products.edit', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, MarketplaceProduct $product)
    {
        $data = $request->validate([
            'marketplace_category_id' => ['required', 'exists:marketplace_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'images.*' => ['nullable', 'image'], // <— baru
        ]);



        $product->marketplace_category_id = $data['marketplace_category_id'];
        $product->name = $data['name'];
        $product->description = $data['description'] ?? null;
        $product->is_active = $request->boolean('is_active');

        if ($request->hasFile('thumbnail')) {
            // hapus file lama kalau ada
            if ($product->thumbnail) {
                Storage::disk('public')->delete($product->thumbnail);
            }

            $path = $request->file('thumbnail')->store('marketplace_thumbnails', 'public');
            $product->thumbnail = $path;
        }
        // tambahkan gambar baru (tidak menghapus yang lama dulu)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                if (!$file) {
                    continue;
                }

                $path = $file->store('marketplace_images', 'public');

                $product->images()->create([
                    'path' => $path,
                    'sort_order' => $index,
                ]);
            }
        }



        $product->save();

        return redirect()
            ->route('admin.marketplace.products.index')
            ->with('ok', 'Produk marketplace berhasil diperbarui.');
    }

    public function destroy(MarketplaceProduct $product)
    {
        // (Opsional tapi sangat disarankan)
        // cegah hapus kalau sudah ada order marketplace biar histori tidak hilang
        if ($product->orders()->exists()) {
            return back()->with('error', 'Produk tidak bisa dihapus karena sudah memiliki order.');
        }

        // hapus thumbnail jika ada
        if ($product->thumbnail) {
            Storage::disk('public')->delete($product->thumbnail);
        }

        // hapus semua file images (records akan ikut kehapus via cascade)
        $product->load('images');
        foreach ($product->images as $img) {
            if ($img->path) {
                Storage::disk('public')->delete($img->path);
            }
        }

        $product->delete();

        return redirect()
            ->route('admin.marketplace.products.index')
            ->with('ok', 'Produk marketplace berhasil dihapus.');
    }

}
