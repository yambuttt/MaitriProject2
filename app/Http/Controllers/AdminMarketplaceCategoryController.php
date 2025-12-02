<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminMarketplaceCategoryController extends Controller
{
    public function index()
    {
        $categories = MarketplaceCategory::orderBy('name')->get();

        return view('dashboard.admin.marketplace.categories.index', [
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        return view('dashboard.admin.marketplace.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        $slug = Str::slug($data['name']);

        // pastikan slug unik
        $original = $slug;
        $i = 1;
        while (MarketplaceCategory::where('slug', $slug)->exists()) {
            $slug = $original.'-'.$i++;
        }

        $data['slug'] = $slug;
        $data['is_active'] = $request->boolean('is_active');

        MarketplaceCategory::create($data);

        return redirect()
            ->route('admin.marketplace.categories.index')
            ->with('ok', 'Kategori marketplace berhasil dibuat.');
    }

    public function edit(MarketplaceCategory $category)
    {
        return view('dashboard.admin.marketplace.categories.edit', [
            'category' => $category,
        ]);
    }

    public function update(Request $request, MarketplaceCategory $category)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        $category->name = $data['name'];
        $category->description = $data['description'] ?? null;
        $category->is_active = $request->boolean('is_active');

        $category->save();

        return redirect()
            ->route('admin.marketplace.categories.index')
            ->with('ok', 'Kategori marketplace berhasil diperbarui.');
    }
}
