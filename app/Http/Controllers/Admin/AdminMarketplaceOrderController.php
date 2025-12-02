<?php

// app/Http/Controllers/Admin/AdminMarketplaceOrderController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceOrder;
use Illuminate\Http\Request;

class AdminMarketplaceOrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status'); // filter

        $orders = MarketplaceOrder::with('product', 'variant', 'user')
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20);

        return view('admin.marketplace.orders.index', compact('orders', 'status'));
    }

    public function show(MarketplaceOrder $order)
    {
        $order->load('product', 'variant', 'user', 'payment');
        return view('admin.marketplace.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, MarketplaceOrder $order)
    {
        $data = $request->validate([
            'status'     => ['required', 'in:paid_received,paid_processing,paid_rejected,paid_finished'],
            'admin_note' => ['nullable', 'string'],
        ]);

        $order->status = $data['status'];
        $order->admin_note = $data['admin_note'] ?? $order->admin_note;
        $order->processed_by_admin_id = auth()->id();

        if ($data['status'] === 'paid_rejected') {
            $order->rejected_at = now();
        } elseif ($data['status'] === 'paid_finished') {
            $order->finished_at = now();
        }

        $order->save();

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}

