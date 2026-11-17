{{-- ============================================================
     RESIDENT UI — shared design system (prefix: rd-)
     I-save bilang: resources/views/partials/resident-ui.blade.php
     Gamit: @include('partials.resident-ui')
============================================================ --}}
<style>
    .rd {
        --ink:    #0f172a;
        --muted:  #64748b;
        --faint:  #94a3b8;
        --line:   #e6eeec;
        --em-50:  #ecfdf5;
        --em-100: #d1fae5;
        --em-200: #a7f3d0;
        --em-500: #10b981;
        --em-600: #059669;
        --em-700: #047857;
        --gold:   #f2c46d;
        --rose:   #e11d48;

        max-width: var(--rd-w, 1100px);
        margin: 0 auto;
        padding: 14px 12px 40px;
        color: var(--ink);
    }
    .rd *, .rd *::before, .rd *::after { box-sizing: border-box; }
    .rd p, .rd h1, .rd h2, .rd h3, .rd h4 { margin: 0; }
    .rd a { text-decoration: none; color: inherit; }
    .rd-stack > * + * { margin-top: 14px; }
    .rd-vh { position: absolute; width: 1px; height: 1px; margin: -1px; padding: 0; overflow: hidden; clip: rect(0 0 0 0); border: 0; }
    [x-cloak] { display: none !important; }
    @media (min-width: 640px) { .rd { padding: 28px 20px 56px; } }

    .rd-rise { animation: rd-rise .7s cubic-bezier(.22, 1, .36, 1) both; animation-delay: calc(var(--d, 0) * 70ms); }
    @keyframes rd-rise { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }
    @media (prefers-reduced-motion: reduce) { .rd-rise { animation: none; } }

    /* ============================================================
       HERO
    ============================================================ */
    .rd-hero {
        position: relative;
        overflow: hidden;
        padding: 16px;
        border-radius: 28px;
        color: #fff;
        background:
            radial-gradient(120% 90% at 100% 0%, rgba(110, 231, 183, .28), transparent 55%),
            linear-gradient(145deg, #064e3b 0%, #065f46 46%, #0b4f4a 100%);
        box-shadow: 0 24px 44px -26px rgba(6, 78, 59, .85);
    }
    .rd-hero::before {
        content: '';
        position: absolute; right: -76px; top: -76px;
        width: 230px; height: 230px;
        border-radius: 50%;
        border: 1px solid rgba(255, 255, 255, .14);
        box-shadow: 0 0 0 30px rgba(255, 255, 255, .035), 0 0 0 60px rgba(255, 255, 255, .025);
        pointer-events: none;
    }
    .rd-hero::after {
        content: '';
        position: absolute; left: -50px; bottom: -70px;
        width: 180px; height: 180px;
        border-radius: 50%;
        border: 1px dashed rgba(242, 196, 109, .38);
        pointer-events: none;
    }
    .rd-hero > * { position: relative; z-index: 1; }
    @media (min-width: 640px) { .rd-hero { padding: 26px 28px; border-radius: 32px; } }

    .rd-hero-row { display: flex; flex-direction: column; gap: 14px; }
    @media (min-width: 640px) { .rd-hero-row { flex-direction: row; align-items: center; justify-content: space-between; gap: 18px; } }
    .rd-id { display: flex; align-items: center; gap: 14px; min-width: 0; }

    .rd-badge {
        flex: none;
        width: 52px; height: 52px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 18px;
        font-size: 20px;
        color: #064e3b;
        background: linear-gradient(135deg, #fde7b0, var(--gold));
        box-shadow: 0 12px 22px -10px rgba(0, 0, 0, .55), inset 0 1px 0 rgba(255, 255, 255, .6);
    }
    .rd-eyebrow {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 9.5px; font-weight: 800;
        letter-spacing: .2em; text-transform: uppercase;
        color: var(--em-200);
    }
    .rd-eyebrow i { width: 6px; height: 6px; border-radius: 50%; background: #6ee7b7; box-shadow: 0 0 0 3px rgba(110, 231, 183, .25); }
    .rd-title { margin-top: 4px !important; font-size: 21px; font-weight: 900; letter-spacing: -.02em; line-height: 1.12; }
    .rd-sub   { margin-top: 5px !important; font-size: 12px; line-height: 1.45; color: #a7f3d0; }
    @media (min-width: 640px) {
        .rd-badge { width: 60px; height: 60px; font-size: 24px; }
        .rd-title { font-size: 29px; }
        .rd-sub   { font-size: 13.5px; }
    }

    .rd-cta {
        position: relative;
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        flex: none;
        width: fit-content;
        padding: 11px 20px;
        border: 0; border-radius: 999px;
        font: inherit; font-size: 12.5px; font-weight: 800;
        color: #065f46 !important;
        background: #fff;
        cursor: pointer;
        box-shadow: 0 14px 24px -12px rgba(0, 0, 0, .5);
        transition: transform .25s, box-shadow .25s;
    }
    .rd-cta i { font-size: 11px; transition: transform .3s; }
    .rd-cta:hover { transform: translateY(-2px); box-shadow: 0 18px 28px -12px rgba(0, 0, 0, .55); }
    .rd-cta:hover i.spin { transform: rotate(90deg); }
    .rd-cta:active { transform: scale(.97); }
    .rd-cta:focus-visible { outline: 3px solid var(--gold); outline-offset: 3px; }
    .rd-cta .count {
        position: absolute; top: -7px; right: -5px;
        min-width: 20px; height: 20px; padding: 0 5px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 999px;
        font-size: 10px; font-weight: 900;
        color: #fff; background: #f59e0b;
        box-shadow: 0 6px 12px -4px rgba(0, 0, 0, .5);
    }

    .rd-tags { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 14px; }
    .rd-tag {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 5px 11px;
        border-radius: 999px;
        font-size: 9.5px; font-weight: 800;
        letter-spacing: .08em; text-transform: uppercase;
        background: rgba(255, 255, 255, .1);
        border: 1px solid rgba(255, 255, 255, .16);
        color: #f0fdf9;
    }
    .rd-tag i { font-size: 9px; color: #6ee7b7; }

    .rd-glass {
        display: flex; align-items: center; gap: 11px;
        width: fit-content;
        padding: 9px 14px;
        border-radius: 18px;
        background: rgba(0, 0, 0, .2);
        border: 1px solid rgba(255, 255, 255, .1);
    }
    .rd-glass > i {
        width: 34px; height: 34px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 12px; font-size: 13px;
        color: #064e3b; background: linear-gradient(135deg, #fde7b0, var(--gold));
    }
    .rd-glass small { display: block; font-size: 8.5px; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; color: var(--em-200); }
    .rd-glass b { display: block; margin-top: 2px; font-size: 13px; font-weight: 800; }

    /* ============================================================
       STAT CARDS
    ============================================================ */
    .rd-stats { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
    @media (min-width: 640px)  { .rd-stats { gap: 14px; } }
    @media (min-width: 1024px) { .rd-stats { grid-template-columns: repeat(4, minmax(0, 1fr)); } }

    .rd-stat {
        position: relative;
        display: flex; align-items: center; gap: 12px;
        overflow: hidden;
        padding: 14px 14px 14px 18px;
        border-radius: 22px;
        background: #fff;
        border: 1px solid var(--line);
        box-shadow: 0 14px 28px -24px rgba(15, 23, 42, .4);
        transition: transform .3s, box-shadow .3s;
    }
    .rd-stat::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: linear-gradient(var(--c1), var(--c2)); }
    @media (hover: hover) { .rd-stat:hover { transform: translateY(-3px); box-shadow: 0 22px 34px -22px var(--c1); } }
    .rd-stat-ico {
        flex: none;
        width: 44px; height: 44px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 15px;
        font-size: 16px; color: #fff;
        background: linear-gradient(135deg, var(--c1), var(--c2));
        box-shadow: 0 10px 18px -8px var(--c1);
    }
    .rd-stat-lbl { font-size: 9.5px; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; color: var(--faint); }
    .rd-stat-num { margin-top: 2px !important; font-size: 28px; font-weight: 900; letter-spacing: -.02em; line-height: 1.05; }
    .rd-stat-chip {
        position: absolute; right: 10px; bottom: 10px;
        display: inline-flex; align-items: center; gap: 4px;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 8.5px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase;
        color: var(--c2);
        background: #f1f5f9;
        background: color-mix(in srgb, var(--c1) 14%, #fff);
    }
    .rd-stat-chip i { font-size: 8px; }

    /* ============================================================
       CARDS / SECTION HEADERS
    ============================================================ */
    .rd-card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 26px;
        box-shadow: 0 14px 30px -24px rgba(15, 23, 42, .35);
        overflow: hidden;
    }
    .rd-pad { padding: 16px; }
    @media (min-width: 640px) { .rd-pad { padding: 22px; } }

    .rd-sec { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
    .rd-ico {
        flex: none;
        width: 38px; height: 38px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 13px;
        font-size: 14px; color: #fff;
        background: linear-gradient(135deg, var(--c1, #10b981), var(--c2, #047857));
        box-shadow: 0 10px 18px -8px var(--c1, #10b981);
    }
    .rd-sec h3, .rd-sec h2 { font-size: 15px; font-weight: 900; letter-spacing: -.01em; }
    .rd-sec p { margin-top: 2px !important; font-size: 10px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--faint); }

    .rd-sechead {
        display: flex; align-items: center; justify-content: space-between; gap: 12px;
        padding: 16px;
        border-bottom: 1px solid #eef3f2;
    }
    .rd-sechead .rd-sec { margin: 0; }
    @media (min-width: 640px) { .rd-sechead { padding: 18px 22px; } }
    .rd-more {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 10.5px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase;
        color: var(--em-600) !important;
        white-space: nowrap;
    }
    .rd-more i { font-size: 9px; transition: transform .25s; }
    .rd-more:hover i { transform: translateX(3px); }

    /* ============================================================
       STATUS PILL
    ============================================================ */
    .rd-pill {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 9.5px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase;
        white-space: nowrap;
        color: var(--c2, #047857);
        background: #f1f5f9;
        background: color-mix(in srgb, var(--c1, #10b981) 13%, #fff);
        border: 1px solid #e2e8f0;
        border-color: color-mix(in srgb, var(--c1, #10b981) 38%, #fff);
    }
    .rd-pill i { font-size: 9px; }

    /* ============================================================
       BUTTONS
    ============================================================ */
    .rd-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        padding: 13px 20px;
        border: 0; border-radius: 15px;
        font: inherit; font-size: 12.5px; font-weight: 800; letter-spacing: .03em;
        color: #fff !important;
        cursor: pointer;
        background: linear-gradient(135deg, #10b981, #059669 55%, #0d9488);
        box-shadow: 0 12px 22px -10px rgba(16, 185, 129, .75), inset 0 1px 0 rgba(255, 255, 255, .25);
        transition: transform .25s, filter .25s;
    }
    .rd-btn:hover { filter: brightness(1.06); transform: translateY(-1px); }
    .rd-btn:active { transform: scale(.98); }
    .rd-btn:focus-visible { outline: 3px solid var(--gold); outline-offset: 3px; }
    .rd-btn--ghost { color: var(--muted) !important; background: #fff; border: 1px solid #dbe4e2; box-shadow: none; }
    .rd-btn--ghost:hover { background: #f8fafb; filter: none; }

    /* ============================================================
       ALERTS
    ============================================================ */
    .rd-alert {
        display: flex; align-items: flex-start; gap: 12px;
        padding: 12px 14px;
        border-radius: 20px;
        font-size: 12px; line-height: 1.55;
        background: var(--bg); border: 1px solid var(--bd); color: var(--tx);
    }
    .rd-alert > i:first-child {
        flex: none;
        width: 32px; height: 32px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 11px; font-size: 12px;
        color: var(--ic); background: #fff; border: 1px solid var(--bd);
    }
    .rd-alert b { font-weight: 900; }
    .rd-alert a { font-weight: 800; text-decoration: underline !important; }
    .rd-alert ul { margin: 4px 0 0; padding-left: 16px; font-size: 11.5px; }
    .rd-alert .x { margin-left: auto; align-self: center; color: inherit; opacity: .55; background: none; border: 0; cursor: pointer; }
    .rd-alert--ok   { --bg: #ecfdf5; --bd: #a7f3d0; --tx: #065f46; --ic: #059669; }
    .rd-alert--err  { --bg: #fff1f2; --bd: #fecdd3; --tx: #9f1239; --ic: #e11d48; }
    .rd-alert--info { --bg: #eff6ff; --bd: #bfdbfe; --tx: #1e3a8a; --ic: #2563eb; }

    /* ============================================================
       EMPTY STATE
    ============================================================ */
    .rd-empty { padding: 40px 18px; text-align: center; }
    .rd-empty-ico {
        width: 72px; height: 72px; margin: 0 auto 14px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 50%;
        font-size: 26px; color: var(--em-200);
        background: linear-gradient(135deg, var(--em-50), #f0fdfa);
        border: 1px dashed var(--em-200);
    }
    .rd-empty h3 { font-size: 15px; font-weight: 900; }
    .rd-empty p  { margin: 4px 0 16px !important; font-size: 12px; color: var(--faint); }

    /* ============================================================
       FORM FIELDS
    ============================================================ */
    .rd-lbl { display: block; margin-bottom: 6px; font-size: 10px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; color: var(--muted); }
    .rd-lbl small { font-size: 10px; font-weight: 600; letter-spacing: 0; text-transform: none; color: var(--faint); }
    .rd-in {
        display: block;
        width: 100%;
        padding: 11px 13px;
        border: 1px solid #dfe8e6;
        border-radius: 14px;
        background: #f8fafb;
        font: inherit; font-size: 14px; font-weight: 600;
        color: var(--ink);
        outline: 0;
        -webkit-appearance: none; appearance: none;
        transition: border-color .2s, box-shadow .2s, background .2s;
    }
    .rd-in::placeholder { color: #cbd5e1; font-weight: 500; }
    .rd-in:focus { border-color: var(--em-500); background: #fff; box-shadow: 0 0 0 3px rgba(16, 185, 129, .16); }
    textarea.rd-in { resize: none; line-height: 1.45; }
    @media (max-width: 640px) { .rd-in { font-size: 16px; } }
    .rd-sel { position: relative; }
    .rd-sel::after {
        content: '';
        position: absolute; right: 15px; top: 50%;
        width: 7px; height: 7px;
        border-right: 2px solid var(--faint); border-bottom: 2px solid var(--faint);
        transform: translateY(-65%) rotate(45deg);
        pointer-events: none;
    }
    .rd-sel .rd-in { padding-right: 36px; cursor: pointer; }
</style>