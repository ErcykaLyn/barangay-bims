<div class="h-full flex flex-col bg-emerald-800 border-r border-emerald-700">
    <!-- Branding with round logo (white border) -->
    <div class="px-6 py-8 flex items-center gap-3">
        <div class="h-10 w-10 rounded-full border-2 border-white shadow-md flex-shrink-0 flex items-center justify-center bg-white/10 p-0.5">
            <img src="{{ asset('images/brgy_logo.png.jpg') }}" 
                 alt="Barangay Logo" 
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

    <!-- Main Menu header (white) -->
    <div class="px-6 mb-4">
        <h2 class="text-[10px] font-black text-white uppercase tracking-[0.2em]">Main Menu</h2>
    </div>

    <nav class="flex-1 px-4 space-y-1.5 overflow-y-auto custom-scroll">
        <!-- Dashboard -->
        <a href="{{ route('resident.dashboard') }}" 
            class="flex items-center p-3.5 rounded-xl transition-all group {{ request()->routeIs('resident.dashboard') ? 'bg-emerald-700 text-white border-l-4 border-emerald-400 shadow-md' : 'text-emerald-100 hover:bg-emerald-700/70 hover:text-white' }}">
            <span class="mr-3.5 text-xl text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
            </span>
            <span class="text-[13px] font-bold tracking-tight">Dashboard</span>
        </a>

        <!-- Request Documents -->
        <a href="{{ route('resident.requests.form') }}" 
            class="flex items-center p-3.5 rounded-xl transition-all group {{ request()->routeIs('resident.requests.form') ? 'bg-emerald-700 text-white border-l-4 border-emerald-400 shadow-md' : 'text-emerald-100 hover:bg-emerald-700/70 hover:text-white' }}">
            <span class="mr-3.5 text-xl text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </span>
            <span class="text-[13px] font-bold tracking-tight">Request Documents</span>
        </a>

        <!-- Track Status -->
        <a href="{{ route('resident.requests.status') }}" 
            class="flex items-center justify-between p-3.5 rounded-xl transition-all group {{ request()->routeIs('resident.requests.status') ? 'bg-emerald-700 text-white border-l-4 border-emerald-400 shadow-md' : 'text-emerald-100 hover:bg-emerald-700/70 hover:text-white' }}">
            <div class="flex items-center">
                <span class="mr-3.5 text-xl text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </span>
                <span class="text-[13px] font-bold tracking-tight">Track Status</span>
            </div>
            @if($requestCount > 0)
                <span class="bg-emerald-400 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $requestCount }}</span>
            @endif
        </a>

        <!-- Barangay Equipment -->
        <a href="{{ route('resident.equipment.index') }}" 
            class="flex items-center justify-between p-3.5 rounded-xl transition-all group {{ request()->routeIs('resident.equipment.*') ? 'bg-emerald-700 text-white border-l-4 border-emerald-400 shadow-md' : 'text-emerald-100 hover:bg-emerald-700/70 hover:text-white' }}">
            <div class="flex items-center">
                <span class="mr-3.5 text-xl text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                    </svg>
                </span>
                <span class="text-[13px] font-bold tracking-tight">Barangay Equipment</span>
            </div>
            @php
                $approvedPickup = Auth::guard('resident')->user()->approved_pickup_count ?? 0;
            @endphp
            @if($approvedPickup > 0)
                <span class="bg-blue-500 text-white text-xs font-bold px-2 py-0.5 rounded-full animate-pulse">
                    {{ $approvedPickup }}
                </span>
            @endif
        </a>

        <!-- INALIS NA ANG ORDINANCES AT HOTLINES DITO DAHIL NASA PUBLIC NA SILA -->

        <!-- Divider for Account section -->
        <div class="pt-4 mt-2 border-t border-emerald-700/50"></div>
        <p class="px-3 text-[10px] font-black text-white uppercase tracking-[0.2em] mt-2">Account</p>

        <!-- My Profile -->
        <a href="{{ route('resident.profile') }}" 
            class="flex items-center p-3.5 rounded-xl transition-all group {{ request()->routeIs('resident.profile') ? 'bg-emerald-700 text-white border-l-4 border-emerald-400 shadow-md' : 'text-emerald-100 hover:bg-emerald-700/70 hover:text-white' }}">
            <span class="mr-3.5 text-xl text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                </svg>
            </span>
            <span class="text-[13px] font-bold tracking-tight">My Profile</span>
        </a>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}" class="mt-2">
            @csrf
            <button type="submit" class="w-full flex items-center p-3.5 rounded-xl text-emerald-200 hover:text-white hover:bg-red-700/80 transition-all group">
                <span class="mr-3.5 transition-colors text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </span>
                <span class="text-[13px] font-bold tracking-tight">Logout</span>
            </button>
        </form>
    </nav>
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