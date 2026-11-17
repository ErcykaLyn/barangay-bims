@extends('layouts.app')

@section('content')
@include('partials.resident-ui')

<style>
    .eq-banner {
        position: relative;
        overflow: hidden;
        display: flex; align-items: center; gap: 14px;
        padding: 14px 16px;
        border-radius: 26px;
        color: #fff;
        background:
            radial-gradient(110% 120% at 100% 0%, rgba(147, 197, 253, .35), transparent 55%),
            linear-gradient(135deg, #1d4ed8, #3730a3);
        box-shadow: 0 22px 38px -24px rgba(29, 78, 216, .9);
    }
    .eq-banner::before {
        content: '';
        position: absolute; right: -40px; top: -60px;
        width: 160px; height: 160px;
        border-radius: 50%;
        border: 1px dashed rgba(242, 196, 109, .45);
        pointer-events: none;
    }
    .eq-banner > * { position: relative; }
    .eq-banner-ico {
        flex: none;
        width: 46px; height: 46px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 16px;
        font-size: 18px; color: #1e3a8a;
        background: linear-gradient(135deg, #fde7b0, var(--gold));
    }
    .eq-banner h3 { font-size: 12px; font-weight: 900; letter-spacing: .14em; text-transform: uppercase; }
    .eq-banner p  { margin-top: 3px !important; font-size: 12.5px; line-height: 1.45; color: #dbeafe; }
    .eq-banner p b { color: #fff; }
    .eq-banner > div { flex: 1; min-width: 0; }
    .eq-banner .rd-cta { color: #1d4ed8 !important; padding: 10px 16px; }

    .eq-how {
        display: grid; gap: 10px;
        padding: 16px;
        border-radius: 26px;
        background: #fff; border: 1px solid var(--line);
        box-shadow: 0 14px 30px -24px rgba(15, 23, 42, .35);
    }
    @media (min-width: 640px) { .eq-how { grid-template-columns: auto 1fr; align-items: center; gap: 16px; padding: 18px 22px; } }
    .eq-how-ttl { display: flex; align-items: center; gap: 12px; }
    .eq-how-ttl i {
        width: 38px; height: 38px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 13px; font-size: 14px; color: #fff;
        background: linear-gradient(135deg, #3b82f6, #4f46e5);
        box-shadow: 0 10px 18px -8px #3b82f6;
    }
    .eq-how-ttl h3 { font-size: 12px; font-weight: 900; letter-spacing: .12em; text-transform: uppercase; }
    .eq-how p { font-size: 12.5px; line-height: 1.6; color: var(--muted); }
    .eq-how p b { color: var(--ink); }

    .eq-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; padding: 14px; }
    @media (min-width: 640px)  { .eq-grid { gap: 14px; padding: 18px 22px 22px; } }
    @media (min-width: 900px)  { .eq-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
    @media (min-width: 1200px) { .eq-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); } }

    .eq-item {
        position: relative;
        display: flex; flex-direction: column;
        overflow: hidden;
        border-radius: 22px;
        background: #fff;
        border: 1px solid var(--line);
        box-shadow: 0 14px 26px -24px rgba(15, 23, 42, .45);
        transition: transform .3s, box-shadow .3s, border-color .3s;
    }
    @media (hover: hover) { .eq-item:hover { transform: translateY(-4px); border-color: var(--em-200); box-shadow: 0 26px 38px -26px rgba(16, 185, 129, .7); } }

    .eq-photo { position: relative; height: 112px; overflow: hidden; background: linear-gradient(135deg, #eef3f2, #dfe9e7); }
    @media (min-width: 640px) { .eq-photo { height: 140px; } }
    .eq-photo img { display: block; width: 100%; height: 100%; object-fit: cover; transition: transform .7s; }
    @media (hover: hover) { .eq-item:hover .eq-photo img { transform: scale(1.08); } }
    .eq-photo .none { height: 100%; display: flex; align-items: center; justify-content: center; font-size: 30px; color: #c3d2cf; }
    .eq-photo::after { content: ''; position: absolute; inset: 0; background: linear-gradient(to top, rgba(0, 0, 0, .38), transparent 55%); pointer-events: none; }

    .eq-avail {
        position: absolute; top: 8px; right: 8px; z-index: 1;
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 9px;
        border-radius: 999px;
        font-size: 9px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase;
        color: #047857; background: #fff;
        box-shadow: 0 6px 12px -4px rgba(0, 0, 0, .3);
    }
    .eq-avail i { width: 6px; height: 6px; border-radius: 50%; background: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, .25); }

    .eq-avail.out {
        color: #b91c1c; 
        background: #fee2e2;
    }
    .eq-avail.out i {
        background: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, .25);
    }

    .eq-cat {
        position: absolute; bottom: 8px; left: 8px; z-index: 1;
        padding: 3px 9px;
        border-radius: 8px;
        font-size: 9px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase;
        color: #fff; background: rgba(15, 23, 42, .55);
    }

    .eq-body { display: flex; flex-direction: column; flex: 1; padding: 12px; }
    @media (min-width: 640px) { .eq-body { padding: 14px; } }
    .eq-name { font-size: 12.5px; font-weight: 900; letter-spacing: .02em; text-transform: uppercase; line-height: 1.25; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .eq-code { margin-top: 3px !important; font-family: ui-monospace, 'SF Mono', Menlo, monospace; font-size: 10px; color: var(--faint); }

    .eq-stock {
        display: flex; align-items: center; justify-content: space-between;
        margin-top: 10px;
        padding: 8px 11px;
        border-radius: 13px;
        background: var(--em-50); border: 1px solid var(--em-100);
        font-size: 10px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; color: var(--em-700);
    }
    .eq-stock i { margin-right: 6px; font-size: 10px; }
    .eq-stock b { font-size: 15px; font-weight: 900; letter-spacing: 0; }

    .eq-stock.out {
        background: #fef2f2; 
        border-color: #fecaca;
        color: #b91c1c;
    }

    .eq-desc { display: none; margin-top: 9px !important; font-size: 11.5px; line-height: 1.5; color: var(--muted); }
    @media (min-width: 640px) { .eq-desc { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; } }

    .eq-go { margin-top: auto; padding-top: 12px; }
    .eq-go .rd-btn { width: 100%; padding: 10px 12px; font-size: 11px; letter-spacing: .06em; text-transform: uppercase; border-radius: 13px; }

    .eq-go .rd-btn.disabled {
        background: #e2e8f0;
        color: #94a3b8;
        cursor: not-allowed;
        pointer-events: none;
        box-shadow: none;
    }

    .eq-empty { grid-column: 1 / -1; }
</style>

<div class="rd rd-stack" style="--rd-w: 1180px">

    @if($approvedCount > 0)
        <div class="eq-banner rd-rise">
            <span class="eq-banner-ico"><i class="fas fa-box-open"></i></span>
            <div>
                <h3>Ready for Pickup!</h3>
                <p>You have <b>{{ $approvedCount }}</b> approved item(s) waiting.</p>
            </div>
            <a href="{{ route('resident.equipment.requests') }}" class="rd-cta">
                <i class="fas fa-eye"></i> View
            </a>
        </div>
    @endif

    <header class="rd-hero rd-rise" style="--d:1">
        <div class="rd-hero-row">
            <div class="rd-id">
                <span class="rd-badge"><i class="fas fa-boxes"></i></span>
                <div>
                    <span class="rd-eyebrow"><i></i>Resident Portal</span>
                    <h2 class="rd-title">Equipment Request</h2>
                    <p class="rd-sub">Borrow barangay equipment for your events</p>
                </div>
            </div>

            <a href="{{ route('resident.equipment.requests') }}" class="rd-cta">
                <i class="fas fa-list"></i> My Requests
                @if($pendingCount + $approvedCount > 0)
                    <span class="count">{{ $pendingCount + $approvedCount }}</span>
                @endif
            </a>
        </div>
    </header>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="rd-alert rd-alert--ok" role="status">
            <i class="fas fa-check-circle"></i>
            <b style="align-self:center">{{ session('success') }}</b>
            <button type="button" @click="show = false" class="x" aria-label="Close"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="rd-alert rd-alert--err" role="alert">
            <i class="fas fa-exclamation-circle"></i>
            <b style="align-self:center">{{ session('error') }}</b>
            <button type="button" @click="show = false" class="x" aria-label="Close"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <div class="rd-stats">
        <div class="rd-stat rd-rise" style="--c1:#10b981; --c2:#047857; --d:2">
            <span class="rd-stat-ico"><i class="fas fa-boxes"></i></span>
            <div>
                <p class="rd-stat-lbl">Available</p>
                <p class="rd-stat-num">{{ $equipments->where('available_stock', '>', 0)->count() }}</p>
            </div>
            <span class="rd-stat-chip"><i class="fas fa-circle"></i> Live</span>
        </div>

        <div class="rd-stat rd-rise" style="--c1:#f59e0b; --c2:#b45309; --d:3">
            <span class="rd-stat-ico"><i class="fas fa-clock"></i></span>
            <div>
                <p class="rd-stat-lbl">Pending</p>
                <p class="rd-stat-num">{{ $pendingCount }}</p>
            </div>
            @if($pendingCount > 0)
                <span class="rd-stat-chip"><i class="fas fa-hourglass-half"></i> Wait</span>
            @else
                <span class="rd-stat-chip" style="--c1:#10b981; --c2:#047857"><i class="fas fa-check"></i> Clear</span>
            @endif
        </div>

        <div class="rd-stat rd-rise" style="--c1:#0ea5e9; --c2:#1d4ed8; --d:4">
            <span class="rd-stat-ico"><i class="fas fa-hand-holding"></i></span>
            <div>
                <p class="rd-stat-lbl">Approved</p>
                <p class="rd-stat-num">{{ $approvedCount }}</p>
            </div>
            <span class="rd-stat-chip"><i class="fas fa-check-circle"></i> OK</span>
        </div>

        <div class="rd-stat rd-rise" style="--c1:#8b5cf6; --c2:#6d28d9; --d:5">
            <span class="rd-stat-ico"><i class="fas fa-history"></i></span>
            <div>
                <p class="rd-stat-lbl">Total</p>
                <p class="rd-stat-num">{{ $myRequests->count() }}</p>
            </div>
            <span class="rd-stat-chip"><i class="fas fa-list"></i> All</span>
        </div>
    </div>

    <div class="eq-how rd-rise" style="--d:6">
        <div class="eq-how-ttl">
            <i class="fas fa-info-circle"></i>
            <h3>How It Works</h3>
        </div>
        <p>Browse available items below. Click <b>Request to Borrow</b> for equipment, or <b>Request to Claim</b> for medicines/supplies. Fill out the form and wait for admin approval.</p>
    </div>

    <section class="rd-card rd-rise" style="--d:7">
        <div class="rd-sechead">
            <div class="rd-sec" style="--c1:#10b981; --c2:#047857">
                <span class="rd-ico"><i class="fas fa-th"></i></span>
                <div>
                    <h2>Available Equipment</h2>
                    <p>{{ $equipments->count() }} items total</p>
                </div>
            </div>
        </div>

        <div class="eq-grid">
            @forelse($equipments as $item)
                @php
                    $isConsumable = in_array(strtolower($item->category ?? ''), ['medicine', 'medicines', 'supplies', 'supply']);
                    $buttonText = $isConsumable ? 'Request to Claim' : 'Request to Borrow';
                    $buttonIcon = $isConsumable ? 'fa-hand-holding-medical' : 'fa-hand-holding';
                @endphp
                <article class="eq-item">
                    <div class="eq-photo">
                        @if($item->photo_url)
                            <img src="{{ $item->photo_url }}" alt="{{ $item->item_name }}" loading="lazy">
                        @else
                            <div class="none"><i class="fas fa-image"></i></div>
                        @endif

                        @if($item->available_stock > 0)
                            <span class="eq-avail"><i></i>Available</span>
                        @else
                            <span class="eq-avail out"><i></i>Out of Stock</span>
                        @endif

                        <span class="eq-cat">{{ $item->category ?? 'General' }}</span>
                    </div>

                    <div class="eq-body">
                        <h3 class="eq-name">{{ $item->item_name }}</h3>
                        <p class="eq-code">#EQ-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</p>

                        <div class="eq-stock {{ $item->available_stock <= 0 ? 'out' : '' }}">
                            <span><i class="fas fa-cubes"></i>Stock</span>
                            <b>{{ $item->available_stock }}</b>
                        </div>

                        @if($item->description)
                            <p class="eq-desc">{{ Str::limit($item->description, 60) }}</p>
                        @endif

                        <div class="eq-go">
                            @if($item->available_stock > 0)
                                <a href="{{ route('resident.equipment.request.form', $item->id) }}" class="rd-btn">
                                    <i class="fas {{ $buttonIcon }}"></i> {{ $buttonText }}
                                </a>
                            @else
                                <button disabled class="rd-btn disabled">
                                    <i class="fas fa-ban"></i> Out of Stock
                                </button>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="rd-empty eq-empty">
                    <div class="rd-empty-ico"><i class="fas fa-box-open"></i></div>
                    <h3>No equipment available</h3>
                    <p>Please check back later.</p>
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection