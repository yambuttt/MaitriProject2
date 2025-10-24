<!doctype html>
<html lang="id" class="h-full">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'MaitriProject')</title>
  <meta name="description"
    content="@yield('meta_description', 'Top up pulsa, data, game, PLN, dan e-wallet cepat & aman. Terhubung Digiflazz.')">
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

<body class="bg-[#0B0F17] text-slate-200 antialiased" data-page="@yield('page', 'default')">
  @include('partials.navbar')

  <main class="pt-16">
    @yield('content')
  </main>

  @include('partials.footer')

  @stack('body')
</body>

</html>