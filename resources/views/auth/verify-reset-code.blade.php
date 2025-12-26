@extends('layouts.app')

@section('title', 'Verifikasi Kode — MaitriProject')
@section('meta_description', 'Masukkan kode verifikasi reset password.')
@section('page', 'auth')

@section('content')
<section class="py-12">
  <div class="mx-auto max-w-[520px] px-4">
    <a href="{{ route('password.forgot') }}" class="text-sm text-slate-400 hover:text-slate-200">← Kembali</a>

    <div class="mt-4 rounded-[20px] border border-slate-800/70 bg-[#111826] p-6 md:p-8 shadow-xl shadow-black/10">
      <h1 class="text-xl font-semibold">Verifikasi Kode</h1>
      <p class="mt-1 text-sm text-slate-400">
        Masukkan kode 6 digit yang kami kirim ke <span class="text-slate-200 font-medium">{{ $email }}</span>.
      </p>

      @if(session('success'))
        <div class="mt-4 rounded-xl border border-emerald-500/30 bg-emerald-500/10 text-emerald-200 text-sm p-3">
          {{ session('success') }}
        </div>
      @endif

      @if ($errors->any())
        <div class="mt-4 rounded-xl border border-red-900/40 bg-red-950/30 text-red-200 text-sm p-3">
          {{ $errors->first() }}
        </div>
      @endif

      <form class="mt-6 space-y-5" method="post" action="{{ route('password.forgot.verify.post') }}">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">

        <div>
          <label class="block text-sm text-slate-300">Kode Verifikasi</label>
          <input name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6" placeholder="123456" required
                 class="mt-1 h-11 w-full rounded-xl bg-[#0E1524] border border-slate-700/60 px-3 text-[15px] tracking-[0.3em] text-center outline-none placeholder:text-slate-500 focus:border-violet-500/70 focus:ring-2 focus:ring-violet-500/30">
          <p class="mt-2 text-xs text-slate-500">Kode berlaku 15 menit.</p>
        </div>

        <button type="submit"
                class="w-full h-11 rounded-2xl bg-violet-600 hover:bg-violet-500 transition font-medium">
          Lanjut Reset Password
        </button>
      </form>
    </div>
  </div>
</section>
@endsection
