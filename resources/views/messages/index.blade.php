@extends('layouts.app')

@section('title', 'Internal Messages')

@section('content')
<div class="messenger-wrapper shadow-sm rounded-4 border" style="height: calc(100vh - 160px); min-height: 620px; overflow: hidden; background: var(--bg-primary); border-color: var(--border-color) !important;">
    <div class="app-container {{ $activeConversation ? 'in-chat' : '' }}" id="appContainer">

        <!-- =========================================================
             MESSAGES SIDEBAR (LEFT)
             ========================================================= -->
        <section class="sidebar">
            <div class="sidebar-header">
                <h1>Messages</h1>
                <div class="sidebar-actions">
                    <button class="circle-btn" id="newMessageBtn" title="New Message" data-bs-toggle="modal" data-bs-target="#newStaffMessageModal">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1-4 1 1-4 9.5-9.5z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Search -->
            <div class="search-container">
                <div class="search-input-wrapper">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" id="searchInput" placeholder="Search staff or messages" autocomplete="off" value="{{ $searchQuery }}" />
                </div>
            </div>

            <!-- Chat List -->
            <div class="chat-list" id="chatListContainer">
                @forelse($conversations as $conv)
                    @php
                        $isActive = $activeConversation && $activeConversation->id === $conv->id;
                        $otherName = $conv->other_user_name;
                        $initial = strtoupper(substr($otherName, 0, 1));
                    @endphp
                    <a href="{{ route('messages.index', ['conversation' => $conv->id]) }}"
                       class="chat-item {{ $isActive ? 'active' : '' }} {{ $conv->is_unread ? 'unread' : '' }}"
                       data-chat-id="{{ $conv->id }}"
                       data-user-name="{{ strtolower($otherName) }}"
                       data-user-role="{{ strtolower($conv->other_user_role) }}"
                       data-last-msg="{{ strtolower($conv->latest_message?->message ?? '') }}">
                        
                        <div class="chat-item-avatar">
                            <div class="avatar-fallback">{{ $initial }}</div>
                            <div class="online-indicator"></div>
                        </div>

                        <div class="chat-item-content">
                            <div class="chat-item-name">{{ $otherName }}</div>
                            <div class="chat-item-role">{{ $conv->other_user_role }}</div>
                            <div class="chat-item-snippet">
                                <span>{{ $conv->latest_message ? $conv->latest_message->message : 'No messages yet' }}</span>
                                <span class="chat-item-time">· {{ $conv->time_formatted }}</span>
                            </div>
                        </div>

                        @if($conv->unread_count > 0)
                            <div class="unread-badge"></div>
                        @endif
                    </a>
                @empty
                    <div class="empty-chat-list">
                        <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <h3>No staff conversations</h3>
                        <p>Start a direct conversation with hospital personnel.</p>
                        <button type="button" class="btn btn-sm btn-success rounded-pill mt-3 px-3" data-bs-toggle="modal" data-bs-target="#newStaffMessageModal">
                            + New Message
                        </button>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- =========================================================
             MAIN CHAT WINDOW (CENTER)
             ========================================================= -->
        <main class="chat-window" id="chatWindow">
            @if($activeConversation)
                @php
                    $otherUser = $activeConversation->participants->firstWhere('id', '!=', auth()->id());
                    $roleName = $otherUser?->roleName ?? 'Staff';
                    $dept = $otherUser?->department ?? 'General Healthcare';
                    $roleDept = $roleName . ($dept ? ' · ' . $dept : '');
                    $activeInitial = strtoupper(substr($otherUser?->name ?? 'S', 0, 1));
                @endphp

                <!-- Chat Header -->
                <header class="chat-header">
                    <div class="chat-header-user" id="chatHeaderTrigger" onclick="toggleDetailsSection(event)">
                        <button class="back-button" id="backButton" title="Back to messages" onclick="event.stopPropagation()">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <polyline points="15 18 9 12 15 6"/>
                            </svg>
                        </button>

                        <div class="chat-item-avatar" style="width: 40px; height: 40px;">
                            <div class="avatar-fallback" id="activeHeaderAvatarFallback">{{ $activeInitial }}</div>
                            <div class="online-indicator" id="activeHeaderOnline"></div>
                        </div>

                        <div class="chat-header-info">
                            <h2 id="activeHeaderName">{{ $otherUser?->name ?? 'Staff Member' }}</h2>
                            <span id="activeHeaderStatus">{{ $roleDept }}</span>
                        </div>
                    </div>

                    <div class="chat-header-actions">
                        <button class="action-icon-btn" id="toggleDetailsBtn" title="Conversation Information" onclick="toggleDetailsSection(event)">
                            <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                            </svg>
                        </button>
                    </div>
                </header>

                <!-- Messages Container -->
                <div class="messages-container" id="messagesContainer">
                    <div class="message-date-separator">TODAY</div>
                    @forelse($activeConversation->messages as $msg)
                        @php
                            $isMine = $msg->sender_id === auth()->id();
                        @endphp
                        <div class="message-group {{ $isMine ? 'sent' : 'received' }}">
                            @if(!$isMine)
                                <div class="message-avatar-fallback">{{ strtoupper(substr($msg->sender->name, 0, 1)) }}</div>
                            @endif
                            <div class="message-bubble-wrapper">
                                <div class="bubble">
                                    {{ $msg->message }}
                                </div>
                                <div class="message-time">
                                    {{ $msg->created_at->format('g:i A') }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 my-auto text-muted">
                            <p class="small mb-0">No messages in this conversation yet. Type a message below to start.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Message Footer -->
                <footer class="chat-footer">
                    <form id="sendMessageForm" action="{{ route('messages.store') }}" method="POST" class="d-flex align-items-center gap-2 w-100 mb-0">
                        @csrf
                        <input type="hidden" name="conversation_id" id="conversationIdInput" value="{{ $activeConversation->id }}">
                        
                        <div class="footer-tools">
                            <button type="button" class="action-icon-btn" id="attachBtn" title="Attach file">
                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/>
                                </svg>
                            </button>
                            <input type="file" id="fileInput" hidden accept="image/*,.pdf,.doc,.docx,.txt" />
                        </div>

                        <div class="input-wrapper flex-grow-1">
                            <input type="text" name="message" id="messageInput" placeholder="Type a message..." autocomplete="off" required />
                        </div>

                        <button type="submit" class="send-action-btn" id="sendBtn" title="Send message">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                            </svg>
                        </button>
                    </form>
                </footer>
            @else
                <div class="empty-chat-window d-flex flex-column align-items-center justify-content-center h-100 text-center p-5">
                    <svg width="48" height="48" fill="none" stroke="#198754" stroke-width="1.5" viewBox="0 0 24 24" class="mb-3">
                        <path d="M12 2C6.48 2 2 6.03 2 11C2 13.8 3.42 16.28 5.65 17.8L5 21.5L8.9 19.55C9.88 19.84 10.92 20 12 20C17.52 20 22 15.97 22 11C22 6.03 17.52 2 12 2Z"/>
                    </svg>
                    <h4 class="fw-bold mb-1">Select a Conversation</h4>
                    <p class="text-secondary small mb-4" style="max-width: 360px;">Choose a staff member from the left list or start a new direct message.</p>
                    <button type="button" class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#newStaffMessageModal">
                        + Start New Message
                    </button>
                </div>
            @endif
        </main>

        <!-- =========================================================
             STAFF / CONVERSATION DETAILS (DESKTOP RIGHT SIDEBAR)
             ========================================================= -->
        @if($activeConversation)
            <aside class="details-sidebar" id="detailsSidebar">
                <div class="details-avatar-fallback" id="detailAvatarFallback">{{ $activeInitial }}</div>
                <div class="details-name" id="detailName">{{ $otherUser?->name ?? 'Staff Member' }}</div>
                <div class="details-role" id="detailRole">{{ $roleName }}</div>
                <div class="details-department" id="detailDepartment">{{ $dept }}</div>
                <div class="details-status online" id="detailStatus">Active now</div>

                <!-- Staff Information -->
                <div class="details-section">
                    <div class="details-section-title">Staff Information</div>
                    <div class="details-info-row">
                        <span class="details-info-label">Role</span>
                        <span class="details-info-value" id="infoRole">{{ $roleName }}</span>
                    </div>
                    <div class="details-info-row">
                        <span class="details-info-label">Department</span>
                        <span class="details-info-value" id="infoDepartment">{{ $dept }}</span>
                    </div>
                    <div class="details-info-row">
                        <span class="details-info-label">Status</span>
                        <span class="details-info-value" id="infoStatus">Online</span>
                    </div>
                </div>

                <!-- Conversation information -->
                <div class="details-section">
                    <div class="details-section-title">Conversation</div>
                    <div class="details-info-row">
                        <span class="details-info-label">Messages</span>
                        <span class="details-info-value" id="infoMessageCount">{{ $activeConversation->messages->count() }}</span>
                    </div>
                    <div class="details-info-row">
                        <span class="details-info-label">Last activity</span>
                        <span class="details-info-value" id="infoLastActivity">{{ $activeConversation->latest_message?->created_at->format('g:i A') ?? '—' }}</span>
                    </div>
                </div>
            </aside>
        @endif

    </div>
</div>

<!-- =========================================================
     STAFF & CONVERSATION DETAILS (CARD MODAL)
     ========================================================= -->
@if($activeConversation)
<div class="modal fade" id="conversationDetailsModal" tabindex="-1" aria-labelledby="conversationDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden; background: var(--bg-primary); color: var(--text-primary); border: 1px solid var(--border-color);">
            <div class="modal-header border-0 pb-0 pt-3 px-3 justify-content-end">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body text-center px-4 pb-4 pt-0">
                <!-- Avatar & Header info -->
                <div class="position-relative d-inline-block mb-3">
                    <div class="avatar-fallback mx-auto" id="detailAvatarFallback" style="width: 76px; height: 76px; font-size: 1.7rem; border: 3px solid var(--accent-green-light);">
                        {{ $activeInitial }}
                    </div>
                    <div class="online-indicator" style="width: 14px; height: 14px; right: 2px; bottom: 2px;"></div>
                </div>

                <h5 class="fw-bold mb-1" id="detailName" style="color: var(--text-primary);">{{ $otherUser?->name ?? 'Staff Member' }}</h5>
                <div class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 mb-1" id="detailRole" style="font-size: 0.78rem; font-weight: 600;">
                    {{ $roleName }}
                </div>
                <div class="text-secondary small mb-3" id="detailDepartment">{{ $dept }}</div>

                <div class="d-inline-flex align-items-center gap-1.5 px-3 py-1 bg-success-subtle text-success rounded-pill small fw-semibold mb-4" id="detailStatus">
                    <span class="d-inline-block rounded-circle bg-success" style="width: 7px; height: 7px;"></span> Active now
                </div>

                <!-- Staff Information Card Section -->
                <div class="card border-0 rounded-3 p-3 mb-3 text-start" style="background: var(--bg-secondary); border: 1px solid var(--border-color);">
                    <div class="text-uppercase text-muted fw-bold mb-2.5" style="font-size: 0.68rem; letter-spacing: 0.5px;">Staff Information</div>
                    <div class="d-flex justify-content-between align-items-center mb-2" style="font-size: 0.82rem;">
                        <span class="text-secondary">Role</span>
                        <span class="fw-semibold text-end" id="infoRole" style="color: var(--text-primary);">{{ $roleName }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2" style="font-size: 0.82rem;">
                        <span class="text-secondary">Department</span>
                        <span class="fw-semibold text-end" id="infoDepartment" style="color: var(--text-primary);">{{ $dept }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center" style="font-size: 0.82rem;">
                        <span class="text-secondary">Status</span>
                        <span class="fw-semibold text-success text-end" id="infoStatus">Online</span>
                    </div>
                </div>

                <!-- Conversation Card Section -->
                <div class="card border-0 rounded-3 p-3 text-start" style="background: var(--bg-secondary); border: 1px solid var(--border-color);">
                    <div class="text-uppercase text-muted fw-bold mb-2.5" style="font-size: 0.68rem; letter-spacing: 0.5px;">Conversation Details</div>
                    <div class="d-flex justify-content-between align-items-center mb-2" style="font-size: 0.82rem;">
                        <span class="text-secondary">Messages</span>
                        <span class="fw-semibold" id="infoMessageCount" style="color: var(--text-primary);">{{ $activeConversation->messages->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center" style="font-size: 0.82rem;">
                        <span class="text-secondary">Last Activity</span>
                        <span class="fw-semibold" id="infoLastActivity" style="color: var(--text-primary);">{{ $activeConversation->latest_message?->created_at->format('g:i A') ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Start New Message Modal -->
<div class="modal fade" id="newStaffMessageModal" tabindex="-1" aria-labelledby="newStaffMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; background: var(--bg-primary); color: var(--text-primary); border: 1px solid var(--border-color);">
            <div class="modal-header border-bottom py-3 px-4" style="background: var(--bg-secondary); border-color: var(--border-color) !important;">
                <div>
                    <h6 class="modal-title fw-bold" id="newStaffMessageModalLabel" style="color: var(--text-primary);">Start New Message</h6>
                    <small class="text-muted d-block" style="font-size: 0.75rem;">Select a staff member to compose a direct message</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-3">
                <div class="search-input-wrapper mb-3" style="background: var(--bg-secondary); padding: 8px 12px; border-radius: 20px; border: 1px solid var(--border-color);">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" id="modalStaffSearchInput" placeholder="Search staff by name or role..." autocomplete="off" style="border: none; background: transparent; outline: none; width: 100%; font-size: 14px; color: var(--text-primary);" />
                </div>

                <div class="list-group list-group-flush overflow-auto" id="modalStaffList" style="max-height: 320px;">
                    @forelse($staffUsers as $staff)
                        <form action="{{ route('messages.store') }}" method="POST" class="modal-staff-item-form">
                            @csrf
                            <input type="hidden" name="recipient_id" value="{{ $staff->id }}">
                            
                            <button type="submit" class="list-group-item list-group-item-action p-2.5 border-0 rounded-3 mb-1 d-flex align-items-center justify-content-between modal-staff-row"
                                    data-staff-name="{{ strtolower($staff->name) }}"
                                    data-staff-role="{{ strtolower($staff->roleName ?? '') }}"
                                    data-staff-dept="{{ strtolower($staff->department ?? '') }}">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="avatar-fallback" style="width: 38px; height: 38px; font-size: 0.9rem;">
                                        {{ strtoupper(substr($staff->name, 0, 1)) }}
                                    </div>
                                    <div class="text-start">
                                        <div class="fw-bold small mb-0" style="color: var(--text-primary);">{{ $staff->name }}</div>
                                        <div class="text-muted" style="font-size: 0.72rem;">
                                            {{ $staff->roleName ?? 'Staff' }} {{ $staff->department ? '· ' . $staff->department : '' }}
                                        </div>
                                    </div>
                                </div>
                                <span class="btn btn-xs btn-outline-success rounded-pill px-3 py-1 text-nowrap">
                                    Chat
                                </span>
                            </button>
                        </form>
                    @empty
                        <div class="text-center py-4 text-muted small">
                            No active hospital staff members found.
                        </div>
                    @endforelse
                </div>
            </div>
            
            <div class="modal-footer border-top py-2 px-4 justify-content-end" style="background: var(--bg-secondary); border-color: var(--border-color) !important;">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<style>
/* =========================================================
   MediSense HIMS Color System (From messenger.html)
   ========================================================= */
:root {
    --bg-primary: #ffffff;
    --bg-secondary: #f4f8f5;
    --bg-hover: #f1f6f2;
    --bg-active: #e5f1e8;

    --text-primary: #17231b;
    --text-secondary: #68756d;
    --text-muted: #8a958e;

    --accent-green: #198754;
    --accent-green-dark: #146c43;
    --accent-green-light: #e8f5ed;

    --accent-red: #dc3545;
    --accent-red-light: #fcebed;

    --online-green: #198754;

    --bubble-received: #f0f4f1;
    --bubble-sent: #198754;

    --border-color: #dfe7e2;

    --shadow-soft: 0 1px 3px rgba(20, 40, 25, 0.06);

    --font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
}

/* Dark Mode Color Overrides */
html[data-theme="dark"] .messenger-wrapper,
html[data-bs-theme="dark"] .messenger-wrapper,
html[data-theme="dark"] #conversationDetailsModal,
html[data-bs-theme="dark"] #conversationDetailsModal,
html[data-theme="dark"] #newStaffMessageModal,
html[data-bs-theme="dark"] #newStaffMessageModal {
    --bg-primary: #000000;
    --bg-secondary: #111111;
    --bg-hover: #171717;
    --bg-active: #1F1F1F;

    --text-primary: #FFFFFF;
    --text-secondary: #D4D4D4;
    --text-muted: #A3A3A3;

    --accent-green: #14C79A;
    --accent-green-dark: #16A34A;
    --accent-green-light: rgba(22, 163, 74, 0.15);

    --accent-red: #EF4444;
    --accent-red-light: rgba(239, 68, 68, 0.15);

    --online-green: #14C79A;

    --bubble-received: #171717;
    --bubble-sent: #16A34A;

    --border-color: #262626;
    --shadow-soft: 0 1px 3px rgba(0, 0, 0, 0.5);
}

html[data-theme="dark"] .chat-list::-webkit-scrollbar-thumb,
html[data-bs-theme="dark"] .chat-list::-webkit-scrollbar-thumb,
html[data-theme="dark"] .messages-container::-webkit-scrollbar-thumb,
html[data-bs-theme="dark"] .messages-container::-webkit-scrollbar-thumb {
    background: #262626;
}

html[data-theme="dark"] .modal-content,
html[data-bs-theme="dark"] .modal-content {
    background-color: var(--bg-primary) !important;
    color: var(--text-primary) !important;
    border-color: var(--border-color) !important;
}

html[data-theme="dark"] .modal-staff-row,
html[data-bs-theme="dark"] .modal-staff-row {
    background-color: transparent !important;
    color: var(--text-primary) !important;
}

html[data-theme="dark"] .modal-staff-row:hover,
html[data-bs-theme="dark"] .modal-staff-row:hover {
    background-color: var(--bg-hover) !important;
}

html[data-theme="dark"] .btn-close,
html[data-bs-theme="dark"] .btn-close {
    filter: invert(1) grayscale(100%) brightness(200%);
}

.messenger-wrapper {
    font-family: var(--font-family);
    color: var(--text-primary);
}

.app-container {
    display: flex;
    width: 100%;
    height: 100%;
    overflow: hidden;
}

/* =========================================================
   MESSAGES SIDEBAR
   ========================================================= */
.sidebar {
    width: 360px;
    border-right: 1px solid var(--border-color);
    display: flex;
    flex-direction: column;
    background: var(--bg-primary);
    flex-shrink: 0;
    height: 100%;
}

.sidebar-header {
    padding: 18px 16px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.sidebar-header h1 {
    font-size: 24px;
    font-weight: 800;
    letter-spacing: -0.5px;
    margin: 0;
}

.sidebar-actions {
    display: flex;
    gap: 8px;
}

.circle-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--bg-secondary);
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: var(--text-primary);
    transition: background 0.2s ease, color 0.2s ease;
}

.circle-btn:hover {
    background: var(--bg-active);
    color: var(--accent-green);
}

/* Search */
.search-container {
    padding: 6px 16px 14px;
}

.search-input-wrapper {
    background: var(--bg-secondary);
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 12px;
    border-radius: 20px;
    border: 1px solid transparent;
    transition: border-color 0.15s ease, background 0.15s ease;
}

.search-input-wrapper:focus-within {
    border-color: var(--accent-green);
    background: var(--bg-primary);
}

.search-input-wrapper svg {
    color: var(--text-secondary);
    flex-shrink: 0;
}

.search-input-wrapper input {
    border: none;
    background: transparent;
    outline: none;
    width: 100%;
    font-size: 15px;
    color: var(--text-primary);
}

.search-input-wrapper input::placeholder {
    color: var(--text-muted);
}

/* Chat List */
.chat-list {
    flex: 1;
    overflow-y: auto;
    padding: 0 8px 12px;
}

.chat-list::-webkit-scrollbar {
    width: 6px;
}

.chat-list::-webkit-scrollbar-thumb {
    background: #d7e1da;
    border-radius: 10px;
}

.chat-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 8px;
    border-radius: 10px;
    cursor: pointer;
    transition: background 0.15s ease;
    text-decoration: none !important;
    color: var(--text-primary);
    user-select: none;
    -webkit-tap-highlight-color: transparent;
    touch-action: manipulation;
}

.chat-item * {
    pointer-events: none;
}

.chat-item:hover {
    background: var(--bg-hover);
    color: var(--text-primary);
}

.chat-item.active {
    background: var(--bg-active);
}

.chat-item-avatar {
    position: relative;
    width: 50px;
    height: 50px;
    flex-shrink: 0;
}

.avatar-fallback {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: var(--accent-green);
    color: #ffffff;
    font-weight: 700;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.online-indicator {
    position: absolute;
    bottom: 1px;
    right: 1px;
    width: 12px;
    height: 12px;
    background: var(--online-green);
    border: 2px solid var(--bg-primary);
    border-radius: 50%;
}

.chat-item-content {
    flex: 1;
    min-width: 0;
}

.chat-item-name {
    font-size: 15px;
    font-weight: 600;
    margin-bottom: 3px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.chat-item-role {
    font-size: 11px;
    color: var(--accent-green);
    font-weight: 600;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.chat-item-snippet {
    font-size: 13px;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.chat-item-snippet > span:first-child {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.chat-item-time {
    margin-left: 5px;
    flex-shrink: 0;
}

.chat-item.unread .chat-item-name {
    font-weight: 700;
}

.chat-item.unread .chat-item-snippet {
    color: var(--text-primary);
    font-weight: 600;
}

.unread-badge {
    width: 8px;
    height: 8px;
    background: var(--accent-green);
    border-radius: 50%;
    flex-shrink: 0;
    margin-left: 8px;
}

.empty-chat-list {
    padding: 40px 20px;
    text-align: center;
    color: var(--text-secondary);
}

.empty-chat-list svg {
    margin-bottom: 12px;
    color: var(--text-muted);
}

.empty-chat-list h3 {
    font-size: 15px;
    margin-bottom: 5px;
    color: var(--text-primary);
}

.empty-chat-list p {
    font-size: 13px;
    line-height: 1.5;
}

/* =========================================================
   MAIN CHAT WINDOW
   ========================================================= */
.chat-window {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: var(--bg-primary);
    height: 100%;
    position: relative;
    min-width: 0;
}

.chat-header {
    min-height: 64px;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 16px;
    flex-shrink: 0;
    background: var(--bg-primary);
    position: relative;
    z-index: 100;
    pointer-events: all !important;
    isolation: isolate;
}

.chat-header-user {
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    min-width: 0;
    pointer-events: all !important;
    position: relative;
    z-index: 101;
}

.back-button {
    display: none;
    background: none;
    border: none;
    color: var(--accent-green);
    cursor: pointer;
    padding: 4px;
    margin-right: -4px;
}

.chat-header-info {
    min-width: 0;
}

.chat-header-info h2 {
    font-size: 16px;
    font-weight: 600;
    line-height: 1.2;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.chat-header-info span {
    display: block;
    font-size: 12px;
    color: var(--text-secondary);
    margin-top: 3px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.chat-header-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
}

.action-icon-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: none;
    background: transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--accent-green);
    cursor: pointer !important;
    position: relative;
    z-index: 200;
    pointer-events: all !important;
    -webkit-tap-highlight-color: rgba(0,0,0,0);
    touch-action: manipulation;
    transition: background 0.15s ease, color 0.15s ease;
}

.action-icon-btn svg,
.action-icon-btn path {
    pointer-events: none;
}

.action-icon-btn:hover {
    background: var(--bg-hover);
    color: var(--accent-green-dark);
}

/* =========================================================
   MESSAGES THREAD
   ========================================================= */
.messages-container {
    flex: 1;
    overflow-y: auto;
    padding: 20px 16px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    min-height: 0;
}

.messages-container::-webkit-scrollbar {
    width: 6px;
}

.messages-container::-webkit-scrollbar-thumb {
    background: #d7e1da;
    border-radius: 10px;
}

.message-date-separator {
    text-align: center;
    font-size: 11px;
    font-weight: 700;
    color: var(--text-muted);
    margin: 14px 0 8px;
    letter-spacing: 0.5px;
}

.message-group {
    display: flex;
    align-items: flex-end;
    gap: 8px;
    max-width: min(70%, 680px);
}

.message-group.received {
    align-self: flex-start;
}

.message-group.sent {
    align-self: flex-end;
    flex-direction: row-reverse;
}

.message-avatar-fallback {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: var(--accent-green);
    color: #ffffff;
    font-weight: 700;
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-bottom: 2px;
}

.message-bubble-wrapper {
    display: flex;
    flex-direction: column;
    gap: 3px;
    min-width: 0;
}

.bubble {
    padding: 9px 14px;
    border-radius: 18px;
    font-size: 15px;
    line-height: 1.45;
    word-break: break-word;
    overflow-wrap: anywhere;
    position: relative;
}

.received .bubble {
    background: var(--bubble-received);
    color: var(--text-primary);
    border-bottom-left-radius: 4px;
}

.sent .bubble {
    background: var(--bubble-sent);
    color: #ffffff;
    border-bottom-right-radius: 4px;
}

.message-time {
    font-size: 10px;
    color: var(--text-muted);
    padding: 0 4px;
}

.sent .message-time {
    text-align: right;
}

.bubble-like {
    background: transparent !important;
    font-size: 38px;
    padding: 0 !important;
    line-height: 1;
}

/* =========================================================
   CHAT FOOTER
   ========================================================= */
.chat-footer {
    padding: 10px 16px 14px;
    display: flex;
    align-items: center;
    gap: 8px;
    background: var(--bg-primary);
    border-top: 1px solid var(--border-color);
    flex-shrink: 0;
}

.footer-tools {
    display: flex;
    align-items: center;
    gap: 2px;
    flex-shrink: 0;
}

.input-wrapper {
    flex: 1;
    min-width: 0;
    display: flex;
    align-items: center;
    background: var(--bg-secondary);
    border-radius: 20px;
    padding: 4px 12px;
    gap: 8px;
    border: 1px solid transparent;
    transition: border-color 0.15s ease, background 0.15s ease;
}

.input-wrapper:focus-within {
    background: var(--bg-primary);
    border-color: var(--accent-green);
}

.input-wrapper input {
    flex: 1;
    min-width: 0;
    border: none;
    background: transparent;
    outline: none;
    font-size: 15px;
    padding: 6px 0;
    color: var(--text-primary);
}

.input-wrapper input::placeholder {
    color: var(--text-muted);
}

.emoji-btn {
    color: var(--accent-green);
    cursor: pointer;
    background: none;
    border: none;
    display: flex;
    align-items: center;
    flex-shrink: 0;
}

.send-action-btn {
    color: var(--accent-green);
    cursor: pointer;
    border: none;
    background: transparent;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: transform 0.1s ease, background 0.15s ease;
    flex-shrink: 0;
}

.send-action-btn:hover {
    background: var(--bg-hover);
}

.send-action-btn:active {
    transform: scale(0.9);
}

/* =========================================================
   RIGHT DETAILS SIDEBAR
   ========================================================= */
.details-sidebar {
    width: 320px;
    border-left: 1px solid var(--border-color);
    background: var(--bg-primary);
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 28px 16px;
    overflow-y: auto;
    flex-shrink: 0;
}

.details-avatar-fallback {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--accent-green);
    color: #ffffff;
    font-weight: 700;
    font-size: 1.8rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
    border: 3px solid var(--accent-green-light);
}

.details-name {
    font-size: 17px;
    font-weight: 700;
    margin-bottom: 4px;
    text-align: center;
}

.details-role {
    font-size: 13px;
    color: var(--accent-green);
    font-weight: 600;
    text-align: center;
    margin-bottom: 3px;
}

.details-department {
    font-size: 12px;
    color: var(--text-secondary);
    text-align: center;
    margin-bottom: 4px;
}

.details-status {
    font-size: 12px;
    color: var(--text-secondary);
    margin-bottom: 24px;
    text-align: center;
}

.details-status.online {
    color: var(--accent-green);
}

.details-actions {
    display: flex;
    gap: 14px;
    margin-bottom: 28px;
    width: 100%;
    justify-content: center;
}

.details-action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    border: none;
    background: none;
    cursor: pointer;
    min-width: 55px;
}

.details-action-btn span {
    font-size: 12px;
    color: var(--text-secondary);
}

.details-action-btn:hover span {
    color: var(--accent-green);
}

.details-section {
    width: 100%;
    border-top: 1px solid var(--border-color);
    padding-top: 18px;
    margin-top: 4px;
}

.details-section-title {
    font-size: 12px;
    font-weight: 700;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 12px;
}

.details-info-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 8px 0;
}

.details-info-label {
    font-size: 12px;
    color: var(--text-secondary);
}

.details-info-value {
    font-size: 12px;
    color: var(--text-primary);
    font-weight: 600;
    text-align: right;
    overflow-wrap: anywhere;
}

/* =========================================================
   RESPONSIVE (From messenger.html)
   ========================================================= */
@media (max-width: 1200px) {
    .details-sidebar {
        width: 280px;
    }
    .sidebar {
        width: 330px;
    }
}

@media (max-width: 1024px) {
    .details-sidebar {
        display: none !important;
    }
}

@media (max-width: 768px) {
    .sidebar {
        width: 100%;
        border-right: none;
    }
    .back-button {
        display: flex !important;
    }
    .app-container.in-chat .sidebar {
        display: none !important;
    }
    .app-container:not(.in-chat) .chat-window {
        display: none !important;
    }
    .chat-header {
        padding: 8px 10px;
    }
    .messages-container {
        padding: 16px 10px;
    }
    .chat-footer {
        padding: 8px 10px 10px;
    }
    .message-group {
        max-width: 84%;
    }
}

/* Modal Stacking Context & Z-Index Fix */
.modal-backdrop {
    z-index: 1040 !important;
}
.modal {
    z-index: 1055 !important;
}
.modal-dialog {
    z-index: 1056 !important;
}
</style>

@push('scripts')
<script>
/* ============================================================
   GLOBAL: toggleDetailsSection — must be top-level (not inside
   DOMContentLoaded) so inline onclick attributes work safely.
   ============================================================ */
window.toggleDetailsSection = function(e) {
    if (e) {
        if (typeof e.preventDefault === 'function') e.preventDefault();
        if (typeof e.stopPropagation === 'function') e.stopPropagation();
    }
    const sidebar = document.getElementById('detailsSidebar');
    const isSmallScreen = window.innerWidth <= 1024;
    if (isSmallScreen) {
        const modalEl = document.getElementById('conversationDetailsModal');
        if (modalEl) {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            } else {
                modalEl.classList.add('show');
                modalEl.setAttribute('aria-modal', 'true');
                modalEl.style.display = 'block';
                document.body.classList.add('modal-open');
            }
        }
    } else {
        if (sidebar) {
            const vis = window.getComputedStyle(sidebar).display;
            if (vis === 'none') {
                sidebar.style.setProperty('display', 'flex', 'important');
                sidebar.classList.remove('d-none');
            } else {
                sidebar.style.setProperty('display', 'none', 'important');
            }
        }
    }
};

document.addEventListener('DOMContentLoaded', function() {
    const appContainer = document.getElementById('appContainer');
    const chatListContainer = document.getElementById('chatListContainer');
    const messagesContainer = document.getElementById('messagesContainer');
    const messageInput = document.getElementById('messageInput');
    const sendMessageForm = document.getElementById('sendMessageForm');
    const sendBtn = document.getElementById('sendBtn');
    const likeIcon = document.getElementById('likeIcon');
    const sendIcon = document.getElementById('sendIcon');
    const backButton = document.getElementById('backButton');
    const searchInput = document.getElementById('searchInput');
    const conversationIdInput = document.getElementById('conversationIdInput');

    const activeHeaderAvatarFallback = document.getElementById('activeHeaderAvatarFallback');
    const activeHeaderName = document.getElementById('activeHeaderName');
    const activeHeaderStatus = document.getElementById('activeHeaderStatus');

    const detailsSidebar = document.getElementById('detailsSidebar');
    const toggleDetailsBtn = document.getElementById('toggleDetailsBtn');
    const closeDetailsMobileBtn = document.getElementById('closeDetailsMobileBtn');
    const detailAvatarFallback = document.getElementById('detailAvatarFallback');
    const detailName = document.getElementById('detailName');
    const detailRole = document.getElementById('detailRole');
    const detailDepartment = document.getElementById('detailDepartment');
    const detailStatus = document.getElementById('detailStatus');
    const infoRole = document.getElementById('infoRole');
    const infoDepartment = document.getElementById('infoDepartment');
    const infoStatus = document.getElementById('infoStatus');
    const infoMessageCount = document.getElementById('infoMessageCount');
    const infoLastActivity = document.getElementById('infoLastActivity');

    const attachBtn = document.getElementById('attachBtn');
    const fileInput = document.getElementById('fileInput');
    const emojiBtn = document.getElementById('emojiBtn');

    // Ensure Bootstrap modals are attached to document.body on show to prevent stacking context backdrop trapping
    document.querySelectorAll('.modal').forEach(function(modalEl) {
        modalEl.addEventListener('show.bs.modal', function () {
            if (this.parentElement !== document.body) {
                document.body.appendChild(this);
            }
        });
    });

    // Staff Search Filter inside New Message Modal
    const modalStaffSearchInput = document.getElementById('modalStaffSearchInput');
    const modalStaffList = document.getElementById('modalStaffList');
    if (modalStaffSearchInput && modalStaffList) {
        modalStaffSearchInput.addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase().trim();
            const rows = modalStaffList.querySelectorAll('.modal-staff-row');
            rows.forEach(row => {
                const name = row.getAttribute('data-staff-name') || '';
                const role = row.getAttribute('data-staff-role') || '';
                const dept = row.getAttribute('data-staff-dept') || '';
                if (name.includes(query) || role.includes(query) || dept.includes(query)) {
                    row.style.setProperty('display', 'flex', 'important');
                } else {
                    row.style.setProperty('display', 'none', 'important');
                }
            });
        });
    }

    // Prevent double submissions when clicking a staff member in New Message modal
    document.querySelectorAll('.modal-staff-item-form').forEach(function(form) {
        form.addEventListener('submit', function() {
            const btn = this.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.classList.add('disabled');
            }
        });
    });

    // Auto Scroll to Bottom
    function scrollToBottom() {
        if (messagesContainer) {
            requestAnimationFrame(() => {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            });
        }
    }
    scrollToBottom();

    // Toggle Input State (Like vs Send icon)
    function updateInputState() {
        if (!messageInput || !likeIcon || !sendIcon) return;
        const hasText = messageInput.value.trim().length > 0;
        likeIcon.style.display = hasText ? 'none' : 'block';
        sendIcon.style.display = hasText ? 'block' : 'none';
    }
    updateInputState();

    if (messageInput) {
        messageInput.addEventListener('input', updateInputState);

        // Enter key handling
        messageInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (sendMessageForm) sendMessageForm.requestSubmit();
            }
        });
    }

    // Mobile Back Button
    if (backButton && appContainer) {
        backButton.addEventListener('click', function() {
            appContainer.classList.remove('in-chat');
        });
    }

    // Client-side Search Staff or Messages
    const chatListEl = document.getElementById('chatListContainer');
    const searchEl = document.getElementById('searchInput');
    if (searchEl && chatListEl) {
        searchEl.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            chatListEl.querySelectorAll('.chat-item').forEach(function(item) {
                if (!query) {
                    item.style.display = '';
                    return;
                }
                const name    = (item.getAttribute('data-user-name') || '').toLowerCase();
                const role    = (item.getAttribute('data-user-role') || '').toLowerCase();
                const lastMsg = (item.getAttribute('data-last-msg') || '').toLowerCase();
                const match   = name.includes(query) || role.includes(query) || lastMsg.includes(query);
                item.style.display = match ? '' : 'none';
            });
        });
    }

    // Attachment File Picker
    if (attachBtn && fileInput) {
        attachBtn.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0] && messageInput) {
                messageInput.value += ` [Attachment: ${this.files[0].name}]`;
                messageInput.focus();
                updateInputState();
            }
        });
    }

    // Mute Button Toggle
    if (muteBtn && muteLabel) {
        muteBtn.addEventListener('click', function() {
            const isMuted = muteLabel.textContent.trim() === 'Unmute';
            muteLabel.textContent = isMuted ? 'Mute' : 'Unmute';
        });
    }

    // Helper to update text on all matching IDs (Desktop Sidebar + Mobile Card Modal)
    function updateDetailTexts(id, text) {
        document.querySelectorAll('#' + id).forEach(el => {
            el.textContent = text;
        });
    }

    // Click Conversation Item (AJAX Thread Load with URL pushState & Fallback)
    if (chatListContainer) {
        chatListContainer.addEventListener('click', function(e) {
            const item = e.target.closest('.chat-item');
            if (!item) return;

            // MUST preventDefault synchronously here before any async code
            e.preventDefault();

            const convId = item.getAttribute('data-chat-id');
            if (!convId) return;

            const href = item.getAttribute('href');

            // Update active state
            chatListContainer.querySelectorAll('.chat-item').forEach(el => el.classList.remove('active'));
            item.classList.add('active');
            if (appContainer) appContainer.classList.add('in-chat');

            const unreadBadge = item.querySelector('.unread-badge');
            if (unreadBadge) unreadBadge.remove();

            if (href) window.history.pushState(null, '', href);

            fetch(`/messages/${convId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => {
                if (!res.ok) throw new Error('Network error ' + res.status);
                return res.json();
            })
            .then(data => {
                if (data.conversation_id) {
                    if (conversationIdInput) conversationIdInput.value = data.conversation_id;
                    if (activeHeaderName) activeHeaderName.textContent = data.other_user.name;
                    if (activeHeaderStatus) activeHeaderStatus.textContent = data.other_user.role;
                    if (activeHeaderAvatarFallback) activeHeaderAvatarFallback.textContent = data.other_user.initials;

                    updateDetailTexts('detailAvatarFallback', data.other_user.initials);
                    updateDetailTexts('detailName', data.other_user.name);
                    updateDetailTexts('detailRole', data.other_user.raw_role || 'Staff');
                    updateDetailTexts('detailDepartment', data.other_user.department || 'General Healthcare');

                    updateDetailTexts('infoRole', data.other_user.raw_role || 'Staff');
                    updateDetailTexts('infoDepartment', data.other_user.department || 'General Healthcare');
                    updateDetailTexts('infoMessageCount', data.messages.length);

                    renderMessages(data.messages, data.other_user);
                    updateTopbarUnreadBadge(data.unread_count);
                }
            })
            .catch(err => {
                console.error('AJAX nav error:', err);
                // Fallback to full page load if JSON fails
                if (href) window.location.href = href;
            });
        });
    }

    // Render Messages Thread
    function renderMessages(messages, otherUser) {
        if (!messagesContainer) return;
        messagesContainer.innerHTML = '<div class="message-date-separator">TODAY</div>';

        messages.forEach(msg => {
            const isMine = msg.is_mine;

            const group = document.createElement('div');
            group.className = `message-group ${isMine ? 'sent' : 'received'}`;

            let avatarHtml = '';
            if (!isMine) {
                avatarHtml = `<div class="message-avatar-fallback">${otherUser ? otherUser.initials : 'S'}</div>`;
            }

            group.innerHTML = `
                ${avatarHtml}
                <div class="message-bubble-wrapper">
                    <div class="bubble">
                        ${escapeHtml(msg.message)}
                    </div>
                    <div class="message-time">
                        ${escapeHtml(msg.created_at)}
                    </div>
                </div>
            `;
            messagesContainer.appendChild(group);
        });

        scrollToBottom();
    }

    // AJAX Form Submit
    if (sendMessageForm) {
        sendMessageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            let text = messageInput ? messageInput.value.trim() : '';

            if (!text) return;

            const formData = new FormData(sendMessageForm);
            formData.set('message', text);

            fetch("{{ route('messages.store') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.message) {
                    appendMessage(data.message);
                    messageInput.value = '';
                    updateInputState();
                    scrollToBottom();
                }
            })
            .catch(err => console.error('Error sending message:', err));
        });
    }

    function appendMessage(msg) {
        if (!messagesContainer) return;

        const group = document.createElement('div');
        group.className = 'message-group sent';
        group.innerHTML = `
            <div class="message-bubble-wrapper">
                <div class="bubble">
                    ${escapeHtml(msg.message)}
                </div>
                <div class="message-time">
                    ${escapeHtml(msg.created_at)}
                </div>
            </div>
        `;
        messagesContainer.appendChild(group);
    }

    function escapeHtml(val) {
        const div = document.createElement('div');
        div.textContent = String(val ?? '');
        return div.innerHTML;
    }

    function updateTopbarUnreadBadge(count) {
        const badgeEl = document.getElementById('msgBadge');
        if (badgeEl) {
            if (count > 0) {
                badgeEl.textContent = count > 99 ? '99+' : count;
                badgeEl.classList.remove('d-none');
                badgeEl.style.display = '';
            } else {
                badgeEl.classList.add('d-none');
                badgeEl.style.display = 'none';
            }
        }
    }
});
</script>
@endpush
@endsection
