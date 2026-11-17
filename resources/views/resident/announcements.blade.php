@extends('layouts.app')

@section('content')
<div class="relative min-h-screen bg-white">
    <!-- Subtle grid background pattern (thesis‑grade) -->
    <div class="fixed inset-0 pointer-events-none opacity-5" style="background-image: radial-gradient(#059669 1px, transparent 1px); background-size: 40px 40px;"></div>

    <div class="max-w-5xl mx-auto pt-8 pb-12 px-4 space-y-8 relative z-10">
        
        <!-- Hero Header – elevated announcement banner -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-800 shadow-xl animate-in fade-in slide-in-from-top-4 duration-700">
            <div class="absolute top-0 right-0 w-48 h-48 bg-white/10 rounded-full blur-3xl -mr-24 -mt-24"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full blur-3xl -ml-24 -mb-24"></div>
            <div class="relative p-8 text-white">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-bullhorn text-2xl"></i>
                    <span class="text-sm font-semibold uppercase tracking-wider bg-white/20 px-3 py-1 rounded-full">Official Barangay Advisories</span>
                </div>
                <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight">Announcements & Notices</h2>
                <p class="text-white/80 text-sm mt-2">Stay updated with the latest news, events, and directives from Barangay Bactad East</p>
                <div class="mt-4 flex items-center gap-4 text-xs opacity-80">
                    <div class="flex items-center gap-1"><i class="fas fa-check-circle"></i> Verified</div>
                    <div class="flex items-center gap-1"><i class="fas fa-clock"></i> Real‑time updates</div>
                </div>
            </div>
        </div>

        @if($announcements->count())
            <div class="space-y-6">
                @foreach($announcements as $index => $ann)
                    @php
                        $isRecent = $ann->created_at->diffInDays(now()) <= 7;
                        $isEdited = $ann->created_at->ne($ann->updated_at);
                        $willBePublished = $ann->published_date && $ann->published_date->isFuture();
                    @endphp
                    <div class="group bg-white rounded-2xl shadow-md border border-slate-100 hover:shadow-xl transition-all duration-300 overflow-hidden hover:-translate-y-1 animate-in fade-in slide-in-from-bottom-4 duration-500" style="animation-delay: {{ $index * 50 }}ms">
                        <!-- Colored top bar (emerald for active, amber for future, gray for old) -->
                        <div class="h-1.5 {{ $willBePublished ? 'bg-amber-500' : ($isRecent ? 'bg-gradient-to-r from-emerald-500 to-emerald-600' : 'bg-slate-300') }}"></div>
                        <div class="p-6 md:p-8">
                            <!-- Header row: title and badges -->
                            <div class="flex flex-wrap justify-between items-start gap-3">
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-center gap-2 mb-2">
                                        <!-- Calendar icon + date -->
                                        <div class="flex items-center gap-1 text-xs text-slate-500 bg-slate-100 px-2.5 py-0.5 rounded-full">
                                            <i class="far fa-calendar-alt text-emerald-500 text-[10px]"></i>
                                            <span class="font-medium">{{ $ann->published_date?->format('M d, Y') ?: $ann->created_at->format('M d, Y') }}</span>
                                        </div>
                                        <!-- Recent badge -->
                                        @if($isRecent && !$willBePublished)
                                            <span class="bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                                <i class="fas fa-bolt text-[8px] mr-1"></i> Recent
                                            </span>
                                        @endif
                                        <!-- Edited badge -->
                                        @if($isEdited && !$willBePublished)
                                            <span class="bg-gray-100 text-gray-500 text-[9px] font-medium px-2 py-0.5 rounded-full">
                                                <i class="fas fa-edit text-[8px] mr-1"></i> Edited
                                            </span>
                                        @endif
                                    </div>
                                    <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight leading-tight">{{ $ann->title }}</h3>
                                </div>
                                <!-- Status badge (future / active) -->
                                @if($willBePublished)
                                    <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-[10px] font-bold uppercase flex items-center gap-1">
                                        <i class="fas fa-hourglass-half"></i> Scheduled
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-bold uppercase flex items-center gap-1">
                                        <i class="fas fa-check-circle"></i> Published
                                    </span>
                                @endif
                            </div>

                            <!-- Announcement content (preserved formatting) -->
                            <div class="mt-5 prose prose-slate max-w-none">
                                <div class="text-slate-600 leading-relaxed whitespace-pre-line">
                                    {{ $ann->content }}
                                </div>
                            </div>

                            <!-- Footer: timestamps and additional info -->
                            <div class="mt-6 pt-4 border-t border-slate-100 flex flex-wrap justify-between items-center gap-2">
                                <div class="text-[11px] text-slate-400 flex items-center gap-2">
                                    <span class="flex items-center gap-1"><i class="far fa-clock"></i> Posted {{ $ann->created_at->diffForHumans() }}</span>
                                    @if($isEdited)
                                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                        <span class="flex items-center gap-1"><i class="fas fa-pen"></i> Updated {{ $ann->updated_at->diffForHumans() }}</span>
                                    @endif
                                </div>
                                @if($ann->published_date && $ann->published_date->isFuture())
                                    <div class="text-[11px] text-amber-600 flex items-center gap-1">
                                        <i class="fas fa-calendar-week"></i>
                                        <span>Will be live on {{ $ann->published_date->format('M d, Y') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if(method_exists($announcements, 'links'))
                <div class="pt-4">
                    {{ $announcements->links() }}
                </div>
            @endif
        @else
            <div class="bg-white rounded-2xl border-2 border-dashed border-slate-200 p-16 text-center transition hover:border-emerald-300 hover:bg-emerald-50/20 animate-in fade-in duration-700">
                <div class="flex flex-col items-center">
                    <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-bullhorn text-3xl text-emerald-500"></i>
                    </div>
                    <p class="text-slate-500 font-medium">No announcements at the moment.</p>
                    <p class="text-slate-400 text-sm mt-1">Please check back later for official barangay updates.</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Back to Top Button (smooth, appears after scrolling) -->
    <button id="backToTop" class="fixed bottom-6 right-6 w-11 h-11 bg-emerald-600 text-white rounded-full shadow-lg hover:bg-emerald-700 transition-all duration-300 opacity-0 invisible hover:scale-110 z-50 flex items-center justify-center focus:outline-none">
        <i class="fas fa-arrow-up text-sm"></i>
    </button>
</div>

<script>
    // Back to Top button logic
    (function() {
        const backToTop = document.getElementById('backToTop');
        if (!backToTop) return;
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTop.classList.remove('opacity-0', 'invisible');
                backToTop.classList.add('opacity-100', 'visible');
            } else {
                backToTop.classList.add('opacity-0', 'invisible');
                backToTop.classList.remove('opacity-100', 'visible');
            }
        });
        backToTop.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    })();
</script>

<style>
    /* Fade‑in animations */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-in {
        animation: fadeIn 0.5s ease-out forwards;
    }
    .delay-100 { animation-delay: 100ms; }
    .delay-200 { animation-delay: 200ms; }
    .invisible { visibility: hidden; }
    .visible { visibility: visible; }
    /* Enhanced prose for announcement content */
    .prose {
        max-width: none;
        line-height: 1.6;
    }
    .prose p {
        margin-bottom: 1em;
    }
</style>
@endsection