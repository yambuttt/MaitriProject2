@extends('layouts.admin')
@section('title', 'Variants — ' . $product->name)

@section('content')
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div>
            <a href="{{ route('admin.products.index') }}" class="text-sm text-slate-400 hover:text-slate-200">← Kembali ke
                Products</a>
            <h1 class="text-2xl md:text-3xl font-semibold mt-1">Variants — {{ $product->name }}</h1>
            <p class="text-slate-400 mt-1">Markup produk (default): <span class="text-slate-200 font-medium">Rp
                    {{ number_format($product->markup_rp, 0, ',', '.') }}</span></p>
        </div>
        <div>
            @if (session('ok'))
                <div class="rounded-lg border border-emerald-900/30 bg-emerald-950/30 text-emerald-200 text-sm px-3 py-2">
                    {{ session('ok') }}
                </div>
            @endif
        </div>
        <div class="flex items-center gap-2">
            <form method="post" action="{{ route('admin.products.variants.digiflazz.sync', $product) }}">
                @csrf
                <button class="h-10 px-4 rounded-xl border border-slate-800/70 hover:border-slate-700">Sinkronkan</button>
            </form>
            <button id="btnOpenDigi" class="h-10 px-4 rounded-xl bg-violet-600 hover:bg-violet-500">Cari dari
                Digiflazz</button>
        </div>


    </div>

    {{-- Filter --}}
    <form method="get" class="mt-4 grid md:grid-cols-[1fr_140px] gap-3">
        <div class="relative">
            <input name="q" value="{{ $q }}" type="search" placeholder="Cari nama varian / buyer_sku_code…"
                class="w-full h-11 rounded-xl bg-[#0E1524] border border-slate-800/70 ps-10 pe-3 outline-none placeholder:text-slate-500 focus:border-violet-500/60 focus:ring-2 focus:ring-violet-500/30">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-5 text-slate-500" viewBox="0 0 24 24" fill="none">
                <path d="M21 21l-4.3-4.3M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" stroke="currentColor" stroke-width="1.5" />
            </svg>
        </div>
        <div class="flex gap-3">
            <select name="per_page"
                class="h-11 rounded-xl bg-[#0E1524] border border-slate-800/70 px-3 outline-none focus:border-violet-500/60 focus:ring-2 focus:ring-violet-500/30">
                @foreach([20, 40, 80, 160] as $n)
                    <option value="{{ $n }}" @selected($pp == $n)>{{ $n }}/hal</option>
                @endforeach
            </select>
            <button class="h-11 rounded-xl bg-violet-600 hover:bg-violet-500 px-4">Terapkan</button>
        </div>
    </form>

    {{-- Form tambah cepat --}}
    <div class="mt-4 rounded-2xl border border-slate-800/70 bg-[#0E1524] p-4">
        <form method="post" action="{{ route('admin.products.variants.store', $product) }}"
            class="grid lg:grid-cols-[220px_1fr_180px_180px_120px] gap-3">
            @csrf
            <div>
                <label class="text-sm text-slate-300">Buyer SKU Code</label>
                <input name="buyer_sku_code" required placeholder="contoh: TSEL-DATA-10GB"
                    class="mt-1 h-11 w-full rounded-xl bg-[#0E1524] border border-slate-700/60 px-3 outline-none focus:border-violet-500/70 focus:ring-2 focus:ring-violet-500/30">
            </div>
            <div>
                <label class="text-sm text-slate-300">Nama Varian</label>
                <input name="name" required placeholder="cth: Paket Data 10GB Harian"
                    class="mt-1 h-11 w-full rounded-xl bg-[#0E1524] border border-slate-700/60 px-3 outline-none focus:border-violet-500/70 focus:ring-2 focus:ring-violet-500/30">
            </div>
            <div>
                <label class="text-sm text-slate-300">Base Price (Rp)</label>
                <input name="base_price" type="number" min="0" step="1" required
                    class="mt-1 h-11 w-full rounded-xl bg-[#0E1524] border border-slate-700/60 px-3 outline-none focus:border-violet-500/70 focus:ring-2 focus:ring-violet-500/30">
            </div>
            <div>
                <label class="text-sm text-slate-300">Markup Varian (Rp)</label>
                <input name="markup_rp" type="number" min="0" step="1" placeholder="kosongkan = pakai markup produk"
                    class="mt-1 h-11 w-full rounded-xl bg-[#0E1524] border border-slate-700/60 px-3 outline-none focus:border-violet-500/70 focus:ring-2 focus:ring-violet-500/30">
            </div>
            <div class="flex items-end justify-end gap-3">
                <label class="inline-flex items-center gap-2 text-sm text-slate-300">
                    <input type="checkbox" name="is_active" value="1" class="accent-violet-600" checked>
                    Aktif
                </label>
                <button class="h-11 px-4 rounded-xl bg-violet-600 hover:bg-violet-500">Tambah</button>
            </div>
        </form>

        @if ($errors->any())
            <div class="mt-3 rounded-lg border border-red-900/40 bg-red-950/30 text-red-200 text-sm px-3 py-2">
                {{ $errors->first() }}
            </div>
        @endif
    </div>
    {{-- BULK toolbar --}}
    <form id="bulkForm" method="post" action="{{ route('admin.products.variants.bulk', $product) }}"
        class="mt-4 rounded-2xl border border-slate-800/70 bg-[#0E1524] p-4 flex flex-col md:flex-row gap-3 md:items-center md:justify-between">
        @csrf @method('PATCH')

        <div class="text-sm text-slate-300">
            <span id="bulkCount" class="font-semibold">0</span> varian dipilih
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2">
                <label class="text-sm text-slate-300">Markup (Rp)</label>
                <input id="bulkMarkup" type="number" min="0" step="1"
                    class="h-10 w-36 rounded-xl bg-[#0E1524] border border-slate-700/60 px-3 outline-none focus:border-violet-500/70 focus:ring-2 focus:ring-violet-500/30">
            </div>

            <input type="hidden" name="action" id="bulkAction">
            <div id="bulkIds"></div> {{-- diisi JS --}}

            <button type="button" id="btnBulkSet" class="h-10 px-4 rounded-xl bg-violet-600 hover:bg-violet-500">
                Terapkan ke yang dipilih
            </button>
            <button type="button" id="btnBulkClear"
                class="h-10 px-4 rounded-xl border border-slate-800/70 hover:border-slate-700">
                Pakai markup produk
            </button>
            <button type="button" id="btnBulkActivate"
                class="h-10 px-4 rounded-xl border border-emerald-700/60 text-emerald-300 hover:bg-emerald-900/20">
                Aktifkan
            </button>
        </div>
    </form>


    {{-- Tabel --}}
    <section class="mt-4 rounded-2xl border border-slate-800/70 bg-[#0E1524] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-[#0c1222] text-slate-400">
                    <tr>
                        <th class="px-4 py-3 w-10">
                            <input type="checkbox" id="checkAll" class="accent-violet-600">
                        </th>
                        <th class="text-left px-4 py-3">SKU</th>
                        <th class="text-left px-4 py-3">Nama</th>
                        <th class="text-left px-4 py-3">Base (Rp)</th>
                        <th class="text-left px-4 py-3">Markup (Rp)</th>
                        <th class="text-left px-4 py-3">Harga Jual</th>
                        <th class="text-left px-4 py-3">Status</th>
                        <th class="text-left px-4 py-3 w-44">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($variants as $v)
                        <tr class="border-t border-slate-800/70">
                            <td class="px-4 py-3">
                                <input type="checkbox" class="rowCheck accent-violet-600" value="{{ $v->id }}">
                            </td>
                            <td class="px-4 py-3 text-slate-400">{{ $v->buyer_sku_code }}</td>
                            <td class="px-4 py-3 font-medium">{{ $v->name }}</td>
                            <td class="px-4 py-3">Rp {{ number_format($v->base_price, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                @if(is_null($v->markup_rp))
                                    <span class="text-slate-400">— (pakai produk: Rp
                                        {{ number_format($product->markup_rp, 0, ',', '.') }})</span>
                                @else
                                    Rp {{ number_format($v->markup_rp, 0, ',', '.') }}
                                @endif
                            </td>
                            <td class="px-4 py-3 font-medium">Rp {{ number_format($v->final_price, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                <form method="post" action="{{ route('admin.products.variants.toggle', [$product, $v]) }}">
                                    @csrf @method('PATCH')
                                    <button
                                        class="inline-flex items-center px-2 py-1 rounded-lg text-xs
                                                                            {{ $v->is_active ? 'bg-emerald-500/10 text-emerald-300 border border-emerald-700/40' : 'bg-slate-500/10 text-slate-300 border border-slate-700/40' }}">
                                        {{ $v->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.products.variants.edit', [$product, $v]) }}"
                                        class="px-3 py-1.5 rounded-lg border border-slate-800/70 hover:border-slate-700">Edit</a>
                                    <form method="post" action="{{ route('admin.products.variants.destroy', [$product, $v]) }}"
                                        onsubmit="return confirm('Hapus varian ini?')">
                                        @csrf @method('DELETE')
                                        <button
                                            class="px-3 py-1.5 rounded-lg border border-red-900/40 text-red-300 hover:bg-red-950/30">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-slate-400">Belum ada varian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-800/70">
            {{ $variants->links() }}
        </div>

        {{-- Modal Digiflazz --}}
        {{-- Modal Digiflazz (scrollable) --}}
        <div id="digiModal" class="fixed inset-0 z-[60] hidden">
            <div class="absolute inset-0 bg-black/60 backdrop-blur"></div>

            {{-- container: flex-col + max height + overflow --}}
            <div class="relative mx-auto max-w-5xl mt-16 bg-[#0E1524] border border-slate-800/70 rounded-2xl
                                  flex flex-col max-h-[85vh] overflow-hidden">

                {{-- header (non-scroll) --}}
                <div class="p-4 border-b border-slate-800/70 flex items-center justify-between">
                    <h3 class="font-medium">Import dari Digiflazz</h3>
                    <button id="btnCloseDigi" class="p-2 rounded-lg border border-slate-800/70">Tutup</button>
                </div>

                {{-- body (scroll area) --}}
                <div class="flex-1 overflow-y-auto p-4">
                    <div class="flex gap-3">
                        <input id="digiQ" type="search" placeholder="Cari nama/brand/sku…"
                            class="h-11 flex-1 rounded-xl bg-[#0E1524] border border-slate-700/60 px-3 outline-none focus:border-violet-500/70 focus:ring-2 focus:ring-violet-500/30">
                        <button id="btnSearchDigi"
                            class="h-11 px-4 rounded-xl bg-violet-600 hover:bg-violet-500">Cari</button>
                    </div>

                    <form id="digiForm" method="post"
                        action="{{ route('admin.products.variants.digiflazz.import', $product) }}" class="mt-4">
                        @csrf

                        <div class="overflow-x-auto rounded-xl border border-slate-800/70">
                            <table class="min-w-full text-sm">
                                <thead class="bg-[#0c1222] text-slate-400">
                                    <tr>
                                        <th class="px-3 py-2 w-10"><input type="checkbox" id="digiCheckAll"></th>
                                        <th class="text-left px-3 py-2">SKU</th>
                                        <th class="text-left px-3 py-2">Nama</th>
                                        <th class="text-left px-3 py-2">Brand</th>
                                        <th class="text-left px-3 py-2">Kategori</th>
                                        <th class="text-left px-3 py-2">Modal (Rp)</th>
                                        <th class="text-left px-3 py-2">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="digiTbody">
                                    <tr>
                                        <td colspan="7" class="px-3 py-6 text-center text-slate-400">Masukkan kata kunci di
                                            atas, lalu klik Cari.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- action bar sticky di bawah modal --}}
                        <div class="sticky bottom-0 bg-[#0E1524] pt-3">
                            <div class="border-t border-slate-800/70 pt-3 flex items-center justify-end">
                                <button type="submit" class="h-11 px-4 rounded-xl bg-violet-600 hover:bg-violet-500">
                                    Import Terpilih
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </section>
@endsection

@push('body')
    <script>
        (function () {
            const modal = document.getElementById('digiModal');
            const openBtn = document.getElementById('btnOpenDigi');
            const closeBtn = document.getElementById('btnCloseDigi');
            const q = document.getElementById('digiQ');
            const btnSearch = document.getElementById('btnSearchDigi');
            const tbody = document.getElementById('digiTbody');
            const checkAll = document.getElementById('digiCheckAll');
            const form = document.getElementById('digiForm');

            function open() { modal.classList.remove('hidden'); }
            function close() { modal.classList.add('hidden'); }

            openBtn?.addEventListener('click', open);
            closeBtn?.addEventListener('click', close);
            modal?.addEventListener('click', (e) => { if (e.target === modal) close(); });

            btnSearch?.addEventListener('click', async (e) => {
                e.preventDefault();
                const keyword = q.value.trim();
                tbody.innerHTML = '<tr><td colspan="7" class="px-3 py-6 text-center">Mencari…</td></tr>';
                const url = new URL('{{ route('admin.products.variants.digiflazz.search', $product) }}', window.location.origin);
                url.searchParams.set('q', keyword);
                try {
                    const res = await fetch(url.toString());
                    const data = await res.json();
                    const items = data.items || [];
                    if (items.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="7" class="px-3 py-6 text-center text-slate-400">Tidak ada hasil.</td></tr>';
                        return;
                    }
                    tbody.innerHTML = items.map((it, idx) => `
                                <tr class="border-t border-slate-800/70">
                                  <td class="px-3 py-2"><input type="checkbox" class="digiRow" data-idx="${idx}"></td>
                                  <td class="px-3 py-2 text-slate-400">${it.buyer_sku_code}</td>
                                  <td class="px-3 py-2">${it.name}</td>
                                  <td class="px-3 py-2">${it.brand || '-'}</td>
                                  <td class="px-3 py-2">${it.category || '-'}</td>
                                  <td class="px-3 py-2">Rp ${Number(it.price).toLocaleString('id-ID')}</td>
                                  <td class="px-3 py-2 ${it.status === 'Active' ? 'text-emerald-300' : 'text-amber-300'}">${it.status || '-'}</td>
                                  <input type="hidden" name="items[${idx}][buyer_sku_code]" value="${it.buyer_sku_code}" disabled>
                                  <input type="hidden" name="items[${idx}][name]" value="${it.name.replaceAll('"', '&quot;')}" disabled>
                                  <input type="hidden" name="items[${idx}][price]" value="${it.price}" disabled>
                                </tr>
                              `).join('');
                    // check/uncheck
                    const rows = Array.from(document.querySelectorAll('.digiRow'));
                    rows.forEach(row => {
                        row.addEventListener('change', () => {
                            const i = row.getAttribute('data-idx');
                            form.querySelector(`input[name="items[${i}][buyer_sku_code]"]`).disabled = !row.checked;
                            form.querySelector(`input[name="items[${i}][name]"]`).disabled = !row.checked;
                            form.querySelector(`input[name="items[${i}][price]"]`).disabled = !row.checked;
                        });
                    });
                    checkAll.checked = false;
                } catch (err) {
                    tbody.innerHTML = '<tr><td colspan="7" class="px-3 py-6 text-center text-red-300">Gagal memuat data.</td></tr>';
                }
            });

            checkAll?.addEventListener('change', () => {
                const rows = Array.from(document.querySelectorAll('.digiRow'));
                rows.forEach(row => {
                    row.checked = checkAll.checked;
                    row.dispatchEvent(new Event('change'));
                });
            });
        })();
    </script>
@endpush

@push('body')
<script>
(function(){
  const checkAll = document.getElementById('checkAll');
  const rowChecks = () => Array.from(document.querySelectorAll('.rowCheck'));
  const bulkCount = document.getElementById('bulkCount');
  const bulkIds = document.getElementById('bulkIds');
  const bulkForm = document.getElementById('bulkForm');
  const bulkAction = document.getElementById('bulkAction');
  const bulkMarkup = document.getElementById('bulkMarkup');
  const btnSet = document.getElementById('btnBulkSet');
  const btnClear = document.getElementById('btnBulkClear');
  const btnActivate = document.getElementById('btnBulkActivate');

  function refreshCount(){
    const n = rowChecks().filter(c => c.checked).length;
    bulkCount.textContent = n;
    bulkIds.innerHTML = '';
    rowChecks().filter(c => c.checked).forEach(c=>{
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'variant_ids[]';
      input.value = c.value;
      bulkIds.appendChild(input);
    });
  }

  checkAll?.addEventListener('change', ()=>{
    rowChecks().forEach(c => c.checked = checkAll.checked);
    refreshCount();
  });
  rowChecks().forEach(c => c.addEventListener('change', refreshCount));

  btnSet?.addEventListener('click', ()=>{
    if (rowChecks().every(c => !c.checked)) return alert('Pilih minimal satu varian.');
    const v = parseInt(bulkMarkup.value || '0', 10);
    if (isNaN(v) || v < 0) return alert('Markup harus angka >= 0.');
    bulkAction.value = 'set_markup';
    // pastikan markup terkirim
    let h = bulkForm.querySelector('input[name="markup_rp"]');
    if (!h) { h = document.createElement('input'); h.type='hidden'; h.name='markup_rp'; bulkForm.appendChild(h); }
    h.value = String(v);
    bulkForm.submit();
  });

  btnClear?.addEventListener('click', ()=>{
    if (rowChecks().every(c => !c.checked)) return alert('Pilih minimal satu varian.');
    bulkAction.value = 'clear_to_product';
    const h = bulkForm.querySelector('input[name="markup_rp"]');
    if (h) h.remove();
    bulkForm.submit();
  });

  btnActivate?.addEventListener('click', ()=>{
    if (rowChecks().every(c => !c.checked)) return alert('Pilih minimal satu varian.');
    bulkAction.value = 'activate';
    const h = bulkForm.querySelector('input[name="markup_rp"]');
    if (h) h.remove();
    bulkForm.submit();
  });
})();
</script>
@endpush
