@extends('layouts.admin')

@section('content')
@php
    /* =====================================================================
       SORTING: A-Z base sa Last name, tapos First name, tapos Middle name
       ===================================================================== */
    $residents = $residents
        ->sortBy(
            fn($r) => mb_strtolower(trim($r->last_name . ' ' . $r->first_name . ' ' . $r->middle_name)),
            SORT_NATURAL | SORT_FLAG_CASE
        )
        ->values();

    /* =====================================================================
       DATA HELPERS (walang binago sa logic — dinagdagan lang)
       ===================================================================== */

    // Senior = 60+ base sa birthday (kahit hindi pa naka-flag sa database)
    $isSenior = fn($r) => $r->is_senior || ($r->date_of_birth && $r->date_of_birth->age >= 60);

    // Account badge: Registered / Pending approval / Rejected / Not registered
    $accountBadge = function ($r) {
        if (!$r->email)                 return ['Not registered', 'fa-user-slash', 'bg-slate-100 text-slate-600 ring-slate-200', 'bg-slate-400'];
        if ($r->status === 'pending')   return ['Pending approval', 'fa-hourglass-half', 'bg-amber-50 text-amber-700 ring-amber-200', 'bg-amber-500'];
        if ($r->status === 'rejected')  return ['Rejected', 'fa-ban', 'bg-rose-50 text-rose-700 ring-rose-200', 'bg-rose-500'];
        return ['Registered', 'fa-user-check', 'bg-emerald-50 text-emerald-700 ring-emerald-200', 'bg-emerald-500'];
    };

    // Maikling detalye sa ilalim ng pangalan: edad · kasarian · hanapbuhay (kung meron)
    $meta = function ($r) {
        $p = [];
        if ($r->date_of_birth) $p[] = $r->date_of_birth->age . ' yrs';
        $g = strtoupper(substr((string) $r->gender, 0, 1));
        if ($g === 'M') $p[] = 'Male'; elseif ($g === 'F') $p[] = 'Female';
        if (!empty($r->occupation)) $p[] = $r->occupation;
        return $p ? implode(' · ', $p) : '—';
    };

    // Initials para sa avatar kapag walang photo
    $initials = fn($r) => strtoupper(
        substr($r->first_name, 0, 1)
        . ($r->middle_name ? substr($r->middle_name, 0, 1) : '')
        . substr($r->last_name, 0, 1)
    );

    // Bawat resident may sariling kulay ng avatar (stable — base sa pangalan)
    $avatarPalette = [
        'from-emerald-400 to-teal-600',
        'from-sky-400 to-indigo-600',
        'from-amber-400 to-orange-600',
        'from-fuchsia-400 to-purple-600',
        'from-rose-400 to-pink-600',
        'from-cyan-400 to-blue-600',
        'from-lime-400 to-green-600',
    ];
    $avatarClass = fn($r) => $avatarPalette[abs(crc32($r->last_name . $r->first_name)) % count($avatarPalette)];

    /* =====================================================================
       COUNTS
       ===================================================================== */
    $totalAll     = $residents->count();
    $totalSeniors = $residents->filter($isSenior)->count();
    $totalPwd     = $residents->where('is_pwd', true)->count();
    $totalVoters  = $residents->where('is_voter', true)->count();

    $pct = fn($n) => $totalAll > 0 ? round(($n / $totalAll) * 100) : 0;

    // Account overview
    $accNotRegistered = $residents->filter(fn($r) => !$r->email)->count();
    $accPending       = $residents->filter(fn($r) => $r->email && $r->status === 'pending')->count();
    $accRejected      = $residents->filter(fn($r) => $r->email && $r->status === 'rejected')->count();
    $accRegistered    = $totalAll - $accNotRegistered - $accPending - $accRejected;

    $accounts = [
        ['label' => 'Registered',     'value' => $accRegistered,    'icon' => 'fa-user-check',     'dot' => 'bg-emerald-500', 'text' => 'text-emerald-700'],
        ['label' => 'Pending',        'value' => $accPending,       'icon' => 'fa-hourglass-half', 'dot' => 'bg-amber-500',   'text' => 'text-amber-700'],
        ['label' => 'Rejected',       'value' => $accRejected,      'icon' => 'fa-ban',            'dot' => 'bg-rose-500',    'text' => 'text-rose-700'],
        ['label' => 'Not registered', 'value' => $accNotRegistered, 'icon' => 'fa-user-slash',     'dot' => 'bg-slate-400',   'text' => 'text-slate-600'],
    ];

    /* =====================================================================
       UI CONFIG
       ===================================================================== */
    $field = 'w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder:text-slate-400 focus:ring-4 focus:ring-emerald-500/15 focus:border-emerald-500 outline-none transition shadow-sm shadow-slate-900/[0.03]';

    // Classification pills (same colors everywhere on this page)
    $tagConfig = [
        'is_deceased'    => ['label' => 'Deceased', 'icon' => 'fa-cross', 'cls' => 'bg-slate-800 text-white ring-slate-900'], // <-- IDINAGDAG
        'is_senior'      => ['label' => 'Senior', 'icon' => 'fa-user-clock', 'cls' => 'bg-orange-50 text-orange-700 ring-orange-200'],
        'is_4ps'         => ['label' => '4Ps',    'icon' => 'fa-hand-holding-heart', 'cls' => 'bg-pink-50 text-pink-700 ring-pink-200'],
        'is_pwd'         => ['label' => 'PWD',    'icon' => 'fa-wheelchair', 'cls' => 'bg-cyan-50 text-cyan-700 ring-cyan-200'],
        'is_solo_parent' => ['label' => 'Solo',   'icon' => 'fa-child-reaching', 'cls' => 'bg-rose-50 text-rose-700 ring-rose-200'],
        'is_voter'       => ['label' => 'Voter',  'icon' => 'fa-vote-yea', 'cls' => 'bg-purple-50 text-purple-700 ring-purple-200'],
    ];

    // Lahat ng tags na tugma sa isang resident
    $tagsFor = function ($r) use ($tagConfig, $isSenior) {
        $out = [];
        foreach ($tagConfig as $key => $tag) {
            if ($key === 'is_senior' ? $isSenior($r) : $r->{$key}) {
                $out[] = $tag;
            }
        }
        return $out;
    };

    $classOptions = [
        'deceased'    => 'Deceased', // <-- IDINAGDAG
        'senior'      => 'Senior',
        '4ps'         => '4Ps',
        'pwd'         => 'PWD',
        'solo_parent' => 'Solo parent',
        'voter'       => 'Voter',
    ];

    $stats = [
        ['label' => 'Total residents', 'short' => 'Total',   'icon' => 'fa-users',      'value' => $totalAll,     'sub' => 'in this list',              'pct' => 100,               'bg' => 'from-emerald-500 to-emerald-800', 'shadow' => 'shadow-emerald-600/25'],
        ['label' => 'Senior citizens', 'short' => 'Seniors', 'icon' => 'fa-user-clock', 'value' => $totalSeniors, 'sub' => $pct($totalSeniors) . '% of total', 'pct' => $pct($totalSeniors), 'bg' => 'from-orange-400 to-orange-700',   'shadow' => 'shadow-orange-500/25'],
        ['label' => 'PWD',             'short' => 'PWD',     'icon' => 'fa-wheelchair', 'value' => $totalPwd,     'sub' => $pct($totalPwd) . '% of total',     'pct' => $pct($totalPwd),     'bg' => 'from-cyan-400 to-blue-700',       'shadow' => 'shadow-cyan-500/25'],
        ['label' => 'Registered voters','short' => 'Voters', 'icon' => 'fa-vote-yea',   'value' => $totalVoters,  'sub' => $pct($totalVoters) . '% of total',  'pct' => $pct($totalVoters),  'bg' => 'from-violet-500 to-purple-800',   'shadow' => 'shadow-violet-500/25'],
    ];

    $hasFilters = request()->filled('search') || request()->filled('zone') || request()->filled('classification');
@endphp

<div class="bims-page min-h-screen bg-gradient-to-br from-slate-50 via-emerald-50/40 to-teal-50/30">

    {{-- ============================================================
         HEADER
         ============================================================ --}}
    <div class="bims-noprint relative overflow-hidden bg-gradient-to-br from-emerald-700 via-emerald-800 to-teal-900">
        {{-- dotted texture --}}
        <div class="absolute inset-0 opacity-[0.18]"
             style="background-image: radial-gradient(rgba(255,255,255,.55) 1px, transparent 1px); background-size: 18px 18px;"></div>
        {{-- glow blobs --}}
        <div class="absolute -top-16 -right-10 w-64 h-64 bg-emerald-300/25 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 left-1/4 w-64 h-64 bg-teal-300/20 rounded-full blur-3xl"></div>
        <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-white/40 to-transparent"></div>

        <div class="relative px-3 sm:px-6 lg:px-8 py-4 sm:py-5">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="relative h-11 w-11 sm:h-12 sm:w-12 flex items-center justify-center rounded-2xl bg-white/15 border border-white/25 text-white flex-shrink-0 backdrop-blur-sm shadow-lg shadow-emerald-950/20">
                        <i class="fas fa-users text-lg"></i>
                        <span class="absolute -bottom-1 -right-1 h-3.5 w-3.5 rounded-full bg-emerald-300 ring-2 ring-emerald-800"></span>
                    </div>
                    <div class="min-w-0">
                        <nav class="flex items-center gap-1.5 text-xs text-emerald-200/80" aria-label="Breadcrumb">
                            <i class="fas fa-house text-[9px]"></i>
                            <span>Dashboard</span>
                            <i class="fas fa-chevron-right text-[8px]"></i>
                            <span class="text-white font-medium">Residents</span>
                        </nav>
                        <h1 class="bims-display text-xl sm:text-3xl text-white truncate">Master list of residents</h1>
                        <p class="hidden sm:block text-xs text-emerald-100/70 mt-1">Tingnan, i-filter at i-manage ang lahat ng naka-record na residente ng barangay.</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="hidden md:inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/10 border border-white/15 text-xs font-medium text-emerald-50 backdrop-blur-sm">
                        <i class="far fa-calendar text-[11px]"></i>{{ now()->format('M d, Y') }}
                    </span>
                    {{-- TINANGGAL ANG PRINT BUTTON DITO --}}
                </div>
            </div>
        </div>

        {{-- wave edge --}}
        <svg class="relative block w-full h-3 sm:h-4 text-slate-50" viewBox="0 0 1200 20" preserveAspectRatio="none" aria-hidden="true">
            <path d="M0,20 L0,8 Q300,-6 600,8 T1200,8 L1200,20 Z" fill="currentColor" opacity=".9"></path>
        </svg>
    </div>

    {{-- ============================================================
         ALERTS
         ============================================================ --}}
    @if(session('success') || session('error'))
        <div class="bims-noprint px-3 sm:px-6 lg:px-8 pt-3 space-y-2">
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     role="status"
                     class="flex items-center gap-3 px-4 py-3 bg-white border border-emerald-200 border-l-4 border-l-emerald-500 rounded-xl shadow-sm shadow-emerald-900/5">
                    <span class="h-8 w-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0"><i class="fas fa-check text-xs"></i></span>
                    <span class="text-sm font-medium text-emerald-900 flex-1">{{ session('success') }}</span>
                    <button @click="show = false" class="text-emerald-400 hover:text-emerald-600 transition" aria-label="Dismiss"><i class="fas fa-times text-xs"></i></button>
                </div>
            @endif
            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     role="alert"
                     class="flex items-center gap-3 px-4 py-3 bg-white border border-rose-200 border-l-4 border-l-rose-500 rounded-xl shadow-sm shadow-rose-900/5">
                    <span class="h-8 w-8 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0"><i class="fas fa-exclamation text-xs"></i></span>
                    <span class="text-sm font-medium text-rose-900 flex-1">{{ session('error') }}</span>
                    <button @click="show = false" class="text-rose-400 hover:text-rose-600 transition" aria-label="Dismiss"><i class="fas fa-times text-xs"></i></button>
                </div>
            @endif
        </div>
    @endif

    {{-- ============================================================
         MAIN
         ============================================================ --}}
    <div class="px-3 sm:px-6 lg:px-8 py-3 sm:py-5 space-y-3 sm:space-y-5">

        {{-- ===== STATS (4 across, even on phones) ===== --}}
        <div class="bims-stats grid grid-cols-4 gap-2 sm:gap-4">
            @foreach($stats as $s)
                <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-gradient-to-br {{ $s['bg'] }} px-2.5 py-2.5 sm:px-5 sm:py-4 text-white shadow-lg {{ $s['shadow'] }} ring-1 ring-white/10 transition duration-300 hover:-translate-y-1 hover:shadow-xl">
                    <div class="absolute -top-10 -right-10 w-28 h-28 rounded-full bg-white/10 transition-transform duration-500 group-hover:scale-125"></div>
                    <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/50 to-transparent"></div>
                    <i class="fas {{ $s['icon'] }} absolute -bottom-2 -right-1 text-4xl sm:text-6xl text-white/15 group-hover:scale-110 group-hover:-rotate-6 transition-transform duration-500" aria-hidden="true"></i>

                    <div class="relative">
                        <div class="flex items-center gap-1.5">
                            <span class="hidden sm:flex h-6 w-6 items-center justify-center rounded-lg bg-white/20 text-[10px]"><i class="fas {{ $s['icon'] }}"></i></span>
                            <p class="text-[11px] sm:text-xs font-semibold text-white/90 leading-tight">
                                <span class="sm:hidden">{{ $s['short'] }}</span>
                                <span class="hidden sm:inline">{{ $s['label'] }}</span>
                            </p>
                        </div>
                        <p class="bims-display bims-figure text-xl sm:text-4xl mt-1 sm:mt-2">{{ number_format($s['value']) }}</p>

                        <div class="hidden sm:block mt-2.5">
                            <div class="h-1 w-full rounded-full bg-white/20 overflow-hidden">
                                <div class="bims-bar h-full rounded-full bg-white/80" style="width: {{ $s['pct'] }}%"></div>
                            </div>
                            <p class="mt-1.5 text-[11px] text-white/75 font-medium">{{ $s['sub'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ===== ACCOUNT OVERVIEW ===== --}}
        <div class="bims-noprint bg-white border border-slate-200/80 rounded-2xl shadow-sm shadow-slate-900/[0.03] px-4 py-3 sm:px-5 sm:py-3.5">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:gap-6">
                <div class="flex items-center gap-2.5 lg:w-44 flex-shrink-0">
                    <div class="h-8 w-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center"><i class="fas fa-id-card text-xs"></i></div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800 leading-tight">Account status</p>
                        <p class="text-[11px] text-slate-400 leading-tight">Online registration</p>
                    </div>
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex h-2.5 w-full rounded-full overflow-hidden bg-slate-100 ring-1 ring-inset ring-slate-200/60">
                        @foreach($accounts as $a)
                            @if($a['value'] > 0)
                                <div class="{{ $a['dot'] }} bims-bar h-full" style="width: {{ $pct($a['value']) }}%" title="{{ $a['label'] }}: {{ $a['value'] }}"></div>
                            @endif
                        @endforeach
                    </div>
                    <div class="mt-2.5 grid grid-cols-2 sm:grid-cols-4 gap-x-4 gap-y-1.5">
                        @foreach($accounts as $a)
                            <div class="flex items-center gap-2 text-xs">
                                <span class="h-2 w-2 rounded-full {{ $a['dot'] }} flex-shrink-0"></span>
                                <span class="text-slate-500 truncate">{{ $a['label'] }}</span>
                                <span class="bims-figure font-bold {{ $a['text'] }} ml-auto sm:ml-0">{{ number_format($a['value']) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== FILTERS ===== --}}
        <div class="bims-noprint bg-white border border-slate-200/80 rounded-2xl shadow-sm shadow-slate-900/[0.03] overflow-hidden">
            <div class="h-1 bg-gradient-to-r from-emerald-400 via-teal-400 to-cyan-400"></div>
            <form method="GET" action="{{ route('admin.residents.index') }}" class="p-3 sm:p-4">
                <div class="grid grid-cols-12 gap-2.5 sm:gap-3 items-end">

                    <div class="col-span-12 lg:col-span-5">
                        <label for="f-search" class="block text-xs font-semibold text-slate-600 mb-1.5">Search</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                            <input id="f-search" type="text" name="search" value="{{ request('search') }}" placeholder="Name or email"
                                   class="{{ $field }} pl-9">
                        </div>
                    </div>

                    <div class="col-span-7 lg:col-span-3">
                        <label for="f-zone" class="block text-xs font-semibold text-slate-600 mb-1.5">Zone / Purok</label>
                        <div class="relative">
                            <i class="fas fa-map-pin absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                            <select id="f-zone" name="zone" class="{{ $field }} pl-9 pr-8 appearance-none cursor-pointer">
                                <option value="">All zones</option>
                                @foreach($zones as $zone)
                                    <option value="{{ $zone }}" {{ request('zone') == $zone ? 'selected' : '' }}>{{ $zone }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
                        </div>
                    </div>

                    <div class="col-span-5 lg:col-span-2">
                        <label for="f-class" class="block text-xs font-semibold text-slate-600 mb-1.5">Classification</label>
                        <div class="relative">
                            <select id="f-class" name="classification" class="{{ $field }} pr-8 appearance-none cursor-pointer">
                                <option value="">All</option>
                                @foreach($classOptions as $val => $text)
                                    <option value="{{ $val }}" {{ request('classification') == $val ? 'selected' : '' }}>{{ $text }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
                        </div>
                    </div>

                    <div class="col-span-12 lg:col-span-2 flex items-center gap-2">
                        <button type="submit"
                                class="flex-1 px-4 py-2 bg-gradient-to-br from-emerald-500 to-emerald-700 hover:from-emerald-600 hover:to-emerald-800 active:scale-[.98] text-white text-sm font-semibold rounded-xl shadow-md shadow-emerald-600/30 transition flex items-center justify-center gap-1.5 focus:outline-none focus:ring-4 focus:ring-emerald-500/25">
                            <i class="fas fa-filter text-[11px]"></i> Filter
                        </button>
                        <a href="{{ route('admin.residents.index') }}" title="Reset filters"
                           class="px-3.5 py-2 bg-white border border-slate-300 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-50 hover:border-slate-400 active:scale-[.98] transition flex items-center justify-center gap-1.5 shadow-sm">
                            <i class="fas fa-rotate-left text-[11px]"></i><span class="lg:hidden">Reset</span>
                        </a>
                    </div>
                </div>

                @if($hasFilters)
                    <div class="mt-3 pt-3 border-t border-dashed border-slate-200 flex flex-wrap items-center gap-1.5">
                        <span class="text-xs font-semibold text-slate-400 mr-1"><i class="fas fa-sliders text-[10px] mr-1"></i>Active filters:</span>
                        @if(request('search'))
                            <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 text-xs font-medium pl-2.5 pr-2 py-1 rounded-full ring-1 ring-inset ring-emerald-200">
                                <i class="fas fa-search text-[9px]"></i> "{{ request('search') }}"
                                <a href="{{ route('admin.residents.index', array_merge(request()->except('search'), ['search' => null])) }}" class="h-4 w-4 flex items-center justify-center rounded-full hover:bg-emerald-200/70 hover:text-emerald-900 transition" aria-label="Remove search filter"><i class="fas fa-times text-[9px]"></i></a>
                            </span>
                        @endif
                        @if(request('zone'))
                            <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 text-xs font-medium pl-2.5 pr-2 py-1 rounded-full ring-1 ring-inset ring-emerald-200">
                                <i class="fas fa-map-pin text-[9px]"></i> {{ request('zone') }}
                                <a href="{{ route('admin.residents.index', array_merge(request()->except('zone'), ['zone' => null])) }}" class="h-4 w-4 flex items-center justify-center rounded-full hover:bg-emerald-200/70 hover:text-emerald-900 transition" aria-label="Remove zone filter"><i class="fas fa-times text-[9px]"></i></a>
                            </span>
                        @endif
                        @if(request('classification'))
                            <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 text-xs font-medium pl-2.5 pr-2 py-1 rounded-full ring-1 ring-inset ring-emerald-200">
                                <i class="fas fa-tag text-[9px]"></i> {{ ucfirst(str_replace('_', ' ', request('classification'))) }}
                                <a href="{{ route('admin.residents.index', array_merge(request()->except('classification'), ['classification' => null])) }}" class="h-4 w-4 flex items-center justify-center rounded-full hover:bg-emerald-200/70 hover:text-emerald-900 transition" aria-label="Remove classification filter"><i class="fas fa-times text-[9px]"></i></a>
                            </span>
                        @endif
                        <a href="{{ route('admin.residents.index') }}" class="ml-auto text-xs font-semibold text-slate-400 hover:text-rose-600 transition">Clear all</a>
                    </div>
                @endif
            </form>
        </div>

        {{-- ===== DIRECTORY ===== --}}
        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm shadow-slate-900/[0.03] overflow-hidden">

            {{-- directory header --}}
            <div class="px-4 py-3 border-b border-slate-100 bg-gradient-to-r from-emerald-50 via-white to-white flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-700 flex items-center justify-center text-white shadow-md shadow-emerald-500/30">
                        <i class="fas fa-list text-xs"></i>
                    </div>
                    <div>
                        <h2 class="bims-display text-base sm:text-lg text-slate-800">Resident directory</h2>
                        <p class="text-xs text-slate-500 leading-tight">
                            <span class="bims-figure font-semibold text-slate-700">{{ number_format($totalAll) }}</span>
                            {{ $totalAll === 1 ? 'record' : 'records' }} found
                        </p>
                    </div>
                </div>

                <span class="bims-noprint inline-flex items-center gap-1.5 text-xs font-medium text-slate-400">
                    <i class="fas fa-arrow-down-a-z text-[11px]"></i> Sorted A–Z by last name
                </span>
            </div>

            @if($residents->isEmpty())
                {{-- ---------- EMPTY STATE ---------- --}}
                <div class="px-4 py-16 text-center">
                    <div class="relative h-20 w-20 mx-auto mb-4">
                        <div class="absolute inset-0 rounded-full bg-gradient-to-br from-emerald-100 to-teal-100 bims-pulse"></div>
                        <div class="relative h-20 w-20 rounded-full bg-white border border-slate-200 shadow-sm flex items-center justify-center">
                            <i class="fas fa-user-magnifying-glass text-2xl text-slate-300"></i>
                        </div>
                    </div>
                    <h3 class="bims-display text-lg text-slate-700">No residents found</h3>
                    <p class="text-sm text-slate-500 mt-1 mb-5">
                        {{ $hasFilters ? 'Walang tugma sa mga napiling filter. Subukang baguhin o i-reset ang mga ito.' : 'Wala pang naka-record na resident.' }}
                    </p>
                    @if($hasFilters)
                        <a href="{{ route('admin.residents.index') }}"
                           class="bims-noprint inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50 hover:border-emerald-400 hover:text-emerald-700 transition shadow-sm">
                            <i class="fas fa-rotate-left text-xs"></i> Reset filters
                        </a>
                    @endif
                </div>
            @else

                {{-- ---------- TABLE (lahat ng screen size; may horizontal scroll sa maliit na screen) ---------- --}}
                <div class="bims-table overflow-x-auto">
                    <table class="w-full text-left border-separate border-spacing-0 min-w-[860px]">
                        <thead>
                            <tr class="text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                <th class="sticky top-0 bg-slate-50/90 backdrop-blur px-4 py-3 w-14 text-center border-b border-slate-200">#</th>
                                <th class="sticky top-0 bg-slate-50/90 backdrop-blur px-4 py-3 border-b border-slate-200">Full name</th>
                                <th class="sticky top-0 bg-slate-50/90 backdrop-blur px-4 py-3 border-b border-slate-200">Zone / Purok</th>
                                <th class="sticky top-0 bg-slate-50/90 backdrop-blur px-4 py-3 border-b border-slate-200">Account</th>
                                <th class="sticky top-0 bg-slate-50/90 backdrop-blur px-4 py-3 border-b border-slate-200">Classifications</th>
                                <th class="bims-noprint sticky top-0 bg-slate-50/90 backdrop-blur px-4 py-3 text-center w-36 border-b border-slate-200">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($residents as $index => $res)
                                @php
                                    [$bLabel, $bIcon, $bCls, $bDot] = $accountBadge($res);
                                    $resTags = $tagsFor($res);
                                @endphp
                                <tr class="bims-row group transition-colors hover:bg-emerald-50/50"
                                    style="animation-delay: {{ min($index, 14) * 25 }}ms">
                                    <td class="px-4 py-3 text-xs text-slate-400 font-mono text-center border-b border-slate-100 group-last:border-0">
                                        <span class="inline-flex h-6 min-w-[1.75rem] px-1 items-center justify-center rounded-md bg-slate-50 group-hover:bg-white group-hover:text-emerald-600 transition">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                    </td>

                                    <td class="px-4 py-3 border-b border-slate-100 group-last:border-0">
                                        <div class="flex items-center gap-3">
                                            <div class="relative flex-shrink-0">
                                                <div class="h-10 w-10 rounded-full overflow-hidden ring-2 ring-white shadow-md flex items-center justify-center bg-gradient-to-br {{ $avatarClass($res) }} transition-transform duration-200 group-hover:scale-105">
                                                    @if($res->profile_photo_url)
                                                        <img src="{{ $res->profile_photo_url }}" alt="{{ $res->first_name }}" class="h-full w-full object-cover" loading="lazy">
                                                    @else
                                                        <span class="text-white text-xs font-bold tracking-wide">{{ $initials($res) }}</span>
                                                    @endif
                                                </div>
                                                <span class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full {{ $bDot }} ring-2 ring-white" title="{{ $bLabel }}"></span>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-slate-800 leading-tight group-hover:text-emerald-800 transition-colors">{{ $res->last_name }}, {{ $res->first_name }}{{ $res->middle_name ? ' ' . $res->middle_name : '' }}</p>
                                                <p class="text-xs text-slate-400 mt-0.5">{{ $meta($res) }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 border-b border-slate-100 group-last:border-0">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-semibold ring-1 ring-inset ring-slate-200">
                                            <i class="fas fa-map-pin text-[10px] text-slate-400"></i>{{ $res->zone_purok ?? 'N/A' }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 border-b border-slate-100 group-last:border-0">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold ring-1 ring-inset {{ $bCls }}">
                                            <i class="fas {{ $bIcon }} text-[10px]"></i>{{ $bLabel }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 border-b border-slate-100 group-last:border-0">
                                        <div class="flex flex-wrap gap-1">
                                            @forelse($resTags as $tag)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold ring-1 ring-inset {{ $tag['cls'] }}">
                                                    <i class="fas {{ $tag['icon'] }} text-[9px]"></i>{{ $tag['label'] }}
                                                </span>
                                            @empty
                                                <span class="text-slate-300 text-xs">—</span>
                                            @endforelse
                                        </div>
                                    </td>

                                    <td class="bims-noprint px-4 py-3 border-b border-slate-100 group-last:border-0">
                                        <div class="flex justify-center items-center gap-0.5 opacity-70 group-hover:opacity-100 transition-opacity">
                                            <a href="{{ route('admin.residents.show', $res->id) }}" title="View profile" aria-label="View profile"
                                               class="h-8 w-8 flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition">
                                                <i class="fas fa-eye text-xs"></i>
                                            </a>
                                            <a href="{{ route('admin.residents.edit', $res->id) }}" title="Edit resident" aria-label="Edit resident"
                                               class="h-8 w-8 flex items-center justify-center text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition">
                                                <i class="fas fa-pen text-xs"></i>
                                            </a>
                                            <form action="{{ route('admin.residents.destroy', $res->id) }}" method="POST" class="inline"
                                                  data-confirm="Permanenteng mabubura ang account ni {{ $res->first_name }} {{ $res->last_name }}."
                                                  data-confirm-title="Burahin ang resident?"
                                                  data-confirm-label="Delete">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Delete resident" aria-label="Delete resident"
                                                        class="h-8 w-8 flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- ---------- FOOTER: legend + count ---------- --}}
                <div class="px-4 py-3 border-t border-slate-100 bg-slate-50/60 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mr-1">Legend</span>
                        @foreach($tagConfig as $tag)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold ring-1 ring-inset {{ $tag['cls'] }}">
                                <i class="fas {{ $tag['icon'] }} text-[9px]"></i>{{ $tag['label'] }}
                            </span>
                        @endforeach
                    </div>
                    <p class="text-xs text-slate-400">
                        Showing <span class="font-semibold text-slate-600 bims-figure">{{ number_format($totalAll) }}</span>
                        {{ $totalAll === 1 ? 'resident' : 'residents' }}
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,800&family=Figtree:wght@400;500;600;700&display=swap');

    [x-cloak] { display: none !important; }

    .bims-page { font-family: 'Figtree', ui-sans-serif, system-ui, sans-serif; }
    .bims-display {
        font-family: 'Bricolage Grotesque', 'Figtree', ui-sans-serif, system-ui, sans-serif;
        font-weight: 800;
        letter-spacing: -0.02em;
        line-height: 1.1;
    }
    .bims-figure { font-variant-numeric: tabular-nums; letter-spacing: -0.02em; }

    /* ---------- Animations ---------- */
    @keyframes bimsRise {
        from { opacity: 0; transform: translateY(14px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes bimsGrow {
        from { transform: scaleX(0); }
        to   { transform: scaleX(1); }
    }
    @keyframes bimsPulse {
        0%, 100% { transform: scale(1);    opacity: .9; }
        50%      { transform: scale(1.12); opacity: .5; }
    }

    .bims-stats > div { animation: bimsRise .55s cubic-bezier(.22, 1, .36, 1) both; }
    .bims-stats > div:nth-child(2) { animation-delay: .07s; }
    .bims-stats > div:nth-child(3) { animation-delay: .14s; }
    .bims-stats > div:nth-child(4) { animation-delay: .21s; }

    .bims-bar   { transform-origin: left center; animation: bimsGrow .9s cubic-bezier(.22, 1, .36, 1) .25s both; }
    .bims-row   { animation: bimsRise .45s cubic-bezier(.22, 1, .36, 1) both; }
    .bims-pulse { animation: bimsPulse 3s ease-in-out infinite; }

    /* ---------- Sticky header sa table kapag nag-scroll ---------- */
    .bims-table thead th { z-index: 1; }

    /* ---------- Nicer scrollbar ---------- */
    .bims-table::-webkit-scrollbar { height: 8px; }
    .bims-table::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
    .bims-table::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    /* ---------- Accessibility ---------- */
    .bims-page a:focus-visible,
    .bims-page button:focus-visible {
        outline: 2px solid #10b981;
        outline-offset: 2px;
    }

    @media (prefers-reduced-motion: reduce) {
        .bims-stats > div,
        .bims-bar,
        .bims-row,
        .bims-pulse { animation: none !important; }
    }

    /* TINANGGAL ANG @media print DITO */
</style>
@endsection