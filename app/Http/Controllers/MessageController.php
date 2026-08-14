<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    /**
     * Get recent conversations for topbar message dropdown.
     */
    public function recent(Request $request): JsonResponse
    {
        $user = $request->user();

        $conversations = $user->conversations()
            ->with(['latestMessage.sender', 'participants.roles'])
            ->orderByDesc('last_message_at')
            ->take(5)
            ->get()
            ->map(function ($conv) use ($user) {
                $otherUser = $conv->participants->firstWhere('id', '!=', $user->id);
                $latest = $conv->latestMessage;
                $pivot = $conv->pivot;

                $isUnread = $latest && ($pivot->last_read_at === null || $pivot->last_read_at < $latest->created_at) && $latest->sender_id !== $user->id;

                $roleName = $otherUser?->roleName ?? 'Staff';
                $dept = $otherUser?->department;
                $roleDept = $roleName . ($dept ? ' · ' . $dept : '');

                return [
                    'id'              => $conv->id,
                    'other_user_name' => $otherUser?->name ?? 'Staff User',
                    'other_user_role' => $roleDept,
                    'last_message'    => $latest ? \Illuminate\Support\Str::limit($latest->message, 45) : 'No messages yet',
                    'is_unread'       => $isUnread,
                    'created_at'      => $latest ? $latest->created_at->diffForHumans(null, true) : '',
                ];
            });

        $unreadCount = $this->getGlobalUnreadCount($user);

        return response()->json([
            'conversations' => $conversations,
            'unread_count'  => $unreadCount,
        ]);
    }

    /**
     * View Staff Messaging Hub page.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $searchQuery = trim($request->input('q', ''));

        // Base conversations query for user
        $conversationsQuery = $user->conversations()
            ->with(['latestMessage.sender', 'participants.roles'])
            ->orderByDesc('last_message_at');

        // Apply optional server-side search filter
        if ($searchQuery !== '') {
            $conversationsQuery->where(function ($q) use ($searchQuery, $user) {
                $q->whereHas('participants', function ($pq) use ($searchQuery, $user) {
                    $pq->where('users.id', '!=', $user->id)
                       ->where(function ($uq) use ($searchQuery) {
                           $uq->where('name', 'like', "%{$searchQuery}%")
                              ->orWhere('department', 'like', "%{$searchQuery}%")
                              ->orWhereHas('roles', function ($rq) use ($searchQuery) {
                                  $rq->where('name', 'like', "%{$searchQuery}%");
                              });
                       });
                })->orWhereHas('messages', function ($mq) use ($searchQuery) {
                    $mq->where('message', 'like', "%{$searchQuery}%");
                });
            });
        }

        $rawConversations = $conversationsQuery->get();

        // Format conversations with unread indicators & participant role info
        $conversations = $rawConversations->map(function ($conv) use ($user) {
            $otherUser = $conv->participants->firstWhere('id', '!=', $user->id);
            $latest = $conv->latestMessage;
            $pivot = $conv->pivot;

            $isUnread = $latest && ($pivot->last_read_at === null || $pivot->last_read_at < $latest->created_at) && $latest->sender_id !== $user->id;

            // Unread count specifically for this conversation
            $unreadCount = $conv->messages()
                ->where('sender_id', '!=', $user->id)
                ->when($pivot->last_read_at, function ($q) use ($pivot) {
                    $q->where('created_at', '>', $pivot->last_read_at);
                })
                ->count();

            $roleName = $otherUser?->roleName ?? 'Staff';
            $dept = $otherUser?->department;
            $roleDept = $roleName . ($dept ? ' · ' . $dept : '');

            // Formatted timestamp (e.g., "2m", "1h", "Yesterday", "Aug 10")
            $timeFormatted = '';
            if ($latest) {
                $created = $latest->created_at;
                if ($created->isToday()) {
                    $timeFormatted = $created->format('g:i A');
                } elseif ($created->isYesterday()) {
                    $timeFormatted = 'Yesterday';
                } else {
                    $timeFormatted = $created->format('M d');
                }
            }

            return (object) [
                'id'              => $conv->id,
                'other_user'      => $otherUser,
                'other_user_name' => $otherUser?->name ?? 'Staff Member',
                'other_user_role' => $roleDept,
                'latest_message'  => $latest,
                'is_unread'       => $isUnread,
                'unread_count'    => $unreadCount,
                'time_formatted'  => $timeFormatted,
                'raw_conv'        => $conv,
            ];
        });

        // Active conversation selection
        $activeConversation = null;
        $activeConversationFormatted = null;

        if ($request->filled('conversation_id')) {
            $activeConversation = $user->conversations()
                ->where('conversations.id', $request->conversation_id)
                ->with(['messages.sender', 'participants.roles'])
                ->first();
        } elseif ($conversations->isNotEmpty()) {
            $firstConvId = $conversations->first()->id;
            $activeConversation = $user->conversations()
                ->where('conversations.id', $firstConvId)
                ->with(['messages.sender', 'participants.roles'])
                ->first();
        }

        // Mark active conversation as read
        if ($activeConversation) {
            $activeConversation->participants()->updateExistingPivot($user->id, [
                'last_read_at' => now(),
            ]);

            // Update matching formatted conversation item unread state
            foreach ($conversations as $c) {
                if ($c->id === $activeConversation->id) {
                    $c->is_unread = false;
                    $c->unread_count = 0;
                }
            }
        }

        $globalUnreadCount = $this->getGlobalUnreadCount($user);

        return view('messages.index', compact(
            'conversations',
            'activeConversation',
            'searchQuery',
            'globalUnreadCount'
        ));
    }

    /**
     * View dedicated New Message creation page.
     */
    public function create(Request $request): View
    {
        $user = $request->user();

        // Get active staff users (except current user) for recipient selection
        $staffUsers = User::where('id', '!=', $user->id)
            ->where('is_active', true)
            ->with('roles')
            ->orderBy('name')
            ->get();

        return view('messages.create', compact('staffUsers'));
    }

    /**
     * View a specific conversation thread (JSON API for Messenger interface).
     */
    public function show(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();

        // Enforce participation authorization policy (IDOR Protection)
        if (!$conversation->isParticipant($user->id)) {
            return response()->json(['error' => 'Unauthorized access to conversation.'], 403);
        }

        // Mark as read
        $conversation->participants()->updateExistingPivot($user->id, [
            'last_read_at' => now(),
        ]);

        $conversation->load(['participants.roles', 'messages.sender']);
        $otherUser = $conversation->participants->firstWhere('id', '!=', $user->id);

        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) use ($user) {
                return [
                    'id'          => $msg->id,
                    'sender_id'   => $msg->sender_id,
                    'sender_name' => $msg->sender->name,
                    'message'     => e($msg->message),
                    'is_mine'     => $msg->sender_id === $user->id,
                    'created_at'  => $msg->created_at->format('g:i A'),
                    'full_date'   => $msg->created_at->format('M d, Y'),
                    'date_label'  => $msg->created_at->isToday()
                        ? 'Today ' . $msg->created_at->format('g:i A')
                        : ($msg->created_at->isYesterday()
                            ? 'Yesterday ' . $msg->created_at->format('g:i A')
                            : $msg->created_at->format('M d, Y g:i A')),
                ];
            });

        $roleName = $otherUser?->roleName ?? 'Staff';
        $dept = $otherUser?->department;
        $roleDept = $roleName . ($dept ? ' · ' . $dept : '');

        return response()->json([
            'conversation_id' => $conversation->id,
            'other_user' => [
                'id'       => $otherUser?->id,
                'name'     => $otherUser?->name ?? 'Staff Member',
                'role'     => $roleDept,
                'initials' => strtoupper(substr($otherUser?->name ?? 'S', 0, 1)),
            ],
            'messages'     => $messages,
            'unread_count' => $this->getGlobalUnreadCount($user),
        ]);
    }

    /**
     * Send a message or start a new conversation.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'recipient_id'    => 'nullable|exists:users,id',
            'conversation_id' => 'nullable|exists:conversations,id',
            'message'         => 'required|string|max:2000',
        ]);

        $sender = $request->user();
        $conversation = null;

        if ($request->filled('conversation_id')) {
            $conversation = $sender->conversations()->where('conversations.id', $request->conversation_id)->first();
            if (!$conversation) {
                if ($request->wantsJson()) {
                    return response()->json(['error' => 'Unauthorized access to conversation.'], 403);
                }
                abort(403, 'Unauthorized access to conversation.');
            }
        } elseif ($request->filled('recipient_id')) {
            $recipientId = (int) $request->recipient_id;
            if ($recipientId === $sender->id) {
                if ($request->wantsJson()) {
                    return response()->json(['error' => 'Cannot message yourself.'], 422);
                }
                return back()->withErrors(['recipient_id' => 'Cannot message yourself.']);
            }

            // Find existing 1-on-1 conversation or create new
            $conversation = Conversation::whereHas('participants', function ($q) use ($sender) {
                $q->where('users.id', $sender->id);
            })->whereHas('participants', function ($q) use ($recipientId) {
                $q->where('users.id', $recipientId);
            })->first();

            if (!$conversation) {
                $conversation = Conversation::create([
                    'created_by' => $sender->id,
                    'last_message_at' => now(),
                ]);
                $conversation->participants()->attach([$sender->id, $recipientId]);
            }
        } else {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Specify a conversation or recipient.'], 422);
            }
            return back()->withErrors(['message' => 'Specify a conversation or recipient.']);
        }

        // Create the message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $sender->id,
            'message'         => trim($request->message),
        ]);

        $conversation->update(['last_message_at' => now()]);

        // Update sender's last_read_at
        $conversation->participants()->updateExistingPivot($sender->id, [
            'last_read_at' => now(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success'         => true,
                'conversation_id' => $conversation->id,
                'message'         => [
                    'id'          => $message->id,
                    'sender_id'   => $sender->id,
                    'sender_name' => $sender->name,
                    'message'     => e($message->message),
                    'is_mine'     => true,
                    'created_at'  => $message->created_at->format('g:i A'),
                    'full_date'   => $message->created_at->format('M d, Y'),
                    'date_label'  => 'Today ' . $message->created_at->format('g:i A'),
                ],
                'unread_count'    => $this->getGlobalUnreadCount($sender),
            ]);
        }

        return redirect()->route('messages.index', ['conversation_id' => $conversation->id])
            ->with('success', 'Message sent successfully.');
    }

    /**
     * Calculate global unread conversations count for user.
     */
    private function getGlobalUnreadCount(User $user): int
    {
        return $user->conversations()
            ->whereHas('latestMessage', function ($q) use ($user) {
                $q->where('sender_id', '!=', $user->id);
            })
            ->get()
            ->filter(function ($conv) use ($user) {
                $latest = $conv->latestMessage;
                $pivot = $conv->pivot;
                return $latest && ($pivot->last_read_at === null || $pivot->last_read_at < $latest->created_at);
            })->count();
    }
}

