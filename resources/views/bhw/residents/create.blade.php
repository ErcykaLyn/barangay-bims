@extends('layouts.bhw')

@section('content')
@php
    $mode      = old('household_mode', $selectedFamily ? 'existing' : 'new');
    $zoneValue = old('zone_purok', $selectedFamily->zone_purok ?? '');
@endphp

<div class="space-y-8 max-w-7xl mx-auto pb-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 rounded-full bg-emerald-50 border-2 border-emerald-200 flex items-center justify-center">
                    <i class="fas fa-user-plus text-emerald-600 text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Resident Registration Form</h1>
                    <p class="text-xs md:text-sm text-slate-500 mt-0.5">Barangay Bactad East, Urdaneta City, Pangasinan</p>
                </div>
            </div>
        </div>
        <div class="text-xs text-slate-500 bg-white border border-slate-200 px-4 py-2 rounded-lg shadow-sm">
            <i class="fas fa-file-alt mr-2 text-emerald-500"></i>
            <span class="font-semibold">Form No.</span> BIMS-RF-001
        </div>
    </div>

    <!-- Mga mensahe -->
    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 bg-emerald-50 border-l-4 border-emerald-500 rounded-lg shadow-sm">
            <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
            <p class="text-sm font-semibold text-emerald-800 flex-1">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 px-4 py-3 bg-rose-50 border-l-4 border-rose-500 rounded-lg shadow-sm">
            <i class="fas fa-exclamation-circle text-rose-600 text-lg"></i>
            <p class="text-sm font-semibold text-rose-800 flex-1">{{ session('error') }}</p>
        </div>
    @endif
    @if($errors->any())
        <div class="px-4 py-3 bg-rose-50 border-l-4 border-rose-500 rounded-lg shadow-sm">
            <ul class="list-disc list-inside text-sm text-rose-800 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Naka-pili na household -->
    @if($selectedFamily)
        <div class="bg-gradient-to-r from-emerald-50 to-white border border-emerald-200 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="h-11 w-11 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow">
                    <i class="fas fa-home"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-emerald-700 uppercase tracking-widest">Adding family member to household</p>
                    <p class="text-sm font-bold text-slate-800">
                        <span class="font-mono">{{ $selectedFamily->family_code }}</span>
                        <span class="text-slate-400 mx-1">·</span>
                        {{ $selectedFamily->head_formal_name }}
                        <span class="text-slate-400 mx-1">·</span>
                        {{ $selectedFamily->zone_purok ?? 'No zone' }}
                    </p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('bhw.family_profiling.show', $selectedFamily->id) }}"
                   class="px-4 py-2 rounded-lg bg-white border border-emerald-300 text-emerald-700 text-xs font-bold hover:bg-emerald-50 transition">
                    <i class="fas fa-eye mr-1"></i> View household
                </a>
                <a href="{{ route('bhw.residents.index') }}"
                   class="px-4 py-2 rounded-lg bg-emerald-700 text-white text-xs font-bold hover:bg-emerald-800 transition">
                    <i class="fas fa-check mr-1"></i> Done
                </a>
            </div>
        </div>
    @endif

    <!-- Form Container -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-md overflow-hidden">
        <!-- Form Header Bar -->
        <div class="bg-gradient-to-r from-emerald-700 to-emerald-600 px-8 py-5">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-white font-black text-lg tracking-wide uppercase">Personal Data Sheet</h2>
                    <p class="text-emerald-100 text-xs mt-1">Please fill out all required fields marked with <span class="text-amber-300 font-bold">*</span></p>
                </div>
                <div class="hidden md:block">
                    <i class="fas fa-id-card text-white/30 text-4xl"></i>
                </div>
            </div>
        </div>

        <form action="{{ route('bhw.residents.store') }}" method="POST" class="p-6 md:p-10 space-y-12" id="residentForm">
            @csrf

            <!-- SECTION I: PERSONAL INFORMATION -->
            <div>
                <div class="flex items-center gap-3 border-b-2 border-emerald-100 pb-4 mb-8">
                    <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center text-white font-black text-sm">I</div>
                    <h2 class="text-base font-black text-slate-800 uppercase tracking-wider">Personal Information</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- First Name -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-widest mb-2">First Name <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" placeholder="Juan" required
                                class="w-full bg-white border border-slate-300 rounded-lg pl-10 pr-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                        </div>
                    </div>

                    <!-- Middle Name -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-widest mb-2">Middle Name <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-user-tag absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input type="text" name="middle_name" value="{{ old('middle_name') }}" placeholder="Santos" required
                                class="w-full bg-white border border-slate-300 rounded-lg pl-10 pr-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                        </div>
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-widest mb-2">Last Name <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-user-alt absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" placeholder="Dela Cruz" required
                                class="w-full bg-white border border-slate-300 rounded-lg pl-10 pr-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                        </div>
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-widest mb-2">Date of Birth <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-calendar-alt absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required
                                class="w-full bg-white border border-slate-300 rounded-lg pl-10 pr-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                        </div>
                    </div>

                    <!-- Gender -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-widest mb-2">Gender <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-venus-mars absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <select name="gender" required
                                class="w-full bg-white border border-slate-300 rounded-lg pl-10 pr-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition appearance-none">
                                <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select Gender</option>
                                @foreach(['Male', 'Female', 'Other'] as $g)
                                    <option value="{{ $g }}" {{ old('gender') === $g ? 'selected' : '' }}>{{ $g }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Civil Status -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-widest mb-2">Civil Status <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-ring absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <select name="civil_status" required
                                class="w-full bg-white border border-slate-300 rounded-lg pl-10 pr-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition appearance-none">
                                <option value="" disabled {{ old('civil_status') ? '' : 'selected' }}>Select Status</option>
                                @foreach(['Single', 'Married', 'Widowed', 'Separated', 'Cohabitation', 'Divorced', 'Annulled'] as $cs)
                                    <option value="{{ $cs }}" {{ old('civil_status') === $cs ? 'selected' : '' }}>{{ $cs }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Contact Number -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-widest mb-2">Contact Number</label>
                        <div class="relative">
                            <i class="fas fa-phone-alt absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input type="tel" name="contact_number" value="{{ old('contact_number') }}" placeholder="09XX XXX XXXX"
                                class="w-full bg-white border border-slate-300 rounded-lg pl-10 pr-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                        </div>
                    </div>

                    <!-- Zone / Purok -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-widest mb-2">Zone / Purok <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-map-pin absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <select name="zone_purok" id="zoneSelect" required
                                class="w-full bg-white border border-slate-300 rounded-lg pl-10 pr-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition appearance-none">
                                <option value="" disabled {{ $zoneValue ? '' : 'selected' }}>Select Zone</option>
                                @for ($i = 1; $i <= 7; $i++)
                                    <option value="Zone {{ $i }}" {{ $zoneValue === 'Zone ' . $i ? 'selected' : '' }}>Zone {{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION II: ADDRESS DETAILS (Simplified) -->
            <div>
                <div class="flex items-center gap-3 border-b-2 border-emerald-100 pb-4 mb-8">
                    <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center text-white font-black text-sm">II</div>
                    <h2 class="text-base font-black text-slate-800 uppercase tracking-wider">Complete Address</h2>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-widest mb-2">Full Address <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="fas fa-map-marked-alt absolute left-3 top-4 text-slate-400 text-sm"></i>
                        <textarea name="complete_address" rows="3" required placeholder="House No., Street, Purok/Zone, Barangay Bactad East, Urdaneta City, Pangasinan"
                            class="w-full bg-white border border-slate-300 rounded-lg pl-10 pr-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">{{ old('complete_address', $selectedFamily->complete_address ?? '') }}</textarea>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1"><i class="fas fa-info-circle"></i> Pakisulat ang buong address para madaling mahanap.</p>
                </div>
            </div>

            <!-- SECTION III: CLASSIFICATIONS -->
            <div>
                <div class="flex items-center gap-3 border-b-2 border-emerald-100 pb-4 mb-8">
                    <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center text-white font-black text-sm">III</div>
                    <h2 class="text-base font-black text-slate-800 uppercase tracking-wider">Resident Classifications</h2>
                </div>

                <p class="text-xs text-slate-500 mb-5 italic">Check all that apply.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Senior Citizen -->
                    <label class="flex items-center gap-3 cursor-pointer group bg-slate-50 border border-slate-200 rounded-xl p-4 hover:border-emerald-300 hover:bg-emerald-50/30 transition">
                        <div class="relative flex-shrink-0">
                            <input type="checkbox" name="is_senior" value="1" {{ old('is_senior') ? 'checked' : '' }} class="peer hidden">
                            <div class="w-5 h-5 bg-white border-2 border-slate-300 rounded peer-checked:bg-emerald-600 peer-checked:border-emerald-600 transition-all"></div>
                            <svg class="absolute top-0.5 left-0.5 w-4 h-4 text-white opacity-0 peer-checked:opacity-100 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-700 uppercase tracking-wider group-hover:text-emerald-700 transition-colors">Senior Citizen</p>
                            <p class="text-[10px] text-slate-400">60 years old and above</p>
                        </div>
                    </label>
                    <!-- PWD -->
                    <label class="flex items-center gap-3 cursor-pointer group bg-slate-50 border border-slate-200 rounded-xl p-4 hover:border-emerald-300 hover:bg-emerald-50/30 transition">
                        <div class="relative flex-shrink-0">
                            <input type="checkbox" name="is_pwd" value="1" {{ old('is_pwd') ? 'checked' : '' }} class="peer hidden">
                            <div class="w-5 h-5 bg-white border-2 border-slate-300 rounded peer-checked:bg-emerald-600 peer-checked:border-emerald-600 transition-all"></div>
                            <svg class="absolute top-0.5 left-0.5 w-4 h-4 text-white opacity-0 peer-checked:opacity-100 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-700 uppercase tracking-wider group-hover:text-emerald-700 transition-colors">PWD</p>
                            <p class="text-[10px] text-slate-400">Person with Disability</p>
                        </div>
                    </label>
                    <!-- 4Ps -->
                    <label class="flex items-center gap-3 cursor-pointer group bg-slate-50 border border-slate-200 rounded-xl p-4 hover:border-emerald-300 hover:bg-emerald-50/30 transition">
                        <div class="relative flex-shrink-0">
                            <input type="checkbox" name="is_4ps" value="1" {{ old('is_4ps') ? 'checked' : '' }} class="peer hidden">
                            <div class="w-5 h-5 bg-white border-2 border-slate-300 rounded peer-checked:bg-emerald-600 peer-checked:border-emerald-600 transition-all"></div>
                            <svg class="absolute top-0.5 left-0.5 w-4 h-4 text-white opacity-0 peer-checked:opacity-100 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-700 uppercase tracking-wider group-hover:text-emerald-700 transition-colors">4Ps Beneficiary</p>
                            <p class="text-[10px] text-slate-400">Pantawid Pamilyang Pilipino</p>
                        </div>
                    </label>
                    <!-- Solo Parent -->
                    <label class="flex items-center gap-3 cursor-pointer group bg-slate-50 border border-slate-200 rounded-xl p-4 hover:border-emerald-300 hover:bg-emerald-50/30 transition">
                        <div class="relative flex-shrink-0">
                            <input type="checkbox" name="is_solo_parent" value="1" {{ old('is_solo_parent') ? 'checked' : '' }} class="peer hidden">
                            <div class="w-5 h-5 bg-white border-2 border-slate-300 rounded peer-checked:bg-emerald-600 peer-checked:border-emerald-600 transition-all"></div>
                            <svg class="absolute top-0.5 left-0.5 w-4 h-4 text-white opacity-0 peer-checked:opacity-100 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-700 uppercase tracking-wider group-hover:text-emerald-700 transition-colors">Solo Parent</p>
                            <p class="text-[10px] text-slate-400">Single parent</p>
                        </div>
                    </label>
                    <!-- Voter -->
                    <label class="flex items-center gap-3 cursor-pointer group bg-slate-50 border border-slate-200 rounded-xl p-4 hover:border-emerald-300 hover:bg-emerald-50/30 transition">
                        <div class="relative flex-shrink-0">
                            <input type="checkbox" name="is_voter" value="1" {{ old('is_voter') ? 'checked' : '' }} class="peer hidden">
                            <div class="w-5 h-5 bg-white border-2 border-slate-300 rounded peer-checked:bg-emerald-600 peer-checked:border-emerald-600 transition-all"></div>
                            <svg class="absolute top-0.5 left-0.5 w-4 h-4 text-white opacity-0 peer-checked:opacity-100 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-700 uppercase tracking-wider group-hover:text-emerald-700 transition-colors">Registered Voter</p>
                            <p class="text-[10px] text-slate-400">Active voter</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- SECTION IV: HOUSEHOLD / FAMILY PROFILE -->
            <div>
                <div class="flex items-center gap-3 border-b-2 border-emerald-100 pb-4 mb-8">
                    <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center text-white font-black text-sm">IV</div>
                    <h2 class="text-base font-black text-slate-800 uppercase tracking-wider">Household / Family Profile</h2>
                </div>

                <p class="text-xs text-slate-500 mb-5 italic">Piliin kung saan mapupunta ang residenteng ito sa Family Profiles.</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="householdModes">
                    <label data-mode-card class="cursor-pointer rounded-xl border-2 border-slate-200 bg-slate-50 p-4 transition hover:border-emerald-300">
                        <div class="flex items-start gap-3">
                            <input type="radio" name="household_mode" value="new" class="mt-1 accent-emerald-600" {{ $mode === 'new' ? 'checked' : '' }}>
                            <div>
                                <p class="text-xs font-bold text-slate-700 uppercase tracking-wider">New household</p>
                                <p class="text-[11px] text-slate-500 mt-0.5">Siya ang Head ng bagong household. May Household No. na awtomatikong gagawin.</p>
                            </div>
                        </div>
                    </label>

                    <label data-mode-card class="cursor-pointer rounded-xl border-2 border-slate-200 bg-slate-50 p-4 transition hover:border-emerald-300">
                        <div class="flex items-start gap-3">
                            <input type="radio" name="household_mode" value="existing" class="mt-1 accent-emerald-600" {{ $mode === 'existing' ? 'checked' : '' }}>
                            <div>
                                <p class="text-xs font-bold text-slate-700 uppercase tracking-wider">Add to existing household</p>
                                <p class="text-[11px] text-slate-500 mt-0.5">Asawa, anak, magulang, o iba pang kapamilya ng umiiral na household.</p>
                            </div>
                        </div>
                    </label>

                    <label data-mode-card class="cursor-pointer rounded-xl border-2 border-slate-200 bg-slate-50 p-4 transition hover:border-emerald-300">
                        <div class="flex items-start gap-3">
                            <input type="radio" name="household_mode" value="none" class="mt-1 accent-emerald-600" {{ $mode === 'none' ? 'checked' : '' }}>
                            <div>
                                <p class="text-xs font-bold text-slate-700 uppercase tracking-wider">No household yet</p>
                                <p class="text-[11px] text-slate-500 mt-0.5">Residents List lang. Pwedeng i-link sa household sa susunod.</p>
                            </div>
                        </div>
                    </label>
                </div>

                <!-- BAGONG DAGDAG: Auto-generated Household Code Panel -->
                <div id="newHouseholdPanel" class="hidden mt-6 p-4 bg-emerald-50/60 border border-emerald-200 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-lg bg-white border border-emerald-200 flex items-center justify-center text-emerald-600">
                            <i class="fas fa-home"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-emerald-700 uppercase tracking-widest">New Household Number</p>
                            <p class="text-xs text-slate-500">Pwedeng i-edit kung gusto ng custom/random.</p>
                        </div>
                    </div>
                    <div class="w-full sm:w-64">
                        <input type="text" name="custom_family_code" id="custom_family_code" placeholder="Select zone first..." readonly
                               class="w-full px-3 py-2 bg-white border border-emerald-300 rounded-lg text-sm font-mono font-bold text-emerald-800 focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                    </div>
                </div>

                <!-- Existing household picker -->
                <div id="existingPanel" class="hidden mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="relative">
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-widest mb-2">Search household <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input type="text" id="householdSearch" autocomplete="off" placeholder="Household No. or head's name..."
                                class="w-full bg-white border border-slate-300 rounded-lg pl-10 pr-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                        </div>
                        <div id="householdResults" class="hidden absolute z-20 mt-1 w-full bg-white border border-slate-200 rounded-lg shadow-lg max-h-64 overflow-auto"></div>
                        <input type="hidden" name="family_profile_id" id="family_profile_id" value="{{ old('family_profile_id', $selectedFamily?->id) }}">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-widest mb-2">Relationship to Head <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-people-roof absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <select name="relation_to_head" id="relation_to_head"
                                class="w-full bg-white border border-slate-300 rounded-lg pl-10 pr-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition appearance-none">
                                <option value="" disabled {{ old('relation_to_head') ? '' : 'selected' }}>Select relationship</option>
                                @foreach(['Spouse', 'Son', 'Daughter', 'Parent', 'Sibling', 'Grandchild', 'Others'] as $rel)
                                    <option value="{{ $rel }}" {{ old('relation_to_head') === $rel ? 'selected' : '' }}>{{ $rel }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="selectedHousehold" class="hidden md:col-span-2 p-4 bg-emerald-50/60 border border-emerald-200 rounded-xl flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <i class="fas fa-home text-emerald-600"></i>
                            <div class="min-w-0">
                                <p class="text-[10px] font-black text-emerald-700 uppercase tracking-widest">Selected household</p>
                                <p id="selectedHouseholdText" class="text-sm font-bold text-slate-800 truncate"></p>
                            </div>
                        </div>
                        <button type="button" id="clearHousehold" class="text-xs font-bold text-rose-600 hover:text-rose-700">Change</button>
                    </div>
                </div>
            </div>

            <!-- SUBMIT SECTION -->
            <div class="pt-6 border-t-2 border-slate-100">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <p class="text-xs text-slate-400 italic order-2 sm:order-1">
                        <i class="fas fa-shield-alt text-emerald-500 mr-1"></i>
                        All information will be kept confidential.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto order-1 sm:order-2">
                        <a href="{{ route('bhw.residents.index') }}" class="px-6 py-3 rounded-lg bg-white border border-slate-300 text-slate-600 font-bold text-sm hover:bg-slate-50 transition text-center">
                            Cancel
                        </a>
                        <button type="submit" class="px-8 py-3 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-sm shadow-md transition transform hover:scale-[1.02] flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i> Submit Registration
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Household picker & Auto-generate Script -->
<script>
    (function () {
        const HOUSEHOLDS = @json($households);

        const form        = document.getElementById('residentForm');
        const radios      = document.querySelectorAll('input[name="household_mode"]');
        const cards       = document.querySelectorAll('[data-mode-card]');
        const existingPanel = document.getElementById('existingPanel');
        const newPanel    = document.getElementById('newHouseholdPanel');
        const codeInput   = document.getElementById('custom_family_code');
        const zoneSelect  = document.getElementById('zoneSelect');
        
        const search      = document.getElementById('householdSearch');
        const results     = document.getElementById('householdResults');
        const hiddenId    = document.getElementById('family_profile_id');
        const relation    = document.getElementById('relation_to_head');
        const chip        = document.getElementById('selectedHousehold');
        const chipText    = document.getElementById('selectedHouseholdText');
        const clearBtn    = document.getElementById('clearHousehold');

        const currentMode = () => (document.querySelector('input[name="household_mode"]:checked') || {}).value;

        function refreshModes() {
            const mode = currentMode();
            cards.forEach(card => {
                const on = card.querySelector('input').checked;
                card.classList.toggle('border-emerald-500', on);
                card.classList.toggle('bg-emerald-50', on);
                card.classList.toggle('border-slate-200', !on);
                card.classList.toggle('bg-slate-50', !on);
            });
            
            existingPanel.classList.toggle('hidden', mode !== 'existing');
            newPanel.classList.toggle('hidden', mode !== 'new');
            relation.required = (mode === 'existing');
            
            // Trigger auto-generate when switching to "New"
            if (mode === 'new' && zoneSelect.value) {
                fetchNextCode(zoneSelect.value);
            }
        }

        // Auto-generate logic
        async function fetchNextCode(zone) {
            if (!zone) return;
            codeInput.value = 'Generating...';
            try {
                const response = await fetch(`{{ route('bhw.family_profiling.next-code') }}?zone=${encodeURIComponent(zone)}`);
                const data = await response.json();
                codeInput.value = data.code || '';
            } catch (error) {
                console.error('Error fetching code:', error);
                codeInput.value = '';
            }
        }

        zoneSelect.addEventListener('change', function() {
            if (currentMode() === 'new') {
                fetchNextCode(this.value);
            }
        });

        // Existing Household picker logic
        function labelFor(h) {
            const zone = h.zone ? ' · ' + h.zone : '';
            return h.code + ' · ' + h.head + zone + ' · ' + h.members + ' member' + (h.members === 1 ? '' : 's');
        }

        function choose(h) {
            hiddenId.value = h.id;
            chipText.textContent = labelFor(h);
            chip.classList.remove('hidden');
            chip.classList.add('flex');
            results.classList.add('hidden');
            search.value = '';

            if (h.zone && zoneSelect) {
                const has = Array.from(zoneSelect.options).some(o => o.value === h.zone);
                if (has) zoneSelect.value = h.zone;
            }
        }

        function clearChoice() {
            hiddenId.value = '';
            chip.classList.add('hidden');
            chip.classList.remove('flex');
            search.focus();
        }

        function renderResults() {
            const q = search.value.trim().toLowerCase();
            results.innerHTML = '';

            if (q.length < 1) {
                results.classList.add('hidden');
                return;
            }

            const matches = HOUSEHOLDS.filter(h =>
                (h.code || '').toLowerCase().includes(q) ||
                (h.head || '').toLowerCase().includes(q) ||
                (h.zone || '').toLowerCase().includes(q)
            ).slice(0, 8);

            if (matches.length === 0) {
                const empty = document.createElement('div');
                empty.className = 'px-4 py-3 text-xs text-slate-400';
                empty.textContent = 'No household found.';
                results.appendChild(empty);
            } else {
                matches.forEach(h => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'w-full text-left px-4 py-2.5 hover:bg-emerald-50 border-b border-slate-100 last:border-0';

                    const top = document.createElement('p');
                    top.className = 'text-sm font-bold text-slate-800';
                    top.textContent = h.head;

                    const sub = document.createElement('p');
                    sub.className = 'text-[11px] text-slate-500';
                    sub.textContent = h.code + (h.zone ? ' · ' + h.zone : '') + ' · ' + h.members + ' member' + (h.members === 1 ? '' : 's');

                    btn.appendChild(top);
                    btn.appendChild(sub);
                    btn.addEventListener('click', () => choose(h));
                    results.appendChild(btn);
                });
            }
            results.classList.remove('hidden');
        }

        radios.forEach(r => r.addEventListener('change', refreshModes));
        search.addEventListener('input', renderResults);
        search.addEventListener('focus', renderResults);
        clearBtn.addEventListener('click', clearChoice);
        document.addEventListener('click', e => {
            if (!results.contains(e.target) && e.target !== search) results.classList.add('hidden');
        });

        form.addEventListener('submit', e => {
            if (currentMode() === 'existing' && !hiddenId.value) {
                e.preventDefault();
                search.focus();
                alert('Pumili muna ng household na paglalagyan sa residenteng ito.');
            }
        });

        // Initial state
        if (hiddenId.value) {
            const h = HOUSEHOLDS.find(x => String(x.id) === String(hiddenId.value));
            if (h) {
                chipText.textContent = labelFor(h);
                chip.classList.remove('hidden');
                chip.classList.add('flex');
            }
        }
        refreshModes();
    })();
</script>
@endsection