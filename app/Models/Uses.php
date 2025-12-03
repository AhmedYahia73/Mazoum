<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Uses extends Model
{
    protected $table = 'uses';

    public $timestamp = true;

    protected $fillable = [
        'en_desc', 'ar_desc', 'link'
    ];


}
