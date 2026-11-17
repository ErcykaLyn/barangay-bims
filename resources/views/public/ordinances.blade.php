@extends('layouts.public')

@section('content')
@php
    $ordinances = [
        [
            'number'   => '2024-01',
            'title'    => 'Anti-Littering',
            'desc'     => 'Pagbabawal sa pagtatapon ng basura sa kalsada at kanal. Multa: ₱500.',
            'category' => 'Environment',
            'date'     => 'Jan 15',
            'icon'     => 'fa-trash',
            'c1'       => '#10b981', 'c2' => '#16a34a',
        ],
        [
            'number'   => '2024-02',
            'title'    => 'Curfew for Minors',
            'desc'     => 'Bawal ang menor de edad (17 pababa) gumala 10PM-4AM.',
            'category' => 'Safety',
            'date'     => 'Feb 3',
            'icon'     => 'fa-clock',
            'c1'       => '#3b82f6', 'c2' => '#4f46e5',
        ],
        [
            'number'   => '2024-03',
            'title'    => 'Peace & Order',
            'desc'     => 'Bawal ang maingay na videoke pagkatapos ng 10PM.',
            'category' => 'Peace',
            'date'     => 'Mar 12',
            'icon'     => 'fa-shield-alt',
            'c1'       => '#0ea5e9', 'c2' => '#0891b2',
        ],
        [
            'number'   => '2024-04',
            'title'    => 'Anti-Stray Animals',
            'desc'     => 'Bawal pakawalan ang alagang hayop sa kalsada.',
            'category' => 'Environment',
            'date'     => 'Apr 5',
            'icon'     => 'fa-paw',
            'c1'       => '#f97316', 'c2' => '#dc2626',
        ],
        [
            'number'   => '2024-05',
            'title'    => 'Smoking Ban',
            'desc'     => 'Bawal manigarilyo sa barangay hall, health center, at school.',
            'category' => 'Health',
            'date'     => 'May 20',
            'icon'     => 'fa-ban',
            'c1'       => '#f43f5e', 'c2' => '#db2777',
        ],
        [
            'number'   => '2024-06',
            'title'    => 'Road Clearing',
            'desc'     => 'Bawal maglagay ng obstruction sa kalsada at sidewalk.',
            'category' => 'Infra',
            'date'     => 'Jun 8',
            'icon'     => 'fa-road',
            'c1'       => '#f59e0b', 'c2' => '#ea580c',
        ],
        [
            'number'   => '2024-07',
            'title'    => 'Anti-Vandalism',
            'desc'     => 'Bawal sirain o dumihan ang pampublikong ari-arian.',
            'category' => 'Peace',
            'date'     => 'Jul 15',
            'icon'     => 'fa-spray-can',
            'c1'       => '#8b5cf6', 'c2' => '#9333ea',
        ],
        [
            'number'   => '2024-08',
            'title'    => 'Waste Segregation',
            'desc'     => 'Ihiwalay ang basura: biodegradable, non-biodegradable, recyclable.',
            'category' => 'Environment',
            'date'     => 'Aug 22',
            'icon'     => 'fa-recycle',
            'c1'       => '#22c55e', 'c2' => '#059669',
        ],
    ];

    $categories = collect($ordinances)->pluck('category')->unique()->values();
    $latest     = collect($ordinances)->last()['number'];
@endphp

<style>
    /* ============================================================
       ORDINANCES — self-contained styles (prefix: od-)
    ============================================================ */
    .od {
        --ink:   #0f172a;
        --muted: #64748b;
        --faint: #94a3b8;
        --line:  #e7e8f4;
        --ind:   #4f46e5;
        --gold:  #f2c46d;

        max-width: 1100px;
        margin: 0 auto;
        padding: 14px 12px 40px;
        color: var(--ink);
    }
    .od *, .od *::before, .od *::after { box-sizing: border-box; }
    .od p, .od h1, .od h2, .od h3 { margin: 0; }
    @media (min-width: 640px) { .od { padding: 28px 20px 56px; } }

    .od-rise { animation: od-rise .7s cubic-bezier(.22, 1, .36, 1) both; animation-delay: calc(var(--d, 0) * 60ms); }
    @keyframes od-rise { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }
    @media (prefers-reduced-motion: reduce) { .od-rise { animation: none; } }

    /* ---------- HERO ---------- */
    .od-hero {
        position: relative;
        overflow: hidden;
        padding: 16px;
        border-radius: 28px;
        color: #fff;
        background:
            radial-gradient(120% 90% at 100% 0%, rgba(167, 139, 250, .34), transparent 55%),
            linear-gradient(145deg, #312e81 0%, #3730a3 46%, #4c1d95 100%);
        box-shadow: 0 24px 44px -26px rgba(49, 46, 129, .9);
    }
    .od-hero::before {
        content: '';
        position: absolute; right: -76px; top: -76px;
        width: 230px; height: 230px;
        border-radius: 50%;
        border: 1px solid rgba(255, 255, 255, .15);
        box-shadow: 0 0 0 30px rgba(255, 255, 255, .035), 0 0 0 60px rgba(255, 255, 255, .025);
        pointer-events: none;
    }
    .od-hero::after {
        content: '';
        position: absolute; left: -50px; bottom: -70px;
        width: 180px; height: 180px;
        border-radius: 50%;
        border: 1px dashed rgba(242, 196, 109, .4);
        pointer-events: none;
    }
    .od-hero > * { position: relative; z-index: 1; }
    @media (min-width: 640px) { .od-hero { padding: 26px 28px 22px; border-radius: 32px; } }

    .od-head { display: flex; align-items: center; gap: 14px; }
    .od-badge {
        flex: none;
        width: 52px; height: 52px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 18px;
        font-size: 20px;
        color: #312e81;
        background: linear-gradient(135deg, #fde7b0, var(--gold));
        box-shadow: 0 12px 22px -10px rgba(0, 0, 0, .55), inset 0 1px 0 rgba(255, 255, 255, .6);
    }
    .od-eyebrow {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 9.5px; font-weight: 800;
        letter-spacing: .2em; text-transform: uppercase;
        color: #c7d2fe;
    }
    .od-eyebrow i { width: 6px; height: 6px; border-radius: 50%; background: #a5b4fc; box-shadow: 0 0 0 3px rgba(165, 180, 252, .28); }
    .od-title { margin-top: 4px !important; font-size: 22px; font-weight: 900; letter-spacing: -.02em; line-height: 1.1; }
    .od-sub   { margin-top: 5px !important; font-size: 12px; line-height: 1.45; color: #c7d2fe; }
    @media (min-width: 640px) {
        .od-badge { width: 60px; height: 60px; font-size: 24px; }
        .od-title { font-size: 30px; }
        .od-sub   { font-size: 13.5px; }
    }

    .od-stats {
        display: grid; grid-template-columns: repeat(3, 1fr);
        margin-top: 18px;
        border-radius: 20px;
        background: rgba(0, 0, 0, .22);
        border: 1px solid rgba(255, 255, 255, .09);
        overflow: hidden;
    }
    .od-stat { padding: 11px 8px; text-align: center; min-width: 0; }
    .od-stat + .od-stat { border-left: 1px solid rgba(255, 255, 255, .1); }
    .od-stat small {
        display: block;
        font-size: 8.5px; font-weight: 800;
        letter-spacing: .16em; text-transform: uppercase;
        color: #c7d2fe; opacity: .9;
    }
    .od-stat b { display: block; margin-top: 4px; font-size: 16px; font-weight: 800; }
    @media (min-width: 640px) { .od-stat b { font-size: 19px; } }

    /* ---------- NOTICE ---------- */
    .od-note {
        display: flex; align-items: flex-start; gap: 12px;
        margin-top: 14px;
        padding: 12px 14px;
        border-radius: 20px;
        background: #eef2ff;
        border: 1px solid #c7d2fe;
        font-size: 12px; line-height: 1.55;
        color: #312e81;
    }
    .od-note i {
        flex: none;
        width: 32px; height: 32px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 11px;
        font-size: 12px;
        color: var(--ind);
        background: #fff;
        border: 1px solid #c7d2fe;
    }
    .od-note b { font-weight: 900; }

    /* ---------- FILTER ---------- */
    .od-bar {
        display: flex; align-items: center; justify-content: space-between; gap: 10px;
        margin-top: 18px; margin-bottom: 10px;
    }
    .od-count { font-size: 11px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; color: var(--faint); white-space: nowrap; }
    .od-chips {
        display: flex; gap: 6px;
        overflow-x: auto;
        padding: 2px 2px 4px;
        margin: 0 -2px;
        scrollbar-width: none;
    }
    .od-chips::-webkit-scrollbar { display: none; }
    .od-chip {
        flex: none;
        display: inline-flex; align-items: center; gap: 6px;
        padding: 7px 13px;
        border-radius: 999px;
        font: inherit; font-size: 11.5px; font-weight: 700;
        color: var(--muted);
        background: #fff;
        border: 1px solid var(--line);
        cursor: pointer;
        transition: background .2s, color .2s, border-color .2s;
    }
    .od-chip em {
        font-style: normal; font-size: 10px; font-weight: 800;
        padding: 1px 6px; border-radius: 999px;
        background: #f1f5f9; color: var(--faint);
    }
    .od-chip:hover { border-color: #a5b4fc; color: var(--ind); }
    .od-chip[aria-pressed="true"] { color: #fff; background: linear-gradient(135deg, #4f46e5, #6d28d9); border-color: transparent; }
    .od-chip[aria-pressed="true"] em { background: rgba(255, 255, 255, .22); color: #fff; }
    .od-chip:focus-visible { outline: 2px solid var(--ind); outline-offset: 2px; }

    /* ---------- GRID / CARD ---------- */
    .od-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
    @media (min-width: 640px)  { .od-grid { gap: 14px; } }
    @media (min-width: 1024px) { .od-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); } }

    .od-card {
        position: relative;
        display: flex; flex-direction: column;
        overflow: hidden;
        padding: 14px 13px 12px;
        border-radius: 22px;
        background: #fff;
        border: 1px solid var(--line);
        box-shadow: 0 14px 28px -24px rgba(15, 23, 42, .4);
        transition: transform .3s, box-shadow .3s, border-color .3s;
    }
    .od-card[hidden] { display: none; }
    .od-card::before {
        content: '';
        position: absolute; left: 0; right: 0; top: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--c1), var(--c2));
    }
    @media (hover: hover) {
        .od-card:hover { transform: translateY(-3px); border-color: var(--c1); box-shadow: 0 22px 36px -22px var(--c1); }
    }

    .od-top { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
    .od-ico {
        width: 40px; height: 40px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 14px;
        font-size: 15px; color: #fff;
        background: linear-gradient(135deg, var(--c1), var(--c2));
        box-shadow: 0 10px 18px -8px var(--c1);
    }
    .od-date {
        padding: 3px 9px;
        border-radius: 999px;
        font-size: 10px; font-weight: 800; letter-spacing: .06em;
        color: var(--muted);
        background: #f4f5fb;
    }
    .od-num {
        margin-top: 12px !important;
        font-family: ui-monospace, 'SF Mono', Menlo, monospace;
        font-size: 10px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase;
        color: var(--c1);
    }
    .od-name { margin-top: 3px !important; font-size: 14px; font-weight: 900; letter-spacing: -.01em; line-height: 1.2; }
    .od-desc { margin-top: 6px !important; font-size: 11.5px; line-height: 1.55; color: var(--muted); flex: 1; }
    @media (min-width: 640px) { .od-name { font-size: 15.5px; } .od-desc { font-size: 12.5px; } }

    .od-foot { margin-top: 12px; padding-top: 10px; border-top: 1px dashed var(--line); }
    .od-cat {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 10px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase;
        color: var(--muted);
    }
    .od-cat i { width: 7px; height: 7px; border-radius: 50%; background: var(--c1); box-shadow: 0 0 0 3px color-mix(in srgb, var(--c1) 22%, transparent); }

    .od-foot-note {
        margin-top: 22px;
        text-align: center;
        font-size: 10.5px; font-weight: 600;
        color: var(--faint);
    }
    .od-foot-note i { margin-right: 4px; }
</style>

<div class="od">

    {{-- HERO --}}
    <header class="od-hero od-rise">
        <div class="od-head">
            <span class="od-badge"><i class="fas fa-scroll"></i></span>
            <div>
                <span class="od-eyebrow"><i></i>Barangay Rules</span>
                <h2 class="od-title">Barangay Ordinances</h2>
                <p class="od-sub">Mga batas at regulasyon ng Barangay Bactad East</p>
            </div>
        </div>

        <div class="od-stats">
            <div class="od-stat"><small>Ordinances</small><b>{{ count($ordinances) }}</b></div>
            <div class="od-stat"><small>Categories</small><b>{{ $categories->count() }}</b></div>
            <div class="od-stat"><small>Latest</small><b>{{ $latest }}</b></div>
        </div>
    </header>

    {{-- INFO BANNER --}}
    <div class="od-note od-rise" style="--d:1">
        <i class="fas fa-gavel"></i>
        <div><b>Paalala:</b> Ang mga ordinansa ay ipinapatupad para sa kaayusan at kaligtasan ng lahat.</div>
    </div>

    {{-- FILTER --}}
    <div class="od-bar od-rise" style="--d:2">
        <div class="od-chips" role="group" aria-label="Filter by category">
            <button type="button" class="od-chip" data-filter="all" aria-pressed="true">All <em>{{ count($ordinances) }}</em></button>
            @foreach($categories as $cat)
                <button type="button" class="od-chip" data-filter="{{ $cat }}" aria-pressed="false">
                    {{ $cat }} <em>{{ collect($ordinances)->where('category', $cat)->count() }}</em>
                </button>
            @endforeach
        </div>
        <span class="od-count" id="odCount" aria-live="polite">{{ count($ordinances) }} ordinances</span>
    </div>

    {{-- ORDINANCES LIST — 2 columns sa phone --}}
    <div class="od-grid">
        @foreach($ordinances as $ord)
            <article class="od-card od-rise" data-cat="{{ $ord['category'] }}"
                     style="--c1: {{ $ord['c1'] }}; --c2: {{ $ord['c2'] }}; --d: {{ $loop->index + 3 }}">
                <div class="od-top">
                    <span class="od-ico"><i class="fas {{ $ord['icon'] }}"></i></span>
                    <span class="od-date">{{ $ord['date'] }}</span>
                </div>

                <p class="od-num">Ord. {{ $ord['number'] }}</p>
                <h3 class="od-name">{{ $ord['title'] }}</h3>
                <p class="od-desc">{{ $ord['desc'] }}</p>

                <div class="od-foot">
                    <span class="od-cat"><i></i>{{ $ord['category'] }}</span>
                </div>
            </article>
        @endforeach
    </div>

    {{-- FOOTER NOTE --}}
    <p class="od-foot-note"><i class="fas fa-info-circle"></i>Para sa kopya ng buong ordinansa, pumunta sa Barangay Hall.</p>
</div>

<script>
    (function () {
        const chips = document.querySelectorAll('.od-chip');
        const cards = document.querySelectorAll('.od-card');
        const count = document.getElementById('odCount');

        chips.forEach(function (chip) {
            chip.addEventListener('click', function () {
                const f = chip.dataset.filter;
                let n = 0;

                chips.forEach(function (c) { c.setAttribute('aria-pressed', c === chip ? 'true' : 'false'); });
                cards.forEach(function (card) {
                    const show = f === 'all' || card.dataset.cat === f;
                    card.hidden = !show;
                    if (show) n++;
                });
                count.textContent = n + (n === 1 ? ' ordinance' : ' ordinances');
            });
        });
    })();
</script>
@endsection