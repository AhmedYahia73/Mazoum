<?php

namespace App\Events\Concerns;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;

trait BroadcastsToUserChat
{
    /**
     * Standard channels for UserChat realtime events.
     *
     * @param int|string $userId
     * @return array
     */
    protected function userChatChannels($userId): array
    {
        return [
            new PrivateChannel('user-chat.' . $userId),
            new Channel('user-chat.' . $userId),
            new Channel('admin-user-chat'),
        ];
    }
}
