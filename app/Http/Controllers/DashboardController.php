<?php

namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\MarketplaceOrder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function user()
    {
        return view('dashboard.user.index');   // ← path barumu
    }

    public function admin(Request $request)
    {
        $perPage = 10;
        $page = max((int) $request->query('page', 1), 1);

        // 1) Ambil order Digiflazz
        $productOrders = Order::with(['product', 'variant', 'user'])
            ->latest()
            ->take(200) // batas aman buat dashboard
            ->get()
            ->map(function (Order $order) {
                return [
                    'source' => 'digiflazz',
                    'code' => $order->code,                      // MP-00001
                    'product' => $order->product->name ?? 'Produk Digiflazz',
                    'variant' => $order->variant->name ?? '-',
                    'customer' => $order->email ?? $order->user?->email ?? '-',
                    'total' => $order->total,
                    'status' => $order->status,                  // success / failed / processing / ...
                    'created_at' => $order->created_at,
                ];
            });

        // 2) Ambil order Marketplace
        $marketplaceOrders = MarketplaceOrder::with(['product', 'variant', 'user'])
            ->latest()
            ->take(200)
            ->get()
            ->map(function (MarketplaceOrder $order) {
                return [
                    'source' => 'marketplace',
                    'code' => $order->invoice_number,            // MPM-00001
                    'product' => $order->product->name ?? 'Produk Marketplace',
                    'variant' => $order->variant->name ?? '-',
                    'customer' => $order->customer_email ?? $order->user?->email ?? '-',
                    'total' => $order->total_amount,
                    'status' => $order->status,                  // not_paid / paid_finished / ...
                    'created_at' => $order->created_at,
                ];
            });

        // 3) Gabung & sort desc berdasarkan waktu
        $all = $productOrders
            ->concat($marketplaceOrders)
            ->sortByDesc('created_at')
            ->values();

        $total = $all->count();
        $items = $all->slice(($page - 1) * $perPage, $perPage)->values();

        $orders = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => route('admin.dashboard')]
        );

        // Kalau request AJAX (buat tombol Next), kirim partial
        if ($request->query('ajax') == 1) {
            return response()->json([
                'html' => view('dashboard.admin.partials.latest-orders-rows', [
                    'orders' => $orders,
                ])->render(),
                'next_page_url' => $orders->hasMorePages()
                    ? $orders->nextPageUrl()
                    : null,
            ]);
        }

        return view('dashboard.admin.index', [
            'orders' => $orders,
        ]);
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

    public function searchOrderByCode(Request $request)
    {
        $code = trim($request->query('code', ''));

        if ($code === '') {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'Kode order harus diisi.');
        }

        // Normalisasi, misal user tulis huruf kecil
        $code = strtoupper($code);

        // Kalau MPM-xxx -> marketplace
        if (Str::startsWith($code, 'MPM-')) {
            $order = MarketplaceOrder::where('invoice_number', $code)->first();

            if (!$order) {
                return redirect()
                    ->route('admin.dashboard')
                    ->with('error', "Order marketplace dengan kode {$code} tidak ditemukan.");
            }

            return redirect()->route('admin.marketplace.orders.show', $order);
        }

        // Default: anggap MP-xxxx = order Digiflazz
        $order = Order::where('code', $code)->first();

        if (!$order) {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', "Order dengan kode {$code} tidak ditemukan.");
        }

        // kamu bisa buat halaman admin khusus, tapi untuk sekarang pakai invoice umum
        return redirect()->route('orders.show', $order);
    }

    public function orderDetail(string $code)
    {
        $code = strtoupper($code);

        if (Str::startsWith($code, 'MPM-')) {
            // Marketplace
            $order = MarketplaceOrder::with(['product', 'variant', 'user', 'payment'])
                ->where('invoice_number', $code)
                ->firstOrFail();

            $type = 'marketplace';
        } else {
            // Digiflazz
            $order = Order::with(['product', 'variant', 'user', 'payments'])
                ->where('code', $code)
                ->firstOrFail();

            $type = 'digiflazz';
        }

        // return HTML partial untuk modal
        return view('dashboard.admin.partials.order-detail-modal', [
            'order' => $order,
            'type' => $type,
        ]);
    }


}
