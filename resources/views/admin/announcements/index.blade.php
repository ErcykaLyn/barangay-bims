@extends('layouts.admin')

@section('content')
@php
    $total = $announcements->count();
    $activeCount = $announcements->where('is_active', true)->count();
    $inactiveCount = $announcements->where('is_active', false)->count();
    $scheduledCount = $announcements->filter(fn ($a) => $a->is_active && $a->published_date && $a->published_date->isFuture())->count();
@endphp

<style>
    .ann-pattern {
        background-color: #047857;
        background-image:
            radial-gradient(circle at 15% 20%, rgba(255,255,255,.14) 0, transparent 38%),
            radial-gradient(circle at 85% 80%, rgba(251,191,36,.20) 0, transparent 35%),
            url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.07'%3E%3Ccircle cx='3' cy='3' r='1.5'/%3E%3Ccircle cx='23' cy='23' r='1.5'/%3E%3C/g%3E%3C/svg%3E"),
            linear-gradient(135deg, #065f46 0%, #047857 50%, #0d9488 100%);
    }
    .ann-float { animation: annFloat 6s ease-in-out infinite; }
    @keyframes annFloat { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }
    .ann-fade { animation: annFade .5s ease both; }
    @keyframes annFade { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .ann-pop { animation: annPop .2s ease both; }
    @keyframes annPop { from { opacity: 0; transform: scale(.95); } to { opacity: 1; transform: scale(1); } }
    .ann-clamp { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
</style>

<div class="relative">
    <div class="pointer-events-none absolute inset-x-0 -top-10 h-[420px] overflow-hidden -z-10">
        <div class="absolute -top-24 -left-16 h-72 w-72 rounded-full bg-emerald-200/40 blur-3xl"></div>
        <div class="absolute top-10 right-0 h-80 w-80 rounded-full bg-amber-200/30 blur-3xl"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">

        {{-- HERO --}}
        <div class="relative overflow-hidden rounded-3xl ann-pattern shadow-xl shadow-emerald-900/10 mb-8 ann-fade">
            <div class="relative px-6 sm:px-10 py-8 sm:py-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-start gap-5">
                    <div class="ann-float shrink-0 h-16 w-16 rounded-2xl bg-white/15 backdrop-blur border border-white/25 flex items-center justify-center shadow-lg">
                        <i class="fas fa-bullhorn text-white text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-emerald-100/90 text-[11px] font-black uppercase tracking-[0.25em] mb-2">Barangay Bactad East</p>
                        <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">Announcements</h1>
                        <p class="text-emerald-50/90 text-sm mt-2 max-w-xl">Pamahalaan ang lahat ng anunsyo, advisory, at abiso para sa mga residente.</p>
                    </div>
                </div>

                <a href="{{ route('admin.announcements.create') }}"
                   class="group shrink-0 inline-flex items-center justify-center gap-2.5 bg-white text-emerald-800 hover:bg-amber-50 px-6 py-4 rounded-2xl text-sm font-black uppercase tracking-wider shadow-lg shadow-emerald-950/20 hover:-translate-y-0.5 transition">
                    <i class="fas fa-plus-circle text-emerald-600 group-hover:rotate-90 transition-transform duration-300"></i> New Announcement
                </a>
            </div>
        </div>

        {{-- FLASH --}}
        @if(session('success'))
            <div id="flash" class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 flex items-center gap-4 ann-fade">
                <div class="h-10 w-10 shrink-0 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <i class="fas fa-check-circle"></i>
                </div>
                <p class="flex-1 text-sm font-semibold text-emerald-800">{{ session('success') }}</p>
                <button type="button" onclick="document.getElementById('flash').remove()" class="h-8 w-8 rounded-lg text-emerald-600 hover:bg-emerald-100 transition" aria-label="Close">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
        @endif

        {{-- STATS (click to filter) --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <button type="button" data-stat="all" class="stat-card text-left bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition ann-fade">
                <div class="flex items-center justify-between">
                    <div class="h-11 w-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center"><i class="fas fa-bullhorn"></i></div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Total</span>
                </div>
                <p class="text-3xl font-black text-slate-800 mt-4 tabular-nums">{{ $total }}</p>
                <p class="text-xs text-slate-500 mt-0.5">Lahat ng anunsyo</p>
            </button>
            <button type="button" data-stat="active" class="stat-card text-left bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition ann-fade">
                <div class="flex items-center justify-between">
                    <div class="h-11 w-11 rounded-xl bg-teal-100 text-teal-600 flex items-center justify-center"><i class="fas fa-check-circle"></i></div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Active</span>
                </div>
                <p class="text-3xl font-black text-slate-800 mt-4 tabular-nums">{{ $activeCount }}</p>
                <p class="text-xs text-slate-500 mt-0.5">Nakikita ng residente</p>
            </button>
            <button type="button" data-stat="scheduled" class="stat-card text-left bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition ann-fade">
                <div class="flex items-center justify-between">
                    <div class="h-11 w-11 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center"><i class="fas fa-clock"></i></div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Scheduled</span>
                </div>
                <p class="text-3xl font-black text-slate-800 mt-4 tabular-nums">{{ $scheduledCount }}</p>
                <p class="text-xs text-slate-500 mt-0.5">Lalabas sa susunod</p>
            </button>
            <button type="button" data-stat="inactive" class="stat-card text-left bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition ann-fade">
                <div class="flex items-center justify-between">
                    <div class="h-11 w-11 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center"><i class="fas fa-eye-slash"></i></div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Inactive</span>
                </div>
                <p class="text-3xl font-black text-slate-800 mt-4 tabular-nums">{{ $inactiveCount }}</p>
                <p class="text-xs text-slate-500 mt-0.5">Nakatago / draft</p>
            </button>
        </div>

        @if($total > 0)
        {{-- TOOLBAR --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 mb-6 flex flex-col lg:flex-row lg:items-center gap-4 ann-fade">
            <div class="relative flex-1 group">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-emerald-600 transition"></i>
                <input type="text" id="searchInput" placeholder="Maghanap ng title o content..."
                       class="w-full bg-slate-50/70 border border-slate-200 rounded-xl pl-11 pr-10 py-3 text-sm text-slate-700 placeholder:text-slate-400 focus:bg-white focus:ring-4 focus:ring-emerald-500/15 focus:border-emerald-500 transition outline-none">
                <button type="button" id="clearSearch" class="hidden absolute right-3 top-1/2 -translate-y-1/2 h-6 w-6 rounded-full bg-slate-200 text-slate-500 hover:bg-slate-300 text-xs transition" aria-label="Clear search">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>

            <div class="flex flex-wrap items-center gap-2" id="filterPills">
                <button type="button" data-filter="all" class="pill px-4 py-2 rounded-full text-xs font-black uppercase tracking-wider border transition">All</button>
                <button type="button" data-filter="active" class="pill px-4 py-2 rounded-full text-xs font-black uppercase tracking-wider border transition">Active</button>
                <button type="button" data-filter="scheduled" class="pill px-4 py-2 rounded-full text-xs font-black uppercase tracking-wider border transition">Scheduled</button>
                <button type="button" data-filter="inactive" class="pill px-4 py-2 rounded-full text-xs font-black uppercase tracking-wider border transition">Inactive</button>
            </div>

            <div class="relative">
                <i class="fas fa-arrow-down-wide-short absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                <select id="sortSelect" class="appearance-none bg-slate-50/70 border border-slate-200 rounded-xl pl-10 pr-9 py-3 text-xs font-bold uppercase tracking-wider text-slate-600 focus:ring-4 focus:ring-emerald-500/15 focus:border-emerald-500 outline-none cursor-pointer">
                    <option value="newest">Newest first</option>
                    <option value="oldest">Oldest first</option>
                    <option value="az">Title A–Z</option>
                </select>
                <i class="fas fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
            </div>
        </div>
        @endif

        {{-- LIST --}}
        <div id="list" class="space-y-5">
            @forelse($announcements as $ann)
                @php
                    $displayDate = $ann->published_date ?: $ann->created_at;
                    $isRecent = $ann->created_at->gte(now()->subDays(7));
                    $isEdited = $ann->created_at->ne($ann->updated_at);
                    $isScheduled = $ann->is_active && $ann->published_date && $ann->published_date->isFuture();
                    $status = !$ann->is_active ? 'inactive' : ($isScheduled ? 'scheduled' : 'active');
                    $plain = trim(strip_tags($ann->content));

                    $accent = ['active' => 'from-emerald-500 to-teal-500', 'scheduled' => 'from-amber-400 to-orange-400', 'inactive' => 'from-slate-300 to-slate-400'][$status];
                    $dateBox = ['active' => 'bg-emerald-50 border-emerald-100 text-emerald-700', 'scheduled' => 'bg-amber-50 border-amber-100 text-amber-700', 'inactive' => 'bg-slate-50 border-slate-200 text-slate-500'][$status];
                    $badge = ['active' => 'bg-emerald-100 text-emerald-700', 'scheduled' => 'bg-amber-100 text-amber-700', 'inactive' => 'bg-slate-100 text-slate-500'][$status];
                    $badgeIcon = ['active' => 'fa-circle-check', 'scheduled' => 'fa-clock', 'inactive' => 'fa-eye-slash'][$status];
                    $badgeText = ['active' => 'Active', 'scheduled' => 'Scheduled', 'inactive' => 'Inactive'][$status];
                @endphp

                <article class="ann-item group bg-white rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-xl hover:shadow-emerald-900/5 hover:-translate-y-0.5 transition-all duration-300 overflow-hidden ann-fade"
                         data-status="{{ $status }}"
                         data-title="{{ \Illuminate\Support\Str::lower($ann->title) }}"
                         data-content="{{ \Illuminate\Support\Str::lower(\Illuminate\Support\Str::limit($plain, 600, '')) }}"
                         data-date="{{ $displayDate->timestamp }}">
                    <div class="h-1.5 bg-gradient-to-r {{ $accent }}"></div>

                    <div class="p-5 sm:p-6 flex flex-col sm:flex-row gap-5">
                        {{-- Date block --}}
                        <div class="hidden sm:flex shrink-0 flex-col items-center justify-center w-20 h-24 rounded-2xl border {{ $dateBox }}">
                            <span class="text-[10px] font-black uppercase tracking-widest opacity-80">{{ $displayDate->format('M') }}</span>
                            <span class="text-3xl font-black leading-none my-0.5">{{ $displayDate->format('d') }}</span>
                            <span class="text-[10px] font-bold opacity-70">{{ $displayDate->format('Y') }}</span>
                        </div>

                        {{-- Body --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <span class="sm:hidden inline-flex items-center gap-1.5 text-[11px] font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full">
                                    <i class="far fa-calendar-alt text-emerald-500"></i> {{ $displayDate->format('M d, Y') }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $badge }}">
                                    <i class="fas {{ $badgeIcon }} text-[10px]"></i> {{ $badgeText }}
                                </span>
                                @if($isRecent && $ann->is_active)
                                    <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-700 text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-full">
                                        <i class="fas fa-bolt text-[9px]"></i> New
                                    </span>
                                @endif
                                @if($isEdited)
                                    <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-500 text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full">
                                        <i class="fas fa-pen text-[9px]"></i> Edited
                                    </span>
                                @endif
                            </div>

                            <h3 class="text-lg sm:text-xl font-extrabold text-slate-800 tracking-tight leading-snug break-words group-hover:text-emerald-700 transition">{{ $ann->title }}</h3>

                            <p class="ann-clamp mt-2 text-sm text-slate-600 leading-relaxed">
                                {{ \Illuminate\Support\Str::limit($plain, 220) }}
                            </p>

                            <div class="mt-4 pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                                <div class="text-[11px] text-slate-400 flex items-center gap-1.5">
                                    @if($isEdited)
                                        <i class="fas fa-pen"></i> Updated {{ $ann->updated_at->diffForHumans() }}
                                    @else
                                        <i class="far fa-clock"></i> Created {{ $ann->created_at->diffForHumans() }}
                                    @endif
                                </div>

                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.announcements.edit', $ann) }}"
                                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider text-emerald-700 bg-emerald-50 hover:bg-emerald-600 hover:text-white transition" title="Edit">
                                        <i class="fas fa-pen-to-square"></i> Edit
                                    </a>
                                    <button type="button"
                                            data-delete-url="{{ route('admin.announcements.destroy', $ann) }}"
                                            data-delete-title="{{ $ann->title }}"
                                            class="delete-btn inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider text-rose-600 bg-rose-50 hover:bg-rose-600 hover:text-white transition" title="Delete">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div class="bg-white rounded-3xl border-2 border-dashed border-slate-200 p-14 text-center hover:border-emerald-300 hover:bg-emerald-50/20 transition ann-fade">
                    <div class="mx-auto h-20 w-20 rounded-3xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-3xl mb-5 ann-float">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <p class="text-lg font-extrabold text-slate-700">Wala pang announcements</p>
                    <p class="text-slate-400 text-sm mt-1 mb-6">Mag-umpisa sa pag-create ng una mong anunsyo para sa mga residente.</p>
                    <a href="{{ route('admin.announcements.create') }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-2xl text-sm font-black uppercase tracking-wider shadow-lg shadow-emerald-600/25 transition">
                        <i class="fas fa-plus-circle"></i> New Announcement
                    </a>
                </div>
            @endforelse
        </div>

        {{-- NO RESULTS (client-side) --}}
        <div id="noResults" class="hidden bg-white rounded-3xl border-2 border-dashed border-slate-200 p-12 text-center">
            <div class="mx-auto h-16 w-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mb-4"><i class="fas fa-magnifying-glass"></i></div>
            <p class="font-extrabold text-slate-700">Walang nahanap</p>
            <p class="text-slate-400 text-sm mt-1 mb-5">Subukan ang ibang salita o ibang filter.</p>
            <button type="button" id="resetFilters" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-black uppercase tracking-wider transition">
                <i class="fas fa-rotate-left"></i> I-reset ang filters
            </button>
        </div>

        @if($total > 0)
            <p id="countText" class="text-center text-xs text-slate-400 pt-6">Showing {{ $total }} of {{ $total }} announcement(s)</p>
        @endif
    </div>
</div>

{{-- DELETE MODAL --}}
<div id="deleteModal" class="hidden fixed inset-0 z-50 items-center justify-center p-4" role="dialog" aria-modal="true">
    <div id="modalBackdrop" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
    <div class="ann-pop relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden">
        <div class="h-1.5 bg-gradient-to-r from-rose-500 to-orange-400"></div>
        <div class="p-7 text-center">
            <div class="mx-auto h-16 w-16 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center text-2xl mb-5">
                <i class="fas fa-trash-alt"></i>
            </div>
            <h3 class="text-xl font-extrabold text-slate-800">Burahin ang anunsyo?</h3>
            <p class="text-sm text-slate-500 mt-2">Permanente nang made-delete ang:</p>
            <p id="modalTitle" class="mt-2 text-sm font-bold text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 break-words"></p>
            <p class="text-xs text-rose-500 mt-3 font-semibold">Hindi na ito mababawi.</p>

            <form id="deleteForm" method="POST" class="mt-6 flex flex-col-reverse sm:flex-row gap-3">
                @csrf
                @method('DELETE')
                <button type="button" id="modalCancel" class="flex-1 px-5 py-3 rounded-2xl bg-white border border-slate-300 text-slate-600 font-bold text-sm uppercase tracking-wider hover:bg-slate-50 transition">Cancel</button>
                <button type="submit" class="flex-1 px-5 py-3 rounded-2xl bg-rose-600 hover:bg-rose-700 text-white font-black text-sm uppercase tracking-wider shadow-lg shadow-rose-600/25 transition flex items-center justify-center gap-2">
                    <i class="fas fa-trash-alt"></i> Delete
                </button>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    var list = document.getElementById('list');
    var items = Array.prototype.slice.call(list.querySelectorAll('.ann-item'));
    var search = document.getElementById('searchInput');
    var clearBtn = document.getElementById('clearSearch');
    var sortSel = document.getElementById('sortSelect');
    var noResults = document.getElementById('noResults');
    var countText = document.getElementById('countText');
    var pills = document.querySelectorAll('.pill');
    var statCards = document.querySelectorAll('.stat-card');
    var filter = 'all';
    var total = items.length;

    var PILL_ON = 'pill px-4 py-2 rounded-full text-xs font-black uppercase tracking-wider border transition bg-emerald-600 border-emerald-600 text-white shadow-md shadow-emerald-600/25';
    var PILL_OFF = 'pill px-4 py-2 rounded-full text-xs font-black uppercase tracking-wider border transition bg-white border-slate-200 text-slate-500 hover:border-emerald-300 hover:text-emerald-700';

    function apply() {
        var q = search ? search.value.trim().toLowerCase() : '';
        var shown = 0;

        items.forEach(function (el) {
            var okStatus = filter === 'all' || el.dataset.status === filter;
            var okText = !q || el.dataset.title.indexOf(q) !== -1 || el.dataset.content.indexOf(q) !== -1;
            var show = okStatus && okText;
            el.style.display = show ? '' : 'none';
            if (show) shown++;
        });

        pills.forEach(function (p) { p.className = p.dataset.filter === filter ? PILL_ON : PILL_OFF; });
        statCards.forEach(function (c) {
            var on = c.dataset.stat === filter && filter !== 'all';
            c.classList.toggle('ring-2', on);
            c.classList.toggle('ring-emerald-500', on);
        });

        if (clearBtn) clearBtn.classList.toggle('hidden', !q);
        noResults.classList.toggle('hidden', !(total > 0 && shown === 0));
        if (countText) countText.textContent = 'Showing ' + shown + ' of ' + total + ' announcement(s)';
    }

    function sortItems() {
        var mode = sortSel ? sortSel.value : 'newest';
        items.sort(function (a, b) {
            if (mode === 'az') return a.dataset.title.localeCompare(b.dataset.title);
            var d = parseInt(a.dataset.date, 10) - parseInt(b.dataset.date, 10);
            return mode === 'oldest' ? d : -d;
        });
        items.forEach(function (el) { list.appendChild(el); });
    }

    function setFilter(f) { filter = f; apply(); }

    pills.forEach(function (p) { p.addEventListener('click', function () { setFilter(p.dataset.filter); }); });
    statCards.forEach(function (c) {
        c.addEventListener('click', function () {
            setFilter(filter === c.dataset.stat ? 'all' : c.dataset.stat);
        });
    });
    if (search) search.addEventListener('input', apply);
    if (clearBtn) clearBtn.addEventListener('click', function () { search.value = ''; search.focus(); apply(); });
    if (sortSel) sortSel.addEventListener('change', sortItems);
    document.getElementById('resetFilters').addEventListener('click', function () {
        if (search) search.value = '';
        filter = 'all';
        apply();
    });

    // ---------- delete modal ----------
    var modal = document.getElementById('deleteModal');
    var form = document.getElementById('deleteForm');

    function openModal(url, title) {
        form.setAttribute('action', url);
        document.getElementById('modalTitle').textContent = title;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.getElementById('modalCancel').focus();
    }
    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.querySelectorAll('.delete-btn').forEach(function (btn) {
        btn.addEventListener('click', function () { openModal(btn.dataset.deleteUrl, btn.dataset.deleteTitle); });
    });
    document.getElementById('modalCancel').addEventListener('click', closeModal);
    document.getElementById('modalBackdrop').addEventListener('click', closeModal);
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeModal(); });

    // auto-hide flash message
    var flash = document.getElementById('flash');
    if (flash) setTimeout(function () { if (flash.parentNode) flash.remove(); }, 6000);

    sortItems();
    apply();
})();
</script>
@endsection