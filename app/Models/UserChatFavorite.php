<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserChatFavorite extends Model
{
    use HasFactory;

    protected $table = 'user_chat_favorites';

    protected $fillable = [
        'user_chat_id',
        'actor_type',
        'actor_id',
    ];

    /**
     * Parent message.
     */
    public function chatMessage()
    {
        return $this->belongsTo(UserChat::class, 'user_chat_id');
    }
}
