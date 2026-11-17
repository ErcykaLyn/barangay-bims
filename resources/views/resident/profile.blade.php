@extends('layouts.app')

@section('content')
@php
    $resident = \App\Models\Resident::find(Auth::guard('resident')->id());

    $fullName = collect([$resident->first_name ?? null, $resident->middle_name ?? null, $resident->last_name ?? null])
        ->filter()->implode(' ');
    $initials = strtoupper(substr($resident->first_name ?? 'U', 0, 1)) . strtoupper(substr($resident->last_name ?? '', 0, 1));
    $age      = ($resident && $resident->date_of_birth) ? \Carbon\Carbon::parse($resident->date_of_birth)->age : null;
    $hasPhoto = $resident && $resident->profile_photo_url;
@endphp

<style>
    /* ============================================================
       RESIDENT PROFILE — self-contained styles (prefix: pf-)
    ============================================================ */
    .pf {
        --ink:      #0f172a;
        --muted:    #64748b;
        --faint:    #94a3b8;
        --line:     #e6eeec;
        --tile:     #f5f9f8;
        --em-50:    #ecfdf5;
        --em-100:   #d1fae5;
        --em-200:   #a7f3d0;
        --em-500:   #10b981;
        --em-600:   #059669;
        --em-700:   #047857;
        --em-900:   #064e3b;
        --gold:     #f2c46d;
        --rose:     #e11d48;

        max-width: 880px;
        margin: 0 auto;
        padding: 14px 12px 40px;
        color: var(--ink);
    }
    .pf *, .pf *::before, .pf *::after { box-sizing: border-box; }
    .pf p, .pf h1, .pf h2, .pf h3 { margin: 0; }
    @media (min-width: 640px) { .pf { padding: 28px 20px 56px; } }

    /* ---------- Mode switching ---------- */
    .pf[data-mode="view"] .pf-edit { display: none !important; }
    .pf[data-mode="edit"] .pf-view { display: none !important; }
    .pf .is-hidden { display: none !important; }

    /* ---------- Entrance ---------- */
    .pf-rise { animation: pf-rise .7s cubic-bezier(.22, 1, .36, 1) both; animation-delay: calc(var(--d, 0) * 80ms); }
    @keyframes pf-rise {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: none; }
    }
    @media (prefers-reduced-motion: reduce) { .pf-rise { animation: none; } }

    /* ---------- Alert ---------- */
    .pf-alert {
        display: flex; gap: 10px;
        margin-bottom: 12px;
        padding: 12px 14px;
        border-radius: 16px;
        background: #fff1f2;
        border: 1px solid #fecdd3;
        color: #9f1239;
        font-size: 12px;
    }
    .pf-alert i { margin-top: 2px; }
    .pf-alert b { display: block; margin-bottom: 4px; font-size: 12px; }
    .pf-alert ul { margin: 0; padding-left: 16px; font-size: 11.5px; line-height: 1.6; }

    /* ============================================================
       HERO — parang digital ID
    ============================================================ */
    .pf-hero {
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
    .pf-hero::before {
        content: '';
        position: absolute;
        right: -76px; top: -76px;
        width: 230px; height: 230px;
        border-radius: 50%;
        border: 1px solid rgba(255, 255, 255, .14);
        box-shadow: 0 0 0 30px rgba(255, 255, 255, .035), 0 0 0 60px rgba(255, 255, 255, .025);
        pointer-events: none;
    }
    .pf-hero::after {
        content: '';
        position: absolute;
        left: -50px; bottom: -70px;
        width: 180px; height: 180px;
        border-radius: 50%;
        border: 1px dashed rgba(242, 196, 109, .38);
        pointer-events: none;
    }
    .pf-hero > * { position: relative; z-index: 1; }
    @media (min-width: 640px) { .pf-hero { padding: 24px 26px 22px; border-radius: 32px; } }

    .pf-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
    .pf-eyebrow {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 9.5px; font-weight: 800;
        letter-spacing: .2em; text-transform: uppercase;
        color: var(--em-200);
    }
    .pf-eyebrow i {
        width: 6px; height: 6px; border-radius: 50%;
        background: #6ee7b7;
        box-shadow: 0 0 0 3px rgba(110, 231, 183, .25);
    }
    .pf-title { margin-top: 4px !important; font-size: 22px; font-weight: 900; letter-spacing: -.02em; line-height: 1.1; }
    @media (min-width: 640px) { .pf-title { font-size: 28px; } }

    .pf-btn-edit, .pf-editing {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 9px 15px;
        border-radius: 999px;
        font: inherit; font-size: 12px; font-weight: 800;
        color: #fff;
        white-space: nowrap;
    }
    .pf-btn-edit {
        cursor: pointer;
        background: rgba(255, 255, 255, .14);
        border: 1px solid rgba(255, 255, 255, .26);
        transition: background .25s, transform .25s;
    }
    .pf-btn-edit:hover { background: rgba(255, 255, 255, .24); transform: translateY(-1px); }
    .pf-btn-edit:active { transform: scale(.97); }
    .pf-editing {
        background: rgba(242, 196, 109, .18);
        border: 1px solid rgba(242, 196, 109, .5);
        color: #fde7b0;
    }

    /* identity */
    .pf-ident { display: flex; align-items: center; gap: 14px; margin-top: 18px; }
    @media (min-width: 640px) { .pf-ident { gap: 20px; margin-top: 24px; } }

    .pf-avatar { position: relative; flex: none; width: 84px; height: 84px; }
    @media (min-width: 640px) { .pf-avatar { width: 104px; height: 104px; } }
    .pf-photo {
        display: block;
        width: 100%; height: 100%;
        border-radius: 50%;
        object-fit: cover;
        box-shadow: 0 0 0 3px rgba(242, 196, 109, .65), 0 14px 26px -10px rgba(0, 0, 0, .55);
    }
    .pf-initials {
        display: flex; align-items: center; justify-content: center;
        font-size: 28px; font-weight: 900; color: #04382b;
        background: linear-gradient(135deg, #a7f3d0, #34d399);
    }
    .pf-file { display: none; }
    .pf-cam {
        position: absolute; right: -2px; bottom: -2px;
        width: 34px; height: 34px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 50%;
        cursor: pointer;
        font-size: 13px;
        color: var(--em-700);
        background: #fff;
        box-shadow: 0 8px 16px -4px rgba(0, 0, 0, .45);
        transition: transform .25s;
    }
    .pf-cam:hover { transform: scale(1.08); }

    .pf-who { min-width: 0; flex: 1; }
    .pf-name {
        font-size: 19px; font-weight: 900;
        letter-spacing: -.02em; line-height: 1.15;
        overflow-wrap: anywhere;
    }
    @media (min-width: 640px) { .pf-name { font-size: 26px; } }
    .pf-user {
        margin-top: 4px !important;
        font-family: ui-monospace, 'SF Mono', Menlo, monospace;
        font-size: 12px;
        color: var(--em-200);
        overflow-wrap: anywhere;
    }
    .pf-zone {
        display: inline-flex; align-items: center; gap: 6px;
        margin-top: 9px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 10.5px; font-weight: 700;
        background: rgba(0, 0, 0, .2);
        color: #e7fff5;
    }
    .pf-zone i { color: var(--gold); font-size: 9px; }

    /* stat strip */
    .pf-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        margin-top: 18px;
        border-radius: 20px;
        background: rgba(0, 0, 0, .2);
        border: 1px solid rgba(255, 255, 255, .08);
        overflow: hidden;
    }
    .pf-stat { padding: 11px 8px; text-align: center; min-width: 0; }
    .pf-stat + .pf-stat { border-left: 1px solid rgba(255, 255, 255, .09); }
    .pf-stat small {
        display: block;
        font-size: 8.5px; font-weight: 800;
        letter-spacing: .16em; text-transform: uppercase;
        color: var(--em-200); opacity: .85;
    }
    .pf-stat b {
        display: block; margin-top: 4px;
        font-size: 14px; font-weight: 800;
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .pf-stat b span { font-size: 10px; font-weight: 600; opacity: .7; margin-left: 2px; }
    @media (min-width: 640px) { .pf-stat b { font-size: 16px; } }

    /* tags */
    .pf-tags { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 14px; }
    .pf-tag {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 9.5px; font-weight: 800;
        letter-spacing: .08em; text-transform: uppercase;
        background: rgba(255, 255, 255, .1);
        border: 1px solid rgba(255, 255, 255, .16);
        color: #f0fdf9;
    }
    .pf-tag i { font-size: 9px; }
    .pf-tag.t-verified { background: rgba(110, 231, 183, .2); border-color: rgba(110, 231, 183, .45); color: #d1fae5; }
    .pf-tag.t-senior i { color: #fdba74; }
    .pf-tag.t-pwd i    { color: #67e8f9; }
    .pf-tag.t-4ps i    { color: #f9a8d4; }
    .pf-tag.t-voter i  { color: #c4b5fd; }

    /* ============================================================
       CARDS
    ============================================================ */
    .pf-card {
        margin-top: 14px;
        padding: 16px;
        border-radius: 26px;
        background: #fff;
        border: 1px solid var(--line);
        box-shadow: 0 14px 30px -24px rgba(15, 23, 42, .35);
        transition: border-color .3s, box-shadow .3s;
    }
    .pf[data-mode="edit"] .pf-card { border-color: var(--em-200); box-shadow: 0 16px 34px -22px rgba(16, 185, 129, .45); }
    @media (min-width: 640px) { .pf-card { padding: 22px; } }

    .pf-head { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
    .pf-ico {
        flex: none;
        width: 38px; height: 38px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 13px;
        font-size: 14px;
        color: var(--em-700);
        background: linear-gradient(135deg, var(--em-100), var(--em-200));
    }
    .pf-head h3 { font-size: 15px; font-weight: 900; letter-spacing: -.01em; }
    .pf-head p {
        margin-top: 2px !important;
        font-size: 10px; font-weight: 700;
        letter-spacing: .14em; text-transform: uppercase;
        color: var(--faint);
    }

    .pf-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; }
    @media (min-width: 640px) {
        .pf-grid { gap: 10px; }
        .pf-grid.cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }
    .pf-all { grid-column: 1 / -1; }

    /* ---------- Tile ---------- */
    .pf-tile {
        min-width: 0;
        padding: 10px 12px 11px;
        border-radius: 16px;
        background: var(--tile);
        border: 1px solid transparent;
        transition: background .25s, border-color .25s, box-shadow .25s;
    }
    .pf[data-mode="edit"] .pf-tile.is-input { background: #fff; border-color: var(--em-200); }
    .pf[data-mode="edit"] .pf-tile.is-input:focus-within {
        border-color: var(--em-500);
        box-shadow: 0 0 0 3px rgba(16, 185, 129, .16);
    }
    .pf[data-mode="edit"] .pf-tile.is-locked { background: #f1f5f9; border-color: #e2e8f0; }

    .pf-lbl {
        display: flex; align-items: center; gap: 6px;
        font-size: 9.5px; font-weight: 800;
        letter-spacing: .13em; text-transform: uppercase;
        color: var(--faint);
    }
    .pf-lbl i { font-size: 9px; color: var(--em-500); }
    .pf-lbl .req { color: var(--rose); letter-spacing: 0; }

    .pf-val {
        margin-top: 5px !important;
        font-size: 13.5px; font-weight: 700; line-height: 1.3;
        color: var(--ink);
        overflow-wrap: anywhere;
    }
    .pf-val.mono { font-family: ui-monospace, 'SF Mono', Menlo, monospace; font-size: 13px; }
    .pf-val.dim  { color: var(--faint); font-weight: 600; }

    .pf-in {
        display: block;
        width: 100%;
        margin-top: 4px;
        padding: 3px 0;
        border: 0; outline: 0;
        background: transparent;
        font: inherit; font-size: 14px; font-weight: 600;
        color: var(--ink);
        -webkit-appearance: none; appearance: none;
        border-radius: 0;
    }
    .pf-in::placeholder { color: #cbd5e1; font-weight: 500; }
    .pf-in:disabled { color: var(--faint); cursor: not-allowed; -webkit-text-fill-color: var(--faint); }
    textarea.pf-in { resize: none; line-height: 1.4; }
    @media (max-width: 640px) { .pf-in { font-size: 16px; } }

    .pf-sel { position: relative; }
    .pf-sel::after {
        content: '';
        position: absolute; right: 3px; top: 50%;
        width: 7px; height: 7px;
        border-right: 2px solid var(--faint);
        border-bottom: 2px solid var(--faint);
        transform: translateY(-65%) rotate(45deg);
        pointer-events: none;
    }
    .pf-sel .pf-in { padding-right: 20px; cursor: pointer; }

    /* ============================================================
       ACTION BAR
    ============================================================ */
    .pf-actions {
        position: sticky;
        bottom: 12px;
        z-index: 30;
        display: flex; gap: 10px;
        margin-top: 14px;
        padding: 10px;
        border-radius: 22px;
        background: #0f172a;
        box-shadow: 0 22px 40px -14px rgba(15, 23, 42, .65);
    }
    .pf-actions button {
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        padding: 13px 16px;
        border: 0; border-radius: 15px;
        font: inherit; font-size: 12.5px; font-weight: 800;
        letter-spacing: .04em;
        cursor: pointer;
        transition: transform .2s, background .2s;
    }
    .pf-actions button:active { transform: scale(.97); }
    .pf-cancel { color: #cbd5e1; background: rgba(255, 255, 255, .08); }
    .pf-cancel:hover { background: rgba(255, 255, 255, .15); }
    .pf-save {
        flex: 1;
        color: #fff;
        background: linear-gradient(135deg, #10b981, #059669 55%, #0d9488);
        box-shadow: 0 10px 20px -8px rgba(16, 185, 129, .7), inset 0 1px 0 rgba(255, 255, 255, .25);
    }
    .pf-save:hover { filter: brightness(1.06); }
    @media (min-width: 640px) {
        .pf-actions { justify-content: flex-end; }
        .pf-save { flex: none; padding-left: 28px; padding-right: 28px; }
    }

    .pf-foot {
        margin-top: 18px;
        text-align: center;
        font-size: 10px; font-weight: 700;
        letter-spacing: .16em; text-transform: uppercase;
        color: var(--faint);
    }
</style>

<div class="pf" id="pf" data-mode="view">

    {{-- VALIDATION ERRORS --}}
    @if($errors->any())
        <div class="pf-alert pf-rise" role="alert">
            <i class="fas fa-exclamation-circle"></i>
            <div>
                <b>Please fix the following errors:</b>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('resident.profile.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">
        @csrf
        @method('PUT') {{-- ITO ANG BAGONG DAGDAG --}}

        {{-- ================= HERO / DIGITAL ID ================= --}}
        <header class="pf-hero pf-rise">
            <div class="pf-top">
                <div>
                    <span class="pf-eyebrow"><i></i>Resident Portal</span>
                    <h1 class="pf-title">My Profile</h1>
                </div>

                <div id="headerButtons">
                    <button type="button" id="editBtn" class="pf-btn-edit pf-view" onclick="enterEditMode()">
                        <i class="fas fa-edit"></i> Edit Profile
                    </button>
                    <span class="pf-editing pf-edit">
                        <i class="fas fa-pen"></i> Editing
                    </span>
                </div>
            </div>

            <div class="pf-ident">
                <div class="pf-avatar">
                    <img id="photoPreview"
                         src="{{ $resident->profile_photo_url ?? '' }}"
                         alt="Profile Photo"
                         class="pf-photo {{ $hasPhoto ? '' : 'is-hidden' }}">
                    <div id="photoPlaceholder" class="pf-photo pf-initials {{ $hasPhoto ? 'is-hidden' : '' }}">
                        {{ $initials }}
                    </div>

                    <label for="profile_photo" id="cameraOverlay" class="pf-cam pf-edit" title="Change photo">
                        <i class="fas fa-camera"></i>
                    </label>
                    <input type="file" name="profile_photo" id="profile_photo" accept="image/*" class="pf-file">
                </div>

                <div class="pf-who">
                    <h2 class="pf-name">{{ $fullName ?: '—' }}</h2>
                    <p class="pf-user">{{ $resident->email ?? '—' }}</p>
                    <span class="pf-zone">
                        <i class="fas fa-map-pin"></i>
                        {{ $resident->zone_purok ?? 'Zone —' }} · Bactad East
                    </span>
                </div>
            </div>

            <div class="pf-stats">
                <div class="pf-stat">
                    <small>Age</small>
                    <b>@if($age !== null){{ $age }}<span>yrs</span>@else — @endif</b>
                </div>
                <div class="pf-stat">
                    <small>Gender</small>
                    <b>{{ $resident->gender ?? '—' }}</b>
                </div>
                <div class="pf-stat">
                    <small>Status</small>
                    <b>{{ $resident->civil_status ?? '—' }}</b>
                </div>
            </div>

            <div class="pf-tags">
                <span class="pf-tag t-verified"><i class="fas fa-check-circle"></i> Verified</span>
                @if($resident && $resident->is_senior)
                    <span class="pf-tag t-senior"><i class="fas fa-user-clock"></i> Senior</span>
                @endif
                @if($resident && $resident->is_pwd)
                    <span class="pf-tag t-pwd"><i class="fas fa-wheelchair"></i> PWD</span>
                @endif
                @if($resident && $resident->is_4ps)
                    <span class="pf-tag t-4ps"><i class="fas fa-hand-holding-heart"></i> 4Ps</span>
                @endif
                @if($resident && $resident->is_voter)
                    <span class="pf-tag t-voter"><i class="fas fa-vote-yea"></i> Voter</span>
                @endif
            </div>
        </header>

        {{-- ================= PERSONAL INFORMATION ================= --}}
        <section class="pf-card pf-rise" style="--d:1" id="pfPersonal">
            <div class="pf-head">
                <span class="pf-ico"><i class="fas fa-user-circle"></i></span>
                <div>
                    <h3>Personal Information</h3>
                    <p>Pangunahing impormasyon</p>
                </div>
            </div>

            <div class="pf-grid cols-3">

                {{-- First name --}}
                <div class="pf-tile is-input">
                    <span class="pf-lbl"><i class="fas fa-user"></i> First <b class="req pf-edit">*</b></span>
                    <p class="pf-val pf-view">{{ $resident->first_name ?? '—' }}</p>
                    <input type="text" name="first_name" class="pf-in pf-edit"
                           value="{{ old('first_name', $resident->first_name) }}" required>
                </div>

                {{-- Middle name --}}
                <div class="pf-tile is-input">
                    <span class="pf-lbl"><i class="fas fa-user"></i> Middle</span>
                    <p class="pf-val pf-view">{{ $resident->middle_name ?? '—' }}</p>
                    <input type="text" name="middle_name" class="pf-in pf-edit"
                           value="{{ old('middle_name', $resident->middle_name) }}" placeholder="Santos">
                </div>

                {{-- Last name --}}
                <div class="pf-tile is-input">
                    <span class="pf-lbl"><i class="fas fa-user"></i> Last <b class="req pf-edit">*</b></span>
                    <p class="pf-val pf-view">{{ $resident->last_name ?? '—' }}</p>
                    <input type="text" name="last_name" class="pf-in pf-edit"
                           value="{{ old('last_name', $resident->last_name) }}" required>
                </div>

                {{-- Email (hindi mababago) --}}
                <div class="pf-tile is-locked">
                    <span class="pf-lbl"><i class="fas fa-envelope"></i> Email</span>
                    <p class="pf-val mono pf-view">{{ $resident->email ?? '—' }}</p>
                    <input type="email" class="pf-in pf-edit" value="{{ $resident->email }}" disabled>
                </div>

                {{-- Age (view only) --}}
                <div class="pf-tile pf-view">
                    <span class="pf-lbl"><i class="fas fa-birthday-cake"></i> Age</span>
                    <p class="pf-val">{{ $age !== null ? $age . ' yrs old' : '—' }}</p>
                </div>

                {{-- Birthdate --}}
                <div class="pf-tile is-input">
                    <span class="pf-lbl"><i class="fas fa-calendar-alt"></i> Birthdate</span>
                    <p class="pf-val pf-view">{{ ($resident && $resident->date_of_birth) ? \Carbon\Carbon::parse($resident->date_of_birth)->format('M d, Y') : '—' }}</p>
                    <input type="date" name="date_of_birth" class="pf-in pf-edit"
                           value="{{ old('date_of_birth', $resident->date_of_birth ? \Carbon\Carbon::parse($resident->date_of_birth)->format('Y-m-d') : '') }}">
                </div>

                {{-- Gender --}}
                <div class="pf-tile is-input">
                    <span class="pf-lbl"><i class="fas fa-venus-mars"></i> Gender</span>
                    <p class="pf-val pf-view">{{ $resident->gender ?? '—' }}</p>
                    <div class="pf-sel pf-edit">
                        <select name="gender" class="pf-in">
                            <option value="">Select Gender</option>
                            <option value="Male" {{ old('gender', $resident->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender', $resident->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                </div>

                {{-- Civil status --}}
                <div class="pf-tile is-input">
                    <span class="pf-lbl"><i class="fas fa-ring"></i> Civil Status</span>
                    <p class="pf-val pf-view">{{ $resident->civil_status ?? '—' }}</p>
                    <div class="pf-sel pf-edit">
                        <select name="civil_status" class="pf-in">
                            <option value="">Select Status</option>
                            <option value="Single" {{ old('civil_status', $resident->civil_status) == 'Single' ? 'selected' : '' }}>Single</option>
                            <option value="Married" {{ old('civil_status', $resident->civil_status) == 'Married' ? 'selected' : '' }}>Married</option>
                            <option value="Widowed" {{ old('civil_status', $resident->civil_status) == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                            <option value="Separated" {{ old('civil_status', $resident->civil_status) == 'Separated' ? 'selected' : '' }}>Separated</option>
                        </select>
                    </div>
                </div>

                {{-- Contact --}}
                <div class="pf-tile is-input">
                    <span class="pf-lbl"><i class="fas fa-phone-alt"></i> Contact</span>
                    <p class="pf-val pf-view">{{ $resident->contact_number ?? '—' }}</p>
                    <input type="tel" name="contact_number" class="pf-in pf-edit"
                           value="{{ old('contact_number', $resident->contact_number) }}" placeholder="09XX XXX XXXX">
                </div>

                {{-- Occupation --}}
                <div class="pf-tile is-input pf-all">
                    <span class="pf-lbl"><i class="fas fa-briefcase"></i> Occupation</span>
                    <p class="pf-val pf-view">{{ $resident->occupation ?? 'None' }}</p>
                    <input type="text" name="occupation" class="pf-in pf-edit"
                           value="{{ old('occupation', $resident->occupation) }}" placeholder="e.g., Farmer">
                </div>
            </div>
        </section>

        {{-- ================= ADDRESS ================= --}}
        <section class="pf-card pf-rise" style="--d:2">
            <div class="pf-head">
                <span class="pf-ico"><i class="fas fa-map-marker-alt"></i></span>
                <div>
                    <h3>Address &amp; Barangay</h3>
                    <p>Tirahan at detalye</p>
                </div>
            </div>

            <div class="pf-grid">

                {{-- Zone / Purok --}}
                <div class="pf-tile is-input pf-edit-all">
                    <span class="pf-lbl"><i class="fas fa-map-pin"></i> Zone / Purok</span>
                    <p class="pf-val pf-view">{{ $resident->zone_purok ?? '—' }}</p>
                    <div class="pf-sel pf-edit">
                        <select name="zone_purok" class="pf-in">
                            <option value="">Select Zone</option>
                            @for($i = 1; $i <= 7; $i++)
                                <option value="Zone {{ $i }}" {{ old('zone_purok', $resident->zone_purok) == "Zone $i" ? 'selected' : '' }}>Zone {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                {{-- Barangay (view only) --}}
                <div class="pf-tile pf-view">
                    <span class="pf-lbl"><i class="fas fa-map"></i> Barangay</span>
                    <p class="pf-val">Bactad East</p>
                </div>

                {{-- Complete address --}}
                <div class="pf-tile is-input pf-all">
                    <span class="pf-lbl"><i class="fas fa-home"></i> Complete Address</span>
                    <p class="pf-val pf-view">{{ $resident->complete_address ?? '—' }}</p>
                    <textarea name="complete_address" rows="2" class="pf-in pf-edit"
                              placeholder="House #, Street, Barangay">{{ old('complete_address', $resident->complete_address) }}</textarea>
                </div>

                {{-- City (view only) --}}
                <div class="pf-tile pf-all pf-view">
                    <span class="pf-lbl"><i class="fas fa-city"></i> City / Municipality</span>
                    <p class="pf-val">Urdaneta City, Pangasinan</p>
                </div>
            </div>
        </section>

        {{-- ================= ACTION BAR ================= --}}
        <div id="formActions" class="pf-actions pf-edit">
            <button type="button" class="pf-cancel" onclick="exitEditMode()">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button type="submit" class="pf-save">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </div>
    </form>

    <p class="pf-foot">Bactad East BIMS</p>
</div>

<script>
    (function () {
        const root        = document.getElementById('pf');
        const form        = document.getElementById('profileForm');
        const input       = document.getElementById('profile_photo');
        const preview     = document.getElementById('photoPreview');
        const placeholder = document.getElementById('photoPlaceholder');

        const originalSrc = preview.getAttribute('src');
        const hadPhoto    = !!originalSrc;

        input?.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (event) {
                preview.src = event.target.result;
                preview.classList.remove('is-hidden');
                placeholder.classList.add('is-hidden');
            };
            reader.readAsDataURL(file);
        });

        window.enterEditMode = function () {
            root.dataset.mode = 'edit';
        };

        window.exitEditMode = function () {
            root.dataset.mode = 'view';
            form.reset();
            if (hadPhoto) {
                preview.src = originalSrc;
                preview.classList.remove('is-hidden');
                placeholder.classList.add('is-hidden');
            } else {
                preview.removeAttribute('src');
                preview.classList.add('is-hidden');
                placeholder.classList.remove('is-hidden');
            }
            window.scrollTo({ top: 0, behavior: 'smooth' });
        };

        @if($errors->any())
            root.dataset.mode = 'edit';
        @endif
    })();
</script>
@endsection