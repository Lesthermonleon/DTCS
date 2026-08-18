<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SessionReplaced implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $userId;
    public string $timestamp;

    /**
     * Create a new event instance.
     */
    public function __construct(int $userId)
    {
        $this->userId    = $userId;
        $this->timestamp = now()->toIso8601String();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->userId),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'SessionReplaced';
    }

    /**
     * Data to broadcast. NO sensitive user data or session IDs are included.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'userId'    => $this->userId,
            'timestamp' => $this->timestamp,
            'message'   => 'Your account was signed in on another device. You have been logged out for security.',
        ];
    }
}
