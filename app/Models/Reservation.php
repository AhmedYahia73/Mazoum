<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Reservation extends Model
{
    protected $table = 'reservation';

    public $timestamp = true;

    protected $fillable = [
        'event_name', 'event_date', 'event_address', 'events_count',
        'package_price', 'mobile', 'gender', 'image', 'employees_count', 'status',
        'employee_name','office_name'
    ];

    public function getImageAttribute($value)
    {
        return Image_Path($value);
    }

}
