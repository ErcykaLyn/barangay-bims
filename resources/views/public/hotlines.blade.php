@extends('layouts.public')

@section('content')
@php
    // Urdaneta City hotlines — pareho ang mga numero at tel: links sa dati
    $hotlines = [
        [
            'name'   => 'Barangay Bactad East',
            'type'   => 'Barangay Hall',
            'number' => '(075) 123 4567',
            'tel'    => 'tel:+631234567',
            'status' => 'Available',
            'icon'   => 'fa-home',
            'c1'     => '#10b981', 'c2' => '#047857',
            'wide'   => true,
        ],
        [
            'name'   => 'Urdaneta District Hospital',
            'type'   => 'Medical Emergency',
            'number' => '(075) 568 9012',
            'tel'    => 'tel:+63755689012',
            'status' => '24/7',
            'icon'   => 'fa-hospital',
            'c1'     => '#3b82f6', 'c2' => '#1d4ed8',
        ],
        [
            'name'   => 'Bureau of Fire Protection',
            'type'   => 'Fire Emergency',
            'number' => '(075) 522 2345',
            'tel'    => 'tel:+63755222345',
            'status' => '24/7',
            'icon'   => 'fa-fire-extinguisher',
            'c1'     => '#f97316', 'c2' => '#dc2626',
        ],
        [
            'name'   => 'PNP Urdaneta City',
            'type'   => 'Police Emergency',
            'number' => '0998 123 4567',
            'tel'    => 'tel:+639981234567',
            'status' => '24/7',
            'icon'   => 'fa-shield-alt',
            'c1'     => '#0ea5e9', 'c2' => '#1d4ed8',
        ],
        [
            'name'   => 'MDRRMO Urdaneta',
            'type'   => 'Disaster Response',
            'number' => '(075) 555 5111',
            'tel'    => 'tel:+63755555111',
            'status' => '24/7',
            'icon'   => 'fa-ambulance',
            'c1'     => '#8b5cf6', 'c2' => '#6d28d9',
        ],
    ];

    $utilities = [
        ['name' => 'Meralco',        'number' => '1620', 'tel' => 'tel:1620', 'icon' => 'fa-bolt', 'c1' => '#facc15', 'c2' => '#d97706'],
        ['name' => 'Water District', 'number' => '1162', 'tel' => 'tel:1162', 'icon' => 'fa-tint', 'c1' => '#38bdf8', 'c2' => '#0891b2'],
    ];
@endphp

<style>
    /* ============================================================
       EMERGENCY HOTLINES — self-contained styles (prefix: hl-)
    ============================================================ */
    .hl {
        --ink:   #0f172a;
        --muted: #64748b;
        --faint: #94a3b8;
        --line:  #f0e6e6;
        --red:   #dc2626;
        --gold:  #f2c46d;

        max-width: 880px;
        margin: 0 auto;
        padding: 14px 12px 40px;
        color: var(--ink);
    }
    .hl *, .hl *::before, .hl *::after { box-sizing: border-box; }
    .hl p, .hl h1, .hl h2, .hl h3 { margin: 0; }
    .hl a { text-decoration: none; color: inherit; }
    @media (min-width: 640px) { .hl { padding: 28px 20px 56px; } }

    .hl-rise { animation: hl-rise .7s cubic-bezier(.22, 1, .36, 1) both; animation-delay: calc(var(--d, 0) * 70ms); }
    @keyframes hl-rise { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }

    /* ---------- HERO ---------- */
    .hl-hero {
        position: relative;
        overflow: hidden;
        padding: 16px;
        border-radius: 28px;
        color: #fff;
        background:
            radial-gradient(120% 90% at 100% 0%, rgba(252, 165, 165, .34), transparent 55%),
            linear-gradient(145deg, #7f1d1d 0%, #b91c1c 48%, #9f1239 100%);
        box-shadow: 0 24px 44px -26px rgba(127, 29, 29, .95);
    }
    .hl-hero::before {
        content: '';
        position: absolute; right: -76px; top: -76px;
        width: 230px; height: 230px;
        border-radius: 50%;
        border: 1px solid rgba(255, 255, 255, .16);
        box-shadow: 0 0 0 30px rgba(255, 255, 255, .04), 0 0 0 60px rgba(255, 255, 255, .025);
        pointer-events: none;
    }
    .hl-hero::after {
        content: '';
        position: absolute; left: -50px; bottom: -70px;
        width: 180px; height: 180px;
        border-radius: 50%;
        border: 1px dashed rgba(242, 196, 109, .42);
        pointer-events: none;
    }
    .hl-hero > * { position: relative; z-index: 1; }
    @media (min-width: 640px) { .hl-hero { padding: 26px 28px; border-radius: 32px; } }

    .hl-head { display: flex; align-items: center; gap: 14px; }
    .hl-badge {
        position: relative;
        flex: none;
        width: 52px; height: 52px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 18px;
        font-size: 20px;
        color: #7f1d1d;
        background: linear-gradient(135deg, #fde7b0, var(--gold));
        box-shadow: 0 12px 22px -10px rgba(0, 0, 0, .55), inset 0 1px 0 rgba(255, 255, 255, .6);
    }
    .hl-badge::after { /* pulse ring */
        content: '';
        position: absolute; inset: 0;
        border-radius: 18px;
        border: 2px solid rgba(253, 231, 176, .8);
        animation: hl-pulse 2.2s ease-out infinite;
    }
    @keyframes hl-pulse {
        from { transform: scale(1);   opacity: .8; }
        to   { transform: scale(1.5); opacity: 0; }
    }
    .hl-eyebrow {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 9.5px; font-weight: 800;
        letter-spacing: .2em; text-transform: uppercase;
        color: #fecaca;
    }
    .hl-eyebrow i { width: 6px; height: 6px; border-radius: 50%; background: #fca5a5; box-shadow: 0 0 0 3px rgba(252, 165, 165, .3); }
    .hl-title { margin-top: 4px !important; font-size: 22px; font-weight: 900; letter-spacing: -.02em; line-height: 1.1; }
    .hl-sub   { margin-top: 5px !important; font-size: 12px; line-height: 1.45; color: #fecaca; }
    @media (min-width: 640px) {
        .hl-badge { width: 60px; height: 60px; font-size: 24px; }
        .hl-title { font-size: 30px; }
        .hl-sub   { font-size: 13.5px; }
    }

    /* ---------- REMINDER ---------- */
    .hl-note {
        display: flex; align-items: flex-start; gap: 12px;
        margin-top: 14px;
        padding: 12px 14px;
        border-radius: 20px;
        background: #fffbeb;
        border: 1px solid #fde68a;
        font-size: 12px; line-height: 1.55;
        color: #78350f;
    }
    .hl-note i {
        flex: none;
        width: 32px; height: 32px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 11px;
        font-size: 12px;
        color: #d97706;
        background: #fff;
        border: 1px solid #fde68a;
    }
    .hl-note b { font-weight: 900; }

    /* ---------- 911 ---------- */
    .hl-911 {
        position: relative;
        overflow: hidden;
        display: flex; align-items: center; justify-content: space-between; gap: 14px;
        margin-top: 14px;
        padding: 18px 18px 18px 20px;
        border-radius: 28px;
        color: #fff;
        background:
            radial-gradient(100% 120% at 0% 0%, rgba(255, 255, 255, .16), transparent 55%),
            linear-gradient(135deg, #ef4444 0%, #dc2626 45%, #be123c 100%);
        box-shadow: 0 24px 40px -22px rgba(220, 38, 38, .85), inset 0 1px 0 rgba(255, 255, 255, .3);
        transition: transform .25s, box-shadow .25s;
    }
    .hl-911::before {
        content: '';
        position: absolute; right: -60px; top: -60px;
        width: 190px; height: 190px;
        border-radius: 50%;
        border: 1px solid rgba(255, 255, 255, .2);
        box-shadow: 0 0 0 26px rgba(255, 255, 255, .05);
        pointer-events: none;
    }
    .hl-911 > * { position: relative; z-index: 1; }
    @media (hover: hover) { .hl-911:hover { transform: translateY(-2px); box-shadow: 0 30px 46px -22px rgba(220, 38, 38, .95), inset 0 1px 0 rgba(255, 255, 255, .3); } }
    .hl-911:active { transform: scale(.985); }
    .hl-911:focus-visible, .hl-row:focus-visible, .hl-util:focus-visible { outline: 3px solid var(--gold); outline-offset: 3px; }

    .hl-tag {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 9.5px; font-weight: 800;
        letter-spacing: .14em; text-transform: uppercase;
        background: rgba(0, 0, 0, .2);
        color: #ffe4e6;
    }
    .hl-911 h3 { margin-top: 10px !important; font-size: 15px; font-weight: 800; color: #ffe4e6; }
    .hl-911-num {
        margin-top: 2px !important;
        font-size: 62px; font-weight: 900;
        letter-spacing: -.04em; line-height: 1;
        text-shadow: 0 6px 18px rgba(0, 0, 0, .25);
    }
    .hl-911-lbl { margin-top: 6px !important; font-size: 10px; font-weight: 700; letter-spacing: .16em; text-transform: uppercase; opacity: .8; }
    @media (min-width: 640px) { .hl-911 { padding: 24px 28px; } .hl-911-num { font-size: 78px; } .hl-911 h3 { font-size: 18px; } }

    .hl-call {
        position: relative;
        flex: none;
        width: 68px; height: 68px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 50%;
        font-size: 24px;
        color: #dc2626;
        background: #fff;
        box-shadow: 0 16px 28px -10px rgba(0, 0, 0, .5);
    }
    .hl-call::before, .hl-call::after {
        content: '';
        position: absolute; inset: 0;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, .85);
        animation: hl-ripple 2.4s ease-out infinite;
    }
    .hl-call::after { animation-delay: 1.2s; }
    @keyframes hl-ripple { from { transform: scale(1); opacity: .8; } to { transform: scale(1.9); opacity: 0; } }
    @media (min-width: 640px) { .hl-call { width: 80px; height: 80px; font-size: 28px; } }

    /* ---------- SECTION CARD ---------- */
    .hl-card {
        margin-top: 14px;
        padding: 16px;
        border-radius: 26px;
        background: #fff;
        border: 1px solid var(--line);
        box-shadow: 0 14px 30px -24px rgba(15, 23, 42, .35);
    }
    @media (min-width: 640px) { .hl-card { padding: 22px; } }

    .hl-sec { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
    .hl-ico {
        flex: none;
        width: 38px; height: 38px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 13px;
        font-size: 14px; color: #fff;
        background: linear-gradient(135deg, var(--c1), var(--c2));
        box-shadow: 0 10px 18px -8px var(--c1);
    }
    .hl-sec h3 { font-size: 15px; font-weight: 900; letter-spacing: -.01em; }
    .hl-sec p  { margin-top: 2px !important; font-size: 10px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--faint); }

    /* ---------- HOTLINE ROW ---------- */
    .hl-list { display: grid; grid-template-columns: 1fr; gap: 8px; }
    @media (min-width: 720px) {
        .hl-list { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
        .hl-wide { grid-column: 1 / -1; }
    }

    .hl-row {
        position: relative;
        display: flex; align-items: center; gap: 12px;
        padding: 12px;
        border-radius: 20px;
        background: #f8fafb;
        border: 1px solid #eef2f3;
        transition: transform .25s, border-color .25s, box-shadow .25s, background .25s;
    }
    @media (hover: hover) {
        .hl-row:hover { background: #fff; border-color: var(--c1); box-shadow: 0 16px 28px -20px var(--c1); transform: translateY(-2px); }
    }
    .hl-row:active { transform: scale(.985); }

    .hl-row .hl-ico { width: 44px; height: 44px; border-radius: 15px; font-size: 16px; }
    .hl-info { min-width: 0; flex: 1; }
    .hl-name { font-size: 13.5px; font-weight: 800; line-height: 1.25; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .hl-type { margin-top: 3px !important; font-size: 9.5px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: var(--faint); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

    .hl-num { flex: none; text-align: right; }
    .hl-num b { display: block; font-family: ui-monospace, 'SF Mono', Menlo, monospace; font-size: 12.5px; font-weight: 800; color: var(--ink); white-space: nowrap; }
    .hl-st {
        display: inline-flex; align-items: center; gap: 5px;
        margin-top: 5px;
        font-size: 9px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase;
        color: var(--c2);
    }
    .hl-st i { width: 6px; height: 6px; border-radius: 50%; background: var(--c1); box-shadow: 0 0 0 3px color-mix(in srgb, var(--c1) 25%, transparent); }
    @media (min-width: 640px) { .hl-name { font-size: 14.5px; } .hl-num b { font-size: 13.5px; } }

    /* ---------- UTILITIES ---------- */
    .hl-utils { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; }
    @media (min-width: 640px) { .hl-utils { gap: 10px; } }
    .hl-util {
        display: flex; align-items: center; gap: 11px;
        padding: 12px;
        border-radius: 20px;
        background: #f8fafb;
        border: 1px solid #eef2f3;
        transition: transform .25s, border-color .25s, box-shadow .25s, background .25s;
    }
    @media (hover: hover) {
        .hl-util:hover { background: #fff; border-color: var(--c1); box-shadow: 0 16px 28px -20px var(--c1); transform: translateY(-2px); }
    }
    .hl-util:active { transform: scale(.985); }
    .hl-util .hl-ico { width: 40px; height: 40px; border-radius: 14px; }
    .hl-util .hl-name { font-size: 12.5px; }
    .hl-util .hl-code { margin-top: 3px !important; font-family: ui-monospace, 'SF Mono', Menlo, monospace; font-size: 15px; font-weight: 900; color: var(--ink); }

    .hl-foot { margin-top: 20px; text-align: center; font-size: 10.5px; font-weight: 600; color: var(--faint); }
    .hl-foot i { margin-right: 4px; }

    @media (prefers-reduced-motion: reduce) {
        .hl-rise, .hl-badge::after, .hl-call::before, .hl-call::after { animation: none; }
    }
</style>

<div class="hl">

    {{-- HERO --}}
    <header class="hl-hero hl-rise">
        <div class="hl-head">
            <span class="hl-badge"><i class="fas fa-phone-alt"></i></span>
            <div>
                <span class="hl-eyebrow"><i></i>Emergency</span>
                <h2 class="hl-title">Emergency Hotlines</h2>
                <p class="hl-sub">Mga importanteng numero para sa emergencies</p>
            </div>
        </div>
    </header>

    {{-- REMINDER --}}
    <div class="hl-note hl-rise" style="--d:1">
        <i class="fas fa-exclamation-triangle"></i>
        <div><b>Paalala:</b> Sa oras ng emergency, manatiling kalmado at tumawag agad sa tamang hotline.</div>
    </div>

    {{-- NATIONAL EMERGENCY --}}
    <a href="tel:911" class="hl-911 hl-rise" style="--d:2" aria-label="Tumawag sa 911">
        <div>
            <span class="hl-tag"><i class="fas fa-shield-alt"></i> National Emergency</span>
            <h3>Tawag Agad sa 911</h3>
            <p class="hl-911-num">911</p>
            <p class="hl-911-lbl">Emergency Number</p>
        </div>
        <span class="hl-call"><i class="fas fa-phone-volume"></i></span>
    </a>

    {{-- URDANETA CITY HOTLINES --}}
    <section class="hl-card hl-rise" style="--d:3">
        <div class="hl-sec" style="--c1:#10b981; --c2:#047857">
            <span class="hl-ico"><i class="fas fa-building"></i></span>
            <div>
                <h3>Urdaneta City Hotlines</h3>
                <p>Local emergency services</p>
            </div>
        </div>

        <div class="hl-list">
            @foreach($hotlines as $h)
                <a href="{{ $h['tel'] }}" class="hl-row {{ !empty($h['wide']) ? 'hl-wide' : '' }}"
                   style="--c1: {{ $h['c1'] }}; --c2: {{ $h['c2'] }}">
                    <span class="hl-ico"><i class="fas {{ $h['icon'] }}"></i></span>
                    <div class="hl-info">
                        <p class="hl-name">{{ $h['name'] }}</p>
                        <p class="hl-type">{{ $h['type'] }}</p>
                    </div>
                    <div class="hl-num">
                        <b>{{ $h['number'] }}</b>
                        <span class="hl-st"><i></i>{{ $h['status'] }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- UTILITY SERVICES --}}
    <section class="hl-card hl-rise" style="--d:4">
        <div class="hl-sec" style="--c1:#f59e0b; --c2:#ea580c">
            <span class="hl-ico"><i class="fas fa-tools"></i></span>
            <div>
                <h3>Utility Services</h3>
                <p>Power, water, atbp.</p>
            </div>
        </div>

        <div class="hl-utils">
            @foreach($utilities as $u)
                <a href="{{ $u['tel'] }}" class="hl-util" style="--c1: {{ $u['c1'] }}; --c2: {{ $u['c2'] }}">
                    <span class="hl-ico"><i class="fas {{ $u['icon'] }}"></i></span>
                    <div class="hl-info">
                        <p class="hl-name">{{ $u['name'] }}</p>
                        <p class="hl-code">{{ $u['number'] }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- NOTE --}}
    <p class="hl-foot"><i class="fas fa-info-circle"></i>Ang mga numerong ito ay maaaring magbago. Paki-verify sa Barangay Hall.</p>
</div>
@endsection