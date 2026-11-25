<?php

namespace App\Events;

use App\Events\Concerns\BroadcastsToUserChat;
use App\Models\UserChat;
use App\Services\UserChat\UserChatFormatter;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UserChatEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels, BroadcastsToUserChat;

    public $message;

    public function __construct(UserChat $message)
    {
        $this->message = $message->loadMissing(['replyTo', 'reactions']);
    }

    public function broadcastOn()
    {
        return $this->userChatChannels($this->message->user_id);
    }

    public function broadcastAs()
    {
        return 'user_chat_event';
    }

    public function broadcastWith(): array
    {
        $data = UserChatFormatter::formatMessage($this->message);
        Log::info('📦 Broadcasting UserChatEvent:', $data);

        return $data;
    }
}
