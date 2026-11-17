@php
    $chatUrl = \Illuminate\Support\Facades\Route::has('public.chatbot.ask') ? route('public.chatbot.ask') : null;
    $chips = ['clearance' => '📄 Clearance', 'hours' => '🕒 Hours', 'indigency' => '🤝 Indigency', 'hotlines' => '🚨 Hotlines', 'residency' => '🏠 Residency'];
@endphp

@if ($chatUrl)
<div id="chatWidget" class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-[9999] font-sans">
  <button id="chatToggle" onclick="toggleChat()" aria-label="Open chat" class="group relative w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16 rounded-full bg-gradient-to-br from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white shadow-2xl transition-all duration-300 transform hover:scale-110 flex items-center justify-center">
    <i class="fas fa-robot text-lg sm:text-xl md:text-2xl" id="chatIcon"></i>
    <span class="absolute -top-1 -right-1 h-3 w-3 sm:h-4 sm:w-4 bg-amber-400 rounded-full border-2 border-white animate-pulse"></span>
  </button>
  <div id="chatWindow" class="hidden absolute bottom-16 sm:bottom-20 right-0 w-[calc(100vw-2rem)] sm:w-[420px] max-w-[420px] bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
    <div class="bg-gradient-to-br from-emerald-700 via-emerald-800 to-teal-900 px-4 sm:px-5 py-3 sm:py-4 flex items-center justify-between">
      <div class="flex items-center gap-2 sm:gap-3">
        <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-full bg-white/10 border border-white/20 flex items-center justify-center text-white"><i class="fas fa-robot text-xs sm:text-sm"></i></div>
        <div>
          <div class="flex items-center gap-1.5"><span class="h-1.5 w-1.5 rounded-full bg-emerald-300 animate-pulse"></span><p class="text-xs sm:text-sm font-black text-white">BIMS AI Assistant</p></div>
          <p class="text-[9px] sm:text-[10px] text-emerald-100/80 uppercase tracking-widest font-bold">Online</p>
        </div>
      </div>
      <button onclick="toggleChat()" aria-label="Close chat" class="h-7 w-7 sm:h-8 sm:w-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all"><i class="fas fa-times text-[10px] sm:text-xs"></i></button>
    </div>
    <div id="chatMessages" class="h-[320px] sm:h-[380px] overflow-y-auto p-3 sm:p-4 space-y-2.5 sm:space-y-3 bg-slate-50/50">
      <div class="flex gap-2">
        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white shadow-sm flex-shrink-0"><i class="fas fa-robot text-[10px] sm:text-xs"></i></div>
        <div class="flex-1"><div class="bg-white rounded-2xl rounded-tl-md p-2.5 sm:p-3 shadow-sm border border-slate-100 inline-block max-w-[85%]"><p class="text-[11px] sm:text-xs text-slate-700 leading-relaxed">Hi! 👋 Ako ang inyong <span class="font-bold text-emerald-600">BIMS AI Assistant</span>. Pwede mo akong tanungin tungkol sa barangay services. Huwag magpadala ng personal na impormasyon dito.</p></div></div>
      </div>
    </div>
    <div class="px-3 sm:px-4 py-1.5 sm:py-2 flex gap-1.5 sm:gap-2 overflow-x-auto border-t border-slate-100 bg-white" style="scrollbar-width:none">
      @foreach ($chips as $key => $label)
        <button onclick="quickAnswer('{{ $key }}')" class="quick-chip text-[9px] sm:text-[10px] bg-slate-100 hover:bg-emerald-100 text-slate-700 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full whitespace-nowrap font-bold">{{ $label }}</button>
      @endforeach
    </div>
    <div class="border-t border-slate-200 p-2.5 sm:p-3 bg-white">
      <div class="flex gap-2 items-end">
        <textarea id="userInput" rows="1" placeholder="Type your question..." class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition resize-none" style="max-height:80px"></textarea>
        <button id="sendBtn" aria-label="Send" class="bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white p-2 sm:p-2.5 rounded-xl transition-all transform hover:scale-105 active:scale-95 shadow-md"><i class="fas fa-paper-plane text-[10px] sm:text-xs"></i></button>
      </div>
    </div>
  </div>
</div>

<script>
const chatWindow = document.getElementById('chatWindow'),
      chatIcon = document.getElementById('chatIcon'),
      chatMessages = document.getElementById('chatMessages'),
      userInput = document.getElementById('userInput'),
      sendBtn = document.getElementById('sendBtn');
let isChatOpen = false, isSending = false;
const chatHistory = [];

const quickQuestions = {
  clearance: 'Paano kumuha ng Barangay Clearance?',
  hours: 'Ano ang office hours ng barangay hall?',
  indigency: 'Ano ang requirements para sa Certificate of Indigency?',
  hotlines: 'Ano ang mga emergency hotline ng barangay?',
  residency: 'Ano ang requirements para sa Certificate of Residency?'
};

function toggleChat() {
  isChatOpen = !isChatOpen;
  if (isChatOpen) {
    chatWindow.classList.remove('hidden'); chatWindow.classList.add('animate-fade-in-up');
    chatIcon.className = 'fas fa-times text-lg sm:text-2xl';
    setTimeout(() => userInput.focus(), 100);
  } else {
    chatWindow.classList.add('hidden');
    chatIcon.className = 'fas fa-robot text-lg sm:text-2xl';
  }
}
function autoResize() { userInput.style.height = 'auto'; userInput.style.height = Math.min(userInput.scrollHeight, 80) + 'px'; }
userInput.addEventListener('input', autoResize);
function scrollToBottom() { chatMessages.scrollTo({ top: chatMessages.scrollHeight, behavior: 'smooth' }); }
function escapeHtml(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
function formatMessage(t) {
  return escapeHtml(t)
    .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
    .replace(/^\s*[\*\-]\s+/gm, '• ')
    .replace(/\n/g, '<br>');
}

function appendMessage(sender, text) {
  const isUser = sender === 'user', el = document.createElement('div');
  el.className = 'flex gap-2 ' + (isUser ? 'flex-row-reverse' : '');
  const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  el.innerHTML = isUser
    ? `<div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white shadow-sm flex-shrink-0"><i class="fas fa-user text-[10px] sm:text-xs"></i></div>
       <div class="flex-1 flex justify-end"><div class="bg-gradient-to-br from-emerald-600 to-teal-600 text-white rounded-2xl rounded-tr-md p-2.5 sm:p-3 shadow-sm max-w-[85%]"><p class="text-[11px] sm:text-xs leading-relaxed">${escapeHtml(text).replace(/\n/g, '<br>')}</p></div></div><div class="w-7 sm:w-8"></div>`
    : `<div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white shadow-sm flex-shrink-0"><i class="fas fa-robot text-[10px] sm:text-xs"></i></div>
       <div class="flex-1"><div class="bg-white rounded-2xl rounded-tl-md p-2.5 sm:p-3 shadow-sm border border-slate-100 inline-block max-w-[90%]"><p class="text-[11px] sm:text-xs text-slate-700 leading-relaxed">${formatMessage(text)}</p></div><span class="text-[9px] sm:text-[10px] text-slate-400 mt-1 block">AI • ${time}</span></div>`;
  chatMessages.appendChild(el); scrollToBottom();
}
function showTyping() {
  const t = document.createElement('div'); t.id = 'typingIndicator'; t.className = 'flex gap-2';
  t.innerHTML = `<div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white shadow-sm flex-shrink-0"><i class="fas fa-robot text-[10px] sm:text-xs"></i></div>
    <div class="flex-1"><div class="bg-white rounded-2xl rounded-tl-md p-2.5 sm:p-3 shadow-sm border border-slate-100 inline-block"><div class="flex gap-1.5">
    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-bounce"></span><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-bounce" style="animation-delay:.15s"></span><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-bounce" style="animation-delay:.3s"></span></div></div></div>`;
  chatMessages.appendChild(t); scrollToBottom();
}
function removeTyping() { const t = document.getElementById('typingIndicator'); if (t) t.remove(); }

async function ask(message) {
  if (isSending || !message) return;
  isSending = true; sendBtn.disabled = true;
  appendMessage('user', message); showTyping();

  try {
    const res = await fetch('{{ $chatUrl }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ question: message, history: chatHistory.slice(-6) })
    });

    let data = null;
    try { data = await res.json(); } catch (e) { /* HTML o walang laman */ }
    removeTyping();

    let answer;
    if (res.ok && data && data.answer) answer = data.answer;
    else if (res.status === 419) answer = 'Nag-expire na ang session. Paki-refresh ang page at subukan ulit.';
    else if (res.status === 429) answer = (data && data.answer) || 'Maraming sabay-sabay na tanong. Subukan ulit pagkalipas ng ilang segundo.';
    else answer = (data && data.answer) || 'May problema sa AI service ngayon. Pakisubukan ulit mamaya.';

    appendMessage('bot', answer);
    if (res.ok && data && data.answer) {
      chatHistory.push({ role: 'user', text: message }, { role: 'bot', text: answer });
    }
  } catch (err) {
    console.error(err); removeTyping();
    appendMessage('bot', 'Walang koneksyon sa internet. Pakisuri at subukan ulit.');
  } finally {
    isSending = false; sendBtn.disabled = false; userInput.focus();
  }
}

function sendMessage() {
  const message = userInput.value.trim();
  if (!message) return;
  userInput.value = ''; autoResize();
  ask(message);
}
function quickAnswer(key) { const q = quickQuestions[key]; if (q) ask(q); }

userInput.addEventListener('keydown', e => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); } });
sendBtn.addEventListener('click', sendMessage);
</script>
@endif