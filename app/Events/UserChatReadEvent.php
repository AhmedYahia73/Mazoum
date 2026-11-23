<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UserChatReadEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $reader;
    public $updatedCount;

    /**
     * Create a new event instance.
     *
     * @param int|string $userId
     * @param string $reader 'user' or 'admin'
     * @param int $updatedCount
     */
    public function __construct($userId, string $reader, int $updatedCount = 0)
    {
        $this->userId = $userId;
        $this->reader = $reader;
        $this->updatedCount = $updatedCount;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [
            new Channel('user-chat.' . $this->userId),
            new Channel('admin-user-chat'),
            new PrivateChannel('user-chat.' . $this->userId),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'user_chat_read_event';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        $data = [
            'user_id'       => (int) $this->userId,
            'reader'        => $this->reader,
            'updated_count' => (int) $this->updatedCount,
            'date'          => now()->format('Y-m-d'),
            'time'          => now()->format('h:i:s A'),
        ];

        Log::info('📦 Broadcasting UserChatReadEvent:', $data);

        return $data;
    }
}
