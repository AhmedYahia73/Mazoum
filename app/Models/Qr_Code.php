<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Qr_Code extends Model
{
    protected $table = 'qr_code';

    public $timestamp = true;

    protected $fillable = [
        'event_user_id', 'event_id', 'qr', 'uu_id', 'counter'
    ];
    
    
    public function event() {
        return $this->belongsTo('App\Models\Events','event_id');
    }
    
    public function event_user() {
        return $this->belongsTo('App\Models\EventUsers','event_user_id');
    }

}
