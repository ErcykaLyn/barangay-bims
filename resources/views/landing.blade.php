<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
<title>BIMS | Barangay Bactad East — Integrated Management System</title>
<meta name="description" content="Barangay Integrated Management System ng Barangay Bactad East, Urdaneta City, Pangasinan. Mag-request ng dokumento online at basahin ang pinakabagong anunsyo.">
<meta name="theme-color" content="#059669">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta property="og:site_name" content="BIMS | Barangay Bactad East">
<meta property="og:title" content="BIMS | Barangay Bactad East">
<meta property="og:description" content="Smart Data. Better Service. Ang digital portal ng Barangay Bactad East.">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url('/') }}">
<meta property="og:image" content="{{ asset('images/brgy_logo.png.jpg') }}">
<script>try{var t=localStorage.getItem('bims-theme');if(t?t==='dark':matchMedia('(prefers-color-scheme: dark)').matches)document.documentElement.classList.add('dark')}catch(e){}</script>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700;12..96,800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<noscript><style>.reveal,.hero-in{opacity:1!important;transform:none!important}</style></noscript>

<style>
/* =====================================================================
   BASE & TOKENS
   ===================================================================== */
* { margin: 0; padding: 0; box-sizing: border-box; }
html { scroll-behavior: smooth; }
body { font-family: 'Inter', sans-serif; background: #fff; color: #1E293B; overflow-x: hidden; }
:root {
  --primary-green: #059669;
  --soft-green: #F0FDF4;
  --border-color: rgba(5,150,105,.12);
}
.font-display { font-family: 'Bricolage Grotesque', 'Inter', sans-serif; letter-spacing: -.025em; }
.bg-pattern { background-color: #fff; background-image: radial-gradient(#05966911 1px, transparent 1px); background-size: 30px 30px; }

/* =====================================================================
   CARDS
   ===================================================================== */
.glass-card {
  position: relative;
  background: rgba(255,255,255,.96);
  border: 1px solid var(--border-color);
  box-shadow: 0 4px 20px -5px rgba(0,0,0,.05);
  transition: transform .4s cubic-bezier(.175,.885,.32,1.275), border-color .3s, box-shadow .3s;
}
.glass-card:hover { transform: translateY(-8px); border-color: var(--primary-green); box-shadow: 0 25px 40px -15px rgba(5,150,105,.18); }
.glass-card::before {
  content: ''; position: absolute; inset: 0; border-radius: inherit; pointer-events: none; opacity: 0;
  background: radial-gradient(280px circle at var(--mx,50%) var(--my,50%), rgba(16,185,129,.14), transparent 70%);
  transition: opacity .3s;
}
.glass-card:hover::before { opacity: 1; }
.svc-icon { transition: transform .4s cubic-bezier(.175,.885,.32,1.4), box-shadow .3s; }
.glass-card:hover .svc-icon { transform: scale(1.1) rotate(-6deg); }

/* =====================================================================
   SECTION HEADINGS
   ===================================================================== */
.section-tag {
  display: inline-flex; align-items: center; gap: 6px; padding: 5px 14px;
  background: var(--soft-green); color: var(--primary-green);
  font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;
  border-radius: 50px; border: 1px solid rgba(5,150,105,.2); margin-bottom: 1rem;
  transition: transform .3s, background .3s;
}
.section-tag::before { content: ''; width: 6px; height: 6px; border-radius: 9999px; background: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,.2); }
.section-tag:hover { transform: scale(1.06); background: #dcfce7; }
.text-gradient-emerald {
  background: linear-gradient(135deg,#10b981 0%,#059669 50%,#0d9488 100%);
  -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}

/* =====================================================================
   NAV (shared partial hooks)
   ===================================================================== */
nav.fixed { transition: box-shadow .35s, background .35s; background: rgba(255,255,255,.97); }
nav.fixed .nav-row { transition: height .35s; }
nav.nav-scrolled { background: rgba(255,255,255,.97); box-shadow: 0 8px 30px -12px rgba(5,150,105,.25); }
nav.nav-scrolled .nav-row { height: 3.5rem; }
nav, nav * { backdrop-filter: none !important; -webkit-backdrop-filter: none !important; }
.nav-link { position: relative; }
.nav-link::after { content: ''; position: absolute; left: 0; bottom: -6px; height: 2px; width: 100%; background: var(--primary-green); border-radius: 2px; transform: scaleX(0); transform-origin: left; transition: transform .35s; }
.nav-link:hover::after, .nav-link.is-active::after { transform: scaleX(1); }
.nav-link.is-active { color: var(--primary-green); }

/* =====================================================================
   SCROLL UI
   ===================================================================== */
#scrollProgress { position: fixed; top: 0; left: 0; height: 3px; width: 100%; transform: scaleX(0); transform-origin: left; will-change: transform; z-index: 100000; background: linear-gradient(90deg,#34d399,#059669,#14b8a6); box-shadow: 0 0 10px rgba(16,185,129,.6); }
#toTop { position: fixed; left: 1rem; bottom: 1rem; z-index: 9990; width: 44px; height: 44px; border-radius: 9999px; background: #fff; color: var(--primary-green); border: 1px solid rgba(5,150,105,.25); box-shadow: 0 10px 25px -8px rgba(5,150,105,.4); opacity: 0; transform: translateY(20px); pointer-events: none; transition: opacity .35s, transform .35s, background .25s, color .25s; }
#toTop.show { opacity: 1; transform: none; pointer-events: auto; }
#toTop:hover { background: var(--primary-green); color: #fff; }

/* =====================================================================
   ANIMATION HELPERS
   ===================================================================== */
@keyframes heroIn { from { opacity: 0; transform: translateY(26px); } to { opacity: 1; transform: none; } }
.hero-in { opacity: 0; animation: heroIn .9s cubic-bezier(.2,.8,.2,1) forwards; animation-delay: var(--d,0s); }
.reveal { opacity: 0; transform: translateY(28px); will-change: opacity, transform; transition: opacity .7s cubic-bezier(.2,.8,.2,1), transform .7s cubic-bezier(.2,.8,.2,1); transition-delay: var(--d,0s); }
.reveal.reveal-left { transform: translateX(-40px); }
.reveal.reveal-right { transform: translateX(40px); }
.reveal.in { opacity: 1; transform: none; }
@keyframes floaty { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
.floaty { animation: floaty 5s ease-in-out infinite; }
.floaty-slow { animation: floaty 7s ease-in-out infinite reverse; }
@keyframes caret { 50% { opacity: 0; } }
.type-wrap { display: inline-block; min-height: 1em; }
.type-caret { display: inline-block; width: 3px; height: .85em; margin-left: 4px; background: #059669; vertical-align: -.05em; border-radius: 2px; animation: caret 1s steps(1) infinite; }
.animate-fade-in-up { animation: fadeInUp .3s ease-out forwards; }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
.btn-shine { position: relative; overflow: hidden; }
.btn-shine::after { content: ''; position: absolute; top: 0; left: -120%; width: 60%; height: 100%; background: linear-gradient(120deg,transparent,rgba(255,255,255,.35),transparent); transform: skewX(-20deg); transition: left .7s; }
.btn-shine:hover::after { left: 130%; }

/* =====================================================================
   HERO
   ===================================================================== */
.hero-mesh {
  position: absolute; inset: 0; pointer-events: none;
  background:
    radial-gradient(60% 50% at 8% 8%,  rgba(110,231,183,.35), transparent 70%),
    radial-gradient(50% 45% at 95% 20%, rgba(94,234,212,.30), transparent 70%),
    radial-gradient(45% 40% at 60% 100%, rgba(253,230,138,.22), transparent 70%);
}
.hero-grid {
  position: absolute; inset: 0; pointer-events: none; opacity: .5;
  background-image: linear-gradient(rgba(5,150,105,.07) 1px, transparent 1px), linear-gradient(90deg, rgba(5,150,105,.07) 1px, transparent 1px);
  background-size: 56px 56px;
  -webkit-mask-image: radial-gradient(ellipse 70% 60% at 50% 30%, #000 30%, transparent 75%);
          mask-image: radial-gradient(ellipse 70% 60% at 50% 30%, #000 30%, transparent 75%);
}
.mock { background: #fff; border: 1px solid rgba(5,150,105,.15); box-shadow: 0 40px 80px -30px rgba(5,150,105,.35), 0 10px 30px -15px rgba(15,23,42,.15); }
.mock-bar { height: 8px; border-radius: 6px; background: linear-gradient(180deg,#34d399,#059669); transform-origin: bottom; animation: barUp 1.2s cubic-bezier(.2,.8,.2,1) both; }
@keyframes barUp { from { transform: scaleY(0); } to { transform: scaleY(1); } }
.chip-float { box-shadow: 0 20px 40px -15px rgba(15,23,42,.25); }

/* =====================================================================
   MARQUEE
   ===================================================================== */
.marquee { overflow: hidden; -webkit-mask-image: linear-gradient(90deg, transparent, #000 10%, #000 90%, transparent); mask-image: linear-gradient(90deg, transparent, #000 10%, #000 90%, transparent); }
.marquee-track { display: flex; gap: 14px; width: max-content; animation: marquee 38s linear infinite; }
.marquee:hover .marquee-track { animation-play-state: paused; }
@keyframes marquee { to { transform: translateX(-50%); } }

/* =====================================================================
   STEPS
   ===================================================================== */
.step-line { position: absolute; top: 28px; left: 12%; right: 12%; height: 2px; background: repeating-linear-gradient(90deg, rgba(5,150,105,.35) 0 8px, transparent 8px 16px); }

/* =====================================================================
   OFFICIALS
   ===================================================================== */
.official-card { transition: transform .3s ease, box-shadow .3s ease; }
.official-card:hover { transform: translateY(-6px); box-shadow: 0 20px 35px -10px rgba(5,150,105,.3); }
.official-card img { transition: transform .8s cubic-bezier(.2,.8,.2,1); }
.official-card:hover img { transform: scale(1.08); }

/* =====================================================================
   FAQ
   ===================================================================== */
.faq-item { transition: box-shadow .4s, border-color .4s; }
.faq-item.active { border-color: rgba(5,150,105,.35); box-shadow: 0 12px 30px -12px rgba(5,150,105,.25); }
.faq-toggle { transition: color .3s, background .3s; }
.faq-toggle:hover { background: #f0fdf4; }
.faq-toggle > i { transition: transform .45s cubic-bezier(.4,0,.2,1); }
.faq-item.active .faq-toggle { color: var(--primary-green); }
.faq-item.active .faq-toggle > i { transform: rotate(180deg); }
.faq-content { max-height: 0; overflow: hidden; opacity: 0; padding-top: 0; transform: translateY(-6px); transition: max-height .5s cubic-bezier(.4,0,.2,1), opacity .4s, transform .4s, padding-top .4s; }
.faq-item.active .faq-content { max-height: 500px; opacity: 1; padding-top: .5rem; transform: none; }

/* =====================================================================
   TIMELINE
   ===================================================================== */
.tl { position: relative; padding-left: 2.25rem; }
.tl::before { content: ''; position: absolute; left: 11px; top: 4px; bottom: 4px; width: 2px; background: rgba(5,150,105,.15); border-radius: 2px; }
.tl-fill { position: absolute; left: 11px; top: 4px; width: 2px; height: 0; background: linear-gradient(#34d399,#059669); border-radius: 2px; box-shadow: 0 0 10px rgba(16,185,129,.5); }
.tl-item { position: relative; padding-bottom: 2rem; }
.tl-item:last-child { padding-bottom: 0; }
.tl-dot { position: absolute; left: -2.25rem; top: 2px; width: 24px; height: 24px; border-radius: 9999px; background: #fff; border: 2px solid #a7f3d0; display: grid; place-items: center; color: #fff; font-size: 9px; transition: background .4s, border-color .4s, transform .5s cubic-bezier(.175,.885,.32,1.6), box-shadow .4s; }
.tl-item.on .tl-dot { background: #059669; border-color: #059669; transform: scale(1.15); box-shadow: 0 0 0 6px rgba(16,185,129,.15); }
.tl-card { opacity: .35; transform: translateX(16px); transition: opacity .6s, transform .6s cubic-bezier(.2,.8,.2,1); }
.tl-item.on .tl-card { opacity: 1; transform: none; }
.tl-year { font-weight: 800; font-size: 1.25rem; color: #059669; line-height: 1.1; }
.tl-title { font-weight: 700; color: #0f172a; margin-top: 2px; }
.tl-text { color: #64748b; font-size: .875rem; margin-top: 2px; line-height: 1.55; }

/* =====================================================================
   CTA
   ===================================================================== */
.cta-bg {
  background:
    radial-gradient(60% 80% at 100% 0%, rgba(20,184,166,.5), transparent 60%),
    radial-gradient(50% 70% at 0% 100%, rgba(16,185,129,.45), transparent 60%),
    linear-gradient(135deg, #065f46, #047857 55%, #0f766e);
}
.cta-dots { background-image: radial-gradient(rgba(255,255,255,.35) 1px, transparent 1px); background-size: 22px 22px; opacity: .25; }

/* =====================================================================
   TOUR / CHAT (shared hooks)
   ===================================================================== */
.mobile-nav-scroll { overflow-x: auto; scrollbar-width: none; -ms-overflow-style: none; }
.mobile-nav-scroll::-webkit-scrollbar { display: none; }
#chatMessages::-webkit-scrollbar { width: 5px; }
#chatMessages::-webkit-scrollbar-track { background: #f1f5f9; }
#chatMessages::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
#tourModal::-webkit-scrollbar { width: 4px; }
#tourModal::-webkit-scrollbar-thumb { background: rgba(255,255,255,.3); border-radius: 10px; }
.quick-chip { transition: all .2s; cursor: pointer; }
.quick-chip:active { transform: scale(.96); }
#chatToggle::after { content: ''; position: absolute; inset: 0; border-radius: 9999px; border: 2px solid rgba(16,185,129,.55); animation: chatRing 2.4s ease-out infinite; pointer-events: none; }
@keyframes chatRing { 0% { transform: scale(1); opacity: .8; } 100% { transform: scale(1.5); opacity: 0; } }
.tour-slide.tour-active { animation: tourIn .5s cubic-bezier(.2,.8,.2,1); }
@keyframes tourIn { from { opacity: 0; transform: translateX(24px); } to { opacity: 1; transform: none; } }

/* =====================================================================
   THEME TOGGLE
   ===================================================================== */
#themeToggle { position: relative; width: 40px; height: 40px; border-radius: 9999px; flex-shrink: 0; overflow: hidden; background: #f0fdf4; color: #059669; border: 1px solid rgba(5,150,105,.25); transition: transform .3s, background .3s; }
#themeToggle:hover { transform: rotate(15deg) scale(1.08); }
#themeToggle i { position: absolute; inset: 0; display: grid; place-items: center; transition: transform .5s cubic-bezier(.2,.8,.2,1), opacity .4s; }
#themeToggle .t-moon { transform: translateY(150%) rotate(-90deg); opacity: 0; }

a:focus-visible, button:focus-visible, textarea:focus-visible { outline: 2px solid #10b981; outline-offset: 3px; }
body { transition: background-color .4s, color .4s; }

/* =====================================================================
   DARK MODE
   ===================================================================== */
html.dark { color-scheme: dark; }
html.dark body { background: #0b1210; color: #e2e8f0; }
html.dark .bg-pattern { background-color: #0b1210; background-image: radial-gradient(#10b98122 1px, transparent 1px); }
html.dark nav.fixed, html.dark nav.nav-scrolled { background: rgba(11,18,16,.88); border-color: rgba(16,185,129,.15); }
html.dark .bg-white, html.dark .bg-white\/90, html.dark .bg-white\/60 { background-color: #111b17 !important; }
html.dark .bg-slate-50, html.dark .bg-slate-50\/50, html.dark .bg-slate-100 { background-color: #0f1a16 !important; }
html.dark .bg-emerald-50\/30 { background-color: #0d1713 !important; }
html.dark .bg-emerald-50 { background-color: rgba(16,185,129,.12) !important; }
html.dark .bg-emerald-100 { background-color: rgba(16,185,129,.18) !important; }
html.dark .bg-blue-100 { background-color: rgba(59,130,246,.18) !important; }
html.dark .bg-amber-50 { background-color: rgba(245,158,11,.12) !important; }
html.dark .glass-card { background: rgba(17,27,23,.85); border-color: rgba(16,185,129,.18); }
html.dark .mock { background: #111b17; border-color: rgba(16,185,129,.2); }
html.dark .text-slate-900 { color: #f1f5f9 !important; }
html.dark .text-slate-800 { color: #e2e8f0 !important; }
html.dark .text-slate-700, html.dark .text-slate-600 { color: #cbd5e1 !important; }
html.dark .text-slate-500, html.dark .text-slate-400 { color: #94a3b8 !important; }
html.dark .border-slate-100, html.dark .border-slate-200, html.dark .border-emerald-50, html.dark .border-emerald-100 { border-color: rgba(16,185,129,.18) !important; }
html.dark .section-tag { background: rgba(16,185,129,.12); }
html.dark .faq-toggle:hover { background: rgba(16,185,129,.08); }
html.dark #toTop { background: #111b17; }
html.dark #userInput { background: #0f1a16; color: #e2e8f0; border-color: rgba(16,185,129,.2); }
html.dark .quick-chip { background: #16241f !important; color: #cbd5e1 !important; }
html.dark .tl-dot { background: #111b17; }
html.dark .tl-title { color: #f1f5f9; }
html.dark .tl-text { color: #94a3b8; }
html.dark .hero-mesh { opacity: .55; }
html.dark #themeToggle { background: #16241f; color: #fbbf24; }
html.dark #themeToggle .t-sun { transform: translateY(-150%) rotate(90deg); opacity: 0; }
html.dark #themeToggle .t-moon { transform: none; opacity: 1; }

@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after { animation-duration: .01ms !important; animation-iteration-count: 1 !important; transition-duration: .01ms !important; }
  .reveal, .hero-in { opacity: 1 !important; transform: none !important; filter: none !important; }
  .marquee-track { animation: none; }
}
</style>
</head>
<body class="bg-pattern">
<div id="scrollProgress"></div>
<button id="toTop" aria-label="Back to top"><i class="fas fa-arrow-up"></i></button>

@php
/* =====================================================================
   PAGE DATA
   ===================================================================== */
$totalResidents = $totalResidents ?? 0;

// [icon, title, description, bullets, gradient, grid-span]
$features = [
  ['fa-id-card',     'Resident Profiling', 'Kumpletong profile ng bawat residente — mula masterlist hanggang demographics ng buong barangay.', ['Masterlist', 'Demographics', 'PWD & Senior'], 'from-emerald-400 to-emerald-600', 'lg:col-span-2'],
  ['fa-file-export', 'Document Issuance',  'Mag-request ng dokumento online, kahit saan.',                                                       ['Clearance', 'Residency', 'Indigency'],      'from-sky-400 to-blue-600',       ''],
  ['fa-chart-line',  'Smart Analytics',    'Awtomatikong ulat at mapa para sa mas matalinong desisyon.',                                         ['Heatmaps', 'Reports', 'History'],           'from-violet-400 to-purple-600',  ''],
  ['fa-user-lock',   'Role-Based Access',  'Tamang permiso ayon sa tungkulin.',                                                                  [],                                          'from-amber-400 to-orange-600',   ''],
  ['fa-search',      'Quick Search',       'Mabilis na paghahanap ng record.',                                                                   [],                                          'from-teal-400 to-cyan-600',      ''],
  ['fa-shield-alt',  'Data Privacy',       'Ang BIMS ay sumusunod sa Data Privacy Act. Ligtas ang personal na impormasyon ng bawat residente.', [],                                          'from-rose-400 to-pink-600',      'lg:col-span-3'],
];

$marquee = ['Barangay Clearance', 'Certificate of Residency', 'Certificate of Indigency', 'Resident Profiling', 'Heatmaps', 'Smart Reports', 'Role-Based Access', 'Quick Search', 'Data Privacy', 'Online Requests'];

$steps = [
  ['fa-sign-in-alt',   'Mag-login',           'Gamitin ang iyong registered account sa BIMS.'],
  ['fa-file-alt',      'Piliin ang dokumento','Buksan ang Document Issuance at pumili ng kailangan mo.'],
  ['fa-pen-to-square', 'Punan ang form',      'Ilagay ang detalye sa simpleng online form.'],
  ['fa-circle-check',  'Hintayin ang proseso','Aasikasuhin ng barangay ang iyong request.'],
];

$officials = [
  ['erwin-aduca',      'Hon. Erwin E. Aduca',       'Punong Barangay',   'Punong B.',  'fa-user-tie',          true],
  ['john-desamito',    'Hon. John R. Desamito',     'Barangay Kagawad',  'Kagawad',    'fa-users',             false],
  ['herbert-fang',     'Hon. Herbert F. Fang',      'Barangay Kagawad',  'Kagawad',    'fa-hand-holding-heart',false],
  ['reydendo-latonio', 'Hon. Reydendo B. Latonio',  'Barangay Kagawad',  'Kagawad',    'fa-book-open',         false],
  ['orly-baybayan',    'Hon. Kgd Orly A. Baybayan', 'Barangay Kagawad',  'Kagawad',    'fa-tractor',           false],
  ['alvin-cuchapin',   'Hon. Alvin A. Cuchapin',    'Barangay Kagawad',  'Kagawad',    'fa-children',          false],
  ['henry-latonio',    'Hon. Henry L. Latonio',     'Barangay Kagawad',  'Kagawad',    'fa-shield-alt',        false],
  ['francis-fernandez','Hon. Francis M. Fernandez', 'Barangay Kagawad',  'Kagawad',    'fa-user-shield',       false],
  ['thenmarck-dulos',  'Hon. Thenmarck V. Dulos',   'SK Chairperson',    'SK Chair',   'fa-user-graduate',     false],
  ['angelito-navarro', 'Hon. Angelito Navarro',     'Barangay Secretary','Secretary',  'fa-file-signature',    false],
];
$captain = $officials[0];
$council = array_slice($officials, 1);

$faqs = [
  ['Paano ako makakakuha ng Barangay Clearance gamit ang BIMS?', 'Kailangan mo munang mag-login sa system gamit ang iyong registered account. Pagkatapos, pumunta sa "Document Issuance" section at piliin ang "Barangay Clearance."'],
  ['Kailan itinatag ang Barangay Bactad East?', 'Ang Barangay Bactad East ay itinatag noong <strong>1964</strong>. Si <strong>Protacio Suyat</strong> ang naging unang Barangay Captain.'],
  ['Gaano kalayo ang Bactad East mula sa City Proper?', 'Ang Barangay Bactad East ay matatagpuan mga <strong>pitong (7) kilometro</strong> mula sa City Proper ng Urdaneta.'],
  ['Paano ako makakapag-request ng Certificate of Residency?', 'Sa pamamagitan ng BIMS, pumunta sa "Document Issuance" section pagkatapos mag-login. Piliin ang "Certificate of Residency" at punan ang online form.'],
  ['Ligtas ba ang aking personal na impormasyon sa BIMS?', 'Oo, ang BIMS ay sumusunod sa <strong>Data Privacy Act of 2012</strong>. Ang lahat ng personal na impormasyon ay naka-encrypt.'],
];

$timeline = [
  ['Bago 1964', 'Bahagi ng Bactad Proper', 'Dating bahagi ng Bactad Proper, ngayon ay Barangay P.T. Orata, at kilala sa malalawak na agricultural farmlands.'],
  ['1964',      'Naaprubahan ang Ordinance', 'Naaprubahan ang Municipal o City Ordinance na nagtatag sa Bactad East.'],
  ['1964',      'Unang Barangay Captain',    'Si Protacio Suyat ang naging unang Barangay Captain.'],
  ['2026',      'Live Census',               'Kasalukuyang naitala ang ' . number_format($totalResidents) . ' na residente ng barangay.'],
  ['2026',      'Inilunsad ang BIMS',        'Ang digital initiative para sa mas mabilis at maayos na serbisyo ng barangay.'],
];

$facts = [['Founded', '1964', 'text-xl sm:text-2xl'], ['1st Captain', 'Protacio S.', 'text-sm sm:text-lg leading-tight'], ['Distance', '7 km', 'text-xl sm:text-2xl'], ['Region', 'I', 'text-xl sm:text-2xl']];

// Tour slides
$tour = [
  ['key' => 'welcome',  'tint' => 'from-emerald-500/20 to-teal-500/20', 'chip' => 'Welcome',            'chipCls' => 'bg-emerald-400/20 border-emerald-400/30 text-emerald-300', 'icon' => 'fa-hand-sparkles', 'l1' => 'Welcome to',   'l2' => 'BIMS Portal',        'grad' => 'from-emerald-400 to-teal-400',  'text' => 'Ang <strong class="text-white">Barangay Integrated Management System</strong> ng Barangay Bactad East.'],
  ['key' => 'docs',     'tint' => 'from-blue-500/20 to-cyan-500/20',    'chip' => 'Document Services',  'chipCls' => 'bg-blue-400/20 border-blue-400/30 text-blue-300',          'icon' => 'fa-file-alt',       'l1' => 'Request',       'l2' => 'Documents Online',   'grad' => 'from-blue-400 to-cyan-400',     'text' => 'I-request ang iyong mga dokumento nang hindi na kailangang pumunta pa sa Barangay Hall.'],
  ['key' => 'equip',    'tint' => 'from-violet-500/20 to-purple-500/20','chip' => 'Equipment',          'chipCls' => 'bg-violet-400/20 border-violet-400/30 text-violet-300',    'icon' => 'fa-boxes',          'l1' => 'Borrow',        'l2' => 'Barangay Equipment', 'grad' => 'from-violet-400 to-purple-400', 'text' => 'Kailangan ng upuan, tent, o sound system para sa iyong event?'],
  ['key' => 'about',    'tint' => 'from-amber-500/20 to-orange-500/20', 'chip' => 'About Barangay',     'chipCls' => 'bg-amber-400/20 border-amber-400/30 text-amber-300',       'icon' => 'fa-landmark',       'l1' => 'Barangay',      'l2' => 'Bactad East',        'grad' => 'from-amber-400 to-orange-400',  'text' => 'Itinatag noong <strong class="text-white">1964</strong>, isa sa mga progresibong barangay ng Urdaneta City.'],
  ['key' => 'start',    'tint' => 'from-emerald-500/20 to-teal-500/20', 'chip' => 'Ready to Start',     'chipCls' => 'bg-emerald-400/20 border-emerald-400/30 text-emerald-300','icon' => 'fa-rocket',         'l1' => "Let's Get",     'l2' => 'Started!',           'grad' => 'from-emerald-400 to-teal-400',  'text' => 'Handa ka na bang gamitin ang BIMS?'],
];
@endphp

<!-- =====================================================================
     TOUR MODAL
     ===================================================================== -->
<div id="tourModal" class="fixed inset-0 z-[99999] hidden bg-slate-900/95 backdrop-blur-md overflow-y-auto">
  <div class="min-h-full flex items-start sm:items-center justify-center p-3 sm:p-4 md:p-6">
    <div class="absolute -top-40 -right-40 w-96 h-96 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-teal-500/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative w-full max-w-5xl z-10 my-4">
      <div class="flex items-center justify-between mb-3 sm:mb-4 px-1">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-lg bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center"><i class="fas fa-compass text-emerald-300 text-xs"></i></div>
          <div>
            <p class="text-[10px] sm:text-xs font-black text-white uppercase tracking-widest">BIMS Tour</p>
            <p class="text-[9px] sm:text-[10px] text-white/50 hidden sm:block">Quick guided tour</p>
          </div>
        </div>
        <button onclick="closeTour()" class="flex items-center gap-2 px-3 sm:px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl text-white text-[10px] sm:text-xs font-black uppercase tracking-widest transition-all hover:scale-[1.03] active:scale-[0.98]">Skip <i class="fas fa-times text-[10px]"></i></button>
      </div>

      <div class="relative bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl sm:rounded-3xl overflow-hidden shadow-2xl">
        <div class="absolute top-0 left-0 h-1 bg-white/10 w-full z-10"><div id="tourProgressBar" class="h-full bg-gradient-to-r from-emerald-400 to-teal-400 transition-all duration-500" style="width:20%"></div></div>
        <div class="absolute top-4 right-4 z-10"><span class="px-2.5 py-1 rounded-full bg-black/40 text-white text-[10px] font-black"><span id="tourCurrentSlide">1</span> / <span id="tourTotalSlides">5</span></span></div>

        @foreach($tour as $i => $t)
        <div class="tour-slide {{ $i === 0 ? 'tour-active' : 'hidden' }}">
          <div class="grid grid-cols-1 lg:grid-cols-2 lg:min-h-[550px]">
            {{-- visual --}}
            <div class="order-1 lg:order-2 bg-gradient-to-br {{ $t['tint'] }} flex items-center justify-center p-8 lg:p-12">
              @switch($t['key'])
                @case('welcome')
                  <div class="w-40 h-40 sm:w-56 sm:h-56 md:w-64 md:h-64 rounded-full border-4 border-white/20 p-2 bg-white/5 floaty">
                    <img src="{{ asset('images/brgy_logo.png.jpg') }}" alt="Barangay Logo" class="w-full h-full object-contain rounded-full">
                  </div>
                  @break
                @case('docs')
                  <div class="relative w-full max-w-[280px] h-48 sm:h-64 flex items-center justify-center">
                    <div class="absolute top-0 left-0 w-24 h-32 sm:w-32 sm:h-40 bg-white rounded-xl shadow-2xl -rotate-12 flex items-center justify-center"><i class="fas fa-file-alt text-blue-500 text-2xl sm:text-4xl"></i></div>
                    <div class="absolute top-8 left-12 w-24 h-32 sm:w-32 sm:h-40 bg-white rounded-xl shadow-2xl rotate-6 flex items-center justify-center z-10"><i class="fas fa-file-shield text-emerald-500 text-2xl sm:text-4xl"></i></div>
                    <div class="absolute top-16 left-24 w-24 h-32 sm:w-32 sm:h-40 bg-white rounded-xl shadow-2xl rotate-12 flex items-center justify-center z-20"><i class="fas fa-file-signature text-teal-500 text-2xl sm:text-4xl"></i></div>
                  </div>
                  @break
                @case('equip')
                  <div class="grid grid-cols-2 gap-3 sm:gap-4">
                    @foreach ([['fa-chair','text-amber-400'],['fa-umbrella-beach','text-sky-400'],['fa-microphone-alt','text-indigo-400'],['fa-futbol','text-orange-400']] as $eq)
                      <div class="w-16 h-16 sm:w-24 sm:h-24 bg-white/10 rounded-2xl border border-white/20 flex items-center justify-center"><i class="fas {{ $eq[0] }} {{ $eq[1] }} text-xl sm:text-3xl"></i></div>
                    @endforeach
                  </div>
                  @break
                @case('about')
                  <div class="relative w-40 h-40 sm:w-56 sm:h-56 flex items-center justify-center">
                    <div class="absolute inset-0 border-4 border-white/10 rounded-full"></div>
                    <div class="absolute inset-3 sm:inset-4 border-2 border-dashed border-amber-400/30 rounded-full"></div>
                    <div class="relative text-center"><i class="fas fa-landmark text-amber-400 text-4xl sm:text-6xl mb-2"></i><p class="text-white font-black text-base sm:text-xl">Est. 1964</p></div>
                  </div>
                  @break
                @default
                  <div class="w-40 h-40 sm:w-56 sm:h-56 md:w-64 md:h-64 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center shadow-2xl shadow-emerald-500/50 floaty"><i class="fas fa-rocket text-white text-5xl sm:text-7xl"></i></div>
              @endswitch
            </div>

            {{-- copy --}}
            <div class="p-6 sm:p-8 md:p-12 flex flex-col justify-center order-2 lg:order-1">
              <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full border mb-4 w-fit {{ $t['chipCls'] }}">
                @if($i === 0)<span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>@else<i class="fas {{ $t['icon'] }} text-[10px]"></i>@endif
                <span class="text-[10px] font-black uppercase tracking-widest">{{ $t['chip'] }}</span>
              </div>
              <h2 class="font-display text-3xl sm:text-4xl md:text-5xl font-extrabold text-white leading-[1.05] mb-3">{{ $t['l1'] }} <br><span class="text-transparent bg-clip-text bg-gradient-to-r {{ $t['grad'] }}">{{ $t['l2'] }}</span></h2>
              <p class="text-slate-300 text-xs sm:text-sm md:text-base leading-relaxed {{ in_array($t['key'], ['welcome','start']) ? 'mb-5' : '' }}">{!! $t['text'] !!}</p>

              @if($t['key'] === 'welcome')
                <div class="flex items-center gap-3 flex-wrap">
                  <div class="flex -space-x-2">
                    <div class="w-8 h-8 rounded-full bg-emerald-500 border-2 border-slate-900 flex items-center justify-center text-white text-[10px] font-black">J</div>
                    <div class="w-8 h-8 rounded-full bg-teal-500 border-2 border-slate-900 flex items-center justify-center text-white text-[10px] font-black">R</div>
                    <div class="w-8 h-8 rounded-full bg-cyan-500 border-2 border-slate-900 flex items-center justify-center text-white text-[10px] font-black">K</div>
                  </div>
                  <p class="text-[10px] sm:text-xs text-slate-400 font-medium">Made by PSU-Asingan BSIT Students</p>
                </div>
              @endif

              @if($t['key'] === 'start')
                <div class="flex flex-col sm:flex-row gap-2.5">
                  <a href="/login" class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white rounded-xl font-bold text-xs sm:text-sm shadow-xl shadow-emerald-500/30 transition-all hover:scale-[1.02]"><i class="fas fa-sign-in-alt"></i> Login Now</a>
                  <button onclick="closeTour()" class="px-5 py-3 bg-white/10 hover:bg-white/20 border border-white/20 text-white rounded-xl font-bold text-xs sm:text-sm transition-all">Explore First</button>
                </div>
              @endif
            </div>
          </div>
        </div>
        @endforeach

        <div class="border-t border-white/10 p-3 sm:p-4 md:p-6 flex items-center justify-between bg-black/20 gap-2">
          <div id="tourDots" class="flex items-center gap-1.5 sm:gap-2"></div>
          <div class="flex items-center gap-2">
            <button id="tourPrevBtn" onclick="tourPrev()" class="hidden px-3 sm:px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white rounded-xl font-bold text-[10px] sm:text-xs transition-all items-center gap-2"><i class="fas fa-arrow-left text-[10px]"></i> Back</button>
            <button id="tourNextBtn" onclick="tourNext()" class="px-4 sm:px-6 py-2 sm:py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white rounded-xl font-bold text-[10px] sm:text-xs transition-all flex items-center gap-2 shadow-lg shadow-emerald-500/30"><span id="tourNextText">Next</span> <i class="fas fa-arrow-right text-[10px]"></i></button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- NAV (shared) -->
@include('partials.public-nav')

<!-- =====================================================================
     HERO
     ===================================================================== -->
<section id="hero" class="relative pt-28 sm:pt-32 md:pt-40 pb-14 sm:pb-20 md:pb-28 overflow-hidden px-4 sm:px-6">
  <div class="hero-mesh"></div>
  <div class="hero-grid"></div>

  <div class="max-w-7xl mx-auto grid lg:grid-cols-12 gap-10 lg:gap-8 items-center relative">

    {{-- ---------- left: copy ---------- --}}
    <div id="heroLeft" class="lg:col-span-6 text-center lg:text-left">
      <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white border border-emerald-100 shadow-sm mb-4 sm:mb-6">
        <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span></span>
        <span class="text-[10px] font-black text-emerald-700 uppercase tracking-[0.2em]">2026 Digital Initiative</span>
      </div>

      <h1 class="font-display text-[2.75rem] sm:text-6xl lg:text-7xl font-extrabold text-slate-900 leading-[0.95] mb-4 sm:mb-6">Smart Data.<br><span class="text-gradient-emerald">Better Service.</span></h1>

      <p class="text-sm sm:text-base md:text-lg text-slate-600 leading-relaxed mb-6 sm:mb-8 max-w-lg mx-auto lg:mx-0">Isang hakbang tungo sa pag-unlad ng <strong class="text-slate-900">Barangay Bactad East</strong>. Ang BIMS ay binuo para sa mas maayos, ligtas, at mabilis na pagproseso ng inyong mga dokumento.</p>

      <div class="flex flex-wrap justify-center lg:justify-start gap-3 items-center">
        <a href="/login" class="group px-6 py-3.5 sm:px-8 sm:py-4 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-2xl font-bold text-sm shadow-xl shadow-emerald-600/30 hover:shadow-2xl hover:-translate-y-0.5 transition-all flex items-center gap-2">
          <i class="fas fa-sign-in-alt text-xs"></i> Mag-login
        </a>
        <a href="#services" class="group px-6 py-3.5 sm:px-8 sm:py-4 bg-slate-900 text-white rounded-2xl font-bold text-sm hover:bg-emerald-700 transition-all shadow-xl shadow-slate-900/20 flex items-center gap-2">
          Explore Modules <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
        </a>
      </div>

      <div class="mt-6 sm:mt-8 flex items-center justify-center lg:justify-start gap-3">
        <div class="flex -space-x-3">
          <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 border-2 border-white flex items-center justify-center text-white text-[10px] font-black shadow-md">J</div>
          <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-gradient-to-br from-teal-400 to-teal-600 border-2 border-white flex items-center justify-center text-white text-[10px] font-black shadow-md">R</div>
          <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-gradient-to-br from-cyan-400 to-cyan-600 border-2 border-white flex items-center justify-center text-white text-[10px] font-black shadow-md">K</div>
        </div>
        <div class="text-left">
          <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Powered by</p>
          <p class="text-xs font-bold text-slate-700">PSU-Asingan BSIT</p>
        </div>
      </div>
    </div>

    {{-- ---------- right: dashboard mockup ---------- --}}
    <div class="lg:col-span-6 relative">
      <div class="hero-card relative max-w-xl mx-auto">
        {{-- floating chips --}}
        <div class="chip-float floaty absolute -top-5 -right-1 sm:-right-5 z-20 flex items-center gap-2.5 px-3.5 py-2.5 rounded-2xl bg-white border border-emerald-100">
          <span class="w-8 h-8 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white text-xs"><i class="fas fa-shield-alt"></i></span>
          <div><p class="text-[11px] font-extrabold text-slate-900 leading-none">Data Encrypted</p><p class="text-[10px] text-slate-500 mt-1">Privacy Act</p></div>
        </div>
        <div class="chip-float floaty-slow absolute -bottom-5 -left-1 sm:-left-6 z-20 flex items-center gap-2.5 px-3.5 py-2.5 rounded-2xl bg-white border border-emerald-100">
          <span class="w-8 h-8 rounded-xl bg-gradient-to-br from-sky-400 to-blue-600 flex items-center justify-center text-white text-xs"><i class="fas fa-check"></i></span>
          <div><p class="text-[11px] font-extrabold text-slate-900 leading-none">Request approved</p><p class="text-[10px] text-slate-500 mt-1">Barangay Clearance</p></div>
        </div>

        {{-- window --}}
        <div class="mock rounded-3xl overflow-hidden">
          <div class="flex items-center gap-2 px-4 py-3 border-b border-slate-100 bg-slate-50/50">
            <span class="w-2.5 h-2.5 rounded-full bg-rose-300"></span><span class="w-2.5 h-2.5 rounded-full bg-amber-300"></span><span class="w-2.5 h-2.5 rounded-full bg-emerald-300"></span>
            <div class="ml-3 flex-1 max-w-[220px] px-3 py-1 rounded-lg bg-white border border-slate-100 text-[10px] text-slate-400 font-medium truncate"><i class="fas fa-lock text-emerald-500 mr-1.5 text-[9px]"></i>bims / dashboard</div>
          </div>

          <div class="p-4 sm:p-6">
            <div class="grid grid-cols-3 gap-2.5 sm:gap-3">
              <div class="col-span-1 rounded-2xl p-3 sm:p-4 bg-gradient-to-br from-emerald-500 to-emerald-700 text-white shadow-lg shadow-emerald-500/30">
                <i class="fas fa-users text-xs opacity-80"></i>
                <p class="font-display text-xl sm:text-3xl font-extrabold leading-none mt-2" data-count="{{ $totalResidents }}">{{ number_format($totalResidents) }}</p>
                <p class="text-[9px] sm:text-[11px] font-semibold opacity-85 mt-1">Total Residents</p>
              </div>
              <div class="rounded-2xl p-3 sm:p-4 bg-blue-50 border border-blue-100">
                <i class="fas fa-file-alt text-xs text-blue-500"></i>
                <p class="font-display text-base sm:text-xl font-extrabold text-slate-900 leading-none mt-2">Online</p>
                <p class="text-[9px] sm:text-[11px] font-semibold text-slate-500 mt-1">Requests</p>
              </div>
              <div class="rounded-2xl p-3 sm:p-4 bg-amber-50 border border-amber-100">
                <i class="fas fa-chart-line text-xs text-amber-500"></i>
                <p class="font-display text-base sm:text-xl font-extrabold text-slate-900 leading-none mt-2">Live</p>
                <p class="text-[9px] sm:text-[11px] font-semibold text-slate-500 mt-1">Dashboard</p>
              </div>
            </div>

            {{-- decorative chart --}}
            <div class="mt-4 rounded-2xl border border-slate-100 p-3 sm:p-4">
              <div class="flex items-center justify-between mb-3">
                <p class="text-[11px] sm:text-xs font-extrabold text-slate-800">Population overview</p>
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Illustration</span>
              </div>
              <div class="flex items-end gap-1.5 sm:gap-2 h-20 sm:h-24">
                @foreach ([40, 62, 48, 80, 66, 92, 74, 58, 86, 70, 96, 78] as $bi => $h)
                  <div class="mock-bar flex-1" style="height: {{ $h }}%; animation-delay: {{ .5 + $bi * .06 }}s; {{ $bi % 3 === 1 ? 'background: linear-gradient(180deg,#5eead4,#0d9488);' : '' }}"></div>
                @endforeach
              </div>
            </div>

            {{-- sample requests --}}
            <div class="mt-4 space-y-2">
              @foreach ([
                ['fa-file-shield', 'Barangay Clearance',        'Approved',   'bg-emerald-100 text-emerald-700'],
                ['fa-house-user',  'Certificate of Residency',  'Processing', 'bg-amber-100 text-amber-700'],
                ['fa-hand-holding-heart', 'Certificate of Indigency', 'Ready',  'bg-sky-100 text-sky-700'],
              ] as $row)
              <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl border border-slate-100">
                <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center text-xs"><i class="fas {{ $row[0] }}"></i></span>
                <p class="flex-1 text-[11px] sm:text-xs font-bold text-slate-700 truncate">{{ $row[1] }}</p>
                <span class="px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-extrabold {{ $row[3] }}">{{ $row[2] }}</span>
              </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ---------- stat band ---------- --}}
  <div class="max-w-6xl mx-auto relative mt-14 sm:mt-20">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4">
      @foreach ([
        ['fa-users',      'from-emerald-400 to-emerald-600', $totalResidents, 'Mga Residente', 'Live data mula sa system', true],
        ['fa-landmark',   'from-amber-400 to-orange-500',    '1964',          'Itinatag',      'Mahigit 60 taon ng serbisyo', false],
        ['fa-shield-alt', 'from-sky-400 to-blue-600',        'RA 10173',      'Data Privacy',  'Sumusunod sa batas', false],
        ['fa-file-export','from-violet-400 to-purple-600',   'Online',        'Document Requests', 'Walang pila, walang abala', false],
      ] as $b)
      <div class="reveal glass-card rounded-2xl p-3.5 sm:p-5 flex items-center gap-3 sm:gap-4" style="--d: {{ $loop->index * .08 }}s">
        <span class="w-10 h-10 sm:w-12 sm:h-12 shrink-0 rounded-xl sm:rounded-2xl bg-gradient-to-br {{ $b[1] }} text-white flex items-center justify-center shadow-lg"><i class="fas {{ $b[0] }} text-sm sm:text-base"></i></span>
        <div class="min-w-0">
          <p class="font-display text-lg sm:text-2xl font-extrabold text-slate-900 leading-none" @if($b[5]) data-count="{{ $b[2] }}" @endif>{{ $b[5] ? number_format($b[2]) : $b[2] }}</p>
          <p class="text-[11px] sm:text-xs font-bold text-slate-700 mt-1 truncate">{{ $b[3] }}</p>
          <p class="text-[10px] text-slate-400 hidden sm:block truncate">{{ $b[4] }}</p>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- =====================================================================
     MARQUEE
     ===================================================================== -->
<section class="py-5 sm:py-7 border-y border-emerald-100 bg-white/60" aria-hidden="true">
  <div class="marquee">
    <div class="marquee-track">
      @for ($r = 0; $r < 2; $r++)
        @foreach($marquee as $m)
          <span class="flex items-center gap-2.5 px-4 py-2 rounded-full bg-white border border-emerald-100 text-xs sm:text-sm font-bold text-slate-600 whitespace-nowrap shadow-sm">
            <i class="fas fa-circle text-[5px] text-emerald-500"></i>{{ $m }}
          </span>
        @endforeach
      @endfor
    </div>
  </div>
</section>

<!-- =====================================================================
     SERVICES / FEATURES
     ===================================================================== -->
<section id="services" class="py-14 sm:py-20 md:py-28 bg-emerald-50/30">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="flex flex-col md:flex-row justify-between items-center md:items-end mb-10 sm:mb-14 gap-6">
      <div class="max-w-2xl text-center md:text-left w-full md:w-auto">
        <span class="section-tag">System Capabilities</span>
        <h2 class="font-display text-3xl sm:text-4xl md:text-6xl font-extrabold text-slate-900 leading-[1.02]">Pangunahing <span class="text-emerald-600">Features</span></h2>
        <p class="text-slate-500 text-sm md:text-base mt-3 sm:mt-4 max-w-xl mx-auto md:mx-0">Tuklasin ang mga pangunahing feature ng BIMS.</p>
      </div>
      <button onclick="startTour()" class="group flex items-center gap-3 px-5 py-3 sm:px-6 sm:py-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-2xl font-bold text-sm shadow-xl shadow-emerald-500/30 hover:shadow-2xl transition-all duration-300 hover:scale-[1.03] active:scale-[0.98] w-full md:w-auto justify-center">
        <div class="relative"><span class="absolute inset-0 bg-white/30 rounded-full animate-ping"></span><div class="relative w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-white/20 flex items-center justify-center"><i class="fas fa-play text-xs"></i></div></div>
        <div class="text-left"><p class="text-[10px] uppercase tracking-widest opacity-80 font-bold">Quick Tour</p><p class="text-sm font-black">Start Tour</p></div>
      </button>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
      @foreach ($features as $f)
      <div class="reveal glass-card {{ $f[5] }} p-6 sm:p-8 rounded-3xl overflow-hidden group" style="--d: {{ $loop->index * .07 }}s">
        <div class="absolute -top-16 -right-16 w-44 h-44 rounded-full bg-gradient-to-br {{ $f[4] }} opacity-[.08] group-hover:opacity-[.16] group-hover:scale-125 transition-all duration-700"></div>

        <div class="relative {{ $loop->last ? 'sm:flex sm:items-center sm:gap-8' : '' }}">
          <div class="svc-icon w-14 h-14 sm:w-16 sm:h-16 shrink-0 rounded-2xl bg-gradient-to-br {{ $f[4] }} flex items-center justify-center mb-5 {{ $loop->last ? 'sm:mb-0' : '' }} shadow-lg">
            <i class="fas {{ $f[0] }} text-white text-xl sm:text-2xl"></i>
          </div>
          <div>
            <h3 class="font-display text-xl sm:text-2xl font-extrabold text-slate-900 mb-2">{{ $f[1] }}</h3>
            <p class="text-slate-500 text-sm leading-relaxed">{{ $f[2] }}</p>
            @if(count($f[3]))
              <ul class="mt-4 flex flex-wrap gap-2">
                @foreach($f[3] as $li)
                  <li class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-100"><i class="fas fa-check-circle text-[10px]"></i>{{ $li }}</li>
                @endforeach
              </ul>
            @endif
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- =====================================================================
     HOW IT WORKS
     ===================================================================== -->
<section id="how" class="py-14 sm:py-20 md:py-28 overflow-hidden">
  <div class="max-w-6xl mx-auto px-4 sm:px-6">
    <div class="text-center mb-10 sm:mb-16">
      <span class="section-tag">Paano Gumagana</span>
      <h2 class="font-display text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-900 mb-3">Apat na hakbang, <span class="text-emerald-600">tapos na.</span></h2>
      <p class="text-slate-500 text-sm md:text-base max-w-xl mx-auto">Hindi mo na kailangang pumila sa Barangay Hall para sa mga karaniwang dokumento.</p>
    </div>

    <div class="relative grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
      <div class="step-line hidden lg:block"></div>
      @foreach ($steps as $s)
      <div class="reveal relative text-center" style="--d: {{ $loop->index * .1 }}s">
        <div class="relative mx-auto w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-600 text-white flex items-center justify-center shadow-xl shadow-emerald-500/30 mb-4 ring-8 ring-white">
          <i class="fas {{ $s[0] }} text-lg sm:text-xl"></i>
          <span class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-slate-900 text-white text-[11px] font-black flex items-center justify-center ring-2 ring-white">{{ $loop->iteration }}</span>
        </div>
        <h3 class="font-display text-base sm:text-lg font-extrabold text-slate-900 mb-1">{{ $s[1] }}</h3>
        <p class="text-slate-500 text-xs sm:text-sm leading-relaxed max-w-[220px] mx-auto">{{ $s[2] }}</p>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- ANNOUNCEMENTS (latest 3; hindi lalabas kung walang published) -->
@include('partials.home-announcements')

<!-- =====================================================================
     ABOUT
     ===================================================================== -->
<section id="about" class="py-14 sm:py-20 md:py-28 overflow-hidden">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="text-center mb-10 sm:mb-16">
      <span class="section-tag">Tungkol sa BIMS</span>
      <h2 class="font-display text-3xl sm:text-4xl md:text-6xl font-extrabold text-slate-900 mb-3 sm:mb-5">Ano ang <span class="text-emerald-600">BIMS</span>?</h2>
      <p class="text-slate-500 text-sm md:text-base max-w-3xl mx-auto leading-relaxed">Ang BIMS ay isang digital platform na binuo upang gawing mas mabilis, maayos, at transparent ang mga serbisyo ng Barangay Bactad East.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-8 items-stretch">
      @foreach ([
        ['bg-emerald-100', 'text-emerald-600', 'from-emerald-400 to-emerald-600', 'fa-bullseye', 'Mission', 'To provide the community of Barangay Bactad East with a secure, efficient, and user-friendly digital portal. The system streamlines barangay operations from managing resident profiles to processing document requests.'],
        ['bg-blue-100',    'text-blue-600',    'from-sky-400 to-blue-600',       'fa-eye',      'Vision',  'To transform Barangay Bactad East into a modern, digitally connected community where technology ensures fast, safe, and easily accessible public services for all.'],
      ] as $mv)
      <div class="reveal {{ $loop->first ? 'reveal-left' : 'reveal-right' }} glass-card p-7 sm:p-10 md:p-12 rounded-3xl flex flex-col overflow-hidden">
        <i class="fas {{ $mv[3] }} absolute -bottom-6 -right-4 text-[9rem] {{ $mv[1] }} opacity-[.06]" aria-hidden="true"></i>
        <div class="svc-icon w-14 h-14 sm:w-20 sm:h-20 bg-gradient-to-br {{ $mv[2] }} rounded-2xl flex items-center justify-center mb-5 sm:mb-8 shadow-lg"><i class="fas {{ $mv[3] }} text-white text-xl sm:text-3xl"></i></div>
        <h3 class="font-display text-2xl sm:text-4xl font-extrabold mb-3 sm:mb-5"><span class="{{ $mv[1] }}">{{ $mv[4] }}</span></h3>
        <p class="relative text-slate-600 text-sm md:text-base leading-relaxed">{{ $mv[5] }}</p>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- =====================================================================
     HISTORY
     ===================================================================== -->
<section id="history" class="py-14 sm:py-20 md:py-28 bg-emerald-50/30">
  <div class="max-w-6xl mx-auto px-4 sm:px-6">
    <div class="text-center mb-10 sm:mb-16">
      <span class="section-tag">Tungkol sa Barangay</span>
      <h2 class="font-display text-3xl sm:text-4xl md:text-6xl font-extrabold text-slate-900 mb-2 sm:mb-4">Kasaysayan ng <span class="text-emerald-600">Bactad East</span></h2>
    </div>

    <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-start">
      {{-- story --}}
      <div>
        <div class="space-y-4 sm:space-y-5 text-slate-600 text-sm sm:text-base leading-relaxed">
          <p class="reveal" style="--d: 0s">Ang <strong class="text-emerald-700">Barangay Bactad East</strong> ay kilala sa malalawak na agricultural farmlands nito. Ito ay dating bahagi ng <strong>Bactad Proper</strong> (na ngayon ay Barangay P.T. Orata).</p>
          <p class="reveal" style="--d: .1s">Sa pamamagitan ng walang humpay na pagsisikap ng mga lider, ang Municipal o City Ordinance ay <strong>naaprubahan noong 1964</strong>. Si <strong class="text-emerald-700">Protacio Suyat</strong> ang naging unang Barangay Captain ng Bactad East.</p>
          <p class="reveal" style="--d: .2s">Ang Barangay Bactad East ay matatagpuan sa <strong>silangang bahagi ng Urdaneta City</strong>, mga <strong>pitong kilometro</strong> mula sa City Proper.</p>
        </div>

        <div class="mt-8 grid grid-cols-2 gap-3">
          @foreach ($facts as $f)
          <div class="reveal bg-white rounded-2xl p-4 sm:p-5 border border-emerald-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all" style="--d: {{ .1 + $loop->index * .08 }}s">
            <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-1">{{ $f[0] }}</p>
            <p class="font-display {{ $f[2] }} font-extrabold text-slate-900">{{ $f[1] }}</p>
          </div>
          @endforeach
        </div>
      </div>

      {{-- timeline --}}
      <div class="tl" id="timeline">
        <div class="tl-fill"></div>
        @foreach ($timeline as $e)
        <div class="tl-item">
          <div class="tl-dot"><i class="fas fa-check"></i></div>
          <div class="tl-card">
            <div class="tl-year font-display">{{ $e[0] }}</div>
            <div class="tl-title">{{ $e[1] }}</div>
            <div class="tl-text">{{ $e[2] }}</div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</section>

<!-- =====================================================================
     OFFICIALS
     ===================================================================== -->
<section id="officials" class="py-14 sm:py-20 md:py-28">
  <div class="max-w-6xl mx-auto px-4 sm:px-6">
    <div class="text-center mb-10 sm:mb-14">
      <span class="section-tag">Kasalukuyang Pamunuan</span>
      <h2 class="font-display text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-900 mb-3">Barangay <span class="text-emerald-600">Officials</span></h2>
      <p class="text-slate-500 text-sm md:text-base max-w-2xl mx-auto">Ang mga opisyal na inihalal ng mamamayan para sa terminong 2023-2025.</p>
    </div>

    {{-- Punong Barangay --}}
    <div class="reveal glass-card rounded-3xl overflow-hidden grid md:grid-cols-5 max-w-4xl mx-auto mb-8 sm:mb-10 official-card">
      <div class="md:col-span-2 h-80 md:h-auto min-h-[300px] bg-emerald-700 relative overflow-hidden">
        <img src="{{ asset('images/officials/' . $captain[0] . '.jpg') }}" alt="{{ $captain[1] }}" class="w-full h-full object-cover object-top" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        <div class="hidden absolute inset-0 items-center justify-center bg-gradient-to-br from-emerald-600 to-emerald-800"><i class="fas {{ $captain[4] }} text-white text-7xl opacity-40"></i></div>
        <div class="absolute inset-0 bg-gradient-to-t from-emerald-950/50 via-transparent to-transparent md:hidden"></div>
      </div>
      <div class="md:col-span-3 p-7 sm:p-10 flex flex-col justify-center relative">
        <i class="fas fa-landmark absolute -bottom-4 -right-3 text-[8rem] text-emerald-600 opacity-[.06]" aria-hidden="true"></i>
        <span class="inline-flex w-fit items-center gap-1.5 px-3 py-1 rounded-full bg-amber-400 text-emerald-900 text-[10px] font-black uppercase tracking-widest shadow mb-4"><i class="fas fa-star text-[9px]"></i>{{ $captain[3] }}</span>
        <h3 class="font-display text-2xl sm:text-4xl font-extrabold text-slate-900 leading-tight">{{ $captain[1] }}</h3>
        <p class="text-emerald-600 font-bold text-sm uppercase tracking-wider mt-2">{{ $captain[2] }}</p>
        <div class="mt-5 flex flex-wrap gap-2">
          <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-100"><i class="fas fa-map-pin text-[10px]"></i>Barangay Bactad East</span>
          <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-100"><i class="fas fa-city text-[10px]"></i>Urdaneta City</span>
        </div>
      </div>
    </div>

    {{-- Council --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 sm:gap-5">
      @foreach ($council as $o)
      <div class="reveal official-card relative rounded-2xl sm:rounded-3xl overflow-hidden bg-emerald-600 shadow-md aspect-[4/5]" style="--d: {{ ($loop->index % 3) * .08 }}s">
        <img src="{{ asset('images/officials/' . $o[0] . '.jpg') }}" alt="{{ $o[1] }}" class="absolute inset-0 w-full h-full object-cover object-top" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        <div class="hidden absolute inset-0 items-center justify-center bg-gradient-to-br from-emerald-500 to-teal-700"><i class="fas {{ $o[4] }} text-white text-5xl sm:text-7xl opacity-40"></i></div>
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/10 to-transparent"></div>
        <span class="absolute top-2.5 left-2.5 sm:top-4 sm:left-4 px-2 sm:px-3 py-0.5 sm:py-1 bg-emerald-100 text-emerald-700 rounded-md sm:rounded-lg text-[8px] sm:text-[10px] font-black uppercase tracking-widest shadow-lg">{{ $o[3] }}</span>
        <div class="absolute bottom-0 inset-x-0 p-3 sm:p-5">
          <h3 class="font-display text-sm sm:text-xl font-extrabold text-white leading-tight">{{ $o[1] }}</h3>
          <p class="text-emerald-300 font-bold text-[9px] sm:text-xs uppercase tracking-wider mt-1">{{ $o[2] }}</p>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- =====================================================================
     FAQ
     ===================================================================== -->
<section id="faq" class="py-14 sm:py-20 md:py-28 bg-slate-50">
  <div class="max-w-4xl mx-auto px-4 sm:px-6">
    <div class="text-center mb-10 sm:mb-14">
      <span class="section-tag">Mga Madalas Itanong</span>
      <h2 class="font-display text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-900 mb-3">Frequently Asked <span class="text-emerald-600">Questions</span></h2>
      <p class="text-slate-500 text-sm md:text-base">Mga sagot sa karaniwang katanungan.</p>
    </div>

    <div class="space-y-3 sm:space-y-4">
      @foreach ($faqs as $f)
      <div class="faq-item bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <button class="faq-toggle w-full px-5 sm:px-7 py-4 sm:py-5 flex justify-between items-center text-left font-bold text-slate-900 text-sm sm:text-base gap-4" aria-expanded="false">
          <span class="flex items-center gap-3">
            <span class="hidden sm:flex w-8 h-8 shrink-0 rounded-lg bg-emerald-50 text-emerald-600 items-center justify-center text-xs font-black">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
            {{ $f[0] }}
          </span>
          <i class="fas fa-chevron-down text-emerald-600 text-xs flex-shrink-0"></i>
        </button>
        <div class="faq-content px-5 sm:px-7 pb-4 sm:pb-6 sm:pl-[4.75rem] text-slate-600 text-xs sm:text-sm leading-relaxed"><p>{!! $f[1] !!}</p></div>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- =====================================================================
     CTA
     ===================================================================== -->
<section class="px-4 sm:px-6 py-14 sm:py-20">
  <div class="reveal cta-bg relative max-w-6xl mx-auto rounded-[2rem] sm:rounded-[2.5rem] overflow-hidden px-6 py-12 sm:px-14 sm:py-16 text-center shadow-2xl shadow-emerald-900/30">
    <div class="cta-dots absolute inset-0"></div>
    <div class="absolute -top-20 -left-20 w-72 h-72 rounded-full bg-emerald-300/20 blur-3xl"></div>
    <div class="absolute -bottom-24 -right-16 w-72 h-72 rounded-full bg-teal-300/20 blur-3xl"></div>

    <div class="relative">
      <div class="w-16 h-16 mx-auto rounded-2xl bg-white/15 border border-white/25 p-1.5 mb-5 floaty">
        <img src="{{ asset('images/brgy_logo.png.jpg') }}" alt="Barangay Logo" class="w-full h-full object-contain rounded-xl">
      </div>
      <h2 class="font-display text-3xl sm:text-5xl font-extrabold text-white leading-[1.05] mb-4">Handa ka na bang <br class="hidden sm:block">gamitin ang <span class="text-emerald-200">BIMS</span>?</h2>
      <p class="text-emerald-50/85 text-sm sm:text-base max-w-xl mx-auto mb-8">Mag-login at simulan nang mag-request ng iyong mga dokumento — mabilis, ligtas, at walang pila.</p>
      <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="/login" class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-2xl bg-white text-emerald-800 font-extrabold text-sm shadow-xl hover:-translate-y-0.5 hover:shadow-2xl transition-all"><i class="fas fa-sign-in-alt"></i> Mag-login Ngayon</a>
        <button onclick="startTour()" class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/30 text-white font-bold text-sm transition-all"><i class="fas fa-play text-xs"></i> Quick Tour</button>
      </div>
    </div>
  </div>
</section>

<!-- FOOTER (shared) -->
@include('partials.public-footer')

<!-- AI CHAT (shared) -->
@include('partials.public-chat')

<!-- =====================================================================
     TOUR SCRIPT
     ===================================================================== -->
<script>
let tourSlide = 0;
const tourSlides = document.querySelectorAll('.tour-slide'), tourTotal = tourSlides.length;
const tourProgressBar = document.getElementById('tourProgressBar'), tourCurrentNum = document.getElementById('tourCurrentSlide'),
      tourPrevBtn = document.getElementById('tourPrevBtn'), tourNextText = document.getElementById('tourNextText'),
      tourDots = document.getElementById('tourDots'), tourModal = document.getElementById('tourModal');
document.getElementById('tourTotalSlides').textContent = tourTotal;

function startTour() { tourSlide = 0; tourModal.classList.remove('hidden'); document.body.style.overflow = 'hidden'; tourModal.scrollTop = 0; updateTourUI(); }
function closeTour() { tourModal.classList.add('hidden'); document.body.style.overflow = ''; }
function updateTourUI() {
  tourSlides.forEach(s => { s.classList.add('hidden'); s.classList.remove('tour-active'); });
  tourSlides[tourSlide].classList.remove('hidden'); tourSlides[tourSlide].classList.add('tour-active');
  tourCurrentNum.textContent = tourSlide + 1;
  tourProgressBar.style.width = ((tourSlide + 1) / tourTotal * 100) + '%';
  tourPrevBtn.classList.toggle('hidden', tourSlide === 0);
  tourPrevBtn.classList.toggle('flex', tourSlide !== 0);
  tourNextText.textContent = tourSlide === tourTotal - 1 ? 'Finish' : 'Next';
  tourDots.innerHTML = '';
  for (let i = 0; i < tourTotal; i++) {
    const d = document.createElement('button');
    d.setAttribute('aria-label', 'Slide ' + (i + 1));
    d.className = 'h-2 rounded-full transition-all duration-300 ' + (i === tourSlide ? 'w-6 sm:w-8 bg-emerald-400' : 'w-2 bg-white/30 hover:bg-white/50');
    d.onclick = () => { tourSlide = i; updateTourUI(); tourModal.scrollTop = 0; };
    tourDots.appendChild(d);
  }
}
function tourNext() { if (tourSlide < tourTotal - 1) { tourSlide++; updateTourUI(); tourModal.scrollTop = 0; } else closeTour(); }
function tourPrev() { if (tourSlide > 0) { tourSlide--; updateTourUI(); tourModal.scrollTop = 0; } }
document.addEventListener('keydown', e => {
  if (tourModal.classList.contains('hidden')) return;
  if (e.key === 'Escape') closeTour();
  if (e.key === 'ArrowRight') tourNext();
  if (e.key === 'ArrowLeft') tourPrev();
});
updateTourUI();
</script>

<!-- =====================================================================
     PAGE EFFECTS
     ===================================================================== -->
<script>
(function () {
  const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const $ = (s, r = document) => r.querySelector(s), $$ = (s, r = document) => Array.from(r.querySelectorAll(s));

  /* ---------- Dark mode ---------- */
  const root = document.documentElement;
  let saved = null; try { saved = localStorage.getItem('bims-theme'); } catch (e) {}
  if (saved ? saved === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches) root.classList.add('dark');
  const loginBtn = $('nav a[href="/login"]');
  if (loginBtn && !$('#themeToggle')) {
    const wrap = document.createElement('div'); wrap.className = 'flex items-center gap-2 sm:gap-3';
    loginBtn.parentNode.insertBefore(wrap, loginBtn);
    const tb = document.createElement('button'); tb.id = 'themeToggle'; tb.setAttribute('aria-label', 'Toggle dark mode');
    tb.innerHTML = '<i class="fas fa-sun t-sun"></i><i class="fas fa-moon t-moon"></i>';
    wrap.append(tb, loginBtn);
    tb.addEventListener('click', () => { const d = root.classList.toggle('dark'); try { localStorage.setItem('bims-theme', d ? 'dark' : 'light'); } catch (e) {} });
  }

  /* ---------- Hero entrance (inaalis ang class pagkatapos para gumana ang hover) ---------- */
  const heroItems = [...$$('#heroLeft > *').map((el, i) => [el, .1 + i * .12]), ...$$('.hero-card').map(el => [el, .45])];
  heroItems.forEach(([el, d]) => {
    el.classList.add('hero-in'); el.style.setProperty('--d', d + 's');
    el.addEventListener('animationend', e => { if (e.animationName === 'heroIn') { el.classList.remove('hero-in'); el.style.removeProperty('--d'); } });
  });

  /* ---------- Scroll reveal ---------- */
  // Mga elementong may .reveal na sa markup + mga galing sa shared partial (announcements)
  $$('#announcements .section-tag, #announcements h2').forEach(el => el.classList.add('reveal'));
  $$('#announcements .grid > article').forEach((el, i) => { el.classList.add('reveal'); el.style.setProperty('--d', Math.min(i * .1, .6) + 's'); });
  $$('#officials .text-center > *, #faq .text-center > *, #how .text-center > *, #about .text-center > *, #history .text-center > *, #services .max-w-2xl > *, footer .grid > div').forEach((el, i) => {
    el.classList.add('reveal'); el.style.setProperty('--d', Math.min((i % 4) * .1, .4) + 's');
  });
  $$('#faq .faq-item').forEach((el, i) => { el.classList.add('reveal'); el.style.setProperty('--d', Math.min(i * .08, .5) + 's'); });

  const io = new IntersectionObserver(entries => entries.forEach(e => {
    if (!e.isIntersecting) return;
    const el = e.target; el.classList.add('in'); io.unobserve(el);
    setTimeout(() => { el.classList.remove('reveal', 'reveal-left', 'reveal-right', 'in'); el.style.removeProperty('--d'); }, 1500);
  }), { threshold: .12, rootMargin: '0px 0px -40px 0px' });
  $$('.reveal').forEach(el => io.observe(el));

  /* ---------- Count-up (lahat ng [data-count]) ---------- */
  $$('[data-count]').forEach(counter => {
    const target = parseInt(counter.dataset.count, 10) || 0;
    if (reduce || !target) return;
    counter.textContent = '0';
    const co = new IntersectionObserver(([e]) => {
      if (!e.isIntersecting) return; co.disconnect();
      const start = performance.now() + 300, dur = 1800;
      const tick = now => {
        const p = Math.max(0, Math.min((now - start) / dur, 1));
        counter.textContent = Math.round(target * (1 - Math.pow(1 - p, 4))).toLocaleString();
        if (p < 1) requestAnimationFrame(tick);
      };
      requestAnimationFrame(tick);
    }, { threshold: .4 });
    co.observe(counter);
  });

  /* ---------- Scroll: progress bar, navbar, back-to-top ---------- */
  const bar = $('#scrollProgress'), nav = $('nav.fixed'), toTop = $('#toTop'); let tk = false;
  let maxScroll = 0; const calc = () => { maxScroll = document.documentElement.scrollHeight - innerHeight; };
  calc(); addEventListener('resize', calc); addEventListener('load', calc); setTimeout(calc, 1500);
  function onScroll() {
    const y = scrollY;
    bar.style.transform = `scaleX(${maxScroll > 0 ? Math.min(y / maxScroll, 1) : 0})`;
    if (nav) nav.classList.toggle('nav-scrolled', y > 20);
    toTop.classList.toggle('show', y > 600);
    tk = false;
  }
  addEventListener('scroll', () => { if (!tk) { requestAnimationFrame(onScroll); tk = true; } }, { passive: true });
  onScroll();
  toTop.addEventListener('click', () => scrollTo({ top: 0, behavior: 'smooth' }));
  root.style.scrollPaddingTop = (innerWidth < 1024 ? 110 : 90) + 'px';

  /* ---------- Active nav link (hash links lang) ---------- */
  const links = $$('nav .hidden.lg\\:flex a'); links.forEach(a => a.classList.add('nav-link'));
  const hashLinks = links.filter(a => (a.getAttribute('href') || '').startsWith('#'));
  const so = new IntersectionObserver(es => es.forEach(e => {
    if (e.isIntersecting) hashLinks.forEach(a => a.classList.toggle('is-active', a.getAttribute('href') === '#' + e.target.id));
  }), { rootMargin: '-45% 0px -50% 0px' });
  hashLinks.map(a => $(a.getAttribute('href'))).filter(Boolean).forEach(s => so.observe(s));

  /* ---------- Card spotlight + button shine ---------- */
  if (window.matchMedia('(hover: hover)').matches) $$('.glass-card').forEach(c => {
    let raf = 0;
    c.addEventListener('pointermove', e => {
      if (raf) return;
      raf = requestAnimationFrame(() => {
        const r = c.getBoundingClientRect(); c.style.setProperty('--mx', (e.clientX - r.left) + 'px'); c.style.setProperty('--my', (e.clientY - r.top) + 'px'); raf = 0;
      });
    }, { passive: true });
  });
  $$('a.bg-gradient-to-r, button.bg-gradient-to-r, a.bg-slate-900').forEach(b => b.classList.add('btn-shine'));

  /* ---------- FAQ accordion ---------- */
  $$('.faq-toggle').forEach(btn => btn.addEventListener('click', () => {
    const item = btn.closest('.faq-item'), open = !item.classList.contains('active');
    $$('.faq-item.active').forEach(o => { o.classList.remove('active'); $('.faq-toggle', o).setAttribute('aria-expanded', 'false'); });
    item.classList.toggle('active', open); btn.setAttribute('aria-expanded', String(open));
  }));

  /* ---------- Typing headline ---------- */
  const target = $('h1 .text-gradient-emerald');
  if (target) {
    const words = ['Better Service.', 'Mas Mabilis.', 'Mas Ligtas.', 'Mas Maayos.'];
    target.classList.add('type-wrap');
    const node = document.createTextNode(words[0]); target.textContent = ''; target.appendChild(node);
    const caret = document.createElement('span'); caret.className = 'type-caret'; target.after(caret);
    if (!reduce) {
      let p = 0, i = words[0].length, del = true;
      const step = () => {
        if (del) {
          if (--i <= 0) { del = false; p = (p + 1) % words.length; i = 0; node.nodeValue = '\u200B'; return setTimeout(step, 350); }
          node.nodeValue = words[p].slice(0, i); setTimeout(step, 40);
        } else {
          node.nodeValue = words[p].slice(0, ++i);
          if (i >= words[p].length) { del = true; return setTimeout(step, 2200); }
          setTimeout(step, 85);
        }
      };
      setTimeout(step, 3000);
    }
  }

  /* ---------- Subtle 3D tilt sa hero dashboard (desktop lang) ---------- */
  const mock = $('.hero-card .mock');
  if (mock && !reduce && window.matchMedia('(hover: hover) and (min-width: 1024px)').matches) {
    const host = mock.parentElement; let raf = 0;
    host.addEventListener('pointermove', e => {
      if (raf) return;
      raf = requestAnimationFrame(() => {
        const r = host.getBoundingClientRect();
        const x = (e.clientX - r.left) / r.width - .5, y = (e.clientY - r.top) / r.height - .5;
        mock.style.transform = `perspective(1000px) rotateY(${x * 6}deg) rotateX(${-y * 6}deg)`;
        raf = 0;
      });
    }, { passive: true });
    host.addEventListener('pointerleave', () => { mock.style.transform = ''; });
    mock.style.transition = 'transform .25s ease-out';
  }

  /* ---------- History timeline ---------- */
  const tl = $('#timeline');
  if (tl) {
    const fill = $('.tl-fill', tl), items = $$('.tl-item', tl); let t2 = false, vis = false;
    const upd = () => {
      const r = tl.getBoundingClientRect(), mid = innerHeight * .6;
      fill.style.height = (reduce ? r.height : Math.max(0, Math.min(r.height, mid - r.top))) + 'px';
      items.forEach(it => it.classList.toggle('on', reduce || it.getBoundingClientRect().top < mid));
      t2 = false;
    };
    new IntersectionObserver(([e]) => { vis = e.isIntersecting; if (vis) upd(); }, { rootMargin: '200px' }).observe(tl);
    addEventListener('scroll', () => { if (vis && !t2) { requestAnimationFrame(upd); t2 = true; } }, { passive: true });
    addEventListener('resize', upd); upd();
  }
})();
</script>
</body>
</html>