@extends('layouts.bhw')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 max-w-7xl mx-auto">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white shadow-md">
                <i class="fas fa-users text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight">Residents List</h1>
                <p class="text-sm text-slate-500 mt-0.5">
                    Manage all registered residents
                    <span class="inline-flex items-center gap-1 ml-2 px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-100">
                        <i class="fas fa-database text-[8px]"></i>
                        {{ $residents->count() }} total
                    </span>
                </p>
            </div>
        </div>
        <a href="{{ route('bhw.residents.create') }}" 
           class="group bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white px-5 py-2.5 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 flex items-center gap-2 w-fit font-bold text-sm">
            <i class="fas fa-plus group-hover:rotate-90 transition-transform duration-300"></i>
            Add Resident
        </a>
    </div>

    <!-- Mga mensahe -->
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

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 mb-6">
        <form method="GET" action="{{ route('bhw.residents.index') }}" class="flex flex-col md:flex-row gap-3">
            <div class="flex-1">
                <div class="relative group">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm group-focus-within:text-emerald-500 transition-colors"></i>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search by name..." 
                           class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition text-sm bg-slate-50/50 focus:bg-white">
                </div>
            </div>

            <div class="w-full md:w-52">
                <div class="relative group">
                    <i class="fas fa-map-pin absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm group-focus-within:text-emerald-500 transition-colors"></i>
                    <select name="zone" 
                            class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 appearance-none bg-slate-50/50 focus:bg-white text-sm cursor-pointer">
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

            <div class="w-full md:w-56">
                <div class="relative group">
                    <i class="fas fa-filter absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm group-focus-within:text-emerald-500 transition-colors"></i>
                    <select name="filter" 
                            class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 appearance-none bg-slate-50/50 focus:bg-white text-sm cursor-pointer">
                        <option value="">All residents</option>
                        <option value="needs_action" {{ request('filter') === 'needs_action' ? 'selected' : '' }}>Needs action</option>
                        <option value="no_household" {{ request('filter') === 'no_household' ? 'selected' : '' }}>Not in a household</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit" 
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl transition flex items-center gap-2 text-sm font-bold shadow-sm hover:shadow-md">
                    <i class="fas fa-filter text-xs"></i> Filter
                </button>
                <a href="{{ route('bhw.residents.index') }}" 
                   class="border border-slate-300 text-slate-600 px-4 py-2.5 rounded-xl hover:bg-slate-50 hover:border-slate-400 transition flex items-center gap-2 text-sm font-bold">
                    <i class="fas fa-undo-alt text-xs"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                    <i class="fas fa-list-ul text-xs"></i>
                </div>
                <div>
                    <h2 class="font-black text-slate-800 text-sm tracking-tight">All Residents</h2>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Barangay Bactad East</p>
                </div>
            </div>
            @if(request('search') || request('zone') || request('filter'))
                <div class="hidden sm:flex items-center gap-2 text-[10px] font-bold text-slate-500">
                    <i class="fas fa-info-circle text-emerald-500"></i>
                    <span>Filtered results: <span class="text-emerald-600">{{ $residents->count() }}</span></span>
                </div>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/80">
                    <tr>
                        <th class="px-4 py-3.5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">#</th>
                        <th class="px-4 py-3.5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Name</th>
                        <th class="px-4 py-3.5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Household</th>
                        <th class="px-4 py-3.5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Age</th>
                        <th class="px-4 py-3.5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Zone</th>
                        <th class="px-4 py-3.5 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Classifications</th>
                        <th class="px-4 py-3.5 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 bg-white">
                    @forelse($residents as $r)
                    @php
                        $fam = $r->familyProfile;
                        $isHead = $fam
                            && mb_strtolower(trim((string) $fam->head_first_name)) === mb_strtolower(trim((string) $r->first_name))
                            && mb_strtolower(trim((string) $fam->head_last_name)) === mb_strtolower(trim((string) $r->last_name));
                    @endphp
                    <tr class="hover:bg-emerald-50/30 transition duration-150 group {{ $r->is_deceased ? 'opacity-60 bg-slate-50' : '' }}">
                        <td class="px-4 py-4 whitespace-nowrap text-xs text-slate-400 font-bold">
                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                        </td>

                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full overflow-hidden flex-shrink-0 ring-2 ring-emerald-100 shadow-sm flex items-center justify-center bg-emerald-50">
                                    @if($r->profile_photo_url)
                                        <img src="{{ $r->profile_photo_url }}" 
                                             alt="{{ $r->first_name }} {{ $r->last_name }}" 
                                             class="h-full w-full object-cover">
                                    @else
                                        <span class="text-white text-[10px] font-black bg-gradient-to-br from-emerald-400 to-emerald-600 h-full w-full flex items-center justify-center">
                                            {{ strtoupper(substr($r->first_name, 0, 1)) }}{{ strtoupper(substr($r->last_name, 0, 1)) }}
                                        </span>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-800 group-hover:text-emerald-700 transition {{ $r->is_deceased ? 'line-through' : '' }}">
                                        {{ $r->first_name }} {{ $r->middle_name ?? '' }} {{ $r->last_name }}
                                    </p>
                                    <div class="flex items-center gap-1.5 flex-wrap mt-0.5">
                                        <p class="text-[10px] text-slate-400 font-medium">
                                            {{ $r->civil_status ?? 'Resident' }}
                                        </p>
                                        <x-needs-action-badge :model="$r" :url="route('bhw.residents.edit', $r->id)" />
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td class="px-4 py-4 whitespace-nowrap">
                            @if($fam)
                                <a href="{{ route('bhw.family_profiling.show', $fam->id) }}"
                                   class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-emerald-100 text-slate-700 hover:text-emerald-800 font-mono text-[10px] font-black border border-slate-200 transition"
                                   title="View household">
                                    <i class="fas fa-home text-slate-400 text-[9px]"></i> {{ $fam->family_code }}
                                </a>
                                @if($isHead)
                                    <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded bg-emerald-600 text-white text-[9px] font-black uppercase">Head</span>
                                @endif
                            @else
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-50 text-slate-400 border border-dashed border-slate-300">
                                        No household
                                    </span>
                                    @if(!$r->is_deceased)
                                        <form method="POST" action="{{ route('bhw.residents.make-head', $r->id) }}" class="inline" onsubmit="return confirm('Gawing Head of the Family si {{ addslashes($r->first_name) }}? Magagawa siya ng bagong household.')">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white text-[9px] font-black uppercase transition shadow-sm" title="Make as Head of New Household">
                                                <i class="fas fa-crown text-[8px]"></i> Make Head
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        </td>

                        <td class="px-4 py-4 whitespace-nowrap">
                            @php
                                $age = $r->age ?? ($r->date_of_birth ? \Carbon\Carbon::parse($r->date_of_birth)->age : null);
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

                        <td class="px-4 py-4 whitespace-nowrap">
                            @if($r->zone_purok)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-slate-100 text-slate-700 border border-slate-200">
                                    <i class="fas fa-location-dot text-slate-400 text-[9px]"></i> {{ $r->zone_purok }}
                                </span>
                            @else
                                <span class="text-slate-300 text-sm">—</span>
                            @endif
                        </td>

                        <td class="px-4 py-4 text-center">
                            <div class="flex flex-wrap justify-center gap-1">
                                {{-- DECEASED BADGE --}}
                                @if($r->is_deceased)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-black bg-slate-800 text-white border border-slate-900">
                                        <i class="fas fa-cross text-[8px]"></i> Deceased
                                    </span>
                                @endif

                                @if($r->is_senior)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-black bg-amber-50 text-amber-700 border border-amber-100">
                                        <i class="fas fa-user-clock text-[8px]"></i> Senior
                                    </span>
                                @endif
                                @if($r->is_pwd)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-black bg-blue-50 text-blue-700 border border-blue-100">
                                        <i class="fas fa-wheelchair text-[8px]"></i> PWD
                                    </span>
                                @endif
                                @if($r->is_4ps)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-black bg-purple-50 text-purple-700 border border-purple-100">
                                        <i class="fas fa-hand-holding-heart text-[8px]"></i> 4Ps
                                    </span>
                                @endif
                                @if(!$r->is_senior && !$r->is_pwd && !$r->is_4ps && !$r->is_deceased)
                                    <span class="text-slate-300 text-xs">—</span>
                                @endif
                            </div>
                        </td>

                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-1">
                                {{-- VIEW / SHOW BUTTON --}}
                                <a href="{{ route('bhw.residents.show', $r->id) }}" 
                                   class="h-8 w-8 rounded-lg bg-slate-50 text-slate-500 hover:bg-slate-700 hover:text-white flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md" 
                                   title="View Resident Profile">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>

                                @if($fam && !$r->is_deceased)
                                    <a href="{{ route('bhw.residents.create', ['household' => $fam->id]) }}"
                                       class="h-8 w-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md"
                                       title="Add family member to {{ $fam->family_code }}">
                                        <i class="fas fa-user-plus text-xs"></i>
                                    </a>
                                @endif
                                @if(!$r->is_deceased)
                                    <button type="button" 
                                            onclick="openDeceasedModal({{ $r->id }}, '{{ addslashes($r->first_name) }} {{ addslashes($r->last_name) }}')"
                                            class="h-8 w-8 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-800 hover:text-white flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md" 
                                            title="Mark as Deceased">
                                        <i class="fas fa-cross text-xs"></i>
                                    </button>
                                @endif
                                <a href="{{ route('bhw.residents.edit', $r->id) }}" 
                                   class="h-8 w-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md" 
                                   title="Edit Resident">
                                    <i class="fas fa-pen text-xs"></i>
                                </a>
                                <form method="POST" action="{{ route('bhw.residents.destroy', $r->id) }}" 
                                      onsubmit="return confirm('Delete this resident? This action cannot be undone.')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" 
                                            class="h-8 w-8 rounded-lg bg-rose-50 text-rose-500 hover:bg-rose-600 hover:text-white flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md" 
                                            title="Delete Resident">
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
                                <div class="h-16 w-16 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center">
                                    <i class="fas fa-users text-slate-300 text-2xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-700">No residents found</p>
                                    <p class="text-xs text-slate-400 mt-1">Get started by adding your first resident record.</p>
                                </div>
                                <a href="{{ route('bhw.residents.create') }}" 
                                   class="mt-2 inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition shadow-sm">
                                    <i class="fas fa-plus-circle"></i> Add First Resident
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($residents, 'hasPages') && $residents->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $residents->links() }}
        </div>
        @endif
    </div>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 p-4 flex items-center gap-3 shadow-sm">
            <div class="h-10 w-10 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                <i class="fas fa-user-check text-sm"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Active Records</p>
                <p class="text-lg font-black text-slate-800">{{ $residents->where('is_deceased', false)->count() }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-4 flex items-center gap-3 shadow-sm">
            <div class="h-10 w-10 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600">
                <i class="fas fa-cross text-sm"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Deceased</p>
                <p class="text-lg font-black text-slate-800">{{ $residents->where('is_deceased', true)->count() }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-4 flex items-center gap-3 shadow-sm">
            <div class="h-10 w-10 rounded-lg bg-cyan-50 border border-cyan-100 flex items-center justify-center text-cyan-600">
                <i class="fas fa-map-marked-alt text-sm"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Zones</p>
                <p class="text-lg font-black text-slate-800">{{ $zones->count() }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-4 flex items-center gap-3 shadow-sm">
            <div class="h-10 w-10 rounded-lg bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600">
                <i class="fas fa-house-circle-xmark text-sm"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Not in a household</p>
                <p class="text-lg font-black text-slate-800">{{ $residents->whereNull('family_profile_id')->count() }}</p>
            </div>
        </div>
    </div>
</div>

<div id="deceasedModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="deceasedTitle">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden transform transition-all">
        <div class="px-4 py-3 bg-gradient-to-r from-slate-700 to-slate-900 flex justify-between items-center">
            <h3 id="deceasedTitle" class="text-base font-bold text-white flex items-center gap-2">
                <i class="fas fa-cross text-slate-300"></i> Mark as Deceased
            </h3>
            <button type="button" onclick="closeDeceasedModal()" class="h-7 w-7 rounded-lg text-white/80 hover:text-white hover:bg-white/15 transition" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="deceasedForm" method="POST" class="p-4 space-y-3">
            @csrf
            @method('PATCH')
            
            <div class="rounded-xl bg-slate-50 border border-slate-100 px-3 py-2.5 text-sm text-slate-600">
                <p class="flex justify-between gap-3">
                    <span>Resident</span> 
                    <strong id="deceasedResidentName" class="text-slate-800 text-right"></strong>
                </p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Date of Death <span class="text-rose-500">*</span></label>
                <input type="date" name="date_of_death" id="deceasedDateInput" required
                       max="{{ date('Y-m-d') }}"
                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm text-slate-800 focus:ring-2 focus:ring-slate-500/25 focus:border-slate-500 outline-none transition">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Cause of Death <span class="text-slate-400 font-normal">(optional)</span></label>
                <textarea name="cause_of_death" rows="2" maxlength="500" placeholder="Optional: Heart attack, old age, etc."
                          class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm text-slate-800 focus:ring-2 focus:ring-slate-500/25 focus:border-slate-500 outline-none transition"></textarea>
            </div>

            <div class="pt-1 flex justify-end gap-2">
                <button type="button" onclick="closeDeceasedModal()" class="px-4 py-2 bg-white border border-slate-300 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-50 transition">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 bg-gradient-to-br from-slate-700 to-slate-900 hover:from-slate-800 hover:to-slate-950 text-white text-sm font-semibold rounded-xl shadow-sm shadow-slate-600/30 transition">
                    <i class="fas fa-cross mr-1 text-xs"></i> Mark Deceased
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openDeceasedModal(id, name) {
        document.getElementById('deceasedResidentName').textContent = name;
        document.getElementById('deceasedForm').action = "{{ url('bhw/residents') }}/" + id + "/mark-deceased";
        document.getElementById('deceasedDateInput').value = '{{ date('Y-m-d') }}';
        const modal = document.getElementById('deceasedModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    function closeDeceasedModal() {
        const modal = document.getElementById('deceasedModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    document.getElementById('deceasedModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeceasedModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeDeceasedModal();
    });
</script>
@endsection