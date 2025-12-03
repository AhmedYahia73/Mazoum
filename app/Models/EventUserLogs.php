<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EventUserLogs extends Model
{
    protected $table = 'event_user_logs';

    public $timestamp = true;

    protected $fillable = [
        'log', 'event_id' , 'event_user_id','message_id', 'error_title' , 'error_details' , 'status'
    ];

}
