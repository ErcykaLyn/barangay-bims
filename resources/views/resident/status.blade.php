@extends('layouts.app')

@section('content')
@php
    $total     = $requests->count();
    $pending   = $requests->where('status', 'pending')->count();
    $accepted  = $requests->where('status', 'accepted')->count();
    $ready     = $requests->where('status', 'ready_for_pickup')->count();
    $completed = $requests->where('status', 'completed')->count();
    $denied    = $requests->where('status', 'denied')->count();

    $statusConfig = [
        'pending' => [
            'label' => 'Pending', 'icon' => 'fa-clock', 'stage' => 1,
            'message' => 'Waiting for initial review.',
            'c1' => '#f59e0b', 'c2' => '#b45309',
        ],
        'accepted' => [
            'label' => 'Accepted', 'icon' => 'fa-check', 'stage' => 2,
            'message' => 'Your request has been accepted. Please wait for printing.',
            'c1' => '#0ea5e9', 'c2' => '#1d4ed8',
        ],
        'ready_for_pickup' => [
            'label' => 'Ready for Pickup', 'icon' => 'fa-box-open', 'stage' => 3,
            'message' => 'Ready at the Barangay Hall. Please bring valid ID and payment.',
            'c1' => '#10b981', 'c2' => '#047857', 'notice' => true,
        ],
        'completed' => [
            'label' => 'Completed', 'icon' => 'fa-check-double', 'stage' => 4,
            'message' => 'Document claimed. Thank you!',
            'c1' => '#059669', 'c2' => '#064e3b',
        ],
        'denied' => [
            'label' => 'Denied', 'icon' => 'fa-times-circle', 'stage' => 0,
            'message' => 'Your request was denied. Contact barangay for details.',
            'c1' => '#f43f5e', 'c2' => '#be123c',
        ],
    ];
    $steps = ['Submitted', 'Accepted', 'Ready', 'Claimed'];
@endphp

@include('partials.resident-ui')

<style>
    .mr-list { display: grid; gap: 12px; }
    @media (min-width: 900px) { .mr-list { grid-template-columns: repeat(2, minmax(0, 1fr)); align-items: start; } }

    .mr-card {
        position: relative;
        overflow: hidden;
        padding: 16px 16px 16px 20px;
        border-radius: 26px;
        background: #fff;
        border: 1px solid var(--line);
        box-shadow: 0 14px 30px -24px rgba(15, 23, 42, .4);
        transition: transform .3s, box-shadow .3s, border-color .3s;
    }
    .mr-card::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 5px; background: linear-gradient(var(--c1), var(--c2)); }
    @media (hover: hover) { .mr-card:hover { transform: translateY(-3px); border-color: var(--c1); box-shadow: 0 22px 36px -24px var(--c1); } }
    .mr-card.is-ready { border-color: var(--em-200); box-shadow: 0 0 0 3px rgba(16, 185, 129, .12), 0 14px 30px -24px rgba(16, 185, 129, .6); }

    .mr-top { display: flex; align-items: center; gap: 12px; }
    .mr-ico {
        flex: none;
        width: 44px; height: 44px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 15px;
        font-size: 16px; color: #fff;
        background: linear-gradient(135deg, var(--c1), var(--c2));
        box-shadow: 0 10px 18px -8px var(--c1);
    }
    .mr-info { min-width: 0; flex: 1; }
    .mr-info h3 { font-size: 14.5px; font-weight: 900; letter-spacing: -.01em; line-height: 1.25; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .mr-info p  { margin-top: 3px !important; font-size: 11px; color: var(--faint); }
    .mr-info p i { margin-right: 5px; font-size: 9.5px; }

    .mr-meta { display: flex; flex-wrap: wrap; gap: 4px 14px; margin-top: 12px; font-size: 12px; color: var(--muted); }
    .mr-meta span { display: inline-flex; align-items: center; gap: 6px; min-width: 0; max-width: 100%; }
    .mr-meta span.purpose { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .mr-meta i { font-size: 10px; color: var(--em-500); }
    .mr-msg { margin-top: 8px !important; font-size: 12px; line-height: 1.5; font-style: italic; color: var(--faint); }

    /* ---------- Progress tracker ---------- */
    .mr-steps { display: grid; grid-template-columns: repeat(4, 1fr); margin: 16px 0 0; padding: 0; list-style: none; }
    .mr-steps li { position: relative; text-align: center; font-size: 9.5px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; color: var(--faint); }
    .mr-steps li::before {
        content: '';
        position: relative; z-index: 1;
        display: block;
        width: 12px; height: 12px; margin: 0 auto 7px;
        border-radius: 50%;
        background: #fff; border: 2px solid #d9e4e1;
    }
    .mr-steps li::after {
        content: '';
        position: absolute; top: 5px; left: calc(-50% + 6px); width: calc(100% - 12px); height: 2px;
        background: #e3ecea;
    }
    .mr-steps li:first-child::after { display: none; }
    .mr-steps li.done { color: var(--c2); }
    .mr-steps li.done::before { background: var(--c1); border-color: var(--c1); }
    .mr-steps li.done::after  { background: var(--c1); }
    .mr-steps li.now::before  { box-shadow: 0 0 0 4px color-mix(in srgb, var(--c1) 24%, transparent); }

    /* ---------- Claim notice ---------- */
    .mr-claim {
        display: flex; align-items: flex-start; gap: 12px;
        margin-top: 14px;
        padding: 12px 14px;
        border-radius: 18px;
        background: linear-gradient(135deg, #ecfdf5, #f0fdfa);
        border: 1px solid var(--em-200);
    }
    .mr-claim > i {
        flex: none;
        width: 34px; height: 34px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 12px;
        font-size: 13px; color: #fff;
        background: linear-gradient(135deg, #10b981, #0d9488);
    }
    .mr-claim b { display: block; font-size: 9.5px; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; color: var(--em-600); }
    .mr-claim p { margin-top: 3px !important; font-size: 12.5px; font-weight: 700; line-height: 1.4; color: #064e3b; }
    .mr-claim small { display: flex; flex-wrap: wrap; gap: 4px 14px; margin-top: 6px; font-size: 11px; font-weight: 700; color: var(--em-700); }
    .mr-claim small i { margin-right: 5px; }
</style>

<div class="rd rd-stack" style="--rd-w: 1100px">

    {{-- ================= HERO ================= --}}
    <header class="rd-hero rd-rise">
        <div class="rd-hero-row">
            <div class="rd-id">
                <span class="rd-badge"><i class="fas fa-file-signature"></i></span>
                <div>
                    <span class="rd-eyebrow"><i></i>Resident Portal</span>
                    <h2 class="rd-title">My Document Requests</h2>
                    <p class="rd-sub">Track the status of your document requests</p>
                </div>
            </div>

            <a href="{{ route('resident.requests.form') }}" class="rd-cta">
                <i class="fas fa-plus spin"></i> New Request
            </a>
        </div>
    </header>

    {{-- ================= STATS ================= --}}
    <div class="rd-stats">
        <div class="rd-stat rd-rise" style="--c1:#10b981; --c2:#047857; --d:1">
            <span class="rd-stat-ico"><i class="fas fa-file-alt"></i></span>
            <div>
                <p class="rd-stat-lbl">Total</p>
                <p class="rd-stat-num">{{ $total }}</p>
            </div>
            <span class="rd-stat-chip"><i class="fas fa-layer-group"></i> All</span>
        </div>

        <div class="rd-stat rd-rise" style="--c1:#f59e0b; --c2:#b45309; --d:2">
            <span class="rd-stat-ico"><i class="fas fa-clock"></i></span>
            <div>
                <p class="rd-stat-lbl">Pending</p>
                <p class="rd-stat-num">{{ $pending }}</p>
            </div>
            @if($pending > 0)
                <span class="rd-stat-chip" style="--c1:#f43f5e; --c2:#be123c"><i class="fas fa-exclamation-circle"></i> Wait</span>
            @endif
        </div>

        <div class="rd-stat rd-rise" style="--c1:#0ea5e9; --c2:#1d4ed8; --d:3">
            <span class="rd-stat-ico"><i class="fas fa-check"></i></span>
            <div>
                <p class="rd-stat-lbl">Accepted</p>
                <p class="rd-stat-num">{{ $accepted }}</p>
            </div>
            <span class="rd-stat-chip"><i class="fas fa-cog"></i> Live</span>
        </div>

        <div class="rd-stat rd-rise" style="--c1:#8b5cf6; --c2:#6d28d9; --d:4">
            <span class="rd-stat-ico"><i class="fas fa-check-circle"></i></span>
            <div>
                <p class="rd-stat-lbl">Ready</p>
                <p class="rd-stat-num">{{ $ready + $completed }}</p>
            </div>
            <span class="rd-stat-chip"><i class="fas fa-trophy"></i> Done</span>
        </div>
    </div>

    {{-- ================= REQUESTS ================= --}}
    @if($requests->isEmpty())
        <div class="rd-card rd-rise" style="--d:5">
            <div class="rd-empty">
                <div class="rd-empty-ico"><i class="fas fa-inbox"></i></div>
                <h3>No document requests yet</h3>
                <p>Start by requesting a barangay document online.</p>
                <a href="{{ route('resident.requests.form') }}" class="rd-btn"><i class="fas fa-plus-circle"></i> Request a Document</a>
            </div>
        </div>
    @else
        <div class="mr-list">
            @foreach($requests as $req)
                @php
                    $cfg   = $statusConfig[$req->status] ?? $statusConfig['pending'];
                    $stage = $cfg['stage'];
                @endphp

                <article class="mr-card rd-rise {{ $req->status == 'ready_for_pickup' ? 'is-ready' : '' }}"
                         style="--c1: {{ $cfg['c1'] }}; --c2: {{ $cfg['c2'] }}; --d: {{ min($loop->index, 8) + 5 }}">

                    <div class="mr-top">
                        <span class="mr-ico"><i class="fas {{ $cfg['icon'] }}"></i></span>
                        <div class="mr-info">
                            <h3>{{ $req->document_type }}</h3>
                            <p><i class="fas fa-clock"></i>{{ $req->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="rd-pill">
                            <i class="fas {{ $cfg['icon'] }}"></i>{{ $cfg['label'] }}
                            @if($req->status == 'ready_for_pickup')
                                <i class="fas fa-bell"></i>
                            @endif
                        </span>
                    </div>

                    <div class="mr-meta">
                        <span><i class="fas fa-calendar-alt"></i>{{ $req->created_at->format('M d, Y') }}</span>
                        <span class="purpose"><i class="fas fa-bullseye"></i>{{ $req->purpose }}</span>
                    </div>

                    <p class="mr-msg">{{ $cfg['message'] }}</p>

                    {{-- Progress (hindi ipinapakita kung denied) --}}
                    @if($stage > 0)
                        <ol class="mr-steps" aria-label="Request progress">
                            @foreach($steps as $i => $step)
                                <li class="{{ ($i + 1) <= $stage ? 'done' : '' }} {{ ($i + 1) == $stage ? 'now' : '' }}">{{ $step }}</li>
                            @endforeach
                        </ol>
                    @endif

                    {{-- CLAIM NOTICE --}}
                    @if($cfg['notice'] ?? false)
                        <div class="mr-claim">
                            <i class="fas fa-bell"></i>
                            <div>
                                <b>Ready for Claim</b>
                                <p>Please claim it at the Barangay Hall and ready your payment.</p>
                                <small>
                                    <span><i class="fas fa-map-marker-alt"></i>Barangay Hall</span>
                                    <span><i class="fas fa-clock"></i>Mon-Fri 8AM-5PM</span>
                                </small>
                            </div>
                        </div>
                    @endif
                </article>
            @endforeach
        </div>
    @endif
</div>
@endsection