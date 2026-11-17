<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#04251c">
    <title>BIMS | Barangay Bactad East</title>
    <link rel="icon" href="{{ asset('images/brgy_logo.png.jpg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @php
        $developers = ['Jizelle Joy Iñigo', 'Ralph Lawrence Pate', 'Kristina Kaye Paasa', 'Vidyl Mae Petsay', 'Xander M. Ragadio', 'Abigail Tabucol'];
        $officials = ['alvin-cuchapin','angelito-navarro','erwin-aduca','francis-fernandez','henry-latonio','herbert-fang','john-desamito','orly-baybayan','reydendo-latonio','thenmarck-dulos'];
        $initials = fn ($n) => mb_strtoupper(mb_substr($n, 0, 1) . mb_substr(strrchr(' ' . $n, ' '), 1, 1));
    @endphp

    <style>
        :root {
            --g-950:#04251c; --g-900:#063a2b; --g-800:#065f46; --g-600:#059669;
            --g-500:#10b981; --g-400:#34d399; --g-200:#a7f3d0; --g-100:#d1fae5; --g-50:#effaf5;
            --out: cubic-bezier(.16, 1, .3, 1);      /* smooth deceleration */
            --inout: cubic-bezier(.76, 0, .24, 1);   /* shutter movement */
        }
        * { margin:0; padding:0; box-sizing:border-box; -webkit-tap-highlight-color:transparent; }
        html { background: var(--g-950); }
        body {
            font-family:'Instrument Sans', system-ui, sans-serif; color:#fff; min-height:100vh; min-height:100dvh;
            background: var(--g-950); -webkit-font-smoothing:antialiased; overflow-x:hidden;
        }
        body.is-loading { overflow:hidden; height:100dvh; }
        h1, h2, .display { font-family:'Bricolage Grotesque','Instrument Sans',sans-serif; letter-spacing:-.025em; }
        a { color:inherit; text-decoration:none; }
        button { font:inherit; color:inherit; background:none; border:0; cursor:pointer; }
        :focus-visible { outline:3px solid var(--g-400); outline-offset:3px; border-radius:.5rem; }

        /* ============ PAGE BACKGROUND (static layers, only blobs move via transform) ============ */
        .bg { position:fixed; inset:0; z-index:0; overflow:hidden;
              background: radial-gradient(ellipse at 20% 0%, rgba(16,185,129,.20), transparent 55%), var(--g-950); }
        .bg::before { content:''; position:absolute; inset:0; opacity:.55;
              background-image: repeating-radial-gradient(circle at 82% 38%, transparent 0 38px, rgba(52,211,153,.07) 38px 39px); }
        .blob { position:absolute; border-radius:50%; will-change:transform; }
        .blob.a { width:60vmax; height:60vmax; right:-20vmax; top:-18vmax;
                  background: radial-gradient(circle, rgba(16,185,129,.22), transparent 65%); animation: drift 22s ease-in-out infinite alternate; }
        .blob.b { width:46vmax; height:46vmax; left:-16vmax; bottom:-22vmax;
                  background: radial-gradient(circle, rgba(6,95,70,.55), transparent 65%); animation: drift 28s ease-in-out infinite alternate-reverse; }
        @keyframes drift { to { transform: translate3d(5vmax, 4vmax, 0) scale(1.08); } }

        /* ============ PRELOADER — the door ============ */
        .pre { --gold:#f2c46d; --gold-dim:rgba(242,196,109,.32); position:fixed; inset:0; z-index:50; pointer-events:none; perspective:1700px; overflow:hidden; }
        .pre.is-on { pointer-events:auto; }
        .pre.is-gone { display:none; }

        .door { position:absolute; top:0; bottom:0; width:50.2%; overflow:hidden; backface-visibility:hidden; -webkit-backface-visibility:hidden; will-change:transform;
                transition: transform 2.3s var(--inout);
                background:
                    linear-gradient(180deg, rgba(255,255,255,.06), transparent 24%, rgba(0,0,0,.42) 100%),
                    repeating-linear-gradient(90deg, rgba(255,255,255,.02) 0 2px, transparent 2px 10px),
                    linear-gradient(135deg, #0c2822 0%, #082a22 45%, #06161a 100%); }
        .door.l { left:0;  transform-origin:left center;  border-right:1px solid rgba(52,211,153,.4); }
        .door.r { right:0; transform-origin:right center; border-left:1px solid rgba(52,211,153,.4); }
        .pre.is-out .door.l { transform: rotateY(-104deg); }
        .pre.is-out .door.r { transform: rotateY(104deg); }
        .door::after { content:''; position:absolute; inset:0; background:#000; opacity:0; transition:opacity 1.8s ease; pointer-events:none; }
        .pre.is-out .door::after { opacity:.7; }
        .pre.is-unlocking:not(.is-out) .door { animation: rumble .5s linear; }
        @keyframes rumble { 0%,100% { transform:translate3d(0,0,0); } 15% { transform:translate3d(-1.5px,.5px,0); } 30% { transform:translate3d(1.5px,-.5px,0); }
                            45% { transform:translate3d(-1px,0,0); } 60% { transform:translate3d(1px,.5px,0); } 80% { transform:translate3d(-.5px,0,0); } }

        .panels { position:absolute; inset:7% 9%; display:grid; grid-template-rows:1fr 1.5fr; gap:4%; }
        .pnl { position:relative; border:1px solid var(--gold-dim); border-radius:12px; background:linear-gradient(145deg, rgba(255,255,255,.035), rgba(0,0,0,.2));
               box-shadow: inset 0 0 44px rgba(0,0,0,.55), inset 0 0 0 7px rgba(255,255,255,.012); }
        .pnl::before { content:''; position:absolute; inset:0; opacity:.7;
               background: radial-gradient(circle 3px at 12px 12px, var(--gold), transparent 3.6px), radial-gradient(circle 3px at calc(100% - 12px) 12px, var(--gold), transparent 3.6px),
                           radial-gradient(circle 3px at 12px calc(100% - 12px), var(--gold), transparent 3.6px), radial-gradient(circle 3px at calc(100% - 12px) calc(100% - 12px), var(--gold), transparent 3.6px); }
        .pnl::after { content:''; position:absolute; inset:22% 18%; border:1px solid rgba(52,211,153,.12); border-radius:8px; }

        .hinge { position:absolute; width:9px; height:46px; border-radius:3px; opacity:.85; background:linear-gradient(90deg,#a87722,var(--gold),#a87722); box-shadow:0 0 10px rgba(242,196,109,.35); }
        .door.l .hinge { left:5px; } .door.r .hinge { right:5px; }
        .hinge.h1 { top:10%; } .hinge.h2 { top:48%; } .hinge.h3 { top:84%; }
        .handle { position:absolute; top:50%; width:6px; height:76px; margin-top:-38px; border-radius:6px; background:linear-gradient(to bottom, var(--g-200), var(--g-500), #0e7490); box-shadow:0 0 16px rgba(16,185,129,.6); }
        .door.l .handle { right:12px; } .door.r .handle { left:12px; }
        .sheen { position:absolute; inset:0; pointer-events:none; transform:translateX(-130%); animation: dsheen 3.4s ease-in-out infinite;
                 background:linear-gradient(105deg, transparent 38%, rgba(255,255,255,.075) 49%, transparent 60%); }
        .door.r .sheen { animation-delay:.35s; }
        @keyframes dsheen { 0% { transform:translateX(-130%); } 60%,100% { transform:translateX(130%); } }
        .pre.is-ready .sheen { animation:none; }

        .frame { position:absolute; inset:0; pointer-events:none; transition: opacity 1.1s ease .1s; }
        .frame::before { content:''; position:absolute; inset:9px; border:1px solid var(--gold-dim); border-radius:22px; box-shadow:inset 0 0 90px rgba(0,0,0,.55); }
        .frame::after { content:''; position:absolute; top:3px; left:50%; width:13px; height:13px; margin-left:-6.5px; background:var(--gold); border-radius:2px; transform:rotate(45deg); box-shadow:0 0 16px rgba(242,196,109,.9); }
        .pre.is-out .frame { opacity:0; }

        .beam { position:absolute; top:0; bottom:0; left:50%; width:2px; margin-left:-1px; pointer-events:none; opacity:0; transform:scaleY(.15);
                background:linear-gradient(to bottom, transparent, var(--g-100) 20%, #fff 50%, var(--g-100) 80%, transparent);
                box-shadow:0 0 24px 6px rgba(52,211,153,.85), 0 0 100px 36px rgba(16,185,129,.45); transition: opacity .5s ease, transform .7s var(--out); }
        .pre.is-ready .beam { opacity:1; transform:scaleY(1); }
        .pre.is-out .beam { opacity:0; transition: opacity 1.2s ease .15s; }

        .dust { position:absolute; inset:0; pointer-events:none; overflow:hidden; }
        .dust i { position:absolute; top:-6px; border-radius:50%; background:rgba(209,250,229,.85); animation: dfall linear forwards; }
        @keyframes dfall { from { transform:translate3d(0,0,0); opacity:.9; } to { transform:translate3d(var(--dx,0), 42vh, 0); opacity:0; } }

        /* seal: logo + progress ring + lock */
        .core { position:absolute; inset:0; display:grid; place-content:center; justify-items:center; text-align:center; padding:0 1rem; pointer-events:none;
                transition: opacity .8s ease, transform 1.1s var(--out); }
        .pre.is-ready .core { transform:scale(1.05); }
        .pre.is-out .core { opacity:0; transform:scale(1.5); }
        .badge { position:relative; width:10rem; height:10rem; display:grid; place-items:center; border-radius:50%;
                 background:radial-gradient(circle, #06161a 56%, #0a2a24 100%); box-shadow:0 0 0 1px rgba(52,211,153,.25), 0 0 56px rgba(16,185,129,.32), 0 24px 44px -10px rgba(0,0,0,.85); }
        .badge svg { position:absolute; inset:0; width:100%; height:100%; transform:rotate(-90deg); overflow:visible; }
        .trk { fill:none; stroke:rgba(255,255,255,.08); stroke-width:3; }
        .glw { fill:none; stroke:rgba(16,185,129,.26); stroke-width:9; stroke-linecap:round; stroke-dasharray:289; stroke-dashoffset:289; }
        .bar { fill:none; stroke:url(#rg); stroke-width:3; stroke-linecap:round; stroke-dasharray:289; stroke-dashoffset:289; }
        .dash { position:absolute; inset:-5px; border-radius:50%; border:1px dashed rgba(242,196,109,.5); animation: spin 22s linear infinite reverse; }
        .pre.is-ready .dash { animation:none; }
        .orbit { position:absolute; inset:-2px; animation: spin 3.4s linear infinite; }
        .orbit::after { content:''; position:absolute; top:0; left:50%; width:7px; height:7px; margin-left:-3.5px; border-radius:50%; background:#fff; box-shadow:0 0 12px 3px rgba(110,231,183,.9); }
        .badge .disc { width:7.4rem; height:7.4rem; padding:.45rem; animation: breathe 2.8s ease-in-out infinite;
                       box-shadow:0 0 0 2px rgba(242,196,109,.55), 0 0 34px rgba(16,185,129,.5); }
        .disc { display:grid; place-items:center; border-radius:50%; background:#fff; }
        .disc img { width:100%; height:100%; object-fit:contain; border-radius:50%; display:block; }
        @keyframes breathe { 50% { transform:scale(1.035); } }
        .shock { position:absolute; inset:0; border-radius:50%; border:2px solid var(--g-200); opacity:0; }
        .pre.is-ready .shock { animation: shock 1.1s ease-out forwards; }
        .pre.is-ready .shock.s2 { animation-delay:.22s; }
        @keyframes shock { from { transform:scale(1); opacity:.75; } to { transform:scale(3.2); opacity:0; } }
        .lock { position:absolute; right:-2px; bottom:-2px; width:2.4rem; height:2.4rem; display:grid; place-items:center; border-radius:50%; color:var(--gold);
                background:#06161a; border:1px solid var(--gold-dim); box-shadow:0 0 16px rgba(242,196,109,.25); transition: color .5s, box-shadow .5s, border-color .5s; }
        .lock svg { width:1.05rem; height:1.05rem; position:static; transform:none; overflow:visible; }
        .shackle { transform-origin:16px 11px; transition: transform .7s var(--out); }
        .pre.is-ready .lock { color:var(--g-200); border-color:rgba(110,231,183,.6); box-shadow:0 0 22px rgba(52,211,153,.7); }
        .pre.is-ready .shackle { transform: translateY(-2px) rotate(-26deg); }

        .pre-name { margin-top:1.5rem; font-size:1.8rem; font-weight:800; }
        .pre-sub { margin-top:.3rem; padding-left:.35em; font-family:ui-monospace,Menlo,monospace; font-size:.62rem; letter-spacing:.35em; color:var(--g-200); }
        .pre-meta { margin-top:1.1rem; display:flex; gap:.5rem; align-items:center; justify-content:center; font-family:ui-monospace,Menlo,monospace; font-size:.72rem; color:rgba(167,243,208,.85); }
        .pre-meta .dot { width:6px; height:6px; border-radius:50%; background:var(--g-200); animation: pdot 1.4s ease-in-out infinite; }
        .pre-meta .dot:nth-child(2) { animation-delay:.2s; } .pre-meta .dot:nth-child(3) { animation-delay:.4s; }
        @keyframes pdot { 0%,100% { opacity:.3; transform:scale(.8); } 50% { opacity:1; transform:scale(1.2); } }
        .pre-meta span.s { min-width:8rem; text-align:left; }
        .pre-meta b { min-width:2.4rem; text-align:right; font-weight:500; color:rgba(255,255,255,.75); font-variant-numeric:tabular-nums; }

        .skip { position:absolute; left:50%; bottom:max(1.2rem, env(safe-area-inset-bottom)); transform:translateX(-50%); padding:.5rem 1.1rem; border-radius:999px; font-size:.72rem;
                color:rgba(255,255,255,.55); border:1px solid rgba(255,255,255,.12); background:rgba(255,255,255,.04); transition: color .25s, border-color .25s, opacity .4s; }
        .skip:hover { color:#fff; border-color:rgba(255,255,255,.35); }
        .pre.is-ready .skip, .pre.is-out .skip { opacity:0; pointer-events:none; }
        @media (max-height:680px) { .badge { width:8.4rem; height:8.4rem; } .badge .disc { width:6.2rem; height:6.2rem; } .pre-name { margin-top:1rem; font-size:1.5rem; } }

        /* ============ SITE ============ */
        .site { position:relative; z-index:1; min-height:100vh; min-height:100dvh; display:flex; flex-direction:column; }
        .wrap { width:100%; max-width:80rem; margin:0 auto; padding-inline:1.25rem; }

        header.top { position:sticky; top:0; z-index:20; padding-top:max(.4rem, env(safe-area-inset-top)); transition: background .4s ease, box-shadow .4s ease; }
        header.top.scrolled { background:rgba(4,37,28,.8); -webkit-backdrop-filter:blur(14px); backdrop-filter:blur(14px); box-shadow:0 1px 0 rgba(255,255,255,.08); }
        header.top .wrap { display:flex; align-items:center; justify-content:space-between; gap:1rem; height:4.25rem; }
        .brand { display:flex; align-items:center; gap:.75rem; }
        .brand img { width:2.8rem; height:2.8rem; border-radius:50%; object-fit:cover; background:#fff; box-shadow:0 0 0 2px rgba(52,211,153,.35); }
        .brand strong { display:block; font-family:'Bricolage Grotesque',sans-serif; font-size:1.15rem; font-weight:800; line-height:1.1; }
        .brand small { display:block; font-size:.72rem; color:var(--g-200); opacity:.8; }
        nav.links { display:none; gap:2rem; }
        nav.links a { font-size:.9rem; color:rgba(255,255,255,.7); transition:color .2s; }
        nav.links a:hover { color:#fff; }
        @media (min-width:900px) { nav.links { display:flex; } }

        .btn { display:inline-flex; align-items:center; justify-content:center; gap:.65rem; height:3.3rem; padding:0 1.8rem; border-radius:1rem;
               font-weight:700; font-size:.95rem; color:#fff; background:linear-gradient(135deg,var(--g-500),var(--g-800));
               box-shadow:0 14px 30px -12px rgba(16,185,129,.75), inset 0 1px 0 rgba(255,255,255,.25);
               transition: transform .35s var(--out), box-shadow .35s var(--out), filter .3s; position:relative; overflow:hidden; }
        .btn svg { width:1rem; height:1rem; transition: transform .35s var(--out); }
        .btn:hover { transform:translateY(-3px); filter:brightness(1.07); box-shadow:0 20px 34px -12px rgba(16,185,129,.9), inset 0 1px 0 rgba(255,255,255,.3); }
        .btn:hover svg { transform:translateX(4px); }
        .btn:active { transform:scale(.98); }
        .btn.ghost { background:rgba(255,255,255,.06); box-shadow:inset 0 0 0 1px rgba(255,255,255,.16); }
        .btn.ghost:hover { background:rgba(255,255,255,.11); box-shadow:inset 0 0 0 1px rgba(255,255,255,.3); }
        .btn.sm { height:2.6rem; padding:0 1.2rem; font-size:.85rem; border-radius:.8rem; }

        /* hero */
        .hero { flex:1; display:grid; align-items:center; gap:2.5rem; padding-block:2.5rem 3rem; }
        @media (min-width:960px) { .hero { grid-template-columns: 1.1fr .9fr; gap:4rem; padding-block:3rem 4rem; } }

        .greet { display:inline-flex; align-items:center; gap:.55rem; padding:.45rem .95rem; border-radius:999px; font-size:.82rem; font-weight:600;
                 color:var(--g-200); background:rgba(16,185,129,.14); border:1px solid rgba(52,211,153,.32); }
        .greet i { width:.5rem; height:.5rem; border-radius:50%; background:var(--g-400); box-shadow:0 0 0 0 rgba(52,211,153,.6); animation: ping 2.2s ease-out infinite; }
        @keyframes ping { to { box-shadow:0 0 0 .6rem rgba(52,211,153,0); } }

        h1 { margin-top:1.4rem; font-size:clamp(2.6rem, 7.2vw, 5.2rem); font-weight:800; line-height:1.02; }
        h1 .ln { display:block; overflow:hidden; padding-bottom:.08em; margin-bottom:-.08em; }
        h1 .ln > span { display:block; transform:translate3d(0,110%,0); transition: transform 1.1s var(--out); transition-delay: calc(.45s + var(--i) * .12s); }
        h1 .g { color:var(--g-400); }
        body.is-open h1 .ln > span { transform:none; }

        .lead { margin-top:1.4rem; max-width:34rem; font-size:1.12rem; line-height:1.65; color:rgba(209,250,229,.78); }
        .cta-row { margin-top:2rem; display:flex; flex-wrap:wrap; gap:.8rem; }
        .secure { margin-top:1.3rem; display:flex; align-items:center; gap:.5rem; font-size:.8rem; color:rgba(167,243,208,.7); }
        .secure svg { width:.95rem; height:.95rem; color:var(--g-400); }

        .rv { opacity:0; transform:translate3d(0,22px,0); transition: opacity 1s var(--out), transform 1s var(--out); transition-delay: calc(.5s + var(--d, 0) * .1s); }
        body.is-open .rv { opacity:1; transform:none; }

        /* emblem composition */
        .emblem { position:relative; width:min(100%, 26rem); aspect-ratio:1; margin-inline:auto; display:grid; place-items:center; }
        .ring { position:absolute; border-radius:50%; will-change:transform; }
        .ring.r1 { inset:0; border:1px solid rgba(52,211,153,.22); }
        .ring.r2 { inset:9%; border:1px dashed rgba(52,211,153,.35); animation: spin 60s linear infinite; }
        .ring.r2::after { content:''; position:absolute; top:-5px; left:50%; width:10px; height:10px; margin-left:-5px; border-radius:50%;
                          background:var(--g-400); box-shadow:0 0 18px 4px rgba(52,211,153,.7); }
        .ring.r3 { inset:20%; background:radial-gradient(circle, rgba(16,185,129,.28), transparent 70%); animation: breathe 4s ease-in-out infinite; }
        @keyframes spin { to { transform:rotate(360deg); } }
        .emblem .disc { position:relative; width:46%; height:auto; aspect-ratio:1; padding:.6rem; animation:none;
                        box-shadow:0 0 0 6px rgba(16,185,129,.2), 0 30px 60px -18px rgba(0,0,0,.7); }

        .chip { position:absolute; display:flex; align-items:center; gap:.6rem; padding:.6rem .95rem .6rem .65rem; border-radius:1rem; font-size:.82rem; font-weight:600;
                color:var(--g-900); background:#fff; box-shadow:0 18px 40px -16px rgba(0,0,0,.65); will-change:transform; animation: float 6s ease-in-out infinite; }
        .chip i { width:2rem; height:2rem; display:grid; place-items:center; border-radius:.65rem; background:var(--g-50); color:var(--g-600); font-style:normal; }
        .chip i svg { width:1rem; height:1rem; }
        .chip.c1 { top:6%; left:-2%; }
        .chip.c2 { top:44%; right:-6%; animation-delay:-2s; }
        .chip.c3 { bottom:8%; left:6%; animation-delay:-4s; }
        @keyframes float { 50% { transform:translate3d(0,-10px,0); } }

        /* developers */
        .devs { border-top:1px solid rgba(255,255,255,.09); padding-block:1.6rem calc(1.4rem + env(safe-area-inset-bottom)); }
        .devs .wrap { display:flex; flex-direction:column; gap:1rem; align-items:center; text-align:center; }
        @media (min-width:960px) { .devs .wrap { flex-direction:row; justify-content:space-between; text-align:left; } }
        .devs p { font-size:.82rem; color:var(--g-200); font-weight:600; white-space:nowrap; }
        .dev-list { display:flex; flex-wrap:wrap; justify-content:center; gap:.5rem; }
        .dev { display:flex; align-items:center; gap:.5rem; padding:.25rem .8rem .25rem .25rem; border-radius:999px; font-size:.78rem; color:rgba(255,255,255,.8);
               background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.09); transition: background .25s, border-color .25s, transform .35s var(--out); }
        .dev:hover { background:rgba(16,185,129,.16); border-color:rgba(52,211,153,.45); transform:translateY(-2px); }
        .dev b { width:1.6rem; height:1.6rem; display:grid; place-items:center; border-radius:50%; font-size:.62rem; font-weight:800; color:var(--g-950);
                 background:linear-gradient(135deg,var(--g-200),var(--g-500)); }
        .thesis { text-align:center; font-size:.7rem; color:rgba(255,255,255,.35); padding-bottom:1rem; }


        /* cursor spotlight (transform only) */
        .spot { position:absolute; left:0; top:0; width:38rem; height:38rem; margin:-19rem 0 0 -19rem; border-radius:50%; opacity:0; transition:opacity 1.2s ease;
                background:radial-gradient(circle, rgba(52,211,153,.14), transparent 65%); will-change:transform; }
        body.is-open .spot { opacity:1; }
        @media (hover:none) { .spot { display:none; } }

        /* scroll reveal */
        .sr { opacity:0; transform:translate3d(0,30px,0); transition: opacity 1s var(--out), transform 1s var(--out); transition-delay: calc(var(--d,0) * .1s); }
        .sr.in { opacity:1; transform:none; }

        /* section heads */
        .sec-head { max-width:40rem; }
        .sec-head h2 { font-size:clamp(1.9rem, 4.2vw, 3rem); font-weight:800; line-height:1.08; }
        .sec-head h2 em { font-style:normal; color:var(--g-400); }
        .sec-head p { margin-top:.9rem; color:rgba(209,250,229,.7); line-height:1.65; }

        /* officials marquee */
        .officials { padding-block:4.5rem 2.5rem; }
        .marquee { margin-top:2.4rem; overflow:hidden; -webkit-mask-image:linear-gradient(90deg,transparent,#000 7%,#000 93%,transparent); mask-image:linear-gradient(90deg,transparent,#000 7%,#000 93%,transparent); }
        .track { display:flex; width:max-content; animation: marquee 60s linear infinite; will-change:transform; }
        .marquee:hover .track { animation-play-state:paused; }
        @keyframes marquee { to { transform:translate3d(-50%,0,0); } }
        .off { position:relative; flex:none; width:12.5rem; aspect-ratio:3/4; margin-right:1rem; border-radius:1.4rem; overflow:hidden;
               background:linear-gradient(160deg,var(--g-800),var(--g-950)); border:1px solid rgba(52,211,153,.22);
               transition: transform .5s var(--out), border-color .3s; }
        .off:hover { transform:translateY(-8px); border-color:rgba(52,211,153,.7); }
        .off b { position:absolute; inset:0; display:grid; place-items:center; font:800 2.8rem 'Bricolage Grotesque',sans-serif; color:rgba(167,243,208,.4); }
        .off img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }
        .off figcaption { position:absolute; left:0; right:0; bottom:0; padding:3rem .95rem .9rem; font-weight:600; font-size:.92rem; background:linear-gradient(transparent, rgba(4,37,28,.94)); }
        .off figcaption small { display:block; margin-top:.15rem; font-size:.7rem; font-weight:500; color:var(--g-400); }

        /* steps */
        .steps { padding-block:3rem 4.5rem; }
        .step-grid { margin-top:2.4rem; display:grid; gap:1rem; }
        @media (min-width:800px) { .step-grid { grid-template-columns:repeat(3,1fr); } }
        .step { position:relative; padding:1.7rem; border-radius:1.5rem; background:rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.09);
                transition: transform .5s var(--out), border-color .3s, background .3s; }
        .step:hover { transform:translateY(-6px); border-color:rgba(52,211,153,.5); background:rgba(16,185,129,.09); }
        .step .n { width:2.7rem; height:2.7rem; display:grid; place-items:center; border-radius:50%; font:800 1.05rem 'Bricolage Grotesque',sans-serif;
                   background:linear-gradient(135deg,var(--g-500),var(--g-800)); box-shadow:0 10px 22px -8px rgba(16,185,129,.8); }
        .step h3 { margin-top:1.2rem; font-family:'Bricolage Grotesque',sans-serif; font-size:1.3rem; font-weight:700; }
        .step p { margin-top:.55rem; font-size:.93rem; line-height:1.65; color:rgba(209,250,229,.7); }
        .cta-band { margin-top:2.2rem; display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:1.2rem; padding:1.6rem 1.8rem; border-radius:1.6rem;
                    background:linear-gradient(135deg, rgba(16,185,129,.22), rgba(6,95,70,.35)); border:1px solid rgba(52,211,153,.3); }
        .cta-band h3 { font-family:'Bricolage Grotesque',sans-serif; font-size:1.4rem; font-weight:700; }
        .cta-band p { margin-top:.25rem; font-size:.92rem; color:rgba(209,250,229,.75); }

        @media (max-width:480px) { .chip { font-size:.72rem; } .chip.c2 { right:-2%; } .chip.c1 { left:0; } }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation:none !important; transition-duration:.01s !important; transition-delay:0s !important; }
        }
    </style>
    <noscript><style>.pre{display:none}.rv,.sr{opacity:1;transform:none}h1 .ln>span{transform:none}body{overflow:auto}</style></noscript>
</head>
<body class="is-loading">

    <div class="bg" aria-hidden="true"><div class="blob a"></div><div class="blob b"></div><div class="spot" id="spot"></div></div>

    {{-- ================= PRELOADER: the door ================= --}}
    <div class="pre is-on" id="pre" role="status" aria-live="polite">
        @foreach(['l', 'r'] as $side)
            <div class="door {{ $side }}" aria-hidden="true">
                <div class="panels"><div class="pnl"></div><div class="pnl"></div></div>
                <span class="hinge h1"></span><span class="hinge h2"></span><span class="hinge h3"></span>
                <span class="handle"></span><span class="sheen"></span>
            </div>
        @endforeach
        <div class="frame" aria-hidden="true"></div>
        <div class="beam" aria-hidden="true"></div>
        <div class="dust" id="dust" aria-hidden="true"></div>

        <div class="core">
            <div>
                <div class="badge">
                    <span class="shock"></span><span class="shock s2"></span>
                    <span class="dash" aria-hidden="true"></span>
                    <svg viewBox="0 0 100 100" aria-hidden="true">
                        <defs><linearGradient id="rg" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#10b981"/><stop offset="100%" stop-color="#34d399"/></linearGradient></defs>
                        <circle class="trk" cx="50" cy="50" r="46"/>
                        <circle class="glw" id="glw" cx="50" cy="50" r="46"/>
                        <circle class="bar" id="bar" cx="50" cy="50" r="46"/>
                    </svg>
                    <span class="orbit" aria-hidden="true"></span>
                    <div class="disc"><img src="{{ asset('images/brgy_logo.png.jpg') }}" alt="Barangay Bactad East logo"></div>
                    <span class="lock" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="5" y="11" width="14" height="10" rx="2.5" fill="currentColor" fill-opacity=".18"/>
                            <path class="shackle" d="M8 11V8a4 4 0 0 1 8 0v3"/>
                        </svg>
                    </span>
                </div>
                <h2 class="pre-name display">BIMS</h2>
                <p class="pre-sub">BARANGAY BACTAD EAST</p>
                <div class="pre-meta"><i class="dot"></i><i class="dot"></i><i class="dot"></i><span class="s" id="status">Starting up…</span><b id="pct">0%</b></div>
            </div>
        </div>
        <button type="button" class="skip" id="skip">Tap to skip</button>
    </div>

    {{-- ================= SITE ================= --}}
    <div class="site">
        <header class="top">
            <div class="wrap">
                <a href="{{ url('/') }}" class="brand rv" style="--d:0">
                    <img src="{{ asset('images/brgy_logo.png.jpg') }}" alt="Barangay Bactad East logo">
                    <span><strong>BIMS</strong><small>Barangay Bactad East</small></span>
                </a>
                <nav class="links rv" style="--d:1" aria-label="Main">
                    <a href="#officials">Officials</a>
                    <a href="#how">How it works</a>
                    <a href="#contact">Contact</a>
                </nav>
                <a href="{{ route('landing') }}" class="btn sm rv" style="--d:2">Enter portal</a>
            </div>
        </header>

        <main class="wrap hero">
            <div>
                <span class="greet rv" style="--d:0"><i></i> Maligayang pagdating</span>

                <h1>
                    <span class="ln" style="--i:0"><span style="--i:0">Your barangay,</span></span>
                    <span class="ln"><span style="--i:1" class="g">now online.</span></span>
                </h1>

                <p class="lead rv" style="--d:3">
                    The Barangay Information Management System of Bactad East. Request documents, keep household records, and read official announcements without waiting in line.
                </p>

                <div class="cta-row rv" style="--d:4">
                    <a href="{{ route('landing') }}" class="btn" id="cta">
                        <span>Get Started</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                    <a href="#officials" class="btn ghost">Meet the officials</a>
                </div>

                <p class="secure rv" style="--d:5">
                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2 4 5v6c0 5 3.4 9.4 8 11 4.6-1.6 8-6 8-11V5l-8-3Zm-1.2 14.2-3.5-3.5 1.4-1.4 2.1 2.1 4.6-4.6 1.4 1.4-6 6Z"/></svg>
                    Secure and verified access
                </p>
            </div>

            <div class="rv" style="--d:2" aria-hidden="true"><div class="emblem" id="emblem">
                <span class="ring r1"></span><span class="ring r2"></span><span class="ring r3"></span>
                <div class="disc"><img src="{{ asset('images/brgy_logo.png.jpg') }}" alt=""></div>

                <div class="chip c1"><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5M9 13h6M9 17h4"/></svg></i>Document requests</div>
                <div class="chip c2"><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12h4l3-8 4 16 3-8h4"/></svg></i>Health records</div>
                <div class="chip c3"><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11v2a2 2 0 0 0 2 2h2l5 4V5L7 9H5a2 2 0 0 0-2 2z"/><path d="M16 9a4 4 0 0 1 0 6"/></svg></i>Announcements</div>
            </div></div>
        </main>

        {{-- ================= OFFICIALS ================= --}}
        <section class="officials" id="officials">
            <div class="wrap sec-head sr">
                <h2>The people serving <em>Bactad East</em></h2>
                <p>Meet the barangay officials behind every request you file and every record you keep.</p>
            </div>
            <div class="marquee sr" style="--d:2">
                <div class="track">
                    @foreach([1, 2] as $copy)
                        @foreach($officials as $slug)
                            @php $nm = \Illuminate\Support\Str::title(str_replace('-', ' ', $slug)); @endphp
                            <figure class="off" @if($copy === 2) aria-hidden="true" @endif>
                                <b>{{ $initials($nm) }}</b>
                                <img src="{{ asset('images/officials/' . $slug . '.jpg') }}" alt="{{ $copy === 1 ? $nm : '' }}" loading="lazy" onerror="this.remove()">
                                <figcaption>{{ $nm }}</figcaption>
                            </figure>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ================= STEPS ================= --}}
        <section class="steps wrap" id="how">
            <div class="sec-head sr">
                <h2>Your first request, <em>in three steps</em></h2>
                <p>Create an account once. After the barangay verifies it, every service is a few taps away.</p>
            </div>
            <div class="step-grid">
                <article class="step sr" style="--d:1"><div class="n">1</div><h3>Register</h3><p>Fill in your details and upload a valid ID so the barangay can confirm that you live here.</p></article>
                <article class="step sr" style="--d:2"><div class="n">2</div><h3>Get verified</h3><p>Barangay staff review your account. You can check the status anytime from the login page.</p></article>
                <article class="step sr" style="--d:3"><div class="n">3</div><h3>Request and track</h3><p>Choose a service, submit it, and follow the request until it is ready for release.</p></article>
            </div>
            <div class="cta-band sr" style="--d:2">
                <div><h3>Ready to get started?</h3><p>Sign in or create your resident account.</p></div>
                <a href="{{ route('landing') }}" class="btn">
                    <span>Get Started</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </a>
            </div>
        </section>

        <section class="devs sr" aria-label="Developers">
            <div class="wrap">
                <p>Developed by</p>
                <div class="dev-list">
                    @foreach($developers as $name)
                        <span class="dev"><b>{{ $initials($name) }}</b>{{ $name }}</span>
                    @endforeach
                </div>
            </div>
            <p class="thesis" id="contact" style="margin-top:1rem">Thesis Project · Digital Barangay Governance 2025</p>
        </section>
    </div>

<script>
(() => {
    'use strict';
    const $ = id => document.getElementById(id);
    const body = document.body, pre = $('pre');
    const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;

    const bar = $('bar'), pct = $('pct'), status = $('status');
    const C = 2 * Math.PI * 46;   // 289
    const steps = [[0, 'Starting up…'], [30, 'Loading portal…'], [60, 'Preparing services…'], [90, 'Almost ready…']];

    let seen = false;
    try { seen = sessionStorage.getItem('bims_splash_seen') === '1'; } catch (e) {}
    const DURATION = reduce ? 200 : (seen ? 1100 : 2800);

    let loaded = document.readyState === 'complete', skipped = false, done = false, lastP = -1;
    addEventListener('load', () => { loaded = true; });

    const t0 = performance.now();
    const ease = t => 1 - Math.pow(1 - t, 3);

    function tick(now) {
        let p = ease(Math.min((now - t0) / DURATION, 1)) * 100;
        if (skipped) p = 100;
        if (!loaded && !skipped) p = Math.min(p, 94);
        const w = Math.floor(p);
        if (w !== lastP) {
            lastP = w;
            bar.style.strokeDashoffset = $('glw').style.strokeDashoffset = C - (w / 100) * C;
            pct.textContent = w + '%';
            for (let i = steps.length - 1; i >= 0; i--) if (w >= steps[i][0]) { status.textContent = steps[i][1]; break; }
        }
        if (p >= 100 && !done) { done = true; open(); return; }
        requestAnimationFrame(tick);
    }

    function dust() {
        if (reduce) return;
        const w = $('dust'), n = matchMedia('(max-width:767px)').matches ? 14 : 26;
        for (let i = 0; i < n; i++) {
            const d = document.createElement('i'), z = Math.random() * 2.6 + 1.2;
            d.style.cssText = `left:${Math.random() * 100}%;width:${z}px;height:${z}px;--dx:${Math.random() * 30 - 15}px;animation-duration:${Math.random() * .9 + .9}s;animation-delay:${Math.random() * .5}s`;
            w.appendChild(d);
        }
        setTimeout(() => { w.textContent = ''; }, 2800);
    }

    function open() {
        bar.style.strokeDashoffset = 0; $('glw').style.strokeDashoffset = 0;
        status.textContent = 'Ready!';
        const k = reduce ? 0.1 : 1;
        setTimeout(() => pre.classList.add('is-ready'), 150 * k);                        // unlock: beam, shockwave, lock opens
        setTimeout(() => { pre.classList.add('is-unlocking'); dust(); }, 800 * k);       // door rumbles, dust falls
        setTimeout(() => {
            pre.classList.add('is-out');                                                 // doors swing open
            setTimeout(() => body.classList.add('is-open'), 750 * k);                    // site rises in behind them
            setTimeout(() => {
                pre.classList.remove('is-on'); pre.classList.add('is-gone');
                body.classList.remove('is-loading');
                const cta = $('cta'); if (cta) cta.focus({ preventScroll: true });
            }, 2500 * k);
            try { sessionStorage.setItem('bims_splash_seen', '1'); } catch (e) {}
        }, 1350 * k);
    }

    const skip = () => { skipped = true; };
    $('skip').addEventListener('click', skip);
    addEventListener('keydown', e => { if (!done && (e.key === 'Escape' || e.key === 'Enter' || e.key === ' ')) skip(); });

    /* gentle pointer parallax on the emblem (desktop only, transform only) */
    if (!reduce && matchMedia('(hover:hover) and (pointer:fine)').matches) {
        const em = $('emblem');
        let tx = 0, ty = 0, cx = 0, cy = 0, run = false;
        const loop = () => {
            cx += (tx - cx) * .07; cy += (ty - cy) * .07;
            em.style.transform = `translate3d(${cx * 14}px, ${cy * 14}px, 0)`;
            if (Math.abs(tx - cx) > .002 || Math.abs(ty - cy) > .002) requestAnimationFrame(loop); else run = false;
        };
        addEventListener('pointermove', e => {
            if (!body.classList.contains('is-open')) return;
            tx = e.clientX / innerWidth - .5; ty = e.clientY / innerHeight - .5;
            if (!run) { run = true; requestAnimationFrame(loop); }
        }, { passive: true });
    }

    /* sticky header state + scroll reveal */
    const head = document.querySelector('header.top');
    const onScroll = () => head.classList.toggle('scrolled', scrollY > 24);
    addEventListener('scroll', onScroll, { passive: true }); onScroll();

    const sr = document.querySelectorAll('.sr');
    if ('IntersectionObserver' in window) {
        const io = new IntersectionObserver(es => es.forEach(e => { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } }), { threshold: .15, rootMargin: '0px 0px -6% 0px' });
        sr.forEach(el => io.observe(el));
    } else sr.forEach(el => el.classList.add('in'));

    /* cursor spotlight (desktop, transform only) */
    if (!reduce && matchMedia('(hover:hover) and (pointer:fine)').matches) {
        const sp = $('spot'); let sx = innerWidth / 2, sy = innerHeight / 3, px = sx, py = sy, go = false;
        const lp = () => {
            px += (sx - px) * .08; py += (sy - py) * .08;
            sp.style.transform = `translate3d(${px}px, ${py}px, 0)`;
            if (Math.abs(sx - px) > .5 || Math.abs(sy - py) > .5) requestAnimationFrame(lp); else go = false;
        };
        addEventListener('pointermove', e => { sx = e.clientX; sy = e.clientY; if (!go) { go = true; requestAnimationFrame(lp); } }, { passive: true });
    }

    requestAnimationFrame(tick);
})();
</script>
</body>
</html>