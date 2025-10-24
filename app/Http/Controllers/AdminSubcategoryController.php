<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminSubcategoryController extends Controller
{
    public function index(Request $request)
    {
        $q     = trim((string) $request->query('q', ''));
        $catId = $request->query('category_id');
        $pp    = (int) $request->query('per_page', 12);
        $pp    = $pp > 0 && $pp <= 100 ? $pp : 12;

        $subs = Subcategory::query()
            ->with('category:id,name')
            ->when($q !== '', fn($qb) =>
                $qb->where(function($w) use ($q) {
                    $w->where('name','like',"%{$q}%")
                      ->orWhere('slug','like',"%{$q}%");
                }))
            ->when($catId, fn($qb) => $qb->where('category_id', $catId))
            ->orderBy('name')
            ->paginate($pp)
            ->withQueryString();

        $categories = Category::orderBy('name')->get(['id','name']);

        return view('dashboard.admin.subcategories.index', compact('subs','q','pp','categories','catId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['required','exists:categories,id'],
            'name'        => ['required','string','max:120'],
            'slug'        => ['nullable','string','max:140',
                Rule::unique('subcategories','slug')->where(fn($q) => $q->where('category_id', $request->input('category_id')))
            ],
            'is_active'   => ['nullable','boolean'],
        ]);
        $data['is_active'] = (bool) ($data['is_active'] ?? true);

        Subcategory::create($data);

        return redirect()->route('admin.subcategories.index', ['category_id' => $data['category_id']])
            ->with('ok','Subkategori berhasil dibuat.');
    }

    public function edit(Subcategory $subcategory)
    {
        $categories = Category::orderBy('name')->get(['id','name']);
        return view('dashboard.admin.subcategories.edit', compact('subcategory','categories'));
    }

    public function update(Request $request, Subcategory $subcategory)
    {
        $data = $request->validate([
            'category_id' => ['required','exists:categories,id'],
            'name'        => ['required','string','max:120'],
            'slug'        => ['nullable','string','max:140',
                Rule::unique('subcategories','slug')
                    ->ignore($subcategory->id)
                    ->where(fn($q) => $q->where('category_id', $request->input('category_id')))
            ],
            'is_active'   => ['nullable','boolean'],
        ]);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        $subcategory->update($data);

        return redirect()->route('admin.subcategories.index', ['category_id' => $data['category_id']])
            ->with('ok','Subkategori berhasil diperbarui.');
    }

    public function toggle(Subcategory $subcategory)
    {
        $subcategory->update(['is_active' => ! $subcategory->is_active]);
        return back()->with('ok','Status subkategori diperbarui.');
    }

    public function destroy(Subcategory $subcategory)
    {
        $subcategory->delete();
        return back()->with('ok','Subkategori dihapus.');
    }
}
