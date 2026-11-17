@php
    // Ang landing page natin ay nasa '/home', hindi '/'.
    // Kaya kapag nasa '/home' tayo, '#' lang ang prefix. Kapag nasa ibang page, '/home#'.
    $onLanding = request()->is('home');
    $h = $onLanding ? '' : url('/home');

    try {
        $hasNewAnn = \App\Models\Announcement::published()
            ->whereRaw('COALESCE(published_date, DATE(created_at)) >= ?', [now()->subDays(7)->toDateString()])
            ->exists();
    } catch (\Throwable $e) {
        $hasNewAnn = false;
    }

    $navItems = [
        ['label' => 'Services',      'href' => $h . '#services'],
        ['label' => 'About',         'href' => $h . '#about'],
        ['label' => 'Announcements', 'href' => route('public.announcements.index'), 'active' => request()->routeIs('public.announcements.*'), 'new' => true],
        // BAGONG DAGDAG: Ordinances at Hotlines
        ['label' => 'Ordinances',    'href' => route('public.ordinances'), 'active' => request()->routeIs('public.ordinances')],
        ['label' => 'Hotlines',      'href' => route('public.hotlines'), 'active' => request()->routeIs('public.hotlines')],
        ['label' => 'Officials',     'href' => $h . '#officials'],
        ['label' => 'History',       'href' => $h . '#history'],
        ['label' => 'FAQ',           'href' => $h . '#faq'],
    ];
@endphp

<nav class="fixed top-0 w-full z-[100] bg-white/90 backdrop-blur-lg border-b border-emerald-50">
  <div class="nav-row max-w-7xl mx-auto px-3 sm:px-6 h-16 md:h-20 flex justify-between items-center">
    <a href="{{ url('/home') }}" class="flex items-center gap-2 sm:gap-3" aria-label="BIMS Home">
      <div class="w-9 h-9 sm:w-10 sm:h-10 md:w-12 md:h-12 rounded-full border-2 border-emerald-500 p-0.5 sm:p-1 bg-white shadow-sm"><img src="{{ asset('images/brgy_logo.png.jpg') }}" alt="Barangay Logo" class="w-full h-full object-contain"></div>
      <div>
        <h1 class="text-sm sm:text-base md:text-xl font-black text-slate-900 leading-none">BIMS</h1>
        <p class="text-[7px] sm:text-[8px] md:text-[10px] font-bold text-emerald-600 tracking-[0.15em] sm:tracking-[0.2em] uppercase">Bactad East</p>
      </div>
    </a>

    <div class="hidden lg:flex items-center space-x-8">
      @foreach ($navItems as $it)
        <a href="{{ $it['href'] }}" class="nav-link inline-flex items-center gap-1.5 text-sm font-bold text-slate-600 hover:text-emerald-600 transition {{ !empty($it['active']) ? 'is-active' : '' }}">
          {{ strtoupper($it['label']) }}
          @if (!empty($it['new']) && $hasNewAnn)
            <span class="rounded-full bg-rose-500 px-1.5 py-0.5 text-[8px] font-black leading-none text-white">NEW</span>
          @endif
        </a>
      @endforeach
    </div>

    <a href="/login" class="bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white px-4 py-2 sm:px-6 sm:py-2.5 rounded-xl font-black text-xs sm:text-sm shadow-lg shadow-emerald-500/30 hover:shadow-xl transition-all active:scale-95 flex items-center gap-1.5 sm:gap-2"><i class="fas fa-sign-in-alt text-[10px] sm:text-xs"></i> LOGIN</a>
  </div>

  <div class="lg:hidden mobile-nav-scroll border-t border-emerald-50 bg-white/60 backdrop-blur px-3 py-2">
    <div class="flex items-center gap-2 whitespace-nowrap">
      @foreach ($navItems as $it)
        <a href="{{ $it['href'] }}" class="text-[10px] sm:text-xs font-bold px-3 py-1.5 rounded-lg border transition {{ !empty($it['active']) ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'text-slate-600 bg-slate-50 hover:bg-emerald-50 hover:text-emerald-600 border-slate-100' }}">
          {{ $it['label'] }}
          @if (!empty($it['new']) && $hasNewAnn)<span class="ml-1 inline-block h-1.5 w-1.5 rounded-full bg-rose-500 align-middle"></span>@endif
        </a>
      @endforeach
    </div>
  </div>
</nav>