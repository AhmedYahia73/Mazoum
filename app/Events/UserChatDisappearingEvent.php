<?php

namespace App\Events;

use App\Events\Concerns\BroadcastsToUserChat;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserChatDisappearingEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels, BroadcastsToUserChat;

    public $userId;
    public $disappearingSeconds;

    public function __construct($userId, ?int $disappearingSeconds)
    {
        $this->userId = (int) $userId;
        $this->disappearingSeconds = $disappearingSeconds;
    }

    public function broadcastOn()
    {
        return $this->userChatChannels($this->userId);
    }

    public function broadcastAs()
    {
        return 'user_chat_disappearing_event';
    }

    public function broadcastWith(): array
    {
        return [
            'user_id'              => $this->userId,
            'disappearing_seconds' => $this->disappearingSeconds,
        ];
    }
}
