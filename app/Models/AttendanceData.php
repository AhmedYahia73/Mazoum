<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceData extends Model
{
    protected $table = 'attendance_data';

    public $timestamps = true;

    protected $fillable = [
        'router_ip',
        'locations',
    ];

    protected $casts = [
        'locations' => 'array',
    ];
}
