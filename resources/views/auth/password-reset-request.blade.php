@extends('layouts.auth')

@section('title', 'BIMS | Request Password Reset')

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
@section('badge-icon', 'fas fa-key')
@section('badge-text', 'Password Reset')
@section('visual-title', 'Forgot Your')
@section('visual-highlight', 'Password?')
@section('visual-highlight-class', 'text-emerald-400')
@section('visual-description', 'Submit your request and wait for the admin approval to reset your password.')
@section('visual-footer', 'Secure Password Recovery')

@section('form-content')
    {{-- Success / Error Messages --}}
    @if(session('success'))
        <div role="status" class="mb-5 p-4 bg-emerald-500/15 border border-emerald-500/30 rounded-xl flex items-start gap-3 animate-fade-in">
            <div class="h-8 w-8 bg-emerald-500 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check text-white text-xs"></i>
            </div>
            <p class="text-[11px] text-emerald-200 leading-relaxed pt-1">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div role="alert" class="mb-5 p-4 bg-rose-500/15 border border-rose-500/30 rounded-xl flex items-start gap-3 animate-fade-in">
            <div class="h-8 w-8 bg-rose-500 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-exclamation text-white text-xs"></i>
            </div>
            <p class="text-[11px] text-rose-200 leading-relaxed pt-1">{{ session('error') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div role="alert" class="mb-5 p-4 bg-rose-500/15 border border-rose-500/30 rounded-xl">
            <div class="flex items-start gap-3">
                <div class="h-8 w-8 bg-rose-500 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation text-white text-xs"></i>
                </div>
                <ul class="text-[11px] text-rose-200 space-y-1 flex-1 pt-0.5">
                    @foreach($errors->all() as $error)
                        <li class="flex items-start gap-1.5">
                            <span class="text-rose-400">&bull;</span>
                            <span>{{ $error }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Header --}}
    <div class="mb-6 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-500/20 border-2 border-emerald-500/40 rounded-2xl mb-3 shadow-lg shadow-emerald-500/10">
            <i class="fas fa-lock text-emerald-400 text-2xl"></i>
        </div>
        <h2 class="text-xl md:text-2xl font-extrabold text-white mb-1.5">Forgot Password?</h2>
        <p class="text-slate-400 text-[11px] md:text-xs max-w-xs mx-auto">
            Submit your request below. The admin will review it and send you a reset link by email.
        </p>
    </div>

    {{-- Progress: Request -> Review -> Email -> New password --}}
    @php $current = 0; @endphp
    <ol class="mb-6 grid grid-cols-4 gap-1.5" aria-label="Password reset steps">
        @foreach([['Request','fa-paper-plane'],['Review','fa-user-shield'],['Email','fa-envelope'],['Reset','fa-key']] as $i => $s)
            @php $done = $i < $current; $active = $i === $current; @endphp
            <li class="flex flex-col items-center gap-1.5 text-center">
                <span class="h-8 w-8 rounded-full flex items-center justify-center text-[10px] border transition
                    {{ $done ? 'bg-emerald-500 border-emerald-400 text-white' : ($active ? 'bg-emerald-500/20 border-emerald-400 text-emerald-300 ring-4 ring-emerald-500/10' : 'bg-white/5 border-white/10 text-slate-500') }}">
                    <i class="fas {{ $done ? 'fa-check' : $s[1] }}"></i>
                </span>
                <span class="text-[9px] font-black uppercase tracking-wider {{ $done || $active ? 'text-emerald-300' : 'text-slate-500' }}">{{ $s[0] }}</span>
            </li>
        @endforeach
    </ol>

    <form action="{{ route('password.request.store') }}" method="POST" class="space-y-4" id="requestForm" novalidate>
        @csrf

        {{-- Email --}}
        <div>
            <div class="input-wrapper">
                <label for="email" class="block text-[10px] font-black text-white uppercase tracking-[2px] mb-1.5 ml-1">
                    Email Address <span class="text-rose-400">*</span>
                </label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       class="custom-input" placeholder="juan@example.com"
                       required autofocus autocomplete="email" inputmode="email" aria-describedby="emailHelp">
                <i class="fas fa-envelope input-icon"></i>
            </div>
            <p id="emailHelp" class="text-[10px] text-slate-500 mt-1.5 ml-1">Use the email you registered your BIMS account with.</p>
        </div>

        {{-- Reason (Optional) --}}
        <div>
            <div class="input-wrapper">
                <label for="reason" class="block text-[10px] font-black text-white uppercase tracking-[2px] mb-1.5 ml-1">
                    Reason <span class="text-slate-500 font-normal normal-case tracking-normal">(Optional)</span>
                </label>
                <textarea id="reason" name="reason" rows="3"
                          class="custom-input resize-none"
                          placeholder="Optional: Why do you need to reset your password?"
                          maxlength="500">{{ old('reason') }}</textarea>
                <i class="fas fa-comment-alt input-icon"></i>
            </div>
            <p class="text-[10px] text-slate-500 mt-1.5 text-right"><span id="reasonCount">0</span>/500</p>
        </div>

        {{-- Submit Button --}}
        <button type="submit" class="login-btn mt-2" id="requestBtn">
            <span id="requestBtnText">Submit Request</span>
            <i class="fas fa-paper-plane text-xs" id="requestBtnIcon"></i>
        </button>
    </form>

    {{-- Trust note --}}
    <div class="mt-5 p-3.5 bg-emerald-500/10 border border-emerald-500/25 rounded-xl flex items-start gap-2.5">
        <i class="fas fa-shield-alt text-emerald-400 text-sm mt-0.5"></i>
        <p class="text-[11px] text-emerald-300/80 leading-relaxed">
            For your security, the Barangay Admin manually verifies every request. Approved links are valid for <strong class="text-emerald-200">24 hours</strong>.
        </p>
    </div>

    {{-- Footer Links --}}
    <div class="mt-6 pt-4 border-t border-white/10 space-y-2 text-center">
        <p class="text-[11px] text-slate-400">
            Already have a pending request?
            <a href="{{ route('password.request.status') }}" class="font-bold text-emerald-500 hover:text-emerald-400 transition underline-offset-4 hover:underline">Check Status</a>
        </p>
        <p class="text-[11px] text-slate-400">
            Remembered your password?
            <a href="{{ route('login') }}" class="font-bold text-emerald-500 hover:text-emerald-400 transition underline-offset-4 hover:underline">Back to Login</a>
        </p>
    </div>

    <script>
        (function () {
            var form = document.getElementById('requestForm');
            var reason = document.getElementById('reason');
            var count = document.getElementById('reasonCount');
            var btn = document.getElementById('requestBtn');
            var txt = document.getElementById('requestBtnText');
            var icon = document.getElementById('requestBtnIcon');

            function updateCount() { if (reason && count) count.textContent = reason.value.length; }
            if (reason) { reason.addEventListener('input', updateCount); updateCount(); }

            if (form) {
                form.addEventListener('submit', function () {
                    if (!form.checkValidity()) return;
                    btn.disabled = true;
                    btn.style.opacity = '.75';
                    btn.style.cursor = 'wait';
                    txt.textContent = 'Submitting...';
                    icon.className = 'fas fa-circle-notch fa-spin text-xs';
                });
            }
        })();
    </script>
@endsection