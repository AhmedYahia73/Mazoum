<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CustomChatEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new Channel('chat.' . $this->message->custom_user_id);
    }
    
    public function broadcastAs()
    {
        return "custom_chat_event";
    }

    public function broadcastWith(): array
    {
        $data = [
            'id' => $this->message->id,
            'msg' => $this->message->msg,
            'image' => url("storage/" . $this->message->image),
            'user_sent' => $this->message->user_sent,
            'is_read' => $this->message->is_read,
            "date" => $this->message->created_at->format("Y-m-d"),
            "time" => $this->message->created_at->format("h:i:s A"),
        ];
        
        Log::info('📦 Broadcasting Data:', $data);
        
        return $data;
    }
}
