<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UserCallEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $action;
    public $callData;

    /**
     * Create a new call event instance.
     *
     * @param int|string $userId The conversation user_id
     * @param string $action 'incoming' | 'accepted' | 'rejected' | 'ended' | 'busy' | 'cancelled'
     * @param array $callData
     */
    public function __construct($userId, string $action, array $callData = [])
    {
        $this->userId = $userId;
        $this->action = $action;
        $this->callData = $callData;
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
        return 'user_call_event';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        $payload = array_merge([
            'action'     => $this->action,
            'user_id'    => (int) $this->userId,
            'time'       => now()->format('h:i:s A'),
            'date'       => now()->format('Y-m-d'),
            'timestamp'  => time(),
        ], $this->callData);

        Log::info('📞 Broadcasting UserCallEvent:', $payload);

        return $payload;
    }
}
