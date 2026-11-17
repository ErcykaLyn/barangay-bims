<div class="w-72 bg-gradient-to-b from-emerald-800 via-emerald-850 to-emerald-950 text-white flex flex-col shadow-2xl relative overflow-hidden">

    <div class="absolute inset-0 opacity-[0.03] pointer-events-none">
        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
            <defs>
                <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                    <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/>
                </pattern>
            </defs>
            <rect width="100" height="100" fill="url(#grid)"/>
        </svg>
    </div>

    <div class="absolute -top-20 -right-20 w-56 h-56 bg-emerald-400/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative px-6 py-7 border-b border-emerald-700/40">
        <div class="flex items-center gap-3">
            <div class="relative">
                <div class="h-12 w-12 rounded-full border-2 border-emerald-400/60 shadow-lg bg-white/10 backdrop-blur flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('images/brgy_logo.png.jpg') }}" alt="Logo" class="h-full w-full object-cover">
                </div>
                <span class="absolute -bottom-1 -right-1 h-4 w-4 rounded-full bg-emerald-400 border-2 border-emerald-900 shadow"></span>
            </div>
            <div>
                <h1 class="text-[13px] font-black tracking-tight leading-tight">
                    Barangay Information<br>
                    Management <span class="text-emerald-300">Bactad East</span>
                </h1>
                <div class="mt-1 inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-emerald-400/15 border border-emerald-400/30">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-[9px] text-emerald-200 font-black uppercase tracking-widest">BHW Panel</span>
                </div>
            </div>
        </div>
    </div>

    <nav class="relative flex-1 px-4 py-6 space-y-1.5 overflow-y-auto custom-scroll">

        <p class="px-4 pt-1 pb-2 text-[10px] font-black text-emerald-400/70 uppercase tracking-[0.2em]">Main Menu</p>

        <a href="{{ route('bhw.dashboard') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group relative
           {{ request()->routeIs('bhw.dashboard') 
                ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-lg shadow-emerald-900/40' 
                : 'text-emerald-100 hover:bg-emerald-700/50 hover:text-white hover:translate-x-1' }}">
            @if(request()->routeIs('bhw.dashboard'))
                <span class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1 bg-emerald-300 rounded-r-full"></span>
            @endif
            <span class="text-lg w-5 text-center"><i class="fas fa-tachometer-alt"></i></span>
            <span class="text-[13px] font-bold tracking-tight">Dashboard</span>
        </a>

        <a href="{{ route('bhw.residents.create') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group relative
           {{ request()->routeIs('bhw.residents.create') 
                ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-lg shadow-emerald-900/40' 
                : 'text-emerald-100 hover:bg-emerald-700/50 hover:text-white hover:translate-x-1' }}">
            @if(request()->routeIs('bhw.residents.create'))
                <span class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1 bg-emerald-300 rounded-r-full"></span>
            @endif
            <span class="text-lg w-5 text-center"><i class="fas fa-user-plus"></i></span>
            <span class="text-[13px] font-bold tracking-tight">Add Resident</span>
        </a>

        <a href="{{ route('bhw.residents.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group relative
           {{ request()->routeIs('bhw.residents.index') 
                ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-lg shadow-emerald-900/40' 
                : 'text-emerald-100 hover:bg-emerald-700/50 hover:text-white hover:translate-x-1' }}">
            @if(request()->routeIs('bhw.residents.index'))
                <span class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1 bg-emerald-300 rounded-r-full"></span>
            @endif
            <span class="text-lg w-5 text-center"><i class="fas fa-users"></i></span>
            <span class="text-[13px] font-bold tracking-tight">Residents List</span>
        </a>

        <div class="pt-4 pb-2">
            <p class="px-4 text-[10px] font-black text-emerald-400/70 uppercase tracking-[0.2em]">Classifications</p>
        </div>

        <a href="{{ route('bhw.family_profiling.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group relative
           {{ request()->routeIs('bhw.family_profiling.*') 
                ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-lg shadow-emerald-900/40' 
                : 'text-emerald-100 hover:bg-emerald-700/50 hover:text-white hover:translate-x-1' }}">
            @if(request()->routeIs('bhw.family_profiling.*'))
                <span class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1 bg-emerald-300 rounded-r-full"></span>
            @endif
            <span class="text-lg w-5 text-center"><i class="fas fa-home"></i></span>
            <span class="text-[13px] font-bold tracking-tight">Family Profiles</span>
        </a>

        <a href="{{ route('bhw.seniors.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group relative
           {{ request()->routeIs('bhw.seniors.index') 
                ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-lg shadow-emerald-900/40' 
                : 'text-emerald-100 hover:bg-emerald-700/50 hover:text-white hover:translate-x-1' }}">
            @if(request()->routeIs('bhw.seniors.index'))
                <span class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1 bg-emerald-300 rounded-r-full"></span>
            @endif
            <span class="text-lg w-5 text-center"><i class="fas fa-user-clock"></i></span>
            <span class="text-[13px] font-bold tracking-tight">Senior Citizens</span>
        </a>

        <a href="{{ route('bhw.pwds.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group relative
           {{ request()->routeIs('bhw.pwds.index') 
                ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-lg shadow-emerald-900/40' 
                : 'text-emerald-100 hover:bg-emerald-700/50 hover:text-white hover:translate-x-1' }}">
            @if(request()->routeIs('bhw.pwds.index'))
                <span class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1 bg-emerald-300 rounded-r-full"></span>
            @endif
            <span class="text-lg w-5 text-center"><i class="fas fa-wheelchair"></i></span>
            <span class="text-[13px] font-bold tracking-tight">PWDs</span>
        </a>

        <a href="{{ route('bhw.4ps.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group relative
           {{ request()->routeIs('bhw.4ps.index') 
                ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-lg shadow-emerald-900/40' 
                : 'text-emerald-100 hover:bg-emerald-700/50 hover:text-white hover:translate-x-1' }}">
            @if(request()->routeIs('bhw.4ps.index'))
                <span class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1 bg-emerald-300 rounded-r-full"></span>
            @endif
            <span class="text-lg w-5 text-center"><i class="fas fa-hand-holding-heart"></i></span>
            <span class="text-[13px] font-bold tracking-tight">4Ps Beneficiaries</span>
        </a>

        <a href="{{ route('bhw.residents.deceased') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group relative
           {{ request()->routeIs('bhw.residents.deceased') 
                ? 'bg-gradient-to-r from-slate-600 to-slate-800 text-white shadow-lg shadow-slate-900/40' 
                : 'text-emerald-100 hover:bg-slate-700/50 hover:text-white hover:translate-x-1' }}">
            @if(request()->routeIs('bhw.residents.deceased'))
                <span class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1 bg-slate-300 rounded-r-full"></span>
            @endif
            <span class="text-lg w-5 text-center"><i class="fas fa-cross"></i></span>
            <span class="text-[13px] font-bold tracking-tight">Deceased</span>
        </a>
    </nav>

    <div class="relative p-4 border-t border-emerald-700/40 space-y-3">

        <div class="flex items-center gap-3 px-3 py-3 rounded-xl bg-emerald-950/40 border border-emerald-700/30">
            <div class="h-9 w-9 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white text-xs font-black shadow-inner flex-shrink-0">
                {{ strtoupper(substr(Auth::user()->name ?? 'BHW', 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-[11px] font-black text-white truncate">{{ Auth::user()->name ?? 'BHW User' }}</p>
                <p class="text-[9px] text-emerald-300 uppercase tracking-widest font-bold">Barangay Health Worker</p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" 
                class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-red-200 bg-red-900/20 border border-red-500/30 hover:bg-red-600 hover:text-white hover:border-red-500 transition-all duration-200 group">
                <i class="fas fa-sign-out-alt text-sm group-hover:rotate-180 transition-transform duration-300"></i>
                <span class="text-[12px] font-black tracking-wider uppercase">Logout</span>
            </button>
        </form>
    </div>
</div>

<style>
    .custom-scroll::-webkit-scrollbar {
        width: 5px;
    }
    .custom-scroll::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scroll::-webkit-scrollbar-thumb {
        background: #34d399;
        border-radius: 10px;
        opacity: 0.5;
    }
    .custom-scroll::-webkit-scrollbar-thumb:hover {
        background: #6ee7b7;
    }
    .custom-scroll {
        scrollbar-width: thin;
        scrollbar-color: #34d399 transparent;
    }
</style>