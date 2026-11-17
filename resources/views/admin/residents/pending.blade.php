@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-slate-100 to-emerald-50/30 pb-20">

    {{-- HEADER --}}
    <div class="bg-white border-b border-slate-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4 min-w-0">
                    <a href="{{ route('admin.residents.index') }}" 
                       class="h-10 w-10 flex items-center justify-center rounded-xl bg-slate-100 hover:bg-emerald-100 text-slate-600 hover:text-emerald-600 transition-all flex-shrink-0 group">
                        <i class="fas fa-arrow-left text-sm group-hover:-translate-x-0.5 transition-transform"></i>
                    </a>
                    <div class="min-w-0">
                        <nav class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">
                            <a href="{{ route('admin.residents.index') }}" class="hover:text-emerald-600 transition">Residents</a>
                            <i class="fas fa-chevron-right text-[8px]"></i>
                            <span class="text-emerald-600">Pending Accounts</span>
                        </nav>
                        <h1 class="text-base sm:text-lg font-black text-slate-800 tracking-tight truncate">
                            Account Approval Queue
                        </h1>
                    </div>
                </div>

                <div class="flex items-center gap-3 flex-shrink-0 w-full sm:w-auto">
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-amber-50 text-amber-700 rounded-xl text-[11px] font-bold uppercase tracking-wider border border-amber-200/60 shadow-sm">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                        </span>
                        <span>{{ $residents->count() }} Waiting</span>
                    </span>

                    @if($residents->count() > 0)
                    <div class="flex items-center gap-2">
                        <button onclick="confirmBulkAction('approve')" 
                                class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-bold uppercase tracking-wider rounded-lg shadow-sm transition-all hover:scale-105">
                            <i class="fas fa-check-double text-[10px]"></i>
                            <span class="hidden sm:inline">Accept All</span>
                        </button>
                        <button onclick="confirmBulkAction('reject')" 
                                class="inline-flex items-center gap-1.5 px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 text-[10px] font-bold uppercase tracking-wider rounded-lg transition-all hover:scale-105">
                            <i class="fas fa-times-circle text-[10px]"></i>
                            <span class="hidden sm:inline">Reject All</span>
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-5">

        {{-- ALERTS --}}
        @if(session('success'))
            <div class="flex items-center gap-3 px-4 py-3.5 bg-emerald-50 border border-emerald-200 rounded-xl shadow-sm animate-fade-in">
                <div class="h-8 w-8 bg-emerald-500 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check text-white text-xs"></i>
                </div>
                <span class="text-sm font-semibold text-emerald-800 flex-1">{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-600 transition">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="flex items-center gap-3 px-4 py-3.5 bg-rose-50 border border-rose-200 rounded-xl shadow-sm">
                <div class="h-8 w-8 bg-rose-500 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation text-white text-xs"></i>
                </div>
                <span class="text-sm font-semibold text-rose-800 flex-1">{{ session('error') }}</span>
                <button onclick="this.parentElement.remove()" class="text-rose-400 hover:text-rose-600 transition">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        @endif

        {{-- INFO BANNER --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-amber-500 via-amber-600 to-orange-600 rounded-2xl shadow-lg shadow-amber-500/20">
            <div class="absolute -right-10 -top-10 w-48 h-48 bg-white/10 rounded-full"></div>
            <div class="absolute -right-5 -bottom-12 w-32 h-32 bg-white/5 rounded-full"></div>
            
            <div class="relative p-6 flex items-start gap-4">
                <div class="h-14 w-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center flex-shrink-0 shadow-inner">
                    <i class="fas fa-user-clock text-white text-2xl"></i>
                </div>
                <div class="flex-1 min-w-0 text-white">
                    <h2 class="text-base font-black uppercase tracking-wider mb-1">Pending Account Approvals</h2>
                    <p class="text-xs text-amber-50 leading-relaxed max-w-2xl">
                        These residents registered themselves and are waiting for admin approval. Accounts marked "Claimed from list" match an existing imported record, while "New account" entries may show possible duplicates. Review each application carefully before approving. Once approved, they can log in immediately.
                    </p>
                </div>
            </div>
        </div>

        {{-- TABLE CARD --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden relative">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-slate-50 to-white">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-gradient-to-br from-amber-100 to-amber-200 rounded-xl flex items-center justify-center text-amber-600 shadow-sm">
                        <i class="fas fa-list text-sm"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider">Approval Queue</h2>
                        <p class="text-[11px] text-slate-500">{{ $residents->count() }} {{ $residents->count() === 1 ? 'account' : 'accounts' }} pending review</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider hidden sm:inline-flex items-center gap-1.5 bg-slate-100 px-3 py-1.5 rounded-lg">
                    <i class="fas fa-sort-amount-down text-[9px]"></i>
                    Newest First
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[900px]">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100">
                            <th class="px-5 py-3.5 text-[10px] font-black text-slate-500 uppercase tracking-wider text-center w-12">#</th>
                            <th class="px-5 py-3.5 text-[10px] font-black text-slate-500 uppercase tracking-wider">Applicant</th>
                            <th class="px-5 py-3.5 text-[10px] font-black text-slate-500 uppercase tracking-wider">ID Verification</th>
                            <th class="px-5 py-3.5 text-[10px] font-black text-slate-500 uppercase tracking-wider">Registration</th>
                            <th class="px-5 py-3.5 text-[10px] font-black text-slate-500 uppercase tracking-wider">Zone</th>
                            <th class="px-5 py-3.5 text-[10px] font-black text-slate-500 uppercase tracking-wider">Submitted</th>
                            <th class="px-5 py-3.5 text-[10px] font-black text-slate-500 uppercase tracking-wider text-center w-56">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($residents as $index => $res)
                        <tr class="hover:bg-emerald-50/30 transition-colors group">
                            <td class="px-5 py-4 text-xs text-slate-400 font-mono font-bold text-center">
                                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                            </td>

                            {{-- APPLICANT INFO --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        <div class="h-11 w-11 rounded-xl overflow-hidden flex-shrink-0 ring-2 ring-white shadow-sm flex items-center justify-center bg-gradient-to-br from-indigo-500 to-indigo-600">
                                            @if($res->profile_photo_url)
                                                <img src="{{ $res->profile_photo_url }}" alt="{{ $res->first_name }}" class="h-full w-full object-cover">
                                            @else
                                                <span class="text-white text-xs font-black tracking-wider">
                                                    {{ strtoupper(substr($res->first_name, 0, 1) . ($res->middle_name ? substr($res->middle_name, 0, 1) : '') . substr($res->last_name, 0, 1)) }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="absolute -bottom-1 -right-1 h-3.5 w-3.5 bg-amber-500 border-2 border-white rounded-full"></div>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-slate-800 leading-tight group-hover:text-emerald-700 transition-colors">
                                            {{ $res->first_name }} {{ $res->middle_name }} {{ $res->last_name }}
                                        </p>
                                        <p class="text-[10px] text-slate-400 font-medium mt-0.5 flex items-center gap-1">
                                            <i class="fas fa-envelope text-[8px]"></i>
                                            {{ $res->email ?? 'No email' }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- ID VERIFICATION --}}
                            <td class="px-5 py-4">
                                <div class="flex flex-col gap-1">
                                    @if($res->valid_id)
                                        <a href="{{ $res->valid_id_url }}" target="_blank" 
                                           class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 text-blue-700 rounded-lg text-[10px] font-bold uppercase border border-blue-200 hover:bg-blue-100 transition">
                                            <i class="fas fa-id-card text-[9px]"></i>
                                            View ID
                                        </a>
                                        <span class="text-[9px] text-slate-500 font-medium ml-1">
                                            {{ $res->id_type ?? 'No type specified' }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-slate-500 rounded-lg text-[10px] font-bold uppercase border border-slate-200">
                                            <i class="fas fa-times-circle text-[9px]"></i>
                                            No ID
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- REGISTRATION TYPE --}}
                            <td class="px-5 py-4">
                                @if($res->claimed_from_import)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-[10px] font-bold uppercase border border-emerald-200">
                                        <i class="fas fa-link text-[9px]"></i>
                                        Claimed from list
                                    </span>
                                    <p class="text-[10px] text-slate-400 mt-1 max-w-[190px] leading-snug">
                                        Tugma sa dating imported na record. Hindi madodoble sa listahan.
                                    </p>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 text-blue-700 rounded-lg text-[10px] font-bold uppercase border border-blue-200">
                                        <i class="fas fa-user-plus text-[9px]"></i>
                                        New account
                                    </span>
                                    @php $similar = $possibleMatches[$res->id] ?? collect(); @endphp
                                    @if($similar->isNotEmpty())
                                        <div class="mt-1.5 max-w-[210px] rounded-lg border border-amber-200 bg-amber-50 px-2 py-1.5">
                                            <p class="text-[10px] font-black text-amber-700 flex items-center gap-1">
                                                <i class="fas fa-exclamation-triangle text-[9px]"></i> Posibleng doble
                                            </p>
                                            @foreach($similar as $m)
                                                <p class="text-[10px] text-amber-800 leading-snug">
                                                    {{ $m->last_name }}, {{ $m->first_name }}
                                                    <span class="text-amber-600">
                                                        · {{ $m->date_of_birth ? $m->date_of_birth->format('M d, Y') : 'no birthday' }}
                                                        · {{ $m->zone_purok ?? 'N/A' }}
                                                    </span>
                                                </p>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-[10px] text-slate-400 mt-1">Walang kahawig sa listahan.</p>
                                    @endif
                                @endif
                            </td>

                            {{-- ZONE --}}
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-[10px] font-bold uppercase border border-slate-200">
                                    <i class="fas fa-map-pin text-[9px]"></i>
                                    {{ $res->zone_purok ?? 'N/A' }}
                                </span>
                            </td>

                            {{-- SUBMITTED DATE --}}
                            <td class="px-5 py-4">
                                <p class="text-xs font-bold text-slate-700">{{ $res->created_at->format('M d, Y') }}</p>
                                <p class="text-[10px] text-slate-400">{{ $res->created_at->diffForHumans() }}</p>
                            </td>

                            {{-- ACTIONS --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.residents.show', $res->id) }}" 
                                       class="h-9 w-9 flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all border border-slate-200 hover:border-indigo-200"
                                       title="View Details">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>

                                    <form action="{{ route('admin.residents.approve', $res->id) }}" method="POST" 
                                          onsubmit="return confirm('Approve {{ $res->first_name }} {{ $res->last_name }}\'s account?');" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-black uppercase tracking-wider rounded-lg shadow-sm shadow-emerald-500/20 transition-all hover:scale-[1.03] active:scale-[0.98]">
                                            <i class="fas fa-check text-[10px]"></i>
                                            Approve
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.residents.reject', $res->id) }}" method="POST" 
                                          onsubmit="return confirm(@js($res->claimed_from_import ? 'Reject registration ni ' . $res->first_name . '? Babalik siya sa imported na listahan.' : 'Reject ' . $res->first_name . ' ' . $res->last_name . '\'s account?'));" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 text-[10px] font-black uppercase tracking-wider rounded-lg transition-all hover:scale-[1.03] active:scale-[0.98]">
                                            <i class="fas fa-times text-[10px]"></i>
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-20 text-center">
                                <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                    <div class="h-20 w-20 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-2xl flex items-center justify-center mb-5 shadow-inner">
                                        <i class="fas fa-check-circle text-3xl text-emerald-500"></i>
                                    </div>
                                    <h3 class="text-base font-black text-slate-800 mb-1">All caught up!</h3>
                                    <p class="text-xs text-slate-400 mb-6 leading-relaxed">No pending resident accounts to review. All registration requests have been processed.</p>
                                    <a href="{{ route('admin.residents.index') }}" 
                                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-emerald-500/20 transition-all hover:scale-[1.02]">
                                        <i class="fas fa-list text-[10px]"></i>
                                        View All Residents
                                    </a>
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

{{-- BULK ACTION MODAL --}}
<div id="bulkActionModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="modalContent">
        <div class="p-6 text-center">
            <div id="modalIconContainer" class="h-16 w-16 mx-auto rounded-2xl flex items-center justify-center mb-4">
                <i id="modalIcon" class="text-3xl"></i>
            </div>
            <h3 id="modalTitle" class="text-lg font-black text-slate-800 mb-2"></h3>
            <p id="modalMessage" class="text-sm text-slate-500 mb-6"></p>
            
            <div class="flex items-center gap-3">
                <button onclick="closeModal()" class="flex-1 px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-bold rounded-xl transition-colors">
                    Cancel
                </button>
                <form id="bulkActionForm" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" id="modalConfirmBtn" class="w-full px-4 py-3 text-white text-sm font-bold rounded-xl shadow-lg transition-all hover:scale-[1.02]">
                        Confirm
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(-5px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fade-in 0.3s ease-out;
    }
    .modal-open #modalContent {
        transform: scale(1);
        opacity: 1;
    }
</style>

<script>
    function confirmBulkAction(action) {
        const modal = document.getElementById('bulkActionModal');
        const form = document.getElementById('bulkActionForm');
        const title = document.getElementById('modalTitle');
        const message = document.getElementById('modalMessage');
        const iconContainer = document.getElementById('modalIconContainer');
        const icon = document.getElementById('modalIcon');
        const confirmBtn = document.getElementById('modalConfirmBtn');

        iconContainer.className = 'h-16 w-16 mx-auto rounded-2xl flex items-center justify-center mb-4';
        confirmBtn.className = 'w-full px-4 py-3 text-white text-sm font-bold rounded-xl shadow-lg transition-all hover:scale-[1.02]';

        if (action === 'approve') {
            form.action = "{{ route('admin.residents.bulk-approve') }}";
            title.textContent = 'Accept All Pending Accounts?';
            message.textContent = 'This will approve all {{ $residents->count() }} pending accounts. They will be able to log in immediately.';
            iconContainer.classList.add('bg-emerald-100', 'text-emerald-600');
            icon.className = 'fas fa-check-double text-3xl';
            confirmBtn.classList.add('bg-emerald-600', 'hover:bg-emerald-700', 'shadow-emerald-500/20');
            confirmBtn.textContent = 'Yes, Accept All';
        } else {
            form.action = "{{ route('admin.residents.bulk-reject') }}";
            title.textContent = 'Reject All Pending Accounts?';
            message.textContent = 'This will reject all {{ $residents->count() }} pending accounts. Ang mga na-claim mula sa listahan ay babalik sa Imported; ang mga bagong account ay mamarkahang rejected.';
            iconContainer.classList.add('bg-rose-100', 'text-rose-600');
            icon.className = 'fas fa-times-circle text-3xl';
            confirmBtn.classList.add('bg-rose-600', 'hover:bg-rose-700', 'shadow-rose-500/20');
            confirmBtn.textContent = 'Yes, Reject All';
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            modal.classList.add('modal-open');
        }, 10);
    }

    function closeModal() {
        const modal = document.getElementById('bulkActionModal');
        
        modal.classList.remove('modal-open');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }

    document.getElementById('bulkActionModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
@endsection