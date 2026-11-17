@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto pt-3 sm:pt-6 pb-8 sm:pb-12 px-3 sm:px-6 space-y-4 sm:space-y-6">

    {{-- ============ HERO ============ --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-700 via-indigo-800 to-purple-900 shadow-xl">
        <div class="absolute -top-16 -right-16 w-48 h-48 bg-indigo-400/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-20 -left-10 w-56 h-56 bg-purple-400/15 rounded-full blur-3xl"></div>

        <div class="relative p-4 sm:p-6 text-white">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="h-11 w-11 sm:h-14 sm:w-14 rounded-xl bg-white/15 backdrop-blur border border-white/30 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-key text-base sm:text-xl"></i>
                    </div>
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-white/20 border border-white/30 mb-1">
                            <span class="h-1.5 w-1.5 rounded-full bg-indigo-300 animate-pulse"></span>
                            <span class="text-[8px] sm:text-[9px] text-white font-black uppercase tracking-widest">Admin Panel</span>
                        </div>
                        <h2 class="text-lg sm:text-2xl font-black tracking-tight leading-tight">Password Reset Requests</h2>
                        <p class="text-indigo-100/90 text-[10px] sm:text-xs mt-0.5">Review and approve resident password reset requests</p>
                    </div>
                </div>

                @if($pendingCount > 0)
                    <div class="flex items-center gap-2 px-3.5 py-2 bg-amber-400/20 border border-amber-300/40 rounded-xl self-start sm:self-center">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-300 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-400"></span>
                        </span>
                        <span class="text-[10px] sm:text-xs font-bold text-amber-100">{{ $pendingCount }} {{ $pendingCount === 1 ? 'request needs' : 'requests need' }} review</span>
                    </div>
                @else
                    <div class="flex items-center gap-2 px-3.5 py-2 bg-emerald-400/20 border border-emerald-300/40 rounded-xl self-start sm:self-center">
                        <i class="fas fa-check-circle text-emerald-300 text-xs"></i>
                        <span class="text-[10px] sm:text-xs font-bold text-emerald-100">All caught up</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ============ STATS ============ --}}
    <div class="grid grid-cols-2 gap-2 sm:gap-3 sm:max-w-xl">
        <div class="bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl p-3.5 text-white shadow-lg shadow-amber-500/20">
            <div class="flex items-center justify-between mb-1">
                <p class="text-[9px] font-black uppercase tracking-widest opacity-90">Pending</p>
                <i class="fas fa-clock text-white/60 text-xs"></i>
            </div>
            <p class="text-2xl font-black mt-1">{{ $pendingCount }}</p>
            <p class="text-[9px] opacity-80 mt-0.5">Waiting for your action</p>
        </div>

        <div class="bg-white rounded-xl p-3.5 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-1">
                <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">Total</p>
                <i class="fas fa-list text-slate-300 text-xs"></i>
            </div>
            <p class="text-2xl font-black mt-1 text-slate-800">{{ $requests->total() }}</p>
            <p class="text-[9px] text-slate-400 mt-0.5">All-time requests</p>
        </div>
    </div>

    {{-- ============ TABLE ============ --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        <div class="px-4 py-3.5 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                <div class="flex items-center justify-between lg:justify-start gap-3">
                    <h3 class="text-[11px] sm:text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-list text-indigo-500"></i> All Requests
                    </h3>
                    <span class="text-[10px] text-slate-400 font-bold bg-slate-100 px-2.5 py-1 rounded-full">
                        {{ $requests->total() }} {{ Str::plural('request', $requests->total()) }}
                    </span>
                </div>

                {{-- Quick filter (applies to the rows on this page) --}}
                <div class="flex flex-col sm:flex-row gap-2">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                        <input type="text" id="reqSearch" placeholder="Search name or email..." autocomplete="off"
                               class="w-full sm:w-56 pl-8 pr-3 py-2 text-[11px] border border-slate-200 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 outline-none transition">
                    </div>
                    <div class="flex items-center gap-1 overflow-x-auto" id="statusChips">
                        @foreach(['all' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'completed' => 'Completed', 'rejected' => 'Rejected'] as $key => $label)
                            <button type="button" data-status="{{ $key }}"
                                    class="status-chip whitespace-nowrap px-2.5 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-wider border transition
                                           {{ $key === 'all' ? 'bg-indigo-600 border-indigo-600 text-white' : 'bg-white border-slate-200 text-slate-500 hover:border-indigo-300 hover:text-indigo-600' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left" id="reqTable">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Resident</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest hidden sm:table-cell">Reason</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest hidden md:table-cell">Submitted</th>
                        <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($requests as $req)
                        @php
                            $name = $req->resident->full_name ?? 'Unknown Resident';
                            $initial = strtoupper(substr($req->resident->first_name ?? $req->email, 0, 1));
                            $isPending = $req->status === 'pending';
                        @endphp
                        <tr class="req-row group transition cursor-pointer {{ $isPending ? 'bg-amber-50/40 hover:bg-amber-50' : 'hover:bg-slate-50/80' }}"
                            data-status="{{ $req->status }}"
                            data-search="{{ strtolower($name . ' ' . $req->email) }}"
                            onclick="window.location='{{ route('admin.password-resets.show', $req->id) }}'">
                            <td class="px-4 py-3.5 {{ $isPending ? 'border-l-4 border-amber-400' : 'border-l-4 border-transparent' }}">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-[11px] font-black flex-shrink-0 shadow-sm">
                                        {{ $initial }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-800 truncate">{{ $name }}</p>
                                        <p class="text-[10px] text-slate-400 truncate flex items-center gap-1">
                                            <i class="fas fa-envelope text-[8px]"></i> {{ $req->email }}
                                        </p>
                                        {{-- Mobile-only reason preview --}}
                                        <p class="sm:hidden text-[10px] text-slate-500 truncate mt-0.5">{{ $req->reason ?: 'No reason provided' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 hidden sm:table-cell">
                                <p class="text-[10px] {{ $req->reason ? 'text-slate-600' : 'text-slate-400 italic' }} line-clamp-2 max-w-xs leading-relaxed">
                                    {{ $req->reason ?: 'No reason provided' }}
                                </p>
                            </td>
                            <td class="px-4 py-3.5">
                                @if($req->status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-700 text-[8px] font-black uppercase tracking-wider">
                                        <span class="w-1.5 h-1.5 bg-amber-400 rounded-full animate-pulse"></span> Pending
                                    </span>
                                @elseif($req->status === 'approved')
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-[8px] font-black uppercase tracking-wider">
                                        <i class="fas fa-check-circle text-[8px]"></i> Approved
                                    </span>
                                @elseif($req->status === 'rejected')
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-rose-50 border border-rose-200 text-rose-700 text-[8px] font-black uppercase tracking-wider">
                                        <i class="fas fa-times-circle text-[8px]"></i> Rejected
                                    </span>
                                @elseif($req->status === 'completed')
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-blue-50 border border-blue-200 text-blue-700 text-[8px] font-black uppercase tracking-wider">
                                        <i class="fas fa-check-double text-[8px]"></i> Completed
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 hidden md:table-cell whitespace-nowrap">
                                <p class="text-[10px] font-bold text-slate-600">{{ $req->created_at->format('M d, Y') }}</p>
                                <p class="text-[9px] text-slate-400">{{ $req->created_at->format('h:i A') }} &middot; {{ $req->created_at->diffForHumans() }}</p>
                            </td>
                            <td class="px-4 py-3.5 text-center" onclick="event.stopPropagation()">
                                <a href="{{ route('admin.password-resets.show', $req->id) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 {{ $isPending ? 'bg-indigo-600 hover:bg-indigo-700 shadow-indigo-500/20' : 'bg-slate-700 hover:bg-slate-800 shadow-slate-500/20' }} text-white text-[9px] font-black uppercase tracking-wider rounded-lg transition shadow-sm hover:scale-105 active:scale-95">
                                    <i class="fas {{ $isPending ? 'fa-clipboard-check' : 'fa-eye' }} text-[8px]"></i> {{ $isPending ? 'Review' : 'View' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-16 text-center">
                                <div class="h-16 w-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-slate-50 to-slate-100 border border-slate-200 flex items-center justify-center">
                                    <i class="fas fa-inbox text-slate-300 text-2xl"></i>
                                </div>
                                <p class="text-sm font-black text-slate-700 mb-1">No requests yet</p>
                                <p class="text-[11px] text-slate-400">Password reset requests from residents will appear here.</p>
                            </td>
                        </tr>
                    @endforelse

                    {{-- Shown by the quick filter when nothing matches --}}
                    <tr id="noMatchRow" class="hidden">
                        <td colspan="5" class="px-4 py-12 text-center">
                            <i class="fas fa-search text-slate-300 text-2xl mb-2"></i>
                            <p class="text-xs font-black text-slate-600">No matching requests on this page</p>
                            <p class="text-[10px] text-slate-400 mt-1">Try another search or status.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
            <div class="px-4 py-3 border-t border-slate-100 bg-slate-50/50">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>

<script>
    (function () {
        var rows = Array.prototype.slice.call(document.querySelectorAll('.req-row'));
        var chips = Array.prototype.slice.call(document.querySelectorAll('.status-chip'));
        var search = document.getElementById('reqSearch');
        var noMatch = document.getElementById('noMatchRow');
        if (!rows.length) return;

        var activeStatus = 'all';
        var on  = ['bg-indigo-600', 'border-indigo-600', 'text-white'];
        var off = ['bg-white', 'border-slate-200', 'text-slate-500'];

        function apply() {
            var q = (search.value || '').trim().toLowerCase();
            var visible = 0;
            rows.forEach(function (row) {
                var okStatus = activeStatus === 'all' || row.dataset.status === activeStatus;
                var okText = !q || row.dataset.search.indexOf(q) !== -1;
                var show = okStatus && okText;
                row.classList.toggle('hidden', !show);
                if (show) visible++;
            });
            noMatch.classList.toggle('hidden', visible !== 0);
        }

        chips.forEach(function (chip) {
            chip.addEventListener('click', function () {
                activeStatus = chip.dataset.status;
                chips.forEach(function (c) {
                    var active = c === chip;
                    on.forEach(function (cl) { c.classList.toggle(cl, active); });
                    off.forEach(function (cl) { c.classList.toggle(cl, !active); });
                });
                apply();
            });
        });
        search.addEventListener('input', apply);
    })();
</script>
@endsection