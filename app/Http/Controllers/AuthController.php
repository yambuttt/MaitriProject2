<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showLogin() { return view('auth.login'); }
    public function showRegister() { return view('auth.register'); }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required','string','max:100'],
            'email'    => ['required','email','max:255', Rule::unique('users','email')],
            'password' => ['required','string','min:8','confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email'=> $data['email'],
            'password' => $data['password'], // auto hashed via cast
            'role' => 'member', // default
        ]);

        Auth::login($user);
        return to_route($user->isAdmin() ? 'admin.dashboard' : 'user.dashboard');
    }

    public function login(Request $request)
    {
        $cred = $request->validate([
            'email' => ['required','email'],
            'password' => ['required','string'],
            'remember' => ['nullable','boolean'],
        ]);

        if (!Auth::attempt(['email'=>$cred['email'],'password'=>$cred['password']], $request->boolean('remember'))) {
            return back()->withErrors(['email'=>'Email atau kata sandi salah.'])->withInput();
        }

        $request->session()->regenerate();
        $user = auth()->user();
        return to_route($user->isAdmin() ? 'admin.dashboard' : 'user.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return to_route('landing');
    }
}
