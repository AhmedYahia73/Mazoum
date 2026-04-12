<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnterUserEvent extends Model
{
    use HasFactory;

    protected $fillable = [ 
        'event_user_id',
        'count',
    ];
}
