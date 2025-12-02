<?php

// app/Http/Controllers/Marketplace/MarketplaceController.php
namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceProduct;
use App\Models\MarketplaceOrder;

class MarketplaceController extends Controller
{
    public function index()
    {
        $products = MarketplaceProduct::with('category')
            ->where('is_active', true)
            ->whereHas('variants', fn($q) => $q->where('is_active', true))
            ->get();

        return view('marketplace.index', compact('products'));
    }

    public function show(MarketplaceProduct $product)
    {
        $product->load(['category', 'variants' => fn($q) => $q->where('is_active', true)]);
        return view('marketplace.show', compact('product'));
    }

    public function invoice(MarketplaceOrder $order)
    {
        $order->load(['product', 'variant', 'payment', 'user']);
        return view('marketplace.invoice', compact('order'));
    }
}

