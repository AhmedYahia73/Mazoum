<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MobileCodes extends Model
{
    protected $table = 'mobile_codes';

    public $timestamp = true;

    protected $fillable = [
        'ar_country_name','en_country_name','country_code','code'
    ];

}
