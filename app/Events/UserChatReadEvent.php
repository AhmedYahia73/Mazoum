<?php

namespace App\Events;

use App\Events\Concerns\BroadcastsToUserChat;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UserChatReadEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels, BroadcastsToUserChat;

    public $userId;
    public $reader;
    public $updatedCount;
    public $readAt;

    public function __construct($userId, string $reader, int $updatedCount = 0, ?string $readAt = null)
    {
        $this->userId = (int) $userId;
        $this->reader = $reader;
        $this->updatedCount = $updatedCount;
        $this->readAt = $readAt ?? now()->toIso8601String();
    }

    public function broadcastOn()
    {
        return $this->userChatChannels($this->userId);
    }

    public function broadcastAs()
    {
        return 'user_chat_read_event';
    }

    public function broadcastWith(): array
    {
        $data = [
            'user_id'       => $this->userId,
            'reader'        => $this->reader,
            'updated_count' => (int) $this->updatedCount,
            'read_at'       => $this->readAt,
            'date'          => now()->format('Y-m-d'),
            'time'          => now()->format('h:i:s A'),
        ];

        Log::info('📦 Broadcasting UserChatReadEvent:', $data);

        return $data;
    }
}
