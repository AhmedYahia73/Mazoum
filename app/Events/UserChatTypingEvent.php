<?php

namespace App\Events;

use App\Events\Concerns\BroadcastsToUserChat;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserChatTypingEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels, BroadcastsToUserChat;

    public $userId;
    public $senderType;
    public $senderId;
    public $isTyping;

    public function __construct($userId, string $senderType, int $senderId, bool $isTyping)
    {
        $this->userId = (int) $userId;
        $this->senderType = $senderType;
        $this->senderId = $senderId;
        $this->isTyping = $isTyping;
    }

    public function broadcastOn()
    {
        return $this->userChatChannels($this->userId);
    }

    public function broadcastAs()
    {
        return 'user_chat_typing_event';
    }

    public function broadcastWith(): array
    {
        return [
            'user_id'     => $this->userId,
            'sender_type' => $this->senderType,
            'sender_id'   => $this->senderId,
            'is_typing'   => $this->isTyping,
        ];
    }
}
