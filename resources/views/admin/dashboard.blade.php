@extends('layouts.admin')

@section('content')
@php
    $population = max($totalResidents ?? 0, 1);

    // % laban sa kabuuang populasyon, may 1 decimal (0.6% hindi "1%")
    $pctOf  = fn($n, $d = null) => round(($n ?? 0) / max($d ?? $population, 1) * 100, 1);
    $fmtPct = fn($p) => rtrim(rtrim(number_format($p, 1), '0'), '.') . '%';
    // Kita pa rin ang bar kapag may laman na maliit na bilang
    $barPct = fn($n, $p) => ($n ?? 0) > 0 ? max($p, 3) : 0;

    $seniorPercent     = $pctOf($totalSeniors ?? 0);
    $pwdPercent        = $pctOf($totalPWD ?? 0);
    $fourPsPercent     = $pctOf($total4ps ?? 0);
    $voterPercent      = $pctOf($totalVoters ?? 0);
    $registeredPercent = $pctOf($totalRegisteredAccounts ?? 0);

    // Pending requests: % ng lahat ng requests na hindi pa naaaksyunan
    $requestTotal   = $totalRequests ?? 0;
    $pendingPercent = $requestTotal > 0 ? $pctOf($pendingRequests, $requestTotal) : 0;

    $pendingReqs = \App\Models\EquipmentRequest::where('status', 'pending')->count();

    // Male / Female (% ng mga may naka-record na gender, kaya laging 100% ang kabuuan)
    $maleCount     = $totalMale ?? 0;
    $femaleCount   = $totalFemale ?? 0;
    $genderTotal   = $maleCount + $femaleCount;
    $malePercent   = $genderTotal > 0 ? round($maleCount / $genderTotal * 100, 1) : 0;
    $femalePercent = $genderTotal > 0 ? round(100 - $malePercent, 1) : 0;

    // Each card keeps its own color. Full class names are written out so Tailwind can see them.
    $cards = [
        [
            'label' => 'Total population', 'short' => 'Population', 'icon' => 'fa-users', 'chipIcon' => 'fa-circle', 'chip' => 'Live',
            'value' => $totalResidents, 'pct' => 100, 'foot' => 'All residents',
            'bg' => 'from-emerald-500 via-emerald-600 to-emerald-800', 'shadow' => 'shadow-emerald-500/30 hover:shadow-emerald-500/50',
            'text' => 'text-emerald-100', 'live' => true,
        ],
        [
            'label' => 'Registered accounts', 'short' => 'Accounts', 'icon' => 'fa-user-check', 'chipIcon' => 'fa-id-badge', 'chip' => 'Accounts',
            'value' => $totalRegisteredAccounts ?? 0,
            'pct' => $barPct($totalRegisteredAccounts ?? 0, $registeredPercent), 'foot' => $fmtPct($registeredPercent),
            'bg' => 'from-teal-400 via-teal-600 to-teal-800', 'shadow' => 'shadow-teal-500/30 hover:shadow-teal-500/50',
            'text' => 'text-teal-100',
        ],
        [
            'label' => 'Pending requests', 'short' => 'Pending', 'icon' => 'fa-file-alt', 'pending' => true,
            'value' => $pendingRequests,
            'pct' => $barPct($pendingRequests, $pendingPercent),
            'bg' => 'from-amber-400 via-orange-500 to-orange-700', 'shadow' => 'shadow-amber-500/30 hover:shadow-amber-500/50',
            'text' => 'text-amber-100',
        ],
        [
            'label' => 'Senior citizens', 'short' => 'Seniors', 'icon' => 'fa-user-clock', 'chipIcon' => 'fa-heart', 'chip' => 'Seniors',
            'value' => $totalSeniors ?? 0,
            'pct' => $barPct($totalSeniors ?? 0, $seniorPercent), 'foot' => $fmtPct($seniorPercent),
            'bg' => 'from-rose-400 via-rose-500 to-pink-700', 'shadow' => 'shadow-rose-500/30 hover:shadow-rose-500/50',
            'text' => 'text-rose-100',
        ],
        [
            'label' => 'PWD records', 'short' => 'PWD', 'icon' => 'fa-wheelchair', 'chipIcon' => 'fa-hands-helping', 'chip' => 'PWD',
            'value' => $totalPWD ?? 0,
            'pct' => $barPct($totalPWD ?? 0, $pwdPercent), 'foot' => $fmtPct($pwdPercent),
            'bg' => 'from-cyan-400 via-sky-500 to-blue-700', 'shadow' => 'shadow-cyan-500/30 hover:shadow-cyan-500/50',
            'text' => 'text-cyan-100',
        ],
        [
            'label' => '4Ps beneficiaries', 'short' => '4Ps', 'icon' => 'fa-hand-holding-heart', 'chipIcon' => 'fa-gift', 'chip' => '4Ps',
            'value' => $total4ps ?? 0,
            'pct' => $barPct($total4ps ?? 0, $fourPsPercent), 'foot' => $fmtPct($fourPsPercent),
            'bg' => 'from-violet-500 via-purple-600 to-purple-800', 'shadow' => 'shadow-violet-500/30 hover:shadow-violet-500/50',
            'text' => 'text-violet-100',
        ],
        [
            'label' => 'Registered voters', 'short' => 'Voters', 'icon' => 'fa-vote-yea', 'chipIcon' => 'fa-check-double', 'chip' => 'Voters',
            'value' => $totalVoters ?? 0,
            'pct' => $barPct($totalVoters ?? 0, $voterPercent), 'foot' => $fmtPct($voterPercent),
            'bg' => 'from-indigo-500 via-indigo-600 to-blue-900', 'shadow' => 'shadow-indigo-500/30 hover:shadow-indigo-500/50',
            'text' => 'text-indigo-100',
        ],
        [
            'label' => 'Male / Female', 'short' => 'M / F', 'icon' => 'fa-venus-mars', 'chipIcon' => 'fa-chart-pie', 'chip' => 'Gender',
            'gender' => true, 'value' => $maleCount, 'value2' => $femaleCount,
            'pct' => $malePercent, 'foot' => $malePercent . '% M',
            'bg' => 'from-sky-500 via-blue-600 to-pink-600', 'shadow' => 'shadow-sky-500/30 hover:shadow-sky-500/50',
            'text' => 'text-sky-100',
        ],
    ];

    $statusConfig = [
        'pending'          => ['cls' => 'bg-amber-50 text-amber-700 ring-amber-200',       'icon' => 'fa-clock',        'label' => 'Pending'],
        'processing'       => ['cls' => 'bg-sky-50 text-sky-700 ring-sky-200',             'icon' => 'fa-spinner',      'label' => 'Processing'],
        'ready_for_pickup' => ['cls' => 'bg-emerald-50 text-emerald-700 ring-emerald-200', 'icon' => 'fa-box-open',     'label' => 'Ready for pickup'],
        'completed'        => ['cls' => 'bg-green-50 text-green-700 ring-green-200',       'icon' => 'fa-check-double', 'label' => 'Completed'],
        'denied'           => ['cls' => 'bg-rose-50 text-rose-700 ring-rose-200',          'icon' => 'fa-times-circle', 'label' => 'Denied'],
        'no_request'       => ['cls' => 'bg-slate-50 text-slate-600 ring-slate-200',       'icon' => 'fa-inbox',        'label' => 'No request'],
    ];

    // INALIS NA ANG admin.residents.create AT admin.residents.import.form DITO
    $quickActions = [
        ['route' => 'admin.residents.pending',     'icon' => 'fa-user-clock',     'label' => 'Pending accounts',    'tint' => 'bg-amber-400/25 text-amber-100'],
        ['route' => 'admin.requests.index',        'icon' => 'fa-file-alt',       'label' => 'Process requests',    'tint' => 'bg-sky-400/25 text-sky-100'],
        ['route' => 'admin.equipment.index',       'icon' => 'fa-boxes',          'label' => 'Manage inventory',    'tint' => 'bg-violet-400/25 text-violet-100'],
        ['route' => 'admin.equipment.requests',    'icon' => 'fa-clipboard-list', 'label' => 'Borrow requests',     'tint' => 'bg-rose-400/25 text-rose-100', 'badge' => $pendingReqs],
    ];
@endphp

<div class="bims-dash min-h-screen bg-gradient-to-br from-slate-50 via-emerald-50/40 to-teal-50/30">

    {{-- ============ HERO HEADER ============ --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-emerald-700 via-emerald-800 to-teal-900">
        <div class="absolute inset-0 opacity-[0.07]">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                <defs>
                    <pattern id="adminGrid" width="8" height="8" patternUnits="userSpaceOnUse">
                        <path d="M 8 0 L 0 0 0 8" fill="none" stroke="white" stroke-width="0.5"/>
                    </pattern>
                </defs>
                <rect width="100" height="100" fill="url(#adminGrid)"/>
            </svg>
        </div>
        <div class="absolute -top-24 -right-16 w-72 h-72 bg-emerald-300/25 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-28 -left-10 w-80 h-80 bg-teal-300/20 rounded-full blur-3xl"></div>
        <div class="absolute top-0 left-1/3 w-48 h-48 bg-cyan-300/10 rounded-full blur-3xl"></div>

        <div class="relative px-3 sm:px-6 lg:px-8 py-3.5 sm:py-7">
            <div class="flex flex-row items-center justify-between gap-3 sm:gap-5">
                <div class="flex items-center gap-2.5 sm:gap-4 min-w-0">
                    <div class="h-10 w-10 sm:h-14 sm:w-14 flex-shrink-0 rounded-xl sm:rounded-2xl bg-white/15 backdrop-blur border border-white/25 flex items-center justify-center text-white shadow-lg shadow-black/10">
                        <i class="fas fa-shield-alt text-base sm:text-xl"></i>
                    </div>
                    <div>
                        <div class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/10 border border-white/20 mb-1.5">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-300 bims-pulse"></span>
                            <span class="text-xs text-emerald-50 font-semibold">Admin panel</span>
                        </div>
                        <h1 class="bims-display text-lg sm:text-3xl text-white">System overview</h1>
                        <p class="text-emerald-100/80 text-[11px] sm:text-sm leading-snug mt-0.5 sm:mt-1">
                            Welcome back, <span class="font-semibold text-white">{{ Auth::user()->name ?? 'Administrator' }}</span>. Here's the barangay snapshot.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 sm:gap-3 bg-white/10 backdrop-blur-sm rounded-xl sm:rounded-2xl px-2.5 py-1.5 sm:px-4 sm:py-3 border border-white/15 flex-shrink-0">
                    <div class="hidden sm:flex h-9 w-9 rounded-xl bg-white/15 items-center justify-center text-emerald-100">
                        <i class="far fa-calendar-alt"></i>
                    </div>
                    <div>
                        <p class="text-[10px] sm:text-xs text-emerald-200 leading-none sm:leading-normal">Today</p>
                        <p class="text-xs sm:text-sm font-semibold text-white">
                            <span class="hidden sm:inline">{{ now()->format('F d, Y') }}</span>
                            <span class="sm:hidden">{{ now()->format('M d, Y') }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="px-3 sm:px-6 lg:px-8 py-3 sm:py-6 space-y-3 sm:space-y-6">

        {{-- ============ STAT CARDS ============ --}}
        <div class="bims-stats flex flex-wrap justify-center gap-2 sm:gap-4 lg:gap-5">
            @foreach($cards as $c)
                @php
                    $isPending = $c['pending'] ?? false;
                    $isGender  = $c['gender'] ?? false;
                @endphp
                <div class="group relative overflow-hidden w-[calc((100%-1rem)/3)] sm:w-[calc((100%-2rem)/3)] lg:w-[calc((100%-3.75rem)/4)] rounded-2xl sm:rounded-3xl bg-gradient-to-br {{ $c['bg'] }} p-2.5 sm:p-5 lg:p-6 text-white shadow-lg sm:shadow-xl {{ $c['shadow'] }} transition-all duration-500 hover:-translate-y-1.5 hover:shadow-2xl">

                    {{-- decoration --}}
                    <div class="absolute -top-12 -right-12 w-40 h-40 rounded-full bg-white/10 group-hover:scale-125 transition-transform duration-700"></div>
                    <div class="absolute -bottom-10 -left-10 w-28 h-28 rounded-full bg-black/10 group-hover:scale-125 transition-transform duration-700"></div>
                    <i class="fas {{ $c['icon'] }} absolute -bottom-3 -right-2 text-[3.25rem] sm:text-[7rem] text-white/10 rotate-[-8deg] group-hover:rotate-0 group-hover:scale-110 transition-all duration-700" aria-hidden="true"></i>
                    <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/60 to-transparent"></div>

                    <div class="relative">
                        <div class="flex justify-between items-start mb-1.5 sm:mb-5">
                            <div class="h-7 w-7 sm:h-12 sm:w-12 rounded-lg sm:rounded-2xl bg-white/20 backdrop-blur border border-white/25 flex items-center justify-center shadow-lg group-hover:rotate-6 group-hover:scale-110 transition-all duration-300">
                                <i class="fas {{ $c['icon'] }} text-xs sm:text-xl"></i>
                            </div>

                            @if($isPending)
                                @if($c['value'] > 0)
                                    <span class="sm:hidden h-2.5 w-2.5 mt-0.5 rounded-full bg-red-500 ring-2 ring-white/80 bims-pulse"></span>
                                    <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-500 text-white text-xs font-bold shadow-md shadow-red-900/20 bims-pulse">
                                        <i class="fas fa-exclamation-circle text-[10px]"></i> Action
                                    </span>
                                @else
                                    <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/20 backdrop-blur text-xs font-semibold">
                                        <i class="fas fa-check-circle text-[10px]"></i> Clear
                                    </span>
                                @endif
                            @else
                                <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/20 backdrop-blur text-xs font-semibold">
                                    @if($c['live'] ?? false)
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-200 bims-pulse"></span>
                                    @else
                                        <i class="fas {{ $c['chipIcon'] }} text-[10px]"></i>
                                    @endif
                                    {{ $c['chip'] }}
                                </span>
                            @endif
                        </div>

                        <p class="text-[11px] sm:text-sm font-semibold leading-tight truncate {{ $c['text'] }}"><span class="sm:hidden">{{ $c['short'] }}</span><span class="hidden sm:inline">{{ $c['label'] }}</span></p>

                        @if($isGender)
                            <p class="bims-display bims-figure text-xl sm:text-5xl mt-0.5 sm:mt-1 drop-shadow-sm whitespace-nowrap">
                                {{ number_format($c['value']) }}<span class="text-sm sm:text-2xl font-semibold text-white/70 mx-0.5 sm:mx-1">/</span>{{ number_format($c['value2']) }}
                            </p>
                        @else
                            <p class="bims-display bims-figure text-2xl sm:text-5xl mt-0.5 sm:mt-1 drop-shadow-sm">{{ number_format($c['value']) }}</p>
                        @endif

                        <div class="flex items-center gap-1.5 sm:gap-3 mt-1.5 sm:mt-5">
                            <div class="flex-1 h-1 sm:h-2 rounded-full bg-black/15 overflow-hidden">
                                <div class="bims-bar h-full rounded-full bg-white shadow-[0_0_8px_rgba(255,255,255,0.7)]" style="--w: {{ $c['pct'] }}%"></div>
                            </div>
                            <span class="text-[9px] sm:text-xs font-bold whitespace-nowrap">
                                @if($isPending)
                                    <span class="hidden sm:inline">{{ $c['value'] > 0 ? 'Needs review' : 'All good' }}</span><span class="sm:hidden">{{ $c['value'] > 0 ? 'Review' : 'OK' }}</span>
                                @elseif($isGender)
                                    <span class="sm:hidden">{{ $malePercent }}% M</span><span class="hidden sm:inline">{{ $malePercent }}% M · {{ $femalePercent }}% F</span>
                                @else
                                    {{ $c['foot'] }}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ============ ACTIVITY + RIGHT COLUMN ============ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 sm:gap-6">

            {{-- Recent activity --}}
            <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                <div class="px-3.5 py-3 sm:px-6 sm:py-5 flex justify-between items-center gap-3 sm:gap-4 bg-gradient-to-r from-emerald-50 via-white to-white border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 sm:h-11 sm:w-11 rounded-xl sm:rounded-2xl bg-gradient-to-br from-emerald-400 to-emerald-700 flex items-center justify-center text-white shadow-md shadow-emerald-500/30">
                            <i class="fas fa-history"></i>
                        </div>
                        <div>
                            <h2 class="bims-display text-lg text-slate-800">Recent activity</h2>
                            <p class="text-xs text-slate-500">Latest resident registrations</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.residents.index') }}"
                       class="group/link inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-700 hover:text-emerald-900 whitespace-nowrap transition">
                        View all <i class="fas fa-arrow-right text-xs group-hover/link:translate-x-1 transition-transform"></i>
                    </a>
                </div>

                <div class="p-2 sm:p-4 space-y-1.5 sm:space-y-2.5 max-h-[360px] sm:max-h-[480px] overflow-y-auto custom-scroll">
                    @forelse($recentActivities ?? [] as $activity)
                        @php
                            $latestRequest = $activity->serviceRequests()->latest()->first();
                            $status = $latestRequest ? $latestRequest->status : 'no_request';
                            $config = $statusConfig[$status] ?? $statusConfig['no_request'];
                        @endphp
                        <div class="flex items-center justify-between gap-2 sm:gap-3 p-2 sm:p-3 rounded-xl sm:rounded-2xl bg-slate-50/80 border border-slate-100 hover:bg-emerald-50/60 hover:border-emerald-200 hover:shadow-sm hover:translate-x-0.5 transition-all duration-200">
                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                <div class="h-9 w-9 sm:h-11 sm:w-11 rounded-full overflow-hidden flex-shrink-0 ring-2 ring-white shadow-md flex items-center justify-center bg-gradient-to-br from-emerald-400 to-teal-600">
                                    @if($activity->profile_photo_url)
                                        <img src="{{ $activity->profile_photo_url }}" alt="{{ $activity->first_name }}" class="h-full w-full object-cover">
                                    @else
                                        <span class="text-white text-xs font-bold">{{ strtoupper(substr($activity->first_name, 0, 1) . substr($activity->last_name, 0, 1)) }}</span>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-800 truncate">{{ $activity->first_name }} {{ $activity->last_name }}</p>
                                    <p class="text-xs text-slate-500 truncate">{{ '@' . ($activity->username ?? 'N/A') }}</p>
                                </div>
                            </div>
                            <div class="flex flex-col items-end gap-1 flex-shrink-0">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold ring-1 ring-inset whitespace-nowrap {{ $config['cls'] }}">
                                    <i class="fas {{ $config['icon'] }} text-[10px]"></i>{{ $config['label'] }}
                                </span>
                                <span class="text-xs text-slate-400 whitespace-nowrap">{{ $activity->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-14">
                            <div class="h-16 w-16 bg-gradient-to-br from-slate-50 to-slate-100 border border-slate-200 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-inbox text-slate-300 text-xl"></i>
                            </div>
                            <p class="text-sm font-semibold text-slate-700">No registrations yet</p>
                            <p class="text-xs text-slate-500 mt-0.5">New residents will show up here.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Right column --}}
            <div class="space-y-3 sm:space-y-6">

                {{-- Quick actions --}}
                <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-gradient-to-br from-emerald-700 via-emerald-800 to-teal-900 p-3.5 sm:p-6 text-white shadow-xl shadow-emerald-600/25 hover:shadow-emerald-600/40 transition-shadow duration-500">
                    <div class="absolute inset-0 opacity-[0.07]">
                        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                            <defs>
                                <pattern id="qaGrid" width="8" height="8" patternUnits="userSpaceOnUse">
                                    <path d="M 8 0 L 0 0 0 8" fill="none" stroke="white" stroke-width="0.5"/>
                                </pattern>
                            </defs>
                            <rect width="100" height="100" fill="url(#qaGrid)"/>
                        </svg>
                    </div>
                    <i class="fas fa-bolt absolute -right-4 -bottom-6 text-[9rem] text-white/5" aria-hidden="true"></i>
                    <div class="absolute -top-16 -left-10 w-44 h-44 bg-emerald-300/15 rounded-full blur-3xl"></div>

                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-1">
                            <i class="fas fa-bolt text-amber-300"></i>
                            <h3 class="bims-display text-base sm:text-xl">Quick actions</h3>
                        </div>
                        <p class="hidden sm:block text-emerald-200/80 text-sm mb-5">Frequently used admin tasks</p><div class="h-2 sm:hidden"></div>

                        <div class="grid grid-cols-2 gap-1.5 lg:flex lg:flex-col lg:gap-2">
                            @foreach($quickActions as $a)
                                <a href="{{ route($a['route']) }}"
                                   class="group relative flex flex-col lg:flex-row items-center justify-center lg:justify-start gap-1 lg:gap-3 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-xl lg:rounded-2xl px-1 py-2 lg:px-3 lg:py-2.5 border border-white/10 hover:border-white/25 lg:hover:translate-x-1 transition-all duration-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-white">
                                    <span class="h-7 w-7 lg:h-9 lg:w-9 rounded-lg lg:rounded-xl {{ $a['tint'] }} flex items-center justify-center text-sm group-hover:scale-110 transition-transform">
                                        <i class="fas {{ $a['icon'] }}"></i>
                                    </span>
                                    <span class="text-[10px] lg:text-sm leading-tight text-center lg:text-left font-semibold lg:flex-1">{{ $a['label'] }}</span>
                                    @if(($a['badge'] ?? 0) > 0)
                                        <span class="absolute top-1 right-1 lg:static h-5 lg:h-6 min-w-[20px] lg:min-w-[24px] px-1 lg:px-1.5 bg-amber-400 text-emerald-950 text-xs font-bold rounded-full flex items-center justify-center shadow bims-pulse">{{ $a['badge'] }}</span>
                                    @else
                                        <i class="hidden lg:inline fas fa-chevron-right text-[10px] text-emerald-300 group-hover:translate-x-0.5 transition-transform"></i>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Support --}}
                <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 p-3.5 sm:p-5 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-start gap-4">
                        <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-sky-400 to-blue-600 flex items-center justify-center text-white flex-shrink-0 shadow-md shadow-blue-500/30">
                            <i class="fas fa-headset text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800">Need assistance?</p>
                            <p class="text-xs text-slate-500 mt-0.5">Reach out to the BIMS support team.</p>
                            <a href="#" class="group/link inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-700 hover:text-emerald-900 mt-2 transition">
                                Contact IT support <i class="fas fa-arrow-right text-xs group-hover/link:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- System status --}}
                <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 p-3.5 sm:p-5 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-4">
                        <p class="bims-display text-base text-slate-800">System status</p>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 border border-emerald-100 text-xs font-semibold text-emerald-700">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 bims-pulse"></span> Online
                        </span>
                    </div>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Database</span>
                            <i class="fas fa-check-circle text-emerald-500"></i>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Sync service</span>
                            <i class="fas fa-check-circle text-emerald-500"></i>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Last backup</span>
                            <span class="text-slate-700 font-semibold">{{ now()->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,800&family=Figtree:wght@400;500;600;700&display=swap');

    .bims-dash { font-family: 'Figtree', ui-sans-serif, system-ui, sans-serif; }
    .bims-display {
        font-family: 'Bricolage Grotesque', 'Figtree', ui-sans-serif, system-ui, sans-serif;
        font-weight: 800;
        letter-spacing: -0.02em;
        line-height: 1.05;
    }
    .bims-figure { font-variant-numeric: tabular-nums; letter-spacing: -0.04em; }

    /* Cards slide in one after another (scoped to the stat grid only) */
    @keyframes bimsRise {
        from { opacity: 0; transform: translateY(18px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .bims-stats > div { animation: bimsRise .55s cubic-bezier(.22, 1, .36, 1) both; }
    .bims-stats > div:nth-child(1) { animation-delay: .05s; }
    .bims-stats > div:nth-child(2) { animation-delay: .12s; }
    .bims-stats > div:nth-child(3) { animation-delay: .19s; }
    .bims-stats > div:nth-child(4) { animation-delay: .26s; }
    .bims-stats > div:nth-child(5) { animation-delay: .33s; }
    .bims-stats > div:nth-child(6) { animation-delay: .40s; }
    .bims-stats > div:nth-child(7) { animation-delay: .47s; }
    .bims-stats > div:nth-child(8) { animation-delay: .54s; }

    .bims-bar { width: var(--w); animation: bimsFill 1.1s .3s cubic-bezier(.22, 1, .36, 1) both; }
    @keyframes bimsFill { from { width: 0; } to { width: var(--w); } }

    .bims-pulse { animation: bimsPulse 2s ease-in-out infinite; }
    @keyframes bimsPulse { 50% { opacity: .45; } }

    @media (prefers-reduced-motion: reduce) {
        .bims-stats > div, .bims-bar, .bims-pulse { animation: none; }
    }

    .custom-scroll::-webkit-scrollbar { width: 6px; height: 6px; }
    .custom-scroll::-webkit-scrollbar-track { background: transparent; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    .custom-scroll { scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent; }
</style>
@endsection