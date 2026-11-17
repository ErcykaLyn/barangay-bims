@extends('layouts.auth')

@section('title', 'BIMS | Request Status')

@section('theme-primary', '#10b981')
@section('theme-primary-light', '#34d399')
@section('theme-primary-dark', '#065f46')

@section('mesh-bg', '
    radial-gradient(at 0% 0%, rgba(16, 185, 129, 0.15) 0px, transparent 50%),
    radial-gradient(at 100% 0%, rgba(52, 211, 153, 0.1) 0px, transparent 50%),
    radial-gradient(at 100% 100%, rgba(16, 185, 129, 0.05) 0px, transparent 50%),
    radial-gradient(at 0% 100%, rgba(6, 95, 70, 0.1) 0px, transparent 50%)
')
@section('visual-bg', 'linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(6, 95, 70, 0.4))')

@section('badge-classes', 'bg-emerald-500/20 border border-emerald-500/30 text-emerald-300')
@section('badge-icon', 'fas fa-clock')
@section('badge-text', 'Request Status')
@section('visual-title', 'Check Your')
@section('visual-highlight', 'Request Status')
@section('visual-highlight-class', 'text-emerald-400')
@section('visual-description', 'Enter your email address to view the status of your password reset request.')
@section('visual-footer', 'Real-time Status Updates')

@section('form-content')
    {{-- Header --}}
    <div class="mb-6 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-500/20 border-2 border-emerald-500/40 rounded-2xl mb-3 shadow-lg shadow-emerald-500/10">
            <i class="fas fa-clipboard-list text-emerald-400 text-2xl"></i>
        </div>
        <h2 class="text-xl md:text-2xl font-extrabold text-white mb-1.5">Request Status</h2>
        <p class="text-slate-400 text-[11px] md:text-xs max-w-xs mx-auto">
            Check the status of your password reset request.
        </p>
    </div>

    {{-- Email Form --}}
    <form action="{{ route('password.request.check') }}" method="POST" class="space-y-4" id="statusForm">
        @csrf
        <div class="input-wrapper">
            <label for="email" class="block text-[10px] font-black text-white uppercase tracking-[2px] mb-1.5 ml-1">
                Email Address <span class="text-rose-400">*</span>
            </label>
            <input type="email" id="email" name="email" value="{{ old('email', request('email')) }}"
                   class="custom-input" placeholder="juan@example.com"
                   required autofocus autocomplete="email" inputmode="email">
            <i class="fas fa-envelope input-icon"></i>
        </div>

        @error('email')
            <p class="text-[11px] text-rose-300 ml-1 flex items-center gap-1.5"><i class="fas fa-exclamation-circle text-[10px]"></i> {{ $message }}</p>
        @enderror

        <button type="submit" class="login-btn" id="statusBtn">
            <span id="statusBtnText">Check Status</span>
            <i class="fas fa-search text-xs" id="statusBtnIcon"></i>
        </button>
    </form>

    {{-- Requests List --}}
    @if($requests->count() > 0)
        @php
            $counts = $requests->groupBy('status')->map->count();
        @endphp
        <div class="mt-6 space-y-3">

            <div class="flex items-center justify-between">
                <h3 class="text-[10px] font-black text-white uppercase tracking-widest">Your Requests</h3>
                <div class="flex items-center gap-1.5">
                    @if(($counts['pending'] ?? 0) > 0)
                        <span class="text-[9px] font-bold text-amber-300 bg-amber-500/10 border border-amber-500/20 px-2 py-0.5 rounded-full">{{ $counts['pending'] }} pending</span>
                    @endif
                    <span class="text-[9px] font-bold text-slate-400 bg-white/5 px-2 py-0.5 rounded-full">{{ $requests->count() }} total</span>
                </div>
            </div>

            <div class="space-y-3 max-h-[26rem] overflow-y-auto pr-1 custom-scroll">
                @foreach($requests as $req)
                    @php
                        $status = $req->status;
                        $stage  = ['pending' => 1, 'approved' => 2, 'completed' => 3][$status] ?? 1;
                    @endphp
                    <div class="bg-white/5 border border-white/10 rounded-xl p-4 hover:bg-white/10 hover:border-white/20 transition-all">

                        {{-- Header: Date & Status --}}
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <div class="min-w-0">
                                <p class="text-[10px] font-bold text-slate-300 flex items-center gap-1.5">
                                    <i class="far fa-clock text-slate-500"></i>
                                    {{ $req->created_at->format('M d, Y') }}
                                    <span class="text-slate-600">&middot;</span>
                                    {{ $req->created_at->format('h:i A') }}
                                </p>
                                <p class="text-[9px] text-slate-500 mt-0.5">Submitted {{ $req->created_at->diffForHumans() }}</p>
                            </div>

                            @if($status === 'pending')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-300 text-[8px] font-black uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 bg-amber-400 rounded-full animate-pulse"></span> Pending
                                </span>
                            @elseif($status === 'approved')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-[8px] font-black uppercase tracking-wider">
                                    <i class="fas fa-check-circle text-[8px]"></i> Approved
                                </span>
                            @elseif($status === 'rejected')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-rose-500/20 border border-rose-500/30 text-rose-300 text-[8px] font-black uppercase tracking-wider">
                                    <i class="fas fa-times-circle text-[8px]"></i> Rejected
                                </span>
                            @elseif($status === 'completed')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-blue-500/20 border border-blue-500/30 text-blue-300 text-[8px] font-black uppercase tracking-wider">
                                    <i class="fas fa-check-double text-[8px]"></i> Completed
                                </span>
                            @endif
                        </div>

                        {{-- Mini progress --}}
                        @if($status !== 'rejected')
                            <div class="flex items-center gap-1 mb-3" aria-hidden="true">
                                @foreach([0,1,2,3] as $n)
                                    <span class="h-1 flex-1 rounded-full {{ $n <= $stage ? ($status === 'pending' && $n === 1 ? 'bg-amber-400/80' : 'bg-emerald-500') : 'bg-white/10' }}"></span>
                                @endforeach
                            </div>
                        @endif

                        {{-- Plain-language status --}}
                        <p class="text-[11px] leading-relaxed mb-2
                            {{ $status === 'rejected' ? 'text-rose-200' : ($status === 'approved' ? 'text-emerald-200' : ($status === 'completed' ? 'text-blue-200' : 'text-amber-200')) }}">
                            @switch($status)
                                @case('pending')   Your request is waiting for the Barangay Admin to review it. @break
                                @case('approved')  Approved! Use the button below (or the link in your email) to set a new password. The link is valid for 24 hours. @break
                                @case('completed') Your password was changed successfully. You can log in with your new password. @break
                                @case('rejected')  Your request was not approved. See the admin note below, or visit the Barangay Hall for help. @break
                            @endswitch
                        </p>

                        {{-- Reason --}}
                        @if($req->reason)
                            <div class="mb-2">
                                <p class="text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Your reason</p>
                                <p class="text-[11px] text-slate-300 leading-relaxed line-clamp-2">{{ $req->reason }}</p>
                            </div>
                        @endif

                        {{-- Admin Notes --}}
                        @if($req->admin_notes)
                            <div class="mb-1 p-2.5 bg-white/5 border-l-2 border-emerald-500/50 rounded-r-lg">
                                <p class="text-[9px] font-bold text-emerald-400 uppercase tracking-wider mb-0.5 flex items-center gap-1">
                                    <i class="fas fa-comment-dots text-[8px]"></i> Admin Note
                                </p>
                                <p class="text-[10px] text-slate-300 italic leading-relaxed">{{ $req->admin_notes }}</p>
                            </div>
                        @endif

                        {{-- Action Button (if approved) --}}
                        @if($status === 'approved')
                            <a href="{{ route('password.reset.form', $req->id) }}"
                               class="mt-3 w-full flex items-center justify-center gap-1.5 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-500 hover:to-emerald-600 text-white text-[10px] font-black uppercase tracking-wider py-2.5 rounded-lg transition-all shadow-lg shadow-emerald-500/20 hover:scale-[1.02] active:scale-[0.98]">
                                <i class="fas fa-key text-[10px]"></i> Reset Password Now
                            </a>
                        @elseif($status === 'rejected')
                            <a href="{{ route('password.request') }}"
                               class="mt-3 w-full flex items-center justify-center gap-1.5 border border-white/15 text-slate-200 hover:bg-white/5 text-[10px] font-black uppercase tracking-wider py-2.5 rounded-lg transition-all">
                                <i class="fas fa-redo text-[10px]"></i> Submit a New Request
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @elseif(request()->isMethod('post') || request('email'))
        {{-- Empty State --}}
        <div class="mt-6 text-center py-8 px-4 bg-white/5 border border-white/10 rounded-2xl">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-white/5 border border-white/10 rounded-2xl mb-3">
                <i class="fas fa-search text-slate-500 text-xl"></i>
            </div>
            <p class="text-[12px] font-bold text-white mb-1">No requests found</p>
            <p class="text-[10px] text-slate-400 leading-relaxed">Walang nahanap na request para sa email na iyon. Check the spelling, or submit a new request.</p>
            <a href="{{ route('password.request') }}" class="inline-flex items-center gap-1.5 mt-3 text-[11px] font-bold text-emerald-400 hover:text-emerald-300 transition">
                <i class="fas fa-paper-plane text-[10px]"></i> Request a reset
            </a>
        </div>
    @endif

    {{-- Footer Links --}}
    <div class="mt-6 pt-4 border-t border-white/10 space-y-2 text-center">
        <p class="text-[11px] text-slate-400">
            Need to submit a new request?
            <a href="{{ route('password.request') }}" class="font-bold text-emerald-500 hover:text-emerald-400 transition underline-offset-4 hover:underline">Request Reset</a>
        </p>
        <p class="text-[11px] text-slate-400">
            Remembered your password?
            <a href="{{ route('login') }}" class="font-bold text-emerald-500 hover:text-emerald-400 transition underline-offset-4 hover:underline">Back to Login</a>
        </p>
    </div>

    <script>
        (function () {
            var form = document.getElementById('statusForm');
            var btn = document.getElementById('statusBtn');
            var txt = document.getElementById('statusBtnText');
            var icon = document.getElementById('statusBtnIcon');
            if (!form) return;
            form.addEventListener('submit', function () {
                if (!form.checkValidity()) return;
                btn.disabled = true;
                btn.style.opacity = '.75';
                btn.style.cursor = 'wait';
                txt.textContent = 'Checking...';
                icon.className = 'fas fa-circle-notch fa-spin text-xs';
            });
        })();
    </script>
@endsection