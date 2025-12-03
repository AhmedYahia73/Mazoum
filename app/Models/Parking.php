<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Parking extends Model
{
    protected $table = 'parking';

    public $timestamp = true;

    protected $fillable = [
        'uu_id', 'user_name','mobile_code','mobile','phone','car_code', 'car_type', 'car_number', 'location',
        'entry_time', 'out_time', 'serial_number',
        'status' , 'message_id', 'parking_status'
    ];

}
