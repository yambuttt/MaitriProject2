<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $q   = trim((string)$request->query('q',''));
        $cat = $request->query('category_id');
        $sub = $request->query('subcategory_id');
        $pp  = (int)$request->query('per_page', 12);
        $pp  = $pp > 0 && $pp <= 100 ? $pp : 12;

        $products = Product::query()
            ->with(['category:id,name','subcategory:id,name'])
            ->when($q !== '', fn($qb) =>
                $qb->where(function($w) use ($q){
                    $w->where('name','like',"%{$q}%")
                      ->orWhere('slug','like',"%{$q}%")
                      ->orWhere('provider','like',"%{$q}%");
                })
            )
            ->when($cat, fn($qb) => $qb->where('category_id', $cat))
            ->when($sub, fn($qb) => $qb->where('subcategory_id', $sub))
            ->orderBy('name')
            ->paginate($pp)
            ->withQueryString();

        $categories   = Category::orderBy('name')->get(['id','name']);
        $subcategories= $cat ? Subcategory::where('category_id',$cat)->orderBy('name')->get(['id','name','category_id']) : collect();

        return view('dashboard.admin.products.index', compact('products','q','pp','categories','subcategories','cat','sub'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get(['id','name']);
        return view('dashboard.admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id'    => ['required','exists:categories,id'],
            'subcategory_id' => [
                'required',
                Rule::exists('subcategories','id')->where(fn($q) => $q->where('category_id', $request->input('category_id')))
            ],
            'name'        => ['required','string','max:160'],
            'slug'        => ['nullable','string','max:180','unique:products,slug'],
            'provider'    => ['nullable','string','max:120'],
            'markup_rp'   => ['required','integer','min:0'],
            'is_active'   => ['nullable','boolean'],
            'description' => ['nullable','string'],
        ]);
        $data['is_active'] = (bool)($data['is_active'] ?? true);

        Product::create($data);

        return redirect()->route('admin.products.index')->with('ok','Produk berhasil dibuat.');
    }

    public function edit(Product $product)
    {
        $categories    = Category::orderBy('name')->get(['id','name']);
        $subcategories = Subcategory::where('category_id',$product->category_id)->orderBy('name')->get(['id','name','category_id']);
        return view('dashboard.admin.products.edit', compact('product','categories','subcategories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'category_id'    => ['required','exists:categories,id'],
            'subcategory_id' => [
                'required',
                Rule::exists('subcategories','id')->where(fn($q) => $q->where('category_id', $request->input('category_id')))
            ],
            'name'        => ['required','string','max:160'],
            'slug'        => ['nullable','string','max:180', Rule::unique('products','slug')->ignore($product->id)],
            'provider'    => ['nullable','string','max:120'],
            'markup_rp'   => ['required','integer','min:0'],
            'is_active'   => ['nullable','boolean'],
            'description' => ['nullable','string'],
        ]);
        $data['is_active'] = (bool)($data['is_active'] ?? false);

        $product->update($data);

        return redirect()->route('admin.products.index')->with('ok','Produk diperbarui.');
    }

    public function toggle(Product $product)
    {
        $product->update(['is_active' => ! $product->is_active]);
        return back()->with('ok','Status produk diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('ok','Produk dihapus.');
    }

    // JSON helper untuk dependent dropdown
    public function subcategoriesByCategory(Category $category)
    {
        return response()->json(
            Subcategory::where('category_id',$category->id)
                ->orderBy('name')
                ->get(['id','name','category_id'])
        );
    }
}
