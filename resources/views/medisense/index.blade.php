@extends('layouts.app')

@section('title', 'MediSense AI — AI Clinical & Information Assistant')
@section('page-title', 'MediSense AI')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('medisense.index') }}">Clinical AI</a></li>
    <li class="breadcrumb-item active">MediSense AI Workspace</li>
@endsection

@section('page-titlebar-custom')
<div class="d-flex align-items-center justify-content-between w-100 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5" style="font-size: 0.75rem;">
            <i class="bi bi-cpu me-1"></i> Clinical Assistant
        </span>
        <span class="badge bg-body-secondary text-body-emphasis border px-2.5 py-1.5" style="font-size: 0.75rem;">
            <i class="bi bi-person-badge me-1 text-success"></i> {{ auth()->user()->roleName }}
        </span>
    </div>

    <div class="d-flex align-items-center gap-2">
        {{-- Activity Log Drawer Toggle --}}
        <button type="button" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1.5" data-bs-toggle="offcanvas" data-bs-target="#activityDrawer" title="View Recent Interactions & Role Scope">
            <i class="bi bi-clock-history"></i>
            <span class="d-none d-sm-inline">Activity Log</span>
        </button>

        {{-- New Chat Button --}}
        <button id="btnClearChat" type="button" class="btn btn-sm btn-outline-primary fw-medium d-inline-flex align-items-center gap-1.5" title="Start New Chat">
            <i class="bi bi-pencil-square"></i>
            <span>New Chat</span>
        </button>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* ══════════════════════════════════════════════════════════════════
       MEDISENSE AI — NATIVE HIMS DESIGN SYSTEM STYLES
       ══════════════════════════════════════════════════════════════════ */
    
    /* Workspace Shell */
    .medisense-workspace-card {
        background-color: var(--card);
        color: var(--text);
        border-color: var(--line) !important;
        min-height: calc(100vh - var(--topbar-height) - 54px - 3.5rem);
        display: flex;
        flex-direction: column;
        transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
    }

    /* Scrollable Stream Container */
    .medisense-chat-stream {
        background-color: var(--paper);
        scroll-behavior: smooth;
        flex: 1;
        overflow-y: auto;
    }

    /* Inner Max-Width Layout Column */
    .medisense-container {
        max-width: 860px;
        width: 100%;
        margin: 0 auto;
    }

    /* User Message Bubble */
    .medisense-user-bubble {
        background-color: var(--card) !important;
        border: 1px solid var(--line) !important;
        color: var(--text) !important;
        padding: 0.75rem 1.15rem;
        border-radius: 1rem;
        max-width: 85%;
        margin-left: auto;
        line-height: 1.55;
        font-size: 0.93rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        word-break: break-word;
        overflow-wrap: anywhere;
    }

    /* Formatted AI Output */
    .medisense-formatted-output {
        color: var(--text);
        line-height: 1.6;
        font-size: 0.93rem;
        word-break: break-word;
        overflow-wrap: anywhere;
    }

    .medisense-formatted-output p {
        margin-bottom: 0.75rem;
    }

    .medisense-formatted-output p:last-child {
        margin-bottom: 0;
    }

    .medisense-formatted-output table {
        width: 100%;
        margin-top: 0.75rem;
        margin-bottom: 0.75rem;
        border-collapse: collapse;
        font-size: 0.85rem;
    }

    .medisense-formatted-output th,
    .medisense-formatted-output td {
        padding: 0.5rem 0.75rem;
        border: 1px solid var(--line);
    }

    .medisense-formatted-output th {
        background-color: rgba(20, 199, 154, 0.08);
        font-weight: 600;
    }

    .medisense-formatted-output pre {
        background-color: var(--paper);
        border: 1px solid var(--line);
        padding: 0.75rem;
        border-radius: 0.5rem;
        overflow-x: auto;
        max-width: 100%;
        font-family: var(--font-mono);
        font-size: 0.84rem;
    }

    .medisense-formatted-output code {
        font-family: var(--font-mono);
        font-size: 0.85em;
        background-color: rgba(20, 199, 154, 0.08);
        padding: 0.15rem 0.35rem;
        border-radius: 0.25rem;
    }

    /* Input Pill Form Control */
    .medisense-input-group {
        background-color: var(--card) !important;
        border: 1px solid var(--line) !important;
        border-radius: 1.5rem !important;
        padding: 0.4rem 0.6rem 0.4rem 1rem;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .medisense-input-group:focus-within {
        border-color: var(--signal) !important;
        box-shadow: 0 0 0 3px rgba(20, 199, 154, 0.18) !important;
    }

    .medisense-textarea {
        background: transparent !important;
        border: none !important;
        outline: none !important;
        color: var(--text) !important;
        font-size: 0.92rem;
        line-height: 1.45;
        width: 100%;
        resize: none;
        max-height: 160px;
        min-height: 24px;
        padding: 0.35rem 0;
    }

    .medisense-textarea::placeholder {
        color: var(--text-soft) !important;
    }

    /* Horizontal Quick Action Suggestions Chip Bar */
    .medisense-chip-bar {
        display: flex;
        gap: 0.45rem;
        overflow-x: auto;
        white-space: nowrap;
        padding-bottom: 0.5rem;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }

    .medisense-chip-bar::-webkit-scrollbar {
        display: none;
    }

    .medisense-chip-btn {
        font-size: 0.78rem;
        font-weight: 500;
        border-radius: 2rem;
        padding: 0.3rem 0.75rem;
        border: 1px solid var(--line);
        background-color: var(--card);
        color: var(--text);
        transition: all 0.15s ease;
    }

    .medisense-chip-btn:hover {
        border-color: var(--signal);
        color: var(--signal-dark);
        background-color: rgba(20, 199, 154, 0.08);
    }

    /* Dark Mode Utility Classes & Overrides */
    html[data-theme="dark"] .medisense-user-bubble,
    html[data-bs-theme="dark"] .medisense-user-bubble {
        background-color: #172d28 !important;
        border-color: #1e3630 !important;
        color: #e2e8f0 !important;
    }

    html[data-theme="dark"] .medisense-input-group,
    html[data-bs-theme="dark"] .medisense-input-group {
        background-color: #0b1412 !important;
        border-color: #1e3630 !important;
    }

    html[data-theme="dark"] .medisense-textarea,
    html[data-bs-theme="dark"] .medisense-textarea {
        color: #e2e8f0 !important;
    }

    html[data-theme="dark"] .medisense-textarea::placeholder,
    html[data-bs-theme="dark"] .medisense-textarea::placeholder {
        color: #94a3b8 !important;
    }

    html[data-theme="dark"] .medisense-formatted-output,
    html[data-bs-theme="dark"] .medisense-formatted-output {
        color: #e2e8f0 !important;
    }

    html[data-theme="dark"] .medisense-chip-btn,
    html[data-bs-theme="dark"] .medisense-chip-btn {
        background-color: #12221e;
        border-color: #1e3630;
        color: #e2e8f0;
    }

    html[data-theme="dark"] .medisense-chip-btn:hover,
    html[data-bs-theme="dark"] .medisense-chip-btn:hover {
        background-color: rgba(20, 199, 154, 0.15);
        border-color: var(--signal);
        color: var(--signal);
    }

    html[data-theme="dark"] .medisense-starter-card,
    html[data-bs-theme="dark"] .medisense-starter-card {
        background-color: #12221e !important;
        border-color: #1e3630 !important;
        color: #e2e8f0 !important;
    }

    html[data-theme="dark"] .offcanvas,
    html[data-bs-theme="dark"] .offcanvas {
        background-color: #12221e !important;
        color: #e2e8f0 !important;
    }

    html[data-theme="dark"] .offcanvas-header,
    html[data-bs-theme="dark"] .offcanvas-header {
        border-bottom-color: #1e3630 !important;
    }

    html[data-theme="dark"] .offcanvas .list-group-item,
    html[data-bs-theme="dark"] .offcanvas .list-group-item {
        background-color: #12221e !important;
        border-color: #1e3630 !important;
        color: #e2e8f0 !important;
    }

    html[data-theme="dark"] .btn-close,
    html[data-bs-theme="dark"] .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%);
    }

    /* Mobile Viewport Responsiveness */
    @media (max-width: 767.98px) {
        .medisense-workspace-card {
            min-height: calc(100vh - var(--topbar-height) - 54px - 1.5rem);
            border-radius: 0 !important;
        }

        .medisense-chat-stream {
            padding: 1rem 0.75rem !important;
        }

        .medisense-user-bubble {
            max-width: 92% !important;
            padding: 0.65rem 0.95rem;
            font-size: 0.88rem;
        }

        .medisense-input-group {
            border-radius: 1.25rem !important;
            padding: 0.3rem 0.5rem 0.3rem 0.85rem;
        }
    }
</style>
@endpush

@section('content')
<div class="card border-0 shadow-sm medisense-workspace-card overflow-hidden">
    {{-- Card Header --}}
    <div class="card-header bg-card border-bottom py-3 px-3 px-md-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2.5">
            <div class="p-2 bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                <i class="bi bi-cpu fs-5"></i>
            </div>
            <div>
                <h6 class="mb-0 fw-bold text-body" style="font-family: var(--font-display);">MediSense AI</h6>
                <small class="text-body-secondary">Clinical & Information Decision Support</small>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1" style="font-size: 0.72rem;">
                <i class="bi bi-shield-check me-1"></i> Active Intent Detection & Guardrails
            </span>
        </div>
    </div>

    {{-- Main Chat Stream --}}
    <div id="chatMessages" class="medisense-chat-stream p-3 p-md-4">
        <div class="medisense-container">
            {{-- Welcome Screen / Empty State --}}
            <div id="welcomeScreen" class="py-4 text-center d-flex flex-column align-items-center justify-content-center" style="min-height: 50vh;">
                <div class="p-3 bg-success-subtle text-success rounded-circle d-inline-flex mb-3 shadow-xs">
                    <i class="bi bi-sparkles fs-2"></i>
                </div>
                <h4 class="fw-bold text-body mb-1" style="font-family: var(--font-display);">MediSense AI Assistant</h4>
                <p class="text-body-secondary small mb-2" style="max-width: 580px; font-size: 0.92rem;">
                    An Intelligent Clinical Decision Support Assistant for Symptom Assessment, Diagnostic Assistance, Treatment Recommendation, and Clinical Service.
                </p>
                <p class="text-body-secondary small mb-4" style="font-size: 0.8rem;">
                    Authorized User: <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->roleName }}). Type any clinical query or select a workflow task below.
                </p>

                {{-- Starter Cards Grid --}}
                <div class="row g-2.5 g-md-3 w-100 text-start" style="max-width: 760px;">
                    <div class="col-12 col-sm-6">
                        <div class="card border h-100 p-3 cursor-pointer card-hover-elevate medisense-starter-card" data-prompt="Please evaluate symptoms for a patient reporting fever, headache, and fatigue.">
                            <div class="d-flex align-items-center gap-2 mb-1 fw-semibold text-body">
                                <i class="bi bi-activity text-success fs-5"></i>
                                <span>Symptom Assessment</span>
                            </div>
                            <div class="text-body-secondary small">Evaluate clinical symptoms, risk factors, and differential triage rules.</div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6">
                        <div class="card border h-100 p-3 cursor-pointer card-hover-elevate medisense-starter-card" data-prompt="Please analyze recent laboratory test results and flag panic value anomalies.">
                            <div class="d-flex align-items-center gap-2 mb-1 fw-semibold text-body">
                                <i class="bi bi-journal-medical text-success fs-5"></i>
                                <span>Lab Results Analysis</span>
                            </div>
                            <div class="text-body-secondary small">Summarize CBC, electrolytes, liver function, and critical analyte alerts.</div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6">
                        <div class="card border h-100 p-3 cursor-pointer card-hover-elevate medisense-starter-card" data-prompt="Please review current patient medications for polypharmacy and drug interactions.">
                            <div class="d-flex align-items-center gap-2 mb-1 fw-semibold text-body">
                                <i class="bi bi-capsule text-success fs-5"></i>
                                <span>Medication & Rx Review</span>
                            </div>
                            <div class="text-body-secondary small">Verify drug monographs, renal adjustments, and contraindications.</div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6">
                        <div class="card border h-100 p-3 cursor-pointer card-hover-elevate medisense-starter-card" data-prompt="Please summarize recent radiology imaging findings and diagnostic reports.">
                            <div class="d-flex align-items-center gap-2 mb-1 fw-semibold text-body">
                                <i class="bi bi-file-earmark-text text-success fs-5"></i>
                                <span>Radiology Interpretation</span>
                            </div>
                            <div class="text-body-secondary small">Extract imaging summary, radiologic impressions, and follow-up steps.</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Messages Stream Container --}}
            <div id="messagesContainer"></div>
        </div>
    </div>

    {{-- Card Footer Input Area --}}
    <div class="card-footer bg-card border-top p-3 sticky-bottom">
        <div class="medisense-container">
            {{-- Quick Action Suggestion Chips --}}
            <div class="medisense-chip-bar mb-2" id="quickActionChips">
                <button type="button" class="btn medisense-chip-btn" data-prompt="Evaluate persistent headache, fever, and nausea symptoms.">
                    <i class="bi bi-activity text-success me-1"></i> Symptom Assessment
                </button>
                <button type="button" class="btn medisense-chip-btn" data-prompt="Provide diagnostic assistance and differential considerations for ">
                    <i class="bi bi-search-heart text-success me-1"></i> Diagnostic Assistance
                </button>
                <button type="button" class="btn medisense-chip-btn" data-prompt="Summarize recent laboratory results and pending test batches.">
                    <i class="bi bi-journal-medical text-success me-1"></i> Summarize Labs
                </button>
                <button type="button" class="btn medisense-chip-btn" data-prompt="Summarize recent radiology imaging findings and scan impressions.">
                    <i class="bi bi-file-earmark-text text-success me-1"></i> Summarize Imaging
                </button>
                <button type="button" class="btn medisense-chip-btn" data-prompt="Review current patient medications for polypharmacy and dosage safety.">
                    <i class="bi bi-check2-circle text-success me-1"></i> Medication Review
                </button>
                <button type="button" class="btn medisense-chip-btn" data-prompt="Assist in formulating a therapeutic diet plan for ">
                    <i class="bi bi-clipboard2-heart text-success me-1"></i> Diet Plan
                </button>
                <button type="button" class="btn medisense-chip-btn" data-prompt="Assist with surgery workflow, OR scheduling, and turnaround times.">
                    <i class="bi bi-scissors text-success me-1"></i> Surgery Workflow
                </button>
                <button type="button" class="btn medisense-chip-btn" data-prompt="Provide operational analytics on clinical throughput and patient backlog.">
                    <i class="bi bi-graph-up-arrow text-success me-1"></i> Operational Analytics
                </button>
            </div>

            {{-- Message Input Form --}}
            <form id="chatForm" class="w-100">
                @csrf
                <div class="medisense-input-group d-flex align-items-end gap-2">
                    <textarea id="inputPrompt" name="prompt" rows="1" 
                              class="medisense-textarea" 
                              placeholder="Message MediSense AI..." required></textarea>

                    <button type="submit" id="btnSend" class="btn btn-success rounded-circle p-0 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" title="Send Message">
                        <i class="bi bi-arrow-up-short fs-4"></i>
                    </button>
                </div>
            </form>

            <div class="text-center text-body-secondary mt-1.5" style="font-size: 0.72rem;">
                MediSense AI provides intelligent clinical decision support to authorized staff. Always verify critical recommendations against hospital protocols.
            </div>
        </div>
    </div>
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
    const chatForm = document.getElementById('chatForm');
    const inputPrompt = document.getElementById('inputPrompt');
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

    // Handle Starter Cards & Quick Action Chips
    document.addEventListener('click', function (e) {
        const starterCard = e.target.closest('.medisense-starter-card, .medisense-chip-btn');
        if (starterCard && starterCard.dataset.prompt) {
            inputPrompt.value = starterCard.dataset.prompt;
            inputPrompt.style.height = 'auto';
            inputPrompt.style.height = Math.min(inputPrompt.scrollHeight, 160) + 'px';
            inputPrompt.focus();
        }
    });

    // Submit Chat Form
    chatForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const prompt = inputPrompt.value.trim();
        if (!prompt) return;

        // Hide welcome screen on first message
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

        // Send AJAX Request
        fetch("{{ route('medisense.chat') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                prompt: prompt
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

    // Clear Session / New Chat
    if (btnClearChat) {
        btnClearChat.addEventListener('click', function () {
            if (confirm('Start a new MediSense AI conversation session?')) {
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
            <div class="d-flex justify-content-end mb-3">
                <div class="medisense-user-bubble">
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
            <div id="${id}" class="d-flex align-items-center gap-2 py-2 mb-3 text-body-secondary small">
                <div class="spinner-border spinner-border-sm text-success" role="status"></div>
                <span>MediSense AI is evaluating clinical data & synthesizing recommendations...</span>
            </div>
        `;
        messagesContainer.insertAdjacentHTML('beforeend', html);
        scrollToBottom();
        return id;
    }

    function appendAiMessage(markdownText, capLabel, sources, citations) {
        const formattedText = formatMarkdown(markdownText);
        const capBadge = capLabel ? `<span class="badge bg-success-subtle text-success border border-success-subtle ms-1" style="font-size: 0.65rem;">${escapeHtml(capLabel)}</span>` : '';

        // Source Badges
        let sourceBadgesHtml = '';
        if (sources && Array.isArray(sources) && sources.length > 0) {
            sourceBadgesHtml = '<div class="d-flex flex-wrap gap-1 mt-2.5 pt-2 border-top align-items-center" style="font-size: 0.72rem;">';
            sourceBadgesHtml += '<span class="text-body-secondary me-1">Sources:</span>';
            sources.forEach(src => {
                let badgeClass = 'bg-body-secondary text-body border';
                if (src.includes('HIMS')) badgeClass = 'bg-primary-subtle text-primary border border-primary-subtle';
                if (src.includes('Web')) badgeClass = 'bg-info-subtle text-info border border-info-subtle';
                if (src.includes('AI')) badgeClass = 'bg-success-subtle text-success border border-success-subtle';
                sourceBadgesHtml += `<span class="badge ${badgeClass} px-2 py-1">${escapeHtml(src)}</span>`;
            });
            sourceBadgesHtml += '</div>';
        }

        // Web Citations
        let citationsHtml = '';
        if (citations && Array.isArray(citations) && citations.length > 0) {
            citationsHtml = '<div class="mt-2.5 p-2.5 bg-body-tertiary rounded-3 border" style="font-size: 0.75rem;">';
            citationsHtml += '<div class="fw-bold mb-1.5 text-body d-flex align-items-center"><i class="bi bi-globe me-1.5 text-info"></i> Grounding Citations:</div><ul class="list-unstyled mb-0 ms-1">';
            citations.forEach(cit => {
                citationsHtml += `<li class="mb-1 text-truncate">
                    <span class="badge bg-secondary-subtle text-secondary me-1" style="font-size: 0.65rem;">${escapeHtml(cit.domain || 'web')}</span>
                    <a href="${escapeHtml(cit.url)}" target="_blank" rel="noopener noreferrer" class="text-decoration-none fw-medium text-body-emphasis">
                        ${escapeHtml(cit.title || cit.url)} <i class="bi bi-box-arrow-up-right ms-0.5 text-body-secondary" style="font-size: 0.65rem;"></i>
                    </a>
                </li>`;
            });
            citationsHtml += '</ul></div>';
        }

        const html = `
            <div class="d-flex align-items-start gap-2.5 mb-4">
                <div class="p-2 bg-success text-white rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                    <i class="bi bi-cpu fs-6"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                    <div class="d-flex align-items-center gap-1.5 mb-1.5">
                        <strong class="text-body small">MediSense AI</strong>
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
            <div class="card border-warning-subtle bg-warning-subtle text-warning-emphasis p-3 mb-4 rounded-3">
                <div class="d-flex align-items-center gap-2 mb-2 fw-bold">
                    <i class="bi bi-shield-exclamation fs-5"></i>
                    <span>Clinical Action Safety Confirmation Required</span>
                </div>
                <p class="mb-2 text-body small">${escapeHtml(data.ai_response || 'This action modifies HIMS records and requires confirmation.')}</p>
                <div class="p-2.5 bg-body rounded-2 border mb-3 small">
                    <div><strong>Action:</strong> ${escapeHtml(details.action || 'HIMS Action')}</div>
                    <div><strong>Patient:</strong> ${escapeHtml(details.patient || 'N/A')}</div>
                    <div><strong>Details:</strong> ${escapeHtml(details.test_name || '')} (${escapeHtml(details.urgency || 'ROUTINE')})</div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <button type="button" class="btn btn-sm btn-success px-3 fw-semibold" onclick="confirmAction('${escapeHtml(details.patient_id || '')}', '${escapeHtml(details.test_name || '')}')">
                        <i class="bi bi-check-circle me-1"></i> Confirm & Execute
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary px-3" onclick="this.closest('.card').remove()">
                        Cancel
                    </button>
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
            <div class="alert alert-danger border-danger-subtle d-flex align-items-center gap-2 mb-3 rounded-3">
                <i class="bi bi-exclamation-triangle-fill fs-5 flex-shrink-0"></i>
                <div><strong>MediSense Error:</strong> ${escapeHtml(errorMsg)}</div>
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
        out = out.replace(/^### (.*$)/gim, '<h6 class="fw-bold text-body mt-3 mb-1.5" style="font-size: 0.95rem;">$1</h6>');
        out = out.replace(/^## (.*$)/gim, '<h5 class="fw-bold text-body mt-3 mb-2" style="font-size: 1.02rem;">$1</h5>');
        out = out.replace(/^# (.*$)/gim, '<h4 class="fw-bold text-body mt-3 mb-2" style="font-size: 1.1rem;">$1</h4>');

        // Bold & Italic
        out = out.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        out = out.replace(/\*(.*?)\*/g, '<em>$1</em>');

        // Blockquotes
        out = out.replace(/^&gt; (.*$)/gim, '<blockquote class="border-start border-3 border-success ps-3 text-body-secondary my-2">$1</blockquote>');

        // Bullet lists
        out = out.replace(/^\- (.*$)/gim, '<li class="ms-3 mb-1">$1</li>');

        // Code blocks
        out = out.replace(/```([\s\S]*?)```/g, '<pre><code>$1</code></pre>');

        // Inline code
        out = out.replace(/`([^`]+)`/g, '<code>$1</code>');

        // Line breaks
        out = out.replace(/\n/g, '<br>');

        return out;
    }
});
</script>
@endpush
