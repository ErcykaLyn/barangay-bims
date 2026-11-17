@extends('layouts.bhw')

@section('content')
@php $fam = $resident->familyProfile; @endphp

<div class="space-y-8 max-w-7xl mx-auto pb-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Edit Resident</h1>
            <p class="text-sm text-slate-500">Update resident information. Fields marked with <span class="text-red-500">*</span> are required.</p>
        </div>
        <div class="text-sm text-slate-400 bg-slate-50 px-4 py-2 rounded-full">
            <i class="fas fa-edit mr-2 text-emerald-500"></i> Update Entry
        </div>
    </div>

    @if($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 bg-rose-50 border border-rose-200 rounded-xl">
            <i class="fas fa-exclamation-circle text-rose-600 mt-0.5"></i>
            <ul class="list-disc list-inside text-xs text-rose-700 space-y-0.5">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- Kulang ang impormasyon: gabay kung ano ang kailangang punan --}}
    @if($resident->needs_action)
        <div class="flex items-start gap-3 px-4 py-3 bg-amber-50 border border-amber-200 rounded-xl">
            <i class="fas fa-exclamation-triangle text-amber-600 mt-0.5"></i>
            <div class="text-xs text-amber-800">
                <p class="font-bold uppercase tracking-wide mb-0.5">Needs action</p>
                <p>Kulang pa ang: <strong>{{ implode(', ', $resident->missing_fields) }}</strong>. Punan sa ibaba at i-save.</p>
            </div>
        </div>
    @endif

    {{-- Household ng residente --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl {{ $fam ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center">
                <i class="fas fa-home"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Household</p>
                @if($fam)
                    <p class="text-sm font-bold text-slate-800">
                        <span class="font-mono">{{ $fam->family_code }}</span>
                        <span class="text-slate-400 mx-1">·</span>
                        {{ $fam->head_formal_name }}
                        <span class="text-slate-400 mx-1">·</span>
                        {{ $fam->zone_purok ?? 'No zone' }}
                    </p>
                @else
                    <p class="text-sm font-semibold text-slate-500">Wala pa sa household</p>
                @endif
            </div>
        </div>
        <div class="flex gap-2">
            @if($fam)
                <a href="{{ route('bhw.family_profiling.show', $fam->id) }}"
                   class="px-4 py-2 rounded-lg bg-white border border-emerald-300 text-emerald-700 text-xs font-bold hover:bg-emerald-50 transition">
                    <i class="fas fa-eye mr-1"></i> View household
                </a>
                <a href="{{ route('bhw.residents.create', ['household' => $fam->id]) }}"
                   class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-xs font-bold hover:bg-indigo-700 transition">
                    <i class="fas fa-user-plus mr-1"></i> Add family member
                </a>
            @else
                <a href="{{ route('bhw.family_profiling.index') }}"
                   class="px-4 py-2 rounded-lg bg-white border border-slate-300 text-slate-600 text-xs font-bold hover:bg-slate-50 transition">
                    <i class="fas fa-list mr-1"></i> Household list
                </a>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-md overflow-hidden">
        <form action="{{ route('bhw.residents.update', $resident->id) }}" method="POST" class="p-8 space-y-10">
            @csrf
            @method('PUT')

            <!-- Personal Information Section -->
            <div>
                <h2 class="text-base font-black text-slate-800 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-4 mb-6">
                    <i class="fas fa-user-circle text-emerald-500 text-xl"></i> Personal Information
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- First Name -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-1">First Name <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input type="text" name="first_name" value="{{ old('first_name', $resident->first_name) }}" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                        </div>
                    </div>

                    <!-- Middle Name -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-1">Middle Name <span class="text-slate-400 normal-case font-medium">(optional)</span></label>
                        <div class="relative">
                            <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input type="text" name="middle_name" value="{{ old('middle_name', $resident->middle_name) }}"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                        </div>
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-1">Last Name <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-user-alt absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input type="text" name="last_name" value="{{ old('last_name', $resident->last_name) }}" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                        </div>
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-1">Date of Birth <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-calendar-alt absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $resident->date_of_birth ? \Carbon\Carbon::parse($resident->date_of_birth)->format('Y-m-d') : '') }}" required
                                class="w-full bg-slate-50 border {{ $resident->date_of_birth ? 'border-slate-200' : 'border-amber-300 bg-amber-50/40' }} rounded-xl pl-10 pr-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                        </div>
                    </div>

                    <!-- Gender -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-1">Gender <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-venus-mars absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <select name="gender" required
                                class="w-full bg-slate-50 border {{ $resident->gender ? 'border-slate-200' : 'border-amber-300 bg-amber-50/40' }} rounded-xl pl-10 pr-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition appearance-none">
                                @php $g = old('gender', $resident->gender); @endphp
                                <option value="" disabled {{ $g ? '' : 'selected' }}>Select gender</option>
                                <option value="Male" {{ $g == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ $g == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ $g == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>

                    <!-- Civil Status -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-1">Civil Status <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-ring absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <select name="civil_status" required
                                class="w-full bg-slate-50 border {{ $resident->civil_status ? 'border-slate-200' : 'border-amber-300 bg-amber-50/40' }} rounded-xl pl-10 pr-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition appearance-none">
                                @php
                                    $cs = old('civil_status', $resident->civil_status);
                                    $csOptions = ['Single', 'Married', 'Widowed', 'Separated', 'Cohabitation', 'Divorced', 'Annulled'];
                                @endphp
                                <option value="" disabled {{ $cs ? '' : 'selected' }}>Select status</option>
                                @foreach($csOptions as $opt)
                                    <option value="{{ $opt }}" {{ $cs == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                                @if($cs && !in_array($cs, $csOptions))
                                    <option value="{{ $cs }}" selected>{{ $cs }}</option>
                                @endif
                            </select>
                        </div>
                    </div>

                    <!-- Contact Number -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-1">Contact Number</label>
                        <div class="relative">
                            <i class="fas fa-phone-alt absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input type="tel" name="contact_number" value="{{ old('contact_number', $resident->contact_number) }}" placeholder="09XX XXX XXXX"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                        </div>
                    </div>

                    <!-- Zone / Purok -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-1">Zone / Purok <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-map-pin absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <select name="zone_purok" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition appearance-none">
                                @php $zp = old('zone_purok', $resident->zone_purok); @endphp
                                <option value="" disabled {{ $zp ? '' : 'selected' }}>Select Zone</option>
                                @for ($i = 1; $i <= 7; $i++)
                                    <option value="Zone {{ $i }}" {{ $zp == "Zone $i" ? 'selected' : '' }}>Zone {{ $i }}</option>
                                @endfor
                                @if($zp && !preg_match('/^Zone [1-7]$/', $zp))
                                    <option value="{{ $zp }}" selected>{{ $zp }}</option>
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address -->
            <div>
                <h2 class="text-base font-black text-slate-800 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-4 mb-6">
                    <i class="fas fa-map-marker-alt text-emerald-500 text-xl"></i> Address Details
                </h2>

                <input type="hidden" name="complete_address" id="complete_address" value="{{ old('complete_address', $resident->complete_address) }}">

                <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Current address</p>
                        <p id="address_preview" class="text-sm font-semibold text-slate-800 break-words">
                            {{ old('complete_address', $resident->complete_address) ?: 'No address on record' }}
                        </p>
                    </div>
                    <button type="button" id="toggleAddressPicker"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-bold hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200 transition flex-shrink-0">
                        <i class="fas fa-pen text-[10px]"></i> Change address
                    </button>
                </div>

                <div id="addressPicker" class="hidden mt-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-1">Region</label>
                            <div class="relative">
                                <i class="fas fa-globe-asia absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <select id="region" class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition appearance-none">
                                    <option value="">Loading regions...</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-1">Province</label>
                            <div class="relative">
                                <i class="fas fa-map-marked-alt absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <select id="province" disabled class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition appearance-none">
                                    <option value="">Select province first</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-1">City / Municipality</label>
                            <div class="relative">
                                <i class="fas fa-city absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <select id="city" disabled class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition appearance-none">
                                    <option value="">Select city/municipality</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-1">Barangay</label>
                            <div class="relative">
                                <i class="fas fa-home absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <select id="barangay" disabled class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition appearance-none">
                                    <option value="">Select barangay</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-slate-500">
                        <i class="fas fa-info-circle text-emerald-500 mr-1"></i>
                        Ang bagong address ay mapapalitan lang kapag napili mo na ang <strong>Barangay</strong>. Hangga't wala, mananatili ang kasalukuyang address.
                    </p>
                </div>
            </div>

            <!-- Resident Classifications -->
            <div>
                <h2 class="text-base font-black text-slate-800 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-4 mb-6">
                    <i class="fas fa-tags text-emerald-500 text-xl"></i> Resident Classifications
                </h2>
                <div class="flex flex-wrap gap-6">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative">
                            <input type="checkbox" name="is_senior" value="1" class="peer hidden" {{ old('is_senior', $resident->is_senior) ? 'checked' : '' }}>
                            <div class="w-5 h-5 bg-slate-100 border border-slate-300 rounded-md peer-checked:bg-emerald-600 peer-checked:border-emerald-600 transition-all"></div>
                            <svg class="absolute top-1 left-1 w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-600 uppercase tracking-wider group-hover:text-emerald-600 transition-colors">Senior Citizen
                        <span class="text-[10px] text-slate-400 normal-case font-medium">Awtomatiko mula sa edad (60+)</span></span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative">
                            <input type="checkbox" name="is_pwd" value="1" class="peer hidden" {{ old('is_pwd', $resident->is_pwd) ? 'checked' : '' }}>
                            <div class="w-5 h-5 bg-slate-100 border border-slate-300 rounded-md peer-checked:bg-emerald-600 peer-checked:border-emerald-600 transition-all"></div>
                            <svg class="absolute top-1 left-1 w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-600 uppercase tracking-wider group-hover:text-emerald-600 transition-colors">PWD</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative">
                            <input type="checkbox" name="is_4ps" value="1" class="peer hidden" {{ old('is_4ps', $resident->is_4ps) ? 'checked' : '' }}>
                            <div class="w-5 h-5 bg-slate-100 border border-slate-300 rounded-md peer-checked:bg-emerald-600 peer-checked:border-emerald-600 transition-all"></div>
                            <svg class="absolute top-1 left-1 w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-600 uppercase tracking-wider group-hover:text-emerald-600 transition-colors">4Ps Beneficiary</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative">
                            <input type="checkbox" name="is_solo_parent" value="1" class="peer hidden" {{ old('is_solo_parent', $resident->is_solo_parent) ? 'checked' : '' }}>
                            <div class="w-5 h-5 bg-slate-100 border border-slate-300 rounded-md peer-checked:bg-emerald-600 peer-checked:border-emerald-600 transition-all"></div>
                            <svg class="absolute top-1 left-1 w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-600 uppercase tracking-wider group-hover:text-emerald-600 transition-colors">Solo Parent</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative">
                            <input type="checkbox" name="is_voter" value="1" class="peer hidden" {{ old('is_voter', $resident->is_voter) ? 'checked' : '' }}>
                            <div class="w-5 h-5 bg-slate-100 border border-slate-300 rounded-md peer-checked:bg-emerald-600 peer-checked:border-emerald-600 transition-all"></div>
                            <svg class="absolute top-1 left-1 w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-600 uppercase tracking-wider group-hover:text-emerald-600 transition-colors">Registered Voter</span>
                    </label>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="pt-4 border-t border-slate-100">
                <div class="flex flex-col sm:flex-row justify-end gap-4">
                    <a href="{{ route('bhw.residents.index') }}" class="px-6 py-3 rounded-xl bg-slate-100 text-slate-600 font-bold text-sm hover:bg-slate-200 transition text-center">
                        Cancel
                    </a>
                    <button type="submit" class="px-8 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md transition transform hover:scale-[1.02] flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i> Update Resident
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- PSGC API JavaScript (same as create) -->
<script>
    async function fetchPSGC(url) {
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return await response.json();
        } catch (error) {
            console.error(`PSGC API error (${url}):`, error);
            return [];
        }
    }

    async function loadRegions() {
        const regionSelect = document.getElementById('region');
        regionSelect.innerHTML = '<option value="">Loading regions...</option>';
        const regions = await fetchPSGC('https://psgc.gitlab.io/api/regions/');
        regionSelect.innerHTML = '<option value="">Select Region</option>';
        regions.forEach(region => {
            const option = document.createElement('option');
            option.value = region.code;
            option.textContent = region.name;
            regionSelect.appendChild(option);
        });
        regionSelect.disabled = false;
    }

    document.getElementById('region').addEventListener('change', async function() {
        const regionCode = this.value;
        const provinceSelect = document.getElementById('province');
        const citySelect = document.getElementById('city');
        const barangaySelect = document.getElementById('barangay');
        
        if (!regionCode) {
            provinceSelect.innerHTML = '<option value="">Select province first</option>';
            provinceSelect.disabled = true;
            citySelect.innerHTML = '<option value="">Select city/municipality</option>';
            citySelect.disabled = true;
            barangaySelect.innerHTML = '<option value="">Select barangay</option>';
            barangaySelect.disabled = true;
            updateAddressPreview();
            return;
        }
        
        provinceSelect.innerHTML = '<option value="">Loading provinces...</option>';
        provinceSelect.disabled = true;
        citySelect.disabled = true;
        barangaySelect.disabled = true;
        
        const provinces = await fetchPSGC(`https://psgc.gitlab.io/api/regions/${regionCode}/provinces`);
        provinceSelect.innerHTML = '<option value="">Select Province</option>';
        if (provinces.length === 0) {
            provinceSelect.innerHTML = '<option value="">No provinces found</option>';
        } else {
            provinces.forEach(province => {
                const option = document.createElement('option');
                option.value = province.code;
                option.textContent = province.name;
                provinceSelect.appendChild(option);
            });
            provinceSelect.disabled = false;
        }
        
        citySelect.innerHTML = '<option value="">Select city/municipality</option>';
        citySelect.disabled = true;
        barangaySelect.innerHTML = '<option value="">Select barangay</option>';
        barangaySelect.disabled = true;
        updateAddressPreview();
    });

    document.getElementById('province').addEventListener('change', async function() {
        const provinceCode = this.value;
        const citySelect = document.getElementById('city');
        const barangaySelect = document.getElementById('barangay');
        
        if (!provinceCode) {
            citySelect.innerHTML = '<option value="">Select city/municipality</option>';
            citySelect.disabled = true;
            barangaySelect.innerHTML = '<option value="">Select barangay</option>';
            barangaySelect.disabled = true;
            updateAddressPreview();
            return;
        }
        
        citySelect.innerHTML = '<option value="">Loading cities...</option>';
        citySelect.disabled = true;
        barangaySelect.disabled = true;
        
        const [cities, municipalities] = await Promise.all([
            fetchPSGC(`https://psgc.gitlab.io/api/provinces/${provinceCode}/cities`),
            fetchPSGC(`https://psgc.gitlab.io/api/provinces/${provinceCode}/municipalities`)
        ]);
        
        const all = [...cities, ...municipalities];
        all.sort((a, b) => a.name.localeCompare(b.name));
        
        citySelect.innerHTML = '<option value="">Select City / Municipality</option>';
        if (all.length === 0) {
            citySelect.innerHTML = '<option value="">No cities/municipalities found</option>';
        } else {
            all.forEach(place => {
                const option = document.createElement('option');
                option.value = place.code;
                option.textContent = place.name;
                citySelect.appendChild(option);
            });
            citySelect.disabled = false;
        }
        
        barangaySelect.innerHTML = '<option value="">Select barangay</option>';
        barangaySelect.disabled = true;
        updateAddressPreview();
    });

    document.getElementById('city').addEventListener('change', async function() {
        const cityCode = this.value;
        const barangaySelect = document.getElementById('barangay');
        
        if (!cityCode) {
            barangaySelect.innerHTML = '<option value="">Select barangay</option>';
            barangaySelect.disabled = true;
            updateAddressPreview();
            return;
        }
        
        barangaySelect.innerHTML = '<option value="">Loading barangays...</option>';
        barangaySelect.disabled = true;
        
        const barangays = await fetchPSGC(`https://psgc.gitlab.io/api/cities-municipalities/${cityCode}/barangays`);
        barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
        if (barangays.length === 0) {
            barangaySelect.innerHTML = '<option value="">No barangays found</option>';
        } else {
            barangays.forEach(barangay => {
                const option = document.createElement('option');
                option.value = barangay.name;
                option.textContent = barangay.name;
                barangaySelect.appendChild(option);
            });
            barangaySelect.disabled = false;
        }
        updateAddressPreview();
    });

    document.getElementById('barangay').addEventListener('change', updateAddressPreview);
    
    function updateAddressPreview() {
        const regionSelect = document.getElementById('region');
        const provinceSelect = document.getElementById('province');
        const citySelect = document.getElementById('city');
        const barangaySelect = document.getElementById('barangay');
        
        const regionText = regionSelect.options[regionSelect.selectedIndex]?.textContent || '';
        const provinceText = provinceSelect.options[provinceSelect.selectedIndex]?.textContent || '';
        const cityText = citySelect.options[citySelect.selectedIndex]?.textContent || '';
        const barangayText = barangaySelect.value;
        
        let addressParts = [];
        if (barangayText) addressParts.push(barangayText);
        if (cityText && cityText !== 'Select City / Municipality') addressParts.push(cityText);
        if (provinceText && provinceText !== 'Select Province') addressParts.push(provinceText);
        if (regionText && regionText !== 'Select Region') addressParts.push(regionText);
        
        // Palitan lang ang address kapag may napili nang Barangay (kumpleto na)
        if (!barangayText) return;

        const fullAddress = addressParts.join(', ');
        document.getElementById('address_preview').innerText = fullAddress;
        document.getElementById('complete_address').value = fullAddress;
    }

    // Ang address picker ay lalabas lang kapag pinindot ang "Change address"
    let regionsLoaded = false;
    document.getElementById('toggleAddressPicker').addEventListener('click', function () {
        const picker = document.getElementById('addressPicker');
        picker.classList.toggle('hidden');
        if (!picker.classList.contains('hidden') && !regionsLoaded) {
            regionsLoaded = true;
            loadRegions();
        }
    });
</script>
@endsection