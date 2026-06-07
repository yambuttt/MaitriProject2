<!doctype html>
<html lang="id" class="h-full">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'MaitriProject')</title>
  <meta name="description"
    content="@yield('meta_description', 'Top up pulsa, data, game, PLN, dan e-wallet cepat & aman.')">





  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @stack('head')



  <style>
    /* ✅ Bikin semua .reveal nampak sebagai default (tidak blank) */
    .reveal {
      opacity: 1;
      transform: none
    }

    .reveal-in {
      opacity: 1;
      transform: none
    }

    /* (opsional) kalau nanti mau animasi lagi, pakai .will-reveal di markup */
    @media (prefers-reduced-motion: no-preference) {
      .will-reveal {
        opacity: 0;
        transform: translateY(16px);
        transition: opacity .4s ease, transform .4s ease
      }

      .will-reveal.reveal-in {
        opacity: 1;
        transform: translateY(0)
      }
    }

    ::selection {
      background: #6D28D9;
      color: white
    }

    .no-scrollbar::-webkit-scrollbar {
      display: none
    }

    .no-scrollbar {
      -ms-overflow-style: none;
      scrollbar-width: none
    }
  </style>


  <script>
    // Fallback: kalau script halaman belum termuat, tampilkan semua .reveal biar tidak blank
    (function () {
      function showAll() {
        try {
          document.querySelectorAll('.reveal').forEach(function (el) {
            el.classList.add('reveal-in');
          });
        } catch (_) { }
      }
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', showAll, { once: true });
      } else {
        showAll();
      }
    })();
  </script>

</head>
@if(auth()->check() && !auth()->user()->hasPaymentPin())
  @php
    $showPinForm = $errors->has('pin') || $errors->has('pin_confirmation');
  @endphp

  <div id="pin-modal" class="fixed inset-0 z-40 flex items-center justify-center bg-black/60">
    <div class="w-full max-w-md rounded-2xl bg-slate-900 border border-violet-500/40 p-5 space-y-4">

      {{-- STEP 1: Info --}}
      <div id="pin-step-info" class="{{ $showPinForm ? 'hidden' : '' }} space-y-3">
        <h2 class="text-lg font-semibold text-slate-50">Buat PIN Pembayaran</h2>
        <p class="text-sm text-slate-300">
          Untuk menggunakan Saldo Maitri, kamu wajib membuat PIN pembayaran terlebih dahulu.
        </p>

        <div class="flex justify-end gap-2 pt-2">
          <button type="button" id="btn-pin-later" class="h-9 px-3 rounded-xl text-sm text-slate-300 hover:bg-slate-800">
            Nanti saja
          </button>
          <button type="button" id="btn-pin-make-now"
            class="h-9 px-4 rounded-xl bg-violet-500 hover:bg-violet-600 text-sm font-medium text-white">
            Buat PIN Sekarang
          </button>
        </div>
      </div>

      {{-- STEP 2: Form PIN --}}
      <div id="pin-step-form" class="{{ $showPinForm ? '' : 'hidden' }} space-y-3">
        <h2 class="text-lg font-semibold text-slate-50">Set PIN Pembayaran</h2>
        <p class="text-sm text-slate-300">
          PIN akan digunakan setiap kali kamu membayar dengan Saldo Maitri.
        </p>

        <form method="post" action="{{ route('dashboard.wallet.pin.update') }}" class="space-y-3">
          @csrf

          <div>
            <label class="block text-xs font-medium text-slate-400 mb-1">PIN Baru</label>
            <input type="password" name="pin" maxlength="6"
              class="h-10 w-full rounded-xl bg-slate-950 border border-slate-700/80 px-3 text-sm text-slate-100"
              placeholder="4–6 digit angka" required>
            @error('pin')
              <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label class="block text-xs font-medium text-slate-400 mb-1">Konfirmasi PIN</label>
            <input type="password" name="pin_confirmation" maxlength="6"
              class="h-10 w-full rounded-xl bg-slate-950 border border-slate-700/80 px-3 text-sm text-slate-100" required>
            @error('pin_confirmation')
              <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
            @enderror
          </div>

          <div class="flex justify-end gap-2 pt-1">
            <button type="button" id="btn-pin-cancel"
              class="h-9 px-3 rounded-xl text-sm text-slate-300 hover:bg-slate-800">
              Batal
            </button>
            <button type="submit"
              class="h-9 px-4 rounded-xl bg-violet-500 hover:bg-violet-600 text-sm font-medium text-white">
              Simpan PIN
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>




  {{-- JS kecil untuk toggle step & close modal --}}
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const modal = document.getElementById('pin-modal');
      const step1 = document.getElementById('pin-step-info');
      const step2 = document.getElementById('pin-step-form');
      const btnInfo = document.getElementById('btn-pin-make-now');
      const btnLater = document.getElementById('btn-pin-later');
      const btnCancel = document.getElementById('btn-pin-cancel');

      if (btnInfo) {
        btnInfo.addEventListener('click', function () {
          step1.classList.add('hidden');
          step2.classList.remove('hidden');
        });
      }

      function closeModal() {
        modal.classList.add('hidden');
      }

      if (btnLater) {
        btnLater.addEventListener('click', closeModal);
      }
      if (btnCancel) {
        btnCancel.addEventListener('click', closeModal);
      }
    });
  </script>
  @endif






<body class="bg-[#0B0F17] text-slate-200 antialiased" data-page="@yield('page', 'default')">
  @include('partials.navbar')

  <main class="pt-16">
    @yield('content')
  </main>

  @include('partials.footer')

  @stack('body')


</body>



</html>