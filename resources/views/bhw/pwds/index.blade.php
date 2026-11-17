@extends('layouts.bhw')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 max-w-7xl mx-auto">

    {{-- ============================================================
         HEADER SECTION
    ============================================================ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-cyan-500 to-cyan-700 flex items-center justify-center text-white shadow-md">
                <i class="fas fa-wheelchair text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight">Persons with Disabilities</h1>
                <p class="text-sm text-slate-500 mt-0.5">
                    List of registered persons with disabilities
                    <span class="inline-flex items-center gap-1 ml-2 px-2 py-0.5 rounded-full text-[10px] font-black bg-cyan-50 text-cyan-700 border border-cyan-100">
                        <i class="fas fa-database text-[8px]"></i>
                        {{ $pwds->count() }} total
                    </span>
                </p>
            </div>
        </div>
        <a href="{{ route('bhw.residents.create') }}" 
           class="group bg-gradient-to-r from-cyan-600 to-cyan-700 hover:from-cyan-700 hover:to-cyan-800 text-white px-5 py-2.5 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 flex items-center gap-2 w-fit font-bold text-sm">
            <i class="fas fa-plus group-hover:rotate-90 transition-transform duration-300"></i>
            Add PWD Record
        </a>
    </div>

    {{-- ============================================================
         STATS CARDS (Quick Overview)
    ============================================================ --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        {{-- Total PWDs --}}
        <div class="bg-white border border-slate-200 rounded-xl p-4 flex items-center gap-3 shadow-sm">
            <div class="h-10 w-10 rounded-lg bg-cyan-50 border border-cyan-100 flex items-center justify-center text-cyan-600 flex-shrink-0">
                <i class="fas fa-wheelchair text-sm"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Total PWDs</p>
                <p class="text-lg font-black text-slate-800 leading-tight">{{ $pwds->count() }}</p>
            </div>
        </div>

        {{-- Senior PWDs --}}
        <div class="bg-white border border-slate-200 rounded-xl p-4 flex items-center gap-3 shadow-sm">
            <div class="h-10 w-10 rounded-lg bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 flex-shrink-0">
                <i class="fas fa-user-clock text-sm"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Senior PWDs</p>
                <p class="text-lg font-black text-slate-800 leading-tight">{{ $pwds->where('is_senior', true)->count() }}</p>
            </div>
        </div>

        {{-- 4Ps PWDs --}}
        <div class="bg-white border border-slate-200 rounded-xl p-4 flex items-center gap-3 shadow-sm">
            <div class="h-10 w-10 rounded-lg bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-600 flex-shrink-0">
                <i class="fas fa-hand-holding-heart text-sm"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">4Ps PWDs</p>
                <p class="text-lg font-black text-slate-800 leading-tight">{{ $pwds->where('is_4ps', true)->count() }}</p>
            </div>
        </div>

        {{-- Zones --}}
        <div class="bg-white border border-slate-200 rounded-xl p-4 flex items-center gap-3 shadow-sm">
            <div class="h-10 w-10 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                <i class="fas fa-map-marked-alt text-sm"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Zones Covered</p>
                <p class="text-lg font-black text-slate-800 leading-tight">{{ $pwds->pluck('zone_purok')->filter()->unique()->count() }}</p>
            </div>
        </div>
    </div>

    {{-- ============================================================
         SEARCH & FILTER BAR
    ============================================================ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 mb-6">
        <form method="GET" action="{{ route('bhw.pwds.index') }}" class="flex flex-col md:flex-row gap-3">
            <!-- Search Input -->
            <div class="flex-1">
                <div class="relative group">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm group-focus-within:text-cyan-500 transition-colors"></i>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search by name..." 
                           class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-cyan-500/30 focus:border-cyan-500 transition text-sm bg-slate-50/50 focus:bg-white">
                </div>
            </div>

            <!-- Zone Filter -->
            <div class="w-full md:w-56">
                <div class="relative group">
                    <i class="fas fa-map-pin absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm group-focus-within:text-cyan-500 transition-colors"></i>
                    <select name="zone" 
                            class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-cyan-500/30 focus:border-cyan-500 appearance-none bg-slate-50/50 focus:bg-white text-sm cursor-pointer">
                        <option value="">All Zones</option>
                        @php
                            $zones = \App\Models\Resident::where('role', 'resident')
                                ->whereNotNull('zone_purok')
                                ->distinct()
                                ->pluck('zone_purok');
                        @endphp
                        @foreach($zones as $zone)
                            <option value="{{ $zone }}" {{ request('zone') == $zone ? 'selected' : '' }}>{{ $zone }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2">
                <button type="submit" 
                        class="bg-cyan-600 hover:bg-cyan-700 text-white px-5 py-2.5 rounded-xl transition flex items-center gap-2 text-sm font-bold shadow-sm hover:shadow-md">
                    <i class="fas fa-filter text-xs"></i> Filter
                </button>
                <a href="{{ route('bhw.pwds.index') }}" 
                   class="border border-slate-300 text-slate-600 px-4 py-2.5 rounded-xl hover:bg-slate-50 hover:border-slate-400 transition flex items-center gap-2 text-sm font-bold">
                    <i class="fas fa-undo-alt text-xs"></i> Reset
                </a>
            </div>
        </form>
    </div>

    {{-- ============================================================
         PWDS TABLE
    ============================================================ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

        <!-- Table Header Bar -->
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-lg bg-cyan-50 border border-cyan-100 flex items-center justify-center text-cyan-600">
                    <i class="fas fa-list-ul text-xs"></i>
                </div>
                <div>
                    <h2 class="font-black text-slate-800 text-sm tracking-tight">PWD Directory</h2>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Barangay Bactad East</p>
                </div>
            </div>
            @if(request('search') || request('zone'))
                <div class="hidden sm:flex items-center gap-2 text-[10px] font-bold text-slate-500">
                    <i class="fas fa-info-circle text-cyan-500"></i>
                    <span>Filtered results: <span class="text-cyan-600">{{ $pwds->count() }}</span></span>
                </div>
            @endif
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
                    @forelse($pwds as $pwd)
                    <tr class="hover:bg-cyan-50/30 transition duration-150 group">
                        <td class="px-4 py-4 whitespace-nowrap text-xs text-slate-400 font-bold">
                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                        </td>

                        {{-- Name with Profile Photo --}}
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full overflow-hidden flex-shrink-0 ring-2 ring-cyan-100 shadow-sm flex items-center justify-center bg-cyan-50">
                                    @if($pwd->profile_photo_url)
                                        <img src="{{ $pwd->profile_photo_url }}" 
                                             alt="{{ $pwd->first_name }} {{ $pwd->last_name }}" 
                                             class="h-full w-full object-cover">
                                    @else
                                        <span class="text-white text-[10px] font-black bg-gradient-to-br from-cyan-400 to-cyan-600 h-full w-full flex items-center justify-center">
                                            {{ strtoupper(substr($pwd->first_name, 0, 1)) }}{{ strtoupper(substr($pwd->last_name, 0, 1)) }}
                                        </span>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-800 group-hover:text-cyan-700 transition">
                                        {{ $pwd->first_name }} {{ $pwd->middle_name ?? '' }} {{ $pwd->last_name }}
                                    </p>
                                    <p class="text-[10px] text-slate-400 font-medium">
                                        {{ $pwd->civil_status ?? 'Resident' }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        {{-- Age --}}
                        <td class="px-4 py-4 whitespace-nowrap">
                            @php
                                $age = $pwd->age ?? ($pwd->date_of_birth ? \Carbon\Carbon::parse($pwd->date_of_birth)->age : null);
                            @endphp
                            @if($age)
                                <span class="inline-flex items-center gap-1 text-sm font-bold text-slate-700">
                                    {{ $age }}
                                    <span class="text-[10px] text-slate-400 font-medium">yrs</span>
                                </span>
                            @else
                                <span class="text-slate-300 text-sm">—</span>
                            @endif
                        </td>

                        {{-- Zone --}}
                        <td class="px-4 py-4 whitespace-nowrap">
                            @if($pwd->zone_purok)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-slate-100 text-slate-700 border border-slate-200">
                                    <i class="fas fa-location-dot text-slate-400 text-[9px]"></i> {{ $pwd->zone_purok }}
                                </span>
                            @else
                                <span class="text-slate-300 text-sm">—</span>
                            @endif
                        </td>

                        {{-- BINAGO: Household Column --}}
                        <td class="px-4 py-4 whitespace-nowrap">
                            @if($pwd->familyProfile)
                                <a href="{{ route('bhw.family_profiling.show', $pwd->familyProfile->id) }}"
                                   class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-emerald-100 text-slate-700 hover:text-emerald-800 font-mono text-[10px] font-black border border-slate-200 transition"
                                   title="View household">
                                    <i class="fas fa-home text-slate-400 text-[9px]"></i> {{ $pwd->familyProfile->family_code }}
                                </a>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-50 text-slate-400 border border-dashed border-slate-300">
                                    No household
                                </span>
                            @endif
                        </td>

                        {{-- Classifications --}}
                        <td class="px-4 py-4 text-center">
                            <div class="flex flex-wrap justify-center gap-1">
                                <!-- Always show PWD badge -->
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-black bg-cyan-50 text-cyan-700 border border-cyan-100" title="Person with Disability">
                                    <i class="fas fa-wheelchair text-[8px]"></i> PWD
                                </span>
                                @if($pwd->is_senior)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-black bg-amber-50 text-amber-700 border border-amber-100" title="Senior Citizen">
                                        <i class="fas fa-user-clock text-[8px]"></i> Senior
                                    </span>
                                @endif
                                @if($pwd->is_4ps)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-black bg-purple-50 text-purple-700 border border-purple-100" title="4Ps Beneficiary">
                                        <i class="fas fa-hand-holding-heart text-[8px]"></i> 4Ps
                                    </span>
                                @endif
                            </div>
                        </td>

                        {{-- Actions --}}
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('bhw.residents.edit', $pwd->id) }}" 
                                   class="h-8 w-8 rounded-lg bg-cyan-50 text-cyan-600 hover:bg-cyan-600 hover:text-white flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md" 
                                   title="Edit Record">
                                    <i class="fas fa-pen text-xs"></i>
                                </a>
                                <form method="POST" action="{{ route('bhw.residents.destroy', $pwd->id) }}" 
                                      onsubmit="return confirm('Delete this PWD record? This action cannot be undone.')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" 
                                            class="h-8 w-8 rounded-lg bg-rose-50 text-rose-500 hover:bg-rose-600 hover:text-white flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md" 
                                            title="Delete Record">
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
                                <div class="h-16 w-16 rounded-full bg-cyan-50 border border-cyan-100 flex items-center justify-center">
                                    <i class="fas fa-wheelchair text-cyan-300 text-2xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-700">No PWD records found</p>
                                    <p class="text-xs text-slate-400 mt-1">Get started by adding your first PWD record.</p>
                                </div>
                                <a href="{{ route('bhw.residents.create') }}" 
                                   class="mt-2 inline-flex items-center gap-2 bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition shadow-sm">
                                    <i class="fas fa-plus-circle"></i> Add First PWD Record
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($pwds, 'hasPages') && $pwds->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $pwds->links() }}
        </div>
        @endif
    </div>
</div>
@endsection