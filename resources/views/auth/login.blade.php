@extends('layouts.app')

@section('title', 'Masuk — MaitriProject')
@section('meta_description', 'Masuk ke akun MaitriProject.')
@section('page', 'auth')

@section('content')
    <section class="py-12">
        <div class="mx-auto max-w-[520px] px-4">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('landing') }}"
                class="text-sm text-slate-400 hover:text-slate-200">← Kembali</a>

            <div class="mt-4 rounded-[20px] border border-slate-800/70 bg-[#111826] p-6 md:p-8 shadow-xl shadow-black/10">
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-xl bg-violet-600/20 grid place-items-center text-violet-300">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none">
                            <path d="M12 3v6m0 0 3-3m-3 3-3-3M4 13v6h16v-6" stroke="currentColor" stroke-width="1.5" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-xl font-semibold leading-tight">Masuk</h1>
                        <p class="text-sm text-slate-400">Akses riwayat & pesanan kamu.</p>
                    </div>
                </div>

                <form class="mt-6 space-y-5" method="post" action="{{ route('login.perform') }}">
                    @csrf

                    @if ($errors->any())
                        <div class="rounded-xl border border-red-900/40 bg-red-950/30 text-red-200 text-sm p-3">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm text-slate-300">Email</label>
                        <input name="email" type="email" value="{{ old('email') }}" placeholder="nama@email.com" required
                            class="mt-1 h-11 w-full rounded-xl bg-[#0E1524] border border-slate-700/60 px-3 text-[15px] outline-none placeholder:text-slate-500 focus:border-violet-500/70 focus:ring-2 focus:ring-violet-500/30">
                    </div>

                    {{-- Password --}}
                    <div>
                        <div class="flex items-center justify-between">
                            <label class="text-sm text-slate-300">Kata Sandi</label>
                            <a class="text-xs text-slate-400 hover:text-slate-200 whitespace-nowrap" href="#">Lupa
                                sandi?</a>
                        </div>
                        <div class="relative mt-1">
                            <input id="loginPass" name="password" type="password" placeholder="••••••••" required
                                class="h-11 w-full rounded-xl bg-[#0E1524] border border-slate-700/60 ps-3 pe-16 text-[15px] outline-none placeholder:text-slate-500 focus:border-violet-500/70 focus:ring-2 focus:ring-violet-500/30">
                            <button type="button" id="toggleLoginPass"
                                class="absolute right-2 top-1/2 -translate-y-1/2 rounded-lg px-3 py-1 text-xs text-slate-400 hover:text-slate-200 bg-transparent">
                                Tampil
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <input id="remember" name="remember" type="checkbox" value="1" class="accent-violet-600" {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember" class="text-sm text-slate-400">Ingat saya</label>
                    </div>

                    <button type="submit"
                        class="w-full h-11 rounded-2xl bg-violet-600 hover:bg-violet-500 transition font-medium">
                        Masuk
                    </button>
                </form>


                <p class="mt-6 text-sm text-slate-400">Belum punya akun?
                    <a href="{{ route('register') }}" class="text-violet-300 hover:text-violet-200">Daftar</a>
                </p>
            </div>
        </div>
    </section>

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
@endsection