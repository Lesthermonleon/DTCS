@extends('layouts.app')

@section('title', 'Staff Messages')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Staff Messages</li>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h4 class="mb-1 fw-bold text-dark d-flex align-items-center gap-2">
            <i class="bi bi-chat-dots-fill text-primary"></i>Staff Messaging Hub
        </h4>
        <p class="text-muted small mb-0">Secure internal communication between HIMS staff members</p>
    </div>
    <a href="{{ route('messages.create') }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
        <i class="bi bi-pencil-square me-1"></i>+ New Message
    </a>
</div>

{{-- Main Messenger Layout Card --}}
<div class="card border-0 shadow-sm overflow-hidden messenger-layout {{ $activeConversation ? 'mobile-active-chat' : '' }}" id="messengerContainer" style="height: calc(100vh - 210px); min-height: 560px;">
    <div class="row g-0 h-100">
        
        {{-- Left: Conversation Sidebar Panel --}}
        <div class="col-12 col-md-4 col-lg-3 border-end bg-white d-flex flex-column h-100 messenger-sidebar-panel">
            
            {{-- Search Bar Header --}}
            <div class="p-3 border-bottom bg-light-subtle">
                <form action="{{ route('messages.index') }}" method="GET" id="searchForm" onsubmit="return false;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-2.5">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" 
                               id="conversationSearchInput" 
                               name="q" 
                               class="form-control border-start-0 ps-1 shadow-none" 
                               placeholder="Search conversations..." 
                               value="{{ $searchQuery }}"
                               autocomplete="off">
                        @if($searchQuery)
                            <a href="{{ route('messages.index') }}" class="input-group-text bg-white border-start-0 text-muted text-decoration-none">
                                <i class="bi bi-x-circle-fill"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Conversation List --}}
            <div class="list-group list-group-flush overflow-auto flex-grow-1" id="conversationList">
                @forelse($conversations as $conv)
                    @php
                        $isActive = $activeConversation && $activeConversation->id === $conv->id;
                    @endphp
                    <a href="{{ route('messages.index', ['conversation_id' => $conv->id]) }}" 
                       class="list-group-item list-group-item-action p-3 border-0 border-bottom conversation-item {{ $isActive ? 'active-conv-item bg-light-subtle border-start border-4 border-primary' : '' }}"
                       data-conv-id="{{ $conv->id }}"
                       data-user-name="{{ strtolower($conv->other_user_name) }}"
                       data-user-role="{{ strtolower($conv->other_user_role) }}"
                       data-last-msg="{{ strtolower($conv->latest_message?->message ?? '') }}">
                        
                        <div class="d-flex align-items-start gap-2.5">
                            {{-- User Circle Avatar --}}
                            <div class="avatar-circle flex-shrink-0" style="width: 42px; height: 42px; border-radius: 50%; background: var(--signal); color: var(--ink); font-family: var(--font-display); font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; justify-content: center;">
                                {{ strtoupper(substr($conv->other_user_name, 0, 1)) }}
                            </div>

                            <div class="flex-grow-1 min-w-0">
                                {{-- Name & Timestamp --}}
                                <div class="d-flex align-items-center justify-content-between mb-0.5">
                                    <h6 class="mb-0 text-truncate font-body {{ $conv->is_unread ? 'fw-bold text-dark' : 'fw-semibold text-dark' }}" style="font-size: 0.86rem;">
                                        {{ $conv->other_user_name }}
                                    </h6>
                                    <span class="conv-time text-muted flex-shrink-0 ms-1" style="font-size: 0.68rem; font-family: var(--font-mono);">
                                        {{ $conv->time_formatted }}
                                    </span>
                                </div>

                                {{-- Role & Department --}}
                                <div class="text-muted text-truncate mb-1" style="font-size: 0.72rem; line-height: 1.2;">
                                    {{ $conv->other_user_role }}
                                </div>

                                {{-- Message Preview & Unread Badge --}}
                                <div class="d-flex align-items-center justify-content-between gap-1">
                                    <p class="mb-0 text-truncate conv-preview {{ $conv->is_unread ? 'fw-bold text-dark' : 'text-muted' }}" style="font-size: 0.76rem; max-width: 160px;">
                                        {{ $conv->latest_message ? $conv->latest_message->message : 'No messages yet' }}
                                    </p>
                                    @if($conv->unread_count > 0)
                                        <span class="badge bg-danger rounded-pill conv-unread-badge ms-auto" style="font-size: 0.62rem; padding: 3px 6px;">
                                            {{ $conv->unread_count }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-5 px-3" id="noConversationsPlaceholder">
                        <i class="bi bi-chat-square-dots text-muted fs-2 mb-2 d-block opacity-40"></i>
                        <p class="text-muted small mb-0">No active staff conversations found.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Right: Active Chat Main Panel --}}
        <div class="col-12 col-md-8 col-lg-9 d-flex flex-column h-100 bg-white messenger-chat-panel">
            @if($activeConversation)
                @php
                    $otherUser = $activeConversation->participants->firstWhere('id', '!=', auth()->id());
                    $roleName = $otherUser?->roleName ?? 'Staff';
                    $dept = $otherUser?->department;
                    $roleDept = $roleName . ($dept ? ' · ' . $dept : '');
                @endphp

                {{-- Active Chat Header --}}
                <div class="p-3 border-bottom d-flex align-items-center justify-content-between bg-white flex-shrink-0" id="chatHeader">
                    <div class="d-flex align-items-center gap-2.5">
                        {{-- Mobile Back Arrow Button --}}
                        <button type="button" class="btn btn-sm btn-light border-0 rounded-circle me-1 d-md-none" id="mobileBackBtn" aria-label="Back to conversations">
                            <i class="bi bi-arrow-left fs-5"></i>
                        </button>

                        {{-- Active User Avatar --}}
                        <div class="avatar-circle flex-shrink-0" id="chatHeaderAvatar" style="width: 42px; height: 42px; border-radius: 50%; background: var(--signal); color: var(--ink); font-family: var(--font-display); font-weight: 700; font-size: 0.95rem; display: flex; align-items: center; justify-content: center;">
                            {{ strtoupper(substr($otherUser?->name ?? 'S', 0, 1)) }}
                        </div>

                        <div>
                            <h6 class="mb-0 fw-bold text-dark" id="chatHeaderName">{{ $otherUser?->name ?? 'Staff Member' }}</h6>
                            <small class="text-muted d-block" id="chatHeaderRole" style="font-size: 0.74rem;">{{ $roleDept }}</small>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span class="badge pill-signal px-2 py-1 small" style="font-size: 0.68rem; font-weight: 600;">
                            HIMS Verified Staff
                        </span>
                    </div>
                </div>

                {{-- Skeleton Loader for Chat Loading --}}
                <div class="flex-grow-1 p-4 d-none" id="chatSkeleton">
                    <div class="d-flex flex-column gap-3">
                        <div class="skeleton-card p-3 rounded-3 align-self-start" style="width: 50%; height: 60px;"></div>
                        <div class="skeleton-card p-3 rounded-3 align-self-end" style="width: 45%; height: 50px;"></div>
                        <div class="skeleton-card p-3 rounded-3 align-self-start" style="width: 60%; height: 70px;"></div>
                    </div>
                </div>

                {{-- Chat Message Thread Container --}}
                <div class="flex-grow-1 p-3 p-md-4 overflow-auto bg-light-subtle" id="chatMessageArea" style="scroll-behavior: smooth;">
                    @php $lastDateHeader = null; @endphp
                    @forelse($activeConversation->messages as $msg)
                        @php 
                            $isMine = $msg->sender_id === auth()->id();
                            $msgDate = $msg->created_at->format('M d, Y');
                            $dateLabel = $msg->created_at->isToday() 
                                ? 'Today ' . $msg->created_at->format('g:i A') 
                                : ($msg->created_at->isYesterday() 
                                    ? 'Yesterday ' . $msg->created_at->format('g:i A') 
                                    : $msg->created_at->format('M d, Y g:i A'));
                        @endphp

                        @if($lastDateHeader !== $msgDate)
                            <div class="text-center my-3">
                                <span class="badge bg-white text-muted border shadow-2xs font-mono fw-normal" style="font-size: 0.68rem; padding: 4px 10px; border-radius: 12px;">
                                    {{ $dateLabel }}
                                </span>
                            </div>
                            @php $lastDateHeader = $msgDate; @endphp
                        @endif

                        <div class="d-flex {{ $isMine ? 'justify-content-end' : 'justify-content-start' }} mb-2.5 message-row">
                            <div class="message-bubble shadow-2xs p-3 position-relative {{ $isMine ? 'bg-primary text-white bubble-sent' : 'bg-white text-dark border bubble-received' }}" 
                                 style="max-width: 78%; word-break: break-word;">
                                
                                @if(!$isMine)
                                    <div class="fw-bold mb-1" style="font-size: 0.72rem; opacity: 0.85;">{{ $msg->sender->name }}</div>
                                @endif
                                
                                <div class="message-content" style="font-size: 0.88rem; white-space: pre-wrap; line-height: 1.45;">{{ $msg->message }}</div>
                                
                                <div class="text-end mt-1" style="font-size: 0.64rem; opacity: 0.75; font-family: var(--font-mono);">
                                    {{ $msg->created_at->format('g:i A') }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 my-auto text-muted" id="emptyThreadPlaceholder">
                            <i class="bi bi-chat-left-text fs-2 mb-2 d-block opacity-40"></i>
                            <p class="small mb-0">No messages in this conversation yet. Send a message to get started.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Chat Composer Footer --}}
                <div class="p-3 border-top bg-white flex-shrink-0">
                    <form id="sendMessageForm" action="{{ route('messages.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="conversation_id" id="conversationIdInput" value="{{ $activeConversation->id }}">
                        
                        <div class="input-group align-items-end gap-2 bg-light p-2 rounded-4 border focus-within-ring">
                            <textarea name="message" 
                                      id="chatInput" 
                                      class="form-control border-0 bg-transparent shadow-none" 
                                      rows="1" 
                                      placeholder="Type a message..." 
                                      required 
                                      style="resize: none; max-height: 120px; font-size: 0.88rem; line-height: 1.4;"></textarea>

                            <button type="submit" 
                                    id="sendBtn" 
                                    class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" 
                                    style="width: 38px; height: 38px;" 
                                    title="Send Message">
                                <i class="bi bi-send-fill" style="font-size: 0.95rem;"></i>
                            </button>
                        </div>
                        <div class="d-flex align-items-center justify-content-between px-2 mt-1">
                            <small class="text-muted" style="font-size: 0.65rem;">Press <kbd class="px-1 py-0 bg-light text-dark border">Enter</kbd> to send, <kbd class="px-1 py-0 bg-light text-dark border">Shift+Enter</kbd> for new line</small>
                            <small class="text-danger d-none" id="inputErrorText" style="font-size: 0.68rem;"></small>
                        </div>
                    </form>
                </div>
            @else
                {{-- Empty State: No Active Conversation Selected --}}
                <div class="d-flex flex-column align-items-center justify-content-center h-100 text-center p-5 bg-light-subtle" id="noActiveConvState">
                    <div class="p-4 rounded-circle bg-white shadow-sm mb-3">
                        <i class="bi bi-chat-left-dots text-primary fs-1"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Staff Internal Communication</h5>
                    <p class="text-muted small mb-4" style="max-width: 380px;">
                        Select a staff conversation from the left sidebar to view messages, or start a new direct message.
                    </p>
                    <a href="{{ route('messages.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="bi bi-plus-lg me-1"></i>Start New Conversation
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
/* ── Messenger Component CSS Styles ── */
.messenger-layout {
    font-family: var(--font-body);
}

.conversation-item {
    transition: background-color 0.15s ease, border-color 0.15s ease;
    cursor: pointer;
}

.conversation-item:hover {
    background-color: var(--sidebar-hover-bg, rgba(20, 199, 154, 0.06)) !important;
}

.focus-within-ring:focus-within {
    border-color: var(--signal) !important;
    box-shadow: 0 0 0 3px rgba(20, 199, 154, 0.2) !important;
}

.bubble-sent {
    border-radius: 18px 18px 4px 18px !important;
}

.bubble-received {
    border-radius: 18px 18px 18px 4px !important;
}

/* Responsive Mobile Panels Switching */
@media (max-width: 767.98px) {
    .messenger-sidebar-panel {
        display: flex !important;
    }
    .messenger-chat-panel {
        display: none !important;
    }
    .mobile-active-chat .messenger-sidebar-panel {
        display: none !important;
    }
    .mobile-active-chat .messenger-chat-panel {
        display: flex !important;
    }
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messengerContainer = document.getElementById('messengerContainer');
    const conversationList = document.getElementById('conversationList');
    const chatMessageArea = document.getElementById('chatMessageArea');
    const chatInput = document.getElementById('chatInput');
    const sendMessageForm = document.getElementById('sendMessageForm');
    const sendBtn = document.getElementById('sendBtn');
    const mobileBackBtn = document.getElementById('mobileBackBtn');
    const searchInput = document.getElementById('conversationSearchInput');
    const conversationIdInput = document.getElementById('conversationIdInput');
    const inputErrorText = document.getElementById('inputErrorText');
    const chatSkeleton = document.getElementById('chatSkeleton');
    const chatHeaderAvatar = document.getElementById('chatHeaderAvatar');
    const chatHeaderName = document.getElementById('chatHeaderName');
    const chatHeaderRole = document.getElementById('chatHeaderRole');

    // Auto-scroll chat area to bottom
    function scrollToBottom() {
        if (chatMessageArea) {
            chatMessageArea.scrollTop = chatMessageArea.scrollHeight;
        }
    }
    scrollToBottom();

    // Auto-resize textarea input on type
    if (chatInput) {
        chatInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });

        // Handle Enter vs Shift+Enter
        chatInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (sendMessageForm) {
                    sendMessageForm.requestSubmit();
                }
            }
        });
    }

    // Mobile Back Button Click Event (Return to conversation list)
    if (mobileBackBtn) {
        mobileBackBtn.addEventListener('click', function() {
            if (messengerContainer) {
                messengerContainer.classList.remove('mobile-active-chat');
            }
        });
    }

    // Client-side instant Conversation Search Filter
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.trim().toLowerCase();
            const items = conversationList.querySelectorAll('.conversation-item');
            let hasVisible = false;

            items.forEach(function(item) {
                const name = item.getAttribute('data-user-name') || '';
                const role = item.getAttribute('data-user-role') || '';
                const lastMsg = item.getAttribute('data-last-msg') || '';

                if (name.includes(query) || role.includes(query) || lastMsg.includes(query)) {
                    item.style.display = 'block';
                    hasVisible = true;
                } else {
                    item.style.display = 'none';
                }
            });

            const placeholder = document.getElementById('noConversationsPlaceholder');
            if (placeholder) {
                placeholder.style.display = hasVisible ? 'none' : 'block';
            }
        });
    }

    // Handle Clicking a Conversation Item (AJAX Thread Loading)
    if (conversationList) {
        conversationList.addEventListener('click', function(e) {
            const item = e.target.closest('.conversation-item');
            if (!item) return;

            // Allow standard link behavior if modifier key (Ctrl/Cmd) is pressed
            if (e.ctrlKey || e.metaKey || e.shiftKey) return;

            e.preventDefault();
            const convId = item.getAttribute('data-conv-id');
            if (!convId) return;

            // Highlight active item in list
            conversationList.querySelectorAll('.conversation-item').forEach(el => {
                el.classList.remove('active-conv-item', 'bg-light-subtle', 'border-start', 'border-4', 'border-primary');
            });
            item.classList.add('active-conv-item', 'bg-light-subtle', 'border-start', 'border-4', 'border-primary');

            // Toggle mobile view state
            if (messengerContainer) {
                messengerContainer.classList.add('mobile-active-chat');
            }

            // Remove unread badge on item
            const badge = item.querySelector('.conv-unread-badge');
            if (badge) badge.remove();
            const nameHeader = item.querySelector('h6');
            if (nameHeader) {
                nameHeader.classList.remove('fw-bold');
                nameHeader.classList.add('fw-semibold');
            }

            // Show skeleton loader
            if (chatMessageArea) chatMessageArea.classList.add('d-none');
            if (chatSkeleton) chatSkeleton.classList.remove('d-none');

            fetch(`/messages/${convId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Failed to load conversation.');
                return response.json();
            })
            .then(data => {
                if (data.conversation_id) {
                    if (conversationIdInput) conversationIdInput.value = data.conversation_id;

                    // Update header user info
                    if (chatHeaderName) chatHeaderName.textContent = data.other_user.name;
                    if (chatHeaderRole) chatHeaderRole.textContent = data.other_user.role;
                    if (chatHeaderAvatar) chatHeaderAvatar.textContent = data.other_user.initials;

                    // Render messages in chat area
                    renderMessages(data.messages);

                    // Sync topbar message count if function available
                    updateTopbarUnreadBadge(data.unread_count);
                }
            })
            .catch(err => {
                console.error('Error loading conversation:', err);
            })
            .finally(() => {
                if (chatSkeleton) chatSkeleton.classList.add('d-none');
                if (chatMessageArea) chatMessageArea.classList.remove('d-none');
                scrollToBottom();
            });
        });
    }

    // Render messages array into chat message area
    function renderMessages(messages) {
        if (!chatMessageArea) return;
        chatMessageArea.innerHTML = '';

        if (!messages || messages.length === 0) {
            chatMessageArea.innerHTML = `
                <div class="text-center py-5 my-auto text-muted" id="emptyThreadPlaceholder">
                    <i class="bi bi-chat-left-text fs-2 mb-2 d-block opacity-40"></i>
                    <p class="small mb-0">No messages in this conversation yet. Send a message to get started.</p>
                </div>
            `;
            return;
        }

        let lastDateHeader = null;

        messages.forEach(msg => {
            if (lastDateHeader !== msg.full_date) {
                const dateHeader = document.createElement('div');
                dateHeader.className = 'text-center my-3';
                dateHeader.innerHTML = `
                    <span class="badge bg-white text-muted border shadow-2xs font-mono fw-normal" style="font-size: 0.68rem; padding: 4px 10px; border-radius: 12px;">
                        ${msg.date_label}
                    </span>
                `;
                chatMessageArea.appendChild(dateHeader);
                lastDateHeader = msg.full_date;
            }

            const msgRow = document.createElement('div');
            msgRow.className = `d-flex ${msg.is_mine ? 'justify-content-end' : 'justify-content-start'} mb-2.5 message-row`;

            const bubble = document.createElement('div');
            bubble.className = `message-bubble shadow-2xs p-3 position-relative ${msg.is_mine ? 'bg-primary text-white' : 'bg-white text-dark border'}`;
            bubble.style.cssText = `max-width: 78%; border-radius: ${msg.is_mine ? '18px 18px 4px 18px' : '18px 18px 18px 4px'}; word-break: break-word;`;

            if (!msg.is_mine) {
                const senderName = document.createElement('div');
                senderName.className = 'fw-bold mb-1';
                senderName.style.cssText = 'font-size: 0.72rem; opacity: 0.85;';
                senderName.textContent = msg.sender_name;
                bubble.appendChild(senderName);
            }

            const content = document.createElement('div');
            content.className = 'message-content';
            content.style.cssText = 'font-size: 0.88rem; white-space: pre-wrap; line-height: 1.45;';
            content.textContent = msg.message;

            const time = document.createElement('div');
            time.className = 'text-end mt-1';
            time.style.cssText = 'font-size: 0.64rem; opacity: 0.75; font-family: var(--font-mono);';
            time.textContent = msg.created_at;

            bubble.appendChild(content);
            bubble.appendChild(time);
            msgRow.appendChild(bubble);

            chatMessageArea.appendChild(msgRow);
        });
    }

    // Handle AJAX Message Submission
    if (sendMessageForm) {
        sendMessageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const messageText = chatInput ? chatInput.value.trim() : '';

            if (!messageText) {
                if (inputErrorText) {
                    inputErrorText.textContent = 'Please type a message before sending.';
                    inputErrorText.classList.remove('d-none');
                }
                return;
            }

            if (inputErrorText) {
                inputErrorText.classList.add('d-none');
            }

            // Set loading state
            sendBtn.disabled = true;
            sendBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

            const formData = new FormData(sendMessageForm);

            fetch("{{ route('messages.store') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to send message.');
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.message) {
                    // Append new message bubble to chat DOM
                    appendMessageBubble(data.message);
                    
                    // Reset textarea
                    chatInput.value = '';
                    chatInput.style.height = 'auto';

                    // Update left conversation sidebar item preview & time
                    updateSidebarPreview(data.conversation_id, data.message.message, data.message.created_at);
                    
                    // Sync unread badge
                    updateTopbarUnreadBadge(data.unread_count);

                    scrollToBottom();
                }
            })
            .catch(error => {
                console.error('Error sending message:', error);
                if (inputErrorText) {
                    inputErrorText.textContent = 'Error sending message. Please try again.';
                    inputErrorText.classList.remove('d-none');
                }
            })
            .finally(() => {
                sendBtn.disabled = false;
                sendBtn.innerHTML = '<i class="bi bi-send-fill" style="font-size: 0.95rem;"></i>';
                chatInput.focus();
            });
        });
    }

    // Helper: Append new message bubble into chat container
    function appendMessageBubble(msg) {
        if (!chatMessageArea) return;

        const emptyPlaceholder = document.getElementById('emptyThreadPlaceholder');
        if (emptyPlaceholder) {
            emptyPlaceholder.remove();
        }

        const msgRow = document.createElement('div');
        msgRow.className = 'd-flex justify-content-end mb-2.5 message-row';

        const bubble = document.createElement('div');
        bubble.className = 'message-bubble shadow-2xs p-3 position-relative bg-primary text-white';
        bubble.style.cssText = 'max-width: 78%; border-radius: 18px 18px 4px 18px; word-break: break-word;';

        const content = document.createElement('div');
        content.className = 'message-content';
        content.style.cssText = 'font-size: 0.88rem; white-space: pre-wrap; line-height: 1.45;';
        content.textContent = msg.message;

        const time = document.createElement('div');
        time.className = 'text-end mt-1';
        time.style.cssText = 'font-size: 0.64rem; opacity: 0.75; font-family: var(--font-mono);';
        time.textContent = msg.created_at;

        bubble.appendChild(content);
        bubble.appendChild(time);
        msgRow.appendChild(bubble);

        chatMessageArea.appendChild(msgRow);
    }

    // Helper: Update preview & timestamp on left conversation sidebar item
    function updateSidebarPreview(convId, messageText, timeText) {
        const item = conversationList.querySelector(`.conversation-item[data-conv-id="${convId}"]`);
        if (item) {
            const previewEl = item.querySelector('.conv-preview');
            const timeEl = item.querySelector('.conv-time');
            if (previewEl) previewEl.textContent = messageText;
            if (timeEl) timeEl.textContent = timeText;

            // Remove unread badge since user is active sender
            const badge = item.querySelector('.conv-unread-badge');
            if (badge) badge.remove();

            // Move this conversation item to the top of list
            conversationList.prepend(item);
        }
    }

    // Helper: Update topbar unread messages badge dynamically
    function updateTopbarUnreadBadge(count) {
        const badgeEl = document.getElementById('topbarMessageBadge');
        if (badgeEl) {
            if (count > 0) {
                badgeEl.textContent = count;
                badgeEl.classList.remove('d-none');
            } else {
                badgeEl.classList.add('d-none');
            }
        }
    }
});
</script>
@endpush
@endsection

