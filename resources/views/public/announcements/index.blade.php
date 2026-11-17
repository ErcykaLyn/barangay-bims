@extends('layouts.public')

@section('title', 'Mga Anunsyo | BIMS Barangay Bactad East')
@section('meta_description', 'Pinakabagong anunsyo, abiso, at balita mula sa Barangay Bactad East, Urdaneta City, Pangasinan.')

@section('content')

{{-- ============ HERO ============ --}}
<section class="relative overflow-hidden bg-gradient-to-br from-emerald-700 via-emerald-800 to-teal-900">
  <div class="absolute inset-0 opacity-[0.07]">
    <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
      <defs><pattern id="annGrid" width="8" height="8" patternUnits="userSpaceOnUse"><path d="M 8 0 L 0 0 0 8" fill="none" stroke="white" stroke-width="0.5"/></pattern></defs>
      <rect width="100" height="100" fill="url(#annGrid)"/>
    </svg>
  </div>
  <div class="absolute -top-24 -right-16 w-72 h-72 bg-emerald-300/25 rounded-full blur-3xl"></div>
  <div class="absolute -bottom-28 -left-10 w-80 h-80 bg-teal-300/20 rounded-full blur-3xl"></div>

  <div class="relative max-w-7xl mx-auto px-3 sm:px-6 py-10 sm:py-14">
    <nav class="flex items-center gap-1.5 text-xs text-emerald-200/80 mb-4" aria-label="Breadcrumb">
      <a href="{{ url('/') }}" class="hover:text-white transition">Home</a>
      <i class="fas fa-chevron-right text-[8px]"></i>
      <span class="text-white font-semibold">Mga Anunsyo</span>
    </nav>

    <div class="rise flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
      <div class="max-w-2xl">
        <div class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1 mb-4">
          <i class="fas fa-bullhorn text-emerald-200 text-xs"></i>
          <span class="text-[10px] font-black uppercase tracking-widest text-emerald-50">Opisyal na Abiso</span>
        </div>
        <h1 class="text-3xl sm:text-5xl font-black tracking-tight text-white leading-[1.05]">Mga Anunsyo at Balita</h1>
        <p class="mt-3 text-sm sm:text-base text-emerald-100/85 leading-relaxed">Lahat ng opisyal na abiso, paalala, at gawain ng Barangay Bactad East sa isang lugar.</p>
      </div>

      <div class="flex gap-3">
        <div class="rounded-2xl border border-white/15 bg-white/10 px-5 py-3 text-center">
          <p class="text-2xl sm:text-3xl font-black text-white leading-none">{{ number_format($total) }}</p>
          <p class="mt-1 text-[10px] font-bold uppercase tracking-widest text-emerald-200">Kabuuan</p>
        </div>
        <div class="rounded-2xl border border-white/15 bg-white/10 px-5 py-3 text-center">
          <p class="text-2xl sm:text-3xl font-black text-white leading-none">{{ number_format($thisMonth) }}</p>
          <p class="mt-1 text-[10px] font-bold uppercase tracking-widest text-emerald-200">Ngayong buwan</p>
        </div>
      </div>
    </div>

    {{-- Search --}}
    <form method="GET" action="{{ route('public.announcements.index') }}" class="rise mt-8 max-w-3xl" style="--d:.1s">
      <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 rounded-2xl bg-white p-2 shadow-xl shadow-emerald-950/20">
        <div class="relative flex-1">
          <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
          <input type="text" name="q" value="{{ $search }}" placeholder="Maghanap ng anunsyo..."
                 class="w-full rounded-xl bg-transparent py-3 pl-11 pr-4 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none">
        </div>
        <select name="month" class="rounded-xl border border-slate-100 bg-slate-50 px-3 py-3 text-sm text-slate-700 focus:outline-none sm:w-52">
          <option value="">Lahat ng buwan</option>
          @foreach ($archive as $m)
            <option value="{{ $m['value'] }}" @selected($month === $m['value'])>{{ $m['label'] }} ({{ $m['total'] }})</option>
          @endforeach
        </select>
        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-3 text-sm font-black text-white shadow-md transition hover:from-emerald-700 hover:to-teal-700 active:scale-95">
          <i class="fas fa-search text-xs"></i> Hanapin
        </button>
      </div>
    </form>
  </div>
</section>

{{-- ============ CONTENT ============ --}}
<section class="max-w-7xl mx-auto px-3 sm:px-6 py-8 sm:py-12">
  <div class="grid lg:grid-cols-12 gap-6 lg:gap-10">

    {{-- LEFT --}}
    <div class="lg:col-span-8">

      @if ($filtering)
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-slate-600">
          <p>
            <strong class="text-slate-800">{{ number_format($announcements->total()) }}</strong> resulta
            @if ($search !== '') para sa “<strong class="text-slate-800">{{ $search }}</strong>” @endif
            @if ($monthLabel) sa <strong class="text-slate-800">{{ $monthLabel }}</strong> @endif
          </p>
          <a href="{{ route('public.announcements.index') }}" class="inline-flex items-center gap-1.5 font-bold text-emerald-700 hover:text-emerald-900 transition">
            <i class="fas fa-times text-xs"></i> I-clear ang filter
          </a>
        </div>
      @endif

      @if ($announcements->isEmpty())
        <div class="rounded-3xl border border-slate-200 bg-white px-6 py-16 text-center">
          <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full border border-emerald-100 bg-emerald-50">
            <i class="fas {{ $filtering ? 'fa-search' : 'fa-bullhorn' }} text-xl text-emerald-500"></i>
          </div>
          <h2 class="text-lg font-black text-slate-800">{{ $filtering ? 'Walang nahanap na anunsyo' : 'Wala pang anunsyo' }}</h2>
          <p class="mx-auto mt-1 max-w-sm text-sm text-slate-500">
            {{ $filtering ? 'Subukan ang ibang salita o piliin ang “Lahat ng buwan”.' : 'Babalik ka lang mamaya. Dito lalabas ang mga bagong abiso ng barangay.' }}
          </p>
          @if ($filtering)
            <a href="{{ route('public.announcements.index') }}" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-700">Ipakita lahat</a>
          @endif
        </div>
      @else

        {{-- Featured --}}
        @if ($featured)
          @php $fd = $featured->display_date; @endphp
          <article class="rise relative mb-6 overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-900 p-6 text-white shadow-2xl shadow-emerald-700/25 sm:p-9">
            <i class="fas fa-bullhorn absolute -bottom-6 -right-4 -rotate-12 text-[9rem] text-white/10 sm:text-[12rem]" aria-hidden="true"></i>
            <div class="absolute -right-10 -top-16 h-56 w-56 rounded-full bg-emerald-300/20 blur-3xl"></div>
            <div class="relative">
              <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-1.5 rounded-full border border-white/25 bg-white/15 px-3 py-1 text-[10px] font-black uppercase tracking-widest">
                  <i class="fas fa-thumbtack text-[9px]"></i> Pinakabago
                </span>
                @if ($featured->is_new)
                  <span class="rounded-full bg-amber-400 px-2.5 py-1 text-[10px] font-black uppercase tracking-widest text-emerald-950">Bago</span>
                @endif
                <span class="text-xs font-semibold text-emerald-100/90"><i class="far fa-calendar-alt mr-1"></i>{{ $fd->format('F j, Y') }}</span>
                <span class="text-xs font-semibold text-emerald-100/90"><i class="far fa-clock mr-1"></i>{{ $featured->read_minutes }} min read</span>
              </div>
              <h2 class="mt-4 text-2xl font-black leading-tight tracking-tight sm:text-4xl">
                <a href="{{ $featured->url }}" class="underline-offset-4 decoration-2 hover:underline">{{ $featured->title }}</a>
              </h2>
              <p class="mt-3 max-w-2xl text-sm leading-relaxed text-emerald-50/90 sm:text-base line-clamp-3">{{ $featured->excerpt }}</p>
              <a href="{{ $featured->url }}" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-[#fff] px-5 py-3 text-sm font-black text-emerald-800 shadow-lg transition hover:bg-emerald-50">
                Basahin ang buong anunsyo <i class="fas fa-arrow-right text-xs"></i>
              </a>
            </div>
          </article>
        @endif

        {{-- Grid --}}
        @php $gridItems = $featured ? $announcements->skip(1) : $announcements; @endphp
        @if ($gridItems->isNotEmpty())
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6">
            @foreach ($gridItems as $a)
              <div class="rise" style="--d: {{ min($loop->index * 0.06, 0.5) }}s">
                @include('partials.announcement-card', ['a' => $a])
              </div>
            @endforeach
          </div>
        @endif

        {{-- Pagination --}}
        @if ($announcements->hasPages())
          @php
            $cur   = $announcements->currentPage();
            $last  = $announcements->lastPage();
            $range = $announcements->getUrlRange(max(1, $cur - 2), min($last, $cur + 2));
          @endphp
          <nav class="mt-10 flex flex-wrap items-center justify-center gap-2" aria-label="Pagination">
            @if ($announcements->onFirstPage())
              <span class="inline-flex h-10 items-center gap-2 rounded-xl border border-slate-200 px-4 text-sm font-bold text-slate-300"><i class="fas fa-chevron-left text-xs"></i> Nauna</span>
            @else
              <a href="{{ $announcements->previousPageUrl() }}" class="inline-flex h-10 items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-600 transition hover:border-emerald-300 hover:text-emerald-700"><i class="fas fa-chevron-left text-xs"></i> Nauna</a>
            @endif

            @foreach ($range as $page => $url)
              @if ($page == $cur)
                <span aria-current="page" class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-600 text-sm font-black text-white shadow-md">{{ $page }}</span>
              @else
                <a href="{{ $url }}" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-sm font-bold text-slate-600 transition hover:border-emerald-300 hover:text-emerald-700">{{ $page }}</a>
              @endif
            @endforeach

            @if ($announcements->hasMorePages())
              <a href="{{ $announcements->nextPageUrl() }}" class="inline-flex h-10 items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-600 transition hover:border-emerald-300 hover:text-emerald-700">Susunod <i class="fas fa-chevron-right text-xs"></i></a>
            @else
              <span class="inline-flex h-10 items-center gap-2 rounded-xl border border-slate-200 px-4 text-sm font-bold text-slate-300">Susunod <i class="fas fa-chevron-right text-xs"></i></span>
            @endif
          </nav>
        @endif
      @endif
    </div>

    {{-- RIGHT --}}
    <aside class="space-y-5 lg:col-span-4">

      @if ($archive->isNotEmpty())
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
          <h3 class="mb-3 flex items-center gap-2 text-sm font-black text-slate-800"><i class="fas fa-archive text-emerald-600"></i> Archive</h3>
          <ul class="space-y-1">
            <li>
              <a href="{{ route('public.announcements.index') }}" class="flex items-center justify-between rounded-lg px-3 py-2 text-sm transition {{ $month === '' ? 'bg-emerald-50 font-bold text-emerald-700' : 'text-slate-600 hover:bg-slate-50' }}">
                <span>Lahat</span><span class="text-xs font-bold text-slate-400">{{ $total }}</span>
              </a>
            </li>
            @foreach ($archive as $m)
              <li>
                <a href="{{ route('public.announcements.index', ['month' => $m['value']]) }}" class="flex items-center justify-between rounded-lg px-3 py-2 text-sm transition {{ $month === $m['value'] ? 'bg-emerald-50 font-bold text-emerald-700' : 'text-slate-600 hover:bg-slate-50' }}">
                  <span>{{ $m['label'] }}</span><span class="text-xs font-bold text-slate-400">{{ $m['total'] }}</span>
                </a>
              </li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-700 via-emerald-800 to-teal-900 p-5 text-white shadow-lg">
        <i class="fas fa-landmark absolute -bottom-4 -right-3 text-7xl text-white/10" aria-hidden="true"></i>
        <div class="relative">
          <h3 class="text-base font-black">Kailangan ng tulong?</h3>
          <ul class="mt-3 space-y-2.5 text-xs text-emerald-50/90">
            <li class="flex items-start gap-2.5"><i class="fas fa-map-marker-alt mt-0.5 text-emerald-300"></i><span>Barangay Hall, Bactad East, Urdaneta City, Pangasinan 2428</span></li>
            <li class="flex items-center gap-2.5"><i class="fas fa-clock text-emerald-300"></i><span>Lunes–Biyernes, 8:00 AM – 5:00 PM</span></li>
            <li class="flex items-center gap-2.5"><i class="fas fa-envelope text-emerald-300"></i><span>bactadeast@urdaneta.gov.ph</span></li>
          </ul>
        </div>
      </div>

      <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-5">
        <p class="text-sm font-black text-slate-800">Mag-request ng dokumento online</p>
        <p class="mt-1 text-xs text-slate-500">Mag-login sa BIMS para sa clearance, residency, at iba pa.</p>
        <a href="/login" class="mt-3 inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white transition hover:bg-emerald-700">
          <i class="fas fa-sign-in-alt"></i> Mag-login
        </a>
      </div>
    </aside>
  </div>
</section>
@endsection