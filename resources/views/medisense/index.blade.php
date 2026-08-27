@extends('layouts.app')

@section('title', 'MediSense AI — Clinical Decision Support Assistant')
@section('page-title', 'MediSense AI')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('medisense.index') }}">Clinical AI</a></li>
    <li class="breadcrumb-item active">MediSense AI Workspace</li>
@endsection

@push('styles')
<style>
  /* =========================================================
     MEDISENSE AI — Clinical Decision Support Assistant
     UI COLOR SYSTEM: GREEN SHADES + RED ONLY
     ========================================================= */

  .medisense-wrapper {
    --radius: 1.25rem;

    /* Base */
    --background: #ffffff;
    --foreground: #173b2a;

    /* Cards */
    --card: #ffffff;
    --card-foreground: #173b2a;

    /* Popovers */
    --popover: #ffffff;
    --popover-foreground: #173b2a;

    /* Primary Green */
    --primary: #15803d;
    --primary-hover: #166534;
    --primary-light: #dcfce7;
    --primary-foreground: #ffffff;

    /* Secondary Green */
    --secondary: #f0fdf4;
    --secondary-foreground: #166534;

    /* Muted */
    --muted: #f3faf5;
    --muted-foreground: #648071;

    /* Accent */
    --accent: #e8f5ec;
    --accent-hover: #dcfce7;
    --accent-foreground: #14532d;

    /* Clinical Alert Red */
    --destructive: #dc2626;
    --destructive-light: #fef2f2;
    --destructive-foreground: #ffffff;

    /* Borders */
    --border: #d9e8de;
    --input: #d5e5da;
    --ring: #22c55e;

    /* Chat */
    --bubble: #eaf7ee;
    --assistant-bubble: #ffffff;
    --composer: #ffffff;

    /* Shadows */
    --shadow-composer: 0 4px 18px rgba(21, 128, 61, 0.08);
    --shadow-card: 0 2px 12px rgba(21, 128, 61, 0.06);

    display: flex;
    flex-direction: column;
    height: calc(100vh - var(--topbar-height, 60px) - 54px - 3.5rem);
    min-height: 580px;
    min-width: 0;
    overflow: hidden;
    background: var(--background);
    color: var(--foreground);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow-card);
  }

  /* Dark Mode Theme Overrides for MediSense AI */
  html[data-theme="dark"] .medisense-wrapper,
  html[data-bs-theme="dark"] .medisense-wrapper {
    --background: #000000;
    --foreground: #FFFFFF;

    --card: #111111;
    --card-foreground: #FFFFFF;

    --popover: #111111;
    --popover-foreground: #FFFFFF;

    --primary: #15803d;
    --primary-hover: #166534;
    --primary-light: rgba(22, 163, 74, 0.15);
    --primary-foreground: #ffffff;

    --secondary: #171717;
    --secondary-foreground: #14C79A;

    --muted: #171717;
    --muted-foreground: #A3A3A3;

    --accent: #1F1F1F;
    --accent-hover: #262626;
    --accent-foreground: #FFFFFF;

    --border: #262626;
    --input: #262626;
    --ring: #16A34A;

    --bubble: #171717;
    --assistant-bubble: #111111;
    --composer: #111111;

    --shadow-composer: 0 4px 18px rgba(0, 0, 0, 0.6);
    --shadow-card: 0 2px 12px rgba(0, 0, 0, 0.5);
  }

  html[data-theme="dark"] .medisense-header {
    background: #0A0A0A;
  }

  html[data-theme="dark"] .message-user .bubble {
    background: #171717;
    border-color: #262626;
    color: #FFFFFF;
  }

  html[data-theme="dark"] .chat-scroll {
    scrollbar-color: #262626 transparent;
  }

  html[data-theme="dark"] .chat-scroll::-webkit-scrollbar-thumb {
    background: #262626;
  }

  html[data-theme="dark"] .assistant-name {
    color: #14C79A;
  }

  html[data-theme="dark"] .avatar {
    background: rgba(22, 163, 74, 0.15);
    color: #14C79A;
    border-color: rgba(22, 163, 74, 0.3);
  }

  html[data-theme="dark"] .assistant-text th {
    background: #171717;
    color: #FFFFFF;
    border-color: #262626;
  }

  html[data-theme="dark"] .assistant-text td {
    border-color: #262626;
    color: #FFFFFF;
  }

  html[data-theme="dark"] .assistant-text pre {
    background: #050505;
    border-color: #262626;
    color: #22C55E;
  }

  html[data-theme="dark"] .assistant-text code {
    background: rgba(22, 163, 74, 0.15);
    color: #4ADE80;
  }

  html[data-theme="dark"] .clinical-notice {
    background: rgba(220, 38, 38, 0.15);
    border-color: rgba(220, 38, 38, 0.3);
    color: #FCA5A5;
  }

  .medisense-wrapper button,
  .medisense-wrapper textarea {
    font: inherit;
  }

  .medisense-wrapper button {
    -webkit-tap-highlight-color: transparent;
  }

  /* =========================================================
     HEADER
     ========================================================= */

  .medisense-header {
    display: grid;
    grid-template-columns: auto minmax(0, 1fr) auto;
    align-items: center;
    gap: 0.5rem;
    min-height: 56px;
    padding: 0.5rem clamp(0.625rem, 2vw, 1rem);
    border-bottom: 1px solid var(--border);
    background: rgba(255, 255, 255, 0.96);
    flex-shrink: 0;
  }

  .header-brand {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    min-width: 0;
  }

  .brand-icon {
    width: 34px;
    height: 34px;
    display: grid;
    place-items: center;
    flex-shrink: 0;
    border-radius: 10px;
    background: var(--primary);
    color: white;
    box-shadow: 0 3px 10px rgba(21, 128, 61, 0.18);
  }

  .brand-text {
    display: flex;
    flex-direction: column;
    min-width: 0;
  }

  .brand-name {
    font-size: 15px;
    font-weight: 700;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .brand-subtitle {
    font-size: 11px;
    line-height: 1.3;
    color: var(--muted-foreground);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .header-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-shrink: 0;
  }

  .header-btn,
  .icon-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    background: transparent;
    color: var(--muted-foreground);
    border-radius: 0.625rem;
    cursor: pointer;
    transition: background 0.15s ease, color 0.15s ease;
  }

  .header-btn {
    gap: 0.25rem;
    padding: 0.4rem 0.6rem;
    font-size: 14px;
    font-weight: 600;
  }

  .icon-btn {
    width: 36px;
    height: 36px;
  }

  .header-btn:hover,
  .icon-btn:hover {
    background: var(--accent);
    color: var(--primary);
  }

  /* =========================================================
     MAIN AREA
     ========================================================= */

  .main-area {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 0;
    min-width: 0;
    overflow: hidden;
  }

  /* =========================================================
     EMPTY STATE
     ========================================================= */

  .empty-state {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 0;
    padding: 1.5rem clamp(0.75rem, 3vw, 2rem);
    overflow-y: auto;
  }

  .clinical-intro {
    display: flex;
    flex-direction: column;
    align-items: center;
    max-width: 700px;
    text-align: center;
    margin-bottom: 1.5rem;
  }

  .ai-logo {
    width: 58px;
    height: 58px;
    display: grid;
    place-items: center;
    margin-bottom: 1rem;
    border-radius: 18px;
    background: linear-gradient(145deg, #15803d, #22c55e);
    color: #ffffff;
    box-shadow: 0 8px 24px rgba(21, 128, 61, 0.18);
  }

  .empty-state h1 {
    font-size: clamp(1.35rem, 3vw, 1.875rem);
    font-weight: 700;
    line-height: 1.25;
    margin-bottom: 0.5rem;
    color: var(--foreground);
  }

  .empty-description {
    max-width: 580px;
    font-size: 0.9rem;
    line-height: 1.6;
    color: var(--muted-foreground);
  }

  .clinical-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    margin-top: 0.75rem;
    padding: 0.35rem 0.7rem;
    border-radius: 999px;
    background: var(--primary-light);
    color: var(--secondary-foreground);
    font-size: 0.75rem;
    font-weight: 600;
  }

  .clinical-badge-dot {
    width: 7px;
    height: 7px;
    border-radius: 999px;
    background: #22c55e;
  }

  /* =========================================================
     COMPOSER
     ========================================================= */

  .composer-wrapper {
    width: 100%;
    max-width: 870px;
    margin: 0 auto;
    padding: 0 0.75rem 0.75rem;
  }

  @media (min-width: 640px) {
    .composer-wrapper {
      padding: 0 1rem 1rem;
    }
  }

  .composer {
    border-radius: 24px;
    border: 1px solid var(--border);
    background: var(--composer);
    padding: 0.5rem;
    box-shadow: var(--shadow-composer);
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
  }

  .composer:focus-within {
    border-color: #86efac;
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.08), var(--shadow-composer);
  }

  .composer textarea {
    width: 100%;
    max-height: 200px;
    min-height: 44px;
    resize: none;
    border: none;
    outline: none;
    background: transparent;
    color: var(--foreground);
    font-size: 15px;
    line-height: 1.5;
    padding: 0.625rem 0.75rem;
    overflow-y: auto;
  }

  .composer textarea::placeholder {
    color: var(--muted-foreground);
  }

  .composer-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    padding: 0 0.25rem 0.125rem;
    min-width: 0;
  }

  .toolbar-left,
  .toolbar-right {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    min-width: 0;
  }

  .toolbar-left {
    flex: 1;
  }

  .round-icon-btn {
    display: grid;
    place-items: center;
    width: 36px;
    height: 36px;
    flex-shrink: 0;
    border: none;
    background: transparent;
    color: var(--muted-foreground);
    border-radius: 9999px;
    cursor: pointer;
    transition: background 0.15s ease, color 0.15s ease;
  }

  .round-icon-btn:hover {
    background: var(--accent);
    color: var(--primary);
  }

  .tools-btn {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    min-width: 0;
    border: none;
    background: transparent;
    color: var(--muted-foreground);
    border-radius: 9999px;
    padding: 0.375rem 0.75rem;
    font-size: 0.8rem;
    cursor: pointer;
    transition: background 0.15s ease, color 0.15s ease;
  }

  .tools-btn:hover {
    background: var(--accent);
    color: var(--primary);
  }

  .send-btn {
    display: grid;
    place-items: center;
    width: 36px;
    height: 36px;
    flex-shrink: 0;
    border: none;
    background: var(--primary);
    color: var(--primary-foreground);
    border-radius: 9999px;
    cursor: pointer;
    transition: background 0.15s ease, opacity 0.15s ease, transform 0.15s ease;
  }

  .send-btn:hover:not(:disabled) {
    background: var(--primary-hover);
    transform: translateY(-1px);
  }

  .send-btn:disabled {
    opacity: 0.3;
    cursor: default;
    transform: none;
  }

  /* =========================================================
     DISCLAIMER
     ========================================================= */

  .disclaimer {
    text-align: center;
    font-size: 0.7rem;
    line-height: 1.45;
    color: var(--muted-foreground);
    margin-top: 0.5rem;
    padding: 0 0.5rem;
  }

  .disclaimer strong {
    color: var(--destructive);
    font-weight: 600;
  }

  /* =========================================================
     CLINICAL SUGGESTIONS
     ========================================================= */

  .suggestions {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    width: 100%;
    max-width: 870px;
    gap: 0.625rem;
    margin-top: 1rem;
    padding: 0 0.5rem;
  }

  .suggestion-chip {
    display: flex;
    align-items: flex-start;
    gap: 0.6rem;
    min-width: 0;
    border: 1px solid var(--border);
    background: var(--card);
    color: var(--foreground);
    border-radius: 14px;
    padding: 0.75rem;
    font-size: 0.8rem;
    line-height: 1.4;
    text-align: left;
    cursor: pointer;
    box-shadow: var(--shadow-card);
    transition: background 0.15s ease, border-color 0.15s ease, transform 0.15s ease;
  }

  .suggestion-chip:hover {
    background: var(--secondary);
    border-color: #86efac;
    transform: translateY(-1px);
  }

  .suggestion-icon {
    display: grid;
    place-items: center;
    width: 28px;
    height: 28px;
    flex-shrink: 0;
    border-radius: 8px;
    background: var(--primary-light);
    color: var(--primary);
  }

  .suggestion-content {
    min-width: 0;
  }

  .suggestion-title {
    display: block;
    font-weight: 600;
    margin-bottom: 0.15rem;
  }

  .suggestion-description {
    display: block;
    color: var(--muted-foreground);
    font-size: 0.72rem;
  }

  /* =========================================================
     CHAT
     ========================================================= */

  #chat-view {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 0;
    min-width: 0;
  }

  .chat-scroll {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    overflow-x: hidden;
    scrollbar-width: thin;
    scrollbar-color: #b7d7c1 transparent;
  }

  .chat-scroll::-webkit-scrollbar {
    width: 7px;
  }

  .chat-scroll::-webkit-scrollbar-thumb {
    background: #b7d7c1;
    border-radius: 4px;
  }

  .messages {
    width: 100%;
    max-width: 870px;
    margin: 0 auto;
    padding: 1.5rem clamp(0.75rem, 3vw, 1rem);
    display: flex;
    flex-direction: column;
    gap: 1.75rem;
  }

  /* =========================================================
     USER MESSAGE
     ========================================================= */

  .message-user {
    display: flex;
    justify-content: flex-end;
    align-items: flex-start;
    width: 100%;
    min-width: 0;
    animation: bubble-in 0.25s ease-out both;
  }

  .message-user .bubble {
    display: inline-block;
    width: fit-content;
    max-width: min(85%, 650px);
    white-space: pre-wrap;
    overflow-wrap: anywhere;
    word-break: break-word;
    border-radius: 1.25rem;
    background: var(--bubble);
    padding: 0.5rem 0.875rem;
    font-size: 14.5px;
    line-height: 1.55;
    color: var(--foreground);
    border: 1px solid #c8ebd1;
    box-shadow: 0 1px 2px rgba(21, 128, 61, 0.05);
  }

  /* =========================================================
     ASSISTANT MESSAGE
     ========================================================= */

  .message-assistant {
    display: flex;
    gap: 0.75rem;
    min-width: 0;
    animation: bubble-in 0.25s ease-out both;
  }

  .avatar {
    margin-top: 0.25rem;
    width: 30px;
    height: 30px;
    flex-shrink: 0;
    display: grid;
    place-items: center;
    border-radius: 10px;
    background: var(--primary-light);
    color: var(--primary);
    border: 1px solid #bbf7d0;
  }

  .assistant-content {
    min-width: 0;
    flex: 1;
  }

  .assistant-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.25rem;
  }

  .assistant-name {
    font-size: 0.82rem;
    font-weight: 700;
    color: var(--primary);
  }

  .assistant-role {
    font-size: 0.7rem;
    color: var(--muted-foreground);
  }

  .assistant-text {
    white-space: pre-wrap;
    overflow-wrap: anywhere;
    font-size: 15px;
    line-height: 1.7;
    color: var(--foreground);
  }

  .assistant-text p { margin-bottom: 0.75rem; }
  .assistant-text p:last-child { margin-bottom: 0; }
  .assistant-text table { width: 100%; margin: 0.75rem 0; border-collapse: collapse; font-size: 0.85rem; }
  .assistant-text th, .assistant-text td { padding: 0.5rem 0.75rem; border: 1px solid var(--border); }
  .assistant-text th { background: var(--secondary); font-weight: 600; }
  .assistant-text pre { background: var(--muted); border: 1px solid var(--border); padding: 0.75rem; border-radius: 0.5rem; overflow-x: auto; font-family: monospace; font-size: 0.84rem; }
  .assistant-text code { font-family: monospace; font-size: 0.85em; background: var(--primary-light); padding: 0.15rem 0.35rem; border-radius: 0.25rem; }

  /* =========================================================
     CLINICAL NOTICE
     ========================================================= */

  .clinical-notice {
    display: flex;
    gap: 0.625rem;
    margin-top: 0.75rem;
    padding: 0.75rem;
    border: 1px solid #fecaca;
    background: var(--destructive-light);
    border-radius: 10px;
    color: var(--destructive);
    font-size: 0.75rem;
    line-height: 1.5;
  }

  .clinical-notice svg {
    flex-shrink: 0;
    margin-top: 1px;
  }

  /* =========================================================
     ACTIONS
     ========================================================= */

  .actions {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.25rem;
    margin-top: 0.5rem;
    color: var(--muted-foreground);
  }

  .action-btn {
    display: grid;
    place-items: center;
    width: 32px;
    height: 32px;
    flex-shrink: 0;
    border: none;
    background: transparent;
    color: inherit;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: background 0.15s ease, color 0.15s ease;
  }

  .action-btn:hover {
    background: var(--accent);
    color: var(--primary);
  }

  /* =========================================================
     TYPING
     ========================================================= */

  .typing-row {
    display: flex;
    gap: 0.75rem;
    min-width: 0;
  }

  .typing-indicator {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-top: 0.65rem;
  }

  .typing-dot {
    width: 7px;
    height: 7px;
    border-radius: 9999px;
    background: var(--primary);
    animation: pulse 1.3s ease-in-out infinite;
  }

  .typing-dot:nth-child(2) { animation-delay: 0.15s; }
  .typing-dot:nth-child(3) { animation-delay: 0.3s; }

  /* =========================================================
     MOBILE & SMALL SCREENS
     ========================================================= */

  @media (max-width: 768px) {
    .medisense-header {
      min-height: 52px;
      padding: 0 0.75rem;
    }
    .brand-subtitle { display: none; }
    .brand-name { font-size: 0.9rem; }
    .brand-icon { width: 34px; height: 34px; }
    .empty-state {
      padding: 1rem 0.75rem 0.5rem;
      justify-content: flex-start;
    }
    .clinical-intro {
      margin-bottom: 1rem;
    }
    .ai-logo {
      width: 48px;
      height: 48px;
      margin-bottom: 0.5rem;
    }
    .empty-state h1 {
      font-size: 1.25rem;
    }
    .empty-description {
      font-size: 0.82rem;
      max-width: 100%;
    }
    .suggestions {
      grid-template-columns: 1fr;
      max-width: 100%;
      gap: 0.5rem;
      margin-top: 0.75rem;
      padding: 0;
    }
    .suggestion-chip {
      padding: 0.625rem 0.75rem;
      font-size: 0.78rem;
    }
    .messages {
      padding: 0.875rem 0.5rem;
      gap: 1.25rem;
      max-width: 100%;
    }
    .message-user .bubble {
      max-width: 90%;
      font-size: 14px;
      padding: 0.45rem 0.75rem;
    }
    .message-assistant {
      gap: 0.5rem;
    }
    .avatar {
      width: 26px;
      height: 26px;
    }
    .composer-wrapper {
      padding: 0 0.5rem 0.5rem;
      max-width: 100%;
    }
    .composer {
      border-radius: 18px;
      padding: 0.375rem;
    }
    .composer textarea {
      font-size: 15px; /* prevents iOS zoom */
      padding: 0.5rem;
      min-height: 40px;
    }
    .round-icon-btn {
      width: 32px;
      height: 32px;
    }
    .send-btn {
      width: 32px;
      height: 32px;
    }
  }

  @media (max-width: 400px) {
    .brand-name { max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .header-actions .icon-btn { width: 32px; height: 32px; }
    .message-user .bubble { max-width: 95%; }
  }

  /* =========================================================
     ANIMATIONS
     ========================================================= */

  @keyframes bubble-in {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: none; }
  }

  @keyframes pulse {
    0%, 100% { opacity: 0.35; transform: translateY(0); }
    50% { opacity: 1; transform: translateY(-2px); }
  }

  .hidden { display: none !important; }
</style>
@endpush

@section('content')

<div class="medisense-wrapper" id="app">

  <!-- =======================================================
       HEADER
       ======================================================= -->

  <header class="medisense-header">

    <div class="header-brand">

      <div class="brand-icon">
        <svg
          width="19"
          height="19"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
        >
          <path d="M12 2a5 5 0 0 1 4.6 3.05A5 5 0 0 1 20 13.9a5 5 0 0 1-3.4 8.05A5 5 0 0 1 12 22a5 5 0 0 1-4.6-3.05A5 5 0 0 1 4 10.1 5 5 0 0 1 7.4 2.05 5 5 0 0 1 12 2Z"/>
          <path d="M12 6v12"/>
          <path d="M8.5 9h7"/>
          <path d="M8.5 15h7"/>
        </svg>
      </div>

      <div class="brand-text">
        <span class="brand-name">MediSense AI</span>
        <span class="brand-subtitle">
          Clinical Decision Support Assistant &bull; {{ auth()->user()->roleName }}
        </span>
      </div>

    </div>

    <div></div>

    <div class="header-actions">

      <button
        class="icon-btn"
        aria-label="New clinical conversation"
        id="new-chat-btn"
        title="New conversation"
      >
        <svg
          width="20"
          height="20"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
        >
          <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
          <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
        </svg>
      </button>

    </div>

  </header>

  <!-- =======================================================
       MAIN
       ======================================================= -->

  <main class="main-area">

    <!-- =====================================================
         EMPTY STATE
         ===================================================== -->

    <div id="empty-state" class="empty-state">

      <div class="clinical-intro">

        <div class="ai-logo">

          <svg
            width="30"
            height="30"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <path d="M12 2a5 5 0 0 1 4.6 3.05A5 5 0 0 1 20 13.9a5 5 0 0 1-3.4 8.05A5 5 0 0 1 12 22a5 5 0 0 1-4.6-3.05A5 5 0 0 1 4 10.1 5 5 0 0 1 7.4 2.05 5 5 0 0 1 12 2Z"/>
            <path d="M12 6v12"/>
            <path d="M8.5 9h7"/>
            <path d="M8.5 15h7"/>
          </svg>

        </div>

        <h1>How can MediSense AI assist you?</h1>

        <p class="empty-description">
          Get clinical decision-support assistance by reviewing
          patient information, laboratory results, radiology findings,
          medications, treatment considerations, and clinical data.
        </p>

        <div class="clinical-badge">
          <span class="clinical-badge-dot"></span>
          Clinical Decision Support Mode Active
        </div>

      </div>

      <div class="composer-wrapper">

        <div class="composer">

          <textarea
            id="empty-input"
            rows="1"
            placeholder="Ask MediSense AI about a clinical case..."
            aria-label="Clinical question"
          ></textarea>

          <div class="composer-toolbar">

            <div class="toolbar-left">

              <button
                class="round-icon-btn"
                aria-label="Attach patient document"
                title="Attach document"
              >
                <svg
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M5 12h14"/>
                  <path d="M12 5v14"/>
                </svg>
              </button>

              <button class="tools-btn">

                <svg
                  width="16"
                  height="16"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <line x1="4" x2="4" y1="21" y2="14"/>
                  <line x1="4" x2="4" y1="10" y2="3"/>
                  <line x1="12" x2="12" y1="21" y2="12"/>
                  <line x1="12" x2="12" y1="8" y2="3"/>
                  <line x1="20" x2="20" y1="21" y2="16"/>
                  <line x1="20" x2="20" y1="12" y2="3"/>
                  <line x1="2" x2="6" y1="14" y2="14"/>
                  <line x1="10" x2="14" y1="8" y2="8"/>
                  <line x1="18" x2="22" y1="16" y2="16"/>
                </svg>

                <span>Clinical Tools</span>

              </button>

            </div>

            <div class="toolbar-right">

              <button
                class="round-icon-btn"
                aria-label="Voice input"
                title="Voice input"
              >
                <svg
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M12 19v3"/>
                  <path d="M12 2a3 3 0 0 1 3 3v7a3 3 0 0 1-6 0V5a3 3 0 0 1 3-3Z"/>
                  <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                </svg>
              </button>

              <button
                class="send-btn"
                id="empty-send"
                aria-label="Send clinical question"
                disabled
              >
                <svg
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="m5 12 7-7 7 7"/>
                  <path d="M12 19V5"/>
                </svg>
              </button>

            </div>

          </div>

        </div>

        <p class="disclaimer">
          MediSense AI provides clinical decision-support information.
          <strong>It does not replace professional clinical judgment.</strong>
        </p>

      </div>

      <!-- ===================================================
           CLINICAL SUGGESTIONS
           =================================================== -->

      <div class="suggestions" id="suggestions">

        <button
          class="suggestion-chip"
          data-prompt="Analyze the provided patient case and summarize the key clinical findings."
        >

          <span class="suggestion-icon">
            <svg width="16" height="16" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor"
                 stroke-width="2"
                 stroke-linecap="round"
                 stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"/>
              <path d="m21 21-4.3-4.3"/>
            </svg>
          </span>

          <span class="suggestion-content">
            <span class="suggestion-title">
              Analyze Patient Case
            </span>

            <span class="suggestion-description">
              Summarize important clinical findings
            </span>
          </span>

        </button>

        <button
          class="suggestion-chip"
          data-prompt="Explain these laboratory results and identify clinically significant findings."
        >

          <span class="suggestion-icon">
            <svg width="16" height="16" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor"
                 stroke-width="2"
                 stroke-linecap="round"
                 stroke-linejoin="round">
              <path d="M9 3h6"/>
              <path d="M10 9h4"/>
              <path d="M8 21h8"/>
              <path d="M7 3v5l-4 9a3 3 0 0 0 2.8 4h12.4a3 3 0 0 0 2.8-4l-4-9V3"/>
            </svg>
          </span>

          <span class="suggestion-content">
            <span class="suggestion-title">
              Explain Lab Results
            </span>

            <span class="suggestion-description">
              Review laboratory findings and abnormalities
            </span>
          </span>

        </button>

        <button
          class="suggestion-chip"
          data-prompt="Review the patient's medications and identify potential medication-related concerns or interactions."
        >

          <span class="suggestion-icon">
            <svg width="16" height="16" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor"
                 stroke-width="2"
                 stroke-linecap="round"
                 stroke-linejoin="round">
              <path d="m10.5 20.5 9-9a3.5 3.5 0 0 0-5-5l-9 9a3.5 3.5 0 0 0 5 5Z"/>
              <path d="m8 8 8 8"/>
            </svg>
          </span>

          <span class="suggestion-content">
            <span class="suggestion-title">
              Review Medications
            </span>

            <span class="suggestion-description">
              Identify medication-related concerns
            </span>
          </span>

        </button>

        <button
          class="suggestion-chip"
          data-prompt="Summarize the patient's clinical findings, diagnostic information, treatments, and pending requests."
        >

          <span class="suggestion-icon">
            <svg width="16" height="16" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor"
                 stroke-width="2"
                 stroke-linecap="round"
                 stroke-linejoin="round">
              <path d="M4 6h16"/>
              <path d="M4 12h16"/>
              <path d="M4 18h10"/>
            </svg>
          </span>

          <span class="suggestion-content">
            <span class="suggestion-title">
              Clinical Summary
            </span>

            <span class="suggestion-description">
              Summarize the patient's clinical information
            </span>
          </span>

        </button>

      </div>

    </div>

    <!-- =====================================================
         CHAT VIEW
         ===================================================== -->

    <div id="chat-view" class="hidden">

      <div class="chat-scroll" id="chat-scroll">

        <div class="messages" id="messages"></div>

      </div>

      <div class="composer-wrapper">

        <div class="composer">

          <textarea
            id="chat-input"
            rows="1"
            placeholder="Ask MediSense AI about this clinical case..."
            aria-label="Clinical question"
          ></textarea>

          <div class="composer-toolbar">

            <div class="toolbar-left">

              <button
                class="round-icon-btn"
                aria-label="Attach document"
                title="Attach document"
              >
                <svg
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M5 12h14"/>
                  <path d="M12 5v14"/>
                </svg>
              </button>

              <button class="tools-btn">

                <svg
                  width="16"
                  height="16"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <line x1="4" x2="4" y1="21" y2="14"/>
                  <line x1="4" x2="4" y1="10" y2="3"/>
                  <line x1="12" x2="12" y1="21" y2="12"/>
                  <line x1="12" x2="12" y1="8" y2="3"/>
                  <line x1="20" x2="20" y1="21" y2="16"/>
                  <line x1="20" x2="20" y1="12" y2="3"/>
                  <line x1="2" x2="6" y1="16" y2="16"/>
                  <line x1="10" x2="14" y1="8" y2="8"/>
                  <line x1="18" x2="22" y1="12" y2="12"/>
                </svg>

                <span>Clinical Tools</span>

              </button>

            </div>

            <div class="toolbar-right">

              <button
                class="round-icon-btn"
                aria-label="Voice input"
                title="Voice input"
              >
                <svg
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M12 19v3"/>
                  <path d="M12 2a3 3 0 0 1 3 3v7a3 3 0 0 1-6 0V5a3 3 0 0 1 3-3Z"/>
                  <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                </svg>
              </button>

              <button
                class="send-btn"
                id="chat-send"
                aria-label="Send clinical question"
                disabled
              >
                <svg
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="m5 12 7-7 7 7"/>
                  <path d="M12 19V5"/>
                </svg>
              </button>

            </div>

          </div>

        </div>

        <p class="disclaimer">
          MediSense AI is a clinical decision-support assistant.
          <strong>Clinical decisions remain the responsibility of authorized healthcare professionals.</strong>
        </p>

      </div>

    </div>

  </main>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const emptyState = document.getElementById("empty-state");
    const chatView = document.getElementById("chat-view");
    const messagesEl = document.getElementById("messages");
    const chatScroll = document.getElementById("chat-scroll");

    const newChatBtn = document.getElementById("new-chat-btn");
    const emptyInput = document.getElementById("empty-input");
    const emptySend = document.getElementById("empty-send");
    const chatInput = document.getElementById("chat-input");
    const chatSend = document.getElementById("chat-send");

    let messages = [];
    let typing = false;

    // Auto-resize textareas
    function autoResize(textarea) {
        if (!textarea) return;
        textarea.style.height = "auto";
        textarea.style.height = Math.min(textarea.scrollHeight, 200) + "px";
    }

    function checkEnableSend(textarea, sendBtn) {
        if (!textarea || !sendBtn) return;
        sendBtn.disabled = !textarea.value.trim();
    }

    [emptyInput, chatInput].forEach(ta => {
        if (!ta) return;
        const targetBtn = ta === emptyInput ? emptySend : chatSend;
        ta.addEventListener('input', function() {
            autoResize(this);
            checkEnableSend(this, targetBtn);
        });
        ta.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendQuery(this.value);
            }
        });
    });

    [emptySend, chatSend].forEach(btn => {
        if (!btn) return;
        btn.addEventListener('click', function() {
            const ta = btn === emptySend ? emptyInput : chatInput;
            sendQuery(ta.value);
        });
    });

    // Handle suggestion chips
    document.addEventListener('click', function(e) {
        const chip = e.target.closest('.suggestion-chip');
        if (chip && chip.dataset.prompt) {
            sendQuery(chip.dataset.prompt);
        }
    });

    // New Chat / Clear Session
    if (newChatBtn) {
        newChatBtn.addEventListener('click', function() {
            if (messages.length > 0 && !confirm('Start a new MediSense AI conversation session?')) {
                return;
            }
            fetch("{{ route('medisense.clear') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(() => {
                showEmpty();
            });
        });
    }

    function showChat() {
        if (emptyState) {
            emptyState.classList.add("hidden");
            emptyState.classList.add("d-none");
            emptyState.style.setProperty('display', 'none', 'important');
        }
        if (chatView) {
            chatView.classList.remove("hidden");
            chatView.classList.remove("d-none");
            chatView.style.setProperty('display', 'flex', 'important');
        }
    }

    function showEmpty() {
        if (chatView) {
            chatView.classList.add("hidden");
            chatView.classList.add("d-none");
            chatView.style.setProperty('display', 'none', 'important');
        }
        if (emptyState) {
            emptyState.classList.remove("hidden");
            emptyState.classList.remove("d-none");
            emptyState.style.setProperty('display', 'flex', 'important');
        }
        messages = [];
        typing = false;
        messagesEl.innerHTML = "";

        if (emptyInput) { emptyInput.value = ""; autoResize(emptyInput); }
        if (chatInput) { chatInput.value = ""; autoResize(chatInput); }
        if (emptySend) emptySend.disabled = true;
        if (chatSend) chatSend.disabled = true;
    }

    function sendQuery(text) {
        const prompt = text.trim();
        if (!prompt) return;

        messages.push({
            id: 'msg-' + Date.now(),
            role: 'user',
            content: prompt
        });

        typing = true;
        showChat();
        renderMessages();

        if (emptyInput) { emptyInput.value = ""; autoResize(emptyInput); }
        if (chatInput) { chatInput.value = ""; autoResize(chatInput); }
        if (emptySend) emptySend.disabled = true;
        if (chatSend) chatSend.disabled = true;

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
            typing = false;
            if (data.requires_confirm) {
                appendConfirmationPrompt(data);
            } else if (data.success) {
                messages.push({
                    id: 'ai-' + Date.now(),
                    role: 'assistant',
                    content: data.ai_response,
                    capLabel: data.capability_label,
                    sources: data.sources,
                    citations: data.citations
                });
                renderMessages();
            } else {
                appendErrorMessage(data.error || 'An error occurred while processing your request.');
                renderMessages();
            }
        })
        .catch(err => {
            typing = false;
            appendErrorMessage('Network connection error. Please try again.');
            renderMessages();
        });
    }

    function renderMessages() {
        messagesEl.innerHTML = messages.map(msg => {
            if (msg.role === 'user') {
                return `
                    <div class="message-user">
                        <div class="bubble">
                            ${escapeHtml(msg.content)}
                        </div>
                    </div>
                `;
            }

            const formatted = formatMarkdown(msg.content);
            const capBadge = msg.capLabel ? `<span class="badge bg-success-subtle text-success border border-success-subtle ms-1" style="font-size: 0.65rem;">${escapeHtml(msg.capLabel)}</span>` : '';

            // Citations
            let citationsHtml = '';
            if (msg.citations && Array.isArray(msg.citations) && msg.citations.length > 0) {
                citationsHtml = '<div class="mt-2 p-2 bg-body-tertiary rounded border" style="font-size: 0.75rem;">';
                citationsHtml += '<div class="fw-bold mb-1 text-body"><i class="bi bi-globe me-1 text-success"></i> Grounding Citations:</div><ul class="list-unstyled mb-0 ms-1">';
                msg.citations.forEach(cit => {
                    citationsHtml += `<li class="mb-1 text-truncate">
                        <a href="${escapeHtml(cit.url)}" target="_blank" rel="noopener noreferrer" class="text-decoration-none text-body-emphasis fw-medium">
                            ${escapeHtml(cit.title || cit.url)} <i class="bi bi-box-arrow-up-right ms-0.5" style="font-size: 0.65rem;"></i>
                        </a>
                    </li>`;
                });
                citationsHtml += '</ul></div>';
            }

            // Sources
            let sourcesHtml = '';
            if (msg.sources && Array.isArray(msg.sources) && msg.sources.length > 0) {
                sourcesHtml = '<div class="d-flex flex-wrap gap-1 mt-2 pt-2 border-top align-items-center" style="font-size: 0.72rem;">';
                sourcesHtml += '<span class="text-body-secondary me-1">Sources:</span>';
                msg.sources.forEach(src => {
                    sourcesHtml += `<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5">${escapeHtml(src)}</span>`;
                });
                sourcesHtml += '</div>';
            }

            return `
                <div class="message-assistant">
                    <span class="avatar">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2a5 5 0 0 1 4.6 3.05A5 5 0 0 1 20 13.9a5 5 0 0 1-3.4 8.05A5 5 0 0 1 12 22a5 5 0 0 1-4.6-3.05A5 5 0 0 1 4 10.1 5 5 0 0 1 7.4 2.05 5 5 0 0 1 12 2Z"/>
                            <path d="M12 6v12"/>
                            <path d="M8.5 9h7"/>
                            <path d="M8.5 15h7"/>
                        </svg>
                    </span>

                    <div class="assistant-content">
                        <div class="assistant-header">
                            <span class="assistant-name">MediSense AI</span>
                            <span class="assistant-role">Clinical Decision Support ${capBadge}</span>
                        </div>

                        <div class="assistant-text">
                            ${formatted}
                        </div>

                        ${citationsHtml}
                        ${sourcesHtml}

                        <div class="clinical-notice">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10.3 2.9 1.8 17a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 2.9a2 2 0 0 0-3.4 0Z"/>
                                <path d="M12 9v4"/>
                                <path d="M12 17h.01"/>
                            </svg>
                            <span>AI-generated clinical support should be reviewed and validated by an authorized healthcare professional.</span>
                        </div>

                        <div class="actions">
                            <button class="action-btn" aria-label="Copy response" title="Copy" onclick="copyText(this)">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="14" height="14" x="8" y="8" rx="2"/>
                                    <path d="M4 16V4a2 2 0 0 1 2-2h10"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }).join("");

        if (typing) {
            messagesEl.insertAdjacentHTML("beforeend", `
                <div class="typing-row">
                    <span class="avatar">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M12 2a5 5 0 0 1 4.6 3.05A5 5 0 0 1 20 13.9a5 5 0 0 1-3.4 8.05A5 5 0 0 1 12 22a5 5 0 0 1-4.6-3.05A5 5 0 0 1 4 10.1 5 5 0 0 1 7.4 2.05 5 5 0 0 1 12 2Z"/>
                        </svg>
                    </span>
                    <div class="typing-indicator">
                        <span class="typing-dot"></span>
                        <span class="typing-dot"></span>
                        <span class="typing-dot"></span>
                    </div>
                </div>
            `);
        }

        requestAnimationFrame(() => {
            chatScroll.scrollTop = chatScroll.scrollHeight;
        });
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
        messagesEl.insertAdjacentHTML('beforeend', html);
        requestAnimationFrame(() => { chatScroll.scrollTop = chatScroll.scrollHeight; });
    }

    function appendErrorMessage(err) {
        messages.push({
            id: 'err-' + Date.now(),
            role: 'assistant',
            content: `**MediSense Error:** ${err}`
        });
    }

    window.confirmAction = function (patientId, testName) {
        sendQuery(`Create a laboratory request for ${testName} with confirmed=true`);
    };

    window.copyText = function (btn) {
        const textEl = btn.closest('.assistant-content')?.querySelector('.assistant-text');
        if (textEl) {
            navigator.clipboard.writeText(textEl.innerText).then(() => {
                btn.title = "Copied!";
                setTimeout(() => { btn.title = "Copy"; }, 2000);
            });
        }
    };

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatMarkdown(src) {
        if (!src) return '';
        let out = escapeHtml(src);
        out = out.replace(/^### (.*$)/gim, '<h6 class="fw-bold mt-3 mb-1.5">$1</h6>');
        out = out.replace(/^## (.*$)/gim, '<h5 class="fw-bold mt-3 mb-2">$1</h5>');
        out = out.replace(/^# (.*$)/gim, '<h4 class="fw-bold mt-3 mb-2">$1</h4>');
        out = out.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        out = out.replace(/\*(.*?)\*/g, '<em>$1</em>');
        out = out.replace(/^&gt; (.*$)/gim, '<blockquote class="border-start border-3 border-success ps-3 text-muted my-2">$1</blockquote>');
        out = out.replace(/^\- (.*$)/gim, '<li class="ms-3 mb-1">$1</li>');
        out = out.replace(/```([\s\S]*?)```/g, '<pre><code>$1</code></pre>');
        out = out.replace(/`([^`]+)`/g, '<code>$1</code>');
        out = out.replace(/\n/g, '<br>');
        return out;
    }
});
</script>
@endpush
