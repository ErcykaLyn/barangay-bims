@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-slate-100">

    <!-- TOP ACTION BAR -->
    <div class="bg-white border-b border-slate-200 shadow-sm">
        <div class="px-4 sm:px-6 lg:px-8 py-3">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('admin.residents.index') }}" 
                   class="h-9 w-9 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors flex-shrink-0">
                    <i class="fas fa-arrow-left text-sm"></i>
                </a>
                <div class="min-w-0">
                    <nav class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        <a href="{{ route('admin.residents.index') }}" class="hover:text-emerald-600 transition">Residents</a>
                        <i class="fas fa-chevron-right text-[8px]"></i>
                        <span class="text-slate-700">Edit Record</span>
                    </nav>
                    <h1 class="text-sm sm:text-base font-black text-slate-800 tracking-tight truncate">
                        {{ $resident->first_name }} {{ $resident->last_name }}
                    </h1>
                </div>
            </div>
        </div>
    </div>

    <!-- ALERT MESSAGES -->
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

        @if($errors->any())
            <div class="flex items-start gap-3 px-4 py-3 bg-rose-50 border-l-4 border-rose-500 rounded-lg shadow-sm">
                <i class="fas fa-exclamation-circle text-rose-600 mt-0.5"></i>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-rose-800 uppercase tracking-wider mb-1">Validation Errors</p>
                    <ul class="list-disc list-inside text-xs text-rose-700 space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>

    <!-- MAIN CONTENT -->
    <form action="{{ route('admin.residents.update', $resident->id) }}" method="POST" id="residentEditForm">
        @csrf
        @method('PUT')

        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <!-- LEFT COLUMN: FORM (8/12) -->
                <div class="lg:col-span-8 space-y-6">

                    <!-- SECTION: Full Name -->
                    <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
                        <div class="px-5 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="h-7 w-7 bg-emerald-100 rounded-md flex items-center justify-center text-emerald-600">
                                    <i class="fas fa-id-badge text-xs"></i>
                                </div>
                                <div>
                                    <h2 class="text-xs font-black text-slate-800 uppercase tracking-wider">Full Name</h2>
                                    <p class="text-[10px] text-slate-500">Resident profile information</p>
                                </div>
                            </div>
                            @if($resident->email)
                                <a href="{{ route('admin.registered-accounts.index', ['search' => $resident->email]) }}"
                                   class="inline-flex items-center gap-1.5 text-[10px] font-black text-emerald-700 hover:text-emerald-900 uppercase tracking-wider">
                                    <i class="fas fa-key text-[9px]"></i> Manage login
                                </a>
                            @endif
                        </div>

                        <div class="p-5 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- First Name -->
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1.5">
                                    First Name <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="first_name" id="inputFirstName" value="{{ old('first_name', $resident->first_name) }}"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all"
                                    required>
                            </div>

                            <!-- Middle Name -->
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1.5">
                                    Middle Name <span class="text-slate-400 font-medium">(Optional)</span>
                                </label>
                                <input type="text" name="middle_name" id="inputMiddleName" value="{{ old('middle_name', $resident->middle_name) }}"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all"
                                    placeholder="Santos">
                            </div>

                            <!-- Last Name -->
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1.5">
                                    Last Name <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="last_name" id="inputLastName" value="{{ old('last_name', $resident->last_name) }}"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all"
                                    required>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION: Personal Information -->
                    <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
                        <div class="px-5 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="h-7 w-7 bg-indigo-100 rounded-md flex items-center justify-center text-indigo-600">
                                    <i class="fas fa-user text-xs"></i>
                                </div>
                                <div>
                                    <h2 class="text-xs font-black text-slate-800 uppercase tracking-wider">Personal Information</h2>
                                    <p class="text-[10px] text-slate-500">Demographics and contact details</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <!-- Birth Date -->
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1.5">
                                    Birth Date <span class="text-rose-500">*</span>
                                </label>
                                <input type="date" name="date_of_birth" 
                                    value="{{ old('date_of_birth', $resident->date_of_birth ? \Carbon\Carbon::parse($resident->date_of_birth)->format('Y-m-d') : '') }}"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all"
                                    required>
                            </div>

                            <!-- Gender -->
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1.5">
                                    Gender <span class="text-rose-500">*</span>
                                </label>
                                <select name="gender"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all cursor-pointer"
                                    required>
                                    <option value="" disabled {{ old('gender', $resident->gender) ? '' : 'selected' }}>Select gender</option>
                                    <option value="Male" {{ old('gender', $resident->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender', $resident->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>

                            <!-- Civil Status -->
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1.5">
                                    Civil Status <span class="text-rose-500">*</span>
                                </label>
                                <select name="civil_status"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all cursor-pointer"
                                    required>
                                    <option value="" disabled {{ old('civil_status', $resident->civil_status) ? '' : 'selected' }}>Select status</option>
                                    <option value="Single" {{ old('civil_status', $resident->civil_status) == 'Single' ? 'selected' : '' }}>Single</option>
                                    <option value="Married" {{ old('civil_status', $resident->civil_status) == 'Married' ? 'selected' : '' }}>Married</option>
                                    <option value="Widowed" {{ old('civil_status', $resident->civil_status) == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                    <option value="Separated" {{ old('civil_status', $resident->civil_status) == 'Separated' ? 'selected' : '' }}>Separated</option>
                                </select>
                            </div>

                            <!-- Contact Number -->
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1.5">Contact Number</label>
                                <input type="text" name="contact_number" value="{{ old('contact_number', $resident->contact_number) }}"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all"
                                    placeholder="09XX XXX XXXX">
                            </div>

                            <!-- Occupation -->
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1.5">Occupation</label>
                                <input type="text" name="occupation" value="{{ old('occupation', $resident->occupation) }}"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all"
                                    placeholder="e.g. Farmer, Teacher">
                            </div>
                        </div>
                    </div>

                    <!-- SECTION: Address -->
                    <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
                        <div class="px-5 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="h-7 w-7 bg-amber-100 rounded-md flex items-center justify-center text-amber-600">
                                    <i class="fas fa-map-marker-alt text-xs"></i>
                                </div>
                                <div>
                                    <h2 class="text-xs font-black text-slate-800 uppercase tracking-wider">Address Information</h2>
                                    <p class="text-[10px] text-slate-500">Zone and complete address</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-5 grid grid-cols-1 sm:grid-cols-4 gap-4">
                            <!-- Zone -->
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1.5">
                                    Zone / Purok <span class="text-rose-500">*</span>
                                </label>
                                <select name="zone_purok"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all cursor-pointer"
                                    required>
                                    <option value="" disabled {{ old('zone_purok', $resident->zone_purok) ? '' : 'selected' }}>Select zone</option>
                                    @for ($i = 1; $i <= 7; $i++)
                                        <option value="Zone {{ $i }}" {{ old('zone_purok', $resident->zone_purok) == "Zone $i" ? 'selected' : '' }}>Zone {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>

                            <!-- Complete Address -->
                            <div class="sm:col-span-3">
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1.5">
                                    Complete Address <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="complete_address" value="{{ old('complete_address', $resident->complete_address) }}"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all"
                                    required>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- RIGHT COLUMN: SIDEBAR (4/12) -->
                <div class="lg:col-span-4 space-y-6">

                    <!-- IDENTITY CARD -->
                    <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
                        <div class="bg-gradient-to-br from-slate-800 to-slate-900 px-5 py-5">
                            <div class="flex items-center gap-4">
                                <div class="h-16 w-16 rounded-lg overflow-hidden ring-2 ring-emerald-500/50 flex items-center justify-center bg-slate-700 flex-shrink-0">
                                    @if($resident->profile_photo_url)
                                        <img src="{{ $resident->profile_photo_url }}" alt="{{ $resident->first_name }}" class="h-full w-full object-cover">
                                    @else
                                        <span id="previewInitials" class="text-white text-xl font-black">
                                            {{ strtoupper(substr($resident->first_name, 0, 1) . substr($resident->last_name, 0, 1)) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[9px] font-black text-emerald-400 uppercase tracking-widest mb-1">Resident Record</p>
                                    <p class="text-base font-bold text-white truncate" id="previewName">{{ $resident->first_name }} {{ $resident->last_name }}</p>
                                    @if($resident->email)
                                        <p class="text-xs text-slate-400 font-mono truncate mt-0.5">{{ $resident->email }}</p>
                                    @else
                                        <p class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-300 mt-1"><i class="fas fa-user-slash text-[9px]"></i> Not registered</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CLASSIFICATIONS -->
                    <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
                        <div class="px-5 py-3 border-b border-slate-200 bg-slate-50">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-tags text-purple-500 text-xs"></i>
                                <h2 class="text-xs font-black text-slate-800 uppercase tracking-wider">Classifications</h2>
                            </div>
                        </div>

                        <div class="p-4 space-y-2">
                            @php
                                $tags = [
                                    'is_senior' => ['label' => 'Senior Citizen', 'icon' => 'fa-user-clock'],
                                    'is_4ps' => ['label' => '4Ps Beneficiary', 'icon' => 'fa-hand-holding-heart'],
                                    'is_pwd' => ['label' => 'PWD', 'icon' => 'fa-wheelchair'],
                                    'is_solo_parent' => ['label' => 'Solo Parent', 'icon' => 'fa-user-friends'],
                                    'is_voter' => ['label' => 'Registered Voter', 'icon' => 'fa-vote-yea'],
                                ];
                            @endphp

                            @foreach($tags as $field => $config)
                            <label class="flex items-center justify-between p-2.5 rounded-md border border-slate-200 hover:border-emerald-300 hover:bg-emerald-50/30 cursor-pointer transition-all group">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <i class="fas {{ $config['icon'] }} text-slate-400 group-hover:text-emerald-600 transition-colors text-xs w-4 text-center"></i>
                                    <span class="text-xs font-semibold text-slate-700 group-hover:text-emerald-800 transition-colors">{{ $config['label'] }}</span>
                                </div>
                                <input type="checkbox" name="{{ $field }}" value="1" 
                                       class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500/30 cursor-pointer" 
                                       {{ old($field, $resident->$field) ? 'checked' : '' }}>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- DANGER ZONE -->
                    <div class="bg-white border border-rose-200 rounded-lg shadow-sm">
                        <div class="px-5 py-3 border-b border-rose-100 bg-rose-50">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-exclamation-triangle text-rose-600 text-xs"></i>
                                <h2 class="text-xs font-black text-rose-800 uppercase tracking-wider">Danger Zone</h2>
                            </div>
                        </div>
                        <div class="p-4">
                            <p class="text-[11px] text-slate-600 mb-3">Permanently delete this resident record. This action cannot be undone.</p>
                            <a href="{{ route('admin.residents.index') }}" 
                               class="w-full px-3 py-2 bg-white border border-rose-300 text-rose-600 text-xs font-bold rounded-md hover:bg-rose-50 transition-colors flex items-center justify-center gap-1.5">
                                <i class="fas fa-arrow-left text-[10px]"></i>
                                Go Back
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- BOTTOM SAVE BAR (Desktop) -->
        <div class="hidden lg:block bg-white border-t border-slate-200 shadow-lg mt-6">
            <div class="px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-2 text-xs text-slate-500">
                        <i class="fas fa-info-circle text-emerald-500"></i>
                        <span>Changes will be saved immediately when you click "Save Changes".</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.residents.index') }}" 
                           class="px-5 py-2.5 bg-white border border-slate-300 text-slate-600 text-xs font-bold rounded-lg hover:bg-slate-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black rounded-lg shadow-md transition-all flex items-center gap-2 uppercase tracking-wider">
                            <i class="fas fa-save text-[10px]"></i>
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- FLOATING SAVE BUTTON (Mobile) -->
    <div class="fixed bottom-6 right-6 z-40 lg:hidden">
        <button type="submit" form="residentEditForm" 
                class="h-14 w-14 bg-emerald-600 hover:bg-emerald-700 text-white rounded-full shadow-2xl shadow-emerald-500/50 flex items-center justify-center transition-all active:scale-95">
            <i class="fas fa-save text-lg"></i>
        </button>
    </div>
</div>

<script>
    // Live preview update
    document.addEventListener('DOMContentLoaded', function() {
        const inputFirstName = document.getElementById('inputFirstName');
        const inputMiddleName = document.getElementById('inputMiddleName');
        const inputLastName = document.getElementById('inputLastName');
        const previewName = document.getElementById('previewName');
        const previewInitials = document.getElementById('previewInitials');

        function updatePreview() {
            const first = inputFirstName?.value || '';
            const middle = inputMiddleName?.value || '';
            const last = inputLastName?.value || '';

            const parts = [first, middle, last].filter(p => p.trim() !== '');
            const fullName = parts.join(' ').trim();

            if (previewName) {
                previewName.textContent = fullName || '{{ $resident->first_name }} {{ $resident->middle_name }} {{ $resident->last_name }}';
            }

            if (previewInitials) {
                let initials = '';
                if (first) initials += first.charAt(0);
                if (middle) initials += middle.charAt(0);
                if (last) initials += last.charAt(0);
                previewInitials.textContent = initials.toUpperCase() || '?';
            }
        }

        inputFirstName?.addEventListener('input', updatePreview);
        inputMiddleName?.addEventListener('input', updatePreview);
        inputLastName?.addEventListener('input', updatePreview);
    });
</script>
@endsection