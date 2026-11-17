@php
    // Ang landing page natin ay nasa '/home', hindi '/'.
    // Kaya kapag nasa '/home' tayo, '#' lang ang prefix. Kapag nasa ibang page, '/home#'.
    $h = request()->is('home') ? '' : url('/home');

    try {
        $footerAnns = \App\Models\Announcement::published()->newestFirst()->take(3)->get();
    } catch (\Throwable $e) {
        $footerAnns = collect();
    }
@endphp

<footer class="bg-slate-900 text-white py-8 sm:py-12 md:py-16">
  <div class="max-w-7xl mx-auto px-3 sm:px-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8 md:gap-12">
      <div>
        <div class="flex items-center gap-3 mb-3 sm:mb-4">
          <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full border-2 border-emerald-500 p-1 bg-white"><img src="{{ asset('images/brgy_logo.png.jpg') }}" alt="Barangay Logo" class="w-full h-full object-contain"></div>
          <div><h3 class="text-base sm:text-lg font-black">BIMS</h3><p class="text-[7px] sm:text-[8px] font-bold text-emerald-400 tracking-[0.2em] uppercase">Bactad East Portal</p></div>
        </div>
        <p class="text-slate-400 text-xs sm:text-sm">Barangay Integrated Management System para sa mas maayos at mabilis na serbisyo publiko.</p>
      </div>

      <div>
        <h4 class="font-bold text-base sm:text-lg mb-3 sm:mb-4">Quick Links</h4>
        <ul class="space-y-1.5 sm:space-y-2 text-slate-400 text-xs sm:text-sm">
          <li><a href="{{ $h }}#services" class="hover:text-emerald-400 transition">Services</a></li>
          <li><a href="{{ $h }}#about" class="hover:text-emerald-400 transition">About</a></li>
          <li><a href="{{ route('public.announcements.index') }}" class="hover:text-emerald-400 transition">Announcements</a></li>
          <li><a href="{{ $h }}#officials" class="hover:text-emerald-400 transition">Officials</a></li>
          <li><a href="{{ $h }}#history" class="hover:text-emerald-400 transition">History</a></li>
          <li><a href="{{ $h }}#faq" class="hover:text-emerald-400 transition">FAQ</a></li>
          <li><a href="/login" class="hover:text-emerald-400 transition">Login</a></li>
        </ul>
      </div>

      <div>
        <h4 class="font-bold text-base sm:text-lg mb-3 sm:mb-4">Mga Anunsyo</h4>
        @if ($footerAnns->isNotEmpty())
          <ul class="space-y-3 text-xs sm:text-sm">
            @foreach ($footerAnns as $fa)
              <li>
                <a href="{{ $fa->url }}" class="group block">
                  <span class="block text-[10px] font-bold uppercase tracking-widest text-emerald-400">{{ $fa->display_date->format('M d, Y') }}</span>
                  <span class="block text-slate-300 group-hover:text-emerald-400 transition line-clamp-2">{{ $fa->title }}</span>
                </a>
              </li>
            @endforeach
          </ul>
          <a href="{{ route('public.announcements.index') }}" class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-emerald-400 hover:text-emerald-300 transition">Tingnan lahat <i class="fas fa-arrow-right text-[10px]"></i></a>
        @else
          <p class="text-slate-500 text-xs sm:text-sm">Wala pang anunsyo sa ngayon.</p>
        @endif
      </div>

      <div>
        <h4 class="font-bold text-base sm:text-lg mb-3 sm:mb-4">Contact</h4>
        <ul class="space-y-2 sm:space-y-3 text-slate-400 text-xs sm:text-sm">
          <li class="flex items-start gap-2 sm:gap-3"><i class="fas fa-map-marker-alt text-emerald-500 mt-1 text-xs"></i><span>Barangay Hall, Bactad East, Urdaneta City, Pangasinan 2428</span></li>
          <li class="flex items-center gap-2 sm:gap-3"><i class="fas fa-envelope text-emerald-500 text-xs"></i><span>bactadeast@urdaneta.gov.ph</span></li>
          <li class="flex items-center gap-2 sm:gap-3"><i class="fas fa-clock text-emerald-500 text-xs"></i><span>Mon-Fri: 8:00 AM - 5:00 PM</span></li>
        </ul>
      </div>
    </div>
    <div class="border-t border-slate-800 mt-6 sm:mt-10 pt-4 sm:pt-8 text-center text-slate-500 text-[10px] sm:text-xs">
      <p>&copy; {{ date('Y') }} Barangay Bactad East, Urdaneta City, Pangasinan. All rights reserved.</p>
      <p class="mt-1">Powered by PSU-Asingan BSIT</p>
    </div>
  </div>
</footer>