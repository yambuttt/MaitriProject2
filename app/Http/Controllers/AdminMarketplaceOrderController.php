<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceOrder;
use Illuminate\Http\Request;

class AdminMarketplaceOrderController extends Controller
{
    /**
     * List semua pesanan marketplace.
     */
    public function index(Request $request)
    {
        $status = $request->query('status'); // not_paid, paid_received, dll
        $q      = trim((string) $request->query('q', ''));

        $orders = MarketplaceOrder::with(['product', 'variant', 'user', 'payment'])
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($q, function ($query) use ($q) {
                $query->where(function ($q2) use ($q) {
                    $q2->where('invoice_number', 'like', "%{$q}%")
                        ->orWhere('customer_email', 'like', "%{$q}%")
                        ->orWhere('customer_phone', 'like', "%{$q}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('dashboard.admin.marketplace.orders.index', [
            'orders' => $orders,
            'status' => $status,
            'q'      => $q,
        ]);
    }

    /**
     * Detail 1 pesanan marketplace.
     */
    public function show(MarketplaceOrder $order)
    {
        $order->load(['product', 'variant', 'user', 'payment']);

        return view('dashboard.admin.marketplace.orders.show', [
            'order' => $order,
        ]);
    }

    /**
     * Update status pesanan + catatan admin.
     */
    public function updateStatus(Request $request, MarketplaceOrder $order)
    {
        $data = $request->validate([
            'status'     => ['required', 'in:paid_received,paid_processing,paid_rejected,paid_finished'],
            'admin_note' => ['nullable', 'string'],
        ]);

        $order->status = $data['status'];
        $order->admin_note = $data['admin_note'] ?? null;
        $order->processed_by_admin_id = auth()->id();

        if ($data['status'] === 'paid_rejected') {
            $order->rejected_at = now();
        } elseif ($data['status'] === 'paid_finished') {
            $order->finished_at = now();
        }

        $order->save();

        return back()->with('ok', 'Status pesanan berhasil diperbarui.');
    }
}
