<?php

namespace App\Events;

use App\Events\Concerns\BroadcastsToUserChat;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserChatDeletedEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels, BroadcastsToUserChat;

    public $userId;
    public $messageId;
    public $deletedAt;

    public function __construct($userId, int $messageId, string $deletedAt)
    {
        $this->userId = (int) $userId;
        $this->messageId = $messageId;
        $this->deletedAt = $deletedAt;
    }

    public function broadcastOn()
    {
        return $this->userChatChannels($this->userId);
    }

    public function broadcastAs()
    {
        return 'user_chat_deleted_event';
    }

    public function broadcastWith(): array
    {
        return [
            'user_id'    => $this->userId,
            'message_id' => $this->messageId,
            'is_deleted' => true,
            'deleted_at' => $this->deletedAt,
        ];
    }
}
