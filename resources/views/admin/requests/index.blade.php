@extends('layouts.admin')

@section('content')
@php
    $pendingCount = $requests->where('status', 'pending')->count();
    $hasFilters   = request('search') || request('status') || request('document_type');

    $field = 'w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-sm text-slate-800 placeholder:text-slate-400 focus:ring-4 focus:ring-emerald-500/15 focus:border-emerald-500 outline-none transition';

    $docTypes = [
        'Barangay Residency Certificate',
        'Barangay Indigency Certificate',
        'Barangay Clearance Certification',
        'Good Moral Certificate',
        'Barangay Business Clearance',
    ];

    $statusOptions = [
        'pending' => 'Pending', 'accepted' => 'Accepted', 'ready_for_pickup' => 'Ready for pickup',
        'completed' => 'Completed', 'denied' => 'Denied',
    ];

    $statusConfig = [
        'pending'          => ['badge' => 'bg-amber-50 text-amber-700 ring-amber-200',       'dot' => 'bg-amber-500',   'bar' => 'border-l-amber-400',   'label' => 'Pending'],
        'accepted'         => ['badge' => 'bg-sky-50 text-sky-700 ring-sky-200',             'dot' => 'bg-sky-500',     'bar' => 'border-l-sky-500',     'label' => 'Accepted'],
        'ready_for_pickup' => ['badge' => 'bg-emerald-50 text-emerald-700 ring-emerald-200', 'dot' => 'bg-emerald-500', 'bar' => 'border-l-emerald-500', 'label' => 'Ready for pickup'],
        'completed'        => ['badge' => 'bg-slate-800 text-white ring-slate-800',          'dot' => 'bg-slate-300',   'bar' => 'border-l-slate-500',   'label' => 'Completed'],
        'denied'           => ['badge' => 'bg-rose-50 text-rose-700 ring-rose-200',          'dot' => 'bg-rose-500',    'bar' => 'border-l-rose-500',    'label' => 'Denied'],
    ];

    // [route suffix, link|post, label, icon, gradient, confirm message]
    $actionsByStatus = [
        'pending' => [
            ['accept',   'post', 'Accept',   'fa-check',        'from-emerald-500 to-green-600', null],
            ['deny',     'post', 'Deny',     'fa-times',        'from-rose-500 to-red-600',      'Deny this request?'],
        ],
        'accepted' => [
            ['print',    'link', 'Print',    'fa-print',        'from-blue-500 to-indigo-600',   null],
            ['ready',    'post', 'Ready',    'fa-box-open',     'from-emerald-500 to-teal-600',  null],
        ],
        'ready_for_pickup' => [
            ['complete', 'post', 'Complete', 'fa-check-double',  'from-green-500 to-emerald-600', null],
            ['print',    'link', 'Re-print', 'fa-print',        'from-slate-700 to-slate-900',   null],
        ],
        'completed' => [
            ['print',    'link', 'Print',    'fa-print',        'from-slate-700 to-slate-900',   null],
        ],
        'denied' => [],
    ];

    $stats = [
        ['Total',     'fa-layer-group',   $requests->count(),                                       '',                 'from-slate-500 to-slate-800',     'shadow-slate-500/25'],
        ['Pending',   'fa-hourglass-half', $pendingCount,                                           'pending',          'from-amber-400 to-orange-600',    'shadow-amber-500/25'],
        ['Accepted',  'fa-check',         $requests->where('status', 'accepted')->count(),         'accepted',         'from-sky-400 to-blue-700',        'shadow-sky-500/25'],
        ['Ready',     'fa-box-open',      $requests->where('status', 'ready_for_pickup')->count(), 'ready_for_pickup', 'from-emerald-500 to-emerald-800', 'shadow-emerald-500/25'],
        ['Completed', 'fa-check-double',  $requests->where('status', 'completed')->count(),        'completed',        'from-teal-500 to-cyan-800',       'shadow-teal-500/25'],
        ['Denied',    'fa-ban',           $requests->where('status', 'denied')->count(),           'denied',           'from-rose-400 to-rose-700',       'shadow-rose-500/25'],
    ];

    $initials = fn($r) => strtoupper(substr(optional($r)->first_name ?? 'X', 0, 1) . substr(optional($r)->last_name ?? 'X', 0, 1));
@endphp

<div class="bims-page min-h-screen bg-gradient-to-br from-slate-50 via-emerald-50/40 to-teal-50/30"
     x-data="{ open: false, msg: '', form: null,
               ask(f, m) { this.form = f; this.msg = m; this.open = true },
               go() { this.open = false; this.form.submit() } }"
     @keydown.escape.window="open = false">

    {{-- HEADER --}}
    <div class="bims-hero relative overflow-hidden bg-gradient-to-br from-emerald-700 via-emerald-800 to-teal-900">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image:radial-gradient(#fff 1px,transparent 1px);background-size:22px 22px"></div>
        <div class="absolute -top-24 -right-16 w-72 h-72 rounded-full pointer-events-none" style="background:radial-gradient(circle,rgba(110,231,183,.28),transparent 68%)"></div>
        <div class="absolute -bottom-28 left-1/4 w-72 h-72 rounded-full pointer-events-none" style="background:radial-gradient(circle,rgba(94,234,212,.2),transparent 68%)"></div>

        <div class="relative px-3 sm:px-6 lg:px-8 py-4 sm:py-5">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="h-11 w-11 sm:h-12 sm:w-12 flex items-center justify-center rounded-2xl bg-white/15 border border-white/25 text-white flex-shrink-0 shadow-lg shadow-black/10">
                        <i class="fas fa-file-alt text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <nav class="flex items-center gap-1.5 text-xs text-emerald-200/80">
                            <span>Dashboard</span><i class="fas fa-chevron-right text-[8px]"></i>
                            <span class="text-white font-medium">Document requests</span>
                        </nav>
                        <h1 class="bims-display text-xl sm:text-3xl text-white truncate">Requests management</h1>
                    </div>
                </div>

                @if($pendingCount > 0)
                    <a href="{{ route('admin.requests.index', ['status' => 'pending']) }}"
                       class="inline-flex items-center gap-2 px-3 py-2 bg-amber-400/25 hover:bg-amber-400/35 text-amber-50 rounded-xl text-xs sm:text-sm font-semibold border border-amber-300/40 transition flex-shrink-0">
                        <span class="relative flex h-2 w-2"><span class="bims-ping absolute inline-flex h-full w-full rounded-full bg-amber-300"></span><span class="relative inline-flex h-2 w-2 rounded-full bg-amber-300"></span></span>
                        {{ $pendingCount }} pending
                    </a>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-400/20 text-emerald-50 rounded-xl text-xs sm:text-sm font-semibold border border-emerald-300/30 flex-shrink-0">
                        <i class="fas fa-check-circle"></i> All clear
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- ALERTS --}}
    @if(session('success') || session('info') || session('error'))
        <div class="px-3 sm:px-6 lg:px-8 pt-4 space-y-2">
            @foreach ([['success','emerald','fa-check-circle',4000], ['info','sky','fa-info-circle',4000], ['error','rose','fa-exclamation-circle',5000]] as [$k, $c, $ic, $ms])
                @if(session($k))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, {{ $ms }})"
                         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:leave="transition ease-in duration-300" x-transition:leave-end="opacity-0 -translate-y-2"
                         class="flex items-center gap-3 px-4 py-3 bg-{{ $c }}-50 border border-{{ $c }}-200 border-l-4 border-l-{{ $c }}-500 rounded-xl shadow-sm">
                        <i class="fas {{ $ic }} text-{{ $c }}-600"></i>
                        <span class="text-sm font-medium text-{{ $c }}-800 flex-1">{{ session($k) }}</span>
                        <button @click="show = false" class="text-{{ $c }}-400 hover:text-{{ $c }}-600" aria-label="Dismiss"><i class="fas fa-times text-xs"></i></button>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    <div class="px-3 sm:px-6 lg:px-8 py-4 sm:py-6 space-y-4 sm:space-y-6">

        {{-- STATS (click para mag-filter) --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2.5 sm:gap-3">
            @foreach($stats as $i => $s)
                @php $active = (request('status') ?? '') === $s[3]; @endphp
                <a href="{{ route('admin.requests.index', array_filter(['status' => $s[3]])) }}"
                   style="--i:{{ $i }}"
                   class="bims-rise group relative overflow-hidden rounded-2xl bg-gradient-to-br {{ $s[4] }} px-4 py-3.5 text-white shadow-lg {{ $s[5] }} transition-all duration-300 hover:-translate-y-1 hover:shadow-xl {{ $active ? 'ring-4 ring-white/70 outline outline-2 outline-emerald-500' : '' }}">
                    <div class="absolute -top-8 -right-8 w-24 h-24 rounded-full bg-white/10"></div>
                    <i class="fas {{ $s[1] }} absolute -bottom-2 -right-1 text-5xl text-white/15 group-hover:scale-110 group-hover:-rotate-6 transition-transform duration-500" aria-hidden="true"></i>
                    <div class="relative">
                        <p class="text-xs font-semibold text-white/85">{{ $s[0] }}</p>
                        <p class="bims-display bims-figure text-3xl mt-0.5" data-count="{{ $s[2] }}">{{ $s[2] }}</p>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- FILTERS --}}
        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm p-3 sm:p-4">
            <form method="GET" action="{{ route('admin.requests.index') }}">
                <div class="grid grid-cols-12 gap-2.5 sm:gap-3 items-end">
                    <div class="col-span-12 lg:col-span-4">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Search</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, email, or request ID" class="{{ $field }} pl-9">
                        </div>
                    </div>
                    <div class="col-span-7 lg:col-span-3">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Document type</label>
                        <select name="document_type" onchange="this.form.submit()" class="{{ $field }} cursor-pointer">
                            <option value="">All documents</option>
                            @foreach($docTypes as $dt)
                                <option value="{{ $dt }}" @selected(request('document_type') == $dt)>{{ $dt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-5 lg:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                        <select name="status" onchange="this.form.submit()" class="{{ $field }} cursor-pointer">
                            <option value="">All status</option>
                            @foreach($statusOptions as $val => $text)
                                <option value="{{ $val }}" @selected(request('status') == $val)>{{ $text }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-12 lg:col-span-3 flex items-center gap-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-gradient-to-br from-emerald-500 to-emerald-700 hover:from-emerald-600 hover:to-emerald-800 text-white text-sm font-semibold rounded-xl shadow-md shadow-emerald-600/25 transition active:scale-95 flex items-center justify-center gap-1.5">
                            <i class="fas fa-search text-[11px]"></i> Filter
                        </button>
                        <a href="{{ route('admin.requests.index') }}" class="flex-1 px-4 py-2 bg-white border border-slate-200 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-50 transition active:scale-95 flex items-center justify-center gap-1.5">
                            <i class="fas fa-redo text-[11px]"></i> Reset
                        </a>
                    </div>
                </div>

                @if($hasFilters)
                    <div class="mt-3 pt-3 border-t border-slate-100 flex flex-wrap items-center gap-1.5">
                        <span class="text-xs font-semibold text-slate-400 mr-1">Active filters:</span>
                        @foreach ([['search','fa-search','"'.request('search').'"'], ['document_type','fa-file-alt',request('document_type')], ['status','fa-tag',ucwords(str_replace('_',' ',(string) request('status')))]] as [$key, $ic, $txt])
                            @if(request($key))
                                <a href="{{ route('admin.requests.index', request()->except($key)) }}" title="Remove filter"
                                   class="inline-flex items-center gap-1.5 bg-emerald-50 hover:bg-rose-50 text-emerald-700 hover:text-rose-600 text-xs font-medium pl-2 pr-1.5 py-0.5 rounded-full ring-1 ring-inset ring-emerald-200 hover:ring-rose-200 transition">
                                    <i class="fas {{ $ic }} text-[9px]"></i> {{ $txt }} <i class="fas fa-times text-[9px] opacity-60"></i>
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif
            </form>
        </div>

        {{-- REQUESTS --}}
        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 bg-gradient-to-r from-emerald-50 via-white to-white flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-700 flex items-center justify-center text-white shadow shadow-emerald-500/30"><i class="fas fa-list text-xs"></i></div>
                    <div>
                        <h2 class="bims-display text-base sm:text-lg text-slate-800">Document requests</h2>
                        <p class="text-xs text-slate-500 leading-tight">{{ $requests->count() }} {{ $requests->count() === 1 ? 'request' : 'requests' }} found</p>
                    </div>
                </div>
                <a href="{{ route('admin.requests.logs') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-semibold border border-slate-200 transition whitespace-nowrap">
                    <i class="fas fa-history text-[11px]"></i> View logs
                </a>
            </div>

            @if($requests->isEmpty())
                <div class="px-4 py-16 text-center">
                    <div class="h-16 w-16 bg-gradient-to-br from-slate-50 to-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4 ring-1 ring-slate-200 rotate-3">
                        <i class="fas fa-folder-open text-2xl text-slate-300"></i>
                    </div>
                    <h3 class="text-sm font-semibold text-slate-700">No requests found</h3>
                    <p class="text-xs text-slate-500 mt-1">{{ $hasFilters ? 'Try changing or clearing your filters.' : "Residents' document requests will appear here." }}</p>
                    @if($hasFilters)
                        <a href="{{ route('admin.requests.index') }}" class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-xl transition"><i class="fas fa-redo text-[10px]"></i> Clear filters</a>
                    @endif
                </div>
            @else

                {{-- PHONE / TABLET: CARDS --}}
                <div class="lg:hidden p-3 space-y-3">
                    @foreach($requests as $req)
                        @php $cfg = $statusConfig[$req->status] ?? $statusConfig['pending']; $r = $req->resident; @endphp
                        <div style="--i:{{ $loop->index }}" class="bims-rise rounded-2xl border border-slate-200 border-l-4 {{ $cfg['bar'] }} bg-white p-3.5 shadow-sm">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="h-10 w-10 rounded-full overflow-hidden flex-shrink-0 ring-2 ring-white shadow flex items-center justify-center bg-gradient-to-br from-emerald-400 to-teal-600">
                                        @if($r && $r->profile_photo_url)
                                            <img src="{{ $r->profile_photo_url }}" alt="{{ $r->first_name }}" loading="lazy" class="h-full w-full object-cover">
                                        @else
                                            <span class="text-white text-xs font-bold">{{ $initials($r) }}</span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-slate-800 leading-tight truncate">{{ $r->last_name ?? 'N/A' }}, {{ $r->first_name ?? '' }}</p>
                                        <p class="text-xs text-slate-400 truncate">{{ $r->email ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold ring-1 ring-inset whitespace-nowrap flex-shrink-0 {{ $cfg['badge'] }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $cfg['dot'] }}"></span>{{ $cfg['label'] }}
                                </span>
                            </div>

                            <div class="mt-3 pt-3 border-t border-dashed border-slate-200">
                                <div class="flex items-baseline justify-between gap-2">
                                    <p class="text-sm font-semibold text-slate-700 leading-tight">{{ $req->document_type }}</p>
                                    <span class="text-[11px] text-slate-400 font-mono flex-shrink-0">#REQ-{{ str_pad($req->id, 4, '0', STR_PAD_LEFT) }}</span>
                                </div>
                                <p class="text-xs text-slate-600 mt-1 line-clamp-2">{{ $req->purpose }}</p>
                                <p class="text-[11px] text-slate-400 mt-1 flex items-center gap-1" title="{{ $req->created_at->format('M d, Y g:i A') }}">
                                    <i class="far fa-clock text-[9px]"></i> {{ $req->created_at->diffForHumans() }}
                                </p>
                            </div>

                            <div class="mt-3 flex items-center gap-2">
                                @forelse($actionsByStatus[$req->status] ?? [] as $a)
                                    @if($a[1] === 'link')
                                        <a href="{{ route('admin.requests.'.$a[0], $req->id) }}" target="_blank"
                                           class="flex-1 h-9 bg-gradient-to-r {{ $a[4] }} text-white rounded-xl text-xs font-bold transition active:scale-95 flex items-center justify-center gap-1.5 shadow-sm">
                                            <i class="fas {{ $a[3] }} text-[10px]"></i> {{ $a[2] }}
                                        </a>
                                    @else
                                        <form action="{{ route('admin.requests.'.$a[0], $req->id) }}" method="POST" class="flex-1">
                                            @csrf
                                            <button type="{{ $a[5] ? 'button' : 'submit' }}" @if($a[5]) @click="ask($el.closest('form'), '{{ $a[5] }}')" @endif
                                                    class="w-full h-9 bg-gradient-to-r {{ $a[4] }} text-white rounded-xl text-xs font-bold transition active:scale-95 flex items-center justify-center gap-1.5 shadow-sm">
                                                <i class="fas {{ $a[3] }} text-[10px]"></i> {{ $a[2] }}
                                            </button>
                                        </form>
                                    @endif
                                @empty
                                    <div class="flex-1 flex items-center justify-center gap-1.5 h-9 bg-rose-50 text-rose-600 rounded-xl text-xs font-semibold border border-rose-200">
                                        <i class="fas fa-times-circle text-[10px]"></i> Denied
                                    </div>
                                @endforelse

                                @if($req->status !== 'pending')
                                    <form action="{{ route('admin.requests.destroy', $req->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="button" @click="ask($el.closest('form'), 'Delete this request permanently?')" title="Delete"
                                                class="h-9 w-9 bg-slate-100 hover:bg-rose-100 text-slate-500 hover:text-rose-600 rounded-xl transition border border-slate-200 hover:border-rose-300 flex items-center justify-center">
                                            <i class="fas fa-trash text-[11px]"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- DESKTOP: TABLE --}}
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[1000px]">
                        <thead class="bg-slate-50/80 border-b border-slate-200">
                            <tr class="text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                <th class="pl-5 pr-3 py-3 w-14 text-center">#</th>
                                <th class="px-4 py-3">Resident</th>
                                <th class="px-4 py-3">Document</th>
                                <th class="px-4 py-3">Purpose</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($requests as $index => $req)
                                @php $cfg = $statusConfig[$req->status] ?? $statusConfig['pending']; $r = $req->resident; @endphp
                                <tr style="--i:{{ $index }}" class="bims-row group hover:bg-emerald-50/50 transition-colors">
                                    <td class="pl-4 pr-3 py-3 text-xs text-slate-400 font-mono text-center border-l-4 {{ $cfg['bar'] }}">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>

                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 rounded-full overflow-hidden flex-shrink-0 ring-2 ring-white shadow flex items-center justify-center bg-gradient-to-br from-emerald-400 to-teal-600 group-hover:scale-105 transition-transform">
                                                @if($r && $r->profile_photo_url)
                                                    <img src="{{ $r->profile_photo_url }}" alt="{{ $r->first_name }}" loading="lazy" class="h-full w-full object-cover">
                                                @else
                                                    <span class="text-white text-xs font-bold">{{ $initials($r) }}</span>
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-slate-800 leading-tight">{{ $r->last_name ?? 'N/A' }}, {{ $r->first_name ?? '' }}</p>
                                                <p class="text-xs text-slate-400">{{ $r->email ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3">
                                        <p class="text-sm font-semibold text-slate-700 leading-tight">{{ $req->document_type }}</p>
                                        <p class="text-xs text-slate-400 font-mono">#REQ-{{ str_pad($req->id, 4, '0', STR_PAD_LEFT) }}</p>
                                    </td>

                                    <td class="px-4 py-3 max-w-[230px]">
                                        <p class="text-xs text-slate-600 truncate" title="{{ $req->purpose }}">{{ $req->purpose }}</p>
                                        <p class="text-[11px] text-slate-400 flex items-center gap-1" title="{{ $req->created_at->format('M d, Y g:i A') }}">
                                            <i class="far fa-clock text-[9px]"></i> {{ $req->created_at->diffForHumans() }}
                                        </p>
                                    </td>

                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold ring-1 ring-inset whitespace-nowrap {{ $cfg['badge'] }}">
                                            <span class="h-1.5 w-1.5 rounded-full {{ $cfg['dot'] }}"></span>{{ $cfg['label'] }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-center gap-2">
                                            @foreach($actionsByStatus[$req->status] ?? [] as $a)
                                                @if($a[1] === 'link')
                                                    <a href="{{ route('admin.requests.'.$a[0], $req->id) }}" target="_blank"
                                                       class="h-8 px-3 bg-gradient-to-r {{ $a[4] }} text-white rounded-lg text-xs font-bold transition hover:shadow-md hover:-translate-y-px active:scale-95 flex items-center gap-1.5 shadow-sm">
                                                        <i class="fas {{ $a[3] }} text-[10px]"></i> {{ $a[2] }}
                                                    </a>
                                                @else
                                                    <form action="{{ route('admin.requests.'.$a[0], $req->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="{{ $a[5] ? 'button' : 'submit' }}" @if($a[5]) @click="ask($el.closest('form'), '{{ $a[5] }}')" @endif
                                                                class="h-8 px-3 bg-gradient-to-r {{ $a[4] }} text-white rounded-lg text-xs font-bold transition hover:shadow-md hover:-translate-y-px active:scale-95 flex items-center gap-1.5 shadow-sm">
                                                            <i class="fas {{ $a[3] }} text-[10px]"></i> {{ $a[2] }}
                                                        </button>
                                                    </form>
                                                @endif
                                            @endforeach

                                            @if($req->status === 'completed')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-slate-100 text-slate-500 rounded-lg text-xs font-semibold border border-slate-200"><i class="fas fa-lock text-[10px]"></i> Locked</span>
                                            @elseif($req->status === 'denied')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-rose-50 text-rose-600 rounded-lg text-xs font-semibold border border-rose-200"><i class="fas fa-times-circle text-[10px]"></i> Denied</span>
                                            @endif

                                            @if($req->status !== 'pending')
                                                <form action="{{ route('admin.requests.destroy', $req->id) }}" method="POST" class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="button" @click="ask($el.closest('form'), 'Delete this request permanently?')" title="Delete"
                                                            class="h-8 w-8 rounded-lg bg-slate-100 hover:bg-rose-100 text-slate-500 hover:text-rose-600 flex items-center justify-center transition border border-slate-200 hover:border-rose-300">
                                                        <i class="fas fa-trash text-[10px]"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- CONFIRM MODAL --}}
    <div x-cloak x-show="open" class="fixed inset-0 z-[9999] flex items-center justify-center p-4" role="dialog" aria-modal="true">
        <div x-show="open" x-transition.opacity class="absolute inset-0 bg-slate-900/60" @click="open = false"></div>
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-2" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl p-5 text-center">
            <div class="mx-auto h-12 w-12 rounded-full bg-rose-50 ring-8 ring-rose-50/60 flex items-center justify-center text-rose-500 mb-3"><i class="fas fa-exclamation-triangle"></i></div>
            <h3 class="bims-display text-lg text-slate-800">Are you sure?</h3>
            <p class="text-sm text-slate-500 mt-1" x-text="msg"></p>
            <div class="mt-5 flex gap-2">
                <button type="button" @click="open = false" class="flex-1 px-4 py-2 bg-white border border-slate-200 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-50 transition">Cancel</button>
                <button type="button" @click="go()" class="flex-1 px-4 py-2 bg-gradient-to-r from-rose-500 to-red-600 hover:from-rose-600 hover:to-red-700 text-white text-sm font-semibold rounded-xl shadow-md shadow-rose-500/25 transition active:scale-95">Yes, continue</button>
            </div>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,800&family=Figtree:wght@400;500;600;700&display=swap');

    [x-cloak] { display: none !important; }
    .bims-page { font-family: 'Figtree', ui-sans-serif, system-ui, sans-serif; }
    .bims-display { font-family: 'Bricolage Grotesque', 'Figtree', ui-sans-serif, system-ui, sans-serif; font-weight: 800; letter-spacing: -0.02em; line-height: 1.1; }
    .bims-figure { font-variant-numeric: tabular-nums; letter-spacing: -0.03em; }

    @keyframes bimsRise { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }
    .bims-rise { animation: bimsRise .5s cubic-bezier(.22, 1, .36, 1) both; animation-delay: calc(min(var(--i, 0), 10) * 45ms); }
    .bims-row  { animation: bimsRise .4s cubic-bezier(.22, 1, .36, 1) both; animation-delay: calc(min(var(--i, 0), 12) * 30ms); }

    @keyframes bimsPing { 75%, 100% { transform: scale(2.4); opacity: 0; } }
    .bims-ping { animation: bimsPing 1.8s cubic-bezier(0, 0, .2, 1) infinite; opacity: .6; }

    @media (prefers-reduced-motion: reduce) {
        .bims-rise, .bims-row, .bims-ping { animation: none; }
    }
</style>

<script>
    // Count-up sa stat cards
    (function () {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        document.querySelectorAll('[data-count]').forEach(function (el) {
            var to = parseInt(el.dataset.count, 10) || 0;
            if (to < 2) return;
            var start = performance.now(), dur = 900;
            (function tick(now) {
                var p = Math.min((now - start) / dur, 1);
                el.textContent = Math.round(to * (1 - Math.pow(1 - p, 3)));
                if (p < 1) requestAnimationFrame(tick);
            })(start);
        });
    })();
</script>
@endsection