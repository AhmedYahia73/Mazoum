<?php

namespace App\Events;

use App\Models\UserChat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UserChatEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     *
     * @param UserChat $message
     */
    public function __construct(UserChat $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [
            new Channel('user-chat.' . $this->message->user_id),
            new Channel('admin-user-chat'),
            new PrivateChannel('user-chat.' . $this->message->user_id),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'user_chat_event';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        $data = [
            'id'            => $this->message->id,
            'user_id'       => $this->message->user_id,
            'admin_id'      => $this->message->admin_id,
            'msg'           => $this->message->msg,
            'image'         => $this->message->image_url,
            'voice'         => $this->message->voice_url,
            'is_admin'      => (bool) $this->message->is_admin,
            'is_admin_read' => (bool) $this->message->is_admin_read,
            'is_user_read'  => (bool) $this->message->is_user_read,
            'date'          => $this->message->date,
            'time'          => $this->message->time,
            'created_at'    => $this->message->created_at ? $this->message->created_at->toIso8601String() : null,
        ];

        Log::info('📦 Broadcasting UserChatEvent:', $data);

        return $data;
    }
}
