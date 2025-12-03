<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EventMessages extends Model
{
    protected $table = 'event_messages';

    public $timestamp = true;

    protected $fillable = [
        'event_id' ,'event_user_id', 'name' , 'mobile', 'message' , 'type' , 'message_id'
    ];
  
  
  	public function event()
    {
        return $this->belongsTo('App\Models\Events', 'event_id');
    }

}
