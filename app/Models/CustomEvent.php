<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CustomEvent extends Model
{
    protected $table = 'custom_event';

    public $timestamp = true;

    protected $fillable = [
        'title', 'image', 'code', 'user_id' , 'color' , 'assistant_id' , 'language',
        'address' , 'date' , 'time'
    ];

    public function getImageAttribute($value)
    {
        return Image_Path($value);
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }



}
