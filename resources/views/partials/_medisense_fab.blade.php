@if (! request()->routeIs('medisense.index'))
{{-- ─── Virtual MediSense AI Floating Action Button & Quick Widget ─── --}}
<div id="medisenseFabContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1085;">
    {{-- Floating Trigger Button --}}
    <button id="medisenseFabBtn" 
            type="button" 
            class="btn rounded-circle shadow-lg d-flex align-items-center justify-content-center text-white border-0 position-relative"
            title="MediSense AI: Clinical Assistant"
            aria-label="Toggle MediSense AI Assistant"
            style="width: 54px; height: 54px; background: linear-gradient(135deg, #0A1F1C 0%, #14C79A 100%); transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);">
        <i class="bi bi-cpu fs-4"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" style="font-size: 0.58rem;">
            AI
        </span>
    </button>

    {{-- Slide-up Quick Chat Drawer --}}
    <div id="medisenseFabWidget" 
         class="card border shadow-lg d-none overflow-hidden bg-body text-body d-flex flex-column" 
         style="transition: opacity 0.25s ease, transform 0.25s ease;">
        
        {{-- Widget Header --}}
        <div class="card-header py-2.5 px-3 border-bottom text-white d-flex align-items-center justify-content-between flex-shrink-0" 
             style="background: linear-gradient(135deg, #0A1F1C 0%, #14352F 100%);">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-cpu text-success fs-5"></i>
                <div>
                    <h6 class="mb-0 fw-bold text-white" style="font-size: 0.85rem; font-family: var(--font-display);">MediSense AI</h6>
                    <small class="text-white-50" style="font-size: 0.65rem;">{{ auth()->user()?->roleName ?? 'Clinical' }} Decision Support</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-1">
                <a href="{{ route('medisense.index') }}" class="btn btn-sm btn-link text-white-50 p-1 hover-text-white" title="Open Full MediSense Workspace">
                    <i class="bi bi-box-arrow-up-right"></i>
                </a>
                <button id="medisenseFabClose" type="button" class="btn-close btn-close-white btn-sm" aria-label="Close" onclick="closeFabWidget()"></button>
            </div>
        </div>

        {{-- Widget Messages Container --}}
        <div id="fabChatMessages" class="card-body p-3 overflow-y-auto bg-body-tertiary flex-grow-1" style="font-size: 0.82rem;">
            <div class="p-2.5 rounded-3 bg-body border shadow-xs mb-3 text-body">
                <div class="fw-semibold mb-1 text-success d-flex align-items-center gap-1" style="font-size: 0.8rem;">
                    <i class="bi bi-robot"></i> Intelligent Assistant Ready
                </div>
                <p class="mb-0 text-body-secondary" style="font-size: 0.78rem;">
                    Ask any medical or clinical workflow question naturally. MediSense automatically determines intent and enforces role-based security.
                </p>
            </div>
        </div>

        {{-- Widget Input Form --}}
        <div class="card-footer p-2 bg-body border-top flex-shrink-0">
            <form id="fabChatForm" class="d-flex align-items-center gap-1.5">
                <input type="text" id="fabInputPrompt" class="form-control form-control-sm border bg-body text-body shadow-none" 
                       placeholder="Ask MediSense AI..." style="font-size: 0.82rem;" required autocomplete="off">
                <button type="submit" id="fabBtnSend" class="btn btn-sm btn-success px-2.5 flex-shrink-0" style="border-radius: 0.4rem;">
                    <i class="bi bi-send-fill"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<style>
#medisenseFabWidget {
    position: fixed !important;
    bottom: 84px !important;
    right: 20px !important;
    width: 390px !important;
    max-width: calc(100vw - 32px) !important;
    height: 550px !important;
    max-height: calc(100vh - 110px) !important;
    z-index: 1090 !important;
    border-radius: 0.85rem !important;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.18), 0 2px 8px rgba(0, 0, 0, 0.08) !important;
    display: flex !important;
    flex-direction: column !important;
}

#medisenseFabWidget.d-none {
    display: none !important;
}

#fabChatMessages {
    flex: 1 1 auto !important;
    min-height: 0 !important;
    overflow-y: auto !important;
}

@media (max-width: 575.98px) {
    #medisenseFabWidget {
        position: fixed !important;
        bottom: 80px !important;
        right: 12px !important;
        left: 12px !important;
        width: auto !important;
        max-width: calc(100vw - 24px) !important;
        height: calc(100vh - 110px) !important;
        max-height: 520px !important;
        z-index: 1090 !important;
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
        if (fabWidget) fabWidget.classList.add('d-none');
        if (fabBtn) fabBtn.style.transform = 'none';
    };

    // Toggle widget
    if (fabBtn && fabWidget) {
        fabBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            if (fabWidget.classList.contains('d-none')) {
                fabWidget.classList.remove('d-none');
                fabBtn.style.transform = 'scale(0.9) rotate(45deg)';
                setTimeout(() => { if (fabInputPrompt) fabInputPrompt.focus(); }, 150);
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

    function closeFabWidget() {
        if (fabWidget) fabWidget.classList.add('d-none');
        if (fabBtn) fabBtn.style.transform = 'none';
    }

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

    function appendFabUser(txt) {
        const html = `
            <div class="d-flex justify-content-end mb-2">
                <div class="p-2 rounded-3 bg-success text-white small" style="max-width: 85%; font-size: 0.78rem;">
                    ${escapeFabHtml(txt)}
                </div>
            </div>
        `;
        fabChatMessages.insertAdjacentHTML('beforeend', html);
        fabChatMessages.scrollTop = fabChatMessages.scrollHeight;
    }

    function appendFabLoading() {
        const id = 'fab-load-' + Date.now();
        const html = `
            <div id="${id}" class="d-flex mb-2">
                <div class="p-2 rounded-3 bg-body border text-body-secondary small d-flex align-items-center gap-2" style="font-size: 0.75rem;">
                    <span class="spinner-border spinner-border-sm text-success" role="status"></span>
                    <span>MediSense thinking...</span>
                </div>
            </div>
        `;
        fabChatMessages.insertAdjacentHTML('beforeend', html);
        fabChatMessages.scrollTop = fabChatMessages.scrollHeight;
        return id;
    }

    function appendFabAi(txt, capLabel, sources, citations) {
        const formatted = formatFabMarkdown(txt);
        const capBadge = capLabel ? `<span class="badge bg-success-subtle text-success ms-1" style="font-size: 0.62rem;">${escapeFabHtml(capLabel)}</span>` : '';

        let badgesHtml = '';
        if (sources && Array.isArray(sources) && sources.length > 0) {
            badgesHtml = '<div class="d-flex flex-wrap gap-1 mt-1.5 pt-1.5 border-top" style="font-size: 0.68rem;">';
            sources.forEach(src => {
                badgesHtml += `<span class="badge bg-body-tertiary text-body border px-1.5 py-0.5">${escapeFabHtml(src)}</span>`;
            });
            badgesHtml += '</div>';
        }

        let citationsHtml = '';
        if (citations && Array.isArray(citations) && citations.length > 0) {
            citationsHtml = '<div class="mt-1.5 p-1.5 bg-body-tertiary rounded border" style="font-size: 0.7rem;">';
            citationsHtml += '<div class="fw-bold mb-1 text-body"><i class="bi bi-globe me-1 text-info"></i> Sources:</div>';
            citations.forEach(cit => {
                citationsHtml += `<div class="text-truncate">
                    <a href="${escapeFabHtml(cit.url)}" target="_blank" rel="noopener noreferrer" class="text-decoration-none text-body-emphasis">
                        ${escapeFabHtml(cit.title || cit.url)}
                    </a>
                </div>`;
            });
            citationsHtml += '</div>';
        }

        const html = `
            <div class="d-flex mb-2">
                <div class="p-2.5 rounded-3 bg-body border text-body shadow-xs" style="max-width: 90%; font-size: 0.78rem; line-height: 1.5;">
                    <div class="fw-bold text-success mb-1 d-flex align-items-center gap-1" style="font-size: 0.72rem;">
                        <i class="bi bi-cpu"></i> MediSense AI ${capBadge}
                    </div>
                    <div>${formatted}</div>
                    ${citationsHtml}
                    ${badgesHtml}
                </div>
            </div>
        `;
        fabChatMessages.insertAdjacentHTML('beforeend', html);
        fabChatMessages.scrollTop = fabChatMessages.scrollHeight;
    }

    function appendFabConfirmationPrompt(data) {
        const details = data.action_details || {};
        const html = `
            <div class="card border-warning-subtle bg-warning-subtle text-warning-emphasis p-2.5 mb-2 rounded-3" style="font-size: 0.76rem;">
                <div class="d-flex align-items-center gap-1.5 mb-1.5 fw-bold text-warning-emphasis">
                    <i class="bi bi-shield-exclamation fs-6"></i>
                    <span>Action Confirmation Required</span>
                </div>
                <p class="mb-1.5 text-body small" style="font-size: 0.75rem;">${escapeFabHtml(data.ai_response || 'Confirmation required.')}</p>
                <div class="p-1.5 bg-body rounded border mb-2 small" style="font-size: 0.72rem;">
                    <div><strong>Action:</strong> ${escapeFabHtml(details.action || 'HIMS Action')}</div>
                    <div><strong>Patient:</strong> ${escapeFabHtml(details.patient || 'N/A')}</div>
                    <div><strong>Details:</strong> ${escapeFabHtml(details.test_name || '')}</div>
                </div>
                <div class="d-flex gap-1.5 flex-wrap">
                    <button type="button" class="btn btn-xs btn-success px-2 py-1 fw-semibold" style="font-size: 0.72rem;" onclick="confirmFabAction('${escapeFabHtml(details.patient_id || '')}', '${escapeFabHtml(details.test_name || '')}')">
                        <i class="bi bi-check-circle me-1"></i> Confirm & Execute
                    </button>
                    <button type="button" class="btn btn-xs btn-outline-secondary px-2 py-1" style="font-size: 0.72rem;" onclick="this.closest('.card').remove()">
                        Cancel
                    </button>
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
            <div class="d-flex mb-2">
                <div class="p-2 rounded-3 bg-danger-subtle text-danger border border-danger-subtle small" style="font-size: 0.75rem;">
                    ${escapeFabHtml(err)}
                </div>
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
