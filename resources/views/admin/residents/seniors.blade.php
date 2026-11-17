@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-slate-100">

    <!-- ============ TOP ACTION BAR ============ -->
    <div class="bg-white border-b border-slate-200 shadow-sm">
        <div class="px-4 sm:px-6 lg:px-8 py-3">
            <div class="flex items-center justify-between gap-4">
                <!-- Left: Breadcrumb -->
                <div class="flex items-center gap-3 min-w-0">
                    <div class="h-9 w-9 flex items-center justify-center rounded-lg bg-orange-100 text-orange-600 flex-shrink-0">
                        <i class="fas fa-user-clock text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <nav class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                            <span>Dashboard</span>
                            <i class="fas fa-chevron-right text-[8px]"></i>
                            <span class="text-slate-700">Seniors List</span>
                        </nav>
                        <h1 class="text-sm sm:text-base font-black text-slate-800 tracking-tight truncate">
                            Senior Citizens
                        </h1>
                    </div>
                </div>

                <!-- Right: Actions -->
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-50 text-orange-700 rounded-lg text-[10px] font-bold uppercase tracking-wider border border-orange-200">
                        <i class="fas fa-user-clock text-[10px]"></i>
                        <span class="hidden sm:inline">{{ $residents->count() }} Seniors</span>
                        <span class="sm:hidden">{{ $residents->count() }}</span>
                    </span>
                    <a href="{{ route('admin.residents.create') }}" 
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[10px] font-bold uppercase tracking-wider shadow-sm transition-colors">
                        <i class="fas fa-plus text-[10px]"></i>
                        <span class="hidden sm:inline">New Entry</span>
                        <span class="sm:hidden">New</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ============ ALERT MESSAGES ============ -->
    <div class="px-4 sm:px-6 lg:px-8 pt-4">
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 x-transition:leave="transition ease-in duration-300"
                 class="flex items-center gap-3 px-4 py-3 bg-emerald-50 border-l-4 border-emerald-500 rounded-lg shadow-sm">
                <i class="fas fa-check-circle text-emerald-600"></i>
                <span class="text-sm font-semibold text-emerald-800 flex-1">{{ session('success') }}</span>
                <button @click="show = false" class="text-emerald-400 hover:text-emerald-600">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        @endif
    </div>

    <!-- ============ MAIN CONTENT ============ -->
    <div class="px-4 sm:px-6 lg:px-8 py-6 space-y-4">

        <!-- ===== STATS ROW ===== -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <!-- Total Seniors -->
            <div class="bg-white border border-slate-200 rounded-lg px-4 py-3 flex items-center gap-3">
                <div class="h-10 w-10 bg-orange-100 rounded-lg flex items-center justify-center text-orange-600 flex-shrink-0">
                    <i class="fas fa-user-clock text-base"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Total Seniors</p>
                    <p class="text-lg font-black text-orange-600 leading-tight">{{ $residents->count() }}</p>
                </div>
            </div>

            <!-- Male -->
            <div class="bg-white border border-slate-200 rounded-lg px-4 py-3 flex items-center gap-3">
                <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 flex-shrink-0">
                    <i class="fas fa-mars text-base"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Male</p>
                    <p class="text-lg font-black text-slate-800 leading-tight">{{ $residents->where('gender', 'Male')->count() }}</p>
                </div>
            </div>

            <!-- Female -->
            <div class="bg-white border border-slate-200 rounded-lg px-4 py-3 flex items-center gap-3">
                <div class="h-10 w-10 bg-pink-100 rounded-lg flex items-center justify-center text-pink-600 flex-shrink-0">
                    <i class="fas fa-venus text-base"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Female</p>
                    <p class="text-lg font-black text-slate-800 leading-tight">{{ $residents->where('gender', 'Female')->count() }}</p>
                </div>
            </div>

            <!-- Zones Covered -->
            <div class="bg-white border border-slate-200 rounded-lg px-4 py-3 flex items-center gap-3">
                <div class="h-10 w-10 bg-amber-100 rounded-lg flex items-center justify-center text-amber-600 flex-shrink-0">
                    <i class="fas fa-map-marker-alt text-base"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Zones</p>
                    <p class="text-lg font-black text-amber-600 leading-tight">{{ $zones->count() }}</p>
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
                        <h2 class="text-xs font-black text-slate-800 uppercase tracking-wider">Filter Seniors</h2>
                        <p class="text-[10px] text-slate-500">Search and narrow down the list</p>
                    </div>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.seniors') }}" class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                    <!-- Search -->
                    <div class="sm:col-span-6">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1.5">Search</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Name or username..."
                                   class="w-full pl-9 pr-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                        </div>
                    </div>

                    <!-- Zone -->
                    <div class="sm:col-span-3">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1.5">Zone / Purok</label>
                        <select name="zone"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all cursor-pointer">
                            <option value="">All Zones</option>
                            @foreach($zones as $zone)
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
                        <a href="{{ route('admin.seniors') }}" 
                           class="px-3 py-2 bg-white border border-slate-300 text-slate-600 text-xs font-bold rounded-md hover:bg-slate-50 transition-colors flex items-center justify-center gap-1.5">
                            <i class="fas fa-redo text-[10px]"></i>
                        </a>
                    </div>
                </div>

                <!-- Active Filters -->
                @if(request('search') || request('zone'))
                    <div class="mt-3 pt-3 border-t border-slate-200 flex flex-wrap items-center gap-2">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Active:</span>
                        @if(request('search'))
                            <span class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2 py-1 rounded-md border border-emerald-200">
                                <i class="fas fa-search text-[8px]"></i>
                                "{{ request('search') }}"
                                <a href="{{ route('admin.seniors', array_merge(request()->except('search'), ['search' => null])) }}" class="hover:text-emerald-900">✕</a>
                            </span>
                        @endif
                        @if(request('zone'))
                            <span class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2 py-1 rounded-md border border-emerald-200">
                                <i class="fas fa-map-pin text-[8px]"></i>
                                {{ request('zone') }}
                                <a href="{{ route('admin.seniors', array_merge(request()->except('zone'), ['zone' => null])) }}" class="hover:text-emerald-900">✕</a>
                            </span>
                        @endif
                    </div>
                @endif
            </form>
        </div>

        <!-- ===== TABLE CARD ===== -->
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
            <!-- Table Header -->
            <div class="px-5 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="h-7 w-7 bg-orange-100 rounded-md flex items-center justify-center text-orange-600">
                        <i class="fas fa-list text-xs"></i>
                    </div>
                    <div>
                        <h2 class="text-xs font-black text-slate-800 uppercase tracking-wider">Seniors Directory</h2>
                        <p class="text-[10px] text-slate-500">{{ $residents->count() }} {{ $residents->count() === 1 ? 'record' : 'records' }} found</p>
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
                            <th class="px-4 py-3 text-[10px] font-black text-slate-500 uppercase tracking-wider">Full Name</th>
                            <th class="px-4 py-3 text-[10px] font-black text-slate-500 uppercase tracking-wider">Gender</th>
                            <th class="px-4 py-3 text-[10px] font-black text-slate-500 uppercase tracking-wider">Zone / Purok</th>
                            <th class="px-4 py-3 text-[10px] font-black text-slate-500 uppercase tracking-wider">Age</th>
                            <th class="px-4 py-3 text-[10px] font-black text-slate-500 uppercase tracking-wider text-center w-32">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($residents as $index => $res)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <!-- Row Number -->
                            <td class="px-4 py-3 text-xs text-slate-500 font-mono text-center">
                                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                            </td>

                            <!-- Full Name + Avatar -->
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-lg overflow-hidden flex-shrink-0 ring-1 ring-slate-200 flex items-center justify-center bg-gradient-to-br from-orange-500 to-orange-700">
                                        @if($res->profile_photo_url)
                                            <img src="{{ $res->profile_photo_url }}" alt="{{ $res->first_name }}" class="h-full w-full object-cover">
                                        @else
                                            <span class="text-white text-xs font-black">
                                                {{ strtoupper(substr($res->first_name, 0, 1) . ($res->middle_name ? substr($res->middle_name, 0, 1) : '') . substr($res->last_name, 0, 1)) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-slate-800 uppercase leading-tight">
                                            {{-- ✅ Kasama na ang middle name --}}
                                            {{ $res->last_name }}, {{ $res->first_name }}{{ $res->middle_name ? ' ' . $res->middle_name : '' }}
                                        </p>
                                        <p class="text-[10px] text-slate-400 font-mono mt-0.5">
                                            {{ '@' . $res->username }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- Gender -->
                            <td class="px-4 py-3">
                                @if($res->gender == 'Male')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-100 text-blue-700 rounded-md text-[10px] font-bold uppercase border border-blue-200">
                                        <i class="fas fa-mars text-[9px]"></i> Male
                                    </span>
                                @elseif($res->gender == 'Female')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-pink-100 text-pink-700 rounded-md text-[10px] font-bold uppercase border border-pink-200">
                                        <i class="fas fa-venus text-[9px]"></i> Female
                                    </span>
                                @else
                                    <span class="text-[10px] text-slate-400 italic">—</span>
                                @endif
                            </td>

                            <!-- Zone -->
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-slate-700 rounded-md text-[10px] font-bold uppercase border border-slate-200">
                                    <i class="fas fa-map-pin text-[9px]"></i>
                                    {{ $res->zone_purok ?? 'N/A' }}
                                </span>
                            </td>

                            <!-- Age -->
                            <td class="px-4 py-3">
                                @if($res->date_of_birth)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-orange-50 text-orange-700 rounded-md text-[10px] font-black uppercase border border-orange-200">
                                        <i class="fas fa-cake text-[9px]"></i>
                                        {{ \Carbon\Carbon::parse($res->date_of_birth)->age }} yrs
                                    </span>
                                @else
                                    <span class="text-[10px] text-slate-400 italic">—</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-3">
                                <div class="flex justify-center items-center gap-1">
                                    <a href="{{ route('admin.residents.show', $res->id) }}" 
                                       class="h-8 w-8 flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-md transition-all"
                                       title="View Profile">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('admin.residents.edit', $res->id) }}" 
                                       class="h-8 w-8 flex items-center justify-center text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-md transition-all"
                                       title="Edit Resident">
                                        <i class="fas fa-pen text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.residents.destroy', $res->id) }}" method="POST" 
                                          onsubmit="return confirm('Warning: Permanenteng mabubura ang account ni {{ $res->first_name }}. Magpatuloy?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="h-8 w-8 flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition-all"
                                                title="Delete Resident">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-16 w-16 bg-orange-50 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-user-clock text-2xl text-orange-300"></i>
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-700 mb-1">No seniors found</h3>
                                    <p class="text-xs text-slate-400 mb-4">Try adjusting your filters or check back later.</p>
                                    <a href="{{ route('admin.seniors') }}" 
                                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-md shadow-sm transition-colors">
                                        <i class="fas fa-redo text-[10px]"></i>
                                        Clear Filters
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection