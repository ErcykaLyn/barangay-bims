@extends('layouts.admin')

@section('content')
@php
    $field = 'w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder:text-slate-400 focus:ring-4 focus:ring-slate-500/15 focus:border-slate-500 outline-none transition shadow-sm shadow-slate-900/[0.03]';
    
    // Counts for the mini stats
    $totalDeceased = $residents->count();
    $thisYear = $residents->filter(fn($r) => $r->date_of_death && \Carbon\Carbon::parse($r->date_of_death)->year === now()->year)->count();
    $avgAge = $totalDeceased > 0 ? round($residents->avg(fn($r) => $r->date_of_birth ? $r->date_of_birth->age : 0)) : 0;
@endphp

<div class="bims-page min-h-screen bg-gradient-to-br from-slate-50 via-slate-100/50 to-slate-200/30">

    {{-- ============================================================
         HEADER
         ============================================================ --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-slate-800 via-slate-900 to-black">
        <div class="absolute inset-0 opacity-[0.15]"
             style="background-image: radial-gradient(rgba(255,255,255,.4) 1px, transparent 1px); background-size: 18px 18px;"></div>
        <div class="absolute -top-16 -right-10 w-64 h-64 bg-slate-600/30 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 left-1/4 w-64 h-64 bg-slate-500/20 rounded-full blur-3xl"></div>
        <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>

        <div class="relative px-3 sm:px-6 lg:px-8 py-5 sm:py-7">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="relative h-11 w-11 sm:h-14 sm:w-14 flex items-center justify-center rounded-2xl bg-white/10 border border-white/20 text-white flex-shrink-0 backdrop-blur-sm shadow-lg shadow-black/20">
                        <i class="fas fa-cross text-lg sm:text-xl"></i>
                    </div>
                    <div class="min-w-0">
                        <nav class="flex items-center gap-1.5 text-xs text-slate-400" aria-label="Breadcrumb">
                            <i class="fas fa-house text-[9px]"></i>
                            <span>Dashboard</span>
                            <i class="fas fa-chevron-right text-[8px]"></i>
                            <span class="text-white font-medium">Deceased</span>
                        </nav>
                        <h1 class="bims-display text-xl sm:text-3xl text-white truncate">Deceased Residents</h1>
                        <p class="hidden sm:block text-xs text-slate-400 mt-1">In loving memory of the residents who have passed away.</p>
                    </div>
                </div>

                <div class="hidden sm:flex flex-col items-end">
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 border border-white/15 text-sm font-bold text-white backdrop-blur-sm">
                        <i class="fas fa-users text-slate-400"></i> {{ number_format($totalDeceased) }} Records
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="px-3 sm:px-6 lg:px-8 py-4 sm:py-6 space-y-4 sm:space-y-6">

        {{-- ===== MINI STATS ===== --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200 flex items-center gap-4">
                <div class="h-10 w-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Deceased</p>
                    <p class="bims-display text-2xl text-slate-800">{{ number_format($totalDeceased) }}</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200 flex items-center gap-4">
                <div class="h-10 w-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">This Year ({{ now()->year }})</p>
                    <p class="bims-display text-2xl text-rose-700">{{ number_format($thisYear) }}</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200 flex items-center gap-4">
                <div class="h-10 w-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Average Age</p>
                    <p class="bims-display text-2xl text-slate-800">{{ $avgAge > 0 ? $avgAge : '—' }} <span class="text-sm font-semibold text-slate-400 font-sans">yrs</span></p>
                </div>
            </div>
        </div>

        {{-- ===== FILTERS ===== --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <form method="GET" action="{{ route('admin.residents.deceased') }}" class="p-3 sm:p-4">
                <div class="grid grid-cols-12 gap-2.5 sm:gap-3 items-end">
                    <div class="col-span-12 lg:col-span-6">
                        <label for="f-search" class="block text-xs font-semibold text-slate-600 mb-1.5">Search</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                            <input id="f-search" type="text" name="search" value="{{ request('search') }}" placeholder="Search by name..."
                                   class="{{ $field }} pl-9">
                        </div>
                    </div>

                    <div class="col-span-7 lg:col-span-3">
                        <label for="f-zone" class="block text-xs font-semibold text-slate-600 mb-1.5">Zone / Purok</label>
                        <div class="relative">
                            <i class="fas fa-map-pin absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                            <select id="f-zone" name="zone" class="{{ $field }} pl-9 pr-8 appearance-none cursor-pointer">
                                <option value="">All Zones</option>
                                @foreach($zones as $zone)
                                    <option value="{{ $zone }}" {{ request('zone') == $zone ? 'selected' : '' }}>{{ $zone }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
                        </div>
                    </div>

                    <div class="col-span-5 lg:col-span-3 flex items-center gap-2">
                        <button type="submit"
                                class="flex-1 px-4 py-2 bg-slate-800 hover:bg-slate-900 active:scale-[.98] text-white text-sm font-semibold rounded-xl shadow-md transition flex items-center justify-center gap-1.5">
                            <i class="fas fa-filter text-[11px]"></i> Filter
                        </button>
                        <a href="{{ route('admin.residents.deceased') }}" title="Reset filters"
                           class="px-3.5 py-2 bg-white border border-slate-300 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-50 transition flex items-center justify-center gap-1.5 shadow-sm">
                            <i class="fas fa-rotate-left text-[11px]"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- ===== DIRECTORY ===== --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-xl bg-slate-200 flex items-center justify-center text-slate-600">
                        <i class="fas fa-list text-xs"></i>
                    </div>
                    <div>
                        <h2 class="bims-display text-base sm:text-lg text-slate-800">Deceased Record</h2>
                        <p class="text-xs text-slate-500 leading-tight">Showing {{ $residents->count() }} results</p>
                    </div>
                </div>
            </div>

            @if($residents->isEmpty())
                <div class="px-4 py-16 text-center">
                    <div class="h-20 w-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
                        <i class="fas fa-cross text-3xl text-slate-300"></i>
                    </div>
                    <h3 class="bims-display text-lg text-slate-700">No deceased records found</h3>
                    <p class="text-sm text-slate-500 mt-1">Residents marked as deceased by BHW will appear here.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[800px]">
                        <thead>
                            <tr class="text-[11px] font-bold uppercase tracking-wider text-slate-500 bg-slate-50/50">
                                <th class="px-4 py-3 w-14 text-center border-b border-slate-200">#</th>
                                <th class="px-4 py-3 border-b border-slate-200">Name</th>
                                <th class="px-4 py-3 border-b border-slate-200">Age</th>
                                <th class="px-4 py-3 border-b border-slate-200">Zone</th>
                                <th class="px-4 py-3 border-b border-slate-200">Date of Death</th>
                                <th class="px-4 py-3 border-b border-slate-200">Cause of Death</th>
                                <th class="px-4 py-3 text-center w-24 border-b border-slate-200">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($residents as $index => $res)
                                <tr class="hover:bg-slate-50/80 transition-colors group">
                                    <td class="px-4 py-3 text-xs text-slate-400 font-mono text-center">
                                        <span class="inline-flex h-6 min-w-[1.75rem] px-1 items-center justify-center rounded-md bg-slate-50 group-hover:bg-white transition">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                    </td>
                                    
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="h-9 w-9 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 flex-shrink-0 ring-2 ring-white shadow-sm">
                                                <i class="fas fa-user text-xs"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-slate-800">{{ $res->last_name }}, {{ $res->first_name }} {{ $res->middle_name }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="px-4 py-3">
                                        <span class="text-sm font-medium text-slate-600">{{ $res->date_of_birth ? $res->date_of_birth->age : '—' }}</span>
                                    </td>
                                    
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-semibold ring-1 ring-inset ring-slate-200">
                                            <i class="fas fa-map-pin text-[10px] text-slate-400"></i>{{ $res->zone_purok ?? 'N/A' }}
                                        </span>
                                    </td>
                                    
                                    <td class="px-4 py-3">
                                        <span class="text-sm font-bold text-rose-600 bg-rose-50 px-2 py-1 rounded-md">
                                            {{ $res->date_of_death ? \Carbon\Carbon::parse($res->date_of_death)->format('M d, Y') : '—' }}
                                        </span>
                                    </td>
                                    
                                    <td class="px-4 py-3">
                                        <span class="text-sm text-slate-600">{{ $res->cause_of_death ?? '—' }}</span>
                                    </td>
                                    
                                    <td class="px-4 py-3">
                                        <div class="flex justify-center items-center gap-1">
                                            <a href="{{ route('admin.residents.show', $res->id) }}" title="View Profile" class="h-8 w-8 flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition">
                                                <i class="fas fa-eye text-xs"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
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
</style>
@endsection