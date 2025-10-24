<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $q     = trim((string) $request->input('q', ''));
        $role  = $request->input('role'); // null | 'member' | 'admin'
        $pp    = (int) $request->input('per_page', 12);
        $pp    = $pp > 0 && $pp <= 100 ? $pp : 12;

        $users = User::query()
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%")
                      ->orWhere('id', $q);
                });
            })
            ->when(in_array($role, ['member','admin'], true), fn($qb) => $qb->where('role', $role))
            ->orderByDesc('created_at')
            ->paginate($pp)
            ->withQueryString();

        return view('dashboard.admin.users.index', [
            'users' => $users,
            'q'     => $q,
            'role'  => $role,
            'perPage' => $pp,
        ]);
    }
}
