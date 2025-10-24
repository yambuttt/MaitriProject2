@extends('layouts.app')

@section('title', 'Daftar — MaitriProject')
@section('meta_description', 'Buat akun MaitriProject.')
@section('page', 'auth')

@section('content')
    <section class="py-12">
        <div class="mx-auto max-w-[640px] px-4">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('landing') }}"
                class="text-sm text-slate-400 hover:text-slate-200">← Kembali</a>

            <div class="mt-4 rounded-[20px] border border-slate-800/70 bg-[#111826] p-6 md:p-8 shadow-xl shadow-black/10">
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-xl bg-violet-600/20 grid place-items-center text-violet-300">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none">
                            <path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm-7 8a7 7 0 0 1 14 0" stroke="currentColor"
                                stroke-width="1.5" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-xl font-semibold leading-tight">Daftar</h1>
                        <p class="text-sm text-slate-400">Buat akun baru untuk mulai transaksi.</p>
                    </div>
                </div>

                <form class="mt-6 space-y-5" method="post" action="{{ route('register.perform') }}">
                    @csrf
                    @if ($errors->any())
                        <div class="rounded-xl border border-red-900/40 bg-red-950/30 text-red-200 text-sm p-3">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    {{-- Gabungkan nama depan+belakang ke name --}}
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-slate-300">Nama Depan</label>
                            <input name="first_name" type="text" placeholder="Nama depan" required
                                class="mt-1 h-11 w-full rounded-xl bg-[#0E1524] border border-slate-700/60 px-3 text-[15px] outline-none placeholder:text-slate-500 focus:border-violet-500/70 focus:ring-2 focus:ring-violet-500/30">
                        </div>
                        <div>
                            <label class="block text-sm text-slate-300">Nama Belakang</label>
                            <input name="last_name" type="text" placeholder="Nama belakang"
                                class="mt-1 h-11 w-full rounded-xl bg-[#0E1524] border border-slate-700/60 px-3 text-[15px] outline-none placeholder:text-slate-500 focus:border-violet-500/70 focus:ring-2 focus:ring-violet-500/30">
                        </div>
                    </div>

                    <input type="hidden" name="name" id="fullName">
                    <script>
                        // satukan nama depan & belakang ke field hidden "name"
                        document.addEventListener('input', () => {
                            const f = document.querySelector('input[name="first_name"]')?.value?.trim() || '';
                            const l = document.querySelector('input[name="last_name"]')?.value?.trim() || '';
                            document.getElementById('fullName').value = [f, l].filter(Boolean).join(' ');
                        });
                    </script>

                    <div>
                        <label class="block text-sm text-slate-300">Email</label>
                        <input name="email" type="email" placeholder="nama@email.com" required
                            class="mt-1 h-11 w-full rounded-xl bg-[#0E1524] border border-slate-700/60 px-3 text-[15px] outline-none placeholder:text-slate-500 focus:border-violet-500/70 focus:ring-2 focus:ring-violet-500/30">
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-slate-300">Kata Sandi</label>
                            <div class="relative mt-1">
                                <input id="regPass" name="password" type="password" placeholder="Minimal 8 karakter"
                                    required
                                    class="h-11 w-full rounded-xl bg-[#0E1524] border border-slate-700/60 ps-3 pe-16 text-[15px] outline-none placeholder:text-slate-500 focus:border-violet-500/70 focus:ring-2 focus:ring-violet-500/30">
                                <button type="button" id="toggleRegPass"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 rounded-lg px-3 py-1 text-xs text-slate-400 hover:text-slate-200 bg-transparent">Tampil</button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm text-slate-300">Konfirmasi Sandi</label>
                            <input id="regPass2" name="password_confirmation" type="password"
                                placeholder="Ulangi kata sandi" required
                                class="mt-1 h-11 w-full rounded-xl bg-[#0E1524] border border-slate-700/60 px-3 text-[15px] outline-none placeholder:text-slate-500 focus:border-violet-500/70 focus:ring-2 focus:ring-violet-500/30">
                        </div>
                    </div>

                    <div class="flex items-start gap-2">
                        <input id="tos" type="checkbox" class="mt-1 accent-violet-600">
                        <label for="tos" class="text-sm text-slate-400">Saya setuju dengan <a href="#"
                                class="text-violet-300 hover:text-violet-200">S&K</a> dan <a href="#"
                                class="text-violet-300 hover:text-violet-200">Kebijakan Privasi</a>.</label>
                    </div>

                    <button id="btnRegister"
                        class="w-full h-11 rounded-2xl bg-violet-600 hover:bg-violet-500 transition font-medium disabled:opacity-50"
                        disabled>
                        Daftar
                    </button>
                </form>

                <p class="mt-6 text-sm text-slate-400">Sudah punya akun?
                    <a href="{{ route('login') }}" class="text-violet-300 hover:text-violet-200">Masuk</a>
                </p>
            </div>
        </div>
    </section>

    @push('body')
        <script>
            // toggle password
            document.getElementById('toggleRegPass')?.addEventListener('click', function () {
                const i = document.getElementById('regPass');
                const is = i.getAttribute('type') === 'password';
                i.setAttribute('type', is ? 'text' : 'password');
                this.textContent = is ? 'Sembunyi' : 'Tampil';
            });

            // enable tombol daftar saat valid
            const pass = document.getElementById('regPass');
            const pass2 = document.getElementById('regPass2');
            const tos = document.getElementById('tos');
            const btn = document.getElementById('btnRegister');
            function check() {
                const ok = pass.value.length >= 8 && pass2.value === pass.value && tos.checked;
                btn.disabled = !ok;
            }
            ['input', 'change', 'keyup'].forEach(e => {
                pass.addEventListener(e, check);
                pass2.addEventListener(e, check);
                tos.addEventListener(e, check);
            });
            check();
        </script>
    @endpush
@endsection