@extends('layouts.app')

@section('title', 'Masuk — MaitriProject')
@section('meta_description', 'Masuk ke akun MaitriProject.')
@section('page', 'auth')

@push('head')
<style>
  .auth-bg {
    background-color: #03050a;
    background-image: 
      radial-gradient(circle at 50% 10%, rgba(124, 58, 237, 0.12) 0%, transparent 50%),
      radial-gradient(circle at 10% 80%, rgba(236, 72, 153, 0.03) 0%, transparent 40%);
  }
  .auth-grid {
    background-image: 
      linear-gradient(rgba(255, 255, 255, 0.005) 1px, transparent 1px),
      linear-gradient(90deg, rgba(255, 255, 255, 0.005) 1px, transparent 1px);
    background-size: 40px 40px;
    mask-image: radial-gradient(ellipse at 50% 50%, black 50%, transparent 100%);
  }
  .luxury-card {
    background: rgba(17, 24, 39, 0.35);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.05);
    box-shadow: 0 20px 50px -15px rgba(0, 0, 0, 0.5);
  }
  .auth-input:focus {
    border-color: rgba(139, 92, 246, 0.5);
    background: rgba(0, 0, 0, 0.4);
    box-shadow: 0 0 15px rgba(139, 92, 246, 0.15);
  }
</style>
@endpush

@section('content')
<div class="auth-bg min-h-[90svh] relative flex items-center justify-center py-16 overflow-hidden">
  
  {{-- Cosmic Grids --}}
  <div class="auth-grid absolute inset-0 pointer-events-none -z-10"></div>

  {{-- Ambient Orbs --}}
  <div class="absolute top-[20%] left-[30%] w-[500px] h-[500px] bg-violet-600/5 blur-[120px] rounded-full pointer-events-none -z-10"></div>

  <div class="mx-auto w-full max-w-[500px] px-4 relative z-10">
    
    {{-- Back Floating Button --}}
    <div class="mb-5 flex justify-start">
      <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('landing') }}"
         class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 hover:border-violet-500/50 hover:bg-violet-600/5 text-xs font-bold text-slate-300 hover:text-white transition-all flex items-center gap-1.5">
        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        Kembali
      </a>
    </div>

    {{-- Main Login Box --}}
    <div class="luxury-card rounded-[2.5rem] p-6 md:p-10">
      
      {{-- Card Header --}}
      <div class="flex items-center gap-4 pb-6 border-b border-white/5">
        <div class="size-12 rounded-2xl bg-gradient-to-tr from-violet-600 to-fuchsia-600 flex items-center justify-center text-white shadow-[0_0_15px_rgba(139,92,246,0.3)]">
          <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
          </svg>
        </div>
        <div>
          <h1 class="text-2xl font-extrabold text-white tracking-tight">Selamat Datang</h1>
          <p class="text-xs text-slate-400 font-medium">Akses riwayat transaksi & pesanan kamu.</p>
        </div>
      </div>

      {{-- Form --}}
      <form class="mt-6 space-y-5" method="post" action="{{ route('login.perform') }}">
        @csrf

        {{-- Error Alerts --}}
        @if ($errors->any())
          <div class="rounded-2xl border border-red-500/20 bg-red-950/20 text-red-200 text-xs font-bold p-4 leading-normal">
            ⚠️ {{ $errors->first() }}
          </div>
        @endif

        {{-- Input: Email --}}
        <div class="space-y-1.5">
          <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">Alamat Email</label>
          <div class="relative">
            <input name="email" type="email" value="{{ old('email') }}" placeholder="nama@email.com" required
              class="auth-input h-12 w-full rounded-xl bg-black/30 border border-white/10 px-4 text-sm text-white placeholder:text-slate-600 outline-none transition-all duration-300">
          </div>
        </div>

        {{-- Input: Password --}}
        <div class="space-y-1.5">
          <div class="flex items-center justify-between">
            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">Kata Sandi</label>
            <a class="text-xs font-bold text-violet-400 hover:text-violet-300 transition-colors" href="{{ route('password.forgot') }}">
              Lupa Sandi?
            </a>
          </div>
          <div class="relative">
            <input id="loginPass" name="password" type="password" placeholder="••••••••" required
              class="auth-input h-12 w-full rounded-xl bg-black/30 border border-white/10 ps-4 pe-16 text-sm text-white placeholder:text-slate-600 outline-none transition-all duration-300">
            <button type="button" id="toggleLoginPass"
              class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg px-3 py-1 text-xs font-bold text-slate-400 hover:text-slate-200 transition-colors">
              Tampil
            </button>
          </div>
        </div>

        {{-- Checkbox Remember Me --}}
        <div class="flex items-center justify-between pt-1">
          <label class="flex items-center gap-2.5 cursor-pointer group">
            <input id="remember" name="remember" type="checkbox" value="1" class="rounded border-white/10 bg-black/30 text-violet-600 focus:ring-0 size-4 cursor-pointer" {{ old('remember') ? 'checked' : '' }}>
            <span class="text-xs font-bold text-slate-400 group-hover:text-slate-300 transition-colors">Ingat saya di perangkat ini</span>
          </label>
        </div>

        {{-- Submit Button --}}
        <button type="submit"
          class="w-full h-12 rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-white font-extrabold text-sm tracking-wide transition-all shadow-[0_0_20px_rgba(139,92,246,0.3)] hover:shadow-[0_0_30px_rgba(139,92,246,0.5)]">
          Masuk Sekarang
        </button>
      </form>

      {{-- Footer Register Redirect --}}
      <div class="mt-6 pt-5 border-t border-white/5 text-center text-xs font-bold text-slate-400 tracking-wide">
        Belum memiliki akun? 
        <a href="{{ route('register') }}" class="text-violet-400 hover:text-violet-300 transition-colors">Daftar Akun Baru</a>
      </div>

    </div>
  </div>
</div>
@endsection

@push('body')
<script>
  document.getElementById('toggleLoginPass')?.addEventListener('click', function () {
      const i = document.getElementById('loginPass');
      const is = i.getAttribute('type') === 'password';
      i.setAttribute('type', is ? 'text' : 'password');
      this.textContent = is ? 'Sembunyi' : 'Tampil';
  });
</script>
@endpush