@extends('layouts.bhw')

@section('content')
@php
    // ---------- Helpers ----------
    $population = max($totalResidents ?? 0, 1);
    $pctOf  = fn($n, $d) => round(($n ?? 0) / max($d ?? 1, 1) * 100, 1);
    $fmtPct = fn($p) => rtrim(rtrim(number_format($p, 1), '0'), '.') . '%';
    $barPct = fn($n, $p) => ($n ?? 0) > 0 ? max($p, 3) : 0;   // kita pa rin ang bar kahit maliit

    $profiled  = max($profiledMembers ?? 0, 1);
    $seniorPct = $pctOf($totalSeniors ?? 0, $population);
    $pwdPct    = $pctOf($totalPwd ?? 0, $population);
    $fourPsPct = $pctOf($total4ps ?? 0, $population);
    $childPct  = $pctOf($totalChildren05 ?? 0, $profiled);
    $toiletPct = $pctOf($noToiletHouseholds ?? 0, max($totalHouseholds ?? 0, 1));

    $genderTotal = ($totalMale ?? 0) + ($totalFemale ?? 0);
    $malePct     = $genderTotal > 0 ? round(($totalMale ?? 0) / $genderTotal * 100, 1) : 0;

    // ---------- Graph data ----------
    $palette = ['#10b981', '#06b6d4', '#f59e0b', '#a855f7', '#0ea5e9', '#ec4899', '#f43f5e', '#f97316'];
    $grads = [
        'from-emerald-400 to-emerald-600', 'from-cyan-400 to-cyan-600', 'from-amber-400 to-orange-500',
        'from-purple-400 to-purple-600',  'from-sky-400 to-blue-600',  'from-pink-400 to-pink-600',
        'from-rose-400 to-red-600',       'from-orange-400 to-orange-600',
    ];

    // Donut segments (SVG circle r=40 -> circumference 251.33)
    $makeDonut = function (array $data) use ($palette) {
        $circ = 2 * M_PI * 40;
        $sum  = max(array_sum($data), 1);
        $out = []; $acc = 0; $i = 0;
        foreach ($data as $label => $value) {
            $frac = $value / $sum;
            $len  = $frac * $circ;
            $out[] = [
                'label' => $label, 'value' => $value, 'pct' => round($frac * 100, 1),
                'color' => $palette[$i++ % count($palette)],
                'dash'  => max($len - ($frac < 1 ? 1.5 : 0), 0), 'circ' => $circ, 'offset' => $acc,
            ];
            $acc += $len;
        }
        return $out;
    };

    // Column chart (age groups)
    $ageMax  = max(array_merge([1], array_values($ageGroups ?? [])));
    $ageSum  = max(array_sum($ageGroups ?? []), 1);
    $ageCols = []; $gi = 0;
    foreach (($ageGroups ?? []) as $label => $value) {
        $ageCols[] = [
            'label' => $label, 'value' => $value, 'pct' => round($value / $ageSum * 100, 1),
            'h' => $value > 0 ? max(round($value / $ageMax * 100), 4) : 0, 'grad' => $grads[$gi++ % count($grads)],
        ];
    }

    // Sectors (nag-o-overlap, kaya % ng buong populasyon ang basehan)
    $sectorDefs = [
        ['Senior Citizens', $totalSeniors ?? 0, 'from-cyan-400 to-cyan-600'],
        ['PWDs',            $totalPwd ?? 0,     'from-amber-400 to-orange-500'],
        ['4Ps Beneficiaries', $total4ps ?? 0,   'from-purple-400 to-purple-600'],
        ['Children 0–5 (profiled)', $totalChildren05 ?? 0, 'from-pink-400 to-pink-600'],
    ];
    $sectorRows = array_map(fn($d) => [
        'label' => $d[0], 'value' => $d[1], 'pct' => $pctOf($d[1], $population),
        'bar' => $d[1] > 0 ? max(round($d[1] / $population * 100), 3) : 0, 'grad' => $d[2],
    ], $sectorDefs);

    $genderDonut = $makeDonut(['Male' => $totalMale ?? 0, 'Female' => $totalFemale ?? 0]);
    $zoneDonut   = $makeDonut($zoneData ?? []);

    $chartTabs = ['age' => 'Age Group', 'gender' => 'Gender', 'sector' => 'Sectors', 'zone' => 'Zones'];

    // 8 cards = 2 rows ng 4. Full class names para makita ng Tailwind.
    $sections = [
        [
            'title' => 'Population',
            'note'  => 'Mula sa listahan ng mga residente',
            'cards' => [
                [
                    'label' => 'Total Residents', 'value' => number_format($totalResidents ?? 0), 'icon' => 'fa-users',
                    'sub' => number_format($totalMale ?? 0) . ' M · ' . number_format($totalFemale ?? 0) . ' F',
                    'pct' => $barPct($totalMale ?? 0, $malePct), 'pctLabel' => $fmtPct($malePct) . ' male',
                    'bg' => 'from-emerald-500 to-emerald-700', 'shadow' => 'shadow-emerald-500/30 hover:shadow-emerald-500/50',
                    'soft' => 'text-emerald-100',
                ],
                [
                    'label' => 'Senior Citizens', 'value' => number_format($totalSeniors ?? 0), 'icon' => 'fa-user-clock',
                    'sub' => 'Ng kabuuang residente', 'pct' => $barPct($totalSeniors ?? 0, $seniorPct), 'pctLabel' => $fmtPct($seniorPct),
                    'bg' => 'from-cyan-500 to-cyan-700', 'shadow' => 'shadow-cyan-500/30 hover:shadow-cyan-500/50',
                    'soft' => 'text-cyan-100',
                ],
                [
                    'label' => 'PWDs', 'value' => number_format($totalPwd ?? 0), 'icon' => 'fa-wheelchair',
                    'sub' => 'Ng kabuuang residente', 'pct' => $barPct($totalPwd ?? 0, $pwdPct), 'pctLabel' => $fmtPct($pwdPct),
                    'bg' => 'from-amber-500 to-orange-600', 'shadow' => 'shadow-amber-500/30 hover:shadow-amber-500/50',
                    'soft' => 'text-amber-100',
                ],
                [
                    'label' => '4Ps Beneficiaries', 'value' => number_format($total4ps ?? 0), 'icon' => 'fa-hand-holding-heart',
                    'sub' => 'Ng kabuuang residente', 'pct' => $barPct($total4ps ?? 0, $fourPsPct), 'pctLabel' => $fmtPct($fourPsPct),
                    'bg' => 'from-purple-500 to-purple-700', 'shadow' => 'shadow-purple-500/30 hover:shadow-purple-500/50',
                    'soft' => 'text-purple-100',
                ],
            ],
        ],
        [
            'title' => 'Households & Health',
            'note'  => 'Mula sa Family Profiling (' . number_format($profiledMembers ?? 0) . ' members na na-profile)',
            'cards' => [
                [
                    'label' => 'Households', 'value' => number_format($totalHouseholds ?? 0), 'icon' => 'fa-house-user',
                    'sub' => number_format($profiledMembers ?? 0) . ' members profiled', 'pct' => null,
                    'bg' => 'from-sky-500 to-blue-700', 'shadow' => 'shadow-sky-500/30 hover:shadow-sky-500/50',
                    'soft' => 'text-sky-100',
                ],
                [
                    'label' => 'Children 0–5 yrs', 'value' => number_format($totalChildren05 ?? 0), 'icon' => 'fa-baby',
                    'sub' => 'Immunization & nutrition', 'pct' => $barPct($totalChildren05 ?? 0, $childPct), 'pctLabel' => $fmtPct($childPct),
                    'bg' => 'from-pink-500 to-pink-700', 'shadow' => 'shadow-pink-500/30 hover:shadow-pink-500/50',
                    'soft' => 'text-pink-100',
                ],
                [
                    'label' => 'Hypertension / Diabetes', 'value' => number_format($totalHypertension ?? 0) . ' / ' . number_format($totalDiabetes ?? 0), 'icon' => 'fa-heartbeat',
                    'sub' => 'Maintenance patients', 'pct' => null,
                    'bg' => 'from-rose-500 to-red-700', 'shadow' => 'shadow-rose-500/30 hover:shadow-rose-500/50',
                    'soft' => 'text-rose-100',
                ],
                [
                    'label' => 'No Toilet', 'value' => number_format($noToiletHouseholds ?? 0), 'icon' => 'fa-toilet',
                    'sub' => 'Households without toilet', 'pct' => $barPct($noToiletHouseholds ?? 0, $toiletPct), 'pctLabel' => $fmtPct($toiletPct),
                    'bg' => 'from-orange-500 to-orange-700', 'shadow' => 'shadow-orange-500/30 hover:shadow-orange-500/50',
                    'soft' => 'text-orange-100',
                ],
            ],
        ],
    ];
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 max-w-7xl mx-auto">

    {{-- ============================================================
         HEADER SECTION
    ============================================================ --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-700 via-emerald-800 to-emerald-950 p-6 md:p-8 mb-8 shadow-lg">
        <!-- Decorative elements -->
        <div class="absolute inset-0 opacity-[0.05]">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <defs>
                    <pattern id="dashGrid" width="8" height="8" patternUnits="userSpaceOnUse">
                        <path d="M 8 0 L 0 0 0 8" fill="none" stroke="white" stroke-width="0.5"/>
                    </pattern>
                </defs>
                <rect width="100" height="100" fill="url(#dashGrid)"/>
            </svg>
        </div>
        <div class="absolute -top-16 -right-16 w-48 h-48 bg-emerald-400/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-20 -left-10 w-56 h-56 bg-cyan-400/10 rounded-full blur-3xl"></div>

        <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-400/15 border border-emerald-400/30 mb-3">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-[10px] text-emerald-200 font-black uppercase tracking-widest">BHW Panel</span>
                </div>
                <h1 class="text-2xl md:text-3xl font-black text-white tracking-tight">Community Dashboard</h1>
                <p class="text-emerald-100/80 text-sm mt-1">
                    Welcome back, <span class="font-bold text-white">{{ Auth::user()->first_name ?? Auth::user()->name }}</span>! Here's your barangay overview.
                </p>
            </div>
            <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-xl px-4 py-2.5 border border-white/10">
                <i class="far fa-calendar-alt text-emerald-300 text-sm"></i>
                <div>
                    <p class="text-[10px] text-emerald-200 uppercase tracking-widest font-bold">Today</p>
                    <p class="text-xs font-bold text-white">{{ now()->format('F d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         STATS CARDS (2 rows x 4)
    ============================================================ --}}
    @foreach($sections as $section)
        <div class="mb-3 flex flex-wrap items-baseline gap-x-3">
            <h2 class="text-[11px] font-black text-slate-500 uppercase tracking-widest">{{ $section['title'] }}</h2>
            <p class="text-[10px] text-slate-400 font-medium">{{ $section['note'] }}</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            @foreach($section['cards'] as $c)
                <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br {{ $c['bg'] }} p-6 text-white shadow-lg {{ $c['shadow'] }} hover:-translate-y-1 transition-all duration-300">
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full group-hover:scale-125 transition-transform duration-500"></div>
                    <div class="absolute -bottom-12 -left-8 w-28 h-28 bg-black/10 rounded-full"></div>
                    <div class="relative flex justify-between items-start gap-3">
                        <div class="min-w-0">
                            <p class="text-[10px] font-black {{ $c['soft'] }} uppercase tracking-widest">{{ $c['label'] }}</p>
                            <p class="text-4xl font-black mt-2 whitespace-nowrap drop-shadow-sm">{{ $c['value'] }}</p>
                            @if(!empty($c['sub']))
                                <p class="text-[10px] {{ $c['soft'] }} font-bold mt-1">{{ $c['sub'] }}</p>
                            @endif
                            @if($c['pct'] !== null)
                                <div class="flex items-center gap-1.5 mt-1.5">
                                    <div class="w-16 bg-white/25 rounded-full h-1 flex-shrink-0">
                                        <div class="bg-white h-1 rounded-full" style="width: {{ $c['pct'] }}%"></div>
                                    </div>
                                    <span class="text-[10px] font-bold whitespace-nowrap">{{ $c['pctLabel'] }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="h-12 w-12 flex-shrink-0 bg-white/20 backdrop-blur-sm border border-white/20 rounded-2xl flex items-center justify-center group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                            <i class="fas {{ $c['icon'] }} text-xl"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach

    {{-- ============================================================
         BOTTOM SECTION
    ============================================================ --}}
    <div class="grid lg:grid-cols-3 gap-6">

        {{-- Population Graphs --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap justify-between items-center gap-3">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                        <i class="fas fa-chart-pie text-sm"></i>
                    </div>
                    <div>
                        <h2 class="font-black text-slate-800 text-sm tracking-tight">Population Breakdown</h2>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ number_format($totalResidents ?? 0) }} residents</p>
                    </div>
                </div>
                <div class="inline-flex flex-wrap rounded-xl bg-slate-100 p-1" id="chartTabs">
                    @foreach($chartTabs as $key => $label)
                        <button type="button" data-tab="{{ $key }}"
                                class="chart-tab px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition {{ $loop->first ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">{{ $label }}</button>
                    @endforeach
                </div>
            </div>

            <div class="p-6 min-h-[22rem]">

                {{-- AGE GROUP: column chart --}}
                <div class="chart-panel" data-panel="age">
                    <div class="flex items-end justify-between gap-3 sm:gap-6 h-64 pt-6 border-b border-slate-200">
                        @foreach($ageCols as $col)
                            <div class="flex-1 h-full flex flex-col justify-end items-center group">
                                <span class="text-sm font-black text-slate-800 mb-1">{{ number_format($col['value']) }}</span>
                                <div class="w-full max-w-[4.5rem] rounded-t-xl bg-gradient-to-t {{ $col['grad'] }} shadow-md group-hover:brightness-110 transition-all duration-500"
                                     style="height: {{ $col['h'] }}%"></div>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex justify-between gap-3 sm:gap-6 mt-2">
                        @foreach($ageCols as $col)
                            <div class="flex-1 text-center">
                                <p class="text-xs font-black text-slate-700">{{ $col['label'] }}</p>
                                <p class="text-[10px] font-bold text-slate-400">{{ $fmtPct($col['pct']) }}</p>
                            </div>
                        @endforeach
                    </div>
                    @if(($noBirthday ?? 0) > 0)
                        <p class="text-[10px] text-slate-400 font-medium mt-4">
                            <i class="fas fa-info-circle mr-1"></i>{{ number_format($noBirthday) }} residente ang walang birthday kaya wala sa graph na ito.
                        </p>
                    @endif
                </div>

                {{-- GENDER + ZONES: donuts --}}
                @foreach(['gender' => $genderDonut, 'zone' => $zoneDonut] as $key => $segments)
                    <div class="chart-panel hidden" data-panel="{{ $key }}">
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-8 py-4">
                            <div class="relative h-56 w-56 flex-shrink-0">
                                <svg viewBox="0 0 100 100" class="h-full w-full -rotate-90">
                                    <circle cx="50" cy="50" r="40" fill="none" stroke="#f1f5f9" stroke-width="14"/>
                                    @foreach($segments as $seg)
                                        @if($seg['value'] > 0)
                                            <circle cx="50" cy="50" r="40" fill="none" stroke="{{ $seg['color'] }}" stroke-width="14"
                                                    stroke-dasharray="{{ round($seg['dash'], 2) }} {{ round($seg['circ'] - $seg['dash'], 2) }}"
                                                    stroke-dashoffset="{{ round(-$seg['offset'], 2) }}"/>
                                        @endif
                                    @endforeach
                                </svg>
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <p class="text-3xl font-black text-slate-800">{{ number_format(collect($segments)->sum('value')) }}</p>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Residents</p>
                                </div>
                            </div>
                            <div class="w-full sm:w-64 space-y-3">
                                @forelse($segments as $seg)
                                    <div class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 border border-slate-100 px-4 py-3">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <span class="h-3 w-3 rounded-full flex-shrink-0" style="background: {{ $seg['color'] }}"></span>
                                            <span class="text-xs font-bold text-slate-700 truncate">{{ $seg['label'] }}</span>
                                        </div>
                                        <div class="text-right flex-shrink-0">
                                            <span class="text-sm font-black text-slate-800">{{ number_format($seg['value']) }}</span>
                                            <span class="text-[10px] font-bold text-slate-400 ml-1">{{ $fmtPct($seg['pct']) }}</span>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-slate-400 text-center">Wala pang data.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- SECTORS: horizontal bars --}}
                <div class="chart-panel hidden space-y-5 py-2" data-panel="sector">
                    @foreach($sectorRows as $row)
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-bold text-slate-700">{{ $row['label'] }}</span>
                                <span class="text-xs font-black text-slate-800">
                                    {{ number_format($row['value']) }}
                                    <span class="text-[10px] font-bold text-slate-400 ml-1">{{ $fmtPct($row['pct']) }}</span>
                                </span>
                            </div>
                            <div class="h-3.5 w-full rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-3.5 rounded-full bg-gradient-to-r {{ $row['grad'] }} transition-all duration-700" style="width: {{ $row['bar'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                    <p class="text-[10px] text-slate-400 font-medium pt-1">
                        <i class="fas fa-info-circle mr-1"></i>Porsyento ng kabuuang residente. Maaaring mapabilang ang isang tao sa higit sa isang sektor.
                    </p>
                </div>
            </div>
        </div>

        <script>
            document.querySelectorAll('#chartTabs .chart-tab').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var tab = btn.dataset.tab;
                    document.querySelectorAll('#chartTabs .chart-tab').forEach(function (b) {
                        var on = b === btn;
                        ['bg-white', 'text-emerald-700', 'shadow-sm'].forEach(function (c) { b.classList.toggle(c, on); });
                        b.classList.toggle('text-slate-500', !on);
                    });
                    document.querySelectorAll('.chart-panel').forEach(function (p) {
                        p.classList.toggle('hidden', p.dataset.panel !== tab);
                    });
                });
            });
        </script>

        {{-- Right Column: Quick Actions + Support --}}
        <div class="space-y-5">

            {{-- Quick Actions --}}
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-700 via-emerald-800 to-emerald-950 p-6 text-white shadow-lg">
                <div class="absolute inset-0 opacity-[0.06]">
                    <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <defs>
                            <pattern id="qaGrid" width="8" height="8" patternUnits="userSpaceOnUse">
                                <path d="M 8 0 L 0 0 0 8" fill="none" stroke="white" stroke-width="0.5"/>
                            </pattern>
                        </defs>
                        <rect width="100" height="100" fill="url(#qaGrid)"/>
                    </svg>
                </div>
                <div class="absolute -right-8 -bottom-8 text-white/5 text-9xl font-black">
                    <i class="fas fa-bolt"></i>
                </div>

                <div class="relative z-10">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-400/15 border border-emerald-400/30 mb-3">
                        <i class="fas fa-bolt text-emerald-300 text-[9px]"></i>
                        <span class="text-[9px] text-emerald-200 font-black uppercase tracking-widest">Shortcuts</span>
                    </div>
                    <h3 class="text-lg font-black mb-1">Quick Actions</h3>
                    <p class="text-emerald-200/70 text-[11px] mb-5">Frequently used tasks</p>

                    <div class="flex flex-col gap-2">
                        <a href="{{ route('bhw.residents.create') }}"
                           class="group flex items-center gap-3 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-xl px-3 py-2.5 transition-all duration-200 hover:translate-x-1 border border-white/5">
                            <div class="h-8 w-8 rounded-lg bg-white/15 flex items-center justify-center text-white text-xs group-hover:scale-110 transition-transform">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <span class="text-xs font-bold flex-1">Add New Resident</span>
                            <i class="fas fa-chevron-right text-[10px] text-emerald-300 group-hover:translate-x-0.5 transition-transform"></i>
                        </a>

                        <a href="{{ route('bhw.residents.index') }}"
                           class="group flex items-center gap-3 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-xl px-3 py-2.5 transition-all duration-200 hover:translate-x-1 border border-white/5">
                            <div class="h-8 w-8 rounded-lg bg-white/15 flex items-center justify-center text-white text-xs group-hover:scale-110 transition-transform">
                                <i class="fas fa-users"></i>
                            </div>
                            <span class="text-xs font-bold flex-1">Manage Residents</span>
                            <i class="fas fa-chevron-right text-[10px] text-emerald-300 group-hover:translate-x-0.5 transition-transform"></i>
                        </a>

                        <a href="{{ route('bhw.seniors.index') }}"
                           class="group flex items-center gap-3 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-xl px-3 py-2.5 transition-all duration-200 hover:translate-x-1 border border-white/5">
                            <div class="h-8 w-8 rounded-lg bg-white/15 flex items-center justify-center text-white text-xs group-hover:scale-110 transition-transform">
                                <i class="fas fa-user-clock"></i>
                            </div>
                            <span class="text-xs font-bold flex-1">Senior Citizens</span>
                            <i class="fas fa-chevron-right text-[10px] text-emerald-300 group-hover:translate-x-0.5 transition-transform"></i>
                        </a>

                        <a href="{{ route('bhw.4ps.index') }}"
                           class="group flex items-center gap-3 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-xl px-3 py-2.5 transition-all duration-200 hover:translate-x-1 border border-white/5">
                            <div class="h-8 w-8 rounded-lg bg-white/15 flex items-center justify-center text-white text-xs group-hover:scale-110 transition-transform">
                                <i class="fas fa-hand-holding-heart"></i>
                            </div>
                            <span class="text-xs font-bold flex-1">4Ps Beneficiaries</span>
                            <i class="fas fa-chevron-right text-[10px] text-emerald-300 group-hover:translate-x-0.5 transition-transform"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Support Card --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-start gap-3">
                    <div class="h-10 w-10 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                        <i class="fas fa-headset text-base"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-black text-slate-800">Need assistance?</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">Reach out to the BIMS support team.</p>
                        <a href="#" class="inline-flex items-center gap-1 text-[10px] font-black text-emerald-600 hover:text-emerald-700 uppercase tracking-widest mt-2 transition">
                            Contact Support <i class="fas fa-arrow-right text-[9px]"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- System Status Card --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">System Status</p>
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-emerald-50 border border-emerald-100">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-[9px] font-black text-emerald-700 uppercase tracking-widest">Online</span>
                    </span>
                </div>
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 font-medium">Database</span>
                        <i class="fas fa-check-circle text-emerald-500 text-xs"></i>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 font-medium">Sync Service</span>
                        <i class="fas fa-check-circle text-emerald-500 text-xs"></i>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 font-medium">Last Backup</span>
                        <span class="text-slate-700 font-bold text-[10px]">Today</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection