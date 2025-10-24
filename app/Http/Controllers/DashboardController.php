<?php

namespace App\Http\Controllers;

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
}
