@if (! request()->routeIs('medisense.index'))
{{-- ─── MediSense AI Floating Action Button & Chat Widget ─── --}}
<div id="medisenseFabContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1085;">

    {{-- Floating Action Button --}}
    <button id="medisenseFabBtn"
            type="button"
            class="ms-fab-btn"
            title="MediSense AI: Clinical Assistant"
            aria-label="Toggle MediSense AI Assistant">
        <i class="bi bi-stars ms-fab-icon"></i>
        <span class="ms-fab-badge" aria-hidden="true">AI</span>
    </button>

    {{-- Chat Popup Widget --}}
    <div id="medisenseFabWidget" class="ms-widget d-none" role="dialog" aria-label="MediSense AI Assistant" aria-modal="true">

        {{-- Header --}}
        <div class="ms-widget-header">
            <div class="ms-widget-header-identity">
                <div class="ms-widget-avatar">
                    <i class="bi bi-stars"></i>
                </div>
                <div class="ms-widget-title-group">
                    <span class="ms-widget-title">MediSense AI</span>
                    <span class="ms-widget-subtitle">{{ auth()->user()?->roleName ?? 'Clinical' }} Decision Support</span>
                </div>
            </div>
            <div class="ms-widget-header-actions">
                <a href="{{ route('medisense.index') }}"
                   class="ms-header-icon-btn"
                   title="Open Full MediSense Workspace"
                   aria-label="Open full workspace">
                    <i class="bi bi-box-arrow-up-right"></i>
                </a>
                <button id="medisenseFabClose"
                        type="button"
                        class="ms-header-icon-btn ms-close-btn"
                        aria-label="Close MediSense AI"
                        onclick="closeFabWidget()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>

        {{-- Messages Area --}}
        <div id="fabChatMessages" class="ms-chat-area">
            {{-- Welcome / Empty State --}}
            <div class="ms-empty-state">
                <div class="ms-empty-avatar">
                    <i class="bi bi-stars"></i>
                </div>
                <p class="ms-empty-title">MediSense AI</p>
                <p class="ms-empty-sub">Clinical Decision Support Assistant</p>
                <p class="ms-empty-hint">
                    Ask about patient clinical information, laboratory results,
                    medications, or other supported clinical workflows.
                </p>
            </div>
        </div>

        {{-- Input Footer --}}
        <div class="ms-input-area">
            <form id="fabChatForm" class="ms-input-form" novalidate>
                <input type="text"
                       id="fabInputPrompt"
                       class="ms-input"
                       placeholder="Ask MediSense AI…"
                       autocomplete="off"
                       required>
                <button type="submit" id="fabBtnSend" class="ms-send-btn" aria-label="Send message">
                    <i class="bi bi-send-fill"></i>
                </button>
            </form>
        </div>

    </div>
</div>

<style>
/* ═══════════════════════════════════════════════════════════
   MEDISENSE AI — DESIGN TOKENS
   ═══════════════════════════════════════════════════════════ */
:root {
    --ms-surface:       #ffffff;
    --ms-surface-alt:   #f7faf8;
    --ms-border:        #e2e8f0;
    --ms-text:          #173b2a;
    --ms-muted:         #64748b;
    --ms-accent:        #15803d;
    --ms-accent-light:  #f0fdf4;
    --ms-accent-border: #bbf7d0;
    --ms-user-bg:       #eaf7ee;
    --ms-user-border:   #c8ebd1;
    --ms-user-text:     #0f172a;
    --ms-danger:        #dc2626;
    --ms-widget-radius: 1.25rem;
    --ms-header-grad:   linear-gradient(135deg, #15803d 0%, #166534 100%);
    --ms-shadow:        0 16px 48px rgba(0,0,0,0.14), 0 2px 8px rgba(0,0,0,0.06);
}

html[data-theme="dark"],
html[data-bs-theme="dark"] {
    --ms-surface:       #111111;
    --ms-surface-alt:   #000000;
    --ms-border:        #262626;
    --ms-text:          #ffffff;
    --ms-muted:         #a3a3a3;
    --ms-accent:        #4ade80;
    --ms-accent-light:  rgba(22,163,74,0.12);
    --ms-accent-border: rgba(22,163,74,0.3);
    --ms-user-bg:       #1a2a1e;
    --ms-user-border:   #2d4a35;
    --ms-user-text:     #e2e8f0;
    --ms-shadow:        0 16px 48px rgba(0,0,0,0.7), 0 2px 8px rgba(0,0,0,0.4);
}

/* ═══════════════════════════════════════════════════════════
   FLOATING ACTION BUTTON
   ═══════════════════════════════════════════════════════════ */
.ms-fab-btn {
    position: relative;
    width: 52px;
    height: 52px;
    border-radius: 50%;
    border: none;
    background: var(--ms-header-grad);
    color: #ffffff;
    display: grid;
    place-items: center;
    box-shadow: 0 8px 24px rgba(21,128,61,0.3);
    cursor: pointer;
    transition: transform 0.2s cubic-bezier(0.34,1.56,0.64,1), box-shadow 0.2s ease;
    outline: none;
}
.ms-fab-btn:hover {
    transform: scale(1.08);
    box-shadow: 0 12px 30px rgba(21,128,61,0.4);
}
.ms-fab-btn:active {
    transform: scale(0.94);
}
.ms-fab-btn.ms-fab-open {
    transform: scale(0.9) rotate(45deg);
}
.ms-fab-icon {
    font-size: 1.35rem;
    pointer-events: none;
}
.ms-fab-badge {
    position: absolute;
    top: 0;
    right: -2px;
    background: #dc2626;
    color: #fff;
    font-size: 0.5rem;
    font-weight: 700;
    letter-spacing: 0.03em;
    padding: 0.15em 0.38em;
    border-radius: 999px;
    border: 1.5px solid #fff;
    line-height: 1.4;
    pointer-events: none;
}

/* ═══════════════════════════════════════════════════════════
   WIDGET POPUP CONTAINER
   ═══════════════════════════════════════════════════════════ */
.ms-widget {
    position: fixed !important;
    bottom: 80px;
    right: 20px;
    width: 400px;
    max-width: calc(100vw - 32px);
    height: 560px;
    max-height: calc(100vh - 110px);
    z-index: 1090;
    border-radius: var(--ms-widget-radius);
    background: var(--ms-surface);
    border: 1px solid var(--ms-border);
    box-shadow: var(--ms-shadow);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    /* Open animation */
    transform-origin: bottom right;
    transition: opacity 0.22s ease, transform 0.22s cubic-bezier(0.16,1,0.3,1);
    opacity: 1;
    transform: translateY(0) scale(1);
}
.ms-widget.d-none {
    display: none !important;
}
/* Entrance state — toggled by JS */
.ms-widget.ms-entering {
    opacity: 0;
    transform: translateY(14px) scale(0.96);
}

/* ═══════════════════════════════════════════════════════════
   HEADER
   ═══════════════════════════════════════════════════════════ */
.ms-widget-header {
    background: var(--ms-header-grad);
    padding: 0.7rem 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    flex-shrink: 0;
    min-width: 0;
}
.ms-widget-header-identity {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    min-width: 0;
    flex: 1;
}
.ms-widget-avatar {
    width: 32px;
    height: 32px;
    background: rgba(255,255,255,0.18);
    border: 1px solid rgba(255,255,255,0.25);
    border-radius: 0.5rem;
    display: grid;
    place-items: center;
    flex-shrink: 0;
    font-size: 0.95rem;
    color: #ffffff;
}
.ms-widget-title-group {
    min-width: 0;
    flex: 1;
}
.ms-widget-title {
    display: block;
    font-size: 0.875rem;
    font-weight: 700;
    color: #ffffff;
    font-family: var(--font-display, 'Inter', sans-serif);
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.ms-widget-subtitle {
    display: block;
    font-size: 0.625rem;
    color: rgba(255,255,255,0.7);
    letter-spacing: 0.04em;
    text-transform: uppercase;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.ms-widget-header-actions {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    flex-shrink: 0;
}
.ms-header-icon-btn {
    width: 28px;
    height: 28px;
    border-radius: 0.375rem;
    border: none;
    background: transparent;
    color: rgba(255,255,255,0.65);
    display: grid;
    place-items: center;
    font-size: 0.8rem;
    cursor: pointer;
    transition: background 0.15s ease, color 0.15s ease;
    text-decoration: none;
    outline: none;
}
.ms-header-icon-btn:hover {
    background: rgba(255,255,255,0.18);
    color: #ffffff;
}
.ms-close-btn:hover {
    background: rgba(220,38,38,0.5);
    color: #ffffff;
}

/* ═══════════════════════════════════════════════════════════
   CHAT MESSAGES AREA
   ═══════════════════════════════════════════════════════════ */
.ms-chat-area {
    flex: 1 1 auto;
    min-height: 0;
    overflow-y: auto;
    padding: 1rem;
    background: var(--ms-surface-alt);
    scroll-behavior: smooth;
    /* Custom scrollbar */
    scrollbar-width: thin;
    scrollbar-color: var(--ms-border) transparent;
}
.ms-chat-area::-webkit-scrollbar { width: 5px; }
.ms-chat-area::-webkit-scrollbar-track { background: transparent; }
.ms-chat-area::-webkit-scrollbar-thumb { background: var(--ms-border); border-radius: 4px; }
html[data-theme="dark"] .ms-chat-area::-webkit-scrollbar-thumb,
html[data-bs-theme="dark"] .ms-chat-area::-webkit-scrollbar-thumb { background: #333; }

/* ─── Empty / Welcome State ─── */
.ms-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 2rem 1.25rem;
    gap: 0.25rem;
}
.ms-empty-avatar {
    width: 52px;
    height: 52px;
    background: var(--ms-accent-light);
    border: 1.5px solid var(--ms-accent-border);
    border-radius: 1rem;
    display: grid;
    place-items: center;
    font-size: 1.5rem;
    color: var(--ms-accent);
    margin-bottom: 0.75rem;
}
.ms-empty-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--ms-text);
    margin: 0;
}
.ms-empty-sub {
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--ms-accent);
    margin: 0.1rem 0 0.5rem;
    font-weight: 600;
}
.ms-empty-hint {
    font-size: 0.78rem;
    color: var(--ms-muted);
    line-height: 1.55;
    max-width: 280px;
    margin: 0;
}

/* ─── AI Message Bubble ─── */
.ms-ai-row {
    display: flex;
    gap: 0.5rem;
    align-items: flex-start;
    margin-bottom: 0.875rem;
}
.ms-ai-avatar {
    width: 26px;
    height: 26px;
    flex-shrink: 0;
    background: var(--ms-accent-light);
    border: 1px solid var(--ms-accent-border);
    border-radius: 0.5rem;
    display: grid;
    place-items: center;
    color: var(--ms-accent);
    font-size: 0.8rem;
    margin-top: 2px;
}
.ms-ai-bubble {
    background: var(--ms-surface);
    border: 1px solid var(--ms-border);
    border-radius: 0 0.875rem 0.875rem 0.875rem;
    padding: 0.6rem 0.875rem;
    max-width: 87%;
    font-size: 0.8rem;
    line-height: 1.6;
    color: var(--ms-text);
    word-break: break-word;
    overflow-wrap: anywhere;
    box-shadow: 0 1px 3px rgba(0,0,0,0.07);
}
.ms-ai-label {
    font-size: 0.7rem;
    font-weight: 700;
    color: var(--ms-accent);
    margin-bottom: 0.3rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

/* ─── User Message Bubble ─── */
.ms-user-row {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 0.625rem;
}
.fab-user-bubble {
    display: inline-block;
    max-width: 82%;
    white-space: pre-wrap;
    overflow-wrap: anywhere;
    word-break: break-word;
    border-radius: 0.875rem 0 0.875rem 0.875rem;
    background: var(--ms-user-bg);
    padding: 0.5rem 0.875rem;
    font-size: 0.8rem;
    line-height: 1.55;
    color: var(--ms-user-text);
    border: 1px solid var(--ms-user-border);
    box-shadow: 0 1px 3px rgba(21,128,61,0.07);
}

/* ─── Thinking / Loading Animation ─── */
.ms-thinking-row {
    display: flex;
    gap: 0.5rem;
    align-items: flex-start;
    margin-bottom: 0.75rem;
}
.ms-thinking-bubble {
    background: var(--ms-surface);
    border: 1px solid var(--ms-border);
    border-radius: 0 0.875rem 0.875rem 0.875rem;
    padding: 0.55rem 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.625rem;
    font-size: 0.76rem;
    color: var(--ms-muted);
}
.ms-dots {
    display: flex;
    gap: 3px;
    align-items: center;
}
.ms-dots span {
    display: block;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--ms-accent);
    animation: ms-pulse 1.4s ease-in-out infinite;
}
.ms-dots span:nth-child(2) { animation-delay: 0.18s; }
.ms-dots span:nth-child(3) { animation-delay: 0.36s; }
@keyframes ms-pulse {
    0%, 80%, 100% { opacity: 0.2; transform: scale(0.8); }
    40%           { opacity: 1;   transform: scale(1);   }
}

/* ─── Error Bubble ─── */
.ms-error-bubble {
    background: rgba(220,38,38,0.08);
    border: 1px solid rgba(220,38,38,0.25);
    border-radius: 0.75rem;
    padding: 0.5rem 0.875rem;
    font-size: 0.78rem;
    color: #dc2626;
    margin-bottom: 0.625rem;
    word-break: break-word;
}
html[data-theme="dark"] .ms-error-bubble,
html[data-bs-theme="dark"] .ms-error-bubble {
    color: #fca5a5;
    background: rgba(220,38,38,0.12);
    border-color: rgba(220,38,38,0.3);
}

/* ─── Source Badges & Citations ─── */
.ms-sources { display: flex; flex-wrap: wrap; gap: 0.3rem; margin-top: 0.5rem; padding-top: 0.4rem; border-top: 1px solid var(--ms-border); }
.ms-source-badge { font-size: 0.62rem; padding: 0.2em 0.55em; background: var(--ms-accent-light); color: var(--ms-accent); border: 1px solid var(--ms-accent-border); border-radius: 999px; font-weight: 600; }
.ms-citations { margin-top: 0.5rem; padding: 0.5rem 0.625rem; background: var(--ms-surface-alt); border: 1px solid var(--ms-border); border-radius: 0.625rem; font-size: 0.7rem; }
.ms-citations-label { font-weight: 700; color: var(--ms-text); margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.3rem; }
.ms-citations a { color: var(--ms-accent); text-decoration: none; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.ms-citations a:hover { text-decoration: underline; }

/* ─── Capability Badge ─── */
.ms-cap-badge { font-size: 0.6rem; padding: 0.18em 0.5em; background: var(--ms-accent-light); color: var(--ms-accent); border: 1px solid var(--ms-accent-border); border-radius: 999px; font-weight: 600; }

/* ─── Confirmation Card ─── */
.ms-confirm-card {
    background: rgba(21,128,61,0.08);
    border: 1px solid rgba(21,128,61,0.25);
    border-radius: 0.875rem;
    padding: 0.75rem 0.875rem;
    font-size: 0.78rem;
    margin-bottom: 0.75rem;
    color: var(--ms-text);
}
.ms-confirm-title {
    font-size: 0.78rem;
    font-weight: 700;
    color: var(--ms-accent);
    display: flex;
    align-items: center;
    gap: 0.35rem;
    margin-bottom: 0.4rem;
}
.ms-confirm-detail { font-size: 0.73rem; color: var(--ms-muted); margin-bottom: 0.5rem; }
.ms-confirm-actions { display: flex; gap: 0.4rem; flex-wrap: wrap; }

/* ═══════════════════════════════════════════════════════════
   INPUT AREA
   ═══════════════════════════════════════════════════════════ */
.ms-input-area {
    flex-shrink: 0;
    padding: 0.625rem 0.875rem;
    background: var(--ms-surface);
    border-top: 1px solid var(--ms-border);
}
.ms-input-form {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}
.ms-input {
    flex: 1;
    min-width: 0;
    background: var(--ms-surface-alt);
    border: 1.5px solid var(--ms-border);
    border-radius: 0.875rem;
    padding: 0.45rem 0.875rem;
    font-size: 0.83rem;
    color: var(--ms-text);
    outline: none;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
    font-family: var(--font-body, 'Inter', sans-serif);
}
.ms-input::placeholder { color: var(--ms-muted); opacity: 1; }
.ms-input:focus {
    border-color: #22c55e;
    box-shadow: 0 0 0 3px rgba(34,197,94,0.15);
}
.ms-input:disabled { opacity: 0.55; cursor: not-allowed; }
.ms-send-btn {
    flex-shrink: 0;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: none;
    background: #15803d;
    color: #ffffff;
    display: grid;
    place-items: center;
    font-size: 0.875rem;
    cursor: pointer;
    transition: background 0.15s ease, transform 0.1s ease, box-shadow 0.15s ease;
    box-shadow: 0 2px 8px rgba(21,128,61,0.3);
}
.ms-send-btn:hover { background: #166534; box-shadow: 0 4px 12px rgba(21,128,61,0.35); }
.ms-send-btn:active { transform: scale(0.92); }
.ms-send-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

/* Dark Mode input overrides */
html[data-theme="dark"] .ms-input,
html[data-bs-theme="dark"] .ms-input {
    background: #0a0a0a !important;
    border-color: #333 !important;
    color: #fff !important;
}
html[data-theme="dark"] .ms-input::placeholder,
html[data-bs-theme="dark"] .ms-input::placeholder { color: #737373 !important; }

/* ═══════════════════════════════════════════════════════════
   RESPONSIVE BREAKPOINTS
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 640px) {
    #medisenseFabContainer { padding: 0.75rem !important; }
    .ms-fab-btn { width: 48px; height: 48px; }
    .ms-fab-icon { font-size: 1.2rem; }
    .ms-widget {
        position: fixed !important;
        bottom: 70px;
        right: 8px;
        left: 8px;
        width: auto !important;
        max-width: calc(100vw - 16px) !important;
        height: calc(100vh - 88px);
        max-height: 520px;
        border-radius: 1rem;
    }
    .ms-input { font-size: 16px !important; } /* Prevent iOS auto-zoom */
}

@media (max-width: 380px) {
    .ms-widget {
        bottom: 66px;
        right: 6px;
        left: 6px;
        max-width: calc(100vw - 12px) !important;
        max-height: 480px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const fabBtn = document.getElementById('medisenseFabBtn');
    const fabWidget = document.getElementById('medisenseFabWidget');
    const fabClose = document.getElementById('medisenseFabClose');
    const fabForm = document.getElementById('fabChatForm');
    const fabInputPrompt = document.getElementById('fabInputPrompt');
    const fabChatMessages = document.getElementById('fabChatMessages');
    const fabBtnSend = document.getElementById('fabBtnSend');
    let fabConversationHistory = [];

    window.closeFabWidget = function() {
        if (!fabWidget) return;
        fabWidget.classList.add('ms-entering');
        fabWidget.style.opacity = '0';
        fabWidget.style.transform = 'translateY(14px) scale(0.96)';
        setTimeout(() => {
            fabWidget.classList.add('d-none');
            fabWidget.classList.remove('ms-entering');
            fabWidget.style.opacity = '';
            fabWidget.style.transform = '';
        }, 200);
        if (fabBtn) { fabBtn.classList.remove('ms-fab-open'); }
    };

    // Toggle widget
    if (fabBtn && fabWidget) {
        fabBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            if (fabWidget.classList.contains('d-none')) {
                // Open with entrance animation
                fabWidget.classList.remove('d-none');
                fabWidget.classList.add('ms-entering');
                fabWidget.style.opacity = '0';
                fabWidget.style.transform = 'translateY(14px) scale(0.96)';
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        fabWidget.style.opacity = '1';
                        fabWidget.style.transform = 'translateY(0) scale(1)';
                        fabWidget.classList.remove('ms-entering');
                    });
                });
                fabBtn.classList.add('ms-fab-open');
                setTimeout(() => { if (fabInputPrompt) fabInputPrompt.focus(); }, 200);
            } else {
                window.closeFabWidget();
            }
        });
    }

    if (fabClose) {
        fabClose.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            window.closeFabWidget();
        });
    }

    document.addEventListener('click', function(e) {
        if (fabWidget && !fabWidget.classList.contains('d-none')) {
            const container = document.getElementById('medisenseFabContainer');
            if (container && !container.contains(e.target)) {
                window.closeFabWidget();
            }
        }
    });

    // Submit FAB chat
    if (fabForm) {
        fabForm.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        fabForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const prompt = fabInputPrompt.value.trim();
            if (!prompt) return;

            // Render User Bubble
            appendFabUser(prompt);
            fabInputPrompt.value = '';

            // Record conversation history
            fabConversationHistory.push({ role: 'user', content: prompt });

            // Render Loading
            const loadId = appendFabLoading();
            fabBtnSend.disabled = true;

            fetch("{{ route('medisense.chat') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    prompt: prompt,
                    conversation_history: fabConversationHistory.slice(-6)
                })
            })
            .then(res => res.json())
            .then(data => {
                removeFabMsg(loadId);
                fabBtnSend.disabled = false;

                if (data.requires_confirm) {
                    appendFabConfirmationPrompt(data);
                } else if (data.success) {
                    fabConversationHistory.push({ role: 'assistant', content: data.ai_response });
                    appendFabAi(data.ai_response, data.capability_label, data.sources, data.citations);
                } else {
                    appendFabError(data.error || 'Error generating AI response.');
                }
            })
            .catch(() => {
                removeFabMsg(loadId);
                fabBtnSend.disabled = false;
                appendFabError('Network error.');
            });
        });
    }

    // Remove empty state on first message
    function removeEmptyState() {
        const empty = fabChatMessages.querySelector('.ms-empty-state');
        if (empty) empty.remove();
    }

    function appendFabUser(txt) {
        removeEmptyState();
        const html = `
            <div class="ms-user-row">
                <div class="fab-user-bubble">${escapeFabHtml(txt)}</div>
            </div>
        `;
        fabChatMessages.insertAdjacentHTML('beforeend', html);
        fabChatMessages.scrollTop = fabChatMessages.scrollHeight;
    }

    function appendFabLoading() {
        const id = 'fab-load-' + Date.now();
        const html = `
            <div id="${id}" class="ms-thinking-row">
                <div class="ms-ai-avatar"><i class="bi bi-stars"></i></div>
                <div class="ms-thinking-bubble">
                    <div class="ms-dots">
                        <span></span><span></span><span></span>
                    </div>
                    <span>MediSense is thinking…</span>
                </div>
            </div>
        `;
        fabChatMessages.insertAdjacentHTML('beforeend', html);
        fabChatMessages.scrollTop = fabChatMessages.scrollHeight;
        return id;
    }

    function appendFabAi(txt, capLabel, sources, citations) {
        const formatted = formatFabMarkdown(txt);
        const capBadge = capLabel
            ? `<span class="ms-cap-badge">${escapeFabHtml(capLabel)}</span>` : '';

        let badgesHtml = '';
        if (sources && Array.isArray(sources) && sources.length > 0) {
            badgesHtml = '<div class="ms-sources">';
            sources.forEach(src => {
                badgesHtml += `<span class="ms-source-badge">${escapeFabHtml(src)}</span>`;
            });
            badgesHtml += '</div>';
        }

        let citationsHtml = '';
        if (citations && Array.isArray(citations) && citations.length > 0) {
            citationsHtml = '<div class="ms-citations"><div class="ms-citations-label"><i class="bi bi-globe"></i> Grounding Citations</div>';
            citations.forEach(cit => {
                citationsHtml += `<a href="${escapeFabHtml(cit.url)}" target="_blank" rel="noopener noreferrer">
                    ${escapeFabHtml(cit.title || cit.url)} <i class="bi bi-box-arrow-up-right" style="font-size:0.6rem;"></i>
                </a>`;
            });
            citationsHtml += '</div>';
        }

        const html = `
            <div class="ms-ai-row">
                <div class="ms-ai-avatar"><i class="bi bi-stars"></i></div>
                <div class="ms-ai-bubble">
                    <div class="ms-ai-label"><i class="bi bi-stars"></i> MediSense AI ${capBadge}</div>
                    <div>${formatted}</div>
                    ${citationsHtml}${badgesHtml}
                </div>
            </div>
        `;
        fabChatMessages.insertAdjacentHTML('beforeend', html);
        fabChatMessages.scrollTop = fabChatMessages.scrollHeight;
    }

    function appendFabConfirmationPrompt(data) {
        const details = data.action_details || {};
        const html = `
            <div class="ms-confirm-card">
                <div class="ms-confirm-title">
                    <i class="bi bi-shield-exclamation"></i> Action Confirmation Required
                </div>
                <p class="ms-confirm-detail" style="margin-bottom:0.5rem;">${escapeFabHtml(data.ai_response || 'Confirmation required.')}</p>
                <div class="ms-confirm-detail" style="background:var(--ms-surface-alt);border:1px solid var(--ms-border);border-radius:0.5rem;padding:0.4rem 0.6rem;margin-bottom:0.5rem;">
                    <div><strong>Action:</strong> ${escapeFabHtml(details.action || 'HIMS Action')}</div>
                    <div><strong>Patient:</strong> ${escapeFabHtml(details.patient || 'N/A')}</div>
                    <div><strong>Details:</strong> ${escapeFabHtml(details.test_name || '')}</div>
                </div>
                <div class="ms-confirm-actions">
                    <button type="button" class="btn btn-sm btn-success py-1 px-2 fw-semibold" style="font-size:0.72rem;" onclick="confirmFabAction('${escapeFabHtml(details.patient_id || '')}', '${escapeFabHtml(details.test_name || '')}')">
                        <i class="bi bi-check-circle me-1"></i>Confirm &amp; Execute
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size:0.72rem;" onclick="this.closest('.ms-confirm-card').remove()">Cancel</button>
                </div>
            </div>
        `;
        fabChatMessages.insertAdjacentHTML('beforeend', html);
        fabChatMessages.scrollTop = fabChatMessages.scrollHeight;
    }

    window.confirmFabAction = function (patientId, testName) {
        appendFabUser(`[CONFIRMED] Execute laboratory request for ${testName}`);
        const loadId = appendFabLoading();

        fetch("{{ route('medisense.chat') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                prompt: `Create a laboratory request for ${testName} with confirmed=true`,
                patient_id: patientId ? parseInt(patientId) : null,
            })
        })
        .then(res => res.json())
        .then(data => {
            removeFabMsg(loadId);
            if (data.success) {
                fabConversationHistory.push({ role: 'assistant', content: data.ai_response });
                appendFabAi(data.ai_response, 'Laboratory Request', data.sources, data.citations);
            } else {
                appendFabError(data.error || 'Action failed to execute.');
            }
        });
    };

    function formatFabMarkdown(str) {
        if (!str) return '';
        let escaped = escapeFabHtml(str);
        escaped = escaped.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        escaped = escaped.replace(/\*(.*?)\*/g, '<em>$1</em>');
        escaped = escaped.replace(/`([^`]+)`/g, '<code class="bg-body-secondary px-1 py-0.5 rounded" style="font-size:0.72rem;">$1</code>');
        escaped = escaped.replace(/\n/g, '<br>');
        return escaped;
    }

    function appendFabError(err) {
        const html = `
            <div class="ms-error-bubble">
                <i class="bi bi-exclamation-triangle me-1"></i>${escapeFabHtml(err)}
            </div>
        `;
        fabChatMessages.insertAdjacentHTML('beforeend', html);
        fabChatMessages.scrollTop = fabChatMessages.scrollHeight;
    }

    function removeFabMsg(id) {
        const el = document.getElementById(id);
        if (el) el.remove();
    }

    function escapeFabHtml(str) {
        const d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }
});
</script>
@endif
