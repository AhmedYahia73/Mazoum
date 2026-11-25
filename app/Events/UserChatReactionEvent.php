<?php

namespace App\Events;

use App\Events\Concerns\BroadcastsToUserChat;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserChatReactionEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels, BroadcastsToUserChat;

    public $userId;
    public $messageId;
    public $actorType;
    public $actorId;
    public $emoji;
    public $reactions;

    public function __construct($userId, int $messageId, string $actorType, int $actorId, ?string $emoji, array $reactions)
    {
        $this->userId = (int) $userId;
        $this->messageId = $messageId;
        $this->actorType = $actorType;
        $this->actorId = $actorId;
        $this->emoji = $emoji;
        $this->reactions = $reactions;
    }

    public function broadcastOn()
    {
        return $this->userChatChannels($this->userId);
    }

    public function broadcastAs()
    {
        return 'user_chat_reaction_event';
    }

    public function broadcastWith(): array
    {
        return [
            'user_id'    => $this->userId,
            'message_id' => $this->messageId,
            'actor_type' => $this->actorType,
            'actor_id'   => $this->actorId,
            'emoji'      => $this->emoji,
            'reactions'  => $this->reactions,
        ];
    }
}
