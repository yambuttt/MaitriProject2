<?php

namespace App\Http\Controllers;

use App\Models\Product;

class LandingController extends Controller
{
    public function index()
    {
        // Ambil 5 produk aktif secara acak + varian aktifnya
        $heroProducts = Product::where('is_active', true)
            ->with(['variants' => function ($q) {
                $q->active()->with('digiflazzVariant');
            }])
            ->has('variants')
            ->inRandomOrder()
            ->take(5)
            ->get();

        return view('pages.landing', [
            'heroProducts' => $heroProducts,
        ]);
    }
}
