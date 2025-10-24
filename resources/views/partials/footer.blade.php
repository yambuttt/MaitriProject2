<footer class="border-t border-slate-800/70">
  <div class="mx-auto max-w-[1280px] px-4 md:px-6 lg:px-8 py-10">
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div>
        <div class="flex items-center gap-2">
          <svg width="24" height="24" viewBox="0 0 24 24" aria-hidden="true"><rect rx="6" width="24" height="24" fill="#7C3AED"/></svg>
          <span class="font-semibold">MaitriProject</span>
        </div>
        <p class="mt-3 text-sm text-slate-400">Top up digital goods cepat & aman.</p>
      </div>
      <div>
        <h4 class="font-medium">Produk</h4>
        <ul class="mt-3 space-y-2 text-sm text-slate-400">
          <li><a href="{{ route('catalog') }}">Game</a></li>
          <li><a href="{{ route('catalog') }}">Pulsa</a></li>
          <li><a href="{{ route('catalog') }}">Data</a></li>
          <li><a href="{{ route('catalog') }}">PLN</a></li>
          <li><a href="{{ route('catalog') }}">e-Wallet</a></li>
        </ul>
      </div>
      <div>
        <h4 class="font-medium">Bantuan</h4>
        <ul class="mt-3 space-y-2 text-sm text-slate-400">
          <li><a href="{{ route('landing') }}#bantuan">FAQ</a></li>
          <li><a href="#">Kontak</a></li>
          <li><a href="#">S&K</a></li>
        </ul>
      </div>
      <div>
        <h4 class="font-medium">Ikuti</h4>
        <ul class="mt-3 space-y-2 text-sm text-slate-400">
          <li>Instagram</li><li>TikTok</li><li>X</li>
        </ul>
      </div>
    </div>
    <div class="mt-8 text-xs text-slate-500">© {{ date('Y') }} MaitriProject. All rights reserved.</div>
  </div>
</footer>
