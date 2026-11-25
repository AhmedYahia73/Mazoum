<?php

namespace App\Events;

use App\Events\Concerns\BroadcastsToUserChat;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserChatEditedEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels, BroadcastsToUserChat;

    public $userId;
    public $messageId;
    public $msg;
    public $editedAt;

    public function __construct($userId, int $messageId, string $msg, string $editedAt)
    {
        $this->userId = (int) $userId;
        $this->messageId = $messageId;
        $this->msg = $msg;
        $this->editedAt = $editedAt;
    }

    public function broadcastOn()
    {
        return $this->userChatChannels($this->userId);
    }

    public function broadcastAs()
    {
        return 'user_chat_edited_event';
    }

    public function broadcastWith(): array
    {
        return [
            'user_id'    => $this->userId,
            'message_id' => $this->messageId,
            'msg'        => $this->msg,
            'is_edited'  => true,
            'edited_at'  => $this->editedAt,
        ];
    }
}
