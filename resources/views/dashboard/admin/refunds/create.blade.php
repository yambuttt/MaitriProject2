@extends('layouts.admin')
@section('title', 'Buat Refund — Admin')

@push('head')
<style>
  .refund-form-card {
    background: rgba(17, 24, 39, 0.25);
    backdrop-filter: blur(25px);
    border: 1px solid rgba(255, 255, 255, 0.05);
    box-shadow: 0 20px 50px -15px rgba(0, 0, 0, 0.6);
  }
  .rules-card {
    background: rgba(139, 92, 246, 0.02);
    border: 1px solid rgba(139, 92, 246, 0.1);
  }
</style>
@endpush

@section('content')
  {{-- Header --}}
  <div class="reveal flex items-center justify-between gap-4">
    <div class="space-y-1">
      <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/5 border border-white/10 text-[9px] font-extrabold uppercase tracking-widest text-violet-300">
        💸 Refund Creation Console
      </div>
      <h1 class="text-3xl font-extrabold text-white tracking-tight">Buat Refund Baru</h1>
      <p class="text-sm text-slate-400 font-medium">Masukkan kode order, cek validitas status transaksi, lalu pilih opsi metode pengembalian dana.</p>
    </div>
    <a href="{{ route('admin.refunds.index') }}"
       class="h-10 px-4 inline-flex items-center rounded-xl bg-white/5 border border-white/10 text-slate-300 hover:text-white text-xs font-bold transition-all shadow-sm">
       ← Kembali
    </a>
  </div>

  {{-- Alert Messages --}}
  @if(session('success'))
    <div class="reveal mt-6 rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-xs font-bold text-emerald-300 flex items-center gap-2">
      <span class="size-2 rounded-full bg-emerald-400 animate-ping"></span>
      {{ session('success') }}
    </div>
  @endif

  @if($errors->any())
    <div class="reveal mt-6 rounded-2xl border border-rose-500/20 bg-rose-500/10 px-4 py-3 text-xs font-bold text-rose-300 space-y-1">
      <div class="flex items-center gap-1.5 mb-1 text-[10px] uppercase tracking-wider text-rose-400">⚠️ Terjadi Kesalahan:</div>
      <ul class="list-disc pl-5 space-y-0.5">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Main Grid Form --}}
  <form id="refundForm" method="POST" action="{{ route('admin.refunds.store') }}" enctype="multipart/form-data"
        class="mt-8 grid gap-6 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,0.8fr)] items-start">
    @csrf

    {{-- LEFT CARD: FORM CONTROLS --}}
    <div class="reveal rounded-3xl refund-form-card p-6 md:p-8 space-y-6">
      
      {{-- Input & Check --}}
      <div class="space-y-2.5">
        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Kode Pesanan</label>
        <div class="flex flex-col sm:flex-row gap-2">
          <input id="codeInput" name="code" value="{{ old('code') }}" placeholder="Contoh: MP-00115"
                 class="h-11 w-full rounded-xl bg-black/40 border border-white/10 px-4 text-xs font-semibold text-white placeholder:text-slate-600 outline-none focus:border-violet-500/60 focus:ring-1 focus:ring-violet-500/30 transition-all">
          <button type="button" id="btnCheck"
                  class="h-11 sm:w-28 rounded-xl border border-white/10 bg-white/5 hover:border-violet-500/50 hover:bg-violet-600 text-xs font-bold text-white transition-all shadow-sm">
            Cek Validitas
          </button>
        </div>
        
        <p id="checkMsg" class="text-[11px] font-semibold text-slate-500">Klik Cek Validitas untuk memverifikasi data transaksi di database.</p>

        {{-- Dynamic Order Preview Panel --}}
        <div id="orderPreview" class="hidden rounded-2xl border border-white/5 bg-black/30 p-4 text-[11px] space-y-2">
          <div class="text-[9px] font-extrabold uppercase tracking-widest text-violet-400 mb-1 block">🔍 Ringkasan Data Pesanan</div>
          
          <div class="flex justify-between gap-3 py-1 border-b border-white/[0.02]">
            <span class="text-slate-400 font-semibold">Nama Produk</span>
            <span id="pvProduct" class="text-white font-bold text-right max-w-[200px] truncate">-</span>
          </div>
          <div class="flex justify-between gap-3 py-1 border-b border-white/[0.02]">
            <span class="text-slate-400 font-semibold">Tujuan / Target</span>
            <span id="pvTarget" class="text-white font-mono font-medium text-right">-</span>
          </div>
          <div class="flex justify-between gap-3 py-1 border-b border-white/[0.02]">
            <span class="text-slate-400 font-semibold">Total Pembayaran</span>
            <span id="pvTotal" class="text-violet-300 font-extrabold text-right">-</span>
          </div>
          <div class="flex justify-between gap-3 py-1 border-b border-white/[0.02]">
            <span class="text-slate-400 font-semibold">Status Transaksi</span>
            <span id="pvStatus" class="text-white font-bold text-right uppercase">-</span>
          </div>
          <div class="flex justify-between gap-3 py-1">
            <span class="text-slate-400 font-semibold">Status Pembayaran</span>
            <span id="pvPay" class="text-slate-300 font-semibold text-right uppercase">-</span>
          </div>
        </div>
      </div>

      <div class="h-px bg-white/5"></div>

      {{-- Select Refund Method --}}
      <div class="space-y-3">
        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Pilih Opsi Metode Refund</label>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          
          {{-- Option Saldo Wallet --}}
          <label class="flex items-start gap-3 rounded-2xl border border-white/10 bg-black/40 p-4 cursor-pointer hover:border-violet-500/30 transition-all group relative overflow-hidden">
            <input type="radio" name="refund_method" value="wallet" disabled class="accent-violet-500 size-4 mt-0.5">
            <div class="flex flex-col">
              <span class="text-xs font-bold text-white group-hover:text-violet-300 transition-colors">Saldo Maitri</span>
              <span class="text-[10px] text-slate-500 mt-1 leading-normal font-semibold">Mengembalikan dana instan langsung ke dompet digital pelanggan.</span>
            </div>
          </label>

          {{-- Option Manual Transfer --}}
          <label class="flex items-start gap-3 rounded-2xl border border-white/10 bg-black/40 p-4 cursor-pointer hover:border-violet-500/30 transition-all group relative overflow-hidden">
            <input type="radio" name="refund_method" value="manual_transfer" disabled class="accent-violet-500 size-4 mt-0.5">
            <div class="flex flex-col">
              <span class="text-xs font-bold text-white group-hover:text-violet-300 transition-colors">Transfer Manual</span>
              <span class="text-[10px] text-slate-500 mt-1 leading-normal font-semibold">Kirim manual (Bank/E-Wallet) dan upload bukti transaksi transfer.</span>
            </div>
          </label>

        </div>

        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wide">⚠️ Opsi metode di atas akan aktif otomatis setelah Cek Validitas berhasil.</p>
      </div>

      {{-- Wallet target user search panel --}}
      <div id="walletSection" class="hidden space-y-2.5">
        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Target Akun Pengguna</label>
        <input type="hidden" name="target_user_id" id="targetUserId">

        <div class="relative">
          <input id="userSearch" type="text" disabled
                 placeholder="Ketik minimal 2 karakter: nama / email pelanggan..."
                 class="h-11 w-full rounded-xl bg-black/40 border border-white/10 px-4 text-xs font-semibold text-white placeholder:text-slate-600 outline-none focus:border-violet-500/60 focus:ring-1 focus:ring-violet-500/30 transition-all">
          
          <div id="userDropdown"
               class="hidden absolute z-20 mt-2 w-full rounded-2xl border border-white/5 bg-[#080f1d] overflow-hidden shadow-2xl backdrop-blur-2xl">
            <div id="userList" class="max-h-60 overflow-y-auto no-scrollbar divide-y divide-white/[0.03]"></div>
          </div>
        </div>

        <div id="userPicked" class="hidden text-xs font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-3.5 py-2.5 rounded-xl flex items-center gap-2">
          <span class="size-1.5 rounded-full bg-emerald-400"></span>
          Target Dipilih: <span id="pickedLabel" class="text-white font-mono font-medium"></span>
        </div>
      </div>

      {{-- Manual transfer proof upload --}}
      <div id="manualSection" class="hidden space-y-2.5">
        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Upload Bukti Transfer</label>
        <input type="file" name="manual_proof" id="manualProof" disabled
               class="block w-full text-xs text-slate-300 file:mr-4 file:rounded-xl file:border-0 file:bg-white/5 file:border-white/10 file:px-4 file:py-2 file:text-xs file:font-bold file:text-white hover:file:bg-violet-600 hover:file:text-white transition-all cursor-pointer">
        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wide">Format diizinkan: JPG, PNG, PDF (Maks 4 MB).</p>
      </div>

      {{-- Administrative Note Input --}}
      <div class="space-y-2.5">
        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Catatan Administrasi</label>
        <input type="text" name="note" value="{{ old('note') }}" disabled id="noteInput"
               class="h-11 w-full rounded-xl bg-black/40 border border-white/10 px-4 text-xs font-semibold text-white placeholder:text-slate-600 outline-none focus:border-violet-500/60 focus:ring-1 focus:ring-violet-500/30 transition-all"
               placeholder="Contoh: Pembatalan karena provider gangguan, kirim manual ke rek BNI.">
      </div>

      <button id="btnSubmit" type="submit" disabled
              class="w-full h-12 rounded-xl bg-violet-600/20 text-slate-400 text-xs font-extrabold uppercase tracking-widest cursor-not-allowed transition-all shadow-md">
        Proses & Simpan Refund
      </button>
    </div>

    {{-- RIGHT CARD: SYSTEM COMPLIANCE BOX --}}
    <div class="reveal rounded-3xl rules-card p-6 md:p-8 text-xs font-semibold text-slate-400 space-y-4">
      <div class="flex items-center gap-2 text-violet-300 font-extrabold text-sm uppercase tracking-wide pb-2.5 border-b border-violet-500/10">
        🛡️ Kebijakan & Aturan Validasi
      </div>
      
      <p class="leading-relaxed">Setiap tindakan refund wajib mengikuti standarisasi verifikasi sistem otomatis berikut:</p>
      
      <ul class="space-y-3">
        <li class="flex items-start gap-2.5">
          <span class="text-violet-400 mt-0.5">●</span>
          <p class="leading-relaxed"><b class="text-white">Status Gagal</b>: Rujukan transaksi harus berstatus akhir <span class="text-rose-400">FAILED</span> dan status pembayaran wajib terverifikasi <span class="text-emerald-400">PAID</span>.</p>
        </li>
        <li class="flex items-start gap-2.5">
          <span class="text-violet-400 mt-0.5">●</span>
          <p class="leading-relaxed"><b class="text-white">Anti Duplikasi</b>: Transaksi dengan kode referensi terkait sama sekali belum pernah diajukan refund sebelumnya.</p>
        </li>
        <li class="flex items-start gap-2.5">
          <span class="text-violet-400 mt-0.5">●</span>
          <p class="leading-relaxed"><b class="text-white">Verifikasi Target Dompet</b>: Saat menggunakan Saldo Maitri, pastikan Anda memilih profil akun user yang tepat dari daftar dropdown pencarian cepat.</p>
        </li>
        <li class="flex items-start gap-2.5">
          <span class="text-violet-400 mt-0.5">●</span>
          <p class="leading-relaxed"><b class="text-white">Mandatory Proof Upload</b>: Pengajuan transfer manual wajib mengunggah file bukti pembayaran resmi demi kebutuhan transparansi audit.</p>
        </li>
      </ul>
    </div>
  </form>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

      const codeInput = document.getElementById('codeInput');
      const btnCheck = document.getElementById('btnCheck');
      const checkMsg = document.getElementById('checkMsg');

      const orderPreview = document.getElementById('orderPreview');
      const pvProduct = document.getElementById('pvProduct');
      const pvTarget = document.getElementById('pvTarget');
      const pvTotal = document.getElementById('pvTotal');
      const pvStatus = document.getElementById('pvStatus');
      const pvPay = document.getElementById('pvPay');

      const refundRadios = document.querySelectorAll('input[name="refund_method"]');
      const walletSection = document.getElementById('walletSection');
      const manualSection = document.getElementById('manualSection');
      const userSearch = document.getElementById('userSearch');
      const userDropdown = document.getElementById('userDropdown');
      const userList = document.getElementById('userList');
      const targetUserId = document.getElementById('targetUserId');
      const userPicked = document.getElementById('userPicked');
      const pickedLabel = document.getElementById('pickedLabel');

      const manualProof = document.getElementById('manualProof');
      const noteInput = document.getElementById('noteInput');
      const btnSubmit = document.getElementById('btnSubmit');

      let eligible = false;

      function setCheckState(ok, msg) {
        checkMsg.textContent = msg;
        checkMsg.className = ok
          ? 'text-[11px] font-bold text-emerald-400'
          : 'text-[11px] font-bold text-rose-400';
      }

      function enableForm() {
        refundRadios.forEach(r => r.disabled = false);
        noteInput.disabled = false;
      }

      function disableForm() {
        refundRadios.forEach(r => { r.disabled = true; r.checked = false; });
        walletSection.classList.add('hidden');
        manualSection.classList.add('hidden');
        userSearch.disabled = true;
        manualProof.disabled = true;
        noteInput.disabled = true;
        btnSubmit.disabled = true;
        btnSubmit.className = 'w-full h-12 rounded-xl bg-violet-600/20 text-slate-400 text-xs font-extrabold uppercase tracking-widest cursor-not-allowed transition-all shadow-md';
        targetUserId.value = '';
        userPicked.classList.add('hidden');
        eligible = false;
      }

      function enableSubmitIfReady() {
        const method = document.querySelector('input[name="refund_method"]:checked')?.value;
        if (!eligible || !method) {
          btnSubmit.disabled = true;
          btnSubmit.className = 'w-full h-12 rounded-xl bg-violet-600/20 text-slate-400 text-xs font-extrabold uppercase tracking-widest cursor-not-allowed transition-all shadow-md';
          return;
        }

        if (method === 'wallet') {
          const hasTarget = !!targetUserId.value;
          btnSubmit.disabled = !hasTarget;
          btnSubmit.className = hasTarget
            ? 'w-full h-12 rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-white text-xs font-extrabold uppercase tracking-widest transition-all shadow-[0_0_15px_rgba(139,92,246,0.25)]'
            : 'w-full h-12 rounded-xl bg-violet-600/20 text-slate-400 text-xs font-extrabold uppercase tracking-widest cursor-not-allowed transition-all shadow-md';
          return;
        }

        if (method === 'manual_transfer') {
          const hasFile = manualProof.files && manualProof.files.length > 0;
          btnSubmit.disabled = !hasFile;
          btnSubmit.className = hasFile
            ? 'w-full h-12 rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-white text-xs font-extrabold uppercase tracking-widest transition-all shadow-[0_0_15px_rgba(139,92,246,0.25)]'
            : 'w-full h-12 rounded-xl bg-violet-600/20 text-slate-400 text-xs font-extrabold uppercase tracking-widest cursor-not-allowed transition-all shadow-md';
          return;
        }
      }

      async function checkCode() {
        const code = (codeInput.value || '').trim();
        if (!code) {
          setCheckState(false, '⚠️ Kode pesanan wajib diisi.');
          disableForm();
          return;
        }

        setCheckState(true, '🌀 Memeriksa status di database...');
        disableForm();

        try {
          const res = await fetch("{{ route('admin.refunds.check') }}", {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrf,
              'Accept': 'application/json',
            },
            body: JSON.stringify({ code }),
          });

          const json = await res.json();

          if (!json.ok || !json.eligible) {
            setCheckState(false, '❌ ' + (json.message || 'Transaksi tidak valid untuk diajukan refund.'));
            orderPreview.classList.add('hidden');
            return;
          }

          eligible = true;
          setCheckState(true, '✅ ' + (json.message || 'Transaksi terverifikasi valid untuk diajukan refund.'));
          enableForm();

          // preview
          const o = json.order || {};
          pvProduct.textContent = (o.product || '-') + ' • ' + (o.variant || '-');
          pvTarget.textContent = o.target || '-';
          pvTotal.textContent = 'Rp ' + Number(o.total || 0).toLocaleString('id-ID');
          pvStatus.textContent = (o.status || '-').toUpperCase();
          pvPay.textContent = ((o.payment_status || '-') + ' / ' + (o.payment_method || '-')).toUpperCase();
          orderPreview.classList.remove('hidden');
        } catch(e) {
          console.error(e);
          setCheckState(false, '❌ Terjadi kesalahan jaringan saat validasi.');
        }
      }

      btnCheck.addEventListener('click', checkCode);

      refundRadios.forEach(r => {
        r.addEventListener('change', () => {
          walletSection.classList.add('hidden');
          manualSection.classList.add('hidden');
          userSearch.disabled = true;
          manualProof.disabled = true;
          targetUserId.value = '';
          userPicked.classList.add('hidden');

          if (r.checked && r.value === 'wallet') {
            walletSection.classList.remove('hidden');
            userSearch.disabled = false;
          }
          if (r.checked && r.value === 'manual_transfer') {
            manualSection.classList.remove('hidden');
            manualProof.disabled = false;
          }

          enableSubmitIfReady();
        });
      });

      manualProof.addEventListener('change', enableSubmitIfReady);

      // user search dropdown
      let userTimer = null;
      userSearch.addEventListener('input', () => {
        clearTimeout(userTimer);
        userTimer = setTimeout(async () => {
          const q = (userSearch.value || '').trim();
          if (q.length < 2) {
            userDropdown.classList.add('hidden');
            userList.innerHTML = '';
            return;
          }

          try {
            const res = await fetch("{{ route('admin.refunds.users.search') }}?q=" + encodeURIComponent(q), {
              headers: { 'Accept': 'application/json' }
            });
            const json = await res.json();
            const items = json.items || [];

            userList.innerHTML = items.length
              ? items.map(u => `
                  <button type="button"
                          class="w-full text-left px-4 py-3 hover:bg-white/[0.03] transition-colors border-b border-white/[0.03] last:border-b-0 flex flex-col gap-0.5">
                    <div class="text-xs font-bold text-white">${u.name}</div>
                    <div class="text-[10px] text-slate-400 font-mono">${u.email}</div>
                    <span class="hidden" data-id="${u.id}" data-name="${u.name}" data-email="${u.email}"></span>
                  </button>
                `).join('')
              : `<div class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">User tidak ditemukan.</div>`;

            userDropdown.classList.remove('hidden');

            // attach click
            Array.from(userList.querySelectorAll('button')).forEach(btn => {
              btn.addEventListener('click', () => {
                const meta = btn.querySelector('span[data-id]');
                targetUserId.value = meta.dataset.id;
                pickedLabel.textContent = `${meta.dataset.name} (${meta.dataset.email})`;
                userPicked.classList.remove('hidden');
                userDropdown.classList.add('hidden');
                enableSubmitIfReady();
              });
            });
          } catch(e) {
            console.error(e);
          }
        }, 250);
      });

      document.addEventListener('click', (e) => {
        if (!userDropdown.contains(e.target) && e.target !== userSearch) {
          userDropdown.classList.add('hidden');
        }
      });

      disableForm();
    });
  </script>
@endsection
