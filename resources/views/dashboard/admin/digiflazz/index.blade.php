@extends('layouts.admin')

@section('title', 'Digiflazz Master')

@section('content')
    <div class="w-full space-y-6">


        {{-- Header + actions --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold text-slate-100">Master Variant Digiflazz</h1>
                <p class="text-sm text-slate-400">
                    Data ini diambil dari API Digiflazz dan disimpan ke tabel <code>digiflazz_variants</code>.
                </p>
            </div>

            <div class="flex gap-3">
                <form method="post" action="{{ route('admin.digiflazz.sync-master') }}">
                    @csrf
                    <button type="submit"
                        class="h-10 px-4 rounded-xl bg-violet-500 hover:bg-violet-600 text-sm font-medium text-white">
                        Sinkron Master dari API
                    </button>
                </form>
            </div>
        </div>

        {{-- Flash message --}}
        @if (session('success'))
            <div class="rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-rose-500/40 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                {{ session('error') }}
            </div>
        @endif

        {{-- GRID: kiri tabel, kanan log --}}
        <div class="grid gap-6 lg:grid-cols-3">

            {{-- KIRI: filter + tabel master --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="rounded-2xl border border-slate-800/70 bg-slate-900/60 p-4 space-y-4">
                    <form method="get" class="flex flex-wrap gap-3 items-end">
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Cari SKU / Nama</label>
                            <input type="text" name="q" value="{{ $q }}"
                                class="h-10 rounded-xl bg-slate-900 border border-slate-700/80 px-3 text-sm text-slate-100 w-64"
                                placeholder="contoh: OWSEM 1 GB...">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Brand</label>
                            <select name="brand"
                                class="h-10 rounded-xl bg-slate-900 border border-slate-700/80 px-3 text-sm text-slate-100">
                                <option value="">Semua</option>
                                @foreach ($brands as $b)
                                    <option value="{{ $b }}" @selected($brand === $b)>{{ $b }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Kategori</label>
                            <select name="category"
                                class="h-10 rounded-xl bg-slate-900 border border-slate-700/80 px-3 text-sm text-slate-100">
                                <option value="">Semua</option>
                                @foreach ($categories as $c)
                                    <option value="{{ $c }}" @selected($category === $c)>{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Status</label>
                            <select name="status"
                                class="h-10 rounded-xl bg-slate-900 border border-slate-700/80 px-3 text-sm text-slate-100">
                                <option value="">Semua</option>
                                <option value="Active" @selected($status === 'Active')>Active</option>
                                <option value="nonaktif" @selected($status === 'nonaktif')>Nonaktif</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Per halaman</label>
                            <select name="per_page"
                                class="h-10 rounded-xl bg-slate-900 border border-slate-700/80 px-3 text-sm text-slate-100">
                                @foreach ([20, 50, 100] as $n)
                                    <option value="{{ $n }}" @selected($pp == $n)>{{ $n }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <button type="submit"
                                class="h-10 mt-1 px-4 rounded-xl bg-violet-500 hover:bg-violet-600 text-sm font-medium text-white">
                                Terapkan
                            </button>
                        </div>
                    </form>

                    <div class="border border-slate-800/80 rounded-2xl overflow-hidden">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-900/80 text-slate-400 text-xs uppercase">
                                <tr>
                                    <th class="px-3 py-2 text-left">SKU</th>
                                    <th class="px-3 py-2 text-left">Nama</th>
                                    <th class="px-3 py-2 text-left">Brand</th>
                                    <th class="px-3 py-2 text-left">Kategori</th>
                                    <th class="px-3 py-2 text-right">Modal (Rp)</th>
                                    <th class="px-3 py-2 text-left">Status</th>
                                    <th class="px-3 py-2 text-left">Last Sync</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/80">
                                @forelse ($variants as $v)
                                                        <tr class="text-slate-100">
                                                            <td class="px-3 py-2 text-slate-300">{{ $v->buyer_sku_code }}</td>
                                                            <td class="px-3 py-2">{{ $v->product_name }}</td>
                                                            <td class="px-3 py-2 text-slate-300">{{ $v->brand }}</td>
                                                            <td class="px-3 py-2 text-slate-300">{{ $v->category }}</td>
                                                            <td class="px-3 py-2 text-right">
                                                                Rp {{ number_format($v->base_price, 0, ',', '.') }}
                                                            </td>
                                                            <td class="px-3 py-2">
                                                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs
                                                                                                                                {{ $v->status === 'Active'
                                    ? 'bg-emerald-500/10 text-emerald-300 border border-emerald-500/40'
                                    : 'bg-amber-500/10 text-amber-300 border border-amber-500/40' }}">
                                                                    {{ $v->status ?? '-' }}
                                                                </span>
                                                            </td>
                                                            <td class="px-3 py-2 text-slate-400 text-xs">
                                                                {{ optional($v->last_synced_at)->format('d M Y H:i') ?? '-' }}
                                                            </td>
                                                        </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-3 py-6 text-center text-slate-400">
                                            Belum ada data master Digiflazz. Coba jalankan sinkron.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $variants->links() }}
                    </div>
                </div>
            </div>

            {{-- KANAN: log sinkronisasi --}}
            <div class="space-y-3">
                <div class="rounded-2xl border border-slate-800/70 bg-slate-900/60 p-4 h-full flex flex-col">
                    <h2 class="text-sm font-semibold text-slate-100 mb-3">Log Sinkronisasi</h2>

                    @if ($logs->isEmpty())
                        <p class="text-sm text-slate-400">Belum ada log.</p>
                    @else
                        <div class="overflow-x-auto text-xs mb-2">
                            <table class="min-w-full">
                                <thead class="bg-slate-900/80 text-slate-400 uppercase">
                                    <tr>
                                        <th class="px-3 py-2 text-left">Waktu</th>
                                        <th class="px-3 py-2 text-left">Tipe</th>
                                        <th class="px-3 py-2 text-left">Status</th>
                                        <th class="px-3 py-2 text-right">Jumlah</th>
                                        <th class="px-3 py-2 text-left">Pesan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/80">
                                    @foreach ($logs as $log)
                                                        <tr class="text-slate-100">
                                                            <td class="px-3 py-2 text-slate-400">
                                                                {{ $log->created_at->format('d M Y H:i:s') }}
                                                            </td>
                                                            <td class="px-3 py-2">
                                                                {{ $log->type === 'master' ? 'Master Digiflazz' : $log->type }}
                                                            </td>
                                                            <td class="px-3 py-2">
                                                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px]
                                                                                                                                    {{ $log->status === 'success'
                                        ? 'bg-emerald-500/10 text-emerald-300 border border-emerald-500/40'
                                        : 'bg-rose-500/10 text-rose-300 border border-rose-500/40' }}">
                                                                    {{ strtoupper($log->status) }}
                                                                </span>
                                                            </td>
                                                            <td class="px-3 py-2 text-right">
                                                                {{ $log->synced_count }}
                                                            </td>
                                                            <td class="px-3 py-2 text-slate-300">
                                                                {{ $log->message }}
                                                            </td>
                                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection