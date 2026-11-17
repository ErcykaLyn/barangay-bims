@extends('layouts.admin')

@section('content')
@php
    $fullName       = trim($resident->first_name . ' ' . $resident->middle_name . ' ' . $resident->last_name);
    $initials       = strtoupper(substr($resident->first_name, 0, 1) . ($resident->middle_name ? substr($resident->middle_name, 0, 1) : '') . substr($resident->last_name, 0, 1));
    $age            = $resident->date_of_birth ? \Carbon\Carbon::parse($resident->date_of_birth)->age : null;
    $totalRequests  = $resident->serviceRequests()->count();
    $recentRequests = $resident->serviceRequests()->latest()->take(3)->get();

    // Full class names written out so Tailwind can always see them
    $tags = [
        'is_senior'      => ['label' => 'Senior citizen',   'short' => 'Senior', 'icon' => 'fa-user-clock',         'box' => 'border-orange-200 bg-orange-50', 'text' => 'text-orange-800', 'icon_cls' => 'text-orange-600', 'dot' => 'bg-orange-500', 'pill' => 'bg-orange-50 text-orange-700 ring-orange-200'],
        'is_4ps'         => ['label' => '4Ps beneficiary',  'short' => '4Ps',    'icon' => 'fa-hand-holding-heart', 'box' => 'border-pink-200 bg-pink-50',     'text' => 'text-pink-800',   'icon_cls' => 'text-pink-600',   'dot' => 'bg-pink-500',   'pill' => 'bg-pink-50 text-pink-700 ring-pink-200'],
        'is_pwd'         => ['label' => 'PWD',              'short' => 'PWD',    'icon' => 'fa-wheelchair',         'box' => 'border-cyan-200 bg-cyan-50',     'text' => 'text-cyan-800',   'icon_cls' => 'text-cyan-600',   'dot' => 'bg-cyan-500',   'pill' => 'bg-cyan-50 text-cyan-700 ring-cyan-200'],
        'is_solo_parent' => ['label' => 'Solo parent',      'short' => 'Solo',   'icon' => 'fa-user-friends',       'box' => 'border-rose-200 bg-rose-50',     'text' => 'text-rose-800',   'icon_cls' => 'text-rose-600',   'dot' => 'bg-rose-500',   'pill' => 'bg-rose-50 text-rose-700 ring-rose-200'],
        'is_voter'       => ['label' => 'Registered voter', 'short' => 'Voter',  'icon' => 'fa-vote-yea',           'box' => 'border-purple-200 bg-purple-50', 'text' => 'text-purple-800', 'icon_cls' => 'text-purple-600', 'dot' => 'bg-purple-500', 'pill' => 'bg-purple-50 text-purple-700 ring-purple-200'],
    ];
    $activeTags = collect($tags)->filter(fn($t, $field) => $resident->$field);

    $statusConfig = [
        'pending'          => ['cls' => 'bg-amber-50 text-amber-700 ring-amber-200',         'label' => 'Pending'],
        'processing'       => ['cls' => 'bg-teal-50 text-teal-700 ring-teal-200',            'label' => 'Processing'],
        'ready_for_pickup' => ['cls' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',   'label' => 'Ready for pickup'],
        'completed'        => ['cls' => 'bg-slate-800 text-white ring-slate-800',            'label' => 'Completed'],
        'denied'           => ['cls' => 'bg-rose-50 text-rose-700 ring-rose-200',            'label' => 'Denied'],
    ];

    // Account state: "Approved" sa DB ng imported ay ibig sabihin nasa listahan lang, hindi registered.
    $hasAccount = filled($resident->email);
    if (!$hasAccount) {
        $acct = ['label' => 'Not registered', 'pill' => 'text-slate-600 bg-slate-100 ring-slate-200', 'dot' => 'bg-slate-400', 'pulse' => false];
    } elseif ($resident->status === 'pending') {
        $acct = ['label' => 'Pending approval', 'pill' => 'text-amber-700 bg-amber-50 ring-amber-200', 'dot' => 'bg-amber-500', 'pulse' => true];
    } elseif ($resident->status === 'rejected') {
        $acct = ['label' => 'Rejected', 'pill' => 'text-rose-700 bg-rose-50 ring-rose-200', 'dot' => 'bg-rose-500', 'pulse' => false];
    } else {
        $acct = ['label' => 'Registered', 'pill' => 'text-emerald-700 bg-emerald-50 ring-emerald-200', 'dot' => 'bg-emerald-500', 'pulse' => true];
    }

    $personal = [
        ['First name',     $resident->first_name,                                  false],
        ['Middle name',    $resident->middle_name ?? '—',                          false],
        ['Last name',      $resident->last_name,                                   false],
        ['Birth date',     $resident->date_of_birth ? \Carbon\Carbon::parse($resident->date_of_birth)->format('M d, Y') : '—', false],
        ['Age',            $age !== null ? $age . ' years old' : '—',              false],
        ['Gender',         $resident->gender ?? '—',                               false],
        ['Civil status',   $resident->civil_status ?? '—',                         false],
        ['Occupation',     $resident->occupation ?? '—',                           false],
        ['Contact number', $resident->contact_number ?? '—',                       true],
    ];
@endphp

<div class="bims-page min-h-screen bg-gradient-to-br from-slate-50 via-emerald-50/40 to-teal-50/30">

    {{-- ============ HEADER ============ --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-emerald-700 via-emerald-800 to-teal-900">
        <div class="absolute -top-16 -right-10 w-56 h-56 bg-emerald-300/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-20 left-1/4 w-56 h-56 bg-teal-300/15 rounded-full blur-3xl"></div>

        <div class="relative px-3 sm:px-6 lg:px-8 py-3 sm:py-4">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                    <a href="{{ route('admin.residents.index') }}" aria-label="Back to residents"
                       class="h-9 w-9 sm:h-10 sm:w-10 flex items-center justify-center rounded-xl bg-white/15 hover:bg-white/25 border border-white/20 text-white transition flex-shrink-0 focus-visible:outline focus-visible:outline-2 focus-visible:outline-white">
                        <i class="fas fa-arrow-left text-sm"></i>
                    </a>
                    <div class="min-w-0">
                        <nav class="flex items-center gap-1.5 text-xs text-emerald-200/80">
                            <a href="{{ route('admin.residents.index') }}" class="hover:text-white transition">Residents</a>
                            <i class="fas fa-chevron-right text-[8px]"></i>
                            <span class="text-white font-medium">View record</span>
                        </nav>
                        <h1 class="bims-display text-lg sm:text-2xl text-white truncate">{{ $fullName }}</h1>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="{{ route('admin.residents.edit', $resident->id) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-emerald-50 text-emerald-800 rounded-lg text-xs font-bold shadow-sm transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-white">
                        <i class="fas fa-pen text-[11px]"></i>
                        <span class="hidden sm:inline">Edit profile</span>
                        <span class="sm:hidden">Edit</span>
                    </a>
                    <form action="{{ route('admin.residents.destroy', $resident->id) }}" method="POST"
                          onsubmit="return confirm(@js('Warning: Permanenteng mabubura ang account ni ' . $resident->first_name . '. Magpatuloy?'));"
                          class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" title="Delete resident" aria-label="Delete resident"
                                class="h-8 w-8 flex items-center justify-center bg-rose-500/25 hover:bg-rose-500/50 text-rose-100 rounded-lg border border-rose-300/40 transition">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ MAIN ============ --}}
    <div class="px-3 sm:px-6 lg:px-8 py-3 sm:py-5">
        
        {{-- IDINAGDAG: DECEASED BANNER --}}
        @if($resident->is_deceased)
            <div class="mb-4 sm:mb-6 p-4 sm:p-5 bg-slate-900 text-white rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-lg border border-slate-700">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-full bg-slate-800 border border-slate-600 flex items-center justify-center text-slate-300 flex-shrink-0">
                        <i class="fas fa-cross text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-0.5">Status Notice</p>
                        <h3 class="text-lg font-bold text-white">Deceased Resident</h3>
                    </div>
                </div>
                <div class="flex flex-wrap gap-6 text-sm">
                    <div>
                        <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Date of Death</p>
                        <p class="font-semibold text-rose-300">
                            {{ $resident->date_of_death ? \Carbon\Carbon::parse($resident->date_of_death)->format('F d, Y') : 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Cause of Death</p>
                        <p class="font-semibold text-white">
                            {{ $resident->cause_of_death ?? 'Not specified' }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 sm:gap-5">

            {{-- ===== LEFT ===== --}}
            <div class="lg:col-span-8 space-y-3 sm:space-y-5">

                {{-- Identity --}}
                <section class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
                    <div class="relative h-14 sm:h-20 bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-600 overflow-hidden">
                        <div class="absolute -top-10 -right-6 w-40 h-40 rounded-full bg-white/15"></div>
                        <div class="absolute -bottom-12 left-1/3 w-32 h-32 rounded-full bg-black/10"></div>
                        <i class="fas fa-id-card absolute right-4 top-1/2 -translate-y-1/2 text-5xl sm:text-6xl text-white/20" aria-hidden="true"></i>
                    </div>

                    <div class="px-4 sm:px-5 pb-4">
                        <div class="relative z-10 flex items-end gap-3 sm:gap-4 -mt-8 sm:-mt-10">
                            <div class="h-16 w-16 sm:h-20 sm:w-20 rounded-2xl overflow-hidden ring-4 ring-white shadow-lg flex items-center justify-center bg-gradient-to-br from-emerald-500 to-teal-700 flex-shrink-0">
                                @if($resident->profile_photo_url)
                                    <img src="{{ $resident->profile_photo_url }}" alt="{{ $resident->first_name }}" class="h-full w-full object-cover">
                                @else
                                    <span class="text-white text-xl sm:text-2xl font-bold">{{ $initials }}</span>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1 pb-0.5 flex items-center justify-between gap-2">
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full ring-1 ring-inset ml-auto {{ $acct['pill'] }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $acct['dot'] }} {{ $acct['pulse'] ? 'bims-pulse' : '' }}"></span>
                                    {{ $acct['label'] }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-2.5">
                            <h2 class="bims-display text-xl sm:text-2xl text-slate-800">{{ $fullName }}</h2>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-0.5 mt-1 text-xs text-slate-500">
                                <span><i class="fas fa-at text-[10px] text-slate-400 mr-1"></i>
                                    @if($hasAccount)
                                        <span class="font-mono text-emerald-700">{{ $resident->email }}</span>
                                    @else
                                        <span class="text-slate-400 italic">Walang account pa</span>
                                    @endif
                                </span>
                                <span><i class="fas fa-calendar-alt text-[10px] text-slate-400 mr-1"></i>{{ $hasAccount ? 'Member since' : 'Added' }} {{ $resident->created_at->format('M d, Y') }}</span>
                                <span class="font-mono text-slate-400">ID #{{ str_pad($resident->id, 5, '0', STR_PAD_LEFT) }}</span>
                            </div>

                            {{-- Classification chips (phone/tablet only; desktop has the sidebar card) --}}
                            @if($activeTags->isNotEmpty())
                                <div class="lg:hidden flex flex-wrap gap-1 mt-2.5">
                                    @foreach($activeTags as $t)
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold ring-1 ring-inset {{ $t['pill'] }}">{{ $t['short'] }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Quick stats --}}
                        <div class="grid grid-cols-3 gap-2 sm:gap-3 mt-3.5">
                            <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-800 px-3 py-2 sm:px-4 sm:py-2.5 text-white shadow shadow-indigo-500/25">
                                <i class="fas fa-birthday-cake absolute -bottom-1 -right-1 text-3xl sm:text-4xl text-white/15" aria-hidden="true"></i>
                                <p class="relative text-[11px] sm:text-xs font-semibold text-white/85">Age</p>
                                <p class="relative bims-display text-xl sm:text-2xl">{{ $age ?? '—' }}</p>
                            </div>
                            <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-800 px-3 py-2 sm:px-4 sm:py-2.5 text-white shadow shadow-emerald-500/25">
                                <i class="fas fa-map-pin absolute -bottom-1 -right-1 text-3xl sm:text-4xl text-white/15" aria-hidden="true"></i>
                                <p class="relative text-[11px] sm:text-xs font-semibold text-white/85">Zone</p>
                                <p class="relative bims-display text-xl sm:text-2xl truncate">{{ $resident->zone_purok ?? '—' }}</p>
                            </div>
                            <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-amber-400 to-orange-600 px-3 py-2 sm:px-4 sm:py-2.5 text-white shadow shadow-amber-500/25">
                                <i class="fas fa-file-alt absolute -bottom-1 -right-1 text-3xl sm:text-4xl text-white/15" aria-hidden="true"></i>
                                <p class="relative text-[11px] sm:text-xs font-semibold text-white/85">Requests</p>
                                <p class="relative bims-display text-xl sm:text-2xl">{{ $totalRequests }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Personal information --}}
                <section class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-4 py-2.5 border-b border-slate-100 bg-gradient-to-r from-indigo-50 via-white to-white flex items-center gap-2.5">
                        <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-indigo-400 to-indigo-700 flex items-center justify-center text-white shadow shadow-indigo-500/30">
                            <i class="fas fa-user text-xs"></i>
                        </div>
                        <div>
                            <h2 class="bims-display text-base text-slate-800">Personal information</h2>
                            <p class="hidden sm:block text-xs text-slate-500 leading-tight">Demographics and contact</p>
                        </div>
                    </div>

                    <dl class="p-4 grid grid-cols-2 sm:grid-cols-3 gap-x-4 gap-y-3">
                        @foreach($personal as [$label, $value, $tabular])
                            <div class="min-w-0">
                                <dt class="text-xs text-slate-500">{{ $label }}</dt>
                                <dd class="text-sm font-semibold text-slate-800 truncate {{ $tabular ? 'tabular-nums' : '' }}">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </section>

                {{-- Address --}}
                <section class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-4 py-2.5 border-b border-slate-100 bg-gradient-to-r from-amber-50 via-white to-white flex items-center gap-2.5">
                        <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-amber-400 to-orange-600 flex items-center justify-center text-white shadow shadow-amber-500/30">
                            <i class="fas fa-map-marker-alt text-xs"></i>
                        </div>
                        <div>
                            <h2 class="bims-display text-base text-slate-800">Address</h2>
                            <p class="hidden sm:block text-xs text-slate-500 leading-tight">Zone and complete address</p>
                        </div>
                    </div>

                    <div class="p-4 grid grid-cols-1 sm:grid-cols-4 gap-x-4 gap-y-3">
                        <div>
                            <p class="text-xs text-slate-500 mb-1">Zone / Purok</p>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-slate-100 text-slate-700 rounded-full text-xs font-semibold ring-1 ring-inset ring-slate-200">
                                <i class="fas fa-map-pin text-[10px]"></i>{{ $resident->zone_purok ?? 'N/A' }}
                            </span>
                        </div>
                        <div class="sm:col-span-3">
                            <p class="text-xs text-slate-500 mb-1">Complete address</p>
                            <div class="flex items-start gap-2 px-3 py-2 bg-emerald-50/60 rounded-lg border border-emerald-100">
                                <i class="fas fa-home text-emerald-600 mt-0.5 text-xs"></i>
                                <span class="text-sm text-slate-700 font-medium leading-snug">{{ $resident->complete_address ?? 'No complete address recorded.' }}</span>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Recent requests --}}
                <section class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-4 py-2.5 border-b border-slate-100 bg-gradient-to-r from-teal-50 via-white to-white flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-teal-400 to-teal-700 flex items-center justify-center text-white shadow shadow-teal-500/30">
                                <i class="fas fa-file-alt text-xs"></i>
                            </div>
                            <div>
                                <h2 class="bims-display text-base text-slate-800">Recent service requests</h2>
                                <p class="hidden sm:block text-xs text-slate-500 leading-tight">Latest 3 requests</p>
                            </div>
                        </div>
                        @if($totalRequests > 0)
                            <span class="text-xs font-medium text-slate-400 whitespace-nowrap">{{ $totalRequests }} {{ $totalRequests === 1 ? 'record' : 'records' }}</span>
                        @endif
                    </div>

                    <div class="p-3 sm:p-4">
                        @if($recentRequests->count())
                            <div class="space-y-2">
                                @foreach($recentRequests as $request)
                                    @php $rc = $statusConfig[$request->status] ?? ['cls' => 'bg-slate-50 text-slate-600 ring-slate-200', 'label' => ucwords(str_replace('_', ' ', $request->status))]; @endphp
                                    <div class="flex items-center justify-between gap-3 px-3 py-2 bg-slate-50/80 rounded-xl border border-slate-100 hover:bg-emerald-50/50 hover:border-emerald-100 transition-colors">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-semibold text-slate-800 truncate">{{ $request->document_type }}</p>
                                            <p class="text-xs text-slate-500 truncate">Purpose: {{ $request->purpose }}</p>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold ring-1 ring-inset whitespace-nowrap flex-shrink-0 {{ $rc['cls'] }}">{{ $rc['label'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6">
                                <div class="h-12 w-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                    <i class="fas fa-inbox text-slate-400"></i>
                                </div>
                                <p class="text-sm text-slate-500 font-medium">No service requests yet.</p>
                            </div>
                        @endif
                    </div>
                </section>
            </div>

            {{-- ===== RIGHT ===== --}}
            <div class="lg:col-span-4 space-y-3 sm:space-y-5">

                {{-- Classifications (desktop; phones see the chips under the name) --}}
                <section class="hidden lg:block bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-4 py-2.5 border-b border-slate-100 bg-gradient-to-r from-violet-50 via-white to-white flex items-center gap-2.5">
                        <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-violet-400 to-purple-700 flex items-center justify-center text-white shadow shadow-violet-500/30">
                            <i class="fas fa-tags text-xs"></i>
                        </div>
                        <h2 class="bims-display text-base text-slate-800">Classifications</h2>
                    </div>

                    <div class="p-3 space-y-1.5">
                        @forelse($activeTags as $t)
                            <div class="flex items-center justify-between px-3 py-2 rounded-xl border {{ $t['box'] }}">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <i class="fas {{ $t['icon'] }} {{ $t['icon_cls'] }} text-xs w-4 text-center"></i>
                                    <span class="text-sm font-semibold {{ $t['text'] }}">{{ $t['label'] }}</span>
                                </div>
                                <span class="inline-flex items-center justify-center h-5 w-5 rounded-full {{ $t['dot'] }} text-white flex-shrink-0">
                                    <i class="fas fa-check text-[8px]"></i>
                                </span>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <div class="h-11 w-11 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                    <i class="fas fa-tag text-slate-400"></i>
                                </div>
                                <p class="text-xs text-slate-500 font-medium">No special classifications</p>
                            </div>
                        @endforelse
                    </div>
                </section>

                {{-- System information --}}
                <section class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-4 py-2.5 border-b border-slate-100 bg-gradient-to-r from-slate-50 via-white to-white flex items-center gap-2.5">
                        <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-slate-400 to-slate-700 flex items-center justify-center text-white shadow shadow-slate-500/30">
                            <i class="fas fa-clock text-xs"></i>
                        </div>
                        <h2 class="bims-display text-base text-slate-800">System information</h2>
                    </div>

                    <div class="p-4 grid grid-cols-2 lg:grid-cols-1 gap-x-4 gap-y-3 lg:gap-y-0 lg:divide-y lg:divide-slate-100">
                        <div class="lg:pb-3">
                            <p class="text-xs text-slate-500 mb-0.5">Added to list</p>
                            <p class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                                <i class="fas fa-calendar-plus text-emerald-500 text-xs"></i>{{ $resident->created_at->format('M d, Y') }}
                            </p>
                            <p class="text-xs text-slate-400 mt-0.5 ml-5">{{ $resident->created_at->format('h:i A') }}</p>
                        </div>
                        <div class="lg:pt-3">
                            <p class="text-xs text-slate-500 mb-0.5">Last updated</p>
                            <p class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                                <i class="fas fa-sync-alt text-emerald-500 text-xs"></i>{{ $resident->updated_at->diffForHumans() }}
                            </p>
                            <p class="text-xs text-slate-400 mt-0.5 ml-5">{{ $resident->updated_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                </section>

                {{-- Quick actions --}}
                <section class="bg-white border border-slate-200/80 rounded-2xl shadow-sm p-3 flex lg:flex-col gap-2">
                    <a href="{{ route('admin.residents.edit', $resident->id) }}"
                       class="flex-1 flex items-center justify-center lg:justify-start gap-2 px-3 py-2 bg-gradient-to-br from-emerald-500 to-emerald-700 hover:from-emerald-600 hover:to-emerald-800 text-white text-sm font-semibold rounded-xl shadow-sm shadow-emerald-600/30 transition">
                        <i class="fas fa-pen text-[11px]"></i> Edit profile
                    </a>
                    <a href="{{ route('admin.residents.index') }}"
                       class="flex-1 flex items-center justify-center lg:justify-start gap-2 px-3 py-2 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition">
                        <i class="fas fa-list text-[11px]"></i> Back to list
                    </a>
                </section>
            </div>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,800&family=Figtree:wght@400;500;600;700&display=swap');

    .bims-page { font-family: 'Figtree', ui-sans-serif, system-ui, sans-serif; }
    .bims-display {
        font-family: 'Bricolage Grotesque', 'Figtree', ui-sans-serif, system-ui, sans-serif;
        font-weight: 800;
        letter-spacing: -0.02em;
        line-height: 1.1;
    }
    .bims-pulse { animation: bimsPulse 2s ease-in-out infinite; }
    @keyframes bimsPulse { 50% { opacity: .4; } }
    @media (prefers-reduced-motion: reduce) { .bims-pulse { animation: none; } }
</style>
@endsection