<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderRefund;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminRefundController extends Controller
{
    public function index(Request $request)
    {
        $q  = trim((string) $request->input('q', ''));
        $pp = (int) $request->input('per_page', 12);
        $pp = $pp > 0 && $pp <= 100 ? $pp : 12;

        $refunds = OrderRefund::query()
            ->with(['order.product', 'order.variant', 'admin', 'targetUser'])
            ->when($q !== '', function ($qb) use ($q) {
                $qb->whereHas('order', function ($o) use ($q) {
                    $o->where('code', 'like', "%{$q}%");
                })->orWhereHas('admin', function ($u) use ($q) {
                    $u->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%");
                })->orWhereHas('targetUser', function ($u) use ($q) {
                    $u->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate($pp)
            ->withQueryString();

        return view('dashboard.admin.refunds.index', [
            'refunds' => $refunds,
            'q' => $q,
            'perPage' => $pp,
        ]);
    }

    public function create()
    {
        return view('dashboard.admin.refunds.create');
    }

    /**
     * AJAX check kode order, biar admin yakin ini memang FAILED + PAID + belum refunded.
     */
    public function check(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50'],
        ]);

        $code = strtoupper(trim($data['code']));

        $order = Order::with(['product', 'variant', 'user', 'refund'])->where('code', $code)->first();

        if (!$order) {
            return response()->json([
                'ok' => true,
                'eligible' => false,
                'message' => 'Order tidak ditemukan.',
            ]);
        }

        // rule: hanya order FAILED + PAID + belum REFUNDED
        $alreadyRefunded = !is_null($order->refunded_at) || $order->status === 'refunded' || $order->payment_status === 'refunded' || $order->refund;
        $eligible = ($order->status === 'failed')
            && (($order->payment_status ?? null) === 'paid')
            && !$alreadyRefunded;

        return response()->json([
            'ok' => true,
            'eligible' => $eligible,
            'message' => $eligible
                ? 'Valid: order FAILED & PAID. Bisa diproses refund.'
                : 'Tidak valid untuk refund. Pastikan status FAILED, payment PAID, dan belum pernah refund.',
            'order' => [
                'code' => $order->code,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'payment_method' => $order->payment_method,
                'total' => (int) $order->total,
                'product' => $order->product?->name,
                'variant' => $order->variant?->name,
                'email' => $order->email,
                'phone' => $order->phone,
                'target' => $order->target,
                'order_user_id' => $order->user_id,
                'order_user_email' => $order->user?->email,
                'order_user_name' => $order->user?->name,
            ],
        ]);
    }

    /**
     * AJAX search user untuk dropdown target refund.
     */
    public function searchUsers(Request $request)
    {
        $q = trim((string) $request->input('q', ''));

        if ($q === '' || strlen($q) < 2) {
            return response()->json(['ok' => true, 'items' => []]);
        }

        $users = User::query()
            ->where(function ($qb) use ($q) {
                $qb->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            })
            ->orderBy('name')
            ->limit(15)
            ->get(['id', 'name', 'email']);

        return response()->json([
            'ok' => true,
            'items' => $users->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'refund_method' => ['required', 'in:wallet,manual_transfer'],
            'target_user_id' => ['nullable', 'exists:users,id'],
            'manual_proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $code = strtoupper(trim($data['code']));
        $admin = $request->user();

        return DB::transaction(function () use ($data, $code, $admin, $request) {
            /** @var Order $order */
            $order = Order::with(['refund'])->where('code', $code)->lockForUpdate()->firstOrFail();

            // Validasi lagi di server (anti tamper)
            $alreadyRefunded = !is_null($order->refunded_at) || $order->status === 'refunded' || $order->payment_status === 'refunded' || $order->refund;
            $eligible = ($order->status === 'failed') && (($order->payment_status ?? null) === 'paid') && !$alreadyRefunded;

            if (!$eligible) {
                return back()->withErrors([
                    'code' => 'Order tidak valid untuk refund. Pastikan FAILED + PAID + belum refunded.',
                ])->withInput();
            }

            $amount = (int) ($order->total ?? 0);
            if ($amount <= 0) {
                return back()->withErrors(['code' => 'Total order tidak valid.'])->withInput();
            }

            $refundMethod = $data['refund_method'];

            $targetUserId = null;
            $proofPath = null;

            if ($refundMethod === 'wallet') {
                // wajib pilih target user
                if (empty($data['target_user_id'])) {
                    return back()->withErrors(['target_user_id' => 'Wajib pilih target user untuk refund ke Saldo Maitri.'])
                        ->withInput();
                }

                $targetUserId = (int) $data['target_user_id'];

                // tambah saldo target user (pakai helper yang sudah ada di User: incrementBalance)
                $target = User::where('id', $targetUserId)->lockForUpdate()->firstOrFail();
                $target->incrementBalance(
                    $amount,
                    "Refund admin untuk order {$order->code}"
                );

                // update order menjadi refunded
                $order->update([
                    'status' => 'refunded',
                    'payment_status' => 'refunded',
                    'refunded_at' => now(),
                    'refund_amount' => $amount,
                    'refund_reason' => 'Refund oleh admin (wallet)',
                    'refunded_to_user_id' => $target->id,
                    'profit' => 0,
                ]);
            }

            if ($refundMethod === 'manual_transfer') {
                // wajib upload bukti transfer
                if (!$request->hasFile('manual_proof')) {
                    return back()->withErrors(['manual_proof' => 'Wajib upload bukti transfer untuk refund manual.'])
                        ->withInput();
                }

                $proofPath = $request->file('manual_proof')->store('refunds', 'public');

                // update order menjadi refunded (tanpa tambah saldo)
                $order->update([
                    'status' => 'refunded',
                    'payment_status' => 'refunded',
                    'refunded_at' => now(),
                    'refund_amount' => $amount,
                    'refund_reason' => 'Refund oleh admin (manual transfer)',
                    'refunded_to_user_id' => null,
                    'profit' => 0,
                ]);
            }

            // simpan audit record
            OrderRefund::create([
                'order_id' => $order->id,
                'admin_id' => $admin->id,
                'refund_method' => $refundMethod,
                'amount' => $amount,
                'target_user_id' => $targetUserId,
                'manual_proof_path' => $proofPath,
                'note' => $data['note'] ?? null,
            ]);

            return redirect()->route('admin.refunds.index')->with('success', 'Refund berhasil diproses.');
        });
    }
}
