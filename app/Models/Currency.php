<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Currency extends Model
{
    protected $table = 'currency';

    public $timestamp = true;

    protected $fillable = [
        'en_name','ar_name'
    ];

}
