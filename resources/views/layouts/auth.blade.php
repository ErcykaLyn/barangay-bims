<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'BIMS | Login')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/brgy_logo.png.jpg') }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --g-950: #04251c; --g-900: #063a2b; --g-800: #065f46; --g-600: #059669;
            --g-500: #10b981; --g-400: #34d399; --g-200: #a7f3d0; --g-50: #effaf5;
            --ink: #0b2a20; --muted: #4d6b5f;
        }
        body { font-family: 'Instrument Sans', system-ui, sans-serif; background: #f6fbf8; color: var(--ink); }
        h1, h2, h3, .font-display { font-family: 'Bricolage Grotesque', 'Instrument Sans', sans-serif; letter-spacing: -0.02em; }

        /* ---------- Header ---------- */
        .site-header { background: rgba(246, 251, 248, .88); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(6, 95, 70, .12); }
        .nav-link { font-size: .875rem; font-weight: 500; color: var(--muted); transition: color .2s; }
        .nav-link:hover { color: var(--g-800); }
        .role-tab { font-size: .8rem; font-weight: 600; padding: .45rem .9rem; border-radius: 999px; color: var(--g-800); transition: background .2s, color .2s; }
        .role-tab:hover { background: var(--g-50); }
        .role-tab.active { background: var(--g-800); color: #fff; }

        /* ---------- Hero ---------- */
        .hero {
            position: relative; overflow: hidden; color: #fff; background: var(--g-950);
            background-image: @yield('mesh-bg', "radial-gradient(at 0% 0%, rgba(16,185,129,.28) 0, transparent 55%), radial-gradient(at 100% 100%, rgba(52,211,153,.18) 0, transparent 50%)");
        }
        .hero::before { /* contour-line texture, nods to a barangay map */
            content: ''; position: absolute; inset: 0; pointer-events: none; opacity: .5;
            background-image: repeating-radial-gradient(circle at 85% 20%, transparent 0 34px, rgba(52, 211, 153, .08) 34px 35px);
        }
        .hero-badge { display: inline-flex; align-items: center; gap: .5rem; padding: .4rem .85rem; border-radius: 999px; font-size: .8rem; font-weight: 600;
            background: rgba(16, 185, 129, .16); border: 1px solid rgba(52, 211, 153, .35); color: var(--g-200); }
        .hero-point { display: flex; gap: .75rem; align-items: flex-start; color: rgba(255, 255, 255, .78); font-size: .95rem; }
        .hero-point i { margin-top: .3rem; color: var(--g-400); font-size: .8rem; }
        .rise { animation: rise .7s cubic-bezier(.2, .7, .2, 1) both; }
        @keyframes rise { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: none; } }

        /* ---------- Login card ---------- */
        .auth-card { background: #fff; border-radius: 1.5rem; padding: 2rem; color: var(--ink);
            box-shadow: 0 30px 70px -25px rgba(0, 0, 0, .55), 0 0 0 1px rgba(255, 255, 255, .6); }
        .lbl { display: block; font-size: .8rem; font-weight: 600; color: var(--g-900); margin: 0 0 .4rem .15rem; }
        .input-wrapper { position: relative; }
        .custom-input { width: 100%; height: 3rem; padding: 0 2.75rem; border-radius: .85rem; font-size: .9rem; color: var(--ink);
            background: var(--g-50); border: 1.5px solid transparent; outline: none; transition: border-color .2s, background .2s, box-shadow .2s; }
        .custom-input::placeholder { color: #8aa79b; }
        .custom-input:focus { background: #fff; border-color: var(--g-500); box-shadow: 0 0 0 4px rgba(16, 185, 129, .18); }
        .input-icon { position: absolute; left: 1rem; bottom: 0; height: 3rem; display: flex; align-items: center; color: var(--g-600); font-size: .85rem; pointer-events: none; }
        .toggle-password { position: absolute; right: .35rem; bottom: 0; height: 3rem; width: 2.6rem; color: #6b8c7f; transition: color .2s; }
        .toggle-password:hover { color: var(--g-800); }
        .login-btn { width: 100%; height: 3.1rem; display: flex; align-items: center; justify-content: center; gap: .6rem; border-radius: .85rem;
            font-weight: 700; font-size: .95rem; color: #fff; background: linear-gradient(135deg, var(--g-500), var(--g-800));
            box-shadow: 0 10px 24px -10px rgba(5, 150, 105, .8); transition: transform .2s, box-shadow .2s, filter .2s; }
        .login-btn:hover { transform: translateY(-2px); filter: brightness(1.06); box-shadow: 0 16px 28px -12px rgba(5, 150, 105, .9); }
        .login-btn:active { transform: translateY(0); }
        .text-link { font-weight: 600; color: var(--g-600); text-underline-offset: 4px; transition: color .2s; }
        .text-link:hover { color: var(--g-800); text-decoration: underline; }
        .check { width: 1rem; height: 1rem; accent-color: var(--g-600); cursor: pointer; }

        /* ---------- Sections ---------- */
        .service { background: #fff; border: 1px solid rgba(6, 95, 70, .12); border-radius: 1.25rem; padding: 1.5rem; transition: border-color .25s, transform .25s; }
        .service:hover { border-color: var(--g-500); transform: translateY(-3px); }
        .service-icon { width: 2.9rem; height: 2.9rem; display: grid; place-items: center; border-radius: .9rem; background: var(--g-50); color: var(--g-600); font-size: 1.1rem; }
        .step-line { position: absolute; left: 1.35rem; top: 2.8rem; bottom: -1.4rem; width: 2px; background: linear-gradient(var(--g-400), transparent); }

        :focus-visible { outline: 3px solid var(--g-400); outline-offset: 2px; }
        @media (prefers-reduced-motion: reduce) { .rise { animation: none; } * { transition: none !important; } }
    </style>
    @stack('styles')
</head>
<body class="antialiased">

    @php
        $current = trim(preg_replace('/^.*(resident|bhw|admin).*$/i', '$1', request()->path()) ?: 'resident');
        $current = in_array(strtolower($current), ['resident', 'bhw', 'admin']) ? strtolower($current) : 'resident';
    @endphp

    {{-- ================= HEADER ================= --}}
    <header class="site-header sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-5 h-16 flex items-center justify-between gap-4">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/brgy_logo.png.jpg') }}" alt="Barangay Bactad East logo"
                     class="w-11 h-11 rounded-full object-cover bg-white ring-2 ring-emerald-600/30">

                <span class="leading-tight">
                    <span class="block font-display font-extrabold text-lg text-emerald-950">BIMS</span>
                    <span class="block text-[11px] text-slate-500">Barangay Bactad East</span>
                </span>
            </a>

            <nav class="hidden lg:flex items-center gap-7">
                <a href="#services" class="nav-link">Services</a>
                <a href="#how" class="nav-link">How it works</a>
                <a href="#contact" class="nav-link">Contact</a>
            </nav>


        </div>
    </header>

    {{-- ================= HERO + LOGIN ================= --}}
    <main>
        <section class="hero">
            <div class="relative max-w-7xl mx-auto px-5 py-14 lg:py-24 grid lg:grid-cols-[1.1fr_.9fr] gap-12 lg:gap-16 items-center">

                <div class="rise">
                    <span class="hero-badge">
                        <i class="@yield('badge-icon', 'fas fa-user')"></i> @yield('badge-text', 'Resident Portal')
                    </span>

                    <h1 class="mt-6 text-4xl sm:text-5xl xl:text-6xl font-extrabold leading-[1.05]">
                        @yield('visual-title', 'Manage Your')
                        <span class="block text-emerald-400">@yield('visual-highlight', 'Barangay Account')</span>
                    </h1>

                    <p class="mt-5 max-w-lg text-lg text-emerald-50/75 leading-relaxed">
                        @yield('visual-description', 'Access barangay services, submit requests, and stay updated with community announcements.')
                    </p>

                    @if($current === 'resident')
                    <ul class="mt-8 space-y-3 max-w-md">
                        <li class="hero-point"><i class="fas fa-circle-check"></i> Request certificates and clearances without queuing</li>
                        <li class="hero-point"><i class="fas fa-circle-check"></i> Track every request from submission to release</li>
                        <li class="hero-point"><i class="fas fa-circle-check"></i> Read official announcements from the barangay hall</li>
                    </ul>
                    @endif

                    <p class="mt-10 inline-flex items-center gap-2 text-sm text-emerald-200/80">
                        <i class="fas fa-shield-halved text-emerald-400"></i> @yield('visual-footer', 'Secure & Encrypted Connection')
                    </p>
                </div>

                <div class="rise w-full max-w-md lg:ml-auto" style="animation-delay:.12s">
                    <div class="auth-card">
                        <div class="flex justify-center mb-5 lg:hidden">
                            <img src="{{ asset('images/brgy_logo.png.jpg') }}" alt="" class="w-16 h-16 rounded-full object-cover">
                        </div>

                        @if(session('success'))
                            <div class="mb-5 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs flex items-start gap-2.5" role="status">
                                <i class="fas fa-circle-check mt-0.5 text-emerald-600"></i><span>{{ session('success') }}</span>
                            </div>
                        @endif
                        @if(session('error'))
                            <div class="mb-5 p-3 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs flex items-start gap-2.5" role="alert">
                                <i class="fas fa-circle-exclamation mt-0.5 text-rose-500"></i><span>{{ session('error') }}</span>
                            </div>
                        @endif

                        @yield('form-content')
                    </div>
                </div>
            </div>
        </section>

        {{-- ================= SERVICES ================= --}}
        <section id="services" class="max-w-7xl mx-auto px-5 py-20">
            <div class="max-w-2xl">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-emerald-950">Everything the barangay hall does, now online</h2>
                <p class="mt-3 text-slate-600">One account covers records, requests, and community updates for Barangay Bactad East.</p>
            </div>

            <div class="mt-10 grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach([
                    ['fa-file-signature', 'Document requests', 'Apply for clearance, indigency, and residency certificates.'],
                    ['fa-people-roof',    'Household profiling', 'Keep family and resident records accurate and up to date.'],
                    ['fa-heart-pulse',    'Health records',     'Barangay health workers manage checkups and community health data.'],
                    ['fa-bullhorn',       'Announcements',      'Get notices on events, programs, and advisories first.'],
                ] as [$icon, $title, $text])
                    <article class="service">
                        <div class="service-icon"><i class="fas {{ $icon }}"></i></div>
                        <h3 class="mt-4 text-lg font-bold text-emerald-950">{{ $title }}</h3>
                        <p class="mt-1.5 text-sm text-slate-600 leading-relaxed">{{ $text }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        {{-- ================= HOW IT WORKS ================= --}}
        <section id="how" class="bg-white border-y border-emerald-900/10">
            <div class="max-w-7xl mx-auto px-5 py-20 grid lg:grid-cols-[.8fr_1.2fr] gap-12">
                <div>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-emerald-950">Get your first request done in three steps</h2>
                    <p class="mt-3 text-slate-600 max-w-md">No account yet? Registration takes a few minutes, and the barangay verifies it before you sign in.</p>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('register') ? route('register') : '#' }}" class="login-btn mt-8 !w-auto inline-flex px-7">
                        <span>Create resident account</span><i class="fas fa-arrow-right-long text-xs"></i>
                    </a>
                </div>

                <ol class="space-y-8">
                    @foreach([
                        ['Register', 'Fill in your details and upload a valid ID so the barangay can confirm you live here.'],
                        ['Wait for approval', 'Staff review your account. You can check the status anytime from the login page.'],
                        ['Request and track', 'Sign in, choose a service, and follow your request until it is ready for release.'],
                    ] as $i => [$title, $text])
                        <li class="relative pl-16">
                            <span class="absolute left-0 top-0 w-11 h-11 rounded-full grid place-items-center font-display font-bold text-white" style="background:var(--g-600)">{{ $i + 1 }}</span>
                            @if(!$loop->last)<span class="step-line"></span>@endif
                            <h3 class="text-lg font-bold text-emerald-950">{{ $title }}</h3>
                            <p class="mt-1 text-slate-600 max-w-lg">{{ $text }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>
    </main>

    {{-- ================= FOOTER ================= --}}
    <footer id="contact" style="background:var(--g-950)" class="text-emerald-50/80">
        <div class="max-w-7xl mx-auto px-5 py-14 grid md:grid-cols-3 gap-10">
            <div>
                <div class="flex items-center gap-3 text-white">
                    <img src="{{ asset('images/brgy_logo.png.jpg') }}" alt="Barangay Bactad East logo" class="w-11 h-11 rounded-full object-cover bg-white">
                    <span class="font-display font-extrabold text-xl">BIMS</span>
                </div>
                <p class="mt-4 text-sm leading-relaxed max-w-xs">Barangay Information Management System for Barangay Bactad East.</p>
            </div>
            <div class="text-sm space-y-3">
                <h4 class="font-display font-bold text-white text-base">Barangay hall</h4>
                <p><i class="fas fa-location-dot w-5 text-emerald-400"></i> Bactad East, Urdaneta City, Pangasinan</p>
                <p><i class="fas fa-envelope w-5 text-emerald-400"></i> info@bactadeast.gov.ph</p>
                <p><i class="fas fa-clock w-5 text-emerald-400"></i> Mon–Fri, 8:00 AM – 5:00 PM</p>
            </div>
            <div class="text-sm space-y-3">
                <h4 class="font-display font-bold text-white text-base">Quick links</h4>
                <a href="#services" class="block hover:text-white transition">Services</a>
                <a href="#how" class="block hover:text-white transition">How it works</a>
                <a href="{{ \Illuminate\Support\Facades\Route::has('register') ? route('register') : '#' }}" class="block hover:text-white transition">Register</a>
            </div>
        </div>
        <div class="border-t border-white/10 py-5 text-center text-xs text-emerald-100/60">
            © {{ date('Y') }} Barangay Bactad East. All rights reserved.
        </div>
    </footer>

    <script>
        document.querySelectorAll('.toggle-password').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = document.getElementById(btn.dataset.target);
                var icon = btn.querySelector('i');
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                icon.className = show ? 'fas fa-eye' : 'fas fa-eye-slash';
                btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
            });
        });
    </script>
    @stack('scripts')
</body>
</html>