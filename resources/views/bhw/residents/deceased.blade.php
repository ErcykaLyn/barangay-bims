@extends('layouts.bhw')

@section('content')
@php
    use Carbon\Carbon;

    // Helper: edad ng resident (gamit ang age column kung meron, kundi compute mula sa birthdate)
    $ageOf = function ($r) {
        if (!is_null($r->age ?? null) && $r->age !== '') return (int) $r->age;
        try {
            return $r->date_of_birth ? Carbon::parse($r->date_of_birth)->age : null;
        } catch (\Throwable $e) {
            return null;
        }
    };

    $total      = $deceased->count();
    $thisYear   = $deceased->filter(function ($r) {
        try { return $r->date_of_death && Carbon::parse($r->date_of_death)->year === now()->year; }
        catch (\Throwable $e) { return false; }
    })->count();
    $ages       = $deceased->map(fn ($r) => $ageOf($r))->filter(fn ($a) => !is_null($a));
    $avgAge     = $ages->count() ? round($ages->avg()) : null;
    $topZone    = $deceased->pluck('zone_purok')->filter()->countBy()->sortDesc()->keys()->first();
    $hasFilter  = request()->filled('search') || request()->filled('zone');
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 max-w-7xl mx-auto">

    {{-- ============ HEADER ============ --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-800 via-slate-700 to-slate-900 p-6 sm:p-8 mb-6 shadow-lg">
        <div class="absolute -right-10 -top-10 h-48 w-48 rounded-full bg-white/5"></div>
        <div class="absolute right-24 -bottom-16 h-40 w-40 rounded-full bg-white/5"></div>

        <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
            <div class="flex items-center gap-4">
                <div class="h-14 w-14 rounded-2xl bg-white/10 backdrop-blur border border-white/10 flex items-center justify-center text-white shadow-inner">
                    <i class="fas fa-cross text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-300">Barangay Bactad East</p>
                    <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Deceased Residents</h1>
                    <p class="text-sm text-slate-300 mt-1">In loving memory of the residents who have passed away.</p>
                </div>
            </div>

            <span class="inline-flex items-center gap-2 self-start sm:self-center px-4 py-2 rounded-full bg-white/10 border border-white/10 text-white text-xs font-bold">
                <i class="fas fa-database text-[10px] text-slate-300"></i>
                {{ number_format($total) }} {{ $total === 1 ? 'record' : 'records' }}
                @if($hasFilter)<span class="text-slate-300 font-medium">· filtered</span>@endif
            </span>
        </div>
    </div>

    {{-- ============ STAT CARDS ============ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center shrink-0"><i class="fas fa-users"></i></div>
            <div class="min-w-0">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Total</p>
                <p class="text-xl font-black text-slate-800 leading-tight">{{ number_format($total) }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0"><i class="fas fa-calendar-check"></i></div>
            <div class="min-w-0">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">This year</p>
                <p class="text-xl font-black text-slate-800 leading-tight">{{ number_format($thisYear) }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0"><i class="fas fa-hourglass-half"></i></div>
            <div class="min-w-0">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Average age</p>
                <p class="text-xl font-black text-slate-800 leading-tight">{{ $avgAge ?? '—' }}@if($avgAge) <span class="text-xs font-semibold text-slate-400">yrs</span>@endif</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0"><i class="fas fa-location-dot"></i></div>
            <div class="min-w-0">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Most records</p>
                <p class="text-sm font-black text-slate-800 leading-tight truncate" title="{{ $topZone ?? '—' }}">{{ $topZone ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- ============ FILTERS ============ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 mb-6">
        <form method="GET" action="{{ route('bhw.residents.deceased') }}" class="flex flex-col md:flex-row gap-3">
            <div class="flex-1">
                <div class="relative group">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm group-focus-within:text-slate-600 transition-colors"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search by name..." autocomplete="off"
                           class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-slate-500/30 focus:border-slate-500 transition text-sm bg-slate-50/50 focus:bg-white">
                </div>
            </div>

            <div class="w-full md:w-56">
                <div class="relative group">
                    <i class="fas fa-map-pin absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm group-focus-within:text-slate-600 transition-colors"></i>
                    <select name="zone"
                            class="w-full pl-10 pr-10 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-slate-500/30 focus:border-slate-500 appearance-none bg-slate-50/50 focus:bg-white text-sm cursor-pointer">
                        <option value="">All Zones</option>
                        @foreach($zones as $zone)
                            <option value="{{ $zone }}" {{ request('zone') == $zone ? 'selected' : '' }}>{{ $zone }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit"
                        class="flex-1 md:flex-none justify-center bg-slate-700 hover:bg-slate-800 text-white px-5 py-2.5 rounded-xl transition flex items-center gap-2 text-sm font-bold shadow-sm hover:shadow-md">
                    <i class="fas fa-filter text-xs"></i> Filter
                </button>
                <a href="{{ route('bhw.residents.deceased') }}"
                   class="flex-1 md:flex-none justify-center border border-slate-300 text-slate-600 px-4 py-2.5 rounded-xl hover:bg-slate-50 hover:border-slate-400 transition flex items-center gap-2 text-sm font-bold">
                    <i class="fas fa-undo-alt text-xs"></i> Reset
                </a>
            </div>
        </form>

        @if($hasFilter)
            <div class="flex flex-wrap items-center gap-2 mt-3 pt-3 border-t border-slate-100">
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Active filters</span>
                @if(request()->filled('search'))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                        <i class="fas fa-search text-[9px] text-slate-400"></i> “{{ request('search') }}”
                    </span>
                @endif
                @if(request()->filled('zone'))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                        <i class="fas fa-map-pin text-[9px] text-slate-400"></i> {{ request('zone') }}
                    </span>
                @endif
            </div>
        @endif
    </div>

    {{-- ============ RECORDS ============ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/60">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600">
                    <i class="fas fa-list-ul text-xs"></i>
                </div>
                <div>
                    <h2 class="font-black text-slate-800 text-sm tracking-tight">Deceased Record</h2>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Barangay Bactad East</p>
                </div>
            </div>
            <span class="hidden sm:inline text-xs font-semibold text-slate-400">Showing {{ number_format($total) }} {{ $total === 1 ? 'result' : 'results' }}</span>
        </div>

        {{-- ---------- Desktop table ---------- --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/80">
                    <tr>
                        <th class="px-4 py-3.5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">#</th>
                        <th class="px-4 py-3.5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Name</th>
                        <th class="px-4 py-3.5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Age</th>
                        <th class="px-4 py-3.5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Zone</th>
                        <th class="px-4 py-3.5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Date of Death</th>
                        <th class="px-4 py-3.5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Cause of Death</th>
                        <th class="px-4 py-3.5 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 bg-white">
                    @forelse($deceased as $r)
                        @php
                            $age     = $ageOf($r);
                            $dod     = null;
                            try { $dod = $r->date_of_death ? Carbon::parse($r->date_of_death) : null; } catch (\Throwable $e) {}
                            $fullName = trim(($r->first_name ?? '') . ' ' . ($r->middle_name ?? '') . ' ' . ($r->last_name ?? ''));
                            $initials = strtoupper(substr($r->first_name ?? '', 0, 1)) . strtoupper(substr($r->last_name ?? '', 0, 1));
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition duration-150 group cursor-pointer"
                            onclick="window.location='{{ route('bhw.residents.show', $r->id) }}'">
                            <td class="px-4 py-4 whitespace-nowrap text-xs text-slate-400 font-bold">
                                {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-full overflow-hidden flex-shrink-0 ring-2 ring-slate-200 shadow-sm flex items-center justify-center bg-slate-100">
                                        @if($r->profile_photo_url)
                                            <img src="{{ $r->profile_photo_url }}" alt="{{ $r->first_name }}" class="h-full w-full object-cover grayscale">
                                        @else
                                            <span class="text-slate-600 text-[10px] font-black bg-gradient-to-br from-slate-200 to-slate-300 h-full w-full flex items-center justify-center">{{ $initials }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800 group-hover:text-slate-900">{{ $fullName }}</p>
                                        <p class="text-[10px] text-slate-400 font-medium flex items-center gap-1 mt-0.5">
                                            <i class="fas fa-cross text-[8px]"></i> Deceased
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold text-slate-700">{{ $age ?? '—' }}@if(!is_null($age)) <span class="text-[10px] text-slate-400 font-medium">yrs</span>@endif</span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-slate-100 text-slate-700 border border-slate-200">
                                    <i class="fas fa-location-dot text-slate-400 text-[9px]"></i> {{ $r->zone_purok ?? '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($dod)
                                    <p class="text-sm text-slate-700 font-semibold">{{ $dod->format('M d, Y') }}</p>
                                    <p class="text-[10px] text-slate-400 font-medium">{{ $dod->diffForHumans() }}</p>
                                @else
                                    <span class="text-sm text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <span class="text-sm text-slate-600 font-semibold block max-w-[16rem] truncate" title="{{ $r->cause_of_death }}">
                                    {{ $r->cause_of_death ?: '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center" onclick="event.stopPropagation()">
                                <a href="{{ route('bhw.residents.show', $r->id) }}"
                                   class="inline-flex h-8 w-8 rounded-lg bg-slate-50 border border-slate-200 text-slate-500 hover:bg-slate-700 hover:border-slate-700 hover:text-white items-center justify-center transition-all shadow-sm"
                                   title="View Details">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="h-16 w-16 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center">
                                        <i class="fas fa-cross text-slate-300 text-2xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-700">No deceased records found</p>
                                        <p class="text-xs text-slate-400 mt-1">
                                            {{ $hasFilter ? 'Try changing or resetting your filters.' : 'Residents marked as deceased will appear here.' }}
                                        </p>
                                    </div>
                                    @if($hasFilter)
                                        <a href="{{ route('bhw.residents.deceased') }}" class="mt-1 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-700 text-white text-xs font-bold hover:bg-slate-800 transition">
                                            <i class="fas fa-undo-alt text-[10px]"></i> Reset filters
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ---------- Mobile cards ---------- --}}
        <div class="md:hidden divide-y divide-slate-100">
            @forelse($deceased as $r)
                @php
                    $age     = $ageOf($r);
                    $dod     = null;
                    try { $dod = $r->date_of_death ? Carbon::parse($r->date_of_death) : null; } catch (\Throwable $e) {}
                    $fullName = trim(($r->first_name ?? '') . ' ' . ($r->middle_name ?? '') . ' ' . ($r->last_name ?? ''));
                    $initials = strtoupper(substr($r->first_name ?? '', 0, 1)) . strtoupper(substr($r->last_name ?? '', 0, 1));
                @endphp
                <a href="{{ route('bhw.residents.show', $r->id) }}" class="block p-4 hover:bg-slate-50 transition">
                    <div class="flex items-center gap-3">
                        <div class="h-12 w-12 rounded-full overflow-hidden flex-shrink-0 ring-2 ring-slate-200 flex items-center justify-center bg-slate-100">
                            @if($r->profile_photo_url)
                                <img src="{{ $r->profile_photo_url }}" alt="{{ $r->first_name }}" class="h-full w-full object-cover grayscale">
                            @else
                                <span class="text-slate-600 text-xs font-black bg-gradient-to-br from-slate-200 to-slate-300 h-full w-full flex items-center justify-center">{{ $initials }}</span>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-black text-slate-800 truncate">{{ $fullName }}</p>
                            <p class="text-[11px] text-slate-400 font-medium">{{ !is_null($age) ? $age . ' yrs old' : 'Age unknown' }}</p>
                        </div>
                        <i class="fas fa-chevron-right text-slate-300 text-xs"></i>
                    </div>
                    <div class="grid grid-cols-2 gap-2 mt-3 text-[11px]">
                        <div class="rounded-lg bg-slate-50 border border-slate-100 px-3 py-2">
                            <p class="font-black uppercase tracking-widest text-slate-400 text-[9px]">Zone</p>
                            <p class="font-bold text-slate-700 truncate">{{ $r->zone_purok ?? '—' }}</p>
                        </div>
                        <div class="rounded-lg bg-slate-50 border border-slate-100 px-3 py-2">
                            <p class="font-black uppercase tracking-widest text-slate-400 text-[9px]">Date of death</p>
                            <p class="font-bold text-slate-700">{{ $dod ? $dod->format('M d, Y') : '—' }}</p>
                        </div>
                        <div class="col-span-2 rounded-lg bg-slate-50 border border-slate-100 px-3 py-2">
                            <p class="font-black uppercase tracking-widest text-slate-400 text-[9px]">Cause of death</p>
                            <p class="font-bold text-slate-700">{{ $r->cause_of_death ?: '—' }}</p>
                        </div>
                    </div>
                </a>
            @empty
                <div class="px-6 py-14 text-center">
                    <div class="mx-auto h-14 w-14 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center mb-3">
                        <i class="fas fa-cross text-slate-300 text-xl"></i>
                    </div>
                    <p class="text-sm font-bold text-slate-700">No deceased records found</p>
                    <p class="text-xs text-slate-400 mt-1">{{ $hasFilter ? 'Try changing or resetting your filters.' : 'Residents marked as deceased will appear here.' }}</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection