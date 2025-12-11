<?php

namespace App\Http\Controllers;
use App\Models\MarketplaceProduct; 

use App\Models\Product;

class LandingController extends Controller
{
   public function index()
    {
        // Produk Digiflazz (top up digital)
        $heroProducts = Product::where('is_active', true)
            ->with(['variants' => function ($q) {
                $q->active()->with('digiflazzVariant');
            }])
            ->has('variants')
            ->inRandomOrder()
            ->take(5)
            ->get();

        // Produk Marketplace untuk hero
        $heroMarketplaceProducts = MarketplaceProduct::where('is_active', true)
            ->with(['variants' => function ($q) {
                $q->where('is_active', true)->orderBy('price');
            }])
            ->has('variants')
            ->latest('id')
            ->take(4)
            ->get();

        return view('pages.landing', [
            'heroProducts'            => $heroProducts,
            'heroMarketplaceProducts' => $heroMarketplaceProducts,
        ]);
    }
}
