<!DOCTYPE html>
<html lang="fil">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="google" content="notranslate"> <!-- IDINAGDAG PARA HINDI MAG-TRANSLATE -->
<title>@yield('title', 'BIMS | Barangay Bactad East')</title>
<meta name="description" content="@yield('meta_description', 'Barangay Integrated Management System ng Barangay Bactad East, Urdaneta City, Pangasinan.')">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta property="og:site_name" content="BIMS | Barangay Bactad East">
<meta property="og:title" content="@yield('title', 'BIMS | Barangay Bactad East')">
<meta property="og:description" content="@yield('meta_description', 'Barangay Integrated Management System ng Barangay Bactad East.')">
<meta property="og:type" content="@yield('og_type', 'website')">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:image" content="{{ asset('images/brgy_logo.png.jpg') }}">
<script>try{var t=localStorage.getItem('bims-theme');if(t?t==='dark':matchMedia('(prefers-color-scheme: dark)').matches)document.documentElement.classList.add('dark')}catch(e){}</script>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
html { scroll-behavior: smooth; }
body { font-family: 'Inter', sans-serif; background: #fff; color: #1E293B; transition: background-color .4s, color .4s; }
:root { --primary-green: #059669; --soft-green: #F0FDF4; --border-color: rgba(5,150,105,.1); }
.bg-pattern { background-color: #fff; background-image: radial-gradient(#05966911 1px, transparent 1px); background-size: 30px 30px; }

.glass-card { position: relative; background: rgba(255,255,255,.96); border: 1px solid var(--border-color); box-shadow: 0 4px 20px -5px rgba(0,0,0,.05); transition: transform .4s cubic-bezier(.175,.885,.32,1.275), border-color .3s, box-shadow .3s; }
.glass-card:hover { transform: translateY(-6px); border-color: var(--primary-green); box-shadow: 0 25px 40px -15px rgba(5,150,105,.15); }
.section-tag { display: inline-block; padding: 4px 12px; background: var(--soft-green); color: var(--primary-green); font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; border-radius: 50px; border: 1px solid rgba(5,150,105,.2); margin-bottom: 1rem; }
.text-gradient-emerald { background: linear-gradient(135deg,#10b981 0%,#059669 50%,#047857 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }

/* Nav */
nav, nav * { backdrop-filter: none !important; -webkit-backdrop-filter: none !important; }
nav.fixed { background: rgba(255,255,255,.97); transition: box-shadow .35s, background .35s; }
nav.fixed .nav-row { transition: height .35s; }
nav.nav-scrolled { box-shadow: 0 8px 30px -12px rgba(5,150,105,.25); }
nav.nav-scrolled .nav-row { height: 3.5rem; }
.nav-link { position: relative; }
.nav-link::after { content: ''; position: absolute; left: 0; bottom: -6px; height: 2px; width: 100%; background: var(--primary-green); border-radius: 2px; transform: scaleX(0); transform-origin: left; transition: transform .35s; }
.nav-link:hover::after, .nav-link.is-active::after { transform: scaleX(1); }
.nav-link.is-active { color: var(--primary-green); }
.mobile-nav-scroll { overflow-x: auto; scrollbar-width: none; -ms-overflow-style: none; }
.mobile-nav-scroll::-webkit-scrollbar { display: none; }

/* Progress, back-to-top, theme toggle */
#scrollProgress { position: fixed; top: 0; left: 0; height: 3px; width: 100%; transform: scaleX(0); transform-origin: left; will-change: transform; z-index: 100000; background: linear-gradient(90deg,#34d399,#059669,#14b8a6); box-shadow: 0 0 10px rgba(16,185,129,.6); }
#toTop { position: fixed; left: 1rem; bottom: 1rem; z-index: 9990; width: 44px; height: 44px; border-radius: 9999px; background: #fff; color: var(--primary-green); border: 1px solid rgba(5,150,105,.25); box-shadow: 0 10px 25px -8px rgba(5,150,105,.4); opacity: 0; transform: translateY(20px); pointer-events: none; transition: opacity .35s, transform .35s, background .25s, color .25s; }
#toTop.show { opacity: 1; transform: none; pointer-events: auto; }
#toTop:hover { background: var(--primary-green); color: #fff; }
#themeToggle { position: relative; width: 40px; height: 40px; border-radius: 9999px; flex-shrink: 0; overflow: hidden; background: #f0fdf4; color: #059669; border: 1px solid rgba(5,150,105,.25); transition: transform .3s, background .3s; }
#themeToggle:hover { transform: rotate(15deg) scale(1.08); }
#themeToggle i { position: absolute; inset: 0; display: grid; place-items: center; transition: transform .5s cubic-bezier(.2,.8,.2,1), opacity .4s; }
#themeToggle .t-moon { transform: translateY(150%) rotate(-90deg); opacity: 0; }

/* Entrance */
@keyframes rise { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: none; } }
.rise { opacity: 0; animation: rise .6s cubic-bezier(.2,.8,.2,1) forwards; animation-delay: var(--d, 0s); }

/* Chat */
#chatMessages::-webkit-scrollbar { width: 5px; }
#chatMessages::-webkit-scrollbar-track { background: #f1f5f9; }
#chatMessages::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.animate-fade-in-up { animation: fadeInUp .3s ease-out forwards; }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
.quick-chip { transition: all .2s; cursor: pointer; }
.quick-chip:active { transform: scale(.96); }
#chatToggle::after { content: ''; position: absolute; inset: 0; border-radius: 9999px; border: 2px solid rgba(16,185,129,.55); animation: chatRing 2.4s ease-out infinite; pointer-events: none; }
@keyframes chatRing { 0% { transform: scale(1); opacity: .8; } 100% { transform: scale(1.5); opacity: 0; } }

a:focus-visible, button:focus-visible, input:focus-visible, select:focus-visible, textarea:focus-visible { outline: 2px solid #10b981; outline-offset: 3px; }

/* Dark mode */
html.dark { color-scheme: dark; }
html.dark body { background: #0b1210; color: #e2e8f0; }
html.dark .bg-pattern { background-color: #0b1210; background-image: radial-gradient(#10b98122 1px, transparent 1px); }
html.dark nav.fixed, html.dark nav.nav-scrolled { background: rgba(11,18,16,.94); border-color: rgba(16,185,129,.15); }
html.dark .bg-white, html.dark .bg-white\/90, html.dark .bg-white\/60 { background-color: #111b17 !important; }
html.dark .bg-slate-50, html.dark .bg-slate-100 { background-color: #0f1a16 !important; }
html.dark .bg-emerald-50 { background-color: rgba(16,185,129,.12) !important; }
html.dark .glass-card { background: rgba(17,27,23,.85); border-color: rgba(16,185,129,.18); }
html.dark .text-slate-900 { color: #f1f5f9 !important; }
html.dark .text-slate-800 { color: #e2e8f0 !important; }
html.dark .text-slate-700, html.dark .text-slate-600 { color: #cbd5e1 !important; }
html.dark .text-slate-500, html.dark .text-slate-400 { color: #94a3b8 !important; }
html.dark .border-slate-100, html.dark .border-slate-200, html.dark .border-emerald-50, html.dark .border-emerald-100, html.dark .border-emerald-200 { border-color: rgba(16,185,129,.18) !important; }
html.dark .section-tag { background: rgba(16,185,129,.12); }
html.dark #toTop { background: #111b17; }
html.dark #userInput, html.dark input, html.dark select, html.dark textarea { color: #e2e8f0; }
html.dark #userInput { background: #0f1a16; border-color: rgba(16,185,129,.2); }
html.dark .quick-chip { background: #16241f !important; color: #cbd5e1 !important; }
html.dark #themeToggle { background: #16241f; color: #fbbf24; }
html.dark #themeToggle .t-sun { transform: translateY(-150%) rotate(90deg); opacity: 0; }
html.dark #themeToggle .t-moon { transform: none; opacity: 1; }

@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after { animation-duration: .01ms !important; animation-iteration-count: 1 !important; transition-duration: .01ms !important; }
  .rise { opacity: 1; animation: none; }
}
@media print {
  nav, footer, #chatWidget, #toTop, #scrollProgress, .no-print { display: none !important; }
  main { padding-top: 0 !important; }
  body { background: #fff !important; color: #000 !important; }
}
</style>
@stack('head')
</head>
<body class="bg-pattern">
<div id="scrollProgress"></div>
<button id="toTop" aria-label="Back to top"><i class="fas fa-arrow-up"></i></button>

@include('partials.public-nav')

<main class="pt-[6.75rem] md:pt-[7.75rem] lg:pt-20">
    @yield('content')
</main>

@include('partials.public-footer')
@include('partials.public-chat')

<script>
(function () {
  const $ = (s, r = document) => r.querySelector(s), $$ = (s, r = document) => Array.from(r.querySelectorAll(s));
  const root = document.documentElement;

  /* Dark mode toggle (kapareho ng home page) */
  const loginBtn = $('nav a[href="/login"]');
  if (loginBtn && !$('#themeToggle')) {
    const wrap = document.createElement('div'); wrap.className = 'flex items-center gap-2 sm:gap-3';
    loginBtn.parentNode.insertBefore(wrap, loginBtn);
    const tb = document.createElement('button'); tb.id = 'themeToggle'; tb.setAttribute('aria-label', 'Toggle dark mode');
    tb.innerHTML = '<i class="fas fa-sun t-sun"></i><i class="fas fa-moon t-moon"></i>';
    wrap.append(tb, loginBtn);
    tb.addEventListener('click', () => { const d = root.classList.toggle('dark'); try { localStorage.setItem('bims-theme', d ? 'dark' : 'light'); } catch (e) {} });
  }

  /* Entrance: tanggalin ang class pagkatapos para gumana ang hover */
  $$('.rise').forEach(el => el.addEventListener('animationend', () => { el.classList.remove('rise'); el.style.removeProperty('--d'); }, { once: true }));

  /* Scroll progress, navbar, back-to-top */
  const bar = $('#scrollProgress'), nav = $('nav.fixed'), toTop = $('#toTop'); let tk = false, maxScroll = 0;
  const calc = () => { maxScroll = document.documentElement.scrollHeight - innerHeight; };
  calc(); addEventListener('resize', calc); addEventListener('load', calc); setTimeout(calc, 1500);
  function onScroll() {
    const y = scrollY;
    bar.style.transform = `scaleX(${maxScroll > 0 ? Math.min(y / maxScroll, 1) : 0})`;
    nav.classList.toggle('nav-scrolled', y > 20);
    toTop.classList.toggle('show', y > 600);
    tk = false;
  }
  addEventListener('scroll', () => { if (!tk) { requestAnimationFrame(onScroll); tk = true; } }, { passive: true });
  onScroll();
  toTop.addEventListener('click', () => scrollTo({ top: 0, behavior: 'smooth' }));
  root.style.scrollPaddingTop = (innerWidth < 1024 ? 110 : 90) + 'px';
})();
</script>
@stack('scripts')
</body>
</html>