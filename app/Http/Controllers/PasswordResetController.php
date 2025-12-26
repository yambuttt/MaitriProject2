<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetCodeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
    // berlaku 15 menit
    private int $expiryMinutes = 15;

    public function showRequest()
    {
        return view('auth.forgot-password');
    }

    public function sendCode(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $email = strtolower(trim($data['email']));
        $user = User::where('email', $email)->first();

        // ✅ cek email terdaftar
        if (!$user) {
            return back()->withErrors(['email' => 'Email belum terdaftar.'])->withInput();
        }

        // generate kode 6 digit
        $code = (string) random_int(100000, 999999);

        // simpan hash kode ke password_reset_tokens (primary: email)
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($code),
                'created_at' => now(),
            ]
        );

        // kirim email
        Mail::to($email)->send(new PasswordResetCodeMail($code));

        // lanjut ke halaman verifikasi kode
        return redirect()->route('password.forgot.verify', ['email' => $email])
            ->with('success', 'Kode verifikasi sudah dikirim ke email kamu.');
    }

    public function showVerify(Request $request)
    {
        $email = strtolower(trim((string) $request->query('email', '')));
        if (!$email) {
            return redirect()->route('password.forgot');
        }

        return view('auth.verify-reset-code', [
            'email' => $email,
        ]);
    }

    public function verifyCode(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'digits:6'],
        ]);

        $email = strtolower(trim($data['email']));
        $code  = trim($data['code']);

        $row = DB::table('password_reset_tokens')->where('email', $email)->first();
        if (!$row) {
            return back()->withErrors(['code' => 'Kode tidak valid atau sudah kadaluarsa.'])->withInput();
        }

        // cek expiry
        $created = $row->created_at ? \Carbon\Carbon::parse($row->created_at) : null;
        if (!$created || $created->addMinutes($this->expiryMinutes)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return back()->withErrors(['code' => 'Kode sudah kadaluarsa. Silakan minta kode baru.']);
        }

        // verify hash
        if (!Hash::check($code, $row->token)) {
            return back()->withErrors(['code' => 'Kode salah.'])->withInput();
        }

        // tandai session bahwa email ini sudah lolos OTP
        $request->session()->put('pw_reset_email', $email);
        $request->session()->put('pw_reset_verified_at', now()->timestamp);

        return redirect()->route('password.reset');
    }

    public function showReset(Request $request)
    {
        $email = $request->session()->get('pw_reset_email');
        $verifiedAt = (int) ($request->session()->get('pw_reset_verified_at') ?? 0);

        // wajib sudah verify dan masih fresh
        if (!$email || !$verifiedAt || now()->timestamp - $verifiedAt > ($this->expiryMinutes * 60)) {
            return redirect()->route('password.forgot')->withErrors([
                'email' => 'Sesi reset tidak valid / kadaluarsa. Silakan minta kode lagi.',
            ]);
        }

        return view('auth.reset-password', ['email' => $email]);
    }

    public function resetPassword(Request $request)
    {
        $email = (string) $request->session()->get('pw_reset_email');
        $verifiedAt = (int) ($request->session()->get('pw_reset_verified_at') ?? 0);

        if (!$email || !$verifiedAt || now()->timestamp - $verifiedAt > ($this->expiryMinutes * 60)) {
            return redirect()->route('password.forgot')->withErrors([
                'email' => 'Sesi reset tidak valid / kadaluarsa. Silakan minta kode lagi.',
            ]);
        }

        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::where('email', $email)->first();
        if (!$user) {
            return redirect()->route('password.forgot')->withErrors([
                'email' => 'Email tidak ditemukan.',
            ]);
        }

        $user->password = $data['password']; // user kamu sudah auto-hash via cast di model (dipakai di register) :contentReference[oaicite:2]{index=2}
        $user->save();

        // hapus token
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        // clear session reset
        $request->session()->forget(['pw_reset_email', 'pw_reset_verified_at']);

        return redirect()->route('login')->with('success', 'Password berhasil direset. Silakan login.');
    }
}
