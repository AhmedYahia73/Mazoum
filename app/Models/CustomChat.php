<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'msg',
        'image',
        'user_id',
        'custom_user_id',
        'custom_event_id',
        'user_sent',
        'is_read',
    ];

    public function user(){
        return $this->belongsTo(User::class, "user_id");
    }

    public function event_user(){
        return $this->belongsTo(CustomEventUsers::class, "custom_user_id");
    }

    public function event(){
        return $this->belongsTo(CustomEvent::class, "custom_event_id");
    }
}
