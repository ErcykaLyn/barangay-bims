@extends('layouts.bhw')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 max-w-[1800px] mx-auto">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-emerald-600 to-emerald-800 flex items-center justify-center text-white shadow-md">
                <i class="fas fa-home text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight">
                    {{ $family->head_formal_name }}
                </h1>
                <p class="text-sm text-slate-500 mt-0.5 flex items-center gap-2">
                    <span class="font-mono font-black text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100">
                        {{ $family->family_code }}
                    </span>
                    <span class="text-slate-300">•</span>
                    <span>{{ $family->zone_purok ?? 'No Zone' }}</span>
                    <span class="text-slate-300">•</span>
                    <span>{{ $family->members->count() }} members</span>
                </p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <!-- BAGONG BUTTON PARA SA LINK RESIDENT -->
            <button type="button" onclick="openLinkModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl shadow-md transition flex items-center gap-2 font-bold text-sm">
                <i class="fas fa-link"></i> Link Existing Resident
            </button>
            <button onclick="window.print()" class="bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-xl shadow-sm transition flex items-center gap-2 font-bold text-sm">
                <i class="fas fa-print"></i> Print
            </button>
            <a href="{{ route('bhw.family_profiling.edit', $family->id) }}" 
               class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl shadow-md transition flex items-center gap-2 font-bold text-sm">
                <i class="fas fa-pen"></i> Edit Household
            </a>
            <a href="{{ route('bhw.family_profiling.index') }}" 
               class="bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-xl shadow-sm transition flex items-center gap-2 font-bold text-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    {{-- FLASH MESSAGE --}}
    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 bg-emerald-50 border-l-4 border-emerald-500 rounded-lg shadow-sm mb-6">
            <i class="fas fa-check-circle text-emerald-600"></i>
            <span class="text-sm font-semibold text-emerald-800 flex-1">{{ session('success') }}</span>
        </div>
    @endif

    {{-- HOUSEHOLD INFO CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Head of Family --}}
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-3">
                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white text-sm font-black shadow-sm">
                    {{ $family->head_initials }}
                </div>
                <div class="h-8 w-8 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                    <i class="fas fa-user-tie text-xs"></i>
                </div>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Head of Family</p>
            <p class="text-sm font-bold text-slate-800 mt-1">{{ $family->head_full_name }}</p>
        </div>

        {{-- Contact --}}
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
            <div class="h-8 w-8 rounded-lg bg-cyan-50 border border-cyan-100 flex items-center justify-center text-cyan-600 mb-3">
                <i class="fas fa-phone text-xs"></i>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Contact Number</p>
            <p class="text-sm font-bold text-slate-800 mt-1">{{ $family->contact_number ?? '—' }}</p>
        </div>

        {{-- Zone --}}
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
            <div class="h-8 w-8 rounded-lg bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 mb-3">
                <i class="fas fa-map-pin text-xs"></i>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Zone / Purok</p>
            <p class="text-sm font-bold text-slate-800 mt-1">{{ $family->zone_purok ?? '—' }}</p>
        </div>

        {{-- Members Count --}}
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
            <div class="h-8 w-8 rounded-lg bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-600 mb-3">
                <i class="fas fa-users text-xs"></i>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Total Members</p>
            <p class="text-sm font-bold text-slate-800 mt-1">{{ $family->members->count() }}</p>
        </div>
    </div>

    {{-- ADDRESS & NOTES --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-3 border-b border-slate-100 bg-slate-50/50">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                <i class="fas fa-info-circle mr-1"></i> Additional Information
            </p>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">Complete Address</p>
                <p class="text-sm text-slate-800 font-medium">{{ $family->complete_address ?? '—' }}</p>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">Parent Family</p>
                @if($family->parentFamily)
                    <a href="{{ route('bhw.family_profiling.show', $family->parentFamily->id) }}" 
                       class="inline-flex items-center gap-1 text-sm font-bold text-emerald-700 hover:text-emerald-800 transition">
                        <i class="fas fa-sitemap text-xs"></i>
                        {{ $family->parentFamily->family_code }} — {{ $family->parentFamily->head_formal_name }}
                    </a>
                @else
                    <p class="text-sm text-slate-500">— Main Household —</p>
                @endif
            </div>
            @if($family->notes)
            <div class="md:col-span-2">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">Notes</p>
                <p class="text-sm text-slate-700 italic bg-amber-50 border-l-4 border-amber-300 px-4 py-3 rounded-r-lg">
                    {{ $family->notes }}
                </p>
            </div>
            @endif
        </div>
    </div>

    {{-- ============================================================
         MEMBERS TABLE (CBMS SPREADSHEET STYLE)
    ============================================================ --}}
    <div class="bg-white rounded-2xl border-2 border-slate-300 overflow-hidden shadow-sm">
        
        {{-- Table Header --}}
        <div class="px-6 py-3 border-b-2 border-slate-300 bg-slate-100 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <i class="fas fa-users text-emerald-600 text-sm"></i>
                <h3 class="font-black text-slate-800 text-xs uppercase tracking-wider">Members of Household</h3>
                <span class="text-[10px] font-bold text-slate-500 bg-white px-2 py-0.5 rounded border border-slate-200">
                    {{ $family->members->count() }} total
                </span>
            </div>
            <a href="{{ route('bhw.family_profiling.edit', $family->id) }}" 
               class="text-[10px] font-black text-emerald-600 hover:text-emerald-700 uppercase tracking-widest flex items-center gap-1 transition">
                <i class="fas fa-pen"></i> Edit Members
            </a>
        </div>

        @if($family->members->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-[3600px] border-collapse text-[10px]">
                <thead class="bg-slate-700 text-white">
                        <tr class="bg-slate-700 text-white">
                            <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center font-black uppercase tracking-wider  min-w-[90px]">Date of Visit</th>
                            <th colspan="3" class="border border-slate-600 px-2 py-2 text-center font-black uppercase tracking-wider ">Name of Household Member</th>
                            <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center font-black uppercase tracking-wider ">Relation to Head</th>
                            <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center font-black uppercase tracking-wider ">Date of Birth</th>
                            <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center font-black uppercase tracking-wider ">Age</th>
                            <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center font-black uppercase tracking-wider ">Sex</th>
                            <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center font-black uppercase tracking-wider ">Civil Status</th>
                            <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center font-black uppercase tracking-wider ">Education</th>
                            <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center font-black uppercase tracking-wider ">Religion</th>
                            <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center font-black uppercase tracking-wider ">Ethnicity</th>
                            <th colspan="4" class="border border-slate-600 px-2 py-2 text-center font-black uppercase tracking-wider bg-indigo-700">Programs</th>
                            <th colspan="3" class="border border-slate-600 px-2 py-2 text-center font-black uppercase tracking-wider bg-cyan-800">PhilHealth</th>
                            <th colspan="4" class="border border-slate-600 px-2 py-2 text-center font-black uppercase tracking-wider bg-rose-700">Health</th>
                            <th colspan="4" class="border border-slate-600 px-2 py-2 text-center font-black uppercase tracking-wider bg-amber-700">Family Planning</th>
                            <th colspan="2" class="border border-slate-600 px-2 py-2 text-center font-black uppercase tracking-wider bg-sky-700">Facilities (WASH)</th>
                        </tr>
                        <tr class="bg-slate-600 text-white">
                            <th class="border border-slate-500 px-2 py-2 text-center font-bold whitespace-nowrap bg-emerald-700 min-w-[130px]">Last Name</th>
                            <th class="border border-slate-500 px-2 py-2 text-center font-bold whitespace-nowrap bg-emerald-700 min-w-[150px]">First Name</th>
                            <th class="border border-slate-500 px-2 py-2 text-center font-bold whitespace-nowrap bg-emerald-700 min-w-[130px]">Middle Name</th>
                            <th class="border border-slate-500 px-2 py-2 text-center font-bold whitespace-nowrap bg-indigo-600 min-w-[60px]">4Ps</th>
                            <th class="border border-slate-500 px-2 py-2 text-center font-bold whitespace-nowrap bg-indigo-600 min-w-[60px]">IP</th>
                            <th class="border border-slate-500 px-2 py-2 text-center font-bold whitespace-nowrap bg-indigo-600 min-w-[60px]">PWD</th>
                            <th class="border border-slate-500 px-2 py-2 text-center font-bold whitespace-nowrap bg-indigo-600 min-w-[110px]">4Ps HH ID</th>
                            <th class="border border-slate-500 px-2 py-2 text-center font-bold whitespace-nowrap bg-cyan-700 min-w-[130px]">PhilHealth ID</th>
                            <th class="border border-slate-500 px-2 py-2 text-center font-bold whitespace-nowrap bg-cyan-700 min-w-[110px]">Membership</th>
                            <th class="border border-slate-500 px-2 py-2 text-center font-bold whitespace-nowrap bg-cyan-700 min-w-[150px]">Category</th>
                            <th class="border border-slate-500 px-2 py-2 text-center font-bold whitespace-nowrap bg-rose-600 min-w-[150px]">Medical History</th>
                            <th class="border border-slate-500 px-2 py-2 text-center font-bold whitespace-nowrap bg-rose-600 min-w-[130px]">Medical Spec.</th>
                            <th class="border border-slate-500 px-2 py-2 text-center font-bold whitespace-nowrap bg-rose-600 min-w-[110px]">Classification</th>
                            <th class="border border-slate-500 px-2 py-2 text-center font-bold whitespace-nowrap bg-rose-600 min-w-[110px]">Immunization</th>
                            <th class="border border-slate-500 px-2 py-2 text-center font-bold whitespace-nowrap bg-amber-600 min-w-[120px]">LMP</th>
                            <th class="border border-slate-500 px-2 py-2 text-center font-bold whitespace-nowrap bg-amber-600 min-w-[80px]">Using FP</th>
                            <th class="border border-slate-500 px-2 py-2 text-center font-bold whitespace-nowrap bg-amber-600 min-w-[110px]">FP Method</th>
                            <th class="border border-slate-500 px-2 py-2 text-center font-bold whitespace-nowrap bg-amber-600 min-w-[90px]">FP Status</th>
                            <th class="border border-slate-500 px-2 py-2 text-center font-bold whitespace-nowrap bg-sky-600 min-w-[110px]">Water Source</th>
                            <th class="border border-slate-500 px-2 py-2 text-center font-bold whitespace-nowrap bg-sky-600 min-w-[180px]">Toilet Facility</th>
                        </tr>
                    </thead>
                <tbody class="bg-white">
                    @foreach($family->members as $member)
                    <tr class="hover:bg-emerald-50/40 transition-colors">
                        <td class="border border-slate-300 px-2 py-2 text-center font-mono text-slate-700">{{ $member->date_of_visit ?? '—' }}</td>
                        <td class="border border-slate-300 px-3 py-2 font-bold text-slate-800 uppercase">{{ $member->last_name }}</td>
                        <td class="border border-slate-300 px-3 py-2 font-bold text-slate-800 uppercase">{{ $member->first_name }}</td>
                        <td class="border border-slate-300 px-3 py-2 text-slate-700 uppercase">{{ $member->middle_name ?: '—' }}</td>
                        <td class="border border-slate-300 px-2 py-2 text-center text-slate-600"><span class="inline-block px-2 py-0.5 rounded bg-slate-100 font-bold text-slate-700">{{ $member->relation_short }}</span></td>
                        <td class="border border-slate-300 px-2 py-2 text-center font-mono text-slate-700">{{ $member->date_of_birth ? $member->date_of_birth->format('m/d/Y') : '—' }}</td>
                        <td class="border border-slate-300 px-2 py-2 text-center font-bold text-slate-800">{{ $member->age ?? '—' }}</td>
                        <td class="border border-slate-300 px-2 py-2 text-center font-bold text-slate-800">{{ $member->sex ?? '—' }}</td>
                        <td class="border border-slate-300 px-2 py-2 text-center text-slate-600"><span class="inline-block px-2 py-0.5 rounded bg-slate-100 font-bold text-slate-700">{{ $member->civil_status_short }}</span></td>
                        <td class="border border-slate-300 px-2 py-2 text-center text-slate-600">{{ $member->education ?? '—' }}</td>
                        <td class="border border-slate-300 px-2 py-2 text-center text-slate-700 uppercase">{{ $member->religion ?? '—' }}</td>
                        <td class="border border-slate-300 px-2 py-2 text-center text-slate-700 uppercase">{{ $member->ethnicity ?? '—' }}</td>
                        <td class="border border-slate-300 px-2 py-2 text-center ">@if($member->is_4ps)
                        <span class="inline-block px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 font-black border border-indigo-200 text-[9px]">YES</span>
                        @else
                        <span class="text-slate-400">NO</span>
                        @endif</td>
                        <td class="border border-slate-300 px-2 py-2 text-center ">@if($member->is_ip || strtolower(trim($member->ethnicity ?? '')) === 'ip')
                        <span class="inline-block px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 font-black border border-indigo-200 text-[9px]">YES</span>
                        @else
                        <span class="text-slate-400">NO</span>
                        @endif</td>
                        <td class="border border-slate-300 px-2 py-2 text-center ">@if($member->is_pwd)
                        <span class="inline-block px-2 py-0.5 rounded bg-rose-50 text-rose-700 font-black border border-rose-200 text-[9px]">YES</span>
                        @else
                        <span class="text-slate-400">NO</span>
                        @endif</td>
                        <td class="border border-slate-300 px-2 py-2 text-center text-slate-600">{{ $member->fourps_household_id ?? '—' }}</td>
                        <td class="border border-slate-300 px-2 py-2 text-center text-slate-600">{{ $member->philhealth_id ?? '—' }}</td>
                        <td class="border border-slate-300 px-2 py-2 text-center text-slate-600">{{ $member->philhealth_member ?? '—' }}</td>
                        <td class="border border-slate-300 px-2 py-2 text-center text-slate-600">{{ $member->philhealth_category ?? '—' }}</td>
                        <td class="border border-slate-300 px-2 py-2 text-center text-slate-600">{{ $member->medical_history ?? '—' }}</td>
                        <td class="border border-slate-300 px-2 py-2 text-center text-slate-600">{{ $member->medical_specify ?? '—' }}</td>
                        <td class="border border-slate-300 px-2 py-2 text-center text-slate-600">{{ $member->classification ?? '—' }}</td>
                        <td class="border border-slate-300 px-2 py-2 text-center text-slate-600">{{ $member->immunization ?? '—' }}</td>
                        <td class="border border-slate-300 px-2 py-2 text-center text-slate-600">{{ $member->lmp_date ? $member->lmp_date->format('m/d/Y') : '—' }}</td>
                        <td class="border border-slate-300 px-2 py-2 text-center text-slate-600">{{ is_null($member->using_fp) ? '—' : ($member->using_fp ? 'Yes' : 'No') }}</td>
                        <td class="border border-slate-300 px-2 py-2 text-center text-slate-600">{{ $member->fp_method ?? '—' }}</td>
                        <td class="border border-slate-300 px-2 py-2 text-center text-slate-600">{{ $member->fp_status ?? '—' }}</td>
                        <td class="border border-slate-300 px-2 py-2 text-center text-slate-600">{{ $member->water_source ?? '—' }}</td>
                        <td class="border border-slate-300 px-2 py-2 text-center text-slate-600">{{ $member->toilet_facility ?? $member->toilet_type ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-10 text-center text-slate-400">
            <div class="flex flex-col items-center gap-3">
                <div class="h-16 w-16 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center">
                    <i class="fas fa-user-slash text-slate-300 text-2xl"></i>
                </div>
                <p class="text-sm font-bold text-slate-700">No members assigned yet</p>
                <p class="text-xs text-slate-400">Add members by editing this household or linking an existing resident.</p>
                <button type="button" onclick="openLinkModal()" class="mt-2 inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition shadow-sm">
                    <i class="fas fa-link"></i> Link Resident
                </button>
            </div>
        </div>
        @endif
    </div>

    {{-- LEGEND --}}
    <div class="mt-6 bg-slate-50 border border-slate-200 rounded-2xl p-5">
        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3">
            <i class="fas fa-book text-slate-400 mr-1"></i> Legend / Code Reference
        </p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-[10px]">
            <div>
                <p class="font-black text-slate-700 mb-1">Civil Status</p>
                <p class="text-slate-500">S = Single, M = Married, W = Widowed, SP = Separated, A = Annulled</p>
            </div>
            <div>
                <p class="font-black text-slate-700 mb-1">Sex</p>
                <p class="text-slate-500">M = Male, F = Female</p>
            </div>
            <div>
                <p class="font-black text-slate-700 mb-1">Relation</p>
                <p class="text-slate-500">Head, Spouse, Son, Daughter, Parent, Sibling, Grandchild, Others</p>
            </div>
            <div>
                <p class="font-black text-slate-700 mb-1">Classifications</p>
                <p class="text-slate-500">4Ps, IP (Indigenous People), PWD (Person with Disability)</p>
            </div>
                <div>
                    <p class="font-black text-slate-700 mb-1">Classification (Age/Health Risk)</p>
                    <p class="text-slate-500">N = Newborn, I = Infant, PSAC, SAC, AD = Adolescent, A = Adult, SC = Senior Citizen</p>
                </div>
                <div>
                    <p class="font-black text-slate-700 mb-1">FP Status</p>
                    <p class="text-slate-500">NA = New Acceptor, CU = Current User, CM = Changing Method, CC = Changing Clinic, DO = Drop-out, R = Restarter</p>
                </div>
                <div>
                    <p class="font-black text-slate-700 mb-1">PhilHealth Membership</p>
                    <p class="text-slate-500">M = Member, D = Dependent</p>
                </div>
        </div>
    </div>

    {{-- BOTTOM BUTTONS --}}
    <div class="mt-8 flex flex-col sm:flex-row justify-center gap-3">
        <a href="{{ route('bhw.family_profiling.index') }}" 
           class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-white border border-slate-300 text-slate-700 font-bold text-sm hover:bg-slate-50 transition">
            <i class="fas fa-arrow-left"></i> Back to Household List
        </a>
        <a href="{{ route('bhw.family_profiling.edit', $family->id) }}" 
           class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md transition">
            <i class="fas fa-pen"></i> Edit This Household
        </a>
    </div>
</div>

{{-- ============================================== --}}
{{-- MODAL PARA SA PAG-LINK NG RESIDENT --}}
{{-- ============================================== --}}
<div id="linkResidentModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeLinkModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start mb-4">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-link text-indigo-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-bold text-slate-900" id="modal-title">Link Existing Resident</h3>
                        <p class="text-xs text-slate-500 mt-1">Search for a registered resident (e.g., those with "No household") to add them to this family.</p>
                    </div>
                </div>

                <!-- Search Input -->
                <div class="relative mt-2">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-slate-400"></i>
                    </div>
                    <input type="text" id="residentSearchInput" class="block w-full pl-10 pr-3 py-2 border border-slate-300 rounded-xl leading-5 bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition" placeholder="Type resident name..." onkeyup="searchResidents()">
                </div>

                <!-- Search Results -->
                <div id="searchResults" class="mt-4 max-h-60 overflow-y-auto space-y-2 border border-slate-100 rounded-xl p-2 bg-slate-50 hidden">
                    <!-- Results will be injected here via JS -->
                </div>
            </div>
            
            <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-2xl border-t border-slate-200">
                <button type="button" onclick="closeLinkModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-bold text-slate-700 hover:bg-slate-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

{{-- PRINT STYLES --}}
<style media="print">
    @page { size: landscape; margin: 0.5cm; }
    body { background: white !important; }
    nav, footer, button, .no-print { display: none !important; }
    a[href]::after { content: ""; }
    .overflow-x-auto { max-height: none !important; overflow: visible !important; }
    table { font-size: 8px !important; }
    thead { position: static !important; }
</style>

{{-- JAVASCRIPT PARA SA MODAL --}}
<script>
    const modal = document.getElementById('linkResidentModal');
    const searchInput = document.getElementById('residentSearchInput');
    const resultsContainer = document.getElementById('searchResults');
    let searchTimeout;

    function openLinkModal() {
        modal.classList.remove('hidden');
        searchInput.value = '';
        searchInput.focus();
        resultsContainer.innerHTML = '';
        resultsContainer.classList.add('hidden');
    }

    function closeLinkModal() {
        modal.classList.add('hidden');
    }

    function searchResidents() {
        clearTimeout(searchTimeout);
        const query = searchInput.value.trim();

        if (query.length < 2) {
            resultsContainer.innerHTML = '';
            resultsContainer.classList.add('hidden');
            return;
        }

        searchTimeout = setTimeout(() => {
            // NAKA-ALIGN NA SA web.php mo: searchResidents (camelCase)
            fetch(`{{ route('bhw.family_profiling.searchResidents', $family->id) }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    resultsContainer.innerHTML = '';
                    if (data.length > 0) {
                        resultsContainer.classList.remove('hidden');
                        data.forEach(resident => {
                            const item = document.createElement('div');
                            item.className = 'flex items-center justify-between p-3 bg-white border border-slate-200 rounded-lg hover:border-indigo-300 hover:shadow-sm transition cursor-pointer';
                            item.innerHTML = `
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-xs">
                                        ${resident.first_name.charAt(0)}${resident.last_name.charAt(0)}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800">${resident.full_name}</p>
                                        <p class="text-[10px] text-slate-500">${resident.zone_purok || 'No Zone'}</p>
                                    </div>
                                </div>
                                <button onclick="attachResident(${resident.id})" class="bg-indigo-50 text-indigo-700 hover:bg-indigo-600 hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                    Link
                                </button>
                            `;
                            resultsContainer.appendChild(item);
                        });
                    } else {
                        resultsContainer.classList.remove('hidden');
                        resultsContainer.innerHTML = '<p class="text-center text-sm text-slate-500 py-3">No residents found.</p>';
                    }
                })
                .catch(error => console.error('Error fetching residents:', error));
        }, 300); // 300ms debounce
    }

    function attachResident(residentId) {
        // NAKA-ALIGN NA SA web.php mo: attachResident (camelCase)
        fetch(`{{ route('bhw.family_profiling.attachResident', $family->id) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}' // Important para hindi ma-403 Forbidden
            },
            body: JSON.stringify({ resident_id: residentId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // I-reload ang page para makita agad yung pagbabago sa listahan
                window.location.reload();
            } else {
                alert(data.error || 'Something went wrong.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to link resident.');
        });
    }
</script>
@endsection