// ------- MOCK DATA (ganti ke Digiflazz nanti) -------
const PRODUCT_DB = {
  // slug contoh: "paket-data-telkomsel"
  "paket-data-telkomsel": {
    name: "Paket Data Internet — Telkomsel",
    meta: "Kategori: Data • Subkategori: Telkomsel",
    description: [
      "Paket Data Telkomsel. Kuota nasional, aktif instan setelah pembayaran.",
      "Beberapa paket menyertakan kuota apps tertentu (mis. Videomax).",
      "Nomor berlaku: semua nomor Telkomsel (Simpati, AS, Loop).",
      "Format tujuan: Nomor HP Telkomsel Anda."
    ],
    // Groups -> daftar varian
    groups: {
      "Umum": [
        { code: "TS-UMUM-1", name: "1 GB + 5 GB Videomax / 30 Hr", price: 24260 },
        { code: "TS-UMUM-2", name: "3 GB + 12 GB Videomax / 30 Hr", price: 56625 },
        { code: "TS-UMUM-3", name: "7 GB + 28 GB Videomax / 30 Hr", price: 104750 },
        { code: "TS-UMUM-4", name: "15 GB + 40 GB Videomax / 30 Hr", price: 155750 },
      ],
      "Harian": [
        { code: "TS-H-1", name: "1 GB / 1 Hari", price: 9000 },
        { code: "TS-H-2", name: "2 GB / 3 Hari", price: 17000 },
      ],
      "Bulanan": [
        { code: "TS-B-1", name: "10 GB / 30 Hari", price: 52000 },
        { code: "TS-B-2", name: "20 GB / 30 Hari", price: 82000 },
      ],
      "Malam": [
        { code: "TS-M-1", name: "5 GB Malam / 30 Hari", price: 15000 },
        { code: "TS-M-2", name: "15 GB Malam / 30 Hari", price: 30000 },
      ],
    }
  }
};

// Metode pembayaran (contoh)
const PAYMENTS = [
  { group: "E-Wallet & QRIS", items: [
    { id: "qris", name: "QRIS", fee: 1000 },
    { id: "dana", name: "DANA", fee: 1500 },
    { id: "ovo",  name: "OVO",  fee: 1500 },
  ]},
  { group: "Convenience Store", items: [
    { id: "alfamart", name: "Alfamart", fee: 2500 },
    { id: "indomaret", name: "Indomaret", fee: 3000 },
  ]},
  { group: "Virtual Account", items: [
    { id: "bca",   name: "BCA", fee: 2500 },
    { id: "bni",   name: "BNI", fee: 2000 },
    { id: "bri",   name: "BRI", fee: 2000 },
    { id: "mandiri", name: "Mandiri", fee: 2500 },
  ]},
];

// ------- STATE -------
const state = {
  slug: null,
  product: null,
  group: null,
  variant: null,   // {code,name,price}
  pay: null,       // {group,id,name,fee}
  target: "",
  email: ""
};

// ------- HELPERS -------
const fmtIDR = (n) => n.toLocaleString("id-ID");
const el = (sel) => document.querySelector(sel);

function updateSummary(){
  const sub = state.variant ? state.variant.price : 0;
  const fee = state.pay ? state.pay.fee : 0;
  const total = sub + fee;

  el("#sProd").textContent = state.product?.name || "—";
  el("#sVar").textContent  = state.variant ? `${state.variant.name}` : "—";
  el("#sPay").textContent  = state.pay ? `${state.pay.name}` : "—";
  el("#sSub").textContent  = `Rp ${fmtIDR(sub)}`;
  el("#sFee").textContent  = `Rp ${fmtIDR(fee)}`;
  el("#sTotal").textContent= `Rp ${fmtIDR(total)}`;

  // Enabled jika semua terisi
  const ready = !!(state.target && state.email && state.variant && state.pay);
  const btn = el("#btnCheckout");
  btn.disabled = !ready;
}

function renderDescription(list){
  const box = el("#pDescription");
  box.innerHTML = list.map(t => `
    <p>• ${t}</p>
  `).join("");
}

function renderVariantTabs(groups){
  const cont = el("#variantTabs");
  cont.innerHTML = "";
  Object.keys(groups).forEach((g, idx) => {
    const b = document.createElement("button");
    b.className = "px-4 py-2 rounded-xl border border-slate-800/70 text-sm hover:border-violet-700/60";
    b.textContent = g;
    if ((state.group && state.group === g) || (!state.group && idx===0)) {
      b.classList.add("bg-violet-600","border-violet-700/60");
      state.group = g;
    }
    b.addEventListener("click", () => {
      state.group = g;
      // reset highlight
      cont.querySelectorAll("button").forEach(x=>x.classList.remove("bg-violet-600","border-violet-700/60"));
      b.classList.add("bg-violet-600","border-violet-700/60");
      state.variant = null;
      renderVariantGrid();
      updateSummary();
    });
    cont.appendChild(b);
  });
}

function renderVariantGrid(){
  const grid = el("#variantGrid");
  const list = state.product.groups[state.group] || [];
  grid.innerHTML = list.map(v => `
    <button class="v-item text-left p-4 rounded-2xl border border-slate-800/70 hover:border-violet-700/60 bg-[#0E1524]">
      <div class="font-medium">${v.name}</div>
      <div class="mt-1 text-sm text-slate-400">Rp ${fmtIDR(v.price)}</div>
    </button>
  `).join("");

  // bind
  grid.querySelectorAll(".v-item").forEach((btn, i) => {
    btn.addEventListener("click", ()=>{
      grid.querySelectorAll(".v-item").forEach(x=>x.classList.remove("ring-2","ring-violet-500/30","border-violet-700/60"));
      btn.classList.add("ring-2","ring-violet-500/30","border-violet-700/60");
      state.variant = (state.product.groups[state.group] || [])[i];
      updateSummary();
    });
  });
}

function renderPayments(){
  const wrap = el("#payList");
  wrap.innerHTML = PAYMENTS.map((g, gi) => `
    <details class="group rounded-2xl border border-slate-800/70 bg-[#111826] p-3" ${gi===0?'open':''}>
      <summary class="list-none cursor-pointer flex items-center justify-between">
        <span class="font-medium">${g.group}</span>
        <svg class="size-5 text-slate-400 group-open:rotate-180 transition" viewBox="0 0 24 24" fill="none"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="1.5"/></svg>
      </summary>
      <div class="mt-3 space-y-2">
        ${g.items.map(it=>`
          <label class="flex items-center justify-between gap-3 rounded-xl border border-slate-800/70 px-3 py-2 cursor-pointer hover:border-violet-700/60">
            <div class="flex items-center gap-3">
              <input type="radio" name="pay" value="${g.group}|${it.id}" class="accent-violet-600">
              <span>${it.name}</span>
            </div>
            <span class="text-sm text-slate-400">Biaya Rp ${fmtIDR(it.fee)}</span>
          </label>
        `).join("")}
      </div>
    </details>
  `).join("");

  // bind
  wrap.querySelectorAll('input[name="pay"]').forEach(r=>{
    r.addEventListener("change", ()=>{
      const [group, id] = r.value.split("|");
      const g = PAYMENTS.find(x=>x.group===group);
      const it = g.items.find(x=>x.id===id);
      state.pay = { group, ...it };
      updateSummary();
    });
  });
}

// ------- INIT -------
(function init(){
  const holder = document.getElementById("productPage");
  state.slug = holder?.dataset?.slug || "";

  // load produk (mock)
  state.product = PRODUCT_DB[state.slug] || PRODUCT_DB["paket-data-telkomsel"];
  el("#pName").textContent = state.product.name;
  el("#pMeta").textContent = state.product.meta;
  renderDescription(state.product.description);

  // varian
  renderVariantTabs(state.product.groups);
  renderVariantGrid();

  // pembayaran
  renderPayments();

  // form
  el("#fTarget").addEventListener("input", (e)=>{ state.target = (e.target.value||"").trim(); updateSummary(); });
  el("#fEmail").addEventListener("input", (e)=>{ state.email = (e.target.value||"").trim(); updateSummary(); });

  // CTA
  el("#btnCheckout").addEventListener("click", ()=>{
    if (el("#btnCheckout").disabled) return;
    // sementara hanya alert (nanti arahkan ke halaman pembayaran/invoice)
    alert(`Pesanan:
- Produk: ${state.product.name}
- Varian: ${state.variant?.name} (Rp ${fmtIDR(state.variant?.price||0)})
- Metode: ${state.pay?.name}
- Tujuan: ${state.target}
- Email: ${state.email}
Total: Rp ${fmtIDR((state.variant?.price||0)+(state.pay?.fee||0))}`);
  });

  updateSummary();
})();
