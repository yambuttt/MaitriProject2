<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminCategoryController extends Controller
{
    // List + search + paginate
    public function index(Request $request)
    {
        $q  = trim((string) $request->query('q', ''));
        $pp = (int) $request->query('per_page', 12);
        $pp = $pp > 0 && $pp <= 100 ? $pp : 12;

        $categories = Category::query()
            ->when($q !== '', fn($qb) =>
                $qb->where('name', 'like', "%{$q}%")
                   ->orWhere('slug', 'like', "%{$q}%"))
            ->orderBy('name')
            ->paginate($pp)
            ->withQueryString();

        return view('dashboard.admin.categories.index', compact('categories','q','pp'));
    }

    // Create
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => ['required','string','max:120'],
            'slug'      => ['nullable','string','max:140','unique:categories,slug'],
            'is_active' => ['nullable','boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? true);

        Category::create($data);

        return redirect()->route('admin.categories.index')
            ->with('ok', 'Kategori berhasil dibuat.');
    }

    // Edit form
    public function edit(Category $category)
    {
        return view('dashboard.admin.categories.edit', compact('category'));
    }

    // Update
    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'      => ['required','string','max:120'],
            'slug'      => ['nullable','string','max:140', Rule::unique('categories','slug')->ignore($category->id)],
            'is_active' => ['nullable','boolean'],
        ]);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        $category->update($data);

        return redirect()->route('admin.categories.index')
            ->with('ok', 'Kategori berhasil diperbarui.');
    }

    // Toggle aktif/nonaktif (quick action)
    public function toggle(Category $category)
    {
        $category->update(['is_active' => ! $category->is_active]);
        return back()->with('ok', 'Status kategori diperbarui.');
    }

    // Delete
    public function destroy(Category $category)
    {
        $category->delete();
        return back()->with('ok', 'Kategori dihapus.');
    }
}
