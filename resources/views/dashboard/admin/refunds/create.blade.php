@extends('layouts.admin')
@section('title', 'Buat Refund — Admin')

@section('content')
  <div class="flex items-center justify-between gap-3">
    <div>
      <h1 class="text-2xl md:text-3xl font-semibold">Buat Refund</h1>
      <p class="mt-1 text-slate-400 text-sm">Masukkan kode order, cek validitas, lalu proses refund.</p>
    </div>
    <a href="{{ route('admin.refunds.index') }}"
       class="text-sm text-slate-300 hover:text-slate-100 underline">← Kembali</a>
  </div>

  @if(session('success'))
    <div class="mt-4 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
      {{ session('success') }}
    </div>
  @endif

  @if($errors->any())
    <div class="mt-4 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
      <ul class="list-disc pl-5 space-y-1">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form id="refundForm" method="POST" action="{{ route('admin.refunds.store') }}" enctype="multipart/form-data"
        class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,0.8fr)] items-start">
    @csrf

    {{-- KIRI --}}
    <div class="rounded-2xl border border-slate-800/70 bg-[#0E1524] p-4 md:p-5 space-y-5">
      <div class="space-y-2">
        <label class="text-xs text-slate-400">Kode Pesanan</label>
        <div class="flex flex-col sm:flex-row gap-2">
          <input id="codeInput" name="code" value="{{ old('code') }}" placeholder="Contoh: MP-00115"
                 class="w-full rounded-xl bg-slate-950 border border-slate-800 px-3 py-2.5 text-sm text-slate-100 focus:outline-none focus:border-violet-500">
          <button type="button" id="btnCheck"
                  class="rounded-xl border border-slate-700/70 px-4 py-2.5 text-sm text-slate-200 hover:border-violet-400">
            Check
          </button>
        </div>
        <p id="checkMsg" class="text-xs text-slate-400">Klik Check untuk validasi order.</p>

        <div id="orderPreview" class="hidden rounded-xl border border-slate-800 bg-slate-950/40 p-3 text-xs space-y-1">
          <div class="flex justify-between gap-3">
            <span class="text-slate-400">Produk</span>
            <span id="pvProduct" class="text-slate-100 text-right">-</span>
          </div>
          <div class="flex justify-between gap-3">
            <span class="text-slate-400">Target</span>
            <span id="pvTarget" class="text-slate-100 font-mono text-right">-</span>
          </div>
          <div class="flex justify-between gap-3">
            <span class="text-slate-400">Total</span>
            <span id="pvTotal" class="text-slate-100 text-right">-</span>
          </div>
          <div class="flex justify-between gap-3">
            <span class="text-slate-400">Status</span>
            <span id="pvStatus" class="text-slate-100 text-right">-</span>
          </div>
          <div class="flex justify-between gap-3">
            <span class="text-slate-400">Payment</span>
            <span id="pvPay" class="text-slate-100 text-right">-</span>
          </div>
        </div>
      </div>

      <div class="h-px bg-slate-800/70"></div>

      <div class="space-y-2">
        <label class="text-xs text-slate-400">Metode Refund</label>

        <div class="grid sm:grid-cols-2 gap-2">
          <label class="flex items-center gap-2 rounded-2xl border border-slate-800/80 px-3 py-2.5 text-sm cursor-pointer">
            <input type="radio" name="refund_method" value="wallet" disabled>
            <span>Saldo Maitri</span>
          </label>

          <label class="flex items-center gap-2 rounded-2xl border border-slate-800/80 px-3 py-2.5 text-sm cursor-pointer">
            <input type="radio" name="refund_method" value="manual_transfer" disabled>
            <span>Transfer Manual</span>
          </label>
        </div>

        <p class="text-xs text-slate-500">Opsi akan aktif setelah kode order valid.</p>
      </div>

      {{-- Wallet target user --}}
      <div id="walletSection" class="hidden space-y-2">
        <label class="text-xs text-slate-400">Target Akun (untuk refund ke Saldo Maitri)</label>

        <input type="hidden" name="target_user_id" id="targetUserId">

        <div class="relative">
          <input id="userSearch" type="text" disabled
                 placeholder="Cari user: nama atau email (min 2 karakter)"
                 class="w-full rounded-xl bg-slate-950 border border-slate-800 px-3 py-2.5 text-sm text-slate-100 focus:outline-none focus:border-violet-500">
          <div id="userDropdown"
               class="hidden absolute z-20 mt-2 w-full rounded-xl border border-slate-800 bg-[#0B1120] overflow-hidden">
            <div id="userList" class="max-h-64 overflow-auto"></div>
          </div>
        </div>

        <div id="userPicked" class="hidden text-xs text-emerald-200">
          Target dipilih: <span id="pickedLabel" class="font-medium"></span>
        </div>
      </div>

      {{-- Manual transfer proof --}}
      <div id="manualSection" class="hidden space-y-2">
        <label class="text-xs text-slate-400">Upload Bukti Transfer</label>
        <input type="file" name="manual_proof" id="manualProof" disabled
               class="block w-full text-sm text-slate-200 file:mr-3 file:rounded-xl file:border-0 file:bg-slate-800/60 file:px-4 file:py-2 file:text-slate-100 hover:file:bg-slate-700/60">
        <p class="text-xs text-slate-500">Format: jpg/png/pdf, max 4MB</p>
      </div>

      <div class="space-y-2">
        <label class="text-xs text-slate-400">Catatan (opsional)</label>
        <input type="text" name="note" value="{{ old('note') }}" disabled id="noteInput"
               class="w-full rounded-xl bg-slate-950 border border-slate-800 px-3 py-2.5 text-sm text-slate-100 focus:outline-none focus:border-violet-500"
               placeholder="Contoh: Sudah konfirmasi pembeli via WA">
      </div>

      <button id="btnSubmit" type="submit" disabled
              class="w-full h-11 rounded-2xl bg-violet-600/40 text-slate-200 font-semibold cursor-not-allowed">
        Submit Refund
      </button>
    </div>

    {{-- KANAN: help box --}}
    <div class="rounded-2xl border border-slate-800/70 bg-[#0E1524] p-4 md:p-5 text-sm space-y-2">
      <div class="font-medium text-slate-100">Rules validasi</div>
      <ul class="list-disc pl-5 text-slate-400 text-xs space-y-1">
        <li>Order harus <b>FAILED</b> dan payment status <b>PAID</b>.</li>
        <li>Order belum pernah <b>REFUNDED</b>.</li>
        <li>Refund ke saldo: pilih target user yang benar (nama/email).</li>
        <li>Refund manual: wajib upload bukti transfer.</li>
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
          ? 'text-xs text-emerald-300'
          : 'text-xs text-rose-300';
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
        btnSubmit.className = 'w-full h-11 rounded-2xl bg-violet-600/40 text-slate-200 font-semibold cursor-not-allowed';
        targetUserId.value = '';
        userPicked.classList.add('hidden');
        eligible = false;
      }

      function enableSubmitIfReady() {
        const method = document.querySelector('input[name="refund_method"]:checked')?.value;
        if (!eligible || !method) {
          btnSubmit.disabled = true;
          btnSubmit.className = 'w-full h-11 rounded-2xl bg-violet-600/40 text-slate-200 font-semibold cursor-not-allowed';
          return;
        }

        if (method === 'wallet') {
          const hasTarget = !!targetUserId.value;
          btnSubmit.disabled = !hasTarget;
          btnSubmit.className = hasTarget
            ? 'w-full h-11 rounded-2xl bg-violet-600 hover:bg-violet-500 text-slate-50 font-semibold'
            : 'w-full h-11 rounded-2xl bg-violet-600/40 text-slate-200 font-semibold cursor-not-allowed';
          return;
        }

        if (method === 'manual_transfer') {
          const hasFile = manualProof.files && manualProof.files.length > 0;
          btnSubmit.disabled = !hasFile;
          btnSubmit.className = hasFile
            ? 'w-full h-11 rounded-2xl bg-violet-600 hover:bg-violet-500 text-slate-50 font-semibold'
            : 'w-full h-11 rounded-2xl bg-violet-600/40 text-slate-200 font-semibold cursor-not-allowed';
          return;
        }
      }

      async function checkCode() {
        const code = (codeInput.value || '').trim();
        if (!code) {
          setCheckState(false, 'Kode pesanan wajib diisi.');
          disableForm();
          return;
        }

        setCheckState(true, 'Memeriksa...');
        disableForm();

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
          setCheckState(false, json.message || 'Tidak valid.');
          orderPreview.classList.add('hidden');
          return;
        }

        eligible = true;
        setCheckState(true, json.message || 'Valid.');
        enableForm();

        // preview
        const o = json.order || {};
        pvProduct.textContent = (o.product || '-') + ' - ' + (o.variant || '-');
        pvTarget.textContent = o.target || '-';
        pvTotal.textContent = 'Rp ' + Number(o.total || 0).toLocaleString('id-ID');
        pvStatus.textContent = (o.status || '-').toUpperCase();
        pvPay.textContent = ((o.payment_status || '-') + ' / ' + (o.payment_method || '-')).toUpperCase();
        orderPreview.classList.remove('hidden');
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

          const res = await fetch("{{ route('admin.refunds.users.search') }}?q=" + encodeURIComponent(q), {
            headers: { 'Accept': 'application/json' }
          });
          const json = await res.json();
          const items = json.items || [];

          userList.innerHTML = items.length
            ? items.map(u => `
                <button type="button"
                        class="w-full text-left px-3 py-2 hover:bg-white/5 border-b border-slate-800/70 last:border-b-0">
                  <div class="text-sm text-slate-100">${u.name}</div>
                  <div class="text-xs text-slate-400">${u.email}</div>
                  <span class="hidden" data-id="${u.id}" data-name="${u.name}" data-email="${u.email}"></span>
                </button>
              `).join('')
            : `<div class="px-3 py-2 text-xs text-slate-400">Tidak ada user.</div>`;

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
        }, 250);
      });

      document.addEventListener('click', (e) => {
        if (!userDropdown.contains(e.target) && e.target !== userSearch) {
          userDropdown.classList.add('hidden');
        }
      });

      // init
      disableForm();
    });
  </script>
@endsection
