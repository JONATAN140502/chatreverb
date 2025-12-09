<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message->load('user');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        if ($this->message->type === 'general') {
            return [new Channel('chat.general')];
        } else {
            // Para chat privado, crear canal privado para ambos usuarios
            $userId1 = min($this->message->user_id, $this->message->receiver_id);
            $userId2 = max($this->message->user_id, $this->message->receiver_id);
            return [
                new PrivateChannel('chat.private.' . $userId1 . '.' . $userId2)
            ];
        }
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }
    
    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'user_id' => $this->message->user_id,
                'receiver_id' => $this->message->receiver_id,
                'type' => $this->message->type,
                'message' => $this->message->message,
                'created_at' => $this->message->created_at->toISOString(),
                'updated_at' => $this->message->updated_at->toISOString(),
                'user' => $this->message->user ? [
                    'id' => $this->message->user->id,
                    'name' => $this->message->user->name,
                    'email' => $this->message->user->email,
                ] : null,
            ]
        ];
    }
}
