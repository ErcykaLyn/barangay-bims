@extends('layouts.app')

@section('content')
<div class="relative min-h-[calc(100vh-4rem)] bg-gradient-to-br from-slate-100 via-emerald-50 to-teal-50 py-8 px-4 sm:px-6">
    <div class="max-w-5xl mx-auto">
        <!-- Card container with glass effect -->
        <div class="backdrop-blur-md bg-white/70 dark:bg-slate-900/70 rounded-2xl shadow-2xl border border-white/20 overflow-hidden transition-all duration-300">
            <!-- Header with gradient and glass -->
            <div class="bg-gradient-to-r from-emerald-700 via-emerald-600 to-teal-600 px-6 py-5 relative overflow-hidden">
                <div class="absolute inset-0 bg-black/10 backdrop-blur-sm"></div>
                <div class="relative flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur flex items-center justify-center shadow-lg">
                        <i class="fas fa-robot text-white text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white tracking-tight">BIMS AI Assistant</h2>
                        <p class="text-emerald-100 text-sm">Your 24/7 barangay companion — ask anything</p>
                    </div>
                </div>
            </div>

            <!-- Chat messages container with custom scrollbar -->
            <div id="chatMessages" class="h-[480px] overflow-y-auto p-6 space-y-5 bg-gradient-to-b from-slate-50/80 to-white/80 dark:from-slate-800/50 dark:to-slate-900/50 transition-colors" style="scroll-behavior: smooth;">
                <!-- Initial bot message -->
                <div class="flex gap-3 animate-fade-in-up">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white shadow-md flex-shrink-0">
                        <i class="fas fa-robot text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <div class="bg-white dark:bg-slate-800 rounded-2xl rounded-tl-md p-4 shadow-md border border-slate-100 dark:border-slate-700 inline-block max-w-[85%] backdrop-blur-sm">
                            <p class="text-slate-700 dark:text-slate-200 text-sm leading-relaxed">Hello! I'm your barangay AI assistant, powered by Google Gemini. Ask me about <span class="font-semibold text-emerald-600 dark:text-emerald-400">clearances, schedules, requirements, permits, or any barangay service</span>. How can I help you today?</p>
                        </div>
                        <span class="text-[11px] text-slate-400 dark:text-slate-500 mt-1 block">AI Assistant • just now</span>
                    </div>
                </div>
            </div>

            <!-- Quick suggestions chip row -->
            <div class="px-6 pb-3 pt-2 flex gap-2 overflow-x-auto scrollbar-none">
                <button onclick="setQuestion('How to get barangay clearance?')" class="quick-chip text-xs bg-slate-100 hover:bg-emerald-100 dark:bg-slate-800 dark:hover:bg-emerald-900/40 text-slate-700 dark:text-slate-300 px-3 py-1.5 rounded-full transition-all whitespace-nowrap shadow-sm">📄 Barangay clearance</button>
                <button onclick="setQuestion('What are the requirements for a business permit?')" class="quick-chip text-xs bg-slate-100 hover:bg-emerald-100 dark:bg-slate-800 dark:hover:bg-emerald-900/40 text-slate-700 dark:text-slate-300 px-3 py-1.5 rounded-full transition-all whitespace-nowrap shadow-sm">🏪 Business permit</button>
                <button onclick="setQuestion('Office hours of the barangay hall?')" class="quick-chip text-xs bg-slate-100 hover:bg-emerald-100 dark:bg-slate-800 dark:hover:bg-emerald-900/40 text-slate-700 dark:text-slate-300 px-3 py-1.5 rounded-full transition-all whitespace-nowrap shadow-sm">🕒 Office hours</button>
                <button onclick="setQuestion('Senior citizen ID requirements')" class="quick-chip text-xs bg-slate-100 hover:bg-emerald-100 dark:bg-slate-800 dark:hover:bg-emerald-900/40 text-slate-700 dark:text-slate-300 px-3 py-1.5 rounded-full transition-all whitespace-nowrap shadow-sm">👴 Senior citizen ID</button>
            </div>

            <!-- Input area with modern styling -->
            <div class="border-t border-slate-200/50 dark:border-slate-700/50 p-5 bg-white/50 dark:bg-slate-900/50 backdrop-blur-sm">
                <div class="flex gap-3 items-end">
                    <div class="flex-1 relative">
                        <textarea id="userInput" rows="1" placeholder="Type your question here... (Press Enter to send, Shift+Enter for new line)" 
                               class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition resize-none overflow-y-auto leading-relaxed shadow-inner dark:text-slate-200"
                               style="max-height: 120px;"></textarea>
                    </div>
                    <button id="sendBtn" class="bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white p-3 rounded-xl transition-all transform hover:scale-105 active:scale-95 shadow-md">
                        <i class="fas fa-paper-plane text-sm"></i>
                    </button>
                </div>
                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-3 text-center flex items-center justify-center gap-2">
                    <i class="fas fa-microchip text-[10px]"></i> Powered by Gemini AI
                    <span class="w-1 h-1 rounded-full bg-slate-300 inline-block"></span>
                    <i class="fas fa-shield-alt text-[10px]"></i> Privacy assured
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom scrollbar for webkit */
    #chatMessages::-webkit-scrollbar {
        width: 5px;
    }
    #chatMessages::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    #chatMessages::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    #chatMessages::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.3s ease-out forwards;
    }
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .quick-chip {
        transition: all 0.2s;
        cursor: pointer;
    }
    .quick-chip:active {
        transform: scale(0.96);
    }
    /* Dark mode scrollbar */
    @media (prefers-color-scheme: dark) {
        #chatMessages::-webkit-scrollbar-track {
            background: #1e293b;
        }
        #chatMessages::-webkit-scrollbar-thumb {
            background: #475569;
        }
        #chatMessages::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }
    }
</style>

<script>
    const chatContainer = document.getElementById('chatMessages');
    const userInput = document.getElementById('userInput');
    const sendBtn = document.getElementById('sendBtn');

    // Auto-resize textarea as user types
    function autoResize() {
        userInput.style.height = 'auto';
        userInput.style.height = Math.min(userInput.scrollHeight, 120) + 'px';
    }
    userInput.addEventListener('input', autoResize);
    autoResize();

    // Scroll to bottom on load
    function scrollToBottom() {
        chatContainer.scrollTo({ top: chatContainer.scrollHeight, behavior: 'smooth' });
    }
    scrollToBottom();

    function appendMessage(sender, text, time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })) {
        const isUser = sender === 'user';
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex gap-3 ${isUser ? 'flex-row-reverse' : ''} animate-fade-in-up`;
        
        if (isUser) {
            messageDiv.innerHTML = `
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white shadow-md flex-shrink-0">
                    <i class="fas fa-user text-sm"></i>
                </div>
                <div class="flex-1 flex justify-end">
                    <div class="bg-gradient-to-br from-emerald-600 to-teal-600 text-white rounded-2xl rounded-tr-md p-3 shadow-md max-w-[85%]">
                        <p class="text-sm leading-relaxed">${escapeHtml(text)}</p>
                    </div>
                </div>
                <div class="w-9"></div>
            `;
        } else {
            messageDiv.innerHTML = `
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white shadow-md flex-shrink-0">
                    <i class="fas fa-robot text-sm"></i>
                </div>
                <div class="flex-1">
                    <div class="bg-white dark:bg-slate-800 rounded-2xl rounded-tl-md p-3 shadow-md border border-slate-100 dark:border-slate-700 inline-block max-w-[85%] backdrop-blur-sm">
                        <p class="text-sm text-slate-700 dark:text-slate-200 leading-relaxed">${escapeHtml(text)}</p>
                    </div>
                    <span class="text-[11px] text-slate-400 dark:text-slate-500 mt-1 block">AI Assistant • ${time}</span>
                </div>
            `;
        }
        chatContainer.appendChild(messageDiv);
        scrollToBottom();
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function showTyping() {
        const typingDiv = document.createElement('div');
        typingDiv.id = 'typingIndicator';
        typingDiv.className = 'flex gap-3 animate-fade-in-up';
        typingDiv.innerHTML = `
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white shadow-md flex-shrink-0">
                <i class="fas fa-robot text-sm"></i>
            </div>
            <div class="flex-1">
                <div class="bg-white dark:bg-slate-800 rounded-2xl rounded-tl-md p-3 shadow-md border border-slate-100 dark:border-slate-700 inline-block">
                    <div class="flex gap-1.5">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-bounce"></span>
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 0.15s"></span>
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 0.3s"></span>
                    </div>
                </div>
            </div>
        `;
        chatContainer.appendChild(typingDiv);
        scrollToBottom();
    }

    function removeTyping() {
        const typing = document.getElementById('typingIndicator');
        if (typing) typing.remove();
    }

    async function sendMessage() {
        const message = userInput.value.trim();
        if (!message) return;

        appendMessage('user', message);
        userInput.value = '';
        autoResize();
        showTyping();

        try {
            const response = await fetch('{{ route("resident.chatbot.send") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ question: message })
            });
            const data = await response.json();
            removeTyping();
            if (response.ok) {
                const reply = data.answer || data.reply || 'No response from AI.';
                appendMessage('bot', reply);
            } else {
                const errorMsg = data.answer || data.message || 'Sorry, I encountered an error. Please try again later.';
                appendMessage('bot', errorMsg);
            }
        } catch (error) {
            console.error(error);
            removeTyping();
            appendMessage('bot', 'Network error. Please check your connection.');
        }
    }

    // Event listeners
    function onEnter(event) {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            sendMessage();
        }
    }
    userInput.addEventListener('keydown', onEnter);
    sendBtn.addEventListener('click', sendMessage);

    // Quick question helper
    window.setQuestion = function(question) {
        userInput.value = question;
        autoResize();
        userInput.focus();
        sendMessage();
    };
</script>
@endsection