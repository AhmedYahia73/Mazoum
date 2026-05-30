<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'custom_event_id',
        'custom_user_id',
        'msg',
        'type', //congratulation, apologize
    ];

    public function custom_event(){
        return $this->belongsTo(CustomEvent::class, "custom_event_id");
    }

    public function user(){
        return $this->belongsTo(CustomEventUsers::class, "custom_user_id");
    }
}
