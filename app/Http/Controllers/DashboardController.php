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
        $userId = auth()->id();

        // 1) Ambil order Digiflazz milik user (ambil lebih dulu, nanti digabung)
        $digiflazz = \App\Models\Order::with(['product', 'variant'])
            ->where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get()
            ->map(function (\App\Models\Order $o) {
                return [
                    'source' => 'digiflazz',
                    'code' => $o->code,
                    'title' => trim(($o->product->name ?? 'Produk Digiflazz') . ' — ' . ($o->variant->name ?? '')),
                    'amount' => $o->total,
                    'status' => $o->status, // success / failed / processing / waiting_payment / ...
                    'created_at' => $o->created_at,
                    'url' => route('orders.show', $o), // invoice Digiflazz
                ];
            });

        // 2) Ambil order Marketplace milik user
        $marketplace = \App\Models\MarketplaceOrder::with(['product', 'variant'])
            ->where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get()
            ->map(function (\App\Models\MarketplaceOrder $o) {
                return [
                    'source' => 'marketplace',
                    'code' => $o->invoice_number,
                    'title' => trim(($o->product->name ?? 'Produk Marketplace') . ' — ' . ($o->variant->name ?? '')),
                    'amount' => $o->total_amount,
                    'status' => $o->status, // not_paid / paid_processing / paid_finished / ...
                    'created_at' => $o->created_at,
                    'url' => route('marketplace.invoice.show', $o->invoice_number),
                ];
            });

        // 3) Gabungkan, urutkan berdasarkan terbaru, ambil 5
        $latestTransactions = $digiflazz
            ->concat($marketplace)
            ->sortByDesc('created_at')
            ->take(5)
            ->values();

        return view('dashboard.user.index', compact('latestTransactions'));
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



    public function orders(\Illuminate\Http\Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));

        $orders = \App\Models\Order::with(['product', 'variant', 'latestPayment'])
            ->where('user_id', auth()->id())
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('code', 'like', "%{$q}%")
                        ->orWhere('target', 'like', "%{$q}%")
                        ->orWhere('buyer_sku_code', 'like', "%{$q}%");
                });
            })
            ->when($status !== '', fn($query) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.user.orders', compact('orders', 'q', 'status'));
    }


    public function marketplaceOrders(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();
        $q = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));

        $orders = \App\Models\MarketplaceOrder::with(['product', 'variant', 'payment'])
            ->where('user_id', $user->id)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('invoice_number', 'like', "%{$q}%")
                        ->orWhere('customer_email', 'like', "%{$q}%")
                        ->orWhere('customer_phone', 'like', "%{$q}%");
                });
            })
            ->when($status !== '', fn($query) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.user.marketplace-orders', compact('orders', 'q', 'status'));
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
