<div class="h-full flex flex-col bg-emerald-800 border-r border-emerald-700">
    {{-- Branding --}}
    <div class="px-6 py-8 flex items-center gap-3">
        <div class="h-10 w-10 rounded-full border-2 border-white shadow-md flex-shrink-0 flex items-center justify-center bg-white/10 p-0.5">
            <img src="{{ asset('images/brgy_logo.png.jpg') }}" 
                 alt="Brgy Logo" 
                 class="h-full w-full rounded-full object-cover"
                 onerror="this.src='https://via.placeholder.com/40?text=BE'">
        </div>
        <div class="flex flex-col">
            <h1 class="text-[13px] font-black text-white tracking-tight leading-tight">
                Barangay Information<br>
                Management <span class="text-emerald-300">Bactad East</span>
            </h1>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-4 space-y-1 overflow-y-auto custom-scroll pb-4">

        {{-- ==================== MAIN ==================== --}}
        <div class="px-2 mb-2">
            <h2 class="text-[10px] font-black text-emerald-300/70 uppercase tracking-[0.2em]">Main</h2>
        </div>

        {{-- Dashboard --}}
        <a href="{{ route('admin.dashboard') }}" 
            class="flex items-center p-3 rounded-xl transition-all group {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-700 text-white border-l-4 border-emerald-400 shadow-md' : 'text-emerald-100 hover:bg-emerald-700/70 hover:text-white' }}">
            <span class="mr-3 text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
            </span>
            <span class="text-[13px] font-bold tracking-tight">Dashboard</span>
        </a>

        {{-- ==================== RESIDENT MANAGEMENT ==================== --}}
        <div class="px-2 pt-4 mb-2">
            <h2 class="text-[10px] font-black text-emerald-300/70 uppercase tracking-[0.2em]">Resident Management</h2>
        </div>

        {{-- Residents List --}}
        <a href="{{ route('admin.residents.index') }}" 
            class="flex items-center p-3 rounded-xl transition-all group {{ request()->routeIs('admin.residents.index') ? 'bg-emerald-700 text-white border-l-4 border-emerald-400 shadow-md' : 'text-emerald-100 hover:bg-emerald-700/70 hover:text-white' }}">
            <span class="mr-3 text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                </svg>
            </span>
            <span class="text-[13px] font-bold tracking-tight">Residents List</span>
        </a>

        {{-- Deceased --}}
        <a href="{{ route('admin.residents.deceased') }}" 
            class="flex items-center p-3 rounded-xl transition-all group {{ request()->routeIs('admin.residents.deceased') ? 'bg-emerald-700 text-white border-l-4 border-emerald-400 shadow-md' : 'text-emerald-100 hover:bg-emerald-700/70 hover:text-white' }}">
            <span class="mr-3 text-white">
                <i class="fas fa-cross text-base"></i>
            </span>
            <span class="text-[13px] font-bold tracking-tight">Deceased</span>
        </a>

        {{-- Pending Accounts --}}
        <a href="{{ route('admin.residents.pending') }}" 
            class="flex items-center justify-between p-3 rounded-xl transition-all group {{ request()->routeIs('admin.residents.pending') ? 'bg-emerald-700 text-white border-l-4 border-emerald-400 shadow-md' : 'text-emerald-100 hover:bg-emerald-700/70 hover:text-white' }}">
            <div class="flex items-center">
                <span class="mr-3 text-white">
                    <i class="fas fa-user-clock text-base"></i>
                </span>
                <span class="text-[13px] font-bold tracking-tight">Pending Accounts</span>
            </div>
            @if(isset($pendingResidentsCount) && $pendingResidentsCount > 0)
                <span class="bg-amber-400 text-emerald-950 text-[10px] font-black px-2 py-0.5 rounded-full shadow-sm">
                    {{ $pendingResidentsCount }}
                </span>
            @endif
        </a>

        {{-- Registered Accounts --}}
        <a href="{{ route('admin.registered-accounts.index') }}" 
            class="flex items-center justify-between p-3 rounded-xl transition-all group {{ request()->routeIs('admin.registered-accounts.*') ? 'bg-emerald-700 text-white border-l-4 border-emerald-400 shadow-md' : 'text-emerald-100 hover:bg-emerald-700/70 hover:text-white' }}">
            <div class="flex items-center">
                <span class="mr-3 text-white">
                    <i class="fas fa-user-check text-base"></i>
                </span>
                <span class="text-[13px] font-bold tracking-tight">Registered Accounts</span>
            </div>
        </a>

        {{-- Voters --}}
        <a href="{{ route('admin.voters.index') }}" 
            class="flex items-center p-3 rounded-xl transition-all group {{ request()->routeIs('admin.voters.index') ? 'bg-emerald-700 text-white border-l-4 border-emerald-400 shadow-md' : 'text-emerald-100 hover:bg-emerald-700/70 hover:text-white' }}">
            <span class="mr-3 text-white">
                <i class="fas fa-vote-yea text-base"></i>
            </span>
            <span class="text-[13px] font-bold tracking-tight">Voters</span>
        </a>

        {{-- ==================== SERVICES ==================== --}}
        <div class="px-2 pt-4 mb-2">
            <h2 class="text-[10px] font-black text-emerald-300/70 uppercase tracking-[0.2em]">Services</h2>
        </div>

        {{-- Inventory --}}
        <a href="{{ route('admin.equipment.index') }}" 
            class="flex items-center p-3 rounded-xl transition-all group {{ request()->routeIs('admin.equipment.index') ? 'bg-emerald-700 text-white border-l-4 border-emerald-400 shadow-md' : 'text-emerald-100 hover:bg-emerald-700/70 hover:text-white' }}">
            <span class="mr-3 text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                </svg>
            </span>
            <span class="text-[13px] font-bold tracking-tight">Inventory</span>
        </a>

        {{-- Borrow Requests --}}
        <a href="{{ route('admin.equipment.requests') }}" 
            class="flex items-center justify-between p-3 rounded-xl transition-all group {{ request()->routeIs('admin.equipment.requests') ? 'bg-emerald-700 text-white border-l-4 border-emerald-400 shadow-md' : 'text-emerald-100 hover:bg-emerald-700/70 hover:text-white' }}">
            <div class="flex items-center">
                <span class="mr-3 text-white">
                    <i class="fas fa-clipboard-list text-base"></i>
                </span>
                <span class="text-[13px] font-bold tracking-tight">Equipment Borrow Requests</span>
            </div>
            @php
                $pendingEquipmentReqs = \App\Models\EquipmentRequest::where('status', 'pending')->count();
            @endphp
            @if($pendingEquipmentReqs > 0)
                <span class="bg-amber-400 text-emerald-950 text-[10px] font-black px-2 py-0.5 rounded-full shadow-sm animate-pulse">
                    {{ $pendingEquipmentReqs }}
                </span>
            @endif
        </a>

        {{-- Requests --}}
        <a href="{{ route('admin.requests.index') }}" 
            class="flex items-center justify-between p-3 rounded-xl transition-all group {{ request()->routeIs('admin.requests.index') ? 'bg-emerald-700 text-white border-l-4 border-emerald-400 shadow-md' : 'text-emerald-100 hover:bg-emerald-700/70 hover:text-white' }}">
            <div class="flex items-center">
                <span class="mr-3 text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                    </svg>
                </span>
                <span class="text-[13px] font-bold tracking-tight">Documents Requests</span>
            </div>
            @if(isset($newRequestsCount) && $newRequestsCount > 0)
                <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $newRequestsCount }}</span>
            @endif
        </a>

        {{-- Request Logs --}}
        <a href="{{ route('admin.requests.logs') }}" 
            class="flex items-center p-3 rounded-xl transition-all group {{ request()->routeIs('admin.requests.logs') ? 'bg-emerald-700 text-white border-l-4 border-emerald-400 shadow-md' : 'text-emerald-100 hover:bg-emerald-700/70 hover:text-white' }}">
            <span class="mr-3 text-white">
                <i class="fas fa-history text-base"></i>
            </span>
            <span class="text-[13px] font-bold tracking-tight">Request History</span>
        </a>

        {{-- Announcements --}}
        <a href="{{ route('admin.announcements.index') }}" 
            class="flex items-center p-3 rounded-xl transition-all group {{ request()->routeIs('admin.announcements.*') ? 'bg-emerald-700 text-white border-l-4 border-emerald-400 shadow-md' : 'text-emerald-100 hover:bg-emerald-700/70 hover:text-white' }}">
            <span class="mr-3 text-white">
                <i class="fas fa-bullhorn text-base"></i>
            </span>
            <span class="text-[13px] font-bold tracking-tight">Announcements</span>
        </a>

        {{-- ==================== SYSTEM ==================== --}}
        <div class="px-2 pt-4 mb-2">
            <h2 class="text-[10px] font-black text-emerald-300/70 uppercase tracking-[0.2em]">System</h2>
        </div>

        {{-- Staff Accounts --}}
        <a href="{{ route('admin.users.index') }}" 
            class="flex items-center p-3 rounded-xl transition-all group {{ request()->routeIs('admin.users.*') ? 'bg-emerald-700 text-white border-l-4 border-emerald-400 shadow-md' : 'text-emerald-100 hover:bg-emerald-700/70 hover:text-white' }}">
            <span class="mr-3 text-white">
                <i class="fas fa-user-tie text-base"></i>
            </span>
            <span class="text-[13px] font-bold tracking-tight">Staff Accounts</span>
        </a>

        {{-- Password Reset Requests --}}
        <a href="{{ route('admin.password-resets.index') }}" 
            class="flex items-center justify-between p-3 rounded-xl transition-all group {{ request()->routeIs('admin.password-resets.*') ? 'bg-emerald-700 text-white border-l-4 border-emerald-400 shadow-md' : 'text-emerald-100 hover:bg-emerald-700/70 hover:text-white' }}">
            <div class="flex items-center">
                <span class="mr-3 text-white">
                    <i class="fas fa-key text-base"></i>
                </span>
                <span class="text-[13px] font-bold tracking-tight">Password Resets</span>
            </div>
            @php
                $pendingPasswordResets = \App\Models\PasswordResetRequest::where('status', 'pending')->count();
            @endphp
            @if($pendingPasswordResets > 0)
                <span class="bg-amber-400 text-emerald-950 text-[10px] font-black px-2 py-0.5 rounded-full shadow-sm animate-pulse">
                    {{ $pendingPasswordResets }}
                </span>
            @endif
        </a>

    </nav>

    {{-- Logout --}}
    <div class="p-4 mt-auto border-t border-emerald-700">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center p-3 text-emerald-200 hover:text-white hover:bg-red-700/80 rounded-xl transition-all group">
                <span class="mr-3 transition-colors text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </span>
                <span class="text-[13px] font-bold tracking-tight">Logout</span>
            </button>
        </form>
    </div>
</div>

<style>
    .custom-scroll::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scroll::-webkit-scrollbar-track {
        background: #065f46;
        border-radius: 10px;
    }
    .custom-scroll::-webkit-scrollbar-thumb {
        background: #34d399;
        border-radius: 10px;
    }
    .custom-scroll::-webkit-scrollbar-thumb:hover {
        background: #6ee7b7;
    }
</style>