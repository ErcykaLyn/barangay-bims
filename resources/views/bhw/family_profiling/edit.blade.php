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
                <h1 class="text-2xl font-black text-slate-800 tracking-tight">Edit Household Profile</h1>
                <p class="text-sm text-slate-500 mt-0.5">
                    {{ $family->family_code }} • {{ $family->head_formal_name }}
                </p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('bhw.family_profiling.show', $family->id) }}" 
               class="bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-xl shadow-sm transition flex items-center gap-2 font-bold text-sm">
                <i class="fas fa-eye"></i> View
            </a>
            <a href="{{ route('bhw.family_profiling.index') }}" 
               class="bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-xl shadow-sm transition flex items-center gap-2 font-bold text-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-rose-50 border-l-4 border-rose-500 rounded-xl p-4 mb-6">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-circle text-rose-600 mt-0.5"></i>
                <div class="flex-1">
                    <p class="text-xs font-black text-rose-900 uppercase tracking-wider">May mga error sa form</p>
                    <ul class="text-xs text-rose-700 mt-1 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('bhw.family_profiling.update', $family->id) }}" method="POST" id="familyForm">
        @csrf
        @method('PUT')

        {{-- Suggestion lists (galing sa Data sheet ng Form2) --}}
        <datalist id="dl-classification">
            <option value="N">N - Newborn (0-28 days)</option><option value="I">I - Infant (0-1 y/o)</option>
            <option value="PSAC">PSAC (1-4 y/o)</option><option value="SAC">SAC - School Age (5-9 y/o)</option>
            <option value="AD">AD - Adolescent (10-19 y/o)</option><option value="A">A - Adult (20-59 y/o)</option>
            <option value="SC">SC - Senior Citizen</option>
        </datalist>
        <datalist id="dl-fpmethod">
            <option value="COC"><option value="POP"><option value="Injectibles"><option value="IUD"><option value="Condom">
            <option value="LAM"><option value="BTL"><option value="Implant"><option value="SDM"><option value="DPT">
            <option value="Withdrawal"><option value="Others">
        </datalist>
        <datalist id="dl-fpstatus">
            <option value="NA">NA - New Acceptor</option><option value="CU">CU - Current User</option>
            <option value="CM">CM - Changing Method</option><option value="CC">CC - Changing Clinic</option>
            <option value="DO">DO - Drop-out</option><option value="R">R - Restarter</option>
        </datalist>
        <datalist id="dl-education">
            <option value="N/A"><option value="Pre-school"><option value="Elementary Student"><option value="Elementary Under Graduate">
            <option value="Elementary Graduate"><option value="High School Student"><option value="High School Undergraduate">
            <option value="High School Graduate"><option value="Senior Highschool"><option value="ALS"><option value="Vocational Course">
            <option value="College Student"><option value="College Under Graduate"><option value="College Graduate">
            <option value="Post Graduate/Masteral/Doctorate">
        </datalist>
        <datalist id="dl-religion">
            <option value="Roman Catholic"><option value="Christian"><option value="Iglesia ni Cristo"><option value="Catholic">
            <option value="Islam"><option value="Baptist"><option value="Born Again Christian"><option value="Buddhism">
            <option value="Church of God"><option value="Jehovahs Witness"><option value="Protestant"><option value="Seventh Day Adventist">
            <option value="LDS-Mormons"><option value="Evangelical"><option value="Pentecostal"><option value="Unknown"><option value="Others">
        </datalist>
        <datalist id="dl-ethnicity"><option value="IP"><option value="Non-IP"></datalist>
        <datalist id="dl-water"><option value="Level 1"><option value="Level 2"><option value="Level 3"><option value="Unknown"></datalist>
        <datalist id="dl-toilet">
            <option value="Pour/flush toilet connected to septic tank">
            <option value="Pour/flush toilet connected to connected to septic tank AND to sewerage system">
            <option value="Ventilated improved pit latrine (VIP) or Composting toilet">
            <option value="Water-sealed connected to open drain"><option value="Overhung latrine">
            <option value="Open-pit Latrine"><option value="Without toilet">
        </datalist>

        {{-- FORM HEADER --}}
        <div class="bg-white rounded-t-2xl border-2 border-b-0 border-slate-300 overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-700 to-emerald-800 px-6 py-4">
                <div class="text-center">
                    <h2 class="text-base font-black text-white uppercase tracking-widest">Household Profiling Form</h2>
                    <p class="text-[10px] text-emerald-100 font-bold mt-0.5">Republic of the Philippines • Philippine Statistics Authority</p>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-slate-50 text-[11px]">
                <div>
                    <span class="font-black text-slate-500 uppercase tracking-wider">Region:</span>
                    <span class="text-slate-800 font-bold">Region I</span>
                </div>
                <div>
                    <span class="font-black text-slate-500 uppercase tracking-wider">City/Municipality:</span>
                    <span class="text-slate-800 font-bold">Urdaneta City</span>
                </div>
                <div>
                    <span class="font-black text-slate-500 uppercase tracking-wider">Barangay:</span>
                    <span class="text-slate-800 font-bold">Bactad East</span>
                </div>
            </div>
        </div>

        {{-- HOUSEHOLD INFO --}}
        <div class="bg-white border-x-2 border-slate-300 px-6 py-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">
                    Family Code <span class="text-red-500">*</span>
                </label>
                <input type="text" name="family_code" required 
                       value="{{ old('family_code', $family->family_code) }}"
                       class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono font-bold focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">
                    Zone / Purok
                </label>
                <select name="zone_purok"
                        class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                    <option value="">Select Zone</option>
                    @for($i = 1; $i <= 7; $i++)
                        <option value="Zone {{ $i }}" {{ old('zone_purok', $family->zone_purok) == 'Zone ' . $i ? 'selected' : '' }}>Zone {{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">
                    Contact Number
                </label>
                <input type="text" name="contact_number" value="{{ old('contact_number', $family->contact_number) }}"
                       class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition"
                       placeholder="09XX XXX XXXX">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">
                    Parent Family
                </label>
                <select name="parent_family_id"
                        class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                    <option value="">— Main Household —</option>
                    @foreach($allFamilies as $fam)
                        <option value="{{ $fam->id }}" {{ old('parent_family_id', $family->parent_family_id) == $fam->id ? 'selected' : '' }}>
                            {{ $fam->family_code }} — {{ $fam->head_formal_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">
                    Complete Address
                </label>
                <input type="text" name="complete_address" value="{{ old('complete_address', $family->complete_address) }}"
                       class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition"
                       placeholder="House No., Street">
            </div>
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">
                    Notes
                </label>
                <input type="text" name="notes" value="{{ old('notes', $family->notes) }}"
                       class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition"
                       placeholder="Additional notes...">
            </div>
        </div>

        {{-- MEMBERS TABLE (CBMS Spreadsheet Style) --}}
        <div class="bg-white border-2 border-slate-300 rounded-b-2xl overflow-hidden">
            <div class="px-6 py-3 border-b-2 border-slate-300 bg-slate-100 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <i class="fas fa-users text-emerald-600 text-sm"></i>
                    <h3 class="font-black text-slate-800 text-xs uppercase tracking-wider">Members of Household</h3>
                    <span class="text-[10px] font-bold text-slate-500">({{ $family->members->count() }} total)</span>
                </div>
                <button type="button" onclick="addMember()" 
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-md text-[11px] font-black shadow-sm transition flex items-center gap-1">
                    <i class="fas fa-plus text-[10px]"></i> Add Row
                </button>
            </div>

            {{-- Horizontal Scrollable Table --}}
            <div class="overflow-x-auto" style="max-height: 60vh;">
                <table class="min-w-[4000px] border-collapse text-[10px]">
                    <thead class="sticky top-0 z-10">
                        <tr class="bg-slate-700 text-white">
                            <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center font-black uppercase tracking-wider  min-w-[90px]">Date of Visit</th>
                            <th colspan="2" class="border border-slate-600 px-2 py-2 text-center font-black uppercase tracking-wider ">Name of Household Member</th>
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
                            <th rowspan="2" class="border border-slate-600 px-2 py-2 text-center font-black uppercase tracking-wider bg-slate-800 w-10">Action</th>
                        </tr>
                        <tr class="bg-slate-600 text-white">
                            <th class="border border-slate-500 px-2 py-2 text-center font-bold whitespace-nowrap bg-emerald-700 min-w-[120px]">Last Name</th>
                            <th class="border border-slate-500 px-2 py-2 text-center font-bold whitespace-nowrap bg-emerald-700 min-w-[220px]">First Name / Middle Name</th>
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
                    <tbody id="members-tbody" class="bg-white">
                        {{-- Pre-filled rows from existing members --}}
                        @foreach($family->members as $index => $member)
                        @php
                            $relVal    = $member->relation_to_head;
                            $civMap    = ['S'=>'Single','M'=>'Married','W'=>'Widowed','SP'=>'Separated','L'=>'Cohabitation','C'=>'Cohabitation','A'=>'Annulled','O'=>'Others'];
                            $civVal    = $civMap[strtoupper($member->civil_status ?? '')] ?? $member->civil_status;
                            $phcVal    = $member->philhealth_category;
                            $toiletVal = $member->toilet_facility ?? $member->toilet_type;
                            $fpVal     = is_null($member->using_fp) ? '' : ($member->using_fp ? '1' : '0');
                        @endphp
                        <tr class="member-row hover:bg-emerald-50/40 transition-colors">
                            <td class="border border-slate-300 p-1"><input type="text" name="members[{{ $index }}][date_of_visit]"
                                           value="{{ $member->date_of_visit }}"
                                           class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none text-center font-mono" placeholder="2024"></td>
                            <td class="border border-slate-300 p-1"><input type="text" name="members[{{ $index }}][last_name]" required
                                           value="{{ $member->last_name }}"
                                           class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none font-bold uppercase" placeholder="Dela Cruz"></td>
                            <td class="border border-slate-300 p-1"><div class="flex gap-1">
                            <input type="text" name="members[{{ $index }}][first_name]" value="{{ $member->first_name }}" required
                                   class="w-1/2 bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none font-bold uppercase" placeholder="Juan">
                            <input type="text" name="members[{{ $index }}][middle_name]" value="{{ $member->middle_name }}"
                                   class="w-1/2 bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none uppercase" placeholder="Santos">
                            </div></td>
                            <td class="border border-slate-300 p-1"><select name="members[{{ $index }}][relation_to_head]"
                                                class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none font-bold">
                                <option value="">—</option>
                                <option value="Head" {{ (string) $relVal === 'Head' ? 'selected' : '' }}>Head</option>
                                <option value="Spouse" {{ (string) $relVal === 'Spouse' ? 'selected' : '' }}>Spouse</option>
                                <option value="Son" {{ (string) $relVal === 'Son' ? 'selected' : '' }}>Son</option>
                                <option value="Daughter" {{ (string) $relVal === 'Daughter' ? 'selected' : '' }}>Daughter</option>
                                <option value="Parent" {{ (string) $relVal === 'Parent' ? 'selected' : '' }}>Parent</option>
                                <option value="Sibling" {{ (string) $relVal === 'Sibling' ? 'selected' : '' }}>Sibling</option>
                                <option value="Grandchild" {{ (string) $relVal === 'Grandchild' ? 'selected' : '' }}>Grandchild</option>
                                <option value="Others" {{ (string) $relVal === 'Others' ? 'selected' : '' }}>Others</option>
                                @if($relVal !== null && $relVal !== '' && !in_array((string) $relVal, ['Head', 'Spouse', 'Son', 'Daughter', 'Parent', 'Sibling', 'Grandchild', 'Others'], true))
                                    <option value="{{ $relVal }}" selected>{{ $relVal }}</option>
                                @endif
                            </select></td>
                            <td class="border border-slate-300 p-1"><input type="date" name="members[{{ $index }}][date_of_birth]"
                                           value="{{ $member->date_of_birth ? $member->date_of_birth->format('Y-m-d') : '' }}"
                                           onchange="computeAge(this)"
                                           class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none"></td>
                            <td class="border border-slate-300 p-1"><input type="number" min="0" max="150" name="members[{{ $index }}][age]"
                                           value="{{ $member->age }}"
                                           class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none text-center font-bold" placeholder="—"></td>
                            <td class="border border-slate-300 p-1"><select name="members[{{ $index }}][sex]"
                                                class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none text-center font-bold">
                                <option value="">—</option>
                                <option value="M" {{ (string) $member->sex === 'M' ? 'selected' : '' }}>M</option>
                                <option value="F" {{ (string) $member->sex === 'F' ? 'selected' : '' }}>F</option>
                                @if($member->sex !== null && $member->sex !== '' && !in_array((string) $member->sex, ['M', 'F'], true))
                                    <option value="{{ $member->sex }}" selected>{{ $member->sex }}</option>
                                @endif
                            </select></td>
                            <td class="border border-slate-300 p-1"><select name="members[{{ $index }}][civil_status]"
                                                class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none text-center font-bold">
                                <option value="">—</option>
                                <option value="Single" {{ (string) $civVal === 'Single' ? 'selected' : '' }}>S</option>
                                <option value="Married" {{ (string) $civVal === 'Married' ? 'selected' : '' }}>M</option>
                                <option value="Widowed" {{ (string) $civVal === 'Widowed' ? 'selected' : '' }}>W</option>
                                <option value="Separated" {{ (string) $civVal === 'Separated' ? 'selected' : '' }}>SP</option>
                                <option value="Cohabitation" {{ (string) $civVal === 'Cohabitation' ? 'selected' : '' }}>C</option>
                                <option value="Annulled" {{ (string) $civVal === 'Annulled' ? 'selected' : '' }}>A</option>
                                <option value="Others" {{ (string) $civVal === 'Others' ? 'selected' : '' }}>O</option>
                                @if($civVal !== null && $civVal !== '' && !in_array((string) $civVal, ['Single', 'Married', 'Widowed', 'Separated', 'Cohabitation', 'Annulled', 'Others'], true))
                                    <option value="{{ $civVal }}" selected>{{ $civVal }}</option>
                                @endif
                            </select></td>
                            <td class="border border-slate-300 p-1"><input type="text" name="members[{{ $index }}][education]"
                                           value="{{ $member->education }}" list="dl-education"
                                           class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none uppercase" placeholder="High School Graduate"></td>
                            <td class="border border-slate-300 p-1"><input type="text" name="members[{{ $index }}][religion]"
                                           value="{{ $member->religion }}" list="dl-religion"
                                           class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none uppercase" placeholder="Roman Catholic"></td>
                            <td class="border border-slate-300 p-1"><input type="text" name="members[{{ $index }}][ethnicity]"
                                           value="{{ $member->ethnicity }}" list="dl-ethnicity"
                                           class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none uppercase" placeholder="IP / Non-IP"></td>
                            <td class="border border-slate-300 p-1 text-center"><input type="hidden" name="members[{{ $index }}][is_4ps]" value="0">
                                        <input type="checkbox" name="members[{{ $index }}][is_4ps]" value="1"
                                               {{ $member->is_4ps ? 'checked' : '' }}
                                               class="w-4 h-4 rounded text-indigo-600 cursor-pointer"></td>
                            <td class="border border-slate-300 p-1 text-center"><input type="hidden" name="members[{{ $index }}][is_ip]" value="0">
                                        <input type="checkbox" name="members[{{ $index }}][is_ip]" value="1"
                                               {{ $member->is_ip ? 'checked' : '' }}
                                               class="w-4 h-4 rounded text-indigo-600 cursor-pointer"></td>
                            <td class="border border-slate-300 p-1 text-center"><input type="hidden" name="members[{{ $index }}][is_pwd]" value="0">
                                        <input type="checkbox" name="members[{{ $index }}][is_pwd]" value="1"
                                               {{ $member->is_pwd ? 'checked' : '' }}
                                               class="w-4 h-4 rounded text-rose-600 cursor-pointer"></td>
                            <td class="border border-slate-300 p-1"><input type="text" name="members[{{ $index }}][fourps_household_id]"
                                           value="{{ $member->fourps_household_id }}"
                                           class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none " placeholder="4Ps HH ID"></td>
                            <td class="border border-slate-300 p-1"><input type="text" name="members[{{ $index }}][philhealth_id]"
                                           value="{{ $member->philhealth_id }}"
                                           class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none " placeholder="PhilHealth ID"></td>
                            <td class="border border-slate-300 p-1"><select name="members[{{ $index }}][philhealth_member]"
                                                class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none text-center">
                                <option value="">—</option>
                                <option value="M" {{ (string) $member->philhealth_member === 'M' ? 'selected' : '' }}>M - Member</option>
                                <option value="D" {{ (string) $member->philhealth_member === 'D' ? 'selected' : '' }}>D - Dependent</option>
                                @if($member->philhealth_member !== null && $member->philhealth_member !== '' && !in_array((string) $member->philhealth_member, ['M', 'D'], true))
                                    <option value="{{ $member->philhealth_member }}" selected>{{ $member->philhealth_member }}</option>
                                @endif
                            </select></td>
                            <td class="border border-slate-300 p-1"><select name="members[{{ $index }}][philhealth_category]"
                                                class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none ">
                                <option value="">—</option>
                                <option value="Direct Contributors" {{ (string) $phcVal === 'Direct Contributors' ? 'selected' : '' }}>Direct Contributors</option>
                                <option value="Indirect Contributors" {{ (string) $phcVal === 'Indirect Contributors' ? 'selected' : '' }}>Indirect Contributors</option>
                                <option value="Unknown" {{ (string) $phcVal === 'Unknown' ? 'selected' : '' }}>Unknown</option>
                                @if($phcVal !== null && $phcVal !== '' && !in_array((string) $phcVal, ['Direct Contributors', 'Indirect Contributors', 'Unknown'], true))
                                    <option value="{{ $phcVal }}" selected>{{ $phcVal }}</option>
                                @endif
                            </select></td>
                            <td class="border border-slate-300 p-1"><input type="text" name="members[{{ $index }}][medical_history]"
                                           value="{{ $member->medical_history }}"
                                           class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none " placeholder="e.g., Hypertension, Diabetes"></td>
                            <td class="border border-slate-300 p-1"><input type="text" name="members[{{ $index }}][medical_specify]"
                                           value="{{ $member->medical_specify }}"
                                           class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none " placeholder="Others, specify"></td>
                            <td class="border border-slate-300 p-1"><input type="text" name="members[{{ $index }}][classification]"
                                           value="{{ $member->classification }}" list="dl-classification"
                                           class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none uppercase text-center" placeholder="A / AD / SC"></td>
                            <td class="border border-slate-300 p-1"><input type="text" name="members[{{ $index }}][immunization]"
                                           value="{{ $member->immunization }}"
                                           class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none " placeholder="e.g., Complete"></td>
                            <td class="border border-slate-300 p-1"><input type="date" name="members[{{ $index }}][lmp_date]"
                                           value="{{ $member->lmp_date ? $member->lmp_date->format('Y-m-d') : '' }}"
                                           class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none "></td>
                            <td class="border border-slate-300 p-1"><select name="members[{{ $index }}][using_fp]"
                                                class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none text-center">
                                <option value="">—</option>
                                <option value="1" {{ (string) $fpVal === '1' ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ (string) $fpVal === '0' ? 'selected' : '' }}>No</option>
                                @if($fpVal !== null && $fpVal !== '' && !in_array((string) $fpVal, ['1', '0'], true))
                                    <option value="{{ $fpVal }}" selected>{{ $fpVal }}</option>
                                @endif
                            </select></td>
                            <td class="border border-slate-300 p-1"><input type="text" name="members[{{ $index }}][fp_method]"
                                           value="{{ $member->fp_method }}" list="dl-fpmethod"
                                           class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none uppercase" placeholder="COC / IUD / ..."></td>
                            <td class="border border-slate-300 p-1"><input type="text" name="members[{{ $index }}][fp_status]"
                                           value="{{ $member->fp_status }}" list="dl-fpstatus"
                                           class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none uppercase text-center" placeholder="NA / CU / ..."></td>
                            <td class="border border-slate-300 p-1"><input type="text" name="members[{{ $index }}][water_source]"
                                           value="{{ $member->water_source }}" list="dl-water"
                                           class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none " placeholder="Level 1 / 2 / 3"></td>
                            <td class="border border-slate-300 p-1"><input type="text" name="members[{{ $index }}][toilet_facility]"
                                           value="{{ $toiletVal }}" list="dl-toilet"
                                           class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none " placeholder="Toilet facility"></td>
                            <td class="border border-slate-300 p-1 text-center">
                            <button type="button" onclick="removeMember(this)"
                                    class="h-6 w-6 rounded-md bg-rose-50 text-rose-500 hover:bg-rose-600 hover:text-white flex items-center justify-center transition-all mx-auto">
                                <i class="fas fa-times text-[10px]"></i>
                            </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Empty State --}}
            <div id="empty-members" class="text-center py-10 text-slate-400 border-t border-slate-200 {{ $family->members->count() > 0 ? 'hidden' : '' }}">
                <i class="fas fa-users text-3xl opacity-30 mb-2"></i>
                <p class="text-xs font-bold">No members added. Click "Add Row" to start.</p>
            </div>
        </div>

        {{-- SUBMIT --}}
        <div class="mt-6 flex flex-col sm:flex-row justify-end gap-3">
            <a href="{{ route('bhw.family_profiling.index') }}" 
               class="px-6 py-3 rounded-xl bg-white border border-slate-300 text-slate-600 font-bold text-sm hover:bg-slate-50 transition text-center">
                Cancel
            </a>
            <button type="submit" 
                    class="px-8 py-3 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-sm shadow-md transition flex items-center justify-center gap-2">
                <i class="fas fa-save"></i> Update Household
            </button>
        </div>
    </form>
</div>

{{-- TEMPLATE FOR NEW ROWS --}}
<template id="member-row-template">
    <tr class="member-row hover:bg-emerald-50/40 transition-colors">
        <td class="border border-slate-300 p-1"><input type="text" name="members[INDEX][date_of_visit]"
                       class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none text-center font-mono" placeholder="2024"></td>
        <td class="border border-slate-300 p-1"><input type="text" name="members[INDEX][last_name]" required
                       class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none font-bold uppercase" placeholder="Dela Cruz"></td>
        <td class="border border-slate-300 p-1"><div class="flex gap-1">
        <input type="text" name="members[INDEX][first_name]" required
               class="w-1/2 bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none font-bold uppercase" placeholder="Juan">
        <input type="text" name="members[INDEX][middle_name]"
               class="w-1/2 bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none uppercase" placeholder="Santos">
        </div></td>
        <td class="border border-slate-300 p-1"><select name="members[INDEX][relation_to_head]"
                            class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none font-bold">
            <option value="">—</option>
            <option value="Head">Head</option>
            <option value="Spouse">Spouse</option>
            <option value="Son">Son</option>
            <option value="Daughter">Daughter</option>
            <option value="Parent">Parent</option>
            <option value="Sibling">Sibling</option>
            <option value="Grandchild">Grandchild</option>
            <option value="Others">Others</option>
        </select></td>
        <td class="border border-slate-300 p-1"><input type="date" name="members[INDEX][date_of_birth]"
                       onchange="computeAge(this)"
                       class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none"></td>
        <td class="border border-slate-300 p-1"><input type="number" min="0" max="150" name="members[INDEX][age]"
                       class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none text-center font-bold" placeholder="—"></td>
        <td class="border border-slate-300 p-1"><select name="members[INDEX][sex]"
                            class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none text-center font-bold">
            <option value="">—</option>
            <option value="M">M</option>
            <option value="F">F</option>
        </select></td>
        <td class="border border-slate-300 p-1"><select name="members[INDEX][civil_status]"
                            class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none text-center font-bold">
            <option value="">—</option>
            <option value="Single">S</option>
            <option value="Married">M</option>
            <option value="Widowed">W</option>
            <option value="Separated">SP</option>
            <option value="Cohabitation">C</option>
            <option value="Annulled">A</option>
            <option value="Others">O</option>
        </select></td>
        <td class="border border-slate-300 p-1"><input type="text" name="members[INDEX][education]" list="dl-education"
                       class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none uppercase" placeholder="High School Graduate"></td>
        <td class="border border-slate-300 p-1"><input type="text" name="members[INDEX][religion]" list="dl-religion"
                       class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none uppercase" placeholder="Roman Catholic"></td>
        <td class="border border-slate-300 p-1"><input type="text" name="members[INDEX][ethnicity]" list="dl-ethnicity"
                       class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none uppercase" placeholder="IP / Non-IP"></td>
        <td class="border border-slate-300 p-1 text-center"><input type="hidden" name="members[INDEX][is_4ps]" value="0">
                    <input type="checkbox" name="members[INDEX][is_4ps]" value="1"
                           class="w-4 h-4 rounded text-indigo-600 cursor-pointer"></td>
        <td class="border border-slate-300 p-1 text-center"><input type="hidden" name="members[INDEX][is_ip]" value="0">
                    <input type="checkbox" name="members[INDEX][is_ip]" value="1"
                           class="w-4 h-4 rounded text-indigo-600 cursor-pointer"></td>
        <td class="border border-slate-300 p-1 text-center"><input type="hidden" name="members[INDEX][is_pwd]" value="0">
                    <input type="checkbox" name="members[INDEX][is_pwd]" value="1"
                           class="w-4 h-4 rounded text-rose-600 cursor-pointer"></td>
        <td class="border border-slate-300 p-1"><input type="text" name="members[INDEX][fourps_household_id]"
                       class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none " placeholder="4Ps HH ID"></td>
        <td class="border border-slate-300 p-1"><input type="text" name="members[INDEX][philhealth_id]"
                       class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none " placeholder="PhilHealth ID"></td>
        <td class="border border-slate-300 p-1"><select name="members[INDEX][philhealth_member]"
                            class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none text-center">
            <option value="">—</option>
            <option value="M">M - Member</option>
            <option value="D">D - Dependent</option>
        </select></td>
        <td class="border border-slate-300 p-1"><select name="members[INDEX][philhealth_category]"
                            class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none ">
            <option value="">—</option>
            <option value="Direct Contributors">Direct Contributors</option>
            <option value="Indirect Contributors">Indirect Contributors</option>
            <option value="Unknown">Unknown</option>
        </select></td>
        <td class="border border-slate-300 p-1"><input type="text" name="members[INDEX][medical_history]"
                       class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none " placeholder="e.g., Hypertension, Diabetes"></td>
        <td class="border border-slate-300 p-1"><input type="text" name="members[INDEX][medical_specify]"
                       class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none " placeholder="Others, specify"></td>
        <td class="border border-slate-300 p-1"><input type="text" name="members[INDEX][classification]" list="dl-classification"
                       class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none uppercase text-center" placeholder="A / AD / SC"></td>
        <td class="border border-slate-300 p-1"><input type="text" name="members[INDEX][immunization]"
                       class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none " placeholder="e.g., Complete"></td>
        <td class="border border-slate-300 p-1"><input type="date" name="members[INDEX][lmp_date]"
                       class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none "></td>
        <td class="border border-slate-300 p-1"><select name="members[INDEX][using_fp]"
                            class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none text-center">
            <option value="">—</option>
            <option value="1">Yes</option>
            <option value="0">No</option>
        </select></td>
        <td class="border border-slate-300 p-1"><input type="text" name="members[INDEX][fp_method]" list="dl-fpmethod"
                       class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none uppercase" placeholder="COC / IUD / ..."></td>
        <td class="border border-slate-300 p-1"><input type="text" name="members[INDEX][fp_status]" list="dl-fpstatus"
                       class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none uppercase text-center" placeholder="NA / CU / ..."></td>
        <td class="border border-slate-300 p-1"><input type="text" name="members[INDEX][water_source]" list="dl-water"
                       class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none " placeholder="Level 1 / 2 / 3"></td>
        <td class="border border-slate-300 p-1"><input type="text" name="members[INDEX][toilet_facility]" list="dl-toilet"
                       class="w-full bg-transparent border-0 px-2 py-1.5 text-[11px] focus:ring-1 focus:ring-emerald-500 outline-none " placeholder="Toilet facility"></td>
        <td class="border border-slate-300 p-1 text-center">
        <button type="button" onclick="removeMember(this)"
                class="h-6 w-6 rounded-md bg-rose-50 text-rose-500 hover:bg-rose-600 hover:text-white flex items-center justify-center transition-all mx-auto">
            <i class="fas fa-times text-[10px]"></i>
        </button>
        </td>
    </tr>
</template>

<script>
    // Start index from existing count so new rows don't collide
    let memberIndex = {{ $family->members->count() }};

    function addMember() {
        const template = document.getElementById('member-row-template');
        const tbody = document.getElementById('members-tbody');
        const emptyMsg = document.getElementById('empty-members');

        const clone = template.content.cloneNode(true);

        // Replace INDEX with actual index
        clone.querySelectorAll('[name*="INDEX"]').forEach(el => {
            el.name = el.name.replace('INDEX', memberIndex);
        });

        tbody.appendChild(clone);
        memberIndex++;
        emptyMsg.classList.add('hidden');

        // Auto-focus the first input
        const lastRow = tbody.lastElementChild;
        const firstInput = lastRow.querySelector('input[name*="[last_name]"]');
        if (firstInput) firstInput.focus();
    }

    function removeMember(btn) {
        const tbody = document.getElementById('members-tbody');
        const emptyMsg = document.getElementById('empty-members');

        if (!confirm('Remove this member row?')) return;

        btn.closest('.member-row').remove();

        if (tbody.children.length === 0) {
            emptyMsg.classList.remove('hidden');
        }
    }

    function computeAge(dateInput) {
        const row = dateInput.closest('.member-row');
        const ageInput = row.querySelector('input[name*="[age]"]');
        
        if (dateInput.value && ageInput) {
            const dob = new Date(dateInput.value);
            const today = new Date();
            let age = today.getFullYear() - dob.getFullYear();
            const m = today.getMonth() - dob.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
                age--;
            }
            ageInput.value = age;
        }
    }
</script>
@endsection