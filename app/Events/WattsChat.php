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

class WattsChat implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('watts_msg.' . $this->message->phone);
    } 
    
    public function broadcastAs()
    {
        return "watts_chat";
    }

    public function broadcastWith(): array
    {
        $data = [
            'id' => $this->message->id ?? null,
            'phone' => $this->message->phone ?? null,
            'message' => $this->message->message ?? null,
            "is_sent_by_me" => $this->message->is_sent_by_me ?? null,
            "message_id" => $this->message->message_id ?? null,
            "created_at" => $this->message->created_at->format("Y-m-d") ?? null,
            "created_at" => $this->message->created_at->format("h:i:s A") ?? null,
        ];
        
        Log::info('📦 Broadcasting Data:', $data);
        
        return $data;
    }
}
