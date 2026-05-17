@extends('layouts.admin')
@section('title', 'Variants — ' . $product->name)

@push('head')
<style>
  .variants-table-card {
    background: rgba(17, 24, 39, 0.25);
    backdrop-filter: blur(25px);
    border: 1px solid rgba(255, 255, 255, 0.05);
    box-shadow: 0 20px 50px -15px rgba(0, 0, 0, 0.6);
  }
  .quick-add-card {
    background: rgba(139, 92, 246, 0.02);
    border: 1px solid rgba(139, 92, 246, 0.1);
  }
  .bulk-action-bar {
    background: rgba(139, 92, 246, 0.03);
    border: 1px solid rgba(139, 92, 246, 0.15);
    box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.4);
  }
  .popup-glass {
    background: rgba(8, 15, 29, 0.92);
    backdrop-filter: blur(25px);
    border: 1px solid rgba(139, 92, 246, 0.25);
    box-shadow: 0 25px 70px -15px rgba(0, 0, 0, 0.8), 0 0 50px -10px rgba(139, 92, 246, 0.15);
  }
</style>
@endpush

@section('content')
    {{-- Header --}}
    <div class="reveal flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="space-y-1">
            <a href="{{ route('admin.products.index') }}" 
               class="inline-flex items-center gap-1 text-xs font-bold text-slate-400 hover:text-white transition-colors">
               ← Kembali ke Produk
            </a>
            <h1 class="text-3xl font-extrabold text-white tracking-tight mt-1.5">Varian — {{ $product->name }}</h1>
            <p class="text-sm text-slate-400 font-medium">
                Markup produk (Default): <span class="text-violet-300 font-extrabold">Rp {{ number_format($product->markup_rp, 0, ',', '.') }}</span>
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <form method="post" action="{{ route('admin.products.variants.digiflazz.sync', $product) }}">
                @csrf
                <button class="h-11 px-5 rounded-xl border border-white/10 bg-white/5 hover:border-violet-500/50 hover:bg-violet-600/10 text-xs font-extrabold text-slate-300 hover:text-white transition-all shadow-sm">
                    Sinkronkan
                </button>
            </form>
            <button id="btnOpenDigi" class="h-11 px-5 rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-xs font-extrabold text-white uppercase tracking-widest transition-all shadow-[0_0_15px_rgba(139,92,246,0.3)]">
                Cari dari Digiflazz
            </button>
        </div>
    </div>

    {{-- System messages --}}
    @if (session('ok'))
        <div class="reveal mt-6 rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-xs font-bold text-emerald-300 flex items-center gap-2">
            <span class="size-1.5 rounded-full bg-emerald-400"></span>
            {{ session('ok') }}
        </div>
    @endif
    @if (session('success'))
        <div class="reveal mt-6 rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-xs font-bold text-emerald-300 flex items-center gap-2">
            <span class="size-1.5 rounded-full bg-emerald-400"></span>
            {{ session('success') }}
        </div>
    @endif
    @if (session('warning'))
        <div class="reveal mt-6 rounded-2xl border border-amber-500/20 bg-amber-500/10 px-4 py-3 text-xs font-bold text-amber-300 flex items-center gap-2">
            <span class="size-1.5 rounded-full bg-amber-400"></span>
            {{ session('warning') }}
        </div>
    @endif
    @if (session('error'))
        <div class="reveal mt-6 rounded-2xl border border-rose-500/20 bg-rose-500/10 px-4 py-3 text-xs font-bold text-rose-300 flex items-center gap-2">
            <span class="size-1.5 rounded-full bg-rose-400"></span>
            {{ session('error') }}
        </div>
    @endif

    {{-- Filters --}}
    <form method="get" class="reveal mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[1fr_140px] gap-3">
        <div class="relative">
            <input name="q" value="{{ $q }}" type="search" placeholder="Cari nama varian atau SKU code..."
                class="w-full h-11 rounded-xl bg-black/40 border border-white/10 ps-10 pe-3 text-xs text-white placeholder:text-slate-600 outline-none focus:border-violet-500/50 transition-all">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 size-4 text-slate-500" viewBox="0 0 24 24" fill="none">
                <path d="M21 21l-4.3-4.3M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" stroke="currentColor" stroke-width="1.5" />
            </svg>
        </div>
        <div class="flex gap-3">
            <select name="per_page"
                class="h-11 w-full rounded-xl bg-black/40 border border-white/10 px-3.5 text-xs text-slate-300 outline-none focus:border-violet-500/50 transition-all">
                @foreach([20, 40, 80, 160] as $n)
                    <option value="{{ $n }}" @selected($pp == $n)>{{ $n }} / Hal</option>
                @endforeach
            </select>
            <button class="h-11 px-5 rounded-xl bg-violet-600 hover:bg-violet-500 text-xs font-bold text-white transition-all shadow-md shrink-0">
                Cari
            </button>
        </div>
    </form>

    {{-- Quick Add Form Card --}}
    <div class="reveal mt-4 rounded-3xl quick-add-card p-5">
        <div class="text-[9px] font-extrabold uppercase tracking-widest text-violet-400 mb-3 block">⚡ Tambah Varian Cepat</div>
        
        <form method="post" action="{{ route('admin.products.variants.store', $product) }}"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[220px_1fr_180px_180px_120px] gap-4 items-end">
            @csrf
            <div class="space-y-1.5">
                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Buyer SKU Code</label>
                <input name="buyer_sku_code" required placeholder="Contoh: TSEL-DATA-10GB..."
                    class="h-11 w-full rounded-xl bg-black/40 border border-white/10 px-4 text-xs font-semibold text-white placeholder:text-slate-600 outline-none focus:border-violet-500/50 transition-all">
            </div>
            
            <div class="space-y-1.5">
                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Nama Varian</label>
                <input name="name" required placeholder="Contoh: Paket Data 10GB Harian..."
                    class="h-11 w-full rounded-xl bg-black/40 border border-white/10 px-4 text-xs font-semibold text-white placeholder:text-slate-600 outline-none focus:border-violet-500/50 transition-all">
            </div>
            
            <div class="space-y-1.5">
                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Base Price (Rupiah)</label>
                <input name="base_price" type="number" min="0" step="1" required
                    class="h-11 w-full rounded-xl bg-black/40 border border-white/10 px-4 text-xs font-semibold text-white placeholder:text-slate-600 outline-none focus:border-violet-500/50 transition-all">
            </div>
            
            <div class="space-y-1.5">
                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Markup Varian (Rupiah)</label>
                <input name="markup_rp" type="number" min="0" step="1" placeholder="Gunakan default produk"
                    class="h-11 w-full rounded-xl bg-black/40 border border-white/10 px-4 text-xs font-semibold text-white placeholder:text-slate-600 outline-none focus:border-violet-500/50 transition-all">
            </div>
            
            <div class="flex items-center justify-between sm:justify-end gap-5">
                <label class="inline-flex items-center gap-2 text-xs font-bold text-slate-300 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" class="accent-violet-600 size-4" checked>
                    <span>Aktif</span>
                </label>
                <button class="h-11 px-5 rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-xs font-bold text-white transition-all shadow-md shrink-0">
                    Tambah
                </button>
            </div>
        </form>

        @if ($errors->any())
            <div class="mt-3.5 rounded-xl border border-rose-500/20 bg-rose-500/10 px-4 py-2.5 text-xs font-bold text-rose-300">
                ⚠️ {{ $errors->first() }}
            </div>
        @endif
    </div>

    {{-- BULK ACTIONS TOOLBAR --}}
    <form id="bulkForm" method="post" action="{{ route('admin.products.variants.bulk', $product) }}"
        class="reveal mt-4 rounded-3xl bulk-action-bar p-5 flex flex-col md:flex-row gap-4 md:items-center md:justify-between">
        @csrf @method('PATCH')

        <div class="text-xs font-extrabold text-slate-300 flex items-center gap-2">
            <span class="size-2 rounded-full bg-violet-400 animate-pulse"></span>
            <span id="bulkCount" class="text-violet-300 font-black">0</span> Varian Terpilih
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2">
                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 whitespace-nowrap">Bulk Markup</label>
                <input id="bulkMarkup" type="number" min="0" step="1" placeholder="Masukkan Rp..."
                    class="h-10 w-32 rounded-xl bg-black/40 border border-white/10 px-3.5 text-xs text-white outline-none focus:border-violet-500/50 transition-all">
            </div>

            <input type="hidden" name="action" id="bulkAction">
            <div id="bulkIds"></div>

            <button type="button" id="btnBulkSet" class="h-10 px-4 rounded-xl bg-violet-600 hover:bg-violet-500 text-xs font-bold text-white transition-all shadow-md">
                Terapkan Markup
            </button>
            <button type="button" id="btnBulkClear"
                class="h-10 px-4 rounded-xl border border-white/10 bg-white/5 hover:border-violet-500/50 hover:bg-violet-600/10 text-xs font-bold text-slate-300 transition-all shadow-sm">
                Default Produk
            </button>
            <button type="button" id="btnBulkActivate"
                class="h-10 px-4 rounded-xl border border-emerald-500/20 bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500 hover:text-white hover:border-emerald-500/50 text-xs font-bold transition-all shadow-sm">
                Aktifkan
            </button>
        </div>
    </form>

    {{-- Grid Table Section --}}
    <section class="reveal mt-4 rounded-3xl variants-table-card overflow-hidden">
        <div class="overflow-x-auto p-4 md:p-0">
            <table class="w-full block md:table text-xs font-medium border-collapse">
                <thead class="hidden md:table-header-group">
                    <tr class="bg-black/30 border-b border-white/5 font-extrabold text-slate-400 uppercase tracking-widest text-[10px]">
                        <th class="px-5 py-4 w-10 text-left">
                            <input type="checkbox" id="checkAll" class="accent-violet-600 size-4">
                        </th>
                        <th class="text-left px-5 py-4">SKU Code</th>
                        <th class="text-left px-5 py-4">Nama Varian</th>
                        <th class="text-left px-5 py-4">Base Price</th>
                        <th class="text-left px-5 py-4">Markup Varian</th>
                        <th class="text-left px-5 py-4">Harga Jual</th>
                        <th class="text-left px-5 py-4">Status</th>
                        <th class="text-left px-5 py-4 w-32">Urutan</th>
                        <th class="text-right px-5 py-4 w-44">Aksi</th>
                    </tr>
                </thead>

                <tbody class="w-full block md:table-row-group divide-y divide-white/[0.03] md:divide-y-0 space-y-4 md:space-y-0">
                    @forelse ($variants as $v)
                        <tr class="flex flex-col md:table-row border-b border-white/[0.03] hover:bg-white/[0.015] transition-all duration-300 p-5 md:p-0 gap-3.5 md:gap-0 bg-white/[0.01] md:bg-transparent rounded-2xl md:rounded-none mb-4 md:mb-0 shadow-lg md:shadow-none border border-white/5 md:border-none">
                            
                            {{-- Checkbox --}}
                            <td class="block md:table-cell px-0 md:px-5 py-0 md:py-4">
                                <div class="flex items-center justify-between md:block">
                                    <span class="md:hidden text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Pilih Varian</span>
                                    <input type="checkbox" class="rowCheck accent-violet-600 size-4" value="{{ $v->id }}">
                                </div>
                            </td>

                            {{-- SKU Code --}}
                            <td class="block md:table-cell px-0 md:px-5 py-0 md:py-4 border-t border-dashed border-white/5 md:border-none pt-3 md:pt-4">
                                <div class="flex items-center justify-between md:block">
                                    <span class="md:hidden text-[10px] font-extrabold uppercase tracking-wider text-slate-500">SKU Code</span>
                                    <span class="font-mono text-xs text-slate-400 whitespace-nowrap bg-white/5 border border-white/5 px-2 py-0.5 rounded-lg md:bg-transparent md:border-none md:p-0">{{ $v->buyer_sku_code }}</span>
                                </div>
                            </td>

                            {{-- Nama Varian --}}
                            <td class="block md:table-cell px-0 md:px-5 py-0 md:py-4 border-t border-dashed border-white/5 md:border-none pt-3 md:pt-4">
                                <div class="md:hidden text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">Nama Varian</div>
                                <span class="text-white font-extrabold text-sm md:text-xs tracking-tight">{{ $v->name }}</span>
                            </td>

                            {{-- Base Price --}}
                            <td class="block md:table-cell px-0 md:px-5 py-0 md:py-4 border-t border-dashed border-white/5 md:border-none pt-3 md:pt-4">
                                <div class="flex items-center justify-between md:block">
                                    <span class="md:hidden text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Base Price</span>
                                    <span class="text-slate-300 font-semibold">Rp {{ number_format($v->base_price, 0, ',', '.') }}</span>
                                </div>
                            </td>

                            {{-- Markup --}}
                            <td class="block md:table-cell px-0 md:px-5 py-0 md:py-4 border-t border-dashed border-white/5 md:border-none pt-3 md:pt-4">
                                <div class="flex items-center justify-between md:block">
                                    <span class="md:hidden text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Markup Varian</span>
                                    <span class="font-semibold">
                                        @if(is_null($v->markup_rp))
                                            <span class="text-slate-500 italic">Rp {{ number_format($product->markup_rp, 0, ',', '.') }} (Default)</span>
                                        @else
                                            Rp {{ number_format($v->markup_rp, 0, ',', '.') }}
                                        @endif
                                    </span>
                                </div>
                            </td>

                            {{-- Harga Jual --}}
                            <td class="block md:table-cell px-0 md:px-5 py-0 md:py-4 border-t border-dashed border-white/5 md:border-none pt-3 md:pt-4">
                                <div class="flex items-center justify-between md:block">
                                    <span class="md:hidden text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Harga Jual</span>
                                    <span class="text-violet-300 font-extrabold text-sm md:text-xs">Rp {{ number_format($v->final_price, 0, ',', '.') }}</span>
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="block md:table-cell px-0 md:px-5 py-0 md:py-4 border-t border-dashed border-white/5 md:border-none pt-3 md:pt-4">
                                <div class="flex items-center justify-between md:block">
                                    <span class="md:hidden text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Status</span>
                                    <form method="post" action="{{ route('admin.products.variants.toggle', [$product, $v]) }}">
                                        @csrf @method('PATCH')
                                        <button class="inline-flex items-center px-2.5 py-1 rounded-full text-[9px] font-extrabold uppercase tracking-wider border transition-all
                                            {{ $v->is_active ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-slate-500/10 text-slate-400 border border-white/5' }}">
                                            {{ $v->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </button>
                                    </form>
                                </div>
                            </td>

                            {{-- Sort Order --}}
                            <td class="block md:table-cell px-0 md:px-5 py-0 md:py-4 border-t border-dashed border-white/5 md:border-none pt-3 md:pt-4">
                                <div class="flex items-center justify-between md:block">
                                    <span class="md:hidden text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Sort Urutan</span>
                                    
                                    <form method="post" action="{{ route('admin.products.variants.sort', [$product, $v]) }}"
                                        class="flex items-center gap-1.5">
                                        @csrf @method('PATCH')
                                        <input type="number" name="sort_order" value="{{ $v->sort_order }}" min="1"
                                            class="h-8 w-14 rounded-lg bg-black/40 border border-white/10 px-2 text-xs text-white outline-none focus:border-violet-500/50 transition-all text-center">
                                        <button class="h-8 px-2.5 rounded-lg border border-white/10 bg-white/5 hover:border-violet-500/50 hover:bg-violet-600 hover:text-white text-[10px] font-extrabold transition-all shrink-0">
                                            Simpan
                                        </button>
                                    </form>
                                </div>
                            </td>

                            {{-- Action buttons --}}
                            <td class="block md:table-cell px-0 md:px-5 py-0 md:py-4 border-t border-dashed border-white/5 md:border-none pt-3 md:pt-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <form method="post" action="{{ route('admin.products.variants.pin', [$product, $v]) }}">
                                        @csrf @method('PATCH')
                                        <button class="h-8 px-3 rounded-lg border border-amber-500/20 bg-amber-500/10 text-amber-300 hover:bg-amber-500 hover:text-white hover:border-amber-500/50 text-[10px] font-bold transition-all shadow-sm">
                                            Top Pin
                                        </button>
                                    </form>

                                    <a href="{{ route('admin.products.variants.edit', [$product, $v]) }}"
                                        class="h-8 px-3 rounded-lg bg-white/5 border border-white/10 hover:border-violet-500/50 hover:bg-violet-600 hover:text-white text-[10px] font-bold flex items-center justify-center transition-all shadow-sm">
                                        Edit
                                    </a>

                                    <form method="post" action="{{ route('admin.products.variants.destroy', [$product, $v]) }}"
                                        onsubmit="return confirm('Hapus varian ini?')">
                                        @csrf @method('DELETE')
                                        <button class="h-8 px-3 rounded-lg bg-white/5 border border-rose-500/20 text-rose-400 hover:bg-rose-600 hover:text-white hover:border-rose-500/50 text-[10px] font-bold flex items-center justify-center transition-all shadow-sm">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="flex flex-col md:table-row">
                            <td colspan="9" class="px-5 py-10 text-center text-xs font-bold uppercase tracking-wider text-slate-500">
                                Belum ada varian produk yang ditambahkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="p-4 border-t border-white/5 flex justify-center sm:justify-end">
            {{ $variants->links() }}
        </div>
    </section>

    {{-- MODAL DIGIFLAZZ REDESIGN --}}
    <div id="digiModal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/70 backdrop-blur-md p-4 transition-all duration-300">
        <div class="relative w-full max-w-5xl popup-glass rounded-[2rem] flex flex-col max-h-[85vh] md:max-h-[80vh] overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            
            {{-- Header --}}
            <div class="p-6 border-b border-white/5 shrink-0 pr-20 flex items-start justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-violet-600/10 border border-violet-500/20 text-[9px] font-extrabold uppercase tracking-widest text-violet-300">
                        ⚡ Digiflazz Engine
                    </div>
                    <h3 class="text-xl font-extrabold text-white tracking-tight mt-1.5">Import Varian dari Digiflazz</h3>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">Sinkronkan, cari, dan import varian produk langsung dari API Digiflazz.</p>
                </div>

                <button type="button" id="btnCloseDigi"
                    class="absolute top-5 right-5 size-8 rounded-lg bg-white/5 border border-white/10 hover:border-rose-500/50 hover:bg-rose-600/5 flex items-center justify-center text-slate-400 hover:text-white transition-all">
                    ✕
                </button>
            </div>

            {{-- Body content --}}
            <div class="flex-1 overflow-y-auto p-6 space-y-5 no-scrollbar flex flex-col">
                <div class="flex gap-2 shrink-0">
                    <input id="digiQ" type="search" placeholder="Masukkan nama, brand, atau SKU Digiflazz..."
                        class="h-11 flex-1 rounded-xl bg-black/40 border border-white/10 px-4 text-xs text-white placeholder:text-slate-600 outline-none focus:border-violet-500/50 transition-all">
                    <button id="btnSearchDigi"
                        class="h-11 px-5 rounded-xl bg-violet-600 hover:bg-violet-500 text-xs font-bold text-white transition-all shadow-md">
                        Cari
                    </button>
                </div>

                <form id="digiForm" method="post" action="{{ route('admin.products.variants.digiflazz.import', $product) }}" class="flex-1 flex flex-col justify-between gap-5">
                    @csrf

                    <div class="overflow-x-auto rounded-2xl border border-white/5 bg-black/30 max-h-96 overflow-y-auto no-scrollbar">
                        <table class="min-w-full text-xs font-medium">
                            <thead class="bg-black/40 text-slate-400 uppercase tracking-widest text-[9px]">
                                <tr>
                                    <th class="px-4 py-3 w-10 text-left"><input type="checkbox" id="digiCheckAll" class="accent-violet-600 size-4"></th>
                                    <th class="text-left px-4 py-3">SKU</th>
                                    <th class="text-left px-4 py-3">Nama Varian</th>
                                    <th class="text-left px-4 py-3">Brand</th>
                                    <th class="text-left px-4 py-3">Kategori</th>
                                    <th class="text-left px-4 py-3">Harga Modal</th>
                                    <th class="text-left px-4 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody id="digiTbody" class="divide-y divide-white/[0.02]">
                                <tr>
                                    <td colspan="7" class="px-4 py-10 text-center text-xs font-bold uppercase tracking-wider text-slate-500">
                                        Masukkan kata kunci pencarian di atas, lalu klik Cari.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Sticky bottom action bar --}}
                    <div class="pt-4 border-t border-white/5 flex items-center justify-end shrink-0">
                        <button type="submit" class="h-11 px-6 rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-xs font-bold text-white transition-all shadow-md">
                            Import Varian Terpilih
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
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

            function open() { 
                modal.classList.remove('hidden'); 
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }
            function close() { 
                modal.classList.add('hidden'); 
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }

            openBtn?.addEventListener('click', open);
            closeBtn?.addEventListener('click', close);
            modal?.addEventListener('click', (e) => { if (e.target === modal) close(); });

            btnSearch?.addEventListener('click', async (e) => {
                e.preventDefault();
                const keyword = q.value.trim();
                tbody.innerHTML = '<tr><td colspan="7" class="px-4 py-8 text-center text-xs text-slate-400 font-bold uppercase tracking-wider">🌀 Sedang mencari data dari Digiflazz...</td></tr>';
                const url = new URL('{{ route('admin.products.variants.digiflazz.search', $product) }}', window.location.origin);
                url.searchParams.set('q', keyword);
                try {
                    const res = await fetch(url.toString());
                    const data = await res.json();
                    const items = data.items || [];
                    if (items.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="7" class="px-4 py-8 text-center text-xs text-slate-500 font-bold uppercase tracking-wider">Tidak ada hasil pencarian.</td></tr>';
                        return;
                    }
                    tbody.innerHTML = items.map((it, idx) => `
                      <tr class="border-t border-white/[0.02] hover:bg-white/[0.01] transition-colors">
                        <td class="px-4 py-3">
                          <input type="checkbox" class="digiRow accent-violet-600 size-4" data-idx="${idx}">
                        </td>
                        <td class="px-4 py-3 text-slate-400 font-mono">${it.buyer_sku_code}</td>
                        <td class="px-4 py-3 text-white font-bold">${it.name}</td>
                        <td class="px-4 py-3 text-slate-300 font-semibold">${it.brand || '-'}</td>
                        <td class="px-4 py-3 text-slate-400">${it.category || '-'}</td>
                        <td class="px-4 py-3 text-violet-300 font-extrabold">Rp ${Number(it.price).toLocaleString('id-ID')}</td>
                        <td class="px-4 py-3">
                          <span class="inline-flex px-2 py-0.5 rounded text-[8px] font-extrabold uppercase tracking-wider
                            ${it.status === 'Active' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20'}">
                            ${it.status || '-'}
                          </span>
                        </td>

                        <!-- HIDDEN DATA INPUTS -->
                        <input type="hidden"
                               name="items[${idx}][digiflazz_variant_id]"
                               value="${it.id}"
                               disabled>

                        <input type="hidden"
                               name="items[${idx}][buyer_sku_code]"
                               value="${it.buyer_sku_code}"
                               disabled>

                        <input type="hidden"
                               name="items[${idx}][name]"
                               value="${it.name.replaceAll('"', '&quot;')}"
                               disabled>

                        <input type="hidden"
                               name="items[${idx}][price]"
                               value="${it.price}"
                               disabled>
                      </tr>
                    `).join('');

                    // check/uncheck row toggle
                    const rows = Array.from(document.querySelectorAll('.digiRow'));
                    rows.forEach(row => {
                        row.addEventListener('change', () => {
                            const i = row.getAttribute('data-idx');
                            form.querySelector(`input[name="items[${i}][digiflazz_variant_id]"]`).disabled = !row.checked;
                            form.querySelector(`input[name="items[${i}][buyer_sku_code]"]`).disabled = !row.checked;
                            form.querySelector(`input[name="items[${i}][name]"]`).disabled = !row.checked;
                            form.querySelector(`input[name="items[${i}][price]"]`).disabled = !row.checked;
                        });
                    });

                    checkAll.checked = false;
                } catch (err) {
                    console.error(err);
                    tbody.innerHTML = '<tr><td colspan="7" class="px-4 py-8 text-center text-xs text-rose-400 font-bold uppercase tracking-wider">❌ Gagal memuat data pencarian Digiflazz.</td></tr>';
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
    <script>
        (function () {
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

            function refreshCount() {
                const n = rowChecks().filter(c => c.checked).length;
                bulkCount.textContent = n;
                bulkIds.innerHTML = '';
                rowChecks().filter(c => c.checked).forEach(c => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'variant_ids[]';
                    input.value = c.value;
                    bulkIds.appendChild(input);
                });
            }

            checkAll?.addEventListener('change', () => {
                rowChecks().forEach(c => c.checked = checkAll.checked);
                refreshCount();
            });
            rowChecks().forEach(c => c.addEventListener('change', refreshCount));

            btnSet?.addEventListener('click', () => {
                if (rowChecks().every(c => !c.checked)) return alert('Pilih minimal satu varian.');
                const v = parseInt(bulkMarkup.value || '0', 10);
                if (isNaN(v) || v < 0) return alert('Markup harus angka >= 0.');
                bulkAction.value = 'set_markup';
                let h = bulkForm.querySelector('input[name="markup_rp"]');
                if (!h) { h = document.createElement('input'); h.type = 'hidden'; h.name = 'markup_rp'; bulkForm.appendChild(h); }
                h.value = String(v);
                bulkForm.submit();
            });

            btnClear?.addEventListener('click', () => {
                if (rowChecks().every(c => !c.checked)) return alert('Pilih minimal satu varian.');
                bulkAction.value = 'clear_to_product';
                const h = bulkForm.querySelector('input[name="markup_rp"]');
                if (h) h.remove();
                bulkForm.submit();
            });

            btnActivate?.addEventListener('click', () => {
                if (rowChecks().every(c => !c.checked)) return alert('Pilih minimal satu varian.');
                bulkAction.value = 'activate';
                const h = bulkForm.querySelector('input[name="markup_rp"]');
                if (h) h.remove();
                bulkForm.submit();
            });
        })();
    </script>
@endpush