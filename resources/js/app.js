function boot() {
  // Toggle menu mobile
  const btn = document.getElementById("mobileBtn");
  const menu = document.getElementById("mobileMenu");
  if (btn && menu) {
    btn.addEventListener("click", () => menu.classList.toggle("hidden"));
  }

  // Muat script halaman dari data-page
  const page = (document.body && document.body.getAttribute("data-page")) || "default";
  if (page === "landing") {
    import("./pages/landing.js");
  } else if (page === "catalog") {
    import("./pages/catalog.js");
  }
}

// Jalankan segera jika DOM sudah siap, kalau belum tunggu
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", boot, { once: true });
} else {
  boot();
}
