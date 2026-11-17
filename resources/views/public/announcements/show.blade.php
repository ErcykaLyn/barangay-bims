@extends('layouts.public')

@section('title', $announcement->title . ' | BIMS Bactad East')
@section('meta_description', $announcement->excerpt)
@section('og_type', 'article')

@php
    $d        = $announcement->display_date;
    $shareUrl = urlencode($announcement->url);
    $ld = [
        '@context'         => 'https://schema.org',
        '@type'            => 'NewsArticle',
        'headline'         => $announcement->title,
        'description'      => $announcement->excerpt,
        'datePublished'    => $d->toDateString(),
        'dateModified'     => optional($announcement->updated_at)->toDateString(),
        'mainEntityOfPage' => $announcement->url,
        'publisher'        => [
            '@type' => 'Organization',
            'name'  => 'Barangay Bactad East',
            'logo'  => ['@type' => 'ImageObject', 'url' => asset('images/brgy_logo.png.jpg')],
        ],
    ];

    // ---------- Linisin ang laman ng anunsyo ----------
    $body = \App\Support\SafeHtml::render($announcement->content);

    // 1. Gawing "Click here" ang default na "link text" (pati sa mga lumang post)
    $body = preg_replace('/(<a\b[^>]*>)\s*link\s*text\s*(<\/a>)/i', '$1Click here$2', $body);

    // 2. Buksan sa bagong tab ang external links
    $body = preg_replace('/<a\s+(?![^>]*\btarget=)([^>]*href="https?:)/i', '<a target="_blank" rel="noopener noreferrer" $1', $body);
@endphp

@push('head')
<script type="application/ld+json">@json($ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG)</script>
<style>
.ann-body { font-size: 1rem; line-height: 1.85; color: #334155; overflow-wrap: anywhere; }
.ann-body p { margin: 0 0 1.15em; }
.ann-body h2, .ann-body h3, .ann-body h4 { font-weight: 800; color: #0f172a; line-height: 1.3; margin: 1.6em 0 .6em; }
.ann-body h2 { font-size: 1.5rem; } .ann-body h3 { font-size: 1.25rem; } .ann-body h4 { font-size: 1.1rem; }
.ann-body ul { list-style: disc; padding-left: 1.4em; margin: 0 0 1.15em; }
.ann-body ol { list-style: decimal; padding-left: 1.4em; margin: 0 0 1.15em; }
.ann-body li { margin: .3em 0; }
.ann-body a { color: #059669; font-weight: 600; text-decoration: underline; text-underline-offset: 3px; }
.ann-body strong { color: #0f172a; }
.ann-body blockquote { border-left: 4px solid #34d399; background: #f0fdf4; padding: .9em 1.1em; border-radius: 0 .75rem .75rem 0; margin: 0 0 1.15em; color: #065f46; }
html.dark .ann-body { color: #cbd5e1; }
html.dark .ann-body h2, html.dark .ann-body h3, html.dark .ann-body h4, html.dark .ann-body strong { color: #f1f5f9; }
html.dark .ann-body blockquote { background: rgba(16,185,129,.1); color: #a7f3d0; }
@media print { .ann-body { color: #000; } }
</style>
@endpush

@section('content')

{{-- ============ SLIM HEADER ============ --}}
<section class="no-print relative overflow-hidden bg-gradient-to-br from-emerald-700 via-emerald-800 to-teal-900">
  <div class="absolute -top-20 -right-10 h-56 w-56 rounded-full bg-emerald-300/20 blur-3xl"></div>
  <div class="relative max-w-7xl mx-auto px-3 sm:px-6 py-6 sm:py-8">
    <nav class="flex flex-wrap items-center gap-1.5 text-xs text-emerald-200/80" aria-label="Breadcrumb">
      <a href="{{ url('/') }}" class="hover:text-white transition">Home</a>
      <i class="fas fa-chevron-right text-[8px]"></i>
      <a href="{{ route('public.announcements.index') }}" class="hover:text-white transition">Mga Anunsyo</a>
      <i class="fas fa-chevron-right text-[8px]"></i>
      <span class="max-w-[14rem] truncate font-semibold text-white sm:max-w-md">{{ $announcement->title }}</span>
    </nav>
  </div>
</section>

<section class="max-w-7xl mx-auto px-3 sm:px-6 py-8 sm:py-12">
  <div class="grid lg:grid-cols-12 gap-6 lg:gap-10">

    {{-- ARTICLE --}}
    <article class="lg:col-span-8">
      <div class="rise overflow-hidden rounded-2xl border border-emerald-100 bg-white shadow-sm sm:rounded-3xl">
        <div class="h-1.5 bg-gradient-to-r from-emerald-400 via-emerald-500 to-teal-500"></div>
        <div class="p-5 sm:p-8 md:p-10">

          <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs font-semibold text-slate-500">
            <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-100 bg-emerald-50 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-emerald-700">
              <i class="fas fa-bullhorn text-[9px]"></i> Anunsyo
            </span>
            @if ($announcement->is_new)
              <span class="rounded-full bg-rose-500 px-2.5 py-1 text-[10px] font-black uppercase tracking-widest text-white">Bago</span>
            @endif
            <span><i class="far fa-calendar-alt mr-1 text-emerald-600"></i>{{ $d->format('l, F j, Y') }}</span>
            <span><i class="far fa-clock mr-1 text-emerald-600"></i>{{ $announcement->read_minutes }} min read</span>
          </div>

          <h1 class="mt-4 text-2xl font-black leading-tight tracking-tight text-slate-900 sm:text-4xl">{{ $announcement->title }}</h1>

          {{-- Actions --}}
          <div class="no-print mt-5 flex flex-wrap items-center gap-2 border-y border-slate-100 py-4">
            <a href="{{ route('public.announcements.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-bold text-slate-600 transition hover:border-emerald-300 hover:text-emerald-700">
              <i class="fas fa-arrow-left text-[10px]"></i> Lahat ng anunsyo
            </a>
            <span class="mx-1 hidden h-5 w-px bg-slate-200 sm:block"></span>
            <button type="button" id="copyLink" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-bold text-slate-600 transition hover:border-emerald-300 hover:text-emerald-700">
              <i class="fas fa-link text-[10px]"></i> <span>Kopyahin ang link</span>
            </button>
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-bold text-slate-600 transition hover:border-blue-300 hover:text-blue-700">
              <i class="fab fa-facebook-f text-[10px]"></i> Ibahagi
            </a>
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-bold text-slate-600 transition hover:border-emerald-300 hover:text-emerald-700">
              <i class="fas fa-print text-[10px]"></i> I-print
            </button>
          </div>

          {{-- Body --}}
          <div class="ann-body mt-6">
            {!! $body !!}
          </div>

          <p class="mt-8 border-t border-slate-100 pt-4 text-xs text-slate-400">
            Inilathala ng Barangay Bactad East noong {{ $d->format('F j, Y') }}.
          </p>
        </div>
      </div>

      {{-- Prev / Next --}}
      @if ($older || $newer)
        <div class="no-print mt-5 grid gap-4 sm:grid-cols-2">
          @if ($older)
            <a href="{{ $older->url }}" class="group rounded-2xl border border-slate-200 bg-white p-4 transition hover:border-emerald-300 hover:shadow-md">
              <p class="text-[10px] font-black uppercase tracking-widest text-slate-400"><i class="fas fa-arrow-left mr-1"></i> Nauna</p>
              <p class="mt-1 text-sm font-bold text-slate-800 transition group-hover:text-emerald-700 line-clamp-2">{{ $older->title }}</p>
            </a>
          @else
            <span></span>
          @endif
          @if ($newer)
            <a href="{{ $newer->url }}" class="group rounded-2xl border border-slate-200 bg-white p-4 text-right transition hover:border-emerald-300 hover:shadow-md">
              <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Susunod <i class="fas fa-arrow-right ml-1"></i></p>
              <p class="mt-1 text-sm font-bold text-slate-800 transition group-hover:text-emerald-700 line-clamp-2">{{ $newer->title }}</p>
            </a>
          @endif
        </div>
      @endif
    </article>

    {{-- SIDEBAR --}}
    <aside class="no-print space-y-5 lg:col-span-4">

      @if ($others->isNotEmpty())
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
          <h3 class="mb-3 flex items-center gap-2 text-sm font-black text-slate-800"><i class="fas fa-newspaper text-emerald-600"></i> Iba pang anunsyo</h3>
          <ul class="divide-y divide-slate-100">
            @foreach ($others as $o)
              <li>
                <a href="{{ $o->url }}" class="group flex gap-3 py-3">
                  <div class="flex h-12 w-12 flex-shrink-0 flex-col items-center justify-center rounded-xl border border-emerald-100 bg-emerald-50 leading-none text-emerald-700">
                    <span class="text-base font-black">{{ $o->display_date->format('d') }}</span>
                    <span class="mt-0.5 text-[9px] font-extrabold uppercase tracking-widest">{{ $o->display_date->format('M') }}</span>
                  </div>
                  <div class="min-w-0">
                    <p class="text-sm font-bold leading-snug text-slate-800 transition group-hover:text-emerald-700 line-clamp-2">{{ $o->title }}</p>
                    @if ($o->is_new)<span class="mt-1 inline-block rounded-full bg-rose-500 px-2 py-0.5 text-[9px] font-black uppercase tracking-widest text-white">Bago</span>@endif
                  </div>
                </a>
              </li>
            @endforeach
          </ul>
          <a href="{{ route('public.announcements.index') }}" class="mt-2 inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 transition hover:text-emerald-900">
            Tingnan lahat <i class="fas fa-arrow-right text-[10px]"></i>
          </a>
        </div>
      @endif

      <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-700 via-emerald-800 to-teal-900 p-5 text-white shadow-lg">
        <i class="fas fa-landmark absolute -bottom-4 -right-3 text-7xl text-white/10" aria-hidden="true"></i>
        <div class="relative">
          <h3 class="text-base font-black">May tanong?</h3>
          <p class="mt-1 text-xs text-emerald-100/85">Makipag-ugnayan sa Barangay Hall.</p>
          <ul class="mt-3 space-y-2.5 text-xs text-emerald-50/90">
            <li class="flex items-start gap-2.5"><i class="fas fa-map-marker-alt mt-0.5 text-emerald-300"></i><span>Barangay Hall, Bactad East, Urdaneta City, Pangasinan 2428</span></li>
            <li class="flex items-center gap-2.5"><i class="fas fa-clock text-emerald-300"></i><span>Lunes–Biyernes, 8:00 AM – 5:00 PM</span></li>
            <li class="flex items-center gap-2.5"><i class="fas fa-envelope text-emerald-300"></i><span>bactadeast@urdaneta.gov.ph</span></li>
          </ul>
        </div>
      </div>
    </aside>
  </div>
</section>
@endsection

@push('scripts')
<script>
document.getElementById('copyLink')?.addEventListener('click', async function () {
  const label = this.querySelector('span'), old = label.textContent;
  try {
    await navigator.clipboard.writeText(location.href);
  } catch (e) {
    const t = document.createElement('textarea'); t.value = location.href;
    document.body.appendChild(t); t.select(); document.execCommand('copy'); t.remove();
  }
  label.textContent = 'Nakopya!';
  setTimeout(() => { label.textContent = old; }, 1800);
});
</script>
@endpush