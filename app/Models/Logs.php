<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Logs extends Model
{
    protected $table = 'logs';

    public $timestamp = true;

    protected $fillable = [
        'log','type','message_id', 'event_id' , 'event_user_id'
    ];

}
