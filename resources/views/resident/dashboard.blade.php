@extends('layouts.app')

@section('content')
@php
    $totalRequests   = $requests->count();
    $pendingCount    = $requests->where('status', 'Pending')->count();
    $processingCount = $requests->where('status', 'Processing')->count();
    $readyCount      = $requests->where('status', 'Ready for Pickup')->count();
    $completedCount  = $requests->where('status', 'Completed')->count();
    $deniedCount     = $requests->where('status', 'Denied')->count();
    $approvedReady   = $readyCount + $completedCount;

    $statusConfig = [
        'Pending'          => ['c1' => '#f59e0b', 'c2' => '#b45309', 'icon' => 'fa-clock',               'desc' => 'Awaiting review'],
        'Processing'       => ['c1' => '#3b82f6', 'c2' => '#1d4ed8', 'icon' => 'fa-spinner fa-pulse',    'desc' => 'Being processed'],
        'Ready for Pickup' => ['c1' => '#10b981', 'c2' => '#047857', 'icon' => 'fa-check-circle',        'desc' => 'Ready to claim'],
        'Completed'        => ['c1' => '#059669', 'c2' => '#064e3b', 'icon' => 'fa-check-double',        'desc' => 'Completed'],
        'Denied'           => ['c1' => '#f43f5e', 'c2' => '#be123c', 'icon' => 'fa-times-circle',        'desc' => 'Not approved'],
    ];
@endphp

@include('partials.resident-ui')

<style>
    .dh-main { display: grid; grid-template-columns: minmax(0, 1fr); gap: 14px; }
    @media (min-width: 1024px) { .dh-main { grid-template-columns: minmax(0, 2fr) minmax(0, 1fr); align-items: start; } }
    .dh-side { display: grid; gap: 14px; }

    /* ---------- Requests list ---------- */
    .dh-row {
        display: flex; align-items: center; flex-wrap: wrap; gap: 12px;
        padding: 14px 16px;
        border-top: 1px solid #eef3f2;
        transition: background .2s;
    }
    .dh-row:first-child { border-top: 0; }
    .dh-row:hover { background: #f8fcfb; }
    @media (min-width: 640px) { .dh-row { padding: 16px 22px; flex-wrap: nowrap; } }

    .dh-ico {
        flex: none;
        width: 42px; height: 42px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 15px;
        font-size: 15px; color: #fff;
        background: linear-gradient(135deg, var(--c1), var(--c2));
        box-shadow: 0 10px 18px -8px var(--c1);
    }
    .dh-info { min-width: 0; flex: 1 1 calc(100% - 56px); }
    @media (min-width: 640px) { .dh-info { flex: 1 1 auto; } }
    .dh-name { font-size: 14px; font-weight: 800; letter-spacing: -.01em; line-height: 1.25; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .dh-meta { display: flex; flex-wrap: wrap; gap: 3px 14px; margin-top: 4px !important; font-size: 11.5px; color: var(--muted); }
    .dh-meta span { display: inline-flex; align-items: center; gap: 6px; min-width: 0; max-width: 100%; }
    .dh-meta span.purpose { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .dh-meta i { font-size: 9.5px; color: var(--em-500); }

    .dh-right { flex: 1 1 100%; padding-left: 54px; }
    @media (min-width: 640px) { .dh-right { flex: none; padding-left: 0; text-align: right; } }
    .dh-desc { display: none; margin-top: 5px !important; font-family: ui-monospace, 'SF Mono', Menlo, monospace; font-size: 10px; color: var(--faint); }
    @media (min-width: 640px) { .dh-desc { display: block; } }

    /* ---------- Sidebar ---------- */
    .dh-qa {
        position: relative;
        overflow: hidden;
        padding: 18px;
        border-radius: 26px;
        color: #fff;
        background:
            radial-gradient(120% 90% at 100% 0%, rgba(110, 231, 183, .26), transparent 55%),
            linear-gradient(145deg, #064e3b 0%, #065f46 50%, #0b4f4a 100%);
        box-shadow: 0 22px 40px -26px rgba(6, 78, 59, .9);
    }
    .dh-qa::before {
        content: '';
        position: absolute; right: -60px; bottom: -60px;
        width: 180px; height: 180px;
        border-radius: 50%;
        border: 1px dashed rgba(242, 196, 109, .4);
        pointer-events: none;
    }
    .dh-qa > * { position: relative; }
    .dh-qa .tag {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 9.5px; font-weight: 800; letter-spacing: .14em; text-transform: uppercase;
        color: var(--gold); background: rgba(0, 0, 0, .22);
    }
    .dh-qa h3 { margin-top: 10px !important; font-size: 18px; font-weight: 900; }
    .dh-qa p { margin: 6px 0 14px !important; font-size: 12px; line-height: 1.55; color: #a7f3d0; }
    .dh-qa .rd-cta { width: 100%; }

    .dh-links { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; }
    .dh-link {
        display: flex; align-items: center; gap: 10px;
        padding: 11px 12px;
        border-radius: 16px;
        font-size: 12px; font-weight: 700;
        background: #f5f9f8;
        border: 1px solid transparent;
        transition: transform .25s, background .25s, border-color .25s, color .25s;
    }
    .dh-link i { width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 10px; font-size: 12px; color: var(--em-600); background: #fff; }
    @media (hover: hover) { .dh-link:hover { transform: translateY(-2px); background: #fff; border-color: var(--em-200); color: var(--em-700); } }

    .dh-hot { display: grid; gap: 4px; }
    .dh-hot div { display: flex; align-items: center; gap: 10px; padding: 9px 10px; border-radius: 14px; transition: background .2s; }
    .dh-hot div:hover { background: #f8fafb; }
    .dh-hot i { flex: none; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; border-radius: 11px; font-size: 12px; color: var(--c1); background: #f1f5f9; background: color-mix(in srgb, var(--c1) 13%, #fff); }
    .dh-hot span { flex: 1; min-width: 0; font-size: 12px; font-weight: 600; color: #475569; line-height: 1.3; }
    .dh-hot b { font-family: ui-monospace, 'SF Mono', Menlo, monospace; font-size: 11px; font-weight: 800; white-space: nowrap; }

    .dh-contact { display: grid; gap: 10px; font-size: 12.5px; color: #475569; }
    .dh-contact p { display: flex; align-items: flex-start; gap: 10px; line-height: 1.45; }
    .dh-contact i { flex: none; width: 24px; margin-top: 1px; text-align: center; font-size: 12px; color: var(--em-500); }
    .dh-contact .mono { font-family: ui-monospace, 'SF Mono', Menlo, monospace; word-break: break-all; }
</style>

<div class="rd rd-stack" style="--rd-w: 1180px">

    {{-- ================= HERO ================= --}}
    <header class="rd-hero rd-rise">
        <div class="rd-hero-row">
            <div class="rd-id">
                <span class="rd-badge"><i class="fas fa-hand-sparkles"></i></span>
                <div>
                    <span class="rd-eyebrow"><i></i>Resident Portal</span>
                    <h2 class="rd-title">Welcome back, {{ auth()->user()->first_name ?? auth()->user()->name }}!</h2>
                    <p class="rd-sub">Here's the status of your requested documents.</p>
                </div>
            </div>

            <a href="{{ route('resident.requests.form') }}" class="rd-cta">
                <i class="fas fa-plus spin"></i> New Request
            </a>
        </div>

        <div class="rd-tags">
            <span class="rd-tag"><i class="fas fa-check-circle"></i> Verified</span>
            <span class="rd-tag"><i class="fas fa-calendar-alt"></i> Since {{ auth()->user()->created_at->format('M Y') }}</span>
        </div>
    </header>

    {{-- ================= STATS ================= --}}
    <div class="rd-stats">
        <div class="rd-stat rd-rise" style="--c1:#10b981; --c2:#047857; --d:1">
            <span class="rd-stat-ico"><i class="fas fa-file-alt"></i></span>
            <div>
                <p class="rd-stat-lbl">Total</p>
                <p class="rd-stat-num">{{ $totalRequests }}</p>
            </div>
            <span class="rd-stat-chip"><i class="fas fa-layer-group"></i> All</span>
        </div>

        <div class="rd-stat rd-rise" style="--c1:#f59e0b; --c2:#b45309; --d:2">
            <span class="rd-stat-ico"><i class="fas fa-clock"></i></span>
            <div>
                <p class="rd-stat-lbl">Pending</p>
                <p class="rd-stat-num">{{ $pendingCount }}</p>
            </div>
            @if($pendingCount > 0)
                <span class="rd-stat-chip" style="--c1:#f43f5e; --c2:#be123c"><i class="fas fa-exclamation-circle"></i> Wait</span>
            @else
                <span class="rd-stat-chip" style="--c1:#10b981; --c2:#047857"><i class="fas fa-check"></i> Clear</span>
            @endif
        </div>

        <div class="rd-stat rd-rise" style="--c1:#3b82f6; --c2:#1d4ed8; --d:3">
            <span class="rd-stat-ico"><i class="fas fa-spinner"></i></span>
            <div>
                <p class="rd-stat-lbl">Processing</p>
                <p class="rd-stat-num">{{ $processingCount }}</p>
            </div>
            <span class="rd-stat-chip"><i class="fas fa-cog"></i> Live</span>
        </div>

        <div class="rd-stat rd-rise" style="--c1:#8b5cf6; --c2:#6d28d9; --d:4">
            <span class="rd-stat-ico"><i class="fas fa-check-double"></i></span>
            <div>
                <p class="rd-stat-lbl">Ready</p>
                <p class="rd-stat-num">{{ $approvedReady }}</p>
            </div>
            <span class="rd-stat-chip"><i class="fas fa-trophy"></i> Done</span>
        </div>
    </div>

    {{-- ================= MAIN ================= --}}
    <div class="dh-main">

        {{-- ====== Recent Requests ====== --}}
        <section class="rd-card rd-rise" style="--d:5">
            <div class="rd-sechead">
                <div class="rd-sec" style="--c1:#10b981; --c2:#047857">
                    <span class="rd-ico"><i class="fas fa-history"></i></span>
                    <div>
                        <h2>Recent Requests</h2>
                        <p>Your latest transactions</p>
                    </div>
                </div>
                <a href="{{ route('resident.requests.form') }}" class="rd-more">New <i class="fas fa-arrow-right"></i></a>
            </div>

            <div>
                @forelse($requests as $request)
                    @php $cfg = $statusConfig[$request->status] ?? $statusConfig['Pending']; @endphp
                    <div class="dh-row" style="--c1: {{ $cfg['c1'] }}; --c2: {{ $cfg['c2'] }}">
                        <span class="dh-ico"><i class="fas {{ $cfg['icon'] }}"></i></span>

                        <div class="dh-info">
                            <p class="dh-name">{{ $request->document_type }}</p>
                            <p class="dh-meta">
                                <span class="purpose"><i class="fas fa-bullseye"></i>{{ $request->purpose }}</span>
                                <span><i class="fas fa-calendar"></i>{{ $request->created_at->format('M d, Y') }}</span>
                            </p>
                        </div>

                        <div class="dh-right">
                            <span class="rd-pill"><i class="fas {{ $cfg['icon'] }}"></i>{{ $request->status }}</span>
                            <p class="dh-desc">{{ $cfg['desc'] }}</p>
                        </div>
                    </div>
                @empty
                    <div class="rd-empty">
                        <div class="rd-empty-ico"><i class="fas fa-inbox"></i></div>
                        <h3>No requests yet</h3>
                        <p>Start by making your first request!</p>
                        <a href="{{ route('resident.requests.form') }}" class="rd-btn"><i class="fas fa-plus-circle"></i> Create First Request</a>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- ====== Sidebar ====== --}}
        <aside class="dh-side">

            {{-- Quick Action --}}
            <div class="dh-qa rd-rise" style="--d:6">
                <span class="tag"><i class="fas fa-bolt"></i> Shortcut</span>
                <h3>Quick Actions</h3>
                <p>Request Barangay Clearance, Certificate of Residency, or Indigency.</p>
                <a href="{{ route('resident.requests.form') }}" class="rd-cta">
                    <i class="fas fa-file-signature"></i> Submit New Request
                </a>
            </div>

            {{-- Barangay Services --}}
            <section class="rd-card rd-pad rd-rise" style="--d:7">
                <div class="rd-sec" style="--c1:#10b981; --c2:#047857">
                    <span class="rd-ico"><i class="fas fa-concierge-bell"></i></span>
                    <div>
                        <h3>Barangay Services</h3>
                        <p>Quick links</p>
                    </div>
                </div>
                <div class="dh-links">
                    <a href="{{ route('resident.requests.form') }}" class="dh-link"><i class="fas fa-file-alt"></i> Clearance</a>
                    <a href="{{ route('resident.requests.form') }}" class="dh-link"><i class="fas fa-home"></i> Residency</a>
                    <a href="{{ route('resident.requests.form') }}" class="dh-link"><i class="fas fa-hand-holding-heart"></i> Indigency</a>
                    <a href="{{ route('resident.requests.form') }}" class="dh-link"><i class="fas fa-id-card"></i> Barangay ID</a>
                </div>
            </section>

            {{-- Emergency Hotlines --}}
            <section class="rd-card rd-pad rd-rise" style="--d:8">
                <div class="rd-sec" style="--c1:#f43f5e; --c2:#dc2626">
                    <span class="rd-ico"><i class="fas fa-phone-alt"></i></span>
                    <div>
                        <h3>Emergency Hotlines</h3>
                        <p>For urgent needs</p>
                    </div>
                </div>
                <div class="dh-hot">
                    <div style="--c1:#f43f5e"><i class="fas fa-ambulance"></i><span>Barangay Emergency</span><b>(075) 123 4567</b></div>
                    <div style="--c1:#3b82f6"><i class="fas fa-hospital"></i><span>Urdaneta District Hospital</span><b>(075) 568 9012</b></div>
                    <div style="--c1:#f97316"><i class="fas fa-fire-extinguisher"></i><span>BFP Urdaneta</span><b>(075) 522 2345</b></div>
                    <div style="--c1:#22c55e"><i class="fas fa-shield-alt"></i><span>PNP Bactad</span><b>0998 123 4567</b></div>
                </div>
            </section>

            {{-- Barangay Hall --}}
            <section class="rd-card rd-pad rd-rise" style="--d:9">
                <div class="rd-sec" style="--c1:#10b981; --c2:#047857">
                    <span class="rd-ico"><i class="fas fa-map-marker-alt"></i></span>
                    <div>
                        <h3>Barangay Hall</h3>
                        <p>Contact info</p>
                    </div>
                </div>
                <div class="dh-contact">
                    <p><i class="fas fa-map-marker-alt"></i><span>Bactad East, Urdaneta City, Pangasinan</span></p>
                    <p><i class="fas fa-phone"></i><span class="mono">(075) 123 4567</span></p>
                    <p><i class="fas fa-envelope"></i><span class="mono">bims@bactadeast.gov.ph</span></p>
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection