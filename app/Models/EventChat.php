<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'msg',
        'image',
        'user_id',
        'event_user_id',
        'event_id',
        'user_sent',
        'is_read',
    ];

    public function user(){
        return $this->belongsTo(User::class, "user_id");
    }

    public function event_user(){
        return $this->belongsTo(EventUsers::class, "event_user_id");
    }

    public function event(){
        return $this->belongsTo(Events::class, "event_id");
    }
}
