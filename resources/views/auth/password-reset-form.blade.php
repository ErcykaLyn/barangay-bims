@extends('layouts.auth')

@section('title', 'BIMS | Reset Password')

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
@section('badge-text', 'Reset Password')
@section('visual-title', 'Create Your')
@section('visual-highlight', 'New Password')
@section('visual-highlight-class', 'text-emerald-400')
@section('visual-description', 'Type your new password below. Make sure it is strong and hard to guess.')
@section('visual-footer', 'Secure Password Reset')

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
            <i class="fas fa-key text-emerald-400 text-2xl"></i>
        </div>
        <h2 class="text-xl md:text-2xl font-extrabold text-white mb-1.5">Reset Password</h2>
        <p class="text-slate-400 text-[11px] md:text-xs max-w-xs mx-auto">
            Enter your new password below. Make sure it&rsquo;s strong and easy for you to remember.
        </p>
    </div>

    {{-- Progress: Request -> Review -> Email -> New password --}}
    @php $current = 3; @endphp
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

    {{-- Info Banner --}}
    <div class="mb-5 p-3.5 bg-emerald-500/10 border border-emerald-500/25 rounded-xl flex items-start gap-2.5">
        <i class="fas fa-shield-alt text-emerald-400 text-sm mt-0.5"></i>
        <div>
            <p class="text-[10px] font-bold text-emerald-200 uppercase tracking-wider">Account Verified</p>
            <p class="text-[11px] text-emerald-300/80 mt-0.5">Your reset request has been approved. You can now set a new password.</p>
        </div>
    </div>

    <form action="{{ route('password.reset.submit', $resetRequest->id) }}" method="POST" class="space-y-4" id="resetForm" novalidate>
        @csrf

        {{-- Email (Read-only) --}}
        <div class="input-wrapper">
            <label for="email" class="block text-[10px] font-black text-white uppercase tracking-[2px] mb-1.5 ml-1">Email Address</label>
            <input type="email" id="email" name="email" value="{{ $resetRequest->email }}"
                   class="custom-input bg-white/10 cursor-not-allowed text-slate-400" readonly tabindex="-1">
            <i class="fas fa-envelope input-icon"></i>
        </div>

        {{-- New Password --}}
        <div>
            <div class="input-wrapper">
                <label for="password" class="block text-[10px] font-black text-white uppercase tracking-[2px] mb-1.5 ml-1">
                    New Password <span class="text-rose-400">*</span>
                </label>
                <input type="password" name="password" id="password"
                       class="custom-input" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                       required minlength="8" autocomplete="new-password" autofocus>
                <i class="fas fa-lock input-icon"></i>
                <button type="button" class="toggle-password" data-target="password" aria-label="Toggle password visibility">
                    <i class="fas fa-eye-slash"></i>
                </button>
            </div>
            <p id="capsWarn" class="hidden text-[10px] text-amber-300 mt-1.5 ml-1 items-center gap-1.5">
                <i class="fas fa-exclamation-triangle text-[9px]"></i> Caps Lock is on
            </p>
        </div>

        {{-- Password Strength Indicator --}}
        <div class="space-y-2 px-1" aria-live="polite">
            <div class="flex items-center gap-2">
                <div class="flex-1 h-1.5 rounded-full bg-white/10 overflow-hidden">
                    <div id="strengthBar" class="h-full w-0 bg-rose-500 transition-all duration-300 rounded-full"></div>
                </div>
                <span id="strengthText" class="text-[9px] font-black uppercase tracking-widest text-slate-500 min-w-[45px] text-right">Weak</span>
            </div>
            <ul id="requirements" class="grid grid-cols-1 sm:grid-cols-2 gap-x-3 gap-y-0.5 text-[10px] text-slate-500">
                <li id="req-length" class="flex items-center gap-1.5"><i class="fas fa-circle text-[6px]"></i><span>At least 8 characters</span></li>
                <li id="req-upper" class="flex items-center gap-1.5"><i class="fas fa-circle text-[6px]"></i><span>Upper &amp; lowercase letters</span></li>
                <li id="req-number" class="flex items-center gap-1.5"><i class="fas fa-circle text-[6px]"></i><span>At least one number</span></li>
                <li id="req-special" class="flex items-center gap-1.5"><i class="fas fa-circle text-[6px]"></i><span>At least one special character</span></li>
            </ul>
        </div>

        {{-- Confirm Password --}}
        <div>
            <div class="input-wrapper">
                <label for="password_confirmation" class="block text-[10px] font-black text-white uppercase tracking-[2px] mb-1.5 ml-1">
                    Confirm Password <span class="text-rose-400">*</span>
                </label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                       class="custom-input" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                       required minlength="8" autocomplete="new-password">
                <i class="fas fa-lock input-icon"></i>
                <button type="button" class="toggle-password" data-target="password_confirmation" aria-label="Toggle password visibility">
                    <i class="fas fa-eye-slash"></i>
                </button>
            </div>
            <p id="matchMsg" class="hidden text-[10px] mt-1.5 ml-1 items-center gap-1.5"></p>
        </div>

        {{-- Submit Button --}}
        <button type="submit" class="login-btn mt-3" id="resetBtn">
            <span id="resetBtnText">Reset Password</span>
            <i class="fas fa-check text-xs" id="resetBtnIcon"></i>
        </button>
    </form>

    {{-- Back to Login --}}
    <div class="mt-6 pt-4 border-t border-white/10 text-center">
        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-[11px] font-bold text-emerald-500 hover:text-emerald-400 transition">
            <i class="fas fa-arrow-left text-[10px]"></i> Back to Login
        </a>
    </div>

    <script>
        (function () {
            var form = document.getElementById('resetForm');
            var pw = document.getElementById('password');
            var cf = document.getElementById('password_confirmation');
            var bar = document.getElementById('strengthBar');
            var label = document.getElementById('strengthText');
            var matchMsg = document.getElementById('matchMsg');
            var caps = document.getElementById('capsWarn');
            var btn = document.getElementById('resetBtn');
            var btnText = document.getElementById('resetBtnText');
            var btnIcon = document.getElementById('resetBtnIcon');

            var reqs = {
                length: document.getElementById('req-length'),
                upper: document.getElementById('req-upper'),
                number: document.getElementById('req-number'),
                special: document.getElementById('req-special')
            };

            var levels = [
                { width: '0%',   color: 'bg-rose-500',    text: 'Weak' },
                { width: '25%',  color: 'bg-rose-500',    text: 'Weak' },
                { width: '50%',  color: 'bg-amber-500',   text: 'Fair' },
                { width: '75%',  color: 'bg-sky-500',     text: 'Good' },
                { width: '100%', color: 'bg-emerald-500', text: 'Strong' }
            ];

            function setReq(el, ok) {
                if (!el) return;
                var icon = el.querySelector('i');
                el.classList.toggle('text-emerald-400', ok);
                el.classList.toggle('text-slate-500', !ok);
                icon.className = ok ? 'fas fa-check-circle text-[9px]' : 'fas fa-circle text-[6px]';
            }

            function setMatch() {
                if (!cf.value) { matchMsg.classList.add('hidden'); matchMsg.classList.remove('flex'); return true; }
                var ok = pw.value === cf.value;
                matchMsg.classList.remove('hidden');
                matchMsg.classList.add('flex');
                matchMsg.className = 'flex text-[10px] mt-1.5 ml-1 items-center gap-1.5 ' + (ok ? 'text-emerald-400' : 'text-rose-400');
                matchMsg.innerHTML = ok
                    ? '<i class="fas fa-check-circle text-[9px]"></i> Passwords match'
                    : '<i class="fas fa-times-circle text-[9px]"></i> Passwords do not match yet';
                return ok;
            }

            function evaluate() {
                var v = pw.value, score = 0;
                var ok = {
                    length: v.length >= 8,
                    upper: /[a-z]/.test(v) && /[A-Z]/.test(v),
                    number: /[0-9]/.test(v),
                    special: /[^a-zA-Z0-9]/.test(v)
                };
                Object.keys(ok).forEach(function (k) { setReq(reqs[k], ok[k]); if (ok[k]) score++; });

                var r = levels[score];
                bar.className = 'h-full transition-all duration-300 rounded-full ' + r.color;
                bar.style.width = v ? r.width : '0%';
                label.textContent = v ? r.text : 'Weak';
                label.className = 'text-[9px] font-black uppercase tracking-widest min-w-[45px] text-right ' +
                    (score >= 3 ? 'text-emerald-400' : score >= 2 ? 'text-amber-400' : 'text-slate-500');
                setMatch();
            }

            function capsHandler(e) {
                if (!e.getModifierState) return;
                var on = e.getModifierState('CapsLock');
                caps.classList.toggle('hidden', !on);
                caps.classList.toggle('flex', on);
            }

            if (!pw || !cf) return;
            pw.addEventListener('input', evaluate);
            cf.addEventListener('input', setMatch);
            [pw, cf].forEach(function (el) {
                el.addEventListener('keyup', capsHandler);
                el.addEventListener('keydown', capsHandler);
                el.addEventListener('blur', function () { caps.classList.add('hidden'); caps.classList.remove('flex'); });
            });

            form.addEventListener('submit', function (e) {
                if (pw.value.length < 8) { e.preventDefault(); pw.focus(); return; }
                if (pw.value !== cf.value) { e.preventDefault(); setMatch(); cf.focus(); return; }
                btn.disabled = true;
                btn.style.opacity = '.75';
                btn.style.cursor = 'wait';
                btnText.textContent = 'Saving...';
                btnIcon.className = 'fas fa-circle-notch fa-spin text-xs';
            });
        })();
    </script>
@endsection