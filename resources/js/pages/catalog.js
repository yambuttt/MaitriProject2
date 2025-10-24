// ====== DATA HIRARKI ======
const CATEGORY_MAP = {
  "Game": ["Mobile Legends", "Free Fire", "Genshin Impact", "PUBG Mobile"],
  "Pulsa": ["Telkomsel", "Indosat", "XL", "Tri", "Smartfren"],
  "Data": ["Telkomsel", "Indosat", "XL", "Tri", "Smartfren"],
  "PLN": ["Token", "Tagihan"],
  "e-Wallet": ["GoPay", "OVO", "DANA", "ShopeePay", "LinkAja"]
};

// MOCK produk (nanti ganti API Digiflazz). Isi subcategory konsisten dgn CATEGORY_MAP.
const MOCK = (() => {
  const cats = Object.keys(CATEGORY_MAP);
  let id = 1;
  const items = [];
  for (const cat of cats) {
    const subs = CATEGORY_MAP[cat];
    for (let i = 0; i < 30; i++) {
      const sub = subs[i % subs.length];
      items.push({
        id: id++,
        name: `${cat} - ${sub} Paket ${1 + (i % 6)}`,
        category: cat,
        subcategory: sub,
        provider: sub, // sederhanakan
        price: 5000 + ((i % 20) * 2000),
        eta: "±1–2 menit"
      });
    }
  }
  return items;
})();

// ====== STATE ======
const state = {
  page: 1,
  perPage: 9,
  qName: "",
  qProvider: "",
  category: null,
  subcategory: null,
  sort: "popular"
};

// ====== RENDER SUBKATEGORI DINAMIS ======
function renderSubcategories() {
  const wrap = document.getElementById("subcatPanel");
  const empty = document.getElementById("subcatEmpty");
  wrap.innerHTML = "";

  if (!state.category) {
    if (empty) {
      empty.classList.remove("hidden");
    } else {
      const node = document.createElement("div");
      node.id = "subcatEmpty";
      node.className = "col-span-2 text-xs text-slate-500";
      node.textContent = "Pilih kategori dulu.";
      wrap.appendChild(node);
    }
    return;
  }

  const subs = CATEGORY_MAP[state.category] || [];
  if (empty) empty.classList.add("hidden");

  subs.forEach(sub => {
    const btn = document.createElement("button");
    btn.className = "subcat-chip px-3 py-1.5 rounded-xl border border-slate-800/70 text-sm hover:border-violet-700/60";
    btn.dataset.value = sub;
    btn.textContent = sub;
    if (state.subcategory === sub) {
      btn.classList.add("ring-2", "ring-violet-500/30", "border-violet-700/60");
    }
    btn.addEventListener("click", () => {
      // toggle
      if (state.subcategory === sub) state.subcategory = null;
      else state.subcategory = sub;

      // update highlight
      wrap.querySelectorAll(".subcat-chip").forEach(c => c.classList.remove("ring-2","ring-violet-500/30","border-violet-700/60"));
      if (state.subcategory) {
        btn.classList.add("ring-2","ring-violet-500/30","border-violet-700/60");
      }
      state.page = 1;
      render();
    });
    wrap.appendChild(btn);
  });
}

// ====== FILTERING ======
function applyFilters() {
  let list = [...MOCK];

  if (state.category) list = list.filter(x => x.category === state.category);
  if (state.subcategory) list = list.filter(x => x.subcategory === state.subcategory);
  if (state.qName) list = list.filter(x => x.name.toLowerCase().includes(state.qName));
  if (state.qProvider) list = list.filter(x => x.provider.toLowerCase().includes(state.qProvider));

  if (state.sort === "price_asc") list.sort((a, b) => a.price - b.price);
  if (state.sort === "price_desc") list.sort((a, b) => b.price - a.price);

  const total = list.length;
  const start = (state.page - 1) * state.perPage;
  const end = start + state.perPage;
  return { total, pages: Math.max(1, Math.ceil(total / state.perPage)), items: list.slice(start, end) };
}

// ====== RENDER PRODUK & HEADER ======
function slugify(str){
  return String(str)
    .toLowerCase()
    .replace(/[^a-z0-9]+/g,'-')
    .replace(/(^-|-$)+/g,'');
}

function render(){
  const grid = document.getElementById('productGrid');
  const info = document.getElementById('resultInfo');
  const pageInfo = document.getElementById('pageInfo');
  const prev = document.getElementById('prevPage');
  const next = document.getElementById('nextPage');

  const {total, pages, items} = applyFilters();

  info.textContent = `Menampilkan ${items.length} dari ${total} produk`;
  pageInfo.textContent = `Halaman ${state.page} / ${pages}`;
  prev.disabled = state.page<=1;
  next.disabled = state.page>=pages;

  grid.innerHTML = items.map(it=>{
    const slug = slugify(`${it.category}-${it.subcategory||it.provider||''}`) || 'paket-data-telkomsel';
    return `
      <a href="/product/${slug}" class="group rounded-3xl border border-slate-800/70 bg-[#111826] p-4 hover:border-violet-700/60 transition block">
        <div class="flex items-center gap-3">
          <div class="size-10 rounded-xl bg-slate-800/60"></div>
          <div class="min-w-0">
            <h3 class="font-medium truncate">${it.name}</h3>
            <p class="text-xs text-slate-400">${it.category}${it.subcategory? ' • '+it.subcategory : ''} • Mulai Rp ${it.price.toLocaleString('id-ID')}</p>
          </div>
        </div>
        <div class="mt-4 flex items-center justify-between">
          <div class="text-sm text-slate-400">Estimasi ${it.eta}</div>
          <span class="px-3 py-1.5 rounded-xl bg-violet-600 group-hover:bg-violet-500 text-sm">Detail</span>
        </div>
      </a>
    `;
  }).join('');
}

// ====== BIND UI ======
function bind() {
  // kategori chips
  const catChips = document.querySelectorAll(".cat-chip");
  catChips.forEach(ch => {
    ch.addEventListener("click", () => {
      // highlight
      catChips.forEach(c => c.classList.remove("ring-2","ring-violet-500/30","border-violet-700/60"));
      const value = ch.dataset.value;
      if (state.category === value) {
        // toggle off
        state.category = null;
        state.subcategory = null;
      } else {
        state.category = value;
        state.subcategory = null; // reset subcat ketika ganti kategori
        ch.classList.add("ring-2","ring-violet-500/30","border-violet-700/60");
      }
      state.page = 1;
      renderSubcategories(); // refresh subcat panel
      render();
    });
  });

  const qn = document.getElementById("qName");
  const qp = document.getElementById("qProvider");
  const sb = document.getElementById("sortBy");
  const btn = document.getElementById("btnSearch");
  const prev = document.getElementById("prevPage");
  const next = document.getElementById("nextPage");
  const reset = document.getElementById("resetFilter");

  btn?.addEventListener("click", () => { state.qName = (qn.value || "").trim().toLowerCase(); state.page = 1; render(); });
  qn?.addEventListener("keydown", e => { if (e.key === "Enter") btn.click(); });
  qp?.addEventListener("input", () => { state.qProvider = (qp.value || "").trim().toLowerCase(); state.page = 1; render(); });
  sb?.addEventListener("change", () => { state.sort = sb.value; state.page = 1; render(); });
  prev?.addEventListener("click", () => { state.page = Math.max(1, state.page - 1); render(); });
  next?.addEventListener("click", () => { state.page = state.page + 1; render(); });
  reset?.addEventListener("click", () => {
    state.page = 1;
    state.qName = "";
    state.qProvider = "";
    state.category = null;
    state.subcategory = null;
    state.sort = "popular";
    if (qn) qn.value = "";
    if (qp) qp.value = "";
    if (sb) sb.value = "popular";
    document.querySelectorAll(".cat-chip").forEach(c => c.classList.remove("ring-2","ring-violet-500/30","border-violet-700/60"));
    renderSubcategories();
    render();
  });
}

(function init(){
  bind();
  renderSubcategories();
  render();
})();
