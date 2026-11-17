@extends('layouts.app')

@section('content')
@php
    // Parehong document_type values sa dati (para gumana pa rin ang controller)
    $docs = [
        [
            'value' => 'Barangay Residency Certificate',
            'title' => 'Barangay Residency',
            'desc'  => 'Patunay na nakatira ka sa Barangay Bactad East.',
            'use'   => 'Para sa school, trabaho, at government IDs',
            'icon'  => 'fa-home',
            'c1'    => '#34d399', 'c2' => '#059669',
        ],
        [
            'value' => 'Barangay Indigency Certificate',
            'title' => 'Indigency',
            'desc'  => 'Patunay na kabilang ka sa mga pamilyang nangangailangan.',
            'use'   => 'Para sa tulong pinansyal, medical, at scholarship',
            'icon'  => 'fa-hand-holding-heart',
            'c1'    => '#fb7185', 'c2' => '#e11d48',
        ],
        [
            'value' => 'Barangay Clearance Certification',
            'title' => 'Barangay Clearance',
            'desc'  => 'Patunay na wala kang nakabinbing kaso sa barangay.',
            'use'   => 'Para sa pag-a-apply ng trabaho at lisensya',
            'icon'  => 'fa-shield-alt',
            'c1'    => '#60a5fa', 'c2' => '#2563eb',
        ],
        [
            'value' => 'Good Moral Certificate',
            'title' => 'Good Moral',
            'desc'  => 'Patunay ng mabuting asal at pagkatao mo sa komunidad.',
            'use'   => 'Para sa school, scholarship, at employment',
            'icon'  => 'fa-award',
            'c1'    => '#fbbf24', 'c2' => '#d97706',
        ],
        [
            'value' => 'Barangay Business Clearance',
            'title' => 'Business Clearance',
            'desc'  => 'Pahintulot ng barangay para magtayo o mag-renew ng negosyo.',
            'use'   => 'Para sa business permit at renewal',
            'icon'  => 'fa-store',
            'c1'    => '#c084fc', 'c2' => '#7c3aed',
        ],
    ];

    $selected = old('document_type', '');
@endphp

<style>
    /* ============================================================
       DOCUMENT REQUEST — self-contained styles (prefix: rq-)
    ============================================================ */
    .rq {
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
        --em-900: #064e3b;
        --gold:   #f2c46d;
        --rose:   #e11d48;

        max-width: 920px;
        margin: 0 auto;
        padding: 14px 12px 40px;
        color: var(--ink);
    }
    .rq *, .rq *::before, .rq *::after { box-sizing: border-box; }
    .rq p, .rq h1, .rq h2, .rq h3, .rq h4 { margin: 0; }
    @media (min-width: 640px) { .rq { padding: 28px 20px 56px; } }

    /* ---------- One-time entrance ---------- */
    .rq-rise { animation: rq-rise .6s cubic-bezier(.22, 1, .36, 1) both; animation-delay: calc(var(--d, 0) * 70ms); }
    @keyframes rq-rise { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }
    @media (prefers-reduced-motion: reduce) { .rq-rise { animation: none; } .rq * { transition: none !important; } }

    /* ---------- Alert ---------- */
    .rq-alert {
        display: flex; gap: 10px;
        margin-top: 14px;
        padding: 12px 14px;
        border-radius: 16px;
        background: #fff1f2; border: 1px solid #fecdd3; color: #9f1239;
        font-size: 12.5px;
    }
    .rq-alert i { margin-top: 2px; }
    .rq-alert b { display: block; margin-bottom: 4px; }
    .rq-alert ul { margin: 0; padding-left: 16px; font-size: 12px; line-height: 1.6; }

    /* ============================================================
       HERO
    ============================================================ */
    .rq-hero {
        position: relative;
        overflow: hidden;
        padding: 22px 18px 20px;
        border-radius: 28px;
        color: #fff;
        background:
            radial-gradient(120% 90% at 100% 0%, rgba(110, 231, 183, .28), transparent 55%),
            linear-gradient(145deg, #064e3b 0%, #065f46 46%, #0b4f4a 100%);
        box-shadow: 0 24px 44px -26px rgba(6, 78, 59, .85);
    }
    .rq-hero::before {
        content: '';
        position: absolute; right: -80px; top: -80px;
        width: 240px; height: 240px; border-radius: 50%;
        border: 1px solid rgba(255, 255, 255, .14);
        box-shadow: 0 0 0 30px rgba(255, 255, 255, .035), 0 0 0 60px rgba(255, 255, 255, .025);
        pointer-events: none;
    }
    .rq-hero::after {
        content: '';
        position: absolute; left: -50px; bottom: -80px;
        width: 190px; height: 190px; border-radius: 50%;
        border: 1px dashed rgba(242, 196, 109, .38);
        pointer-events: none;
    }
    .rq-hero > * { position: relative; z-index: 1; }
    @media (min-width: 640px) { .rq-hero { padding: 32px 34px 28px; border-radius: 32px; } }

    .rq-crumb {
        display: inline-flex; align-items: center; gap: 8px;
        font-size: 12px; font-weight: 700; color: var(--em-200);
    }
    .rq-crumb i { width: 6px; height: 6px; border-radius: 50%; background: #6ee7b7; box-shadow: 0 0 0 3px rgba(110, 231, 183, .25); }

    .rq-title {
        margin-top: 10px !important;
        max-width: 560px;
        font-size: 26px; font-weight: 900; letter-spacing: -.025em; line-height: 1.1;
    }
    @media (min-width: 640px) { .rq-title { font-size: 38px; } }
    .rq-lead {
        margin-top: 10px !important;
        max-width: 520px;
        font-size: 13.5px; line-height: 1.6;
        color: #d5f5e6;
    }
    @media (min-width: 640px) { .rq-lead { font-size: 15px; } }

    /* how it works: totoong sequence kaya may numbering */
    .rq-flow {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0;
        margin-top: 22px;
        border-radius: 20px;
        background: rgba(0, 0, 0, .2);
        border: 1px solid rgba(255, 255, 255, .08);
        overflow: hidden;
    }
    .rq-flow > div { padding: 12px 10px; min-width: 0; }
    .rq-flow > div + div { border-left: 1px solid rgba(255, 255, 255, .09); }
    .rq-flow b {
        display: flex; align-items: center; gap: 7px;
        font-size: 12.5px; font-weight: 800;
    }
    .rq-flow b span {
        flex: none;
        width: 20px; height: 20px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 50%;
        font-size: 10.5px; font-weight: 900;
        color: #04382b; background: var(--gold);
    }
    .rq-flow small { display: block; margin-top: 4px; font-size: 11px; line-height: 1.4; color: var(--em-200); opacity: .9; }
    @media (max-width: 520px) {
        .rq-flow { grid-template-columns: 1fr; }
        .rq-flow > div + div { border-left: 0; border-top: 1px solid rgba(255, 255, 255, .09); }
    }

    /* ============================================================
       DOCUMENT LIST
    ============================================================ */
    .rq-sec { margin-top: 26px; }
    .rq-sec-head { display: flex; align-items: baseline; justify-content: space-between; gap: 12px; margin-bottom: 14px; padding: 0 4px; }
    .rq-sec-head h2 { font-size: 18px; font-weight: 900; letter-spacing: -.015em; }
    .rq-sec-head span { font-size: 12px; font-weight: 600; color: var(--muted); }

    .rq-docs { display: grid; grid-template-columns: 1fr; gap: 10px; }
    @media (min-width: 760px) {
        .rq-docs { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .rq-docs > label:last-child { grid-column: 1 / -1; }
    }

    .rq-pick { position: relative; display: block; cursor: pointer; }
    .rq-vh { position: absolute; opacity: 0; width: 1px; height: 1px; pointer-events: none; }

    .rq-doc {
        position: relative;
        display: flex; align-items: center; gap: 14px;
        height: 100%;
        padding: 16px;
        border-radius: 24px;
        background: #fff;
        border: 2px solid var(--line);
        transition: border-color .2s, background .2s, box-shadow .2s, transform .2s;
    }
    @media (hover: hover) {
        .rq-pick:hover .rq-doc { border-color: var(--c1); box-shadow: 0 18px 30px -24px var(--c1); }
    }
    .rq-ico {
        flex: none;
        width: 54px; height: 54px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 18px;
        font-size: 20px; color: #fff;
        background: linear-gradient(135deg, var(--c1), var(--c2));
        box-shadow: 0 12px 20px -10px var(--c1);
    }
    .rq-body { min-width: 0; flex: 1; padding-right: 26px; }
    .rq-body h3 { font-size: 15.5px; font-weight: 900; letter-spacing: -.01em; line-height: 1.2; }
    .rq-body p { margin-top: 4px !important; font-size: 12.5px; line-height: 1.5; color: var(--muted); }
    .rq-body em {
        display: inline-flex; align-items: center; gap: 6px;
        margin-top: 8px;
        font-style: normal; font-size: 11.5px; font-weight: 700;
        color: var(--em-700);
    }
    .rq-body em i { font-size: 10px; color: var(--em-500); }

    .rq-mark {
        position: absolute; top: 14px; right: 14px;
        width: 24px; height: 24px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 50%;
        font-size: 10px; color: transparent;
        background: #fff; border: 2px solid #d5e1de;
        transition: all .2s;
    }

    .rq-vh:checked + .rq-doc {
        border-color: var(--em-500);
        background: linear-gradient(160deg, #f0fdf8, #dcfaee);
        box-shadow: 0 18px 30px -22px rgba(16, 185, 129, .8);
    }
    .rq-vh:checked + .rq-doc .rq-mark { color: #fff; background: var(--em-500); border-color: var(--em-500); transform: scale(1.08); }
    .rq-vh:focus-visible + .rq-doc { outline: 3px solid var(--gold); outline-offset: 3px; }

    /* ============================================================
       NOTE
    ============================================================ */
    .rq-note {
        display: flex; gap: 12px;
        margin-top: 16px;
        padding: 14px 16px;
        border-radius: 20px;
        background: #f6faf9; border: 1px solid #e3ede9;
        font-size: 12.5px; line-height: 1.6; color: #475569;
    }
    .rq-note i { flex: none; margin-top: 3px; color: var(--em-500); }
    .rq-note b { color: var(--ink); font-weight: 800; }
    .rq-note a { color: var(--em-700); font-weight: 800; text-decoration: underline; text-underline-offset: 2px; }

    /* ============================================================
       ACTION BAR
    ============================================================ */
    .rq-bar {
        position: sticky; bottom: 12px; z-index: 30;
        display: flex; align-items: center; gap: 12px;
        margin-top: 18px;
        padding: 10px 10px 10px 18px;
        border-radius: 24px;
        background: #0f172a;
        box-shadow: 0 22px 40px -14px rgba(15, 23, 42, .65);
    }
    .rq-pickd { min-width: 0; flex: 1; }
    .rq-pickd small { display: block; font-size: 11px; font-weight: 600; color: #94a3b8; }
    .rq-pickd b {
        display: block; margin-top: 2px;
        font-size: 14px; font-weight: 800; color: #fff;
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .rq-pickd b.is-empty { color: #64748b; font-weight: 600; }

    .rq-cancel {
        flex: none;
        padding: 13px 16px;
        border-radius: 16px;
        font-size: 13px; font-weight: 700;
        color: #cbd5e1; text-decoration: none;
        background: rgba(255, 255, 255, .08);
        transition: background .2s;
    }
    .rq-cancel:hover { background: rgba(255, 255, 255, .15); color: #fff; }
    .rq-send {
        flex: none;
        display: inline-flex; align-items: center; gap: 9px;
        padding: 13px 22px;
        border: 0; border-radius: 16px;
        font: inherit; font-size: 13.5px; font-weight: 800;
        color: #fff; cursor: pointer;
        background: linear-gradient(135deg, #10b981, #059669 55%, #0d9488);
        box-shadow: 0 10px 20px -8px rgba(16, 185, 129, .7), inset 0 1px 0 rgba(255, 255, 255, .25);
        transition: filter .2s, transform .2s, opacity .2s;
    }
    .rq-send:hover:not(:disabled) { filter: brightness(1.07); }
    .rq-send:active:not(:disabled) { transform: scale(.97); }
    .rq-send:disabled { opacity: .4; cursor: not-allowed; box-shadow: none; }

    @media (max-width: 560px) {
        .rq-bar { flex-wrap: wrap; padding: 12px; }
        .rq-pickd { flex: 1 1 100%; padding: 0 4px; }
        .rq-cancel { flex: 1; text-align: center; }
        .rq-send { flex: 2; justify-content: center; }
    }

    .rq-foot {
        margin-top: 18px;
        text-align: center;
        font-size: 11px; font-weight: 600; color: var(--faint);
    }
</style>

<div class="rq">

    {{-- ================= HERO ================= --}}
    <header class="rq-hero rq-rise">
        <span class="rq-crumb"><i></i> Resident Portal · Barangay Bactad East</span>
        <h1 class="rq-title">Humingi ng barangay document nang hindi pumipila.</h1>
        <p class="rq-lead">
            Pumili ng dokumento at ipadala ang request. Gagamitin namin ang impormasyon sa iyong profile,
            kaya wala ka nang pupunan.
        </p>

        <div class="rq-flow">
            <div>
                <b><span>1</span> Pumili</b>
                <small>Piliin ang kailangan mong dokumento</small>
            </div>
            <div>
                <b><span>2</span> Ipadala</b>
                <small>Susuriin ito ng barangay sa loob ng 24-48 oras</small>
            </div>
            <div>
                <b><span>3</span> Kunin</b>
                <small>Magpapakita ng status kapag handa na para sa pickup</small>
            </div>
        </div>
    </header>

    {{-- ================= VALIDATION ERRORS ================= --}}
    @if($errors->any())
        <div class="rq-alert rq-rise" role="alert">
            <i class="fas fa-exclamation-circle"></i>
            <div>
                <b>May mali sa request mo</b>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- ================= FORM ================= --}}
    <form action="{{ route('resident.requests.store') }}" method="POST" id="requestForm">
        @csrf

        <section class="rq-sec rq-rise" style="--d:1">
            <div class="rq-sec-head">
                <h2>Anong dokumento ang kailangan mo?</h2>
                <span>{{ count($docs) }} available</span>
            </div>

            <div class="rq-docs">
                @foreach($docs as $doc)
                    <label class="rq-pick" style="--c1: {{ $doc['c1'] }}; --c2: {{ $doc['c2'] }}">
                        <input type="radio" name="document_type" value="{{ $doc['value'] }}"
                               class="rq-vh" data-title="{{ $doc['title'] }}"
                               {{ $loop->first ? 'required' : '' }}
                               {{ $selected === $doc['value'] ? 'checked' : '' }}>
                        <div class="rq-doc">
                            <span class="rq-ico"><i class="fas {{ $doc['icon'] }}"></i></span>
                            <div class="rq-body">
                                <h3>{{ $doc['title'] }}</h3>
                                <p>{{ $doc['desc'] }}</p>
                                <em><i class="fas fa-check-circle"></i> {{ $doc['use'] }}</em>
                            </div>
                            <span class="rq-mark"><i class="fas fa-check"></i></span>
                        </div>
                    </label>
                @endforeach
            </div>

            <div class="rq-note">
                <i class="fas fa-info-circle"></i>
                <p>
                    <b>Paalala:</b> Siguraduhing tama ang detalye sa iyong
                    <a href="{{ route('resident.profile') }}">profile</a> dahil dito kukunin ang impormasyon
                    sa dokumento. Masusubaybayan mo ang request sa
                    <a href="{{ route('resident.requests.status') }}">Track Status</a>.
                </p>
            </div>
        </section>

        {{-- ================= ACTION BAR ================= --}}
        <div class="rq-bar rq-rise" style="--d:2">
            <div class="rq-pickd">
                <small>Napili mo</small>
                <b id="pickedName" class="is-empty">Wala pang napipili</b>
            </div>

            <a href="{{ route('resident.dashboard') }}" class="rq-cancel">Cancel</a>
            <button type="submit" class="rq-send" id="sendBtn" disabled>
                <i class="fas fa-paper-plane"></i> Send request
            </button>
        </div>

        <p class="rq-foot">Bactad East BIMS · Digital Barangay Services</p>
    </form>
</div>

<script>
    (function () {
        const form   = document.getElementById('requestForm');
        const radios = form.querySelectorAll('input[name="document_type"]');
        const name   = document.getElementById('pickedName');
        const send   = document.getElementById('sendBtn');

        function sync() {
            const chosen = form.querySelector('input[name="document_type"]:checked');
            if (chosen) {
                name.textContent = chosen.dataset.title;
                name.classList.remove('is-empty');
                send.disabled = false;
            } else {
                name.textContent = 'Wala pang napipili';
                name.classList.add('is-empty');
                send.disabled = true;
            }
        }

        radios.forEach(r => r.addEventListener('change', sync));
        sync();

        // Iwas double-submit
        form.addEventListener('submit', function () {
            send.disabled = true;
            send.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        });
    })();
</script>
@endsection