<?php

namespace App\Events;

use App\Events\Concerns\BroadcastsToUserChat;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserChatPinnedEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels, BroadcastsToUserChat;

    public $userId;
    public $messageId;
    public $isPinned;
    public $pinnedAt;

    public function __construct($userId, int $messageId, bool $isPinned, ?string $pinnedAt)
    {
        $this->userId = (int) $userId;
        $this->messageId = $messageId;
        $this->isPinned = $isPinned;
        $this->pinnedAt = $pinnedAt;
    }

    public function broadcastOn()
    {
        return $this->userChatChannels($this->userId);
    }

    public function broadcastAs()
    {
        return 'user_chat_pinned_event';
    }

    public function broadcastWith(): array
    {
        return [
            'user_id'    => $this->userId,
            'message_id' => $this->messageId,
            'is_pinned'  => $this->isPinned,
            'pinned_at'  => $this->pinnedAt,
        ];
    }
}
