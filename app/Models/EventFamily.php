<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EventFamily extends Model
{
    protected $table = 'event_family';

    public $timestamp = true;

    protected $fillable = [
        'event_id' , 'name','mobile' , 'scan_qr'
    ];

}
