@extends('layouts.app')

@section('title', 'MediSense AI — Intelligent Clinical Decision Support Assistant')
@section('page-title', 'MediSense')

@section('page-titlebar-custom')
<div class="d-flex align-items-center justify-content-between w-100">
    <div class="d-flex align-items-center gap-2">
        <div class="gpt-model-badge" title="MediSense AI Model Version">
            <i class="bi bi-sparkles text-success"></i>
            <span>MediSense AI 2.0</span>
            <span class="status-dot ms-1" title="Engine Active"></span>
        </div>

        {{-- Role pill --}}
        <span class="badge bg-body-secondary text-body-emphasis border px-2 py-1.5" style="font-size: 0.72rem;">
            <i class="bi bi-person-badge me-1 text-success"></i> {{ auth()->user()->roleName }}
        </span>
    </div>

    <div class="d-flex align-items-center gap-2">
        {{-- Activity Drawer Toggle --}}
        <button type="button" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" data-bs-toggle="offcanvas" data-bs-target="#activityDrawer" title="View Recent Interactions & Role Scope">
            <i class="bi bi-clock-history"></i>
            <span class="d-none d-md-inline">Activity Log</span>
        </button>

        {{-- New Chat Button --}}
        <button id="btnClearChat" type="button" class="btn btn-sm btn-outline-primary fw-medium d-flex align-items-center gap-1" title="Start New Chat">
            <i class="bi bi-pencil-square"></i>
            <span>New Chat</span>
        </button>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* ChatGPT Inspired Theme Design System */
    :root {
        --gpt-bg-canvas: #f9f9fb;
        --gpt-bg-surface: #ffffff;
        --gpt-bg-user: #f4f4f6;
        --gpt-bg-input: #ffffff;
        --gpt-bg-hover: #ececee;
        --gpt-text-main: #0d0d0d;
        --gpt-text-muted: #676767;
        --gpt-border: #e5e5e7;
        --gpt-emerald: #10a37f;
        --gpt-emerald-dark: #0d8a6c;
        --gpt-emerald-light: rgba(16, 163, 127, 0.12);
        --gpt-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    }

    [data-bs-theme="dark"], body.dark-mode {
        --gpt-bg-canvas: #212121;
        --gpt-bg-surface: #171717;
        --gpt-bg-user: #2f2f2f;
        --gpt-bg-input: #2f2f2f;
        --gpt-bg-hover: #383838;
        --gpt-text-main: #ececf1;
        --gpt-text-muted: #b4b4b4;
        --gpt-border: #383838;
        --gpt-shadow: 0 4px 24px rgba(0, 0, 0, 0.35);
    }

    .medisense-gpt-wrapper {
        background-color: var(--gpt-bg-canvas);
        color: var(--gpt-text-main);
        min-height: calc(100vh - 70px);
        display: flex;
        flex-direction: column;
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    /* Top ChatGPT Model Header Bar */
    .gpt-header {
        height: 56px;
        border-bottom: 1px solid var(--gpt-border);
        background-color: var(--gpt-bg-canvas);
        backdrop-filter: blur(8px);
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .gpt-model-badge {
        background-color: var(--gpt-bg-hover);
        color: var(--gpt-text-main);
        font-weight: 600;
        font-size: 0.9rem;
        padding: 0.4rem 0.85rem;
        border-radius: 0.75rem;
        border: 1px solid var(--gpt-border);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.15s ease;
    }

    .gpt-model-badge:hover {
        background-color: var(--gpt-border);
    }

    .status-dot {
        width: 8px;
        height: 8px;
        background-color: var(--gpt-emerald);
        border-radius: 50%;
        box-shadow: 0 0 8px var(--gpt-emerald);
    }

    /* Main Chat Column Centering */
    .gpt-container {
        max-width: 820px;
        width: 100%;
        margin: 0 auto;
    }

    .gpt-chat-stream {
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem 1rem 140px 1rem;
        scroll-behavior: smooth;
    }

    /* Empty State Welcome Screen */
    .gpt-empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 55vh;
        text-align: center;
    }

    .gpt-spark-icon {
        width: 56px;
        height: 56px;
        background: linear-gradient(135deg, #10a37f 0%, #0d8a6c 100%);
        color: #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        box-shadow: 0 8px 24px rgba(16, 163, 127, 0.3);
        margin-bottom: 1.25rem;
    }

    .gpt-starter-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
        width: 100%;
        max-width: 720px;
        margin-top: 2rem;
    }

    @media (max-width: 640px) {
        .gpt-starter-grid {
            grid-template-columns: 1fr;
        }
    }

    .gpt-starter-card {
        background-color: var(--gpt-bg-surface);
        border: 1px solid var(--gpt-border);
        border-radius: 1rem;
        padding: 1rem 1.15rem;
        text-align: left;
        cursor: pointer;
        transition: all 0.18s ease;
    }

    .gpt-starter-card:hover {
        background-color: var(--gpt-bg-hover);
        border-color: var(--gpt-emerald);
        transform: translateY(-2px);
    }

    /* Message Row & Bubbles */
    .gpt-msg-row {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.75rem;
        animation: fadeIn 0.2s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(4px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .gpt-msg-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.9rem;
        flex-shrink: 0;
    }

    .gpt-avatar-ai {
        background: linear-gradient(135deg, #10a37f 0%, #0abf95 100%);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(16, 163, 127, 0.25);
    }

    .gpt-avatar-user {
        background-color: var(--gpt-bg-user);
        color: var(--gpt-text-main);
        border: 1px solid var(--gpt-border);
    }

    .gpt-user-bubble {
        background-color: var(--gpt-bg-user);
        color: var(--gpt-text-main);
        padding: 0.85rem 1.25rem;
        border-radius: 1.25rem;
        max-width: 85%;
        margin-left: auto;
        line-height: 1.55;
        font-size: 0.95rem;
    }

    .gpt-ai-response {
        flex: 1;
        min-width: 0;
        color: var(--gpt-text-main);
        line-height: 1.65;
        font-size: 0.95rem;
    }

    /* Bottom Fixed Floating Bar */
    .gpt-bottom-fixed {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, var(--gpt-bg-canvas) 80%, rgba(0,0,0,0));
        padding: 0.5rem 1rem 1rem 1rem;
        z-index: 90;
    }

    /* Quick Action Suggestions Chips */
    .gpt-chip-container {
        display: flex;
        gap: 0.5rem;
        overflow-x: auto;
        padding-bottom: 0.5rem;
        scrollbar-width: none;
    }

    .gpt-chip-container::-webkit-scrollbar {
        display: none;
    }

    .gpt-chip {
        background-color: var(--gpt-bg-surface);
        border: 1px solid var(--gpt-border);
        color: var(--gpt-text-main);
        font-size: 0.8rem;
        font-weight: 500;
        padding: 0.35rem 0.85rem;
        border-radius: 2rem;
        white-space: nowrap;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .gpt-chip:hover {
        background-color: var(--gpt-emerald);
        color: #ffffff;
        border-color: var(--gpt-emerald);
    }

    /* Input Pill Bar */
    .gpt-input-box {
        background-color: var(--gpt-bg-input);
        border: 1px solid var(--gpt-border);
        border-radius: 1.75rem;
        padding: 0.5rem 0.75rem 0.5rem 1.15rem;
        box-shadow: var(--gpt-shadow);
        display: flex;
        align-items: flex-end;
        gap: 0.75rem;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .gpt-input-box:focus-within {
        border-color: var(--gpt-emerald);
        box-shadow: 0 0 0 2px var(--gpt-emerald-light), var(--gpt-shadow);
    }

    .gpt-textarea {
        background: transparent;
        border: none;
        outline: none;
        color: var(--gpt-text-main);
        font-size: 0.95rem;
        line-height: 1.5;
        width: 100%;
        resize: none;
        max-height: 160px;
        min-height: 24px;
        padding: 0.35rem 0;
    }

    .gpt-send-btn {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background-color: var(--gpt-emerald);
        color: #ffffff;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
        transition: all 0.15s ease;
    }

    .gpt-send-btn:hover {
        background-color: var(--gpt-emerald-dark);
        transform: scale(1.05);
    }

    .gpt-send-btn:disabled {
        background-color: var(--gpt-border);
        color: var(--gpt-text-muted);
        cursor: not-allowed;
        transform: none;
    }

    .gpt-disclaimer {
        font-size: 0.73rem;
        color: var(--gpt-text-muted);
        text-align: center;
        margin-top: 0.4rem;
    }
</style>
@endpush

@section('content')
<div class="medisense-gpt-wrapper" id="gptWrapper">
    {{-- Main Chat Stream Area --}}
    <main id="chatMessages" class="gpt-chat-stream">
        <div class="gpt-container">
            {{-- ChatGPT Welcome Screen (Empty State) --}}
            <div id="welcomeScreen" class="gpt-empty-state">
                <div class="gpt-spark-icon">
                    <i class="bi bi-sparkles"></i>
                </div>
                <h3 class="fw-bold mb-2 text-body">MediSense AI</h3>
                <p class="text-body-secondary mb-1" style="max-width: 580px; font-size: 0.95rem;">
                    An Intelligent Clinical Decision Support Assistant for Symptom Assessment, Diagnostic Assistance, Treatment Recommendation, and Clinical Service.
                </p>
                <p class="text-muted small mb-4" style="font-size: 0.8rem;">
                    Logged in as <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->roleName }}). Automatic intent detection active.
                </p>

                {{-- Starter Cards Grid --}}
                <div class="gpt-starter-grid">
                    <div class="gpt-starter-card" data-prompt="Please evaluate symptoms for a patient reporting fever, headache, and fatigue.">
                        <div class="d-flex align-items-center gap-2 mb-1 fw-semibold text-body">
                            <i class="bi bi-activity text-success fs-5"></i>
                            <span>Symptom Assessment</span>
                        </div>
                        <div class="text-body-secondary small">Evaluate clinical symptoms, risk factors, and differential triage rules.</div>
                    </div>

                    <div class="gpt-starter-card" data-prompt="Please analyze recent laboratory test results and flag panic value anomalies.">
                        <div class="d-flex align-items-center gap-2 mb-1 fw-semibold text-body">
                            <i class="bi bi-journal-medical text-success fs-5"></i>
                            <span>Lab Results Analysis</span>
                        </div>
                        <div class="text-body-secondary small">Summarize CBC, electrolytes, liver function, and critical analyte alerts.</div>
                    </div>

                    <div class="gpt-starter-card" data-prompt="Please review current patient medications for polypharmacy and drug interactions.">
                        <div class="d-flex align-items-center gap-2 mb-1 fw-semibold text-body">
                            <i class="bi bi-capsule text-success fs-5"></i>
                            <span>Medication & Rx Review</span>
                        </div>
                        <div class="text-body-secondary small">Verify drug monographs, renal adjustments, and contraindications.</div>
                    </div>

                    <div class="gpt-starter-card" data-prompt="Please summarize recent radiology imaging findings and diagnostic reports.">
                        <div class="d-flex align-items-center gap-2 mb-1 fw-semibold text-body">
                            <i class="bi bi-file-earmark-text text-success fs-5"></i>
                            <span>Radiology Interpretation</span>
                        </div>
                        <div class="text-body-secondary small">Extract imaging summary, radiologic impressions, and follow-up steps.</div>
                    </div>
                </div>
            </div>

            {{-- Messages Stream rendered dynamically --}}
            <div id="messagesContainer"></div>
        </div>
    </main>

    {{-- Bottom Fixed Floating Input Bar --}}
    <footer class="gpt-bottom-fixed">
        <div class="gpt-container">
            {{-- Quick Action Suggestion Chips Bar --}}
            <div class="gpt-chip-container mb-2" id="quickActionChips">
                <button type="button" class="gpt-chip" data-prompt="Evaluate persistent headache, fever, and nausea symptoms.">
                    <i class="bi bi-activity text-success me-1"></i> Symptom Assessment
                </button>
                <button type="button" class="gpt-chip" data-prompt="Provide diagnostic assistance and differential considerations for ">
                    <i class="bi bi-search-heart text-success me-1"></i> Diagnostic Assistance
                </button>
                <button type="button" class="gpt-chip" data-prompt="Summarize recent laboratory results and pending test batches.">
                    <i class="bi bi-journal-medical text-success me-1"></i> Summarize Labs
                </button>
                <button type="button" class="gpt-chip" data-prompt="Summarize recent radiology imaging findings and scan impressions.">
                    <i class="bi bi-file-earmark-text text-success me-1"></i> Summarize Imaging
                </button>
                <button type="button" class="gpt-chip" data-prompt="Review current patient medications for polypharmacy and dosage safety.">
                    <i class="bi bi-check2-circle text-success me-1"></i> Medication Review
                </button>
                <button type="button" class="gpt-chip" data-prompt="Assist in formulating a therapeutic diet plan for ">
                    <i class="bi bi-clipboard2-heart text-success me-1"></i> Diet Plan
                </button>
                <button type="button" class="gpt-chip" data-prompt="Assist with surgery workflow, OR scheduling, and turnaround times.">
                    <i class="bi bi-scissors text-success me-1"></i> Surgery Workflow
                </button>
                <button type="button" class="gpt-chip" data-prompt="Provide operational analytics on clinical throughput and patient backlog.">
                    <i class="bi bi-graph-up-arrow text-success me-1"></i> Operational Analytics
                </button>
            </div>

            {{-- ChatGPT Pill Input Form --}}
            <form id="chatForm" class="w-100">
                @csrf
                <div class="gpt-input-box">
                    <textarea id="inputPrompt" name="prompt" rows="1" 
                              class="gpt-textarea" 
                              placeholder="Message MediSense AI..." required></textarea>

                    <button type="submit" id="btnSend" class="gpt-send-btn" title="Send Message">
                        <i class="bi bi-arrow-up-short"></i>
                    </button>
                </div>
            </form>

            <div class="gpt-disclaimer">
                MediSense AI provides intelligent clinical decision support to authorized staff. Always verify critical recommendations against hospital protocols.
            </div>
        </div>
    </footer>
</div>

{{-- Offcanvas Activity Log Drawer --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="activityDrawer" aria-labelledby="activityDrawerLabel">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold d-flex align-items-center gap-2" id="activityDrawerLabel">
            <i class="bi bi-clock-history text-success"></i> Activity & Access Scope
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-3">
        {{-- Role Scope Card --}}
        <div class="card border-0 bg-body-tertiary mb-3 rounded-3">
            <div class="card-body p-3 small">
                <h6 class="fw-bold mb-2 text-body d-flex align-items-center gap-1.5">
                    <i class="bi bi-shield-check text-success"></i> Role Access Profile
                </h6>
                <div class="d-flex justify-content-between py-1 border-bottom">
                    <span class="text-body-secondary">Primary Role:</span>
                    <span class="fw-bold text-body">{{ auth()->user()->roleName }}</span>
                </div>
                <div class="d-flex justify-content-between py-1 border-bottom">
                    <span class="text-body-secondary">Capabilities:</span>
                    <span class="badge bg-success-subtle text-success">Unrestricted Universal Access</span>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-body-secondary">Routing Engine:</span>
                    <span class="badge bg-info-subtle text-info">Automatic Intent Detection</span>
                </div>
            </div>
        </div>

        {{-- Activity History --}}
        <h6 class="fw-bold mb-2 text-body d-flex align-items-center justify-content-between">
            <span>Recent MediSense Logs</span>
            <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.65rem;">{{ $history->count() }} logged</span>
        </h6>
        <div class="list-group list-group-flush rounded-3 border">
            @forelse($history as $item)
                <div class="list-group-item p-2.5">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="badge bg-success-subtle text-success" style="font-size: 0.65rem;">{{ ucfirst(str_replace('_', ' ', $item->capability)) }}</span>
                        <small class="text-body-secondary" style="font-size: 0.65rem;">{{ $item->created_at->diffForHumans() }}</small>
                    </div>
                    <div class="text-body text-truncate fw-medium small mb-0.5">{{ $item->user_prompt }}</div>
                    @if($item->patient)
                        <div class="text-body-secondary text-truncate" style="font-size: 0.7rem;">
                            <i class="bi bi-person me-1"></i> {{ $item->patient->full_name }}
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-3 text-center text-body-secondary small">No recent MediSense interactions.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const gptWrapper = document.getElementById('gptWrapper');
    const chatForm = document.getElementById('chatForm');
    const inputPrompt = document.getElementById('inputPrompt');
    const patientSelect = document.getElementById('patientSelect');
    const chatMessages = document.getElementById('chatMessages');
    const messagesContainer = document.getElementById('messagesContainer');
    const welcomeScreen = document.getElementById('welcomeScreen');
    const btnSend = document.getElementById('btnSend');
    const btnClearChat = document.getElementById('btnClearChat');

    // Auto-resize textarea
    inputPrompt.addEventListener('input', function () {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 160) + 'px';
    });

    // Enter to submit (Shift+Enter for newline)
    inputPrompt.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            chatForm.dispatchEvent(new Event('submit'));
        }
    });

    // Handle Quick Starter Cards & Chips
    document.addEventListener('click', function (e) {
        const starterCard = e.target.closest('.gpt-starter-card, .gpt-chip');
        if (starterCard && starterCard.dataset.prompt) {
            inputPrompt.value = starterCard.dataset.prompt;
            inputPrompt.style.height = 'auto';
            inputPrompt.style.height = Math.min(inputPrompt.scrollHeight, 160) + 'px';
            inputPrompt.focus();
        }
    });

    // Theme Initialization (if toggle present)
    const currentTheme = localStorage.getItem('medisense_gpt_theme') || 'light';
    document.documentElement.setAttribute('data-bs-theme', currentTheme);

    // Submit Chat Form
    chatForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const prompt = inputPrompt.value.trim();
        const patientId = patientSelect ? patientSelect.value : null;

        if (!prompt) return;

        // Hide welcome screen on first prompt
        if (welcomeScreen) {
            welcomeScreen.style.display = 'none';
        }

        // Render User Message
        appendUserMessage(prompt);
        inputPrompt.value = '';
        inputPrompt.style.height = 'auto';

        // Render Loading State
        const loadingId = appendLoadingMessage();
        btnSend.disabled = true;

        // Send AJAX
        fetch("{{ route('medisense.chat') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                prompt: prompt,
                patient_id: patientId ? parseInt(patientId) : null,
            })
        })
        .then(res => res.json())
        .then(data => {
            removeMessage(loadingId);
            btnSend.disabled = false;

            if (data.requires_confirm) {
                appendConfirmationPrompt(data);
            } else if (data.success) {
                appendAiMessage(data.ai_response, data.capability_label, data.sources, data.citations);
            } else {
                appendErrorMessage(data.error || 'An error occurred while processing your request.');
            }
        })
        .catch(err => {
            removeMessage(loadingId);
            btnSend.disabled = false;
            appendErrorMessage('Network connection error. Please try again.');
        });
    });

    // Clear Session
    if (btnClearChat) {
        btnClearChat.addEventListener('click', function () {
            if (confirm('Clear current MediSense AI session?')) {
                fetch("{{ route('medisense.clear') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(() => {
                    messagesContainer.innerHTML = '';
                    if (welcomeScreen) welcomeScreen.style.display = 'flex';
                });
            }
        });
    }

    function appendUserMessage(text) {
        const html = `
            <div class="gpt-msg-row justify-content-end">
                <div class="gpt-user-bubble">
                    ${escapeHtml(text)}
                </div>
            </div>
        `;
        messagesContainer.insertAdjacentHTML('beforeend', html);
        scrollToBottom();
    }

    function appendLoadingMessage() {
        const id = 'loading-' + Date.now();
        const html = `
            <div id="${id}" class="gpt-msg-row">
                <div class="gpt-ai-response d-flex align-items-center gap-2 text-body-secondary py-1">
                    <span class="spinner-grow spinner-grow-sm text-success"></span>
                    <span>MediSense AI is evaluating HIMS data, searching web grounding & synthesizing analysis...</span>
                </div>
            </div>
        `;
        messagesContainer.insertAdjacentHTML('beforeend', html);
        scrollToBottom();
        return id;
    }

    function appendAiMessage(markdownText, capLabel, sources, citations) {
        const formattedText = formatMarkdown(markdownText);
        const capBadge = capLabel ? `<span class="badge bg-success-subtle text-success ms-1" style="font-size: 0.65rem;">${escapeHtml(capLabel)}</span>` : '';

        // Source Badges (HIMS Data, Web Search, AI Analysis)
        let sourceBadgesHtml = '';
        if (sources && Array.isArray(sources)) {
            sourceBadgesHtml = '<div class="d-flex flex-wrap gap-1 mt-2 pt-2 border-top align-items-center" style="font-size: 0.72rem;">';
            sourceBadgesHtml += '<span class="text-body-secondary fw-semibold me-1">Sources:</span>';
            sources.forEach(src => {
                let badgeClass = 'bg-body-secondary text-body border';
                if (src.includes('HIMS')) badgeClass = 'bg-primary-subtle text-primary border border-primary-subtle';
                if (src.includes('Web')) badgeClass = 'bg-info-subtle text-info border border-info-subtle';
                if (src.includes('AI')) badgeClass = 'bg-success-subtle text-success border border-success-subtle';
                sourceBadgesHtml += `<span class="badge ${badgeClass} px-2 py-1">${escapeHtml(src)}</span>`;
            });
            sourceBadgesHtml += '</div>';
        }

        // Web Search Citations
        let citationsHtml = '';
        if (citations && Array.isArray(citations) && citations.length > 0) {
            citationsHtml = '<div class="mt-2.5 p-2.5 bg-body-tertiary rounded-3 border" style="font-size: 0.75rem;">';
            citationsHtml += '<div class="fw-bold mb-1.5 text-body d-flex align-items-center"><i class="bi bi-globe me-1.5 text-info"></i> Web Grounding Citations:</div><ul class="list-unstyled mb-0 ms-1">';
            citations.forEach(cit => {
                citationsHtml += `<li class="mb-1 text-truncate">
                    <span class="badge bg-secondary-subtle text-secondary me-1" style="font-size: 0.65rem;">${escapeHtml(cit.domain || 'web')}</span>
                    <a href="${escapeHtml(cit.url)}" target="_blank" rel="noopener noreferrer" class="text-decoration-none fw-medium text-body-emphasis hover-underline">
                        ${escapeHtml(cit.title || cit.url)} <i class="bi bi-box-arrow-up-right ms-0.5 text-body-secondary" style="font-size: 0.65rem;"></i>
                    </a>
                </li>`;
            });
            citationsHtml += '</ul></div>';
        }

        const html = `
            <div class="gpt-msg-row">
                <div class="gpt-ai-response">
                    <div class="d-flex align-items-center gap-1 mb-1 small">
                        <strong class="text-body">MediSense AI</strong>
                        ${capBadge}
                    </div>
                    <div class="medisense-formatted-output">
                        ${formattedText}
                    </div>
                    ${citationsHtml}
                    ${sourceBadgesHtml}
                </div>
            </div>
        `;
        messagesContainer.insertAdjacentHTML('beforeend', html);
        scrollToBottom();
    }

    function appendConfirmationPrompt(data) {
        const details = data.action_details || {};
        const html = `
            <div class="gpt-msg-row">
                <div class="gpt-ai-response border border-warning-subtle bg-warning-subtle p-3 rounded-3">
                    <div class="d-flex align-items-center gap-2 mb-2 text-warning-emphasis fw-bold">
                        <i class="bi bi-shield-exclamation fs-5"></i>
                        <span>Clinical Safety Confirmation Required</span>
                    </div>
                    <p class="mb-2 text-body small">${escapeHtml(data.ai_response || 'This sensitive clinical action requires user confirmation.')}</p>
                    <div class="p-2.5 bg-body rounded-2 border mb-3 small">
                        <div><strong>Action:</strong> ${escapeHtml(details.action || 'HIMS Action')}</div>
                        <div><strong>Patient:</strong> ${escapeHtml(details.patient || 'N/A')}</div>
                        <div><strong>Details:</strong> ${escapeHtml(details.test_name || '')} (${escapeHtml(details.urgency || 'ROUTINE')})</div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-success px-3 fw-semibold" onclick="confirmAction('${escapeHtml(details.patient_id || '')}', '${escapeHtml(details.test_name || '')}')">
                            <i class="bi bi-check-circle me-1"></i> Confirm & Execute Action
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3" onclick="this.closest('.gpt-msg-row').remove()">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        `;
        messagesContainer.insertAdjacentHTML('beforeend', html);
        scrollToBottom();
    }

    window.confirmAction = function (patientId, testName) {
        appendUserMessage(`[CONFIRMED] Execute laboratory request for ${testName}`);
        const loadingId = appendLoadingMessage();

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
            removeMessage(loadingId);
            if (data.success) {
                appendAiMessage(data.ai_response, 'Laboratory Request', data.sources, data.citations);
            } else {
                appendErrorMessage(data.error || 'Action failed to execute.');
            }
        });
    };

    function appendErrorMessage(errorMsg) {
        const html = `
            <div class="gpt-msg-row">
                <div class="gpt-ai-response text-danger border border-danger-subtle bg-danger-subtle p-3 rounded-3">
                    <strong>MediSense Error:</strong> ${escapeHtml(errorMsg)}
                </div>
            </div>
        `;
        messagesContainer.insertAdjacentHTML('beforeend', html);
        scrollToBottom();
    }

    function removeMessage(id) {
        const el = document.getElementById(id);
        if (el) el.remove();
    }

    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatMarkdown(src) {
        if (!src) return '';
        let out = escapeHtml(src);

        // Headers
        out = out.replace(/^### (.*$)/gim, '<h5 class="fw-bold text-body mt-3 mb-2 border-bottom pb-1" style="font-size: 0.95rem;">$1</h5>');
        out = out.replace(/^## (.*$)/gim, '<h4 class="fw-bold text-body mt-3 mb-2" style="font-size: 1.05rem;">$1</h4>');
        out = out.replace(/^# (.*$)/gim, '<h3 class="fw-bold text-body mt-3 mb-2" style="font-size: 1.15rem;">$1</h3>');

        // Bold & Italic
        out = out.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        out = out.replace(/\*(.*?)\*/g, '<em>$1</em>');

        // Blockquotes
        out = out.replace(/^&gt; (.*$)/gim, '<blockquote class="border-start border-3 border-success ps-3 text-body-secondary my-2">$1</blockquote>');

        // Bullet lists
        out = out.replace(/^\- (.*$)/gim, '<li class="ms-3 mb-1">$1</li>');

        // Line breaks
        out = out.replace(/\n/g, '<br>');

        return out;
    }
});
</script>
@endpush
