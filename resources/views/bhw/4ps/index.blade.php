@extends('layouts.bhw')

@section('content')
@php
    // ---- Helpers / stats (computed once) ----
    $total      = $fourps->count();
    $seniors    = $fourps->where('is_senior', true)->count();
    $pwds       = $fourps->where('is_pwd', true)->count();
    $zonesCount = $fourps->pluck('zone_purok')->filter()->unique()->count();

    $zones = \App\Models\Resident::where('role', 'resident')
        ->whereNotNull('zone_purok')
        ->distinct()
        ->orderBy('zone_purok')
        ->pluck('zone_purok');
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 max-w-[1600px] mx-auto">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center text-white shadow-md">
                <i class="fas fa-hand-holding-heart text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight">4Ps Beneficiaries</h1>
                <p class="text-sm text-slate-500 mt-0.5">
                    Pantawid Pamilyang Pilipino Program beneficiaries
                    <span class="inline-flex items-center gap-1 ml-2 px-2 py-0.5 rounded-full text-[10px] font-black bg-purple-50 text-purple-700 border border-purple-100">
                        <i class="fas fa-database text-[8px]"></i> {{ $total }} total
                    </span>
                </p>
            </div>
        </div>

        @if(\Illuminate\Support\Facades\Route::has('bhw.residents.create'))
            <a href="{{ route('bhw.residents.create') }}" class="group bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white px-5 py-2.5 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 flex items-center gap-2 font-bold text-sm self-start md:self-auto">
                <i class="fas fa-plus group-hover:rotate-90 transition-transform duration-300"></i>
                Add Beneficiary
            </a>
        @endif
    </div>

    {{-- FLASH MESSAGES --}}
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

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
        <div class="bg-white border border-slate-200 rounded-xl p-4 flex items-center gap-3 shadow-sm">
            <div class="h-10 w-10 rounded-lg bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-600">
                <i class="fas fa-hand-holding-heart text-sm"></i>
            </div>
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Total 4Ps</p>
                <p class="text-lg font-black text-slate-800 leading-tight">{{ $total }}</p>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 flex items-center gap-3 shadow-sm">
            <div class="h-10 w-10 rounded-lg bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600">
                <i class="fas fa-user-clock text-sm"></i>
            </div>
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Senior Members</p>
                <p class="text-lg font-black text-slate-800 leading-tight">{{ $seniors }}</p>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 flex items-center gap-3 shadow-sm">
            <div class="h-10 w-10 rounded-lg bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-600">
                <i class="fas fa-wheelchair text-sm"></i>
            </div>
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">PWD Members</p>
                <p class="text-lg font-black text-slate-800 leading-tight">{{ $pwds }}</p>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 flex items-center gap-3 shadow-sm">
            <div class="h-10 w-10 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                <i class="fas fa-map-location-dot text-sm"></i>
            </div>
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Zones Covered</p>
                <p class="text-lg font-black text-slate-800 leading-tight">{{ $zonesCount }}</p>
            </div>
        </div>
    </div>

    {{-- SEARCH & FILTER --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 mb-6">
        <form method="GET" action="{{ route('bhw.4ps.index') }}" class="flex flex-col md:flex-row gap-3">
            <div class="flex-1">
                <div class="relative group">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm group-focus-within:text-purple-500 transition-colors"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search by name..."
                           class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500/30 focus:border-purple-500 transition text-sm bg-slate-50/50 focus:bg-white">
                </div>
            </div>
            <div class="w-full md:w-56">
                <div class="relative">
                    <i class="fas fa-location-dot absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                    <select name="zone"
                            class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500/30 focus:border-purple-500 bg-slate-50/50 focus:bg-white text-sm cursor-pointer appearance-none">
                        <option value="">All Zones</option>
                        @foreach($zones as $zone)
                            <option value="{{ $zone }}" {{ request('zone') == $zone ? 'selected' : '' }}>{{ $zone }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2.5 rounded-xl transition flex items-center gap-2 text-sm font-bold shadow-sm">
                    <i class="fas fa-filter text-xs"></i> Filter
                </button>
                <a href="{{ route('bhw.4ps.index') }}" class="border border-slate-300 text-slate-600 px-4 py-2.5 rounded-xl hover:bg-slate-50 transition flex items-center gap-2 text-sm font-bold">
                    <i class="fas fa-undo-alt text-xs"></i> Reset
                </a>
            </div>
        </form>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-lg bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-600">
                    <i class="fas fa-list-ul text-xs"></i>
                </div>
                <div>
                    <h2 class="font-black text-slate-800 text-sm tracking-tight">4Ps Directory</h2>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Barangay Bactad East</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/80">
                    <tr>
                        <th class="px-4 py-3.5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">#</th>
                        <th class="px-4 py-3.5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Name</th>
                        <th class="px-4 py-3.5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Age</th>
                        <th class="px-4 py-3.5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Zone</th>
                        <!-- BINAGO: Contact -> Household -->
                        <th class="px-4 py-3.5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Household</th>
                        <th class="px-4 py-3.5 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Classifications</th>
                        <th class="px-4 py-3.5 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 bg-white">
                    @forelse($fourps as $beneficiary)
                        @php
                            $age = $beneficiary->age
                                ?? ($beneficiary->date_of_birth ? \Carbon\Carbon::parse($beneficiary->date_of_birth)->age : null);
                            $initials = strtoupper(
                                mb_substr($beneficiary->first_name ?? '', 0, 1) . mb_substr($beneficiary->last_name ?? '', 0, 1)
                            );
                            $fullName = trim(($beneficiary->first_name ?? '') . ' ' . ($beneficiary->middle_name ?? '') . ' ' . ($beneficiary->last_name ?? ''));
                        @endphp
                        <tr class="hover:bg-purple-50/30 transition duration-150 group">
                            <td class="px-4 py-4 whitespace-nowrap text-xs text-slate-400 font-bold">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>

                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center text-white text-[10px] font-black flex-shrink-0 shadow-sm">
                                        {{ $initials ?: '—' }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800 group-hover:text-purple-700 transition">{{ $fullName }}</p>
                                        @if($beneficiary->civil_status ?? null)
                                            <p class="text-[10px] text-slate-400 font-medium">{{ $beneficiary->civil_status }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($age !== null)
                                    <span class="text-sm font-black text-slate-700">{{ $age }}</span>
                                    <span class="text-[10px] text-slate-400 font-medium">yrs</span>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>

                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($beneficiary->zone_purok)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-slate-100 text-slate-700 border border-slate-200">
                                        <i class="fas fa-location-dot text-slate-400 text-[9px]"></i> {{ $beneficiary->zone_purok }}
                                    </span>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>

                            <!-- BINAGO: Household Column -->
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($beneficiary->familyProfile)
                                    <a href="{{ route('bhw.family_profiling.show', $beneficiary->familyProfile->id) }}"
                                       class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-emerald-100 text-slate-700 hover:text-emerald-800 font-mono text-[10px] font-black border border-slate-200 transition"
                                       title="View household">
                                        <i class="fas fa-home text-slate-400 text-[9px]"></i> {{ $beneficiary->familyProfile->family_code }}
                                    </a>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-50 text-slate-400 border border-dashed border-slate-300">
                                        No household
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-4 text-center">
                                <div class="flex flex-wrap justify-center gap-1">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-purple-50 text-purple-700 border border-purple-100">
                                        <i class="fas fa-hand-holding-heart text-[9px]"></i> 4Ps
                                    </span>
                                    @if($beneficiary->is_senior)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-50 text-amber-700 border border-amber-100">
                                            <i class="fas fa-user-clock text-[9px]"></i> Senior
                                        </span>
                                    @endif
                                    @if($beneficiary->is_pwd)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-sky-50 text-sky-700 border border-sky-100">
                                            <i class="fas fa-wheelchair text-[9px]"></i> PWD
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('bhw.residents.edit', $beneficiary->id) }}" class="h-8 w-8 rounded-lg bg-purple-50 text-purple-600 hover:bg-purple-600 hover:text-white flex items-center justify-center transition-all shadow-sm" title="Edit">
                                        <i class="fas fa-pen text-xs"></i>
                                    </a>
                                    <form method="POST" action="{{ route('bhw.residents.destroy', $beneficiary->id) }}" onsubmit="return confirm('Delete this 4Ps beneficiary?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="h-8 w-8 rounded-lg bg-rose-50 text-rose-500 hover:bg-rose-600 hover:text-white flex items-center justify-center transition-all shadow-sm" title="Delete">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="h-16 w-16 rounded-full bg-purple-50 border border-purple-100 flex items-center justify-center">
                                        <i class="fas fa-hand-holding-heart text-purple-300 text-2xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-700">No 4Ps beneficiaries found</p>
                                        <p class="text-xs text-slate-400 mt-1">
                                            @if(request('search') || request('zone'))
                                                Try a different search or reset the filters.
                                            @else
                                                Beneficiaries marked as 4Ps will appear here.
                                            @endif
                                        </p>
                                    </div>
                                    @if(request('search') || request('zone'))
                                        <a href="{{ route('bhw.4ps.index') }}" class="mt-1 inline-flex items-center gap-2 border border-slate-300 text-slate-600 hover:bg-slate-50 px-4 py-2 rounded-xl text-xs font-bold transition">
                                            <i class="fas fa-undo-alt"></i> Reset filters
                                        </a>
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
@endsection