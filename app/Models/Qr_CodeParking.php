<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Qr_CodeParking extends Model
{
    protected $table = 'qr_code_parking';

    public $timestamp = true;

    protected $fillable = [
        'parking_id', 'uu_id', 'qr', 'counter'
    ];


    public function parking()
    {
        return $this->belongsTo('App\Models\Parking', 'parking_id');
    }

}
