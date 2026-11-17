@extends('layouts.app')

@section('content')
@php
    $res        = $resetRequest->resident;
    $status     = $resetRequest->status;
    $isPending  = $status === 'pending';
    $isApproved = $status === 'approved';
    $isDone     = $status === 'completed';
    $isRejected = $status === 'rejected';
    $reviewed   = in_array($status, ['approved', 'rejected', 'completed']);
    $name       = $res->full_name ?? 'Unknown Resident';
    $initial    = strtoupper(substr($res->first_name ?? 'U', 0, 1));

    $accStatus  = strtolower($res->status ?? '');
    $accClasses = match(true) {
        $accStatus === 'approved' => 'bg-emerald-50 border-emerald-200 text-emerald-700',
        $accStatus === 'pending'  => 'bg-amber-50 border-amber-200 text-amber-700',
        $accStatus === 'rejected' => 'bg-rose-50 border-rose-200 text-rose-700',
        default                   => 'bg-slate-50 border-slate-200 text-slate-600',
    };

    // Expiry ng reset link: 24 oras mula sa approval
    $expiresAt = ($isApproved && $resetRequest->approved_at) ? $resetRequest->approved_at->copy()->addDay() : null;
@endphp

<div class="max-w-5xl mx-auto pt-3 sm:pt-6 pb-8 sm:pb-12 px-3 sm:px-6 space-y-4 sm:space-y-5">

    {{-- Back --}}
    <a href="{{ route('admin.password-resets.index') }}"
       class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-800 transition">
        <i class="fas fa-arrow-left text-[10px]"></i> Back to Requests
    </a>

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
                    <div class="min-w-0">
                        <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-white/20 border border-white/30 mb-1">
                            <span class="h-1.5 w-1.5 rounded-full bg-indigo-300 animate-pulse"></span>
                            <span class="text-[8px] sm:text-[9px] text-white font-black uppercase tracking-widest">Request #{{ $resetRequest->id }}</span>
                        </div>
                        <h2 class="text-lg sm:text-2xl font-black tracking-tight leading-tight">Password Reset Request</h2>
                        <p class="text-indigo-100/90 text-[10px] sm:text-xs mt-0.5 truncate">{{ $name }} &middot; {{ $resetRequest->email }}</p>
                    </div>
                </div>

                @if($isPending)
                    <div class="flex items-center gap-2 px-3 py-2 bg-amber-400/20 border border-amber-300/40 rounded-xl self-start sm:self-center">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-300 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-400"></span>
                        </span>
                        <span class="text-[10px] sm:text-xs font-bold text-amber-100">Needs Review</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ============ TIMELINE ============ --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-5">
        <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Request Timeline</h3>

        <div class="flex items-start justify-between gap-1 sm:gap-2">
            {{-- Step 1: Submitted --}}
            <div class="flex flex-col items-center flex-1">
                <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-full bg-emerald-500 flex items-center justify-center text-white shadow-md shadow-emerald-500/30">
                    <i class="fas fa-paper-plane text-[10px] sm:text-xs"></i>
                </div>
                <p class="text-[8px] sm:text-[9px] font-black text-slate-700 uppercase tracking-wider mt-1.5 text-center">Submitted</p>
                <p class="text-[8px] text-slate-400 text-center">{{ $resetRequest->created_at->format('M d, h:i A') }}</p>
            </div>

            <div class="h-0.5 flex-1 mt-4 sm:mt-5 rounded-full {{ $reviewed ? 'bg-emerald-500' : 'bg-slate-200' }}"></div>

            {{-- Step 2: Reviewed / Rejected --}}
            <div class="flex flex-col items-center flex-1">
                @if($isRejected)
                    <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-full bg-rose-500 flex items-center justify-center text-white shadow-md shadow-rose-500/30">
                        <i class="fas fa-times text-[10px] sm:text-xs"></i>
                    </div>
                    <p class="text-[8px] sm:text-[9px] font-black text-rose-600 uppercase tracking-wider mt-1.5 text-center">Rejected</p>
                @elseif($reviewed)
                    <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-full bg-emerald-500 flex items-center justify-center text-white shadow-md shadow-emerald-500/30">
                        <i class="fas fa-check text-[10px] sm:text-xs"></i>
                    </div>
                    <p class="text-[8px] sm:text-[9px] font-black text-slate-700 uppercase tracking-wider mt-1.5 text-center">Approved</p>
                @else
                    <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-full bg-amber-400 flex items-center justify-center text-white shadow-md shadow-amber-400/30 animate-pulse">
                        <i class="fas fa-clock text-[10px] sm:text-xs"></i>
                    </div>
                    <p class="text-[8px] sm:text-[9px] font-black text-slate-700 uppercase tracking-wider mt-1.5 text-center">In Review</p>
                @endif
                @if($resetRequest->approved_at)
                    <p class="text-[8px] text-slate-400 text-center">{{ $resetRequest->approved_at->format('M d, h:i A') }}</p>
                @endif
            </div>

            <div class="h-0.5 flex-1 mt-4 sm:mt-5 rounded-full {{ $isDone ? 'bg-emerald-500' : 'bg-slate-200' }}"></div>

            {{-- Step 3: Completed --}}
            <div class="flex flex-col items-center flex-1">
                <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-full flex items-center justify-center shadow-md {{ $isDone ? 'bg-emerald-500 text-white shadow-emerald-500/30' : 'bg-slate-200 text-slate-400 shadow-transparent' }}">
                    <i class="fas fa-flag-checkered text-[10px] sm:text-xs"></i>
                </div>
                <p class="text-[8px] sm:text-[9px] font-black uppercase tracking-wider mt-1.5 text-center {{ $isDone ? 'text-slate-700' : 'text-slate-400' }}">Completed</p>
                @if($isDone && $resetRequest->updated_at)
                    <p class="text-[8px] text-slate-400 text-center">{{ $resetRequest->updated_at->format('M d, h:i A') }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- ============ INFO CARDS ============ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Resident --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white flex items-center gap-2">
                <div class="h-7 w-7 rounded-lg bg-indigo-100 flex items-center justify-center">
                    <i class="fas fa-user-circle text-indigo-600 text-xs"></i>
                </div>
                <h3 class="text-[11px] font-black text-slate-800 uppercase tracking-wider">Resident Information</h3>
            </div>
            <div class="p-4 space-y-3">
                <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-sm font-black flex-shrink-0 shadow-sm">
                        {{ $initial }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-black text-slate-800 truncate">{{ $name }}</p>
                        <p class="text-[10px] text-slate-500 font-mono truncate" id="reqEmail">{{ $resetRequest->email }}</p>
                    </div>
                    <button type="button" id="copyEmail" title="Copy email"
                            class="h-8 w-8 rounded-lg border border-slate-200 text-slate-400 hover:text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50 transition flex items-center justify-center flex-shrink-0">
                        <i class="far fa-copy text-xs"></i>
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Zone / Purok</p>
                        <p class="text-[11px] font-bold text-slate-800">{{ $res->zone_purok ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Contact</p>
                        <p class="text-[11px] font-bold text-slate-800">{{ $res->contact_number ?? 'N/A' }}</p>
                    </div>
                </div>

                <div>
                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Complete Address</p>
                    <p class="text-[11px] font-bold text-slate-800">{{ $res->complete_address ?? 'N/A' }}</p>
                </div>

                <div>
                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Account Status</p>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full border text-[9px] font-black uppercase {{ $accClasses }}">
                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                        {{ $res->status ?? 'N/A' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Request --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white flex items-center gap-2">
                <div class="h-7 w-7 rounded-lg bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-file-alt text-purple-600 text-xs"></i>
                </div>
                <h3 class="text-[11px] font-black text-slate-800 uppercase tracking-wider">Request Information</h3>
            </div>
            <div class="p-4 space-y-3">
                <div>
                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Reason</p>
                    <div class="bg-slate-50 border border-slate-100 rounded-lg p-2.5">
                        <p class="text-[11px] leading-relaxed {{ $resetRequest->reason ? 'text-slate-700' : 'text-slate-400 italic' }}">{{ $resetRequest->reason ?: 'No reason provided' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Submitted</p>
                        <p class="text-[11px] font-bold text-slate-800">{{ $resetRequest->created_at->format('M d, Y') }}</p>
                        <p class="text-[9px] text-slate-400">{{ $resetRequest->created_at->format('h:i A') }} &middot; {{ $resetRequest->created_at->diffForHumans() }}</p>
                    </div>
                    <div>
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Current Status</p>
                        @if($isPending)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 border border-amber-200 text-amber-700 text-[9px] font-black uppercase">
                                <span class="w-1.5 h-1.5 bg-amber-400 rounded-full animate-pulse"></span> Pending
                            </span>
                        @elseif($isApproved)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-[9px] font-black uppercase">
                                <i class="fas fa-check-circle text-[8px]"></i> Approved
                            </span>
                        @elseif($isRejected)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-rose-50 border border-rose-200 text-rose-700 text-[9px] font-black uppercase">
                                <i class="fas fa-times-circle text-[8px]"></i> Rejected
                            </span>
                        @elseif($isDone)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-50 border border-blue-200 text-blue-700 text-[9px] font-black uppercase">
                                <i class="fas fa-check-double text-[8px]"></i> Completed
                            </span>
                        @endif
                    </div>
                </div>

                @if($resetRequest->admin_notes)
                    <div>
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Admin Notes</p>
                        <div class="bg-indigo-50 border-l-2 border-indigo-500 rounded-r-lg p-2.5">
                            <p class="text-[11px] text-slate-700 italic leading-relaxed">{{ $resetRequest->admin_notes }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ============ ACTIONS ============ --}}
    @if($isPending)
        <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 flex items-start gap-2.5">
            <i class="fas fa-shield-alt text-amber-500 text-sm mt-0.5"></i>
            <p class="text-[11px] text-amber-800 leading-relaxed">
                <strong>Verify before approving.</strong> Confirm that the resident really made this request (for example, by checking their contact number or asking in person). Approving sends a reset link to <strong>{{ $resetRequest->email }}</strong>.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <form action="{{ route('admin.password-resets.approve', $resetRequest->id) }}" method="POST" class="js-action-form" data-confirm="Approve this request and send the reset link to {{ $resetRequest->email }}?">
                @csrf
                <div class="h-full bg-white rounded-2xl border-2 border-emerald-200 shadow-sm p-4 hover:border-emerald-400 transition flex flex-col">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="h-8 w-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                            <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-[11px] font-black text-emerald-700 uppercase tracking-wider">Approve Request</p>
                            <p class="text-[9px] text-emerald-600">Send reset link via email (valid 24 hours)</p>
                        </div>
                    </div>
                    <textarea name="admin_notes" rows="2" maxlength="500"
                              class="w-full bg-emerald-50 border border-emerald-200 rounded-lg p-2.5 text-[11px] mb-3 resize-none focus:ring-2 focus:ring-emerald-500/30 outline-none"
                              placeholder="Optional notes for the resident..."></textarea>
                    <button type="submit"
                            class="mt-auto w-full bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-black uppercase tracking-wider py-2.5 rounded-lg transition shadow-sm shadow-emerald-500/20 hover:scale-[1.02] active:scale-[0.98]">
                        <i class="fas fa-check mr-1"></i> <span class="btn-label">Approve &amp; Send Email</span>
                    </button>
                </div>
            </form>

            <form action="{{ route('admin.password-resets.reject', $resetRequest->id) }}" method="POST" class="js-action-form" data-confirm="Reject this request? The resident will be notified.">
                @csrf
                <div class="h-full bg-white rounded-2xl border-2 border-rose-200 shadow-sm p-4 hover:border-rose-400 transition flex flex-col">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="h-8 w-8 rounded-lg bg-rose-100 flex items-center justify-center">
                            <i class="fas fa-times-circle text-rose-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-[11px] font-black text-rose-700 uppercase tracking-wider">Reject Request</p>
                            <p class="text-[9px] text-rose-600">Resident will be notified</p>
                        </div>
                    </div>
                    <textarea name="admin_notes" rows="2" required maxlength="500"
                              class="w-full bg-rose-50 border border-rose-200 rounded-lg p-2.5 text-[11px] mb-3 resize-none focus:ring-2 focus:ring-rose-500/30 outline-none"
                              placeholder="Reason for rejection (required)..."></textarea>
                    <button type="submit"
                            class="mt-auto w-full bg-rose-600 hover:bg-rose-700 text-white text-[10px] font-black uppercase tracking-wider py-2.5 rounded-lg transition shadow-sm shadow-rose-500/20 hover:scale-[1.02] active:scale-[0.98]">
                        <i class="fas fa-times mr-1"></i> <span class="btn-label">Reject Request</span>
                    </button>
                </div>
            </form>
        </div>

    @elseif($isApproved)
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-5 text-center">
            <div class="inline-flex items-center justify-center h-12 w-12 rounded-2xl bg-blue-100 border border-blue-200 mb-3">
                <i class="fas fa-paper-plane text-blue-600 text-lg"></i>
            </div>
            <p class="text-sm font-black text-blue-900">Request Approved</p>
            <p class="text-[11px] text-blue-700 mt-1">An email with the reset link has been sent to the resident. Waiting for them to reset their password.</p>
            @if($resetRequest->approved_at)
                <p class="text-[9px] text-blue-500 mt-2">Approved on {{ $resetRequest->approved_at->format('M d, Y h:i A') }}</p>
            @endif
            @if($expiresAt)
                <p class="text-[10px] font-bold mt-2 {{ $expiresAt->isPast() ? 'text-rose-600' : 'text-blue-700' }}">
                    <i class="far fa-hourglass-half mr-1"></i>
                    {{ $expiresAt->isPast() ? 'Link window ended ' . $expiresAt->diffForHumans() : 'Link valid until ' . $expiresAt->format('M d, h:i A') . ' (' . $expiresAt->diffForHumans() . ')' }}
                </p>
            @endif
        </div>

    @elseif($isDone)
        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-200 rounded-2xl p-5 text-center">
            <div class="inline-flex items-center justify-center h-12 w-12 rounded-2xl bg-emerald-100 border border-emerald-200 mb-3">
                <i class="fas fa-flag-checkered text-emerald-600 text-lg"></i>
            </div>
            <p class="text-sm font-black text-emerald-900">Password Reset Completed</p>
            <p class="text-[11px] text-emerald-700 mt-1">The resident has successfully reset their password.</p>
        </div>

    @elseif($isRejected)
        <div class="bg-gradient-to-br from-rose-50 to-pink-50 border border-rose-200 rounded-2xl p-5 text-center">
            <div class="inline-flex items-center justify-center h-12 w-12 rounded-2xl bg-rose-100 border border-rose-200 mb-3">
                <i class="fas fa-times-circle text-rose-600 text-lg"></i>
            </div>
            <p class="text-sm font-black text-rose-900">Request Rejected</p>
            <p class="text-[11px] text-rose-700 mt-1">This request has been rejected. The resident was notified.</p>
        </div>
    @endif
</div>

<script>
    (function () {
        // Copy email
        var copyBtn = document.getElementById('copyEmail');
        var emailEl = document.getElementById('reqEmail');
        if (copyBtn && emailEl) {
            copyBtn.addEventListener('click', function () {
                var text = emailEl.textContent.trim();
                var done = function () {
                    copyBtn.innerHTML = '<i class="fas fa-check text-xs text-emerald-600"></i>';
                    setTimeout(function () { copyBtn.innerHTML = '<i class="far fa-copy text-xs"></i>'; }, 1500);
                };
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text).then(done).catch(function () {});
                }
            });
        }

        // Confirm + loading state on approve/reject
        document.querySelectorAll('.js-action-form').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                if (!form.checkValidity()) return;
                if (!window.confirm(form.dataset.confirm || 'Are you sure?')) { e.preventDefault(); return; }
                var btn = form.querySelector('button[type="submit"]');
                if (!btn) return;
                // Delay para hindi ma-cancel ang submit ng disabled button
                setTimeout(function () {
                    btn.disabled = true;
                    btn.classList.add('opacity-70', 'cursor-wait');
                    var label = btn.querySelector('.btn-label');
                    if (label) label.textContent = 'Please wait...';
                }, 0);
            });
        });
    })();
</script>
@endsection