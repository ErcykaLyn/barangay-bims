@extends('layouts.admin')

@section('content')
@php
    $hasFilter  = request()->filled('search') || request()->filled('zone');
    $thisMonth  = $accounts->filter(fn($a) => $a->created_at && $a->created_at->isCurrentMonth())->count();
    $zoneCount  = $accounts->pluck('zone_purok')->filter()->unique()->count();
    $latest     = $accounts->sortByDesc('created_at')->first();
@endphp

<div class="min-h-screen bg-gradient-to-br from-slate-50 via-emerald-50/30 to-slate-100 pb-12">

    {{-- HEADER --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-emerald-700 via-emerald-800 to-emerald-950 shadow-lg">
        <div class="absolute -top-16 -right-16 w-64 h-64 bg-emerald-400/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-20 -left-10 w-72 h-72 bg-teal-400/10 rounded-full blur-3xl"></div>

        {{-- extra bottom padding: dito papatong ang stats cards --}}
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 sm:pt-10 pb-16 sm:pb-20">
            <div class="flex items-center gap-4">
                <div class="h-14 w-14 rounded-2xl bg-white/10 backdrop-blur border border-white/20 flex items-center justify-center text-white shadow-xl flex-shrink-0">
                    <i class="fas fa-user-check text-xl"></i>
                </div>
                <div>
                    <nav class="flex items-center gap-1.5 text-[10px] font-bold text-emerald-200/80 uppercase tracking-wider mb-1">
                        <span>Admin</span>
                        <i class="fas fa-chevron-right text-[8px]"></i>
                        <span class="text-white">Registered Accounts</span>
                    </nav>
                    <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Registered Accounts</h1>
                    <p class="text-sm text-emerald-100/80 mt-1">Residents who registered their own accounts and were approved</p>
                </div>
            </div>
        </div>
    </div>

    {{-- relative z-10 + negative margin: tamang pagpatong ng cards sa header (hindi na natatakpan) --}}
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 sm:-mt-12">

        {{-- ALERTS --}}
        @if(session('success'))
            <div class="mb-4 flex items-center gap-3 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-xl shadow-sm">
                <i class="fas fa-check-circle text-emerald-600"></i>
                <span class="text-sm font-semibold text-emerald-800">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 flex items-center gap-3 px-4 py-3 bg-rose-50 border border-rose-200 rounded-xl shadow-sm">
                <i class="fas fa-exclamation-circle text-rose-600"></i>
                <span class="text-sm font-semibold text-rose-800">{{ session('error') }}</span>
            </div>
        @endif

        {{-- STATS CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-2xl border border-slate-200 border-t-4 border-t-emerald-500 p-5 shadow-md hover:shadow-lg transition">
                <div class="flex items-center justify-between mb-3">
                    <div class="h-11 w-11 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white shadow-md shadow-emerald-500/30">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <span class="text-[10px] font-black text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full uppercase tracking-wider">Active</span>
                </div>
                <p class="text-3xl font-black text-slate-800 leading-none">{{ number_format($totalAccounts) }}</p>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mt-1.5">Active Accounts</p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 border-t-4 border-t-indigo-500 p-5 shadow-md hover:shadow-lg transition">
                <div class="flex items-center justify-between mb-3">
                    <div class="h-11 w-11 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center text-white shadow-md shadow-indigo-500/30">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-2 py-1 rounded-full uppercase tracking-wider">{{ now()->format('M Y') }}</span>
                </div>
                <p class="text-3xl font-black text-slate-800 leading-none">{{ number_format($thisMonth) }}</p>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mt-1.5">Registered This Month</p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 border-t-4 border-t-amber-500 p-5 shadow-md hover:shadow-lg transition">
                <div class="flex items-center justify-between mb-3">
                    <div class="h-11 w-11 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center text-white shadow-md shadow-amber-500/30">
                        <i class="fas fa-location-dot"></i>
                    </div>
                    <span class="text-[10px] font-black text-amber-600 bg-amber-50 px-2 py-1 rounded-full uppercase tracking-wider">Zones</span>
                </div>
                <p class="text-3xl font-black text-slate-800 leading-none">{{ number_format($zoneCount) }}</p>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mt-1.5">
                    @if($latest)
                        Latest: {{ $latest->created_at->diffForHumans() }}
                    @else
                        Zones with accounts
                    @endif
                </p>
            </div>
        </div>

        {{-- SEARCH & FILTER --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-6">
            <form method="GET" action="{{ route('admin.registered-accounts.index') }}" class="flex flex-col md:flex-row gap-3">
                <div class="flex-1">
                    <div class="relative group">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm group-focus-within:text-emerald-500 transition-colors"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search by name or email..."
                               class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition text-sm bg-slate-50/50 focus:bg-white">
                    </div>
                </div>
                <div class="w-full md:w-48">
                    <select name="zone" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 bg-slate-50/50 focus:bg-white text-sm cursor-pointer">
                        <option value="">All Zones</option>
                        @foreach($zones as $zone)
                            <option value="{{ $zone }}" {{ request('zone') == $zone ? 'selected' : '' }}>{{ $zone }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl transition flex items-center gap-2 text-sm font-bold shadow-sm shadow-emerald-500/20">
                        <i class="fas fa-filter text-xs"></i> Filter
                    </button>
                    <a href="{{ route('admin.registered-accounts.index') }}" class="border border-slate-300 text-slate-600 px-4 py-2.5 rounded-xl hover:bg-slate-50 transition flex items-center gap-2 text-sm font-bold">
                        <i class="fas fa-undo-alt text-xs"></i> Reset
                    </a>
                </div>
            </form>

            @if($hasFilter)
                <div class="flex flex-wrap items-center gap-2 mt-3 pt-3 border-t border-slate-100">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Filters:</span>
                    @if(request()->filled('search'))
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 text-[11px] font-bold">
                            <i class="fas fa-search text-[9px]"></i> "{{ request('search') }}"
                        </span>
                    @endif
                    @if(request()->filled('zone'))
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200 text-[11px] font-bold">
                            <i class="fas fa-location-dot text-[9px]"></i> {{ request('zone') }}
                        </span>
                    @endif
                </div>
            @endif
        </div>

        {{-- TABLE CARD --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap gap-3 justify-between items-center bg-gradient-to-r from-emerald-50/50 via-white to-white">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white shadow-sm">
                        <i class="fas fa-list-ul text-xs"></i>
                    </div>
                    <div>
                        <h2 class="font-black text-slate-800 text-sm tracking-tight">Registered Account List</h2>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Barangay Bactad East</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider inline-flex items-center gap-1.5 bg-slate-100 px-3 py-1.5 rounded-lg">
                    <i class="fas fa-eye text-[9px]"></i>
                    {{ $accounts->count() }} showing
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/80">
                        <tr>
                            <th class="px-4 py-3.5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest w-12">#</th>
                            <th class="px-4 py-3.5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Name</th>
                            <th class="px-4 py-3.5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Contact</th>
                            <th class="px-4 py-3.5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Zone</th>
                            <th class="px-4 py-3.5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Valid ID</th>
                            <th class="px-4 py-3.5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Registered On</th>
                            <th class="px-4 py-3.5 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                            <th class="px-4 py-3.5 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 bg-white">
                        @forelse($accounts as $r)
                        <tr class="hover:bg-emerald-50/40 transition duration-150 group">
                            <td class="px-4 py-4 whitespace-nowrap text-xs text-slate-400 font-mono font-bold">
                                {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-full overflow-hidden flex-shrink-0 ring-2 ring-white shadow-sm flex items-center justify-center bg-gradient-to-br from-emerald-400 to-emerald-600">
                                        @if($r->profile_photo_url)
                                            <img src="{{ $r->profile_photo_url }}" alt="" class="h-full w-full object-cover">
                                        @else
                                            <span class="text-white text-[10px] font-black tracking-wider">
                                                {{ strtoupper(substr($r->first_name, 0, 1)) }}{{ strtoupper(substr($r->last_name, 0, 1)) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800 group-hover:text-emerald-700 transition uppercase">
                                            {{ $r->last_name }}, {{ $r->first_name }} {{ $r->middle_name ?? '' }}
                                        </p>
                                        <p class="text-[10px] text-slate-400 font-medium">
                                            {{ $r->civil_status ?? 'Resident' }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <p class="inline-flex items-center gap-1.5 text-sm text-slate-700">
                                    <i class="fas fa-envelope text-slate-400 text-xs"></i> {{ $r->email }}
                                </p>
                                @if($r->contact_number)
                                    <p class="flex items-center gap-1.5 text-[11px] text-slate-500 mt-0.5">
                                        <i class="fas fa-phone text-emerald-500 text-[10px]"></i> {{ $r->contact_number }}
                                    </p>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-slate-100 text-slate-700 border border-slate-200">
                                    <i class="fas fa-location-dot text-slate-400 text-[9px]"></i> {{ $r->zone_purok ?? '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($r->valid_id)
                                    <a href="{{ $r->valid_id_url }}" target="_blank" rel="noopener"
                                       class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-black bg-blue-50 text-blue-700 border border-blue-100 hover:bg-blue-600 hover:text-white transition"
                                       title="Buksan ang valid ID">
                                        <i class="fas fa-id-card text-[10px]"></i> {{ $r->id_type ?: 'View ID' }}
                                    </a>
                                @else
                                    <span class="text-slate-300 text-sm">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <p class="text-sm font-semibold text-slate-700">{{ $r->created_at->format('M d, Y') }}</p>
                                <p class="text-[10px] text-slate-400">{{ $r->created_at->diffForHumans() }}</p>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase tracking-wider">
                                    <i class="fas fa-check-circle text-[9px]"></i> Active
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                <div class="inline-flex items-center gap-1">
                                    <button type="button" onclick="openAccountModal(this)"
                                            data-id="{{ $r->id }}"
                                            data-name="{{ $r->first_name }} {{ $r->last_name }}"
                                            data-email="{{ $r->email }}"
                                            data-contact="{{ $r->contact_number }}"
                                            title="Edit account (email / password)"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition">
                                        <i class="fas fa-key text-[9px]"></i> Edit account
                                    </button>
                                    <a href="{{ route('admin.residents.edit', $r->id) }}" title="Edit profile"
                                       class="h-8 w-8 flex items-center justify-center text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition">
                                        <i class="fas fa-pen text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                    <div class="h-20 w-20 rounded-2xl bg-gradient-to-br from-emerald-50 to-emerald-100 flex items-center justify-center mb-5 shadow-inner">
                                        <i class="fas fa-user-check text-emerald-400 text-3xl"></i>
                                    </div>
                                    <h3 class="text-base font-black text-slate-800 mb-1">
                                        {{ $hasFilter ? 'No accounts match your filters' : 'No registered accounts yet' }}
                                    </h3>
                                    <p class="text-xs text-slate-400 mb-4 leading-relaxed">
                                        @if($hasFilter)
                                            Try a different name, email, or zone.
                                        @else
                                            Residents who self-register via the Registration Form and get approved will appear here.
                                        @endif
                                    </p>
                                    @if($hasFilter)
                                        <a href="{{ route('admin.registered-accounts.index') }}" class="inline-flex items-center gap-2 border border-slate-300 text-slate-600 hover:bg-slate-50 px-4 py-2 rounded-xl text-xs font-bold transition">
                                            <i class="fas fa-undo-alt"></i> Reset filters
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ============ EDIT ACCOUNT MODAL ============ --}}
<div id="accountModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-emerald-50 to-white flex items-center justify-between">
            <div class="flex items-center gap-3 min-w-0">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white shadow">
                    <i class="fas fa-key"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="text-sm font-black text-slate-800">Edit account</h3>
                    <p id="accountModalName" class="text-[11px] text-slate-500 truncate"></p>
                </div>
            </div>
            <button type="button" onclick="closeAccountModal()" class="text-slate-400 hover:text-slate-600" aria-label="Close"><i class="fas fa-times"></i></button>
        </div>

        <form id="accountForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="account_id" id="accountId" value="{{ old('account_id') }}">

            @if($errors->any() && old('account_id'))
                <div class="rounded-lg bg-rose-50 border border-rose-200 px-3 py-2">
                    <ul class="list-disc list-inside text-xs text-rose-700 space-y-0.5">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1.5">Email <span class="text-rose-500">*</span></label>
                <input type="email" name="email" id="accountEmail" required
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none">
                <p class="text-[10px] text-slate-400 mt-1">Used for login. Must be unique.</p>
            </div>

            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1.5">Contact number</label>
                <input type="text" name="contact_number" id="accountContact" placeholder="09XX XXX XXXX"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1.5">New password</label>
                    <input type="password" name="password" autocomplete="new-password" placeholder="••••••••"
                           class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1.5">Confirm</label>
                    <input type="password" name="password_confirmation" autocomplete="new-password" placeholder="••••••••"
                           class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none">
                </div>
            </div>
            <p class="text-[10px] text-slate-400 -mt-2"><i class="fas fa-info-circle mr-1"></i>Leave password blank to keep the current one (min. 8 characters if changed).</p>

            <div class="flex items-center gap-3 pt-2">
                <button type="button" onclick="closeAccountModal()" class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-bold rounded-xl transition">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-500/20 transition">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
    const accountUrlTemplate = "{{ route('admin.residents.account.update', ['id' => '__ID__']) }}";

    function showAccountModal(id, name, email, contact) {
        document.getElementById('accountForm').action = accountUrlTemplate.replace('__ID__', id);
        document.getElementById('accountId').value = id;
        document.getElementById('accountModalName').textContent = name;
        document.getElementById('accountEmail').value = email || '';
        document.getElementById('accountContact').value = contact || '';
        const m = document.getElementById('accountModal');
        m.classList.remove('hidden');
        m.classList.add('flex');
    }

    function openAccountModal(btn) {
        showAccountModal(btn.dataset.id, btn.dataset.name, btn.dataset.email, btn.dataset.contact);
    }

    function closeAccountModal() {
        const m = document.getElementById('accountModal');
        m.classList.add('hidden');
        m.classList.remove('flex');
    }

    document.getElementById('accountModal').addEventListener('click', function (e) {
        if (e.target === this) closeAccountModal();
    });

    @if($errors->any() && old('account_id'))
        // May validation error: buksan ulit ang modal at ibalik ang tinype
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.querySelector('[data-id="{{ old('account_id') }}"]');
            showAccountModal(
                "{{ old('account_id') }}",
                btn ? btn.dataset.name : '',
                @js(old('email')),
                @js(old('contact_number'))
            );
        });
    @endif
</script>
@endsection