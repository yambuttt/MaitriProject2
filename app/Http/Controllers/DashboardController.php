<?php

namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\MarketplaceOrder;

class DashboardController extends Controller
{
    public function user()
    {
        return view('dashboard.user.index');   // ← path barumu
    }

    public function admin()
    {
        return view('dashboard.admin.index');  // ← path barumu
    }

    public function orders()
    {
        $orders = Order::where('user_id', auth()->id())
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('dashboard.user.orders', compact('orders'));
    }

    public function marketplaceOrders()
    {
        $user = auth()->user();

        $orders = MarketplaceOrder::with(['product', 'variant', 'payment'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('dashboard.user.marketplace-orders', compact('orders'));
    }

}
