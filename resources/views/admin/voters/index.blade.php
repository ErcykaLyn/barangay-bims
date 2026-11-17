@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-slate-100">

    <!-- ============ TOP ACTION BAR ============ -->
    <div class="bg-white border-b border-slate-200 shadow-sm">
        <div class="px-4 sm:px-6 lg:px-8 py-3">
            <div class="flex items-center justify-between gap-4">
                <!-- Left: Breadcrumb -->
                <div class="flex items-center gap-3 min-w-0">
                    <div class="h-9 w-9 flex items-center justify-center rounded-lg bg-purple-100 text-purple-600 flex-shrink-0">
                        <i class="fas fa-vote-yea text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <nav class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                            <span>Dashboard</span>
                            <i class="fas fa-chevron-right text-[8px]"></i>
                            <span class="text-slate-700">Voters</span>
                        </nav>
                        <h1 class="text-sm sm:text-base font-black text-slate-800 tracking-tight truncate">
                            Registered Voters Directory
                        </h1>
                    </div>
                </div>

                <!-- Right: Badge -->
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-50 text-purple-700 rounded-lg text-[10px] font-bold uppercase tracking-wider border border-purple-200">
                        <i class="fas fa-check-circle text-[10px]"></i>
                        <span class="hidden sm:inline">{{ $voters->count() }} Registered</span>
                        <span class="sm:hidden">{{ $voters->count() }}</span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- ============ MAIN CONTENT ============ -->
    <div class="px-4 sm:px-6 lg:px-8 py-6 space-y-4">

        <!-- ===== STATS ROW ===== -->
        @php
            $totalVoters = $voters->count();
            $maleVoters = $voters->where('gender', 'Male')->count();
            $femaleVoters = $voters->where('gender', 'Female')->count();
            $seniorVoters = $voters->where('is_senior', true)->count();
            $pwdVoters = $voters->where('is_pwd', true)->count();
        @endphp

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5 sm:gap-3">
            <!-- Total -->
            <div class="bg-white border border-slate-200 rounded-lg px-4 py-3 flex items-center gap-3">
                <div class="h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600 flex-shrink-0">
                    <i class="fas fa-vote-yea text-base"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Total Voters</p>
                    <p class="text-lg font-black text-slate-800 leading-tight">{{ $totalVoters }}</p>
                </div>
            </div>

            <!-- Male -->
            <div class="bg-white border border-slate-200 rounded-lg px-4 py-3 flex items-center gap-3">
                <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 flex-shrink-0">
                    <i class="fas fa-mars text-base"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Male</p>
                    <p class="text-lg font-black text-slate-800 leading-tight">{{ $maleVoters }}</p>
                </div>
            </div>

            <!-- Female -->
            <div class="bg-white border border-slate-200 rounded-lg px-4 py-3 flex items-center gap-3">
                <div class="h-10 w-10 bg-pink-100 rounded-lg flex items-center justify-center text-pink-600 flex-shrink-0">
                    <i class="fas fa-venus text-base"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Female</p>
                    <p class="text-lg font-black text-slate-800 leading-tight">{{ $femaleVoters }}</p>
                </div>
            </div>

            <!-- Seniors -->
            <div class="bg-white border border-slate-200 rounded-lg px-4 py-3 flex items-center gap-3">
                <div class="h-10 w-10 bg-orange-100 rounded-lg flex items-center justify-center text-orange-600 flex-shrink-0">
                    <i class="fas fa-user-clock text-base"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Seniors</p>
                    <p class="text-lg font-black text-slate-800 leading-tight">{{ $seniorVoters }}</p>
                </div>
            </div>

            <!-- PWD -->
            <div class="bg-white border border-slate-200 rounded-lg px-4 py-3 flex items-center gap-3">
                <div class="h-10 w-10 bg-cyan-100 rounded-lg flex items-center justify-center text-cyan-600 flex-shrink-0">
                    <i class="fas fa-wheelchair text-base"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">PWD</p>
                    <p class="text-lg font-black text-slate-800 leading-tight">{{ $pwdVoters }}</p>
                </div>
            </div>
        </div>

        <!-- ===== FILTER CARD ===== -->
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
            <div class="px-5 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="h-7 w-7 bg-slate-200 rounded-md flex items-center justify-center text-slate-600">
                        <i class="fas fa-filter text-xs"></i>
                    </div>
                    <div>
                        <h2 class="text-xs font-black text-slate-800 uppercase tracking-wider">Filter Voters</h2>
                        <p class="text-[10px] text-slate-500">Search and narrow down the list</p>
                    </div>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.voters.index') }}" class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                    <!-- Search -->
                    <div class="sm:col-span-6">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1.5">Search</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Name, username, or address..."
                                   class="w-full pl-9 pr-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                        </div>
                    </div>

                    <!-- Zone -->
                    <div class="sm:col-span-3">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1.5">Zone / Purok</label>
                        <select name="zone"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all cursor-pointer">
                            <option value="">All Zones</option>
                            @foreach(($zones ?? []) as $zone)
                                <option value="{{ $zone }}" {{ request('zone') == $zone ? 'selected' : '' }}>{{ $zone }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="sm:col-span-3 flex items-end gap-2">
                        <button type="submit" 
                            class="flex-1 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-md shadow-sm transition-colors flex items-center justify-center gap-1.5">
                            <i class="fas fa-search text-[10px]"></i> Filter
                        </button>
                        <a href="{{ route('admin.voters.index') }}" 
                           class="px-3 py-2 bg-white border border-slate-300 text-slate-600 text-xs font-bold rounded-md hover:bg-slate-50 transition-colors flex items-center justify-center gap-1.5">
                            <i class="fas fa-redo text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- ===== TABLE CARD ===== -->
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
            <!-- Table Header -->
            <div class="px-5 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="h-7 w-7 bg-purple-100 rounded-md flex items-center justify-center text-purple-600">
                        <i class="fas fa-list text-xs"></i>
                    </div>
                    <div>
                        <h2 class="text-xs font-black text-slate-800 uppercase tracking-wider">Voters Registry</h2>
                        <p class="text-[10px] text-slate-500">{{ $totalVoters }} {{ $totalVoters === 1 ? 'voter' : 'voters' }} found</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider hidden sm:block">
                    Sorted by Latest
                </span>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[900px]">
                    <thead class="bg-slate-50/50 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-[10px] font-black text-slate-500 uppercase tracking-wider text-center w-12">#</th>
                            <th class="px-4 py-3 text-[10px] font-black text-slate-500 uppercase tracking-wider">Voter Name</th>
                            <th class="px-4 py-3 text-[10px] font-black text-slate-500 uppercase tracking-wider">Zone</th>
                            <th class="px-4 py-3 text-[10px] font-black text-slate-500 uppercase tracking-wider">Address</th>
                            <th class="px-4 py-3 text-[10px] font-black text-slate-500 uppercase tracking-wider">Classifications</th>
                            <th class="px-4 py-3 text-[10px] font-black text-slate-500 uppercase tracking-wider text-center w-20">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($voters as $index => $voter)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <!-- Row Number -->
                            <td class="px-4 py-3 text-xs text-slate-500 font-mono text-center">
                                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                            </td>

                            <!-- Voter Name -->
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-lg overflow-hidden flex-shrink-0 ring-1 ring-slate-200 flex items-center justify-center bg-gradient-to-br from-purple-500 to-purple-700">
                                        @if($voter->profile_photo_url)
                                            <img src="{{ $voter->profile_photo_url }}" alt="{{ $voter->first_name }}" class="h-full w-full object-cover">
                                        @else
                                            <span class="text-white text-xs font-black">
                                                {{ strtoupper(substr($voter->first_name, 0, 1) . substr($voter->last_name, 0, 1)) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-slate-800 leading-tight flex items-center gap-1.5">
                                            {{ $voter->last_name }}, {{ $voter->first_name }}
                                            @if($voter->gender == 'Male')
                                                <i class="fas fa-mars text-blue-500 text-[10px]" title="Male"></i>
                                            @elseif($voter->gender == 'Female')
                                                <i class="fas fa-venus text-pink-500 text-[10px]" title="Female"></i>
                                            @endif
                                        </p>
                                        <p class="text-[10px] text-slate-400 font-mono mt-0.5">
                                            {{ '@' . $voter->username }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- Zone -->
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-slate-700 rounded-md text-[10px] font-bold uppercase border border-slate-200">
                                    <i class="fas fa-map-pin text-[9px]"></i>
                                    {{ $voter->zone_purok ?? 'N/A' }}
                                </span>
                            </td>

                            <!-- Address -->
                            <td class="px-4 py-3 max-w-xs">
                                @if($voter->complete_address)
                                    <div class="flex items-start gap-1.5">
                                        <i class="fas fa-home text-slate-400 text-[10px] mt-0.5 flex-shrink-0"></i>
                                        <span class="text-xs text-slate-600 truncate">{{ Str::limit($voter->complete_address, 40) }}</span>
                                    </div>
                                @else
                                    <span class="text-[10px] text-slate-400 italic">—</span>
                                @endif
                            </td>

                            <!-- Classifications -->
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @if($voter->is_senior)
                                        <span class="px-2 py-0.5 bg-orange-100 text-orange-700 rounded text-[9px] font-black uppercase border border-orange-200">Senior</span>
                                    @endif
                                    @if($voter->is_pwd)
                                        <span class="px-2 py-0.5 bg-cyan-100 text-cyan-700 rounded text-[9px] font-black uppercase border border-cyan-200">PWD</span>
                                    @endif
                                    @if($voter->is_4ps)
                                        <span class="px-2 py-0.5 bg-pink-100 text-pink-700 rounded text-[9px] font-black uppercase border border-pink-200">4Ps</span>
                                    @endif
                                    @if($voter->is_solo_parent)
                                        <span class="px-2 py-0.5 bg-rose-100 text-rose-700 rounded text-[9px] font-black uppercase border border-rose-200">Solo</span>
                                    @endif
                                    <span class="px-2 py-0.5 bg-purple-100 text-purple-700 rounded text-[9px] font-black uppercase border border-purple-200">
                                        <i class="fas fa-check text-[7px] mr-0.5"></i>Voter
                                    </span>
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-3">
                                <div class="flex justify-center items-center gap-1">
                                    <a href="{{ route('admin.residents.show', $voter->id) }}" 
                                       class="h-8 w-8 flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-md transition-all"
                                       title="View Profile">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('admin.residents.edit', $voter->id) }}" 
                                       class="h-8 w-8 flex items-center justify-center text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-md transition-all"
                                       title="Edit Voter">
                                        <i class="fas fa-pen text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-16 w-16 bg-purple-50 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-vote-yea text-2xl text-purple-300"></i>
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-700 mb-1">No registered voters found</h3>
                                    <p class="text-xs text-slate-400 mb-4">Try adjusting your filters or add new voters.</p>
                                    <a href="{{ route('admin.residents.create') }}" 
                                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-md shadow-sm transition-colors">
                                        <i class="fas fa-user-plus text-[10px]"></i>
                                        Add New Resident
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(method_exists($voters, 'links') && $voters->hasPages())
                <div class="px-5 py-3 border-t border-slate-200 bg-slate-50">
                    {{ $voters->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection