<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EventUserActions extends Model
{
    protected $table = 'event_users_actions';

    public $timestamp = true;

    protected $fillable = [
         'event_id', 'event_user_id', 'mobile', 'action' , 'msg' , 'users_count'
    ];

    public function event()
    {
        return $this->belongsTo('App\Models\Events', 'event_id');
    }

    public function event_user()
    {
        return $this->belongsTo('App\Models\EventUsers', 'event_user_id');
    }
}
