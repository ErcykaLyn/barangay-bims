@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-slate-100">

    <!-- ============ TOP ACTION BAR ============ -->
    <div class="bg-white border-b border-slate-200 shadow-sm">
        <div class="px-4 sm:px-6 lg:px-8 py-3">
            <div class="flex items-center justify-between gap-4">
                <!-- Left: Breadcrumb -->
                <div class="flex items-center gap-3 min-w-0">
                    <a href="{{ route('admin.users.index') }}" 
                       class="h-9 w-9 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors flex-shrink-0">
                        <i class="fas fa-arrow-left text-sm"></i>
                    </a>
                    <div class="min-w-0">
                        <nav class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                            <a href="{{ route('admin.users.index') }}" class="hover:text-emerald-600 transition">Staff</a>
                            <i class="fas fa-chevron-right text-[8px]"></i>
                            <span class="text-slate-700">New Account</span>
                        </nav>
                        <h1 class="text-sm sm:text-base font-black text-slate-800 tracking-tight truncate">
                            Create Staff Account
                        </h1>
                    </div>
                </div>

                <!-- Right: Badge -->
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-lg text-[10px] font-bold uppercase tracking-wider border border-emerald-200">
                        <i class="fas fa-shield-alt text-[10px]"></i>
                        <span class="hidden sm:inline">Secure Creation</span>
                        <span class="sm:hidden">Secure</span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- ============ ALERT MESSAGES ============ -->
    @if($errors->any())
        <div class="px-4 sm:px-6 lg:px-8 pt-4">
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
        </div>
    @endif

    <!-- ============ MAIN CONTENT ============ -->
    <form action="{{ route('admin.users.store') }}" method="POST" id="staffForm">
        @csrf

        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <!-- ============ LEFT COLUMN: FORM (8/12) ============ -->
                <div class="lg:col-span-8 space-y-6">

                    <!-- ===== SECTION: Personal Information ===== -->
                    <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
                        <div class="px-5 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="h-7 w-7 bg-emerald-100 rounded-md flex items-center justify-center text-emerald-600">
                                    <i class="fas fa-user text-xs"></i>
                                </div>
                                <div>
                                    <h2 class="text-xs font-black text-slate-800 uppercase tracking-wider">Personal Information</h2>
                                    <p class="text-[10px] text-slate-500">Staff member's name</p>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Required</span>
                        </div>

                        <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- First Name -->
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1.5">
                                    First Name <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="first_name" id="inputFirstName" value="{{ old('first_name') }}"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all"
                                    placeholder="Juan" required>
                            </div>

                            <!-- Last Name -->
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1.5">
                                    Last Name <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="last_name" id="inputLastName" value="{{ old('last_name') }}"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all"
                                    placeholder="Dela Cruz" required>
                            </div>
                        </div>
                    </div>

                    <!-- ===== SECTION: Contact Information ===== -->
                    <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
                        <div class="px-5 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="h-7 w-7 bg-indigo-100 rounded-md flex items-center justify-center text-indigo-600">
                                    <i class="fas fa-envelope text-xs"></i>
                                </div>
                                <div>
                                    <h2 class="text-xs font-black text-slate-800 uppercase tracking-wider">Contact Information</h2>
                                    <p class="text-[10px] text-slate-500">Login credentials</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-5">
                            <!-- Email -->
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1.5">
                                    Email Address <span class="text-rose-500">*</span>
                                </label>
                                <input type="email" name="email" id="inputEmail" value="{{ old('email') }}"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all"
                                    placeholder="user@bactadeast.gov.ph" required>
                                <p class="text-[10px] text-slate-400 mt-1">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    This email will be used for login credentials.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- ===== SECTION: Role Selection ===== -->
                    <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
                        <div class="px-5 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="h-7 w-7 bg-purple-100 rounded-md flex items-center justify-center text-purple-600">
                                    <i class="fas fa-user-tag text-xs"></i>
                                </div>
                                <div>
                                    <h2 class="text-xs font-black text-slate-800 uppercase tracking-wider">Role Selection</h2>
                                    <p class="text-[10px] text-slate-500">Assign staff permissions</p>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Required</span>
                        </div>

                        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Admin Role Card -->
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="role" value="admin" class="peer sr-only" required
                                    {{ old('role') == 'admin' ? 'checked' : '' }}>
                                <div class="p-4 bg-white border-2 border-slate-200 rounded-lg transition-all group-hover:border-indigo-300 group-hover:bg-indigo-50/30 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:shadow-md peer-checked:shadow-indigo-500/10">
                                    <div class="flex items-start gap-3">
                                        <div class="h-10 w-10 bg-indigo-100 text-indigo-600 rounded-md flex items-center justify-center flex-shrink-0 transition-all">
                                            <i class="fas fa-user-shield text-base"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-black text-slate-800 uppercase tracking-wider">Administrator</p>
                                            <p class="text-[10px] text-slate-500 mt-0.5 leading-relaxed">Full system access & management</p>
                                        </div>
                                        <div class="h-4 w-4 rounded-full border-2 border-slate-300 peer-checked:border-indigo-500 peer-checked:bg-indigo-500 flex items-center justify-center flex-shrink-0 transition-all mt-1">
                                            <i class="fas fa-check text-white text-[6px] opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- BHW Role Card -->
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="role" value="bhw" class="peer sr-only" required
                                    {{ old('role') == 'bhw' ? 'checked' : '' }}>
                                <div class="p-4 bg-white border-2 border-slate-200 rounded-lg transition-all group-hover:border-cyan-300 group-hover:bg-cyan-50/30 peer-checked:border-cyan-500 peer-checked:bg-cyan-50 peer-checked:shadow-md peer-checked:shadow-cyan-500/10">
                                    <div class="flex items-start gap-3">
                                        <div class="h-10 w-10 bg-cyan-100 text-cyan-600 rounded-md flex items-center justify-center flex-shrink-0 transition-all">
                                            <i class="fas fa-user-nurse text-base"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-black text-slate-800 uppercase tracking-wider">BHW</p>
                                            <p class="text-[10px] text-slate-500 mt-0.5 leading-relaxed">Health worker role & profiling</p>
                                        </div>
                                        <div class="h-4 w-4 rounded-full border-2 border-slate-300 peer-checked:border-cyan-500 peer-checked:bg-cyan-500 flex items-center justify-center flex-shrink-0 transition-all mt-1">
                                            <i class="fas fa-check text-white text-[6px] opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- ===== SECTION: Account Security ===== -->
                    <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
                        <div class="px-5 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="h-7 w-7 bg-slate-200 rounded-md flex items-center justify-center text-slate-700">
                                    <i class="fas fa-lock text-xs"></i>
                                </div>
                                <div>
                                    <h2 class="text-xs font-black text-slate-800 uppercase tracking-wider">Account Security</h2>
                                    <p class="text-[10px] text-slate-500">Set login password</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Password -->
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1.5">
                                    Password <span class="text-rose-500">*</span>
                                </label>
                                <input type="password" name="password" required minlength="8"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-mono text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all"
                                    placeholder="Minimum 8 characters">
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1.5">
                                    Confirm Password <span class="text-rose-500">*</span>
                                </label>
                                <input type="password" name="password_confirmation" required
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-mono text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all"
                                    placeholder="Re-enter password">
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ============ RIGHT COLUMN: SIDEBAR (4/12) ============ -->
                <div class="lg:col-span-4 space-y-6">

                    <!-- ===== LIVE PREVIEW CARD ===== -->
                    <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
                        <div class="px-5 py-3 border-b border-slate-200 bg-slate-50">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-eye text-emerald-500 text-xs"></i>
                                <h2 class="text-xs font-black text-slate-800 uppercase tracking-wider">Live Preview</h2>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-slate-800 to-slate-900 px-5 py-5">
                            <div class="flex items-center gap-4">
                                <div class="h-16 w-16 rounded-lg flex items-center justify-center bg-slate-700 ring-2 ring-emerald-500/50 flex-shrink-0">
                                    <span id="previewInitials" class="text-white text-xl font-black">?</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[9px] font-black text-emerald-400 uppercase tracking-widest mb-1">New Staff Member</p>
                                    <p class="text-base font-bold text-white truncate" id="previewName">Juan Dela Cruz</p>
                                    <p class="text-xs text-slate-400 font-mono truncate mt-0.5" id="previewEmail">email@bactadeast.gov.ph</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 space-y-3">
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1.5">Role</p>
                                <span id="previewRole" class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-slate-500 rounded-md text-[10px] font-black uppercase tracking-wider border border-slate-200">
                                    <i class="fas fa-question-circle"></i>
                                    No Role Selected
                                </span>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1.5">Status</p>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-md text-[10px] font-black uppercase tracking-wider border border-emerald-200">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Active on Creation
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- ===== SECURITY TIPS ===== -->
                    <div class="bg-gradient-to-br from-emerald-600 to-emerald-800 rounded-lg shadow-sm p-5 text-white relative overflow-hidden">
                        <div class="absolute -right-6 -bottom-6 text-white/10 text-8xl">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="relative z-10">
                            <div class="flex items-center gap-2 mb-3">
                                <i class="fas fa-shield-halved text-emerald-200"></i>
                                <h3 class="text-xs font-black uppercase tracking-wider">Security Guidelines</h3>
                            </div>
                            <ul class="space-y-2 text-[11px] text-emerald-50 leading-relaxed">
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-check text-emerald-300 mt-0.5 text-[10px]"></i>
                                    <span>Use a strong password with 8+ characters</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-check text-emerald-300 mt-0.5 text-[10px]"></i>
                                    <span>Only grant Admin role to trusted personnel</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-check text-emerald-300 mt-0.5 text-[10px]"></i>
                                    <span>Use official barangay email addresses</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-check text-emerald-300 mt-0.5 text-[10px]"></i>
                                    <span>Account is active immediately upon creation</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- ============ BOTTOM SAVE BAR (Desktop) ============ -->
        <div class="hidden lg:block bg-white border-t border-slate-200 shadow-lg mt-6">
            <div class="px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-2 text-xs text-slate-500">
                        <i class="fas fa-info-circle text-emerald-500"></i>
                        <span>New staff account will be activated immediately.</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.users.index') }}" 
                           class="px-5 py-2.5 bg-white border border-slate-300 text-slate-600 text-xs font-bold rounded-lg hover:bg-slate-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black rounded-lg shadow-md transition-all flex items-center gap-2 uppercase tracking-wider">
                            <i class="fas fa-save text-[10px]"></i>
                            Create Account
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- ============ FLOATING SAVE BUTTON (Mobile) ============ -->
    <div class="fixed bottom-6 right-6 z-40 lg:hidden">
        <button type="submit" form="staffForm" 
                class="h-14 w-14 bg-emerald-600 hover:bg-emerald-700 text-white rounded-full shadow-2xl shadow-emerald-500/50 flex items-center justify-center transition-all active:scale-95">
            <i class="fas fa-save text-lg"></i>
        </button>
    </div>
</div>

<script>
    // Live preview update
    document.addEventListener('DOMContentLoaded', function() {
        const firstNameInput = document.getElementById('inputFirstName');
        const lastNameInput = document.getElementById('inputLastName');
        const emailInput = document.getElementById('inputEmail');
        const roleInputs = document.querySelectorAll('input[name="role"]');

        const previewInitials = document.getElementById('previewInitials');
        const previewName = document.getElementById('previewName');
        const previewEmail = document.getElementById('previewEmail');
        const previewRole = document.getElementById('previewRole');

        function updatePreview() {
            const firstName = firstNameInput?.value || '';
            const lastName = lastNameInput?.value || '';
            const email = emailInput?.value || '';
            const role = document.querySelector('input[name="role"]:checked')?.value || '';

            // Initials
            const initials = (firstName.charAt(0) + lastName.charAt(0)).toUpperCase() || '?';
            previewInitials.textContent = initials;

            // Name
            const fullName = (firstName + ' ' + lastName).trim() || 'Juan Dela Cruz';
            previewName.textContent = fullName;

            // Email
            previewEmail.textContent = email || 'email@bactadeast.gov.ph';

            // Role badge
            if (role === 'admin') {
                previewRole.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 bg-indigo-100 text-indigo-700 rounded-md text-[10px] font-black uppercase tracking-wider border border-indigo-200';
                previewRole.innerHTML = '<i class="fas fa-user-shield"></i> Administrator';
            } else if (role === 'bhw') {
                previewRole.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 bg-cyan-100 text-cyan-700 rounded-md text-[10px] font-black uppercase tracking-wider border border-cyan-200';
                previewRole.innerHTML = '<i class="fas fa-user-nurse"></i> BHW';
            } else {
                previewRole.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-slate-500 rounded-md text-[10px] font-black uppercase tracking-wider border border-slate-200';
                previewRole.innerHTML = '<i class="fas fa-question-circle"></i> No Role Selected';
            }
        }

        // Attach listeners
        firstNameInput?.addEventListener('input', updatePreview);
        lastNameInput?.addEventListener('input', updatePreview);
        emailInput?.addEventListener('input', updatePreview);
        roleInputs.forEach(input => input.addEventListener('change', updatePreview));

        // Initial update
        updatePreview();
    });
</script>
@endsection