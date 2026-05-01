<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WattsChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'name',
        'message',
        'message_id',
        'is_sent_by_me'
    ];
}
