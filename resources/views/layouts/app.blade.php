<!DOCTYPE html>
<html lang="en" x-data="{ sidebarOpen: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BIMS | Bactad East</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Font Awesome 6 for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: {
                        sidebar: '#FFFFFF',
                        background: '#F9FAFB',
                        accent: '#6366F1'
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-thumb { background: #E2E8F0; border-radius: 10px; }
        body { background-color: #F9FAFB; }
        .custom-scroll { scrollbar-gutter: stable; }

        /* ============================================================
           DOCUMENT CARD CHECKMARK FIX (Using :has() selector)
        ============================================================ */
        label:has(.doc-radio:checked) .doc-card {
            border-color: #10b981 !important;
            background-color: #ecfdf5 !important;
            box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.25) !important;
        }
        label:has(.doc-radio:checked) .doc-card .checkmark-bg {
            background-color: #10b981 !important;
            border-color: #10b981 !important;
        }
        label:has(.doc-radio:checked) .doc-card .checkmark-icon {
            opacity: 1 !important;
        }
        label:has(.doc-radio:checked) .doc-card .doc-icon {
            transform: scale(1.1) rotate(3deg);
        }
        .doc-card, .checkmark-bg, .checkmark-icon, .doc-icon {
            transition: all 0.2s ease;
        }
        .doc-radio:checked ~ .doc-card {
            border-color: #10b981 !important;
            background-color: #ecfdf5 !important;
        }
        .doc-radio:checked ~ .doc-card .checkmark-bg {
            background-color: #10b981 !important;
            border-color: #10b981 !important;
        }
        .doc-radio:checked ~ .doc-card .checkmark-icon {
            opacity: 1 !important;
        }
    </style>
</head>
<body class="font-sans antialiased text-slate-900 overflow-hidden bg-[#F9FAFB]">

    {{-- ============================================================
         LAYOUT WRAPPER
    ============================================================ --}}
    <div class="h-screen flex overflow-hidden">

        {{-- ============================================================
             DESKTOP SIDEBAR (Fixed sa PC, Hidden sa Mobile)
        ============================================================ --}}
        <aside class="w-[280px] bg-white border-r border-slate-100 hidden lg:flex flex-col flex-shrink-0 relative overflow-hidden">
            @if(Auth::check() && Auth::user()->role === 'admin')
                @include('layouts.sidebar')
            @else
                @include('layouts.resident_sidebar')
            @endif
        </aside>

        {{-- ============================================================
             MOBILE SIDEBAR DRAWER (Off-canvas)
        ============================================================ --}}
        {{-- Backdrop --}}
        <div x-show="sidebarOpen" 
             x-cloak
             x-transition:enter="transition-opacity ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm lg:hidden">
        </div>

        {{-- Mobile Sidebar (Slide-in mula kaliwa) --}}
        <aside x-show="sidebarOpen"
               x-cloak
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in duration-200"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="-translate-x-full"
               class="fixed inset-y-0 left-0 z-50 w-[280px] bg-white shadow-2xl lg:hidden">
            {{-- Close Button --}}
            <button @click="sidebarOpen = false" 
                    class="absolute top-3 right-3 h-8 w-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-700 transition z-10">
                <i class="fas fa-times text-xs"></i>
            </button>

            @if(Auth::check() && Auth::user()->role === 'admin')
                @include('layouts.sidebar')
            @else
                @include('layouts.resident_sidebar')
            @endif
        </aside>

        {{-- ============================================================
             MAIN CONTENT AREA
        ============================================================ --}}
        <main class="flex-1 flex flex-col min-w-0 bg-background overflow-hidden relative text-left">

            {{-- ===== TOPBAR (Responsive) ===== --}}
            <header class="h-16 bg-white/40 backdrop-blur-md flex items-center justify-between px-4 sm:px-6 lg:px-10 border-b border-slate-100/50 flex-shrink-0">

                {{-- LEFT: Hamburger (Mobile) + Breadcrumb (Desktop) --}}
                <div class="flex items-center gap-3">
                    {{-- Hamburger Button - Mobile Only --}}
                    <button @click="sidebarOpen = true"
                            class="lg:hidden h-9 w-9 rounded-xl bg-white hover:bg-emerald-50 border border-slate-200 flex items-center justify-center text-slate-600 hover:text-emerald-600 transition-all hover:scale-105 active:scale-95 shadow-sm">
                        <i class="fas fa-bars text-sm"></i>
                    </button>

                    {{-- Breadcrumb - Desktop Only --}}
                    <div class="hidden lg:block text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">
                        BIMS · Bactad East
                    </div>
                </div>

                {{-- RIGHT: User Info --}}
                <div class="flex items-center gap-3">
                    @php
                        $authUser = Auth::user();
                        $photoUrl = null;
                        if ($authUser) {
                            try {
                                $photoUrl = $authUser->profile_photo_url ?? null;
                            } catch (\Exception $e) {
                                $photoUrl = null;
                            }
                        }
                    @endphp

                    {{-- User Name (Mobile: hidden, Desktop: shown) --}}
                    <div class="hidden sm:block text-right">
                        <p class="text-[11px] font-extrabold text-slate-700 uppercase tracking-wide">
                            {{ Auth::user()->first_name ?? Auth::user()->name }}
                        </p>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest leading-none">
                            {{ Auth::user()->role === 'admin' ? 'SYSTEM ADMIN' : 'RESIDENT' }}
                        </p>
                    </div>

                    {{-- Avatar --}}
                    <div class="h-9 w-9 rounded-full overflow-hidden flex-shrink-0 ring-2 ring-emerald-500/30 shadow-md bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center">
                        @if($photoUrl)
                            <img src="{{ $photoUrl }}" 
                                 alt="{{ $authUser->first_name ?? 'User' }}" 
                                 class="h-full w-full object-cover">
                        @else
                            <span class="text-white text-xs font-black">
                                {{ strtoupper(substr($authUser->first_name ?? $authUser->name ?? 'U', 0, 1)) }}{{ strtoupper(substr($authUser->last_name ?? '', 0, 1)) }}
                            </span>
                        @endif
                    </div>
                </div>
            </header>

            {{-- ===== CONTENT ===== --}}
            <div class="flex-1 overflow-y-auto pt-4 sm:pt-6 lg:pt-8 px-4 sm:px-6 lg:px-10 custom-scroll">
                <div class="w-full max-w-7xl mx-auto">

                    {{-- Success Message --}}
                    @if(session('success'))
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                             class="mb-6 p-4 bg-white rounded-2xl border border-emerald-100 shadow-sm flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-500 flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                </div>
                                <span class="font-bold text-sm text-slate-700">{{ session('success') }}</span>
                            </div>
                            <button @click="show = false" class="text-slate-300 hover:text-slate-600 px-2">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @endif

                    {{-- Error Message --}}
                    @if(session('error'))
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                             class="mb-6 p-4 bg-white rounded-2xl border border-rose-100 shadow-sm flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-500 flex-shrink-0">
                                    <i class="fas fa-exclamation-circle"></i>
                                </div>
                                <span class="font-bold text-sm text-slate-700">{{ session('error') }}</span>
                            </div>
                            <button @click="show = false" class="text-slate-300 hover:text-slate-600 px-2">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @endif

                    {{-- Content Slot --}}
                    <div class="animate-in fade-in slide-in-from-bottom-2 duration-700">
                        @yield('content')
                    </div>
                    
                    {{-- Footer --}}
                    <footer class="mt-16 sm:mt-20 pb-8 sm:pb-10 opacity-30">
                        <div class="flex flex-col sm:flex-row justify-between items-center gap-2">
                            <p class="text-[10px] font-black uppercase tracking-[0.4em] text-slate-400">
                                BIMS Bactad East &copy; {{ date('Y') }}
                            </p>
                            <div class="hidden sm:block h-px bg-slate-200 flex-1 mx-8"></div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                Barangay Information Management System
                            </p>
                        </div>
                    </footer>
                </div>
            </div>
        </main>
    </div>

</body>
</html>