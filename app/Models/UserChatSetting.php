<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserChatSetting extends Model
{
    use HasFactory;

    protected $table = 'user_chat_settings';

    protected $fillable = [
        'user_id',
        'disappearing_seconds',
        'updated_by_type',
        'updated_by_id',
    ];

    protected $casts = [
        'disappearing_seconds' => 'integer',
    ];

    /**
     * Conversation owner user.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
