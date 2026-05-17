@extends('layouts.admin')
@section('title', 'Dashboard — Admin')

@push('head')
<style>
  .metric-card {
    background: rgba(17, 24, 39, 0.3);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.05);
    box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  }
  .metric-card:hover {
    border-color: rgba(139, 92, 246, 0.3);
    background: rgba(139, 92, 246, 0.04);
    transform: translateY(-2px);
    box-shadow: 0 15px 35px -10px rgba(139, 92, 246, 0.15);
  }
  .luxury-table-card {
    background: rgba(17, 24, 39, 0.25);
    backdrop-filter: blur(25px);
    border: 1px solid rgba(255, 255, 255, 0.05);
    box-shadow: 0 20px 50px -15px rgba(0, 0, 0, 0.6);
  }
  .popup-glass {
    background: rgba(8, 15, 29, 0.9);
    backdrop-filter: blur(25px);
    border: 1px solid rgba(139, 92, 246, 0.2);
    box-shadow: 0 25px 70px -15px rgba(0, 0, 0, 0.8), 0 0 50px -10px rgba(139, 92, 246, 0.1);
  }
</style>
@endpush

@section('content')
  {{-- Header --}}
  <div class="reveal space-y-1">
    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/5 border border-white/10 text-[9px] font-extrabold uppercase tracking-widest text-violet-300">
      📈 Real-time Analytics
    </div>
    <h1 class="text-3xl font-extrabold text-white tracking-tight">Dashboard Admin</h1>
    <p class="text-sm text-slate-400 font-medium">Ringkasan singkat performa toko digital kamu hari ini.</p>
  </div>

  {{-- KPI Cards --}}
  <section class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5">
    
    {{-- KPI 1: Pendapatan --}}
    <div class="reveal metric-card rounded-2xl p-5 relative overflow-hidden group">
      <div class="absolute -right-16 -top-16 w-32 h-32 rounded-full blur-2xl bg-emerald-500/5 pointer-events-none group-hover:bg-emerald-500/10 transition-all"></div>
      
      <div class="flex items-center justify-between gap-3">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pendapatan Hari Ini</span>
        <div class="size-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-400">
          <svg class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
      </div>
      
      <div class="mt-4">
        <div class="text-2xl font-extrabold text-white tracking-tight">Rp 1.250.000</div>
        <div class="mt-1 flex items-center gap-1 text-[10px] font-extrabold text-emerald-400 tracking-wide uppercase">
          <span>▲ +8.2%</span>
          <span class="text-slate-500 font-bold lowercase">dari kemarin</span>
        </div>
      </div>
    </div>

    {{-- KPI 2: Order Baru --}}
    <div class="reveal metric-card rounded-2xl p-5 relative overflow-hidden group">
      <div class="absolute -right-16 -top-16 w-32 h-32 rounded-full blur-2xl bg-violet-500/5 pointer-events-none group-hover:bg-violet-500/10 transition-all"></div>
      
      <div class="flex items-center justify-between gap-3">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Order Baru</span>
        <div class="size-8 rounded-lg bg-violet-500/10 flex items-center justify-center text-violet-400">
          <svg class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
          </svg>
        </div>
      </div>
      
      <div class="mt-4">
        <div class="text-2xl font-extrabold text-white tracking-tight">47</div>
        <div class="mt-1 flex items-center gap-1 text-[10px] font-extrabold text-emerald-400 tracking-wide uppercase">
          <span>▲ +3.1%</span>
          <span class="text-slate-500 font-bold lowercase">volume transaksi</span>
        </div>
      </div>
    </div>

    {{-- KPI 3: Sukses --}}
    <div class="reveal metric-card rounded-2xl p-5 relative overflow-hidden group">
      <div class="absolute -right-16 -top-16 w-32 h-32 rounded-full blur-2xl bg-fuchsia-500/5 pointer-events-none group-hover:bg-fuchsia-500/10 transition-all"></div>
      
      <div class="flex items-center justify-between gap-3">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sukses</span>
        <div class="size-8 rounded-lg bg-fuchsia-500/10 flex items-center justify-center text-fuchsia-400">
          <svg class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
      </div>
      
      <div class="mt-4">
        <div class="text-2xl font-extrabold text-white tracking-tight">45</div>
        <div class="mt-1 flex items-center gap-1 text-[10px] font-extrabold text-emerald-400 tracking-wide uppercase">
          <span>📈 95.7%</span>
          <span class="text-slate-500 font-bold lowercase">success rate</span>
        </div>
      </div>
    </div>

    {{-- KPI 4: Pending --}}
    <div class="reveal metric-card rounded-2xl p-5 relative overflow-hidden group">
      <div class="absolute -right-16 -top-16 w-32 h-32 rounded-full blur-2xl bg-amber-500/5 pointer-events-none group-hover:bg-amber-500/10 transition-all"></div>
      
      <div class="flex items-center justify-between gap-3">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pending</span>
        <div class="size-8 rounded-lg bg-amber-500/10 flex items-center justify-center text-amber-400">
          <svg class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
      </div>
      
      <div class="mt-4">
        <div class="text-2xl font-extrabold text-white tracking-tight">12</div>
        <div class="mt-1 flex items-center gap-1 text-[10px] font-extrabold text-amber-400 tracking-wide uppercase">
          <span>⚠️ Perlu Cek</span>
          <span class="text-slate-500 font-bold lowercase">payment gateway</span>
        </div>
      </div>
    </div>

  </section>

  {{-- Table Container --}}
  <section class="mt-8 rounded-3xl luxury-table-card overflow-hidden reveal">
    
    {{-- Header Table --}}
    <div class="p-5 border-b border-white/5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h2 class="font-extrabold text-white text-base tracking-tight">Order Terbaru</h2>
        <p class="text-xs text-slate-500 font-medium">Daftar pesanan digital dan marketplace teranyar masuk sistem.</p>
      </div>

      {{-- Form Search Order --}}
      <form action="{{ route('admin.orders.search') }}" method="GET" class="flex items-center gap-2 relative">
        <input type="text" name="code" placeholder="Cari kode MP / MPM..."
          class="h-9 w-48 rounded-xl bg-black/40 border border-white/10 px-3.5 text-xs text-white placeholder:text-slate-600 outline-none focus:border-violet-500/50 transition-all"
          required>
        <button type="submit" class="h-9 px-4 rounded-xl bg-violet-600 hover:bg-violet-500 text-xs font-bold text-white transition-all shadow-md">
          Cari
        </button>
      </form>
    </div>

    {{-- Responsive Table Grid --}}
    <div class="overflow-x-auto p-4 md:p-0">
      <table class="w-full block md:table text-xs font-medium border-collapse">
        <thead class="hidden md:table-header-group">
          <tr class="bg-black/30 border-b border-white/5 font-extrabold text-slate-400 uppercase tracking-widest text-[10px]">
            <th class="text-left px-5 py-4">Order ID</th>
            <th class="text-left px-5 py-4">Produk</th>
            <th class="text-left px-5 py-4">User</th>
            <th class="text-left px-5 py-4">Total</th>
            <th class="text-left px-5 py-4">Status</th>
            <th class="text-left px-5 py-4">Waktu</th>
            <th class="text-right px-5 py-4">Detail</th>
          </tr>
        </thead>
        <tbody id="latest-orders-body" class="w-full block md:table-row-group divide-y divide-white/[0.03] md:divide-y-0 space-y-4 md:space-y-0">
          @include('dashboard.admin.partials.latest-orders-rows', ['orders' => $orders])
        </tbody>
      </table>
    </div>

    {{-- Next Pagination AJAX Button --}}
    @if($orders->hasMorePages())
      <div class="p-4 border-t border-white/5 flex justify-end">
        <button id="latest-orders-next" data-next-url="{{ $orders->nextPageUrl() }}"
          class="h-9 px-5 rounded-xl border border-white/10 hover:border-violet-500/50 hover:bg-violet-600/5 text-xs font-bold text-slate-300 hover:text-white transition-all">
          Lihat Lebih Banyak
        </button>
      </div>
    @endif
  </section>

  {{-- Modal Detail Order Redesign --}}
  <div id="order-detail-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 backdrop-blur-md p-4 transition-all duration-300">
    <div class="relative w-full max-w-2xl popup-glass rounded-[2rem] flex flex-col max-h-[85vh] md:max-h-[80vh] overflow-hidden animate-in fade-in zoom-in-95 duration-200">
      
      {{-- Modal Top Close --}}
      <button type="button" id="order-detail-close"
        class="absolute top-5 right-5 size-8 rounded-xl bg-white/5 border border-white/10 hover:border-rose-500/50 hover:bg-rose-600/5 flex items-center justify-center text-slate-400 hover:text-white transition-all z-10">
        ✕
      </button>

      {{-- Sticky Header --}}
      <div class="p-6 pb-4 border-b border-white/5 shrink-0 pr-16">
        <div class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-violet-600/10 border border-violet-500/20 text-[9px] font-extrabold uppercase tracking-widest text-violet-300">
          📝 Order Info
        </div>
        <h2 class="text-xl font-extrabold text-white tracking-tight mt-1.5">
          Detail Transaksi Pesanan
        </h2>
      </div>

      {{-- Scrollable Content Body --}}
      <div id="order-detail-body" class="flex-1 overflow-y-auto p-6 text-xs text-slate-300 leading-relaxed font-medium no-scrollbar">
        <div class="text-center text-slate-500 py-8 font-semibold uppercase tracking-wider text-[10px]">
          🌀 Memuat detail pesanan...
        </div>
      </div>
    </div>
  </div>


  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        // === NEXT BUTTON ===
        const btnNext = document.getElementById('latest-orders-next');
        const tbody = document.getElementById('latest-orders-body');

        if (btnNext && tbody) {
          btnNext.addEventListener('click', async () => {
            const url = btnNext.dataset.nextUrl;
            if (!url) return;

            btnNext.disabled = true;
            btnNext.textContent = 'Memuat...';

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
                btnNext.textContent = 'Lihat Lebih Banyak';
              } else {
                btnNext.remove();
              }
            } catch (e) {
              console.error(e);
              btnNext.disabled = false;
              btnNext.textContent = 'Lihat Lebih Banyak';
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
          modalBody.innerHTML = '<div class="text-center text-slate-500 py-8 font-semibold uppercase tracking-wider text-[10px]">🌀 Memuat detail pesanan...</div>';
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
            modalBody.innerHTML = '<div class="text-center text-slate-500 py-8 font-semibold uppercase tracking-wider text-[10px]">🌀 Memuat detail pesanan...</div>';

            try {
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
              modalBody.innerHTML = '<div class="text-center text-rose-400 py-8 font-bold">⚠️ Gagal memuat detail pesanan.</div>';
            }
          });
        }
      });
    </script>
  @endpush

@endsection