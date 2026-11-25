<?php

namespace App\Events;

use App\Events\Concerns\BroadcastsToUserChat;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserChatDeliveredEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels, BroadcastsToUserChat;

    public $userId;
    public $messageIds;
    public $deliveredBy;
    public $deliveredAt;

    public function __construct($userId, array $messageIds, string $deliveredBy, string $deliveredAt)
    {
        $this->userId = (int) $userId;
        $this->messageIds = array_values(array_map('intval', $messageIds));
        $this->deliveredBy = $deliveredBy;
        $this->deliveredAt = $deliveredAt;
    }

    public function broadcastOn()
    {
        return $this->userChatChannels($this->userId);
    }

    public function broadcastAs()
    {
        return 'user_chat_delivered_event';
    }

    public function broadcastWith(): array
    {
        return [
            'user_id'      => $this->userId,
            'message_ids'  => $this->messageIds,
            'delivered_by' => $this->deliveredBy,
            'delivered_at' => $this->deliveredAt,
        ];
    }
}
