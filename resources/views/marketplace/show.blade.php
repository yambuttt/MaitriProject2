@extends('layouts.app')

@section('title', $product->name.' — Marketplace')
@section('page','marketplace-product')

@section('content')
  <section class="py-8">
    <div class="mx-auto max-w-[960px] px-4 md:px-6 lg:px-8">

      <a href="{{ route('marketplace.index') }}" class="text-sm text-slate-400 hover:text-slate-200">
        ← Kembali ke marketplace
      </a>

      <div class="mt-4 grid gap-6 md:grid-cols-[minmax(0,2fr)_minmax(0,1.3fr)]">
        <div class="space-y-4">
          <h1 class="text-2xl md:text-3xl font-semibold text-slate-50">{{ $product->name }}</h1>
          <p class="text-sm text-slate-400">
            {{ $product->category?->name ?? 'Marketplace' }}
          </p>
          <div class="text-sm leading-relaxed text-slate-300">
            {!! nl2br(e($product->description)) !!}
          </div>
        </div>

        <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-4">
          <h2 class="text-sm font-semibold text-slate-200 mb-3">Pilih Varian</h2>

          <form method="POST" action="{{ route('marketplace.checkout.create', $product) }}" id="variantForm" class="space-y-3">
            @csrf
            <input type="hidden" name="variant_id" id="variantIdInput">

            @foreach($product->variants as $variant)
              <button type="button"
                      class="variantItem w-full text-left rounded-xl border border-slate-800/70 px-3 py-3 text-sm flex items-center justify-between"
                      data-id="{{ $variant->id }}">
                <div>
                  <div class="font-medium text-slate-50">{{ $variant->name }}</div>
                  @if($variant->duration_days)
                    <div class="text-xs text-slate-400">{{ $variant->duration_days }} hari</div>
                  @endif
                </div>
                <div class="font-semibold text-slate-100">
                  Rp {{ number_format($variant->price, 0, ',', '.') }}
                </div>
              </button>
            @endforeach

            <button type="submit" id="btnContinue"
                    class="mt-4 w-full px-4 py-3 rounded-2xl bg-violet-600 hover:bg-violet-500 text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                    disabled>
              Lanjutkan ke checkout
            </button>
          </form>
        </div>
      </div>
    </div>
  </section>

  <script>
    (function () {
      const items = document.querySelectorAll('.variantItem');
      const input = document.getElementById('variantIdInput');
      const btn   = document.getElementById('btnContinue');

      items.forEach(item => {
        item.addEventListener('click', () => {
          const id = item.dataset.id;
          input.value = id;

          items.forEach(i => i.classList.remove('border-violet-500'));
          item.classList.add('border-violet-500');

          btn.disabled = false;
        });
      });
    })();
  </script>
@endsection
