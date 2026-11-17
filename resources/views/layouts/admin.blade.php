<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>BIMS | Admin Management – Barangay Bactad East</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    {{-- Updated Font Awesome (6.5.1) so newer icons like fa-pen-to-square, fa-xmark, fa-floppy-disk render --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: { 
                        brand: '#059669',
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        body { background-color: #ffffff; }
        .custom-scroll::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scroll::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .sidebar-link { transition: all 0.2s ease-in-out; }
    </style>

    {{-- Optional: pages can push extra styles with @push('styles') --}}
    @stack('styles')
</head>
<body class="font-sans antialiased text-slate-900" x-data="{ sidebarOpen: false }">

    <div class="h-screen flex overflow-hidden"> 
        
        <!-- ============ DESKTOP SIDEBAR ============ -->
        <aside class="w-[280px] hidden lg:flex flex-col shrink-0">
            @include('layouts.sidebar')
        </aside>

        <!-- ============ MOBILE SIDEBAR OVERLAY ============ -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-black/60 z-40 lg:hidden"
             x-cloak></div>

        <!-- Mobile Sidebar Drawer -->
        <aside x-show="sidebarOpen"
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in duration-200"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="-translate-x-full"
               class="fixed inset-y-0 left-0 w-[280px] z-50 lg:hidden overflow-y-auto custom-scroll"
               x-cloak>
            @include('layouts.sidebar')
        </aside>

        <!-- ============ MAIN CONTENT AREA ============ -->
        <main class="flex-1 flex flex-col min-w-0 bg-slate-100">
            
            <!-- ============ HEADER ============ -->
            <header class="h-14 sm:h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 lg:px-8 shrink-0 sticky top-0 z-30 shadow-sm">
                
                <!-- Left: Mobile Menu + Page Context -->
                <div class="flex items-center gap-3 min-w-0">
                    <!-- Hamburger (mobile only) -->
                    <button @click="sidebarOpen = true" 
                        class="lg:hidden h-9 w-9 flex items-center justify-center rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors flex-shrink-0">
                        <i class="fas fa-bars text-sm"></i>
                    </button>
                    
                    <!-- Page context -->
                    <div class="min-w-0">
                        <p class="text-[9px] font-black text-emerald-600 uppercase tracking-widest leading-none">Admin Panel</p>
                        <p class="text-xs font-bold text-slate-700 leading-tight mt-0.5">Barangay Bactad East</p>
                    </div>
                </div>
                
                <!-- Right: Avatar + Name + Role -->
                <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
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

                    <!-- User info (right aligned) -->
                    <div class="text-right hidden sm:block">
                        <p class="text-[11px] font-black text-slate-800 tracking-tight uppercase leading-tight">
                            {{ $authUser->first_name ?? $authUser->name ?? 'Admin' }}
                            {{ $authUser->last_name ?? '' }}
                        </p>
                        <div class="flex items-center justify-end gap-1 mt-0.5">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <p class="text-[9px] font-bold text-emerald-600 uppercase tracking-wider leading-none">
                                System Admin
                            </p>
                        </div>
                    </div>

                    <!-- Avatar -->
                    <div class="h-9 w-9 rounded-lg overflow-hidden flex-shrink-0 ring-2 ring-emerald-500/30 shadow-sm bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center">
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

            <!-- ============ PAGE CONTENT ============ -->
            <div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 custom-scroll">
                <div class="max-w-7xl mx-auto">

                    {{-- ✅ Session messages ay inalis dito para hindi mag-duplicate --}}
                    {{-- Nasa child blade (@section('content')) na ang notifications --}}

                    @yield('content')
                    
                    <footer class="mt-8 py-6 border-t border-slate-200 flex flex-col sm:flex-row justify-between items-center gap-3">
                        <p class="text-[9px] font-black uppercase tracking-[0.3em] text-slate-400 text-center sm:text-left">BIMS &copy; 2026</p>
                        <span class="text-[9px] font-bold text-slate-300 uppercase tracking-widest italic">Barangay Bactad East</span>
                    </footer>
                </div>
            </div>
        </main>
    </div>

    {{-- ============ GLOBAL CONFIRM MODAL ============ --}}
    {{-- Any <form data-confirm="..."> in the admin panel will use this instead of the browser's confirm() --}}
    @include('partials.confirm-modal')

    {{-- Optional: pages can push extra scripts with @push('scripts') --}}
    @stack('scripts')

</body>
</html>