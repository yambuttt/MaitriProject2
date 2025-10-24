const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// Mobile drawer
(function(){
  const btn=document.getElementById('mobileBtn');
  const menu=document.getElementById('mobileMenu');
  if(btn&&menu){ btn.addEventListener('click',()=>menu.classList.toggle('hidden')); }
})();

// Scroll reveal
(function(){
  if(reduce) { document.querySelectorAll('.reveal').forEach(el=>el.classList.add('reveal-in')); return; }
  const io = new IntersectionObserver((entries)=>{
    for(const e of entries){ if(e.isIntersecting){ e.target.classList.add('reveal-in'); io.unobserve(e.target); } }
  }, {threshold:.12, rootMargin:'0px 0px -10% 0px'});
  document.querySelectorAll('.reveal').forEach(el=>io.observe(el));
})();

// Parallax hero
(function(){
  if(reduce) return;
  const el = document.querySelector('[data-parallax]');
  if(!el) return;
  let last=0;
  const onScroll=()=>{
    const r = el.getBoundingClientRect(), vh = innerHeight||0;
    const prog = 1 - Math.min(Math.max((r.top + r.height*0.3)/(vh + r.height),0),1);
    const y = Math.round(prog*12);
    if(y!==last){ el.style.transform = `translateY(${y}px)`; last=y; }
  };
  onScroll(); addEventListener('scroll', onScroll, {passive:true}); addEventListener('resize', onScroll);
})();
