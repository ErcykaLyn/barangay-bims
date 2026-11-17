@extends('layouts.admin')

@section('content')
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
    .ann-preview-body ul { list-style: disc; padding-left: 1.25rem; margin: .5rem 0; }
    .ann-preview-body ol { list-style: decimal; padding-left: 1.25rem; margin: .5rem 0; }
    .ann-preview-body a { color: #047857; text-decoration: underline; }
    .ann-preview-body p { margin-bottom: .5rem; }
    .ann-scroll::-webkit-scrollbar { width: 6px; }
    .ann-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 999px; }
</style>

<div class="relative">
    {{-- Decorative background blobs --}}
    <div class="pointer-events-none absolute inset-x-0 -top-10 h-[420px] overflow-hidden -z-10">
        <div class="absolute -top-24 -left-16 h-72 w-72 rounded-full bg-emerald-200/40 blur-3xl"></div>
        <div class="absolute top-10 right-0 h-80 w-80 rounded-full bg-amber-200/30 blur-3xl"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">

        {{-- BREADCRUMB --}}
        <nav class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-5 ann-fade">
            <a href="{{ route('admin.announcements.index') }}" class="inline-flex items-center gap-2 text-emerald-600 hover:text-emerald-700 transition">
                <i class="fas fa-arrow-left"></i> Announcements
            </a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <span class="text-slate-500">Create New</span>
        </nav>

        {{-- HERO --}}
        <div class="relative overflow-hidden rounded-3xl ann-pattern shadow-xl shadow-emerald-900/10 mb-8 ann-fade">
            <div class="relative px-6 sm:px-10 py-8 sm:py-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-start gap-5">
                    <div class="ann-float shrink-0 h-16 w-16 rounded-2xl bg-white/15 backdrop-blur border border-white/25 flex items-center justify-center shadow-lg">
                        <i class="fas fa-bullhorn text-white text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-emerald-100/90 text-[11px] font-black uppercase tracking-[0.25em] mb-2">Barangay Bactad East</p>
                        <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">Create New Announcement</h1>
                        <p class="text-emerald-50/90 text-sm mt-2 max-w-xl">Mag-publish ng mahahalagang update, advisory, o balita para sa mga residente ng barangay.</p>
                    </div>
                </div>

                <div class="flex md:flex-col gap-2 text-xs font-bold">
                    <span class="inline-flex items-center gap-2 bg-white/15 border border-white/20 text-white rounded-full px-4 py-2 backdrop-blur">
                        <i class="fas fa-pen-nib text-amber-300"></i> Isulat
                    </span>
                    <span class="inline-flex items-center gap-2 bg-white/15 border border-white/20 text-white rounded-full px-4 py-2 backdrop-blur">
                        <i class="fas fa-eye text-amber-300"></i> I-preview
                    </span>
                    <span class="inline-flex items-center gap-2 bg-white/15 border border-white/20 text-white rounded-full px-4 py-2 backdrop-blur">
                        <i class="fas fa-paper-plane text-amber-300"></i> I-publish
                    </span>
                </div>
            </div>
        </div>

        {{-- ERROR SUMMARY --}}
        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 p-5 flex items-start gap-4 ann-fade">
                <div class="h-10 w-10 shrink-0 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
                <div>
                    <p class="text-sm font-black text-rose-800 uppercase tracking-wider">May kailangan pang ayusin</p>
                    <ul class="mt-1 text-sm text-rose-700 list-disc pl-5 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.announcements.store') }}" method="POST" id="announcementForm"
              class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            @csrf

            {{-- ============ LEFT COLUMN ============ --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- CARD: BASIC INFO --}}
                <section class="bg-white rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition overflow-hidden ann-fade">
                    <header class="flex items-center justify-between gap-3 px-6 sm:px-8 py-5 border-b border-slate-100 bg-gradient-to-r from-emerald-50/70 to-white">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-md shadow-emerald-600/30 font-black">1</div>
                            <div>
                                <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider">Basic Information</h2>
                                <p class="text-xs text-slate-500">Pamagat at buong detalye ng anunsyo</p>
                            </div>
                        </div>
                        <span class="hidden sm:inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                            <span class="text-red-500 text-sm leading-none">*</span> Required
                        </span>
                    </header>

                    <div class="p-6 sm:p-8 space-y-7">
                        {{-- Title --}}
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="title" class="block text-[11px] font-bold text-slate-600 uppercase tracking-widest">
                                    Title <span class="text-red-500">*</span>
                                </label>
                                <span id="titleCount" class="text-[11px] font-semibold text-slate-400 tabular-nums">0 / 150</span>
                            </div>
                            <div class="relative group">
                                <i class="fas fa-heading absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-emerald-600 transition"></i>
                                <input type="text" id="title" name="title" value="{{ old('title') }}" required maxlength="150"
                                       placeholder="e.g., Barangay Assembly 2025"
                                       class="w-full bg-slate-50/60 border @error('title') border-rose-400 @else border-slate-300 @enderror rounded-2xl pl-11 pr-4 py-3.5 text-base font-semibold text-slate-800 placeholder:font-normal placeholder:text-slate-400 focus:bg-white focus:ring-4 focus:ring-emerald-500/15 focus:border-emerald-500 transition outline-none">
                            </div>
                            @error('title')
                                <p class="text-rose-500 text-xs mt-1.5 flex items-center gap-1.5"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Content --}}
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="content" class="block text-[11px] font-bold text-slate-600 uppercase tracking-widest">
                                    Content <span class="text-red-500">*</span>
                                </label>
                                <span id="contentCount" class="text-[11px] font-semibold text-slate-400 tabular-nums">0 characters</span>
                            </div>

                            {{-- Quick templates --}}
                            <div class="flex flex-wrap items-center gap-2 mb-3">
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 mr-1">Quick start:</span>
                                <button type="button" data-template="advisory"
                                        class="tpl-btn inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-full border border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100 transition">
                                    <i class="fas fa-triangle-exclamation"></i> Advisory
                                </button>
                                <button type="button" data-template="event"
                                        class="tpl-btn inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-full border border-sky-200 bg-sky-50 text-sky-700 hover:bg-sky-100 transition">
                                    <i class="fas fa-calendar-check"></i> Event
                                </button>
                                <button type="button" data-template="notice"
                                        class="tpl-btn inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-full border border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition">
                                    <i class="fas fa-clipboard-list"></i> Notice
                                </button>
                            </div>

                            <div class="rounded-2xl border @error('content') border-rose-400 @else border-slate-300 @enderror overflow-hidden focus-within:ring-4 focus-within:ring-emerald-500/15 focus-within:border-emerald-500 transition bg-white">
                                {{-- Toolbar --}}
                                <div class="flex flex-wrap items-center gap-1 px-3 py-2 bg-slate-50 border-b border-slate-200">
                                    <button type="button" data-fmt="bold" title="Bold"
                                            class="fmt-btn h-8 w-8 rounded-lg text-slate-600 hover:bg-white hover:text-emerald-700 hover:shadow-sm transition"><i class="fas fa-bold"></i></button>
                                    <button type="button" data-fmt="italic" title="Italic"
                                            class="fmt-btn h-8 w-8 rounded-lg text-slate-600 hover:bg-white hover:text-emerald-700 hover:shadow-sm transition"><i class="fas fa-italic"></i></button>
                                    <span class="w-px h-5 bg-slate-200 mx-1"></span>
                                    <button type="button" data-fmt="ul" title="Bulleted list"
                                            class="fmt-btn h-8 w-8 rounded-lg text-slate-600 hover:bg-white hover:text-emerald-700 hover:shadow-sm transition"><i class="fas fa-list-ul"></i></button>
                                    <button type="button" data-fmt="ol" title="Numbered list"
                                            class="fmt-btn h-8 w-8 rounded-lg text-slate-600 hover:bg-white hover:text-emerald-700 hover:shadow-sm transition"><i class="fas fa-list-ol"></i></button>
                                    <span class="w-px h-5 bg-slate-200 mx-1"></span>
                                    <button type="button" data-fmt="link" title="Insert link"
                                            class="fmt-btn h-8 w-8 rounded-lg text-slate-600 hover:bg-white hover:text-emerald-700 hover:shadow-sm transition"><i class="fas fa-link"></i></button>
                                    <button type="button" data-fmt="br" title="Line break"
                                            class="fmt-btn h-8 w-8 rounded-lg text-slate-600 hover:bg-white hover:text-emerald-700 hover:shadow-sm transition"><i class="fas fa-level-down-alt"></i></button>
                                    <span class="ml-auto hidden sm:inline text-[10px] font-bold uppercase tracking-widest text-slate-400">HTML supported</span>
                                </div>

                                <textarea id="content" name="content" rows="12" required
                                          placeholder="Isulat dito ang buong detalye ng anunsyo..."
                                          class="w-full block bg-white px-4 py-4 text-sm leading-relaxed text-slate-700 placeholder:text-slate-400 resize-y outline-none border-0 focus:ring-0">{{ old('content') }}</textarea>
                            </div>

                            <p class="text-xs text-slate-400 mt-2 flex items-start gap-1.5">
                                <i class="fas fa-info-circle mt-0.5"></i>
                                <span>Pwedeng gumamit ng plain text o simpleng HTML (tulad ng <code class="bg-slate-100 px-1 rounded text-slate-600">&lt;strong&gt;</code>, <code class="bg-slate-100 px-1 rounded text-slate-600">&lt;ul&gt;</code>, <code class="bg-slate-100 px-1 rounded text-slate-600">&lt;a&gt;</code>).</span>
                            </p>
                            @error('content')
                                <p class="text-rose-500 text-xs mt-1.5 flex items-center gap-1.5"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                {{-- CARD: PUBLISHING SETTINGS --}}
                <section class="bg-white rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition overflow-hidden ann-fade">
                    <header class="flex items-center gap-3 px-6 sm:px-8 py-5 border-b border-slate-100 bg-gradient-to-r from-emerald-50/70 to-white">
                        <div class="h-10 w-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-md shadow-emerald-600/30 font-black">2</div>
                        <div>
                            <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider">Publishing Settings</h2>
                            <p class="text-xs text-slate-500">Kailan ilalabas at makikita ba agad</p>
                        </div>
                    </header>

                    <div class="p-6 sm:p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Published Date --}}
                        <div>
                            <label for="published_date" class="block text-[11px] font-bold text-slate-600 uppercase tracking-widest mb-2">Published Date</label>
                            <div class="relative group">
                                <i class="fas fa-calendar-alt absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-emerald-600 transition"></i>
                                <input type="date" id="published_date" name="published_date" value="{{ old('published_date', date('Y-m-d')) }}"
                                       class="w-full bg-slate-50/60 border @error('published_date') border-rose-400 @else border-slate-300 @enderror rounded-2xl pl-11 pr-4 py-3.5 text-sm font-semibold text-slate-700 focus:bg-white focus:ring-4 focus:ring-emerald-500/15 focus:border-emerald-500 transition outline-none">
                            </div>
                            <div class="flex flex-wrap gap-2 mt-3">
                                <button type="button" data-days="0" class="date-chip text-[11px] font-bold px-3 py-1.5 rounded-full border border-slate-200 bg-white text-slate-600 hover:border-emerald-400 hover:text-emerald-700 hover:bg-emerald-50 transition">Ngayon</button>
                                <button type="button" data-days="1" class="date-chip text-[11px] font-bold px-3 py-1.5 rounded-full border border-slate-200 bg-white text-slate-600 hover:border-emerald-400 hover:text-emerald-700 hover:bg-emerald-50 transition">Bukas</button>
                                <button type="button" data-days="7" class="date-chip text-[11px] font-bold px-3 py-1.5 rounded-full border border-slate-200 bg-white text-slate-600 hover:border-emerald-400 hover:text-emerald-700 hover:bg-emerald-50 transition">Next week</button>
                            </div>
                            <p class="text-xs text-slate-400 mt-3">Default ay ngayong araw. Pwede mong i-schedule sa hinaharap.</p>
                            @error('published_date')
                                <p class="text-rose-500 text-xs mt-1.5 flex items-center gap-1.5"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Status Toggle --}}
                        <div>
                            <span class="block text-[11px] font-bold text-slate-600 uppercase tracking-widest mb-2">Visibility Status</span>
                            <label for="is_active" class="flex items-center gap-4 cursor-pointer bg-slate-50 border border-slate-200 rounded-2xl p-4 hover:border-emerald-300 hover:bg-emerald-50/40 transition">
                                <div class="relative shrink-0">
                                    <input type="checkbox" name="is_active" id="is_active" value="1" checked class="sr-only peer">
                                    <div class="w-14 h-8 bg-slate-300 rounded-full peer-checked:bg-emerald-600 transition-colors peer-focus-visible:ring-4 peer-focus-visible:ring-emerald-500/30"></div>
                                    <div class="absolute top-1 left-1 h-6 w-6 bg-white rounded-full shadow transition-transform peer-checked:translate-x-6"></div>
                                </div>
                                <div class="min-w-0">
                                    <p id="statusLabel" class="text-sm font-black text-slate-800 uppercase tracking-wider">Active</p>
                                    <p id="statusHint" class="text-xs text-slate-500 mt-0.5">Makikita agad ng mga residente kapag naka-on.</p>
                                </div>
                            </label>

                            <div class="mt-4 rounded-2xl bg-amber-50 border border-amber-100 p-4 flex items-start gap-3">
                                <i class="fas fa-lightbulb text-amber-500 mt-0.5"></i>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Kapag naka-off ang <strong>Active</strong>, hindi ito lalabas sa public announcements page. Pero pwede mo pa ring i-edit o i-activate anytime.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            {{-- ============ RIGHT COLUMN (sticky) ============ --}}
            <aside class="space-y-6 lg:sticky lg:top-6">

                {{-- LIVE PREVIEW --}}
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden ann-fade">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                        <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-eye text-emerald-600"></i> Live Preview
                        </h3>
                        <span id="pvBadge" class="inline-flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Live
                        </span>
                    </div>

                    <div class="p-5 bg-slate-50/70">
                        <article class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                            <div class="h-1.5 bg-gradient-to-r from-emerald-600 via-teal-500 to-amber-400"></div>
                            <div class="p-5">
                                <div class="flex items-center gap-2 text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-3">
                                    <i class="fas fa-calendar-day text-emerald-500"></i>
                                    <span id="pvDate">—</span>
                                </div>
                                <h4 id="pvTitle" class="text-lg font-extrabold text-slate-800 leading-snug mb-3 break-words">Pamagat ng anunsyo</h4>
                                <div id="pvContent" class="ann-preview-body ann-scroll text-sm text-slate-600 leading-relaxed max-h-64 overflow-y-auto break-words">
                                    <p class="text-slate-400 italic">Lalabas dito ang preview ng iyong isinusulat...</p>
                                </div>
                                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-2 text-[11px] text-slate-400">
                                    <i class="fas fa-landmark text-emerald-500"></i> Barangay Bactad East
                                </div>
                            </div>
                        </article>
                    </div>
                </div>

                {{-- CHECKLIST + ACTIONS --}}
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 ann-fade">
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest flex items-center gap-2 mb-4">
                        <i class="fas fa-list-check text-emerald-600"></i> Bago Mag-publish
                    </h3>

                    <ul class="space-y-3 mb-5">
                        <li class="flex items-center gap-3 text-sm" data-check="title">
                            <span class="chk h-6 w-6 rounded-full bg-slate-100 text-slate-300 flex items-center justify-center text-xs transition"><i class="fas fa-check"></i></span>
                            <span class="text-slate-600">May pamagat</span>
                        </li>
                        <li class="flex items-center gap-3 text-sm" data-check="content">
                            <span class="chk h-6 w-6 rounded-full bg-slate-100 text-slate-300 flex items-center justify-center text-xs transition"><i class="fas fa-check"></i></span>
                            <span class="text-slate-600">May sapat na detalye (20+ characters)</span>
                        </li>
                        <li class="flex items-center gap-3 text-sm" data-check="date">
                            <span class="chk h-6 w-6 rounded-full bg-slate-100 text-slate-300 flex items-center justify-center text-xs transition"><i class="fas fa-check"></i></span>
                            <span class="text-slate-600">May published date</span>
                        </li>
                    </ul>

                    <div class="mb-5">
                        <div class="flex justify-between text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1.5">
                            <span>Progress</span><span id="progressText">0%</span>
                        </div>
                        <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div id="progressBar" class="h-full w-0 rounded-full bg-gradient-to-r from-emerald-500 to-teal-400 transition-all duration-500"></div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3">
                        <button type="submit" id="submitBtn"
                                class="group w-full px-6 py-4 rounded-2xl bg-gradient-to-r from-emerald-700 to-emerald-600 hover:from-emerald-800 hover:to-emerald-700 text-white font-black text-sm shadow-lg shadow-emerald-700/25 hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 transition flex items-center justify-center gap-2.5 uppercase tracking-wider">
                            <i class="fas fa-paper-plane group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition" id="submitIcon"></i>
                            <span id="submitText">Publish Announcement</span>
                        </button>
                        <a href="{{ route('admin.announcements.index') }}"
                           class="w-full px-6 py-3 rounded-2xl bg-white border border-slate-300 text-slate-600 font-bold text-sm hover:bg-slate-50 hover:border-slate-400 transition text-center uppercase tracking-wider">
                            Cancel
                        </a>
                    </div>
                </div>
            </aside>
        </form>
    </div>
</div>

<script>
(function () {
    var $ = function (id) { return document.getElementById(id); };
    var title = $('title'), content = $('content'), date = $('published_date'), active = $('is_active');

    var TEMPLATES = {
        advisory: '<strong>ADVISORY:</strong> [Ilagay ang paksa]\n\nMagandang araw, mga kabarangay!\n\nIpinababatid po sa lahat na [detalye ng advisory].\n\n<ul>\n<li>[Unang paalala]</li>\n<li>[Ikalawang paalala]</li>\n</ul>\n\nPara sa mga katanungan, makipag-ugnayan sa Barangay Hall.\n\nMaraming salamat po!',
        event: '<strong>PAANYAYA:</strong> [Pangalan ng event]\n\n<strong>Petsa:</strong> [Petsa]\n<strong>Oras:</strong> [Oras]\n<strong>Lugar:</strong> [Lugar]\n\nInaanyayahan po ang lahat ng residente na dumalo. [Dagdag na detalye].\n\nMaraming salamat po!',
        notice: '<strong>ABISO:</strong> [Paksa ng abiso]\n\nIpinababatid po sa lahat ng residente ng Barangay Bactad East na [detalye ng abiso].\n\nInaasahan po namin ang inyong kooperasyon.\n\n— Pamunuan ng Barangay'
    };

    // ---------- helpers ----------
    function esc(s) {
        return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    // Minimal sanitizer for the preview only (allows simple formatting tags)
    var ALLOWED = ['STRONG', 'B', 'EM', 'I', 'U', 'UL', 'OL', 'LI', 'A', 'BR', 'P'];
    function sanitize(html) {
        var doc = new DOMParser().parseFromString('<div>' + html + '</div>', 'text/html');
        var root = doc.body.firstChild;
        (function walk(node) {
            Array.prototype.slice.call(node.childNodes).forEach(function (child) {
                if (child.nodeType === 1) {
                    if (ALLOWED.indexOf(child.tagName) === -1) {
                        child.replaceWith(doc.createTextNode(child.textContent));
                        return;
                    }
                    Array.prototype.slice.call(child.attributes).forEach(function (a) {
                        var keep = child.tagName === 'A' && a.name === 'href' && /^(https?:|mailto:|tel:)/i.test(a.value);
                        if (!keep) child.removeAttribute(a.name);
                    });
                    if (child.tagName === 'A') { child.setAttribute('target', '_blank'); child.setAttribute('rel', 'noopener'); }
                    walk(child);
                }
            });
        })(root);
        return root.innerHTML;
    }

    function setCheck(name, ok) {
        var li = document.querySelector('[data-check="' + name + '"]');
        if (!li) return;
        var chk = li.querySelector('.chk');
        chk.className = 'chk h-6 w-6 rounded-full flex items-center justify-center text-xs transition ' +
            (ok ? 'bg-emerald-500 text-white shadow shadow-emerald-500/40' : 'bg-slate-100 text-slate-300');
        li.querySelector('span:last-child').className = ok ? 'text-slate-800 font-semibold' : 'text-slate-600';
    }

    function parseDate(v) {
        if (!v) return null;
        var d = new Date(v + 'T00:00:00');
        return isNaN(d.getTime()) ? null : d;
    }

    // ---------- main update ----------
    function update() {
        var t = title.value.trim(), c = content.value.trim();

        $('titleCount').textContent = title.value.length + ' / 150';
        $('contentCount').textContent = content.value.length.toLocaleString() + ' characters';

        // preview title / body
        $('pvTitle').textContent = t || 'Pamagat ng anunsyo';
        if (c) {
            var hasTags = /<[a-z][\s\S]*>/i.test(c);
            $('pvContent').innerHTML = hasTags
                ? sanitize(c.replace(/\n/g, '<br>'))
                : esc(c).replace(/\n/g, '<br>');
        } else {
            $('pvContent').innerHTML = '<p class="text-slate-400 italic">Lalabas dito ang preview ng iyong isinusulat...</p>';
        }

        // date + status
        var d = parseDate(date.value);
        var today = new Date(); today.setHours(0, 0, 0, 0);
        $('pvDate').textContent = d
            ? d.toLocaleDateString('en-PH', { year: 'numeric', month: 'long', day: 'numeric' })
            : 'Walang petsa';

        var badge = $('pvBadge');
        if (!active.checked) {
            badge.className = 'inline-flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full bg-slate-200 text-slate-600';
            badge.innerHTML = '<i class="fas fa-eye-slash"></i> Hidden';
            $('statusLabel').textContent = 'Inactive';
            $('statusHint').textContent = 'Hindi makikita ng mga residente. Naka-save bilang draft.';
        } else if (d && d > today) {
            badge.className = 'inline-flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full bg-amber-100 text-amber-700';
            badge.innerHTML = '<i class="fas fa-clock"></i> Scheduled';
            $('statusLabel').textContent = 'Active · Naka-schedule';
            $('statusHint').textContent = 'Lalabas ito sa napiling petsa.';
        } else {
            badge.className = 'inline-flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700';
            badge.innerHTML = '<span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Live';
            $('statusLabel').textContent = 'Active';
            $('statusHint').textContent = 'Makikita agad ng mga residente kapag naka-on.';
        }

        // checklist + progress
        var checks = { title: t.length > 0, content: c.length >= 20, date: !!d };
        var done = 0;
        Object.keys(checks).forEach(function (k) { setCheck(k, checks[k]); if (checks[k]) done++; });
        var pct = Math.round((done / 3) * 100);
        $('progressBar').style.width = pct + '%';
        $('progressText').textContent = pct + '%';
    }

    // ---------- toolbar ----------
    function wrapSelection(open, close, placeholder) {
        var s = content.selectionStart, e = content.selectionEnd;
        var sel = content.value.substring(s, e) || placeholder || '';
        content.setRangeText(open + sel + close, s, e, 'end');
        content.focus();
        content.setSelectionRange(s + open.length, s + open.length + sel.length);
        update();
    }

    document.querySelectorAll('.fmt-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            switch (btn.dataset.fmt) {
                case 'bold':   wrapSelection('<strong>', '</strong>', 'bold text'); break;
                case 'italic': wrapSelection('<em>', '</em>', 'italic text'); break;
                case 'ul':     wrapSelection('<ul>\n<li>', '</li>\n</ul>', 'item'); break;
                case 'ol':     wrapSelection('<ol>\n<li>', '</li>\n</ol>', 'item'); break;
                case 'br':     wrapSelection('<br>\n', '', ''); break;
                case 'link':
                    var url = prompt('I-paste ang link (https://...)');
                    // FIXED: "Click here" na ang default label (dating "link text")
                    if (url) wrapSelection('<a href="' + url.replace(/"/g, '&quot;') + '">', '</a>', 'Click here');
                    break;
            }
        });
    });

    // ---------- templates ----------
    document.querySelectorAll('.tpl-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (content.value.trim() && !confirm('Papalitan nito ang kasalukuyang content. Ituloy?')) return;
            content.value = TEMPLATES[btn.dataset.template];
            content.focus();
            update();
        });
    });

    // ---------- date chips ----------
    document.querySelectorAll('.date-chip').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var d = new Date();
            d.setDate(d.getDate() + parseInt(btn.dataset.days, 10));
            var m = String(d.getMonth() + 1).padStart(2, '0');
            var day = String(d.getDate()).padStart(2, '0');
            date.value = d.getFullYear() + '-' + m + '-' + day;
            update();
        });
    });

    // ---------- submit state ----------
    $('announcementForm').addEventListener('submit', function () {
        var btn = $('submitBtn');
        setTimeout(function () { btn.disabled = true; }, 0);
        btn.classList.add('opacity-80', 'cursor-not-allowed');
        $('submitIcon').className = 'fas fa-spinner fa-spin';
        $('submitText').textContent = 'Nagpa-publish...';
    });

    [title, content, date].forEach(function (el) { el.addEventListener('input', update); });
    active.addEventListener('change', update);
    update();
})();
</script>
@endsection