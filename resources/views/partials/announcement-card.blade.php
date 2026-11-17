@php $d = $a->display_date; @endphp
<article class="glass-card group relative flex flex-col overflow-hidden rounded-2xl sm:rounded-3xl bg-white" @isset($delay) style="--d: {{ $delay }}s" @endisset>
  <div class="h-1 w-full bg-gradient-to-r from-emerald-400 via-emerald-500 to-teal-500"></div>
  <div class="flex flex-1 flex-col p-5 sm:p-6">
    <div class="flex items-start justify-between gap-3">
      <div class="flex h-14 w-14 flex-col items-center justify-center rounded-2xl border border-emerald-100 bg-emerald-50 leading-none text-emerald-700">
        <span class="text-xl font-black">{{ $d->format('d') }}</span>
        <span class="mt-1 text-[10px] font-extrabold uppercase tracking-widest">{{ $d->format('M') }}</span>
      </div>
      @if ($a->is_new)
        <span class="rounded-full bg-rose-500 px-2.5 py-1 text-[10px] font-black uppercase tracking-widest text-white shadow-sm">Bago</span>
      @endif
    </div>

    <p class="mt-4 text-[11px] font-bold uppercase tracking-widest text-slate-400">{{ $d->format('Y') }} • {{ $a->read_minutes }} min read</p>

    <h3 class="mt-1 text-lg font-extrabold leading-snug text-slate-900 transition group-hover:text-emerald-700 line-clamp-2">
      <a href="{{ $a->url }}" class="after:absolute after:inset-0">{{ $a->title }}</a>
    </h3>
    <p class="mt-2 text-sm leading-relaxed text-slate-500 line-clamp-3">{{ $a->excerpt }}</p>

    <div class="mt-auto pt-5">
      <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600">
        Basahin <i class="fas fa-arrow-right text-[10px] transition-transform group-hover:translate-x-1"></i>
      </span>
    </div>
  </div>
</article>