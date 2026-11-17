@extends('layouts.bhw')

@section('content')
@php
    $families = $families
        ->sortBy('family_code', SORT_NATURAL | SORT_FLAG_CASE)
        ->values();

    // Ang mga sub-household (Z2-xxxx-B, -C...) ay ipinapakita sa loob ng main household nila.
    // Kapag wala sa listahan ang parent (hal. na-filter), lalabas sila bilang sariling row.
    $allIds      = $families->pluck('id');
    $topFamilies = $families->filter(fn($f) => !$f->parent_family_id || !$allIds->contains($f->parent_family_id))->values();

    // Isang household = isang Household No. (ang Family A/B ay nasa loob ng iisang household)
    $totalHouseholds = $topFamilies->count();
    $totalMembers    = $families->sum(fn($f) => $f->members->where('is_deceased', false)->count());
    $totalSeniors    = $families->sum(fn($f) => $f->members->where('age', '>=', 60)->where('is_deceased', false)->count());
    $total4ps        = $families->sum(fn($f) => $f->members->where('is_4ps', true)->where('is_deceased', false)->count());

    $zones = \App\Models\FamilyProfile::whereNotNull('zone_purok')
        ->distinct()
        ->orderBy('zone_purok')
        ->pluck('zone_purok');
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 max-w-[1800px] mx-auto">

    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white shadow-md">
                <i class="fas fa-home text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight">Household Profiling</h1>
                <p class="text-sm text-slate-500 mt-0.5">
                    Household records of Barangay Bactad East
                    <span class="inline-flex items-center gap-1 ml-2 px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-100">
                        <i class="fas fa-database text-[8px]"></i> {{ $totalHouseholds }} households
                    </span>
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('bhw.family_profiling.template') }}"
               class="bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-xl shadow-sm transition flex items-center gap-2 font-bold text-sm">
                <i class="fas fa-download text-emerald-600"></i> Template
            </a>

            <form id="importForm" action="{{ route('bhw.family_profiling.import') }}" method="POST" enctype="multipart/form-data" class="inline">
                @csrf
                <label id="importLabel" class="cursor-pointer bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl shadow-sm transition flex items-center gap-2 font-bold text-sm">
                    <i class="fas fa-file-import" id="importIcon"></i>
                    <span id="importText">Import Data</span>
                    <input type="file" name="file" class="hidden" accept=".xlsx,.xls,.csv,.xlsm" onchange="handleImport(this)">
                </label>
            </form>

            <a href="{{ route('bhw.family_profiling.export', array_merge(request()->query(), ['type' => 'zone'])) }}"
               class="bg-white border-2 border-emerald-500 hover:bg-emerald-50 text-emerald-700 px-4 py-2.5 rounded-xl shadow-sm transition flex items-center gap-2 font-bold text-sm">
                <i class="fas fa-file-excel"></i> Export Zone
            </a>

            <a href="{{ route('bhw.family_profiling.create') }}"
               class="group bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white px-5 py-2.5 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 flex items-center gap-2 font-bold text-sm">
                <i class="fas fa-plus group-hover:rotate-90 transition-transform duration-300"></i>
                New Household
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 bg-emerald-50 border-l-4 border-emerald-500 rounded-lg shadow-sm mb-6">
            <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
            <p class="text-sm font-semibold text-emerald-800 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-600"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 px-4 py-3 bg-rose-50 border-l-4 border-rose-500 rounded-lg shadow-sm mb-6">
            <i class="fas fa-exclamation-circle text-rose-600 text-lg"></i>
            <p class="text-sm font-semibold text-rose-800 flex-1">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-rose-400 hover:text-rose-600"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
        <div class="bg-white border border-slate-200 rounded-xl p-4 flex items-center gap-3 shadow-sm hover:shadow-md transition">
            <div class="h-11 w-11 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                <i class="fas fa-home"></i>
            </div>
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Households</p>
                <p class="text-xl font-black text-slate-800 leading-tight">{{ number_format($totalHouseholds) }}</p>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 flex items-center gap-3 shadow-sm hover:shadow-md transition">
            <div class="h-11 w-11 rounded-xl bg-cyan-50 border border-cyan-100 flex items-center justify-center text-cyan-600">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Total Members (Living)</p>
                <p class="text-xl font-black text-slate-800 leading-tight">{{ number_format($totalMembers) }}</p>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 flex items-center gap-3 shadow-sm hover:shadow-md transition">
            <div class="h-11 w-11 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600">
                <i class="fas fa-user-clock"></i>
            </div>
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Seniors (60+)</p>
                <p class="text-xl font-black text-slate-800 leading-tight">{{ number_format($totalSeniors) }}</p>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 flex items-center gap-3 shadow-sm hover:shadow-md transition">
            <div class="h-11 w-11 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600">
                <i class="fas fa-hand-holding-heart"></i>
            </div>
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">4Ps Members</p>
                <p class="text-xl font-black text-slate-800 leading-tight">{{ number_format($total4ps) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 mb-6">
        <form method="GET" action="{{ route('bhw.family_profiling.index') }}" class="flex flex-col md:flex-row gap-3">
            <div class="flex-1">
                <div class="relative group">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm group-focus-within:text-emerald-500 transition-colors"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search household head or household number..."
                           class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition text-sm bg-slate-50/50 focus:bg-white">
                </div>
            </div>
            <div class="w-full md:w-56">
                <div class="relative">
                    <i class="fas fa-location-dot absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                    <select name="zone"
                            class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 bg-slate-50/50 focus:bg-white text-sm cursor-pointer appearance-none">
                        <option value="">All Zones</option>
                        @foreach($zones as $zone)
                            <option value="{{ $zone }}" {{ request('zone') == $zone ? 'selected' : '' }}>{{ $zone }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl transition flex items-center gap-2 text-sm font-bold shadow-sm">
                    <i class="fas fa-filter text-xs"></i> Filter
                </button>
                <a href="{{ route('bhw.family_profiling.index') }}" class="border border-slate-300 text-slate-600 px-4 py-2.5 rounded-xl hover:bg-slate-50 transition flex items-center gap-2 text-sm font-bold">
                    <i class="fas fa-undo-alt text-xs"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap gap-3 justify-between items-center bg-slate-50/50">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                    <i class="fas fa-list-ul text-xs"></i>
                </div>
                <div>
                    <h2 class="font-black text-slate-800 text-sm tracking-tight">Household Directory</h2>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Click a row to expand members</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="toggleAll(true)"
                        class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 text-[11px] font-bold flex items-center gap-1.5 transition">
                    <i class="fas fa-angles-down text-[10px]"></i> Expand all
                </button>
                <button type="button" onclick="toggleAll(false)"
                        class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 text-[11px] font-bold flex items-center gap-1.5 transition">
                    <i class="fas fa-angles-up text-[10px]"></i> Collapse all
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/80">
                    <tr>
                        <th class="px-4 py-3.5 w-8"></th>
                        <th class="px-4 py-3.5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">#</th>
                        <th class="px-4 py-3.5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Household No.</th>
                        <th class="px-4 py-3.5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Household Head</th>
                        <th class="px-4 py-3.5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Zone</th>
                        <th class="px-4 py-3.5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Address</th>
                        <th class="px-4 py-3.5 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Members (Living)</th>
                        <th class="px-4 py-3.5 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Seniors</th>
                        <th class="px-4 py-3.5 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 bg-white">
                    @forelse($topFamilies as $fp)
                    @php
                        $subFamilies   = $fp->childFamilies->sortBy('family_code', SORT_NATURAL | SORT_FLAG_CASE)->values();
                        $groupFamilies = collect([$fp])->concat($subFamilies);
                        $allLiving     = $groupFamilies->sum(fn($g) => $g->members->where('is_deceased', false)->count());
                        $allDeceased   = $groupFamilies->sum(fn($g) => $g->members->where('is_deceased', true)->count());
                        $allSeniors    = $groupFamilies->sum(fn($g) => $g->members->where('age', '>=', 60)->where('is_deceased', false)->count());
                        $deceasedHead  = $fp->members->where('relation_to_head', 'Head')->where('is_deceased', true)->first();
                        $ownLetter     = $fp->parent_family_id
                            ? (preg_match('/-([A-Z])$/', $fp->family_code, $mm0) ? $mm0[1] : 'B')
                            : null;
                    @endphp

                    <tr id="row-{{ $fp->id }}"
                        class="hover:bg-emerald-50/40 transition duration-150 group cursor-pointer"
                        onclick="toggleMembers({{ $fp->id }})">
                        <td class="px-4 py-4 text-center">
                            <i class="fas fa-chevron-right text-slate-400 text-xs transition-transform duration-200" id="chevron-{{ $fp->id }}"></i>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-xs text-slate-400 font-bold">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-mono text-[10px] font-black border border-slate-200">
                                <i class="fas fa-hashtag text-slate-400 text-[9px]"></i> {{ $fp->family_code }}
                            </span>
                            @if($ownLetter)
                                <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded bg-violet-600 text-white text-[9px] font-black" title="Sub-household ng {{ optional($fp->parentFamily)->family_code }}">{{ $ownLetter }}</span>
                            @endif
                            @if($subFamilies->isNotEmpty())
                                <span class="ml-1 inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-violet-50 text-violet-700 border border-violet-200 text-[9px] font-black" title="May {{ $subFamilies->count() }} sub-household">
                                    <i class="fas fa-sitemap text-[8px]"></i> A{{ $subFamilies->map(fn($s) => preg_match('/-([A-Z])$/', $s->family_code, $mm1) ? ' · ' . $mm1[1] : '')->implode('') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br {{ $deceasedHead ? 'from-slate-400 to-slate-600' : 'from-emerald-400 to-emerald-600' }} flex items-center justify-center text-white text-[10px] font-black flex-shrink-0 shadow-sm">
                                    {{ $fp->head_initials }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-800 group-hover:text-emerald-700 transition uppercase {{ $deceasedHead ? 'line-through text-slate-500' : '' }}">
                                        {{ $fp->head_formal_name }}
                                        @if($deceasedHead)
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-slate-800 text-white text-[8px] font-black uppercase tracking-wider ml-1">
                                                <i class="fas fa-cross text-[7px]"></i> Deceased
                                            </span>
                                        @endif
                                    </p>
                                    <p class="text-[10px] text-slate-400 font-medium">Household Head</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-slate-100 text-slate-700 border border-slate-200">
                                <i class="fas fa-location-dot text-slate-400 text-[9px]"></i> {{ $fp->zone_purok ?? '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-xs text-slate-600 max-w-xs"><span class="line-clamp-1">{{ $fp->complete_address ?? '—' }}</span></td>
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-100" title="Living members">
                                    <i class="fas fa-users text-[9px]"></i> {{ $allLiving }}
                                </span>
                                @if($allDeceased > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-black bg-slate-800 text-white border border-slate-700" title="Deceased members">
                                        <i class="fas fa-cross text-[9px]"></i> {{ $allDeceased }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        {{-- BAGONG COLUMN PARA SA SENIORS --}}
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            @if($allSeniors > 0)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-50 text-amber-700 border border-amber-100">
                                    <i class="fas fa-user-clock text-[9px]"></i> {{ $allSeniors }}
                                </span>
                            @else
                                <span class="text-slate-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-center" onclick="event.stopPropagation()">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('bhw.family_profiling.show', $fp->id) }}" class="h-8 w-8 rounded-lg bg-slate-50 text-slate-500 hover:bg-slate-700 hover:text-white flex items-center justify-center transition-all shadow-sm" title="View"><i class="fas fa-eye text-xs"></i></a>
                                <a href="{{ route('bhw.family_profiling.edit', $fp->id) }}" class="h-8 w-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition-all shadow-sm" title="Edit"><i class="fas fa-pen text-xs"></i></a>
                                <a href="{{ route('bhw.family_profiling.export', ['type' => 'household', 'id' => $fp->id]) }}" class="h-8 w-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all shadow-sm" title="Export Household (1 Page)"><i class="fas fa-file-excel text-xs"></i></a>
                                <form method="POST" action="{{ route('bhw.family_profiling.destroy', $fp->id) }}" onsubmit="return confirm('Delete this household and all members?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="h-8 w-8 rounded-lg bg-rose-50 text-rose-500 hover:bg-rose-600 hover:text-white flex items-center justify-center transition-all shadow-sm" title="Delete"><i class="fas fa-trash-alt text-xs"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <tr id="members-{{ $fp->id }}" class="hidden bg-slate-50/70">
                        <td colspan="9" class="p-0">
                            <div class="px-6 py-5 border-t border-slate-200 border-l-4 border-l-emerald-500">

                                @foreach($groupFamilies as $gi => $grp)
                                @php
                                    $gLiving        = $grp->members->where('is_deceased', false);
                                    $gLivingCount   = $gLiving->count();
                                    $gDeceasedCount = $grp->members->where('is_deceased', true)->count();
                                    $gSenior        = $gLiving->where('age', '>=', 60)->count();
                                    $gFourps        = $gLiving->where('is_4ps', true)->count();
                                    $gIp            = $gLiving->filter(fn($m) => $m->is_ip || strtolower(trim($m->ethnicity ?? '')) === 'ip')->count();
                                    $gDeceasedNames = $grp->members->where('is_deceased', true)->pluck('first_name')->toArray();
                                    $gDeceasedHead  = $grp->members->where('relation_to_head', 'Head')->where('is_deceased', true)->first();
                                    if ($gDeceasedHead && !in_array($grp->head_first_name, $gDeceasedNames)) {
                                        $gDeceasedNames[] = $grp->head_first_name;
                                    }
                                    $gLetter = $gi === 0
                                        ? 'A'
                                        : (preg_match('/-([A-Z])$/', $grp->family_code, $mm2) ? $mm2[1] : chr(65 + $gi));
                                @endphp

                                <div class="{{ $gi > 0 ? 'mt-6 pt-5 border-t-2 border-dashed border-violet-200' : '' }}">
                                    <div class="flex flex-wrap items-center gap-2 mb-3">
                                        @if($subFamilies->isNotEmpty())
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md {{ $gi > 0 ? 'bg-violet-600' : 'bg-emerald-600' }} text-white text-[10px] font-black uppercase tracking-wider">
                                                <i class="fas fa-sitemap text-[9px]"></i> Family {{ $gLetter }}
                                            </span>
                                            <span class="font-mono text-[10px] font-black text-slate-600 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded">{{ $grp->family_code }}</span>
                                            <span class="text-[11px] font-black text-slate-700 uppercase">{{ $grp->head_formal_name }}</span>
                                        @endif
                                        @if($gi > 0)
                                            <a href="{{ route('bhw.family_profiling.show', $grp->id) }}" class="h-6 w-6 rounded-md bg-slate-50 text-slate-500 hover:bg-slate-700 hover:text-white flex items-center justify-center transition-all" title="View"><i class="fas fa-eye text-[10px]"></i></a>
                                            <a href="{{ route('bhw.family_profiling.edit', $grp->id) }}" class="h-6 w-6 rounded-md bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition-all" title="Edit"><i class="fas fa-pen text-[10px]"></i></a>
                                        @endif
                                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-wider mr-2">
                                            <i class="fas fa-users mr-1 text-emerald-600"></i> Living Family Members ({{ $gLivingCount }})
                                        </p>
                                        @if($gDeceasedCount)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-slate-800 text-white border border-slate-700" title="Deceased: {{ implode(', ', $gDeceasedNames) }}">
                                                <i class="fas fa-cross text-[9px]"></i> {{ $gDeceasedCount }} Deceased
                                            </span>
                                        @endif
                                        @if($gSenior)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-50 text-amber-700 border border-amber-100"><i class="fas fa-user-clock text-[9px]"></i> {{ $gSenior }} Senior</span>
                                        @endif
                                        @if($gFourps)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-indigo-50 text-indigo-700 border border-indigo-100"><i class="fas fa-hand-holding-heart text-[9px]"></i> {{ $gFourps }} 4Ps</span>
                                        @endif
                                        @if($gIp)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-violet-50 text-violet-700 border border-violet-100"><i class="fas fa-feather text-[9px]"></i> {{ $gIp }} IP</span>
                                        @endif
                                    </div>

                                    @if($gLivingCount > 0)
                                        <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
                                                <table class="min-w-full text-[10px] border-collapse whitespace-nowrap">
                                                    <thead>
                                                        <tr class="text-white text-[9px] uppercase tracking-widest">
                                                            <th colspan="1" class="px-2 py-1 bg-slate-800 border border-slate-700">Visit</th>
                                                            <th colspan="1" class="px-2 py-1 bg-slate-800 border border-slate-700">&nbsp;</th>
                                                            <th colspan="3" class="px-2 py-1 bg-emerald-800 border border-emerald-700">Name</th>
                                                            <th colspan="8" class="px-2 py-1 bg-slate-700 border border-slate-600">Personal Info</th>
                                                            <th colspan="2" class="px-2 py-1 bg-indigo-800 border border-indigo-700">Programs</th>
                                                            <th colspan="3" class="px-2 py-1 bg-cyan-800 border border-cyan-700">PhilHealth</th>
                                                            <th colspan="4" class="px-2 py-1 bg-rose-800 border border-rose-700">Health</th>
                                                            <th colspan="4" class="px-2 py-1 bg-amber-800 border border-amber-700">Family Planning</th>
                                                            <th colspan="2" class="px-2 py-1 bg-sky-800 border border-sky-700">WASH</th>
                                                        </tr>
                                                        <tr class="text-white">
                                                            <th class="px-2 py-1.5 bg-slate-700 border border-slate-600">Date of Visit</th>
                                                            <th class="px-2 py-1.5 bg-slate-700 border border-slate-600">Relation</th>
                                                            <th class="px-2 py-1.5 bg-emerald-700 border border-emerald-600">Last Name</th>
                                                            <th class="px-2 py-1.5 bg-emerald-700 border border-emerald-600">First Name</th>
                                                            <th class="px-2 py-1.5 bg-emerald-700 border border-emerald-600">Middle</th>
                                                            <th class="px-2 py-1.5 bg-slate-600 border border-slate-500">DOB</th>
                                                            <th class="px-2 py-1.5 bg-slate-600 border border-slate-500">Age</th>
                                                            <th class="px-2 py-1.5 bg-slate-600 border border-slate-500">Sex</th>
                                                            <th class="px-2 py-1.5 bg-slate-600 border border-slate-500">Civil</th>
                                                            <th class="px-2 py-1.5 bg-slate-600 border border-slate-500">Education</th>
                                                            <th class="px-2 py-1.5 bg-slate-600 border border-slate-500">Religion</th>
                                                            <th class="px-2 py-1.5 bg-slate-600 border border-slate-500">Ethnicity</th>
                                                            <th class="px-2 py-1.5 bg-slate-600 border border-slate-500">IP</th>
                                                            <th class="px-2 py-1.5 bg-indigo-700 border border-indigo-600">4Ps</th>
                                                            <th class="px-2 py-1.5 bg-indigo-700 border border-indigo-600">4Ps HH ID</th>
                                                            <th class="px-2 py-1.5 bg-cyan-700 border border-cyan-600">PhilHealth ID</th>
                                                            <th class="px-2 py-1.5 bg-cyan-700 border border-cyan-600">Membership</th>
                                                            <th class="px-2 py-1.5 bg-cyan-700 border border-cyan-600">Category</th>
                                                            <th class="px-2 py-1.5 bg-rose-700 border border-rose-600">Medical Hist.</th>
                                                            <th class="px-2 py-1.5 bg-rose-700 border border-rose-600">Medical Spec.</th>
                                                            <th class="px-2 py-1.5 bg-rose-700 border border-rose-600">Classification</th>
                                                            <th class="px-2 py-1.5 bg-rose-700 border border-rose-600">Immunization</th>
                                                            <th class="px-2 py-1.5 bg-amber-700 border border-amber-600">LMP</th>
                                                            <th class="px-2 py-1.5 bg-amber-700 border border-amber-600">Using FP</th>
                                                            <th class="px-2 py-1.5 bg-amber-700 border border-amber-600">FP Method</th>
                                                            <th class="px-2 py-1.5 bg-amber-700 border border-amber-600">FP Status</th>
                                                            <th class="px-2 py-1.5 bg-sky-700 border border-sky-600">Water</th>
                                                            <th class="px-2 py-1.5 bg-sky-700 border border-sky-600">Toilet</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white">
                                                    @foreach($gLiving as $member)
                                                        <tr class="odd:bg-white even:bg-slate-50/60 hover:bg-emerald-50/60 transition">
                                                            <td class="border border-slate-200 px-2 py-1.5 text-center font-mono">{{ $member->date_of_visit ?? '—' }}</td>
                                                            <td class="border border-slate-200 px-2 py-1.5 text-center"><span class="inline-block px-1.5 py-0.5 rounded bg-slate-100 font-bold">{{ $member->relation_short }}</span></td>
                                                            <td class="border border-slate-200 px-2 py-1.5 font-bold text-slate-800 uppercase">{{ $member->last_name }}</td>
                                                            <td class="border border-slate-200 px-2 py-1.5 font-bold text-slate-800 uppercase">{{ $member->first_name }}</td>
                                                            <td class="border border-slate-200 px-2 py-1.5 text-slate-700 uppercase">{{ $member->middle_name ?? '—' }}</td>
                                                            <td class="border border-slate-200 px-2 py-1.5 text-center font-mono">{{ $member->date_of_birth ? $member->date_of_birth->format('m/d/Y') : '—' }}</td>
                                                            <td class="border border-slate-200 px-2 py-1.5 text-center font-bold">{{ $member->age ?? '—' }}</td>
                                                            <td class="border border-slate-200 px-2 py-1.5 text-center font-bold">{{ $member->sex ?? '—' }}</td>
                                                            <td class="border border-slate-200 px-2 py-1.5 text-center"><span class="inline-block px-1.5 py-0.5 rounded bg-slate-100 font-bold">{{ $member->civil_status_short }}</span></td>
                                                            <td class="border border-slate-200 px-2 py-1.5 text-center">{{ $member->education ?? '—' }}</td>
                                                            <td class="border border-slate-200 px-2 py-1.5 text-center">{{ $member->religion ?? '—' }}</td>
                                                            <td class="border border-slate-200 px-2 py-1.5 text-center">{{ $member->ethnicity ?? '—' }}</td>
                                                            <td class="border border-slate-200 px-2 py-1.5 text-center">@if($member->is_ip || strtolower(trim($member->ethnicity ?? '')) === 'ip') <span class="inline-block px-1.5 py-0.5 rounded bg-violet-50 text-violet-700 font-black">YES</span> @else <span class="text-slate-300">NO</span> @endif</td>
                                                            <td class="border border-slate-200 px-2 py-1.5 text-center">@if($member->is_4ps) <span class="inline-block px-1.5 py-0.5 rounded bg-indigo-50 text-indigo-700 font-black">YES</span> @else <span class="text-slate-300">NO</span> @endif</td>
                                                            <td class="border border-slate-200 px-2 py-1.5 text-center">{{ $member->fourps_household_id ?? '—' }}</td>
                                                            <td class="border border-slate-200 px-2 py-1.5 text-center">{{ $member->philhealth_id ?? '—' }}</td>
                                                            <td class="border border-slate-200 px-2 py-1.5 text-center">{{ $member->philhealth_member ?? '—' }}</td>
                                                            <td class="border border-slate-200 px-2 py-1.5 text-center">{{ $member->philhealth_category ?? '—' }}</td>
                                                            <td class="border border-slate-200 px-2 py-1.5 text-center">{{ $member->medical_history ?? '—' }}</td>
                                                            <td class="border border-slate-200 px-2 py-1.5 text-center">{{ $member->medical_specify ?? '—' }}</td>
                                                            <td class="border border-slate-200 px-2 py-1.5 text-center">{{ $member->classification ?? '—' }}</td>
                                                            <td class="border border-slate-200 px-2 py-1.5 text-center">{{ $member->immunization ?? '—' }}</td>
                                                            <td class="border border-slate-200 px-2 py-1.5 text-center">{{ $member->lmp_date ? \Carbon\Carbon::parse($member->lmp_date)->format('m/d/Y') : '—' }}</td>
                                                            <td class="border border-slate-200 px-2 py-1.5 text-center">{{ is_null($member->using_fp ?? null) ? '—' : (in_array(strtolower((string) $member->using_fp), ['1','y','yes','true']) ? 'Yes' : 'No') }}</td>
                                                            <td class="border border-slate-200 px-2 py-1.5 text-center">{{ $member->fp_method ?? '—' }}</td>
                                                            <td class="border border-slate-200 px-2 py-1.5 text-center">{{ $member->fp_status ?? '—' }}</td>
                                                            <td class="border border-slate-200 px-2 py-1.5 text-center">{{ $member->water_source ?? '—' }}</td>
                                                            <td class="border border-slate-200 px-2 py-1.5 text-center">{{ $member->toilet_facility ?? '—' }}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                    @else
                                        <div class="text-center py-6 text-slate-400 bg-white rounded-xl border border-dashed border-slate-200">
                                            <i class="fas fa-users-slash text-2xl opacity-30 mb-2"></i>
                                            <p class="text-xs">No living members added yet.</p>
                                            <a href="{{ route('bhw.family_profiling.edit', $grp->id) }}" class="mt-2 inline-flex items-center gap-1 text-emerald-600 hover:text-emerald-700 text-[10px] font-bold uppercase tracking-wider"><i class="fas fa-plus-circle"></i> Add members</a>
                                        </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <div class="h-16 w-16 rounded-full bg-emerald-50 border border-emerald-100 flex items-center justify-center">
                                    <i class="fas fa-home text-emerald-300 text-2xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-700">No household records found</p>
                                    <p class="text-xs text-slate-400 mt-1">
                                        @if(request('search') || request('zone'))
                                            Try a different search or reset the filters.
                                        @else
                                            Get started by adding your first household profile.
                                        @endif
                                    </p>
                                </div>
                                @if(request('search') || request('zone'))
                                    <a href="{{ route('bhw.family_profiling.index') }}" class="mt-1 inline-flex items-center gap-2 border border-slate-300 text-slate-600 hover:bg-slate-50 px-4 py-2 rounded-xl text-xs font-bold transition"><i class="fas fa-undo-alt"></i> Reset filters</a>
                                @else
                                    <a href="{{ route('bhw.family_profiling.create') }}" class="mt-1 inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition shadow-sm"><i class="fas fa-plus-circle"></i> Add First Household</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function setExpanded(id, expand) {
        const members = document.getElementById('members-' + id);
        const chevron = document.getElementById('chevron-' + id);
        const row     = document.getElementById('row-' + id);
        if (!members) return;

        members.classList.toggle('hidden', !expand);
        if (chevron) chevron.classList.toggle('rotate-90', expand);
        if (row) row.classList.toggle('bg-emerald-50/40', expand);
    }

    function toggleMembers(id) {
        const members = document.getElementById('members-' + id);
        if (!members) return;
        setExpanded(id, members.classList.contains('hidden'));
    }

    function toggleAll(expand) {
        document.querySelectorAll('[id^="members-"]').forEach(function (el) {
            setExpanded(el.id.replace('members-', ''), expand);
        });
    }

    function handleImport(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            const maxSize = 20;

            if (file.size > maxSize * 1024 * 1024) {
                alert(`File is too large (${fileSize} MB). Maximum allowed is ${maxSize} MB.`);
                input.value = '';
                return;
            }

            const label = document.getElementById('importLabel');
            const text  = document.getElementById('importText');
            const icon  = document.getElementById('importIcon');

            label.classList.remove('bg-blue-600', 'hover:bg-blue-700', 'cursor-pointer');
            label.classList.add('bg-blue-400', 'cursor-wait');
            text.innerText = 'Importing...';
            icon.className = 'fas fa-spinner fa-spin';

            document.getElementById('importForm').submit();
        }
    }
</script>
@endsection