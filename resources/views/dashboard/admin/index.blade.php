@extends('layouts.admin')
@section('title', 'Dashboard — Admin')

@section('content')
  <h1 class="text-2xl md:text-3xl font-semibold">Dashboard Admin</h1>
  <p class="mt-1 text-slate-400">Ringkasan singkat performa toko digital kamu.</p>

  {{-- KPI Cards --}}
  <section class="mt-6 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="rounded-2xl border border-slate-800/70 bg-[#0E1524] p-4">
      <div class="text-sm text-slate-400">Pendapatan Hari Ini</div>
      <div class="mt-2 text-2xl font-semibold">Rp 1.250.000</div>
      <div class="mt-1 text-xs text-emerald-400">+8.2% dari kemarin</div>
    </div>
    <div class="rounded-2xl border border-slate-800/70 bg-[#0E1524] p-4">
      <div class="text-sm text-slate-400">Order Baru</div>
      <div class="mt-2 text-2xl font-semibold">47</div>
      <div class="mt-1 text-xs text-emerald-400">+3.1%</div>
    </div>
    <div class="rounded-2xl border border-slate-800/70 bg-[#0E1524] p-4">
      <div class="text-sm text-slate-400">Sukses</div>
      <div class="mt-2 text-2xl font-semibold">45</div>
      <div class="mt-1 text-xs text-emerald-400">95.7% success rate</div>
    </div>
    <div class="rounded-2xl border border-slate-800/70 bg-[#0E1524] p-4">
      <div class="text-sm text-slate-400">Pending</div>
      <div class="mt-2 text-2xl font-semibold">12</div>
      <div class="mt-1 text-xs text-amber-400">cek payment gateway</div>
    </div>
  </section>

  {{-- Table Placeholder --}}
  <section class="mt-6 rounded-2xl border border-slate-800/70 bg-[#0E1524] overflow-hidden">
    <div class="p-4 border-b border-slate-800/70 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
      <h2 class="font-medium">Order Terbaru</h2>

      {{-- Form cari order by kode MP / MPM --}}
      <form action="{{ route('admin.orders.search') }}" method="GET" class="flex items-center gap-2">
        <input type="text" name="code" placeholder="Cari kode MP / MPM..."
          class="bg-slate-900/60 border border-slate-700/70 rounded-lg px-3 py-1.5 text-xs text-slate-100 focus:outline-none focus:border-violet-400"
          required>
        <button type="submit" class="text-sm text-violet-300 hover:text-violet-200">
          Cari
        </button>
      </form>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-[#0c1222] text-slate-400">
          <tr>
            <th class="text-left px-4 py-3">Order ID</th>
            <th class="text-left px-4 py-3">Produk</th>
            <th class="text-left px-4 py-3">User</th>
            <th class="text-left px-4 py-3">Total</th>
            <th class="text-left px-4 py-3">Status</th>
            <th class="text-left px-4 py-3">Waktu</th>
            <th class="text-left px-4 py-3 text-right">Detail</th>
          </tr>
        </thead>
        <tbody id="latest-orders-body">
          @include('dashboard.admin.partials.latest-orders-rows', ['orders' => $orders])
        </tbody>
      </table>
    </div>

    {{-- Tombol Next (AJAX) --}}
    @if($orders->hasMorePages())
      <div class="p-4 border-t border-slate-800/70 flex justify-end">
        <button id="latest-orders-next" data-next-url="{{ $orders->nextPageUrl() }}"
          class="px-3 py-1.5 text-xs rounded-lg border border-slate-700/70 text-slate-200 hover:border-violet-400 hover:text-violet-200">
          Next
        </button>
      </div>
    @endif
  </section>
  {{-- Modal Detail Order --}}
  <div id="order-detail-modal" class="fixed inset-0 z-40 hidden items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="relative w-full max-w-2xl mx-4 bg-[#080F1D] border border-slate-800/80 rounded-2xl shadow-xl">
      <button type="button" id="order-detail-close"
        class="absolute top-3 right-3 text-slate-400 hover:text-slate-200 text-sm">
        ✕
      </button>

      <div class="p-4 border-b border-slate-800/70">
        <h2 class="text-sm font-semibold text-slate-100">
          Detail Pesanan
        </h2>
      </div>

      <div id="order-detail-body" class="p-4 text-sm text-slate-100">
        <div class="text-center text-slate-400 text-xs py-6">
          Memuat detail pesanan...
        </div>
      </div>
    </div>
  </div>


  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        // === NEXT BUTTON (sudah ada) ===
        const btnNext = document.getElementById('latest-orders-next');
        const tbody = document.getElementById('latest-orders-body');

        if (btnNext && tbody) {
          btnNext.addEventListener('click', async () => {
            const url = btnNext.dataset.nextUrl;
            if (!url) return;

            btnNext.disabled = true;
            btnNext.textContent = 'Loading...';

            try {
              const sep = url.includes('?') ? '&' : '?';
              const res = await fetch(url + sep + 'ajax=1', {
                headers: {
                  'X-Requested-With': 'XMLHttpRequest',
                  'Accept': 'application/json',
                },
              });
              const data = await res.json();

              if (data.html) {
                tbody.insertAdjacentHTML('beforeend', data.html);
              }

              if (data.next_page_url) {
                btnNext.dataset.nextUrl = data.next_page_url;
                btnNext.disabled = false;
                btnNext.textContent = 'Next';
              } else {
                btnNext.remove();
              }
            } catch (e) {
              console.error(e);
              btnNext.disabled = false;
              btnNext.textContent = 'Next';
            }
          });
        }

        // === MODAL DETAIL ORDER ===
        const modal = document.getElementById('order-detail-modal');
        const modalBody = document.getElementById('order-detail-body');
        const btnClose = document.getElementById('order-detail-close');

        const openModal = () => {
          modal.classList.remove('hidden');
          modal.classList.add('flex');
        };

        const closeModal = () => {
          modal.classList.add('hidden');
          modal.classList.remove('flex');
          modalBody.innerHTML = '<div class="text-center text-slate-400 text-xs py-6">Memuat detail pesanan...</div>';
        };

        if (btnClose) {
          btnClose.addEventListener('click', closeModal);
        }

        if (modal) {
          modal.addEventListener('click', (e) => {
            if (e.target === modal) {
              closeModal();
            }
          });
        }

        // Delegasi: dengerin klik tombol detail di tbody
        if (tbody && modal && modalBody) {
          tbody.addEventListener('click', async (e) => {
            const btn = e.target.closest('[data-order-detail-btn]');
            if (!btn) return;

            const code = btn.dataset.orderCode;
            if (!code) return;

            openModal();
            modalBody.innerHTML = '<div class="text-center text-slate-400 text-xs py-6">Memuat detail pesanan...</div>';

            try {
              // template URL dari route, kita ganti placeholder-nya
              const templateUrl = "{{ route('admin.orders.detail', ['code' => 'CODE_PLACEHOLDER']) }}";
              const url = templateUrl.replace('CODE_PLACEHOLDER', encodeURIComponent(code));

              const res = await fetch(url, {
                headers: {
                  'X-Requested-With': 'XMLHttpRequest',
                  'Accept': 'text/html',
                },
              });

              const html = await res.text();
              modalBody.innerHTML = html;
            } catch (e) {
              console.error(e);
              modalBody.innerHTML = '<div class="text-center text-rose-400 text-xs py-6">Gagal memuat detail pesanan.</div>';
            }
          });
        }
      });
    </script>
  @endpush


@endsection