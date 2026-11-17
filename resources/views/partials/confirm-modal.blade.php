{{-- Reusable confirm modal. Include ONCE in layouts/admin.blade.php, right before </body>:
     @include('partials.confirm-modal')
--}}
<style>
    @keyframes cfmPop { from { opacity: 0; transform: translateY(8px) scale(.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
    @keyframes cfmShake { 0%,100% { transform: rotate(0); } 20% { transform: rotate(-10deg); } 40% { transform: rotate(10deg); } 60% { transform: rotate(-6deg); } 80% { transform: rotate(6deg); } }
    .cfm-pop { animation: cfmPop .22s ease both; }
    .cfm-shake { animation: cfmShake .6s ease .15s both; }
</style>

<div id="cfmModal" class="hidden fixed inset-0 z-[9999] items-center justify-center p-4" role="alertdialog" aria-modal="true" aria-labelledby="cfmTitle" aria-describedby="cfmMessage">
    <div id="cfmBackdrop" class="absolute inset-0 bg-slate-900/55 backdrop-blur-sm"></div>

    <div class="cfm-pop relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden">
        <div id="cfmBar" class="h-1.5 bg-gradient-to-r from-rose-500 to-orange-400"></div>

        <div class="p-7 text-center">
            <div id="cfmIconWrap" class="cfm-shake mx-auto h-16 w-16 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center text-2xl mb-5">
                <i id="cfmIcon" class="fas fa-trash-alt"></i>
            </div>

            <h3 id="cfmTitle" class="text-xl font-extrabold text-slate-800">Burahin?</h3>
            <p id="cfmMessage" class="text-sm text-slate-500 mt-2 leading-relaxed"></p>

            <div id="cfmWarn" class="mt-4 inline-flex items-center gap-2 text-xs font-bold text-rose-600 bg-rose-50 border border-rose-100 rounded-full px-4 py-2">
                <i class="fas fa-triangle-exclamation"></i> <span>Hindi na ito mababawi.</span>
            </div>

            <div class="mt-7 flex flex-col-reverse sm:flex-row gap-3">
                <button type="button" id="cfmCancel"
                        class="flex-1 px-5 py-3 rounded-2xl bg-white border border-slate-300 text-slate-600 font-bold text-sm uppercase tracking-wider hover:bg-slate-50 transition">
                    Cancel
                </button>
                <button type="button" id="cfmOk"
                        class="flex-1 px-5 py-3 rounded-2xl bg-rose-600 hover:bg-rose-700 text-white font-black text-sm uppercase tracking-wider shadow-lg shadow-rose-600/25 transition flex items-center justify-center gap-2">
                    <i id="cfmOkIcon" class="fas fa-trash-alt"></i> <span id="cfmOkText">Delete</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var modal = document.getElementById('cfmModal');
    if (!modal) return;

    var el = {
        title: document.getElementById('cfmTitle'),
        msg: document.getElementById('cfmMessage'),
        ok: document.getElementById('cfmOk'),
        okText: document.getElementById('cfmOkText'),
        okIcon: document.getElementById('cfmOkIcon'),
        cancel: document.getElementById('cfmCancel'),
        bar: document.getElementById('cfmBar'),
        iconWrap: document.getElementById('cfmIconWrap'),
        icon: document.getElementById('cfmIcon'),
        warn: document.getElementById('cfmWarn')
    };
    var pendingForm = null;

    var THEMES = {
        danger: {
            bar: 'h-1.5 bg-gradient-to-r from-rose-500 to-orange-400',
            iconWrap: 'cfm-shake mx-auto h-16 w-16 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center text-2xl mb-5',
            ok: 'flex-1 px-5 py-3 rounded-2xl bg-rose-600 hover:bg-rose-700 text-white font-black text-sm uppercase tracking-wider shadow-lg shadow-rose-600/25 transition flex items-center justify-center gap-2',
            icon: 'fas fa-trash-alt', showWarn: true
        },
        warning: {
            bar: 'h-1.5 bg-gradient-to-r from-amber-400 to-orange-400',
            iconWrap: 'cfm-shake mx-auto h-16 w-16 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-2xl mb-5',
            ok: 'flex-1 px-5 py-3 rounded-2xl bg-amber-500 hover:bg-amber-600 text-white font-black text-sm uppercase tracking-wider shadow-lg shadow-amber-500/25 transition flex items-center justify-center gap-2',
            icon: 'fas fa-triangle-exclamation', showWarn: false
        },
        success: {
            bar: 'h-1.5 bg-gradient-to-r from-emerald-500 to-teal-500',
            iconWrap: 'mx-auto h-16 w-16 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-2xl mb-5',
            ok: 'flex-1 px-5 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-sm uppercase tracking-wider shadow-lg shadow-emerald-600/25 transition flex items-center justify-center gap-2',
            icon: 'fas fa-circle-check', showWarn: false
        }
    };

    function open(form) {
        var d = form.dataset;
        var t = THEMES[d.confirmType] || THEMES.danger;

        el.title.textContent = d.confirmTitle || 'Sigurado ka ba?';
        el.msg.textContent = d.confirm || 'Magpatuloy sa aksyon na ito?';
        el.okText.textContent = d.confirmLabel || 'Delete';

        el.bar.className = t.bar;
        el.iconWrap.className = t.iconWrap;
        el.icon.className = d.confirmIcon ? d.confirmIcon : t.icon;
        el.okIcon.className = d.confirmIcon ? d.confirmIcon : t.icon;
        el.ok.className = t.ok;
        el.warn.style.display = t.showWarn && d.confirmWarn !== 'false' ? '' : 'none';

        pendingForm = form;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        el.cancel.focus();
    }

    function close() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
        pendingForm = null;
    }

    // Intercept any <form data-confirm="..."> submit
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form || !form.hasAttribute || !form.hasAttribute('data-confirm')) return;
        if (form.dataset.confirmed === '1') return;
        e.preventDefault();
        open(form);
    }, true);

    el.ok.addEventListener('click', function () {
        if (!pendingForm) return;
        var f = pendingForm;
        f.dataset.confirmed = '1';
        el.ok.disabled = true;
        el.okIcon.className = 'fas fa-spinner fa-spin';
        f.submit();
    });

    el.cancel.addEventListener('click', close);
    document.getElementById('cfmBackdrop').addEventListener('click', close);
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && pendingForm) close(); });
})();
</script>