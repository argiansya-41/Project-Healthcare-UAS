@extends('layouts.app')

@section('header-title', 'Tanya AI Kesehatan')

@section('content')
    <div class="card" style="display: flex; flex-direction: column; height: calc(100vh - 160px); min-height: 500px; padding: 0; overflow: hidden; border: 1px solid var(--card-border); background-color: var(--card-bg);">
        <!-- Chat Header -->
        <div style="padding: 20px 24px; border-bottom: 1px solid var(--card-border); display: flex; align-items: center; justify-content: space-between; background: linear-gradient(180deg, rgba(255, 255, 255, 0.9) 0%, rgba(248, 250, 252, 0.5) 100%);">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="background: linear-gradient(135deg, rgba(15, 118, 110, 0.15) 0%, rgba(8, 145, 178, 0.2) 100%); color: var(--accent-color); width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; box-shadow: 0 4px 10px rgba(15, 118, 110, 0.15);">
                    <i class="ri-robot-2-fill"></i>
                </div>
                <div>
                    <h3 style="font-size: 16px; font-weight: 700; margin: 0; color: var(--text-primary);">Asisten AI Kesehatan</h3>
                    <div style="display: flex; align-items: center; gap: 8px; margin-top: 2px;">
                        <span style="font-size: 12px; color: var(--success); display: flex; align-items: center; gap: 5px; font-weight: 500;">
                            <span style="display: inline-block; width: 8px; height: 8px; background-color: var(--success); border-radius: 50%; box-shadow: 0 0 8px var(--success); transform: translate3d(0,0,0);"></span> Online
                        </span>
                        @if($hasApiKey)
                            <span class="badge badge-success" style="font-size: 10px; padding: 2px 8px; border-radius: 8px; margin-bottom: 0;">Mode: Cerdas (Gemini AI)</span>
                        @else
                            <span class="badge badge-warning" style="font-size: 10px; padding: 2px 8px; border-radius: 8px; color: #b45309; background-color: #fffbeb; border: 1px solid #fde68a; margin-bottom: 0;">Mode: Terbatas (Offline/Lokal)</span>
                        @endif
                    </div>
                </div>
            </div>
            <div>
                <button type="button" id="clear-chat-btn" class="btn btn-secondary btn-sm">
                    <i class="ri-delete-bin-7-line"></i> Bersihkan Percakapan
                </button>
            </div>
        </div>

        <!-- Chat Messages Container -->
        <div id="chat-body" style="flex: 1; overflow-y: auto; padding: 24px; display: flex; flex-direction: column; gap: 20px; background-color: rgba(248, 250, 252, 0.3);">
            <!-- AI Welcome Bubble -->
            <div id="welcome-bubble" style="display: flex; gap: 12px; max-width: 80%; align-self: flex-start;">
                <div style="width: 32px; height: 32px; border-radius: 50%; background-color: var(--accent-color); color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; box-shadow: 0 2px 6px rgba(15, 118, 110, 0.2);">
                    <i class="ri-robot-2-line"></i>
                </div>
                <div style="background-color: #ffffff; border: 1px solid var(--card-border); border-radius: 0 16px 16px 16px; padding: 14px 18px; color: var(--text-primary); font-size: 14px; line-height: 1.6; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
                    Halo <strong>{{ auth()->user()->name }}</strong>! Saya adalah Asisten AI Kesehatan Anda.<br><br>
                    
                    @if(!$hasApiKey)
                        <div style="margin-bottom: 12px; padding: 10px 12px; background-color: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; color: #b45309; font-size: 12.5px; line-height: 1.4;">
                            <i class="ri-information-line"></i> <strong>Informasi Sistem (Mode Terbatas):</strong><br>
                            Saat ini AI berjalan dalam <strong>Mode Offline/Lokal</strong>. Pertanyaan hanya dapat dijawab secara rinci jika mencakup topik DBD, TBC, Diare, Polio, Campak, dan dampak Alkohol/Rokok. Pertanyaan kesehatan lainnya akan dibantu dengan edukasi kesehatan umum.
                        </div>
                    @endif

                    Tanyakan kepada saya seputar informasi medis dan kesehatan seperti:
                    <ul style="margin: 8px 0 0 18px; padding: 0;">
                        <li>Gejala dan deskripsi penyakit menular (seperti DBD, TBC, Diare, dll.)</li>
                        <li>Langkah pencegahan penularan penyakit</li>
                        <li>Edukasi kesehatan umum dan anjuran penanganan pertama</li>
                        <li>Informasi vaksinasi, imunisasi anak, dan efek sampingnya</li>
                    </ul>
                </div>
            </div>
            
            <!-- Typing Indicator Bubble -->
            <div id="typing-bubble" style="display: none; gap: 12px; max-width: 80%; align-self: flex-start;">
                <div style="width: 32px; height: 32px; border-radius: 50%; background-color: var(--accent-color); color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
                    <i class="ri-robot-2-line"></i>
                </div>
                <div style="background-color: #ffffff; border: 1px solid var(--card-border); border-radius: 0 16px 16px 16px; padding: 14px 18px; display: flex; align-items: center; justify-content: center; gap: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
                    <div class="typing-dot" style="width: 8px; height: 8px; background-color: var(--accent-color); border-radius: 50%; opacity: 0.6; animation: typing-animation 1.4s infinite;"></div>
                    <div class="typing-dot" style="width: 8px; height: 8px; background-color: var(--accent-color); border-radius: 50%; opacity: 0.6; animation: typing-animation 1.4s infinite 0.2s;"></div>
                    <div class="typing-dot" style="width: 8px; height: 8px; background-color: var(--accent-color); border-radius: 50%; opacity: 0.6; animation: typing-animation 1.4s infinite 0.4s;"></div>
                </div>
            </div>
        </div>

        <!-- Suggestion Prompt Chips -->
        <div id="suggestions-container" style="padding: 12px 24px; display: flex; gap: 10px; flex-wrap: wrap; border-top: 1px solid var(--card-border); background-color: rgba(248, 250, 252, 0.4);">
            <button type="button" class="suggestion-chip" data-prompt="Apa saja gejala utama penyakit DBD?">Gejala DBD</button>
            <button type="button" class="suggestion-chip" data-prompt="Bagaimana pencegahan dan penanganan diare pada balita?">Pencegahan Diare</button>
            <button type="button" class="suggestion-chip" data-prompt="Jelaskan tentang penyakit TBC dan langkah pengobatannya.">Langkah Pengobatan TBC</button>
            <button type="button" class="suggestion-chip" data-prompt="Apa efek samping imunisasi (KIPI) dan cara mengatasinya?">Efek Samping Imunisasi</button>
        </div>

        <!-- Chat Input Form -->
        <div style="padding: 16px 24px 20px; border-top: 1px solid var(--card-border); display: flex; flex-direction: column; gap: 8px; background-color: #ffffff;">
            <form id="chat-form" style="display: flex; gap: 12px;">
                @csrf
                <input type="text" id="chat-input" placeholder="Tanyakan tentang gejala, pencegahan penyakit, atau imunisasi di sini..." autocomplete="off" style="flex: 1; border: 1px solid var(--card-border); border-radius: 12px; padding: 14px 18px; font-size: 14px; color: var(--text-primary); transition: all 0.2s; outline: none;" onfocus="this.style.borderColor='var(--accent-color)'; this.style.boxShadow='0 0 0 3px rgba(15, 118, 110, 0.08)';" onblur="this.style.borderColor='var(--card-border)'; this.style.boxShadow='none';">
                <button type="submit" id="chat-submit" class="btn btn-primary" style="border-radius: 12px; padding: 0 24px; display: flex; align-items: center; justify-content: center; gap: 8px; font-weight: 600; font-size: 14px; background-color: var(--accent-color); transition: all 0.2s;" onmouseover="this.style.backgroundColor='var(--accent-hover)'" onmouseout="this.style.backgroundColor='var(--accent-color)'">
                    <span>Kirim</span> <i class="ri-send-plane-2-fill"></i>
                </button>
            </form>
            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 11px; color: var(--text-secondary); padding: 0 4px;">
                <span>*AI menjawab secara otomatis berdasarkan keilmuan medis dasar.</span>
                <span style="font-weight: 600; color: var(--accent-color); display: flex; align-items: center; gap: 4px;"><i class="ri-shield-check-line"></i> Ruang Lingkup Kesehatan Terverifikasi</span>
            </div>
        </div>
    </div>

    <style>
        #clear-chat-btn {
            background: transparent;
            border: 1px solid var(--card-border);
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 12px;
            transition: all 0.2s;
            cursor: pointer;
        }
        #clear-chat-btn:hover, #clear-chat-btn.confirming {
            border-color: var(--danger) !important;
            color: var(--danger) !important;
            background-color: rgba(239, 68, 68, 0.05) !important;
        }
        .suggestion-chip {
            background-color: #ffffff;
            border: 1px solid var(--card-border);
            color: var(--text-primary);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.01);
        }
        .suggestion-chip:hover {
            border-color: var(--accent-color);
            background-color: rgba(15, 118, 110, 0.03);
            color: var(--accent-color);
            transform: translateY(-1px);
        }
        .ai-warning-alert {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            color: #b45309;
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            padding: 12px 14px;
            border-radius: 10px;
            margin-top: 4px;
        }
        .ai-warning-alert i {
            font-size: 18px;
            color: #d97706;
            margin-top: 1px;
            flex-shrink: 0;
        }
        @keyframes typing-animation {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-6px);
            }
        }
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.2);
                opacity: 0.7;
            }
        }
        
        /* Structured medical reply styles */
        #chat-body h3 {
            font-size: 15px;
            font-weight: 700;
            margin: 0 0 10px 0;
            color: var(--accent-color);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        #chat-body p {
            margin: 0 0 8px 0;
        }
        #chat-body ul {
            margin: 0 0 12px 20px;
            padding: 0;
        }
        #chat-body li {
            margin-bottom: 4px;
        }
        #chat-body em {
            font-style: italic;
        }

        /* Focus intents */
        .intent-focused-green {
            border: 1px solid #bbf7d0 !important;
            background-color: #f0fdf4 !important;
            border-radius: 10px !important;
            padding: 12px 14px !important;
            box-shadow: 0 2px 6px rgba(34, 197, 94, 0.05);
        }
        .intent-focused-amber {
            border: 1px solid #fde68a !important;
            background-color: #fffbeb !important;
            border-radius: 10px !important;
            padding: 12px 14px !important;
            box-shadow: 0 2px 6px rgba(245, 158, 11, 0.05);
        }
        .intent-focused-blue {
            border: 1px solid #bae6fd !important;
            background-color: #f0f9ff !important;
            border-radius: 10px !important;
            padding: 12px 14px !important;
            box-shadow: 0 2px 6px rgba(56, 189, 248, 0.05);
        }
        .ai-thinking-container {
            transition: all 0.25s ease-in-out;
        }
        .ai-thinking-header:hover {
            background-color: rgba(241, 245, 249, 0.8) !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatForm = document.getElementById('chat-form');
            const chatInput = document.getElementById('chat-input');
            const chatSubmit = document.getElementById('chat-submit');
            const chatBody = document.getElementById('chat-body');
            const typingBubble = document.getElementById('typing-bubble');
            const clearChatBtn = document.getElementById('clear-chat-btn');
            
            // Auto scroll to bottom
            function scrollToBottom() {
                chatBody.scrollTop = chatBody.scrollHeight;
            }

            // Create Accordion HTML for Thinking Steps
            function createThinkingAccordion(thinkingSteps) {
                if (!thinkingSteps || thinkingSteps.length === 0) return '';
                
                let stepsList = '';
                thinkingSteps.forEach(step => {
                    stepsList += `<li style="margin-bottom: 4px;">${step}</li>`;
                });

                const randomId = 'thinking-' + Math.random().toString(36).substr(2, 9);

                return `
                    <div class="ai-thinking-container" id="${randomId}" style="margin-bottom: 12px; border: 1px solid var(--card-border); border-radius: 10px; background-color: rgba(248, 250, 252, 0.5); overflow: hidden; width: 100%; box-sizing: border-box;">
                        <div class="ai-thinking-header" style="padding: 8px 12px; display: flex; align-items: center; justify-content: space-between; cursor: pointer; user-select: none; background-color: rgba(241, 245, 249, 0.5); transition: background-color 0.2s;" onclick="
                            const body = document.getElementById('body-${randomId}');
                            const chevron = document.getElementById('chev-${randomId}');
                            if (body.style.display === 'none') {
                                body.style.display = 'block';
                                chevron.style.transform = 'rotate(0deg)';
                            } else {
                                body.style.display = 'none';
                                chevron.style.transform = 'rotate(-90deg)';
                            }
                        ">
                            <div style="display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; color: var(--text-secondary);">
                                <i class="ri-mind-map" style="color: var(--accent-color); font-size: 14px;"></i>
                                <span>Proses Berpikir AI</span>
                            </div>
                            <i class="ri-arrow-down-s-line" id="chev-${randomId}" style="font-size: 14px; color: var(--text-secondary); transition: transform 0.2s; transform: rotate(-90deg);"></i>
                        </div>
                        <div id="body-${randomId}" class="ai-thinking-body" style="padding: 10px 14px; display: none; border-top: 1px solid var(--card-border); font-size: 11.5px; color: var(--text-secondary); line-height: 1.5; background-color: #ffffff;">
                            <ul style="margin: 0; padding-left: 16px; list-style-type: disc;">
                                ${stepsList}
                            </ul>
                        </div>
                    </div>
                `;
            }

            // Append a message to the chat container
            function appendMessage(sender, htmlContent, thinkingSteps = null, isDirectAiHtml = false) {
                const messageWrapper = document.createElement('div');
                messageWrapper.style.display = 'flex';
                messageWrapper.style.gap = '12px';
                messageWrapper.style.maxWidth = '80%';
                
                if (sender === 'user') {
                    messageWrapper.style.alignSelf = 'flex-end';
                    messageWrapper.innerHTML = `
                        <div style="background-color: var(--accent-color); color: #ffffff; border-radius: 16px 0 16px 16px; padding: 12px 16px; font-size: 14px; line-height: 1.6; box-shadow: 0 2px 8px rgba(15, 118, 110, 0.15);">
                            ${htmlContent}
                        </div>
                        <div style="width: 32px; height: 32px; border-radius: 50%; background-color: var(--bg-sidebar); color: var(--text-sidebar-active); display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; flex-shrink: 0;">
                            ${"{{ substr(auth()->user()->name, 0, 1) }}"}
                        </div>
                    `;
                } else {
                    messageWrapper.style.alignSelf = 'flex-start';
                    
                    if (isDirectAiHtml) {
                        messageWrapper.innerHTML = `
                            <div style="width: 32px; height: 32px; border-radius: 50%; background-color: var(--accent-color); color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; box-shadow: 0 2px 6px rgba(15, 118, 110, 0.25);">
                                <i class="ri-robot-2-line"></i>
                            </div>
                            <div style="background-color: #ffffff; border: 1px solid var(--card-border); border-radius: 0 16px 16px 16px; padding: 14px 18px; color: var(--text-primary); font-size: 14px; line-height: 1.6; box-shadow: 0 2px 8px rgba(0,0,0,0.02); display: flex; flex-direction: column; width: 100%; box-sizing: border-box;">
                                ${htmlContent}
                            </div>
                        `;
                    } else {
                        let thinkingHtml = '';
                        if (thinkingSteps && thinkingSteps.length > 0) {
                            thinkingHtml = createThinkingAccordion(thinkingSteps);
                        }
                        
                        messageWrapper.innerHTML = `
                            <div style="width: 32px; height: 32px; border-radius: 50%; background-color: var(--accent-color); color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; box-shadow: 0 2px 6px rgba(15, 118, 110, 0.25);">
                                <i class="ri-robot-2-line"></i>
                            </div>
                            <div style="background-color: #ffffff; border: 1px solid var(--card-border); border-radius: 0 16px 16px 16px; padding: 14px 18px; color: var(--text-primary); font-size: 14px; line-height: 1.6; box-shadow: 0 2px 8px rgba(0,0,0,0.02); display: flex; flex-direction: column; width: 100%; box-sizing: border-box;">
                                ${thinkingHtml}
                                <div class="reply-content">${htmlContent}</div>
                            </div>
                        `;
                    }
                }
                
                // Insert before the typing indicator bubble
                chatBody.insertBefore(messageWrapper, typingBubble);
                scrollToBottom();
                
                // Save messages to localstorage for persistence
                saveChatToLocal();
            }

            // Save chat log to localStorage
            function saveChatToLocal() {
                const logs = [];
                const children = Array.from(chatBody.children);
                children.forEach(child => {
                    if (child.id === 'welcome-bubble' || child.id === 'typing-bubble') return;
                    
                    const isUser = child.style.alignSelf === 'flex-end';
                    if (isUser) {
                        const contentDiv = child.querySelector('div[style*="background-color"]');
                        if (contentDiv) {
                            logs.push({
                                sender: 'user',
                                htmlContent: contentDiv.innerHTML.trim()
                            });
                        }
                    } else {
                        const contentDiv = child.querySelector('div[style*="border-radius: 0 16px 16px 16px"]');
                        if (contentDiv) {
                            logs.push({
                                sender: 'ai',
                                htmlContent: contentDiv.innerHTML.trim()
                            });
                        }
                    }
                });
                localStorage.setItem('healthcare_ai_chat_log_v2', JSON.stringify(logs));
            }

            // Load chat log from localStorage
            function loadChatFromLocal() {
                const stored = localStorage.getItem('healthcare_ai_chat_log_v2');
                if (stored) {
                    const logs = JSON.parse(stored);
                    if (logs.length > 0) {
                        const children = Array.from(chatBody.children);
                        children.forEach(child => {
                            if (child.id !== 'welcome-bubble' && child.id !== 'typing-bubble') {
                                child.remove();
                            }
                        });
                        
                        const originalSave = saveChatToLocal;
                        saveChatToLocal = function() {};
                        
                        logs.forEach(log => {
                            if (log.sender === 'user') {
                                appendMessage('user', log.htmlContent);
                            } else {
                                appendMessage('ai', log.htmlContent, null, true);
                            }
                        });
                        
                        saveChatToLocal = originalSave;
                    }
                }
            }

            // Send message to the backend
            async function sendMessage(text) {
                if (!text.trim()) return;

                // Disable submit button and input
                chatInput.disabled = true;
                chatSubmit.disabled = true;
                
                // Show typing indicator
                typingBubble.style.display = 'flex';
                scrollToBottom();

                try {
                    const response = await fetch("{{ route('ai-chat.send') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        },
                        body: JSON.stringify({ message: text })
                    });
                    
                    const data = await response.json();
                    
                    // Hide typing indicator
                    typingBubble.style.display = 'none';

                    if (data.reply) {
                        appendMessage('ai', data.reply, data.thinking);
                    } else {
                        appendMessage('ai', 'Maaf, terjadi kendala koneksi dengan server Asisten AI.');
                    }
                } catch (error) {
                    console.error('AI Chat Error:', error);
                    typingBubble.style.display = 'none';
                    appendMessage('ai', 'Maaf, terjadi kesalahan saat menghubungi server AI.');
                } finally {
                    chatInput.disabled = false;
                    chatSubmit.disabled = false;
                    chatInput.value = '';
                    chatInput.focus();
                }
            }

            // Form Submit Listener
            chatForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const text = chatInput.value.trim();
                if (!text) return;

                appendMessage('user', text);
                sendMessage(text);
            });

            // Suggestion Chips Click
            document.querySelectorAll('.suggestion-chip').forEach(chip => {
                chip.addEventListener('click', function() {
                    const prompt = this.getAttribute('data-prompt');
                    appendMessage('user', prompt);
                    sendMessage(prompt);
                });
            });

            // Clear Chat Action with 2-step confirmation
            let clearConfirmTimeout = null;
            let isConfirmingClear = false;

            clearChatBtn.addEventListener('click', function() {
                if (!isConfirmingClear) {
                    isConfirmingClear = true;
                    clearChatBtn.classList.add('confirming');
                    clearChatBtn.innerHTML = '<i class="ri-error-warning-line"></i> Yakin Bersihkan?';
                    clearConfirmTimeout = setTimeout(resetClearButton, 3000);
                } else {
                    clearTimeout(clearConfirmTimeout);
                    resetClearButton();
                    
                    localStorage.removeItem('healthcare_ai_chat_log_v2');
                    
                    // Remove all message elements except the welcome bubble and the typing bubble
                    const children = Array.from(chatBody.children);
                    children.forEach(child => {
                        if (child.id !== 'welcome-bubble' && child.id !== 'typing-bubble') {
                            child.remove();
                        }
                    });
                    
                    scrollToBottom();
                }
            });

            function resetClearButton() {
                isConfirmingClear = false;
                clearChatBtn.classList.remove('confirming');
                clearChatBtn.innerHTML = '<i class="ri-delete-bin-7-line"></i> Bersihkan Percakapan';
            }

            // Initialize: load localstorage chats
            loadChatFromLocal();
            scrollToBottom();

            // Handle URL Query Parameter (if navigated from dashboard popular question links)
            const urlParams = new URLSearchParams(window.location.search);
            const queryParam = urlParams.get('q');
            if (queryParam) {
                // Remove search parameter from browser history to prevent repeating query on refresh
                const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                window.history.pushState({ path: newUrl }, '', newUrl);

                // Send the query immediately
                appendMessage('user', queryParam);
                sendMessage(queryParam);
            }
        });
    </script>
@endsection
