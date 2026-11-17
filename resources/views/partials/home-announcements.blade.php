@php
    try {
        $homeAnns = \App\Models\Announcement::published()->newestFirst()->take(3)->get();
    } catch (\Throwable $e) {
        $homeAnns = collect();
    }
@endphp

@if ($homeAnns->isNotEmpty())
<section id="announcements" class="py-12 sm:py-16 md:py-24">
  <div class="max-w-7xl mx-auto px-3 sm:px-6">
    <div class="flex flex-col md:flex-row justify-between items-center md:items-end mb-8 sm:mb-12 gap-4">
      <div class="text-center md:text-left">
        <span class="section-tag">Balita at Abiso</span>
        <h2 class="text-2xl sm:text-3xl md:text-5xl font-black text-slate-900 tracking-tight">Pinakabagong <span class="text-emerald-600">Anunsyo</span></h2>
        <p class="text-slate-500 text-xs sm:text-sm md:text-base mt-2 sm:mt-4 max-w-xl">Manatiling updated sa mga abiso at gawain ng Barangay Bactad East.</p>
      </div>
      <a href="{{ route('public.announcements.index') }}" class="group inline-flex items-center gap-2 rounded-2xl border border-emerald-200 bg-white px-5 py-3 text-xs sm:text-sm font-bold text-emerald-700 shadow-sm transition hover:bg-emerald-50">
        Tingnan lahat <i class="fas fa-arrow-right text-xs transition-transform group-hover:translate-x-1"></i>
      </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
      @foreach ($homeAnns as $a)
        @include('partials.announcement-card', ['a' => $a])
      @endforeach
    </div>
  </div>
</section>
@endif