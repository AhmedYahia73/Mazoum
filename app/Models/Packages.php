<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Packages extends Model
{
    protected $table = 'packages';

    public $timestamp = true;

    protected $fillable = [
        'en_name','ar_name', 'users_count', 'price' , 'currency_id' , 'image' , 'status'
    ];

    public function currency() {
        return $this->belongsTo('App\Models\Currency','currency_id');
    }
  
  	
  	public function getImageAttribute($value)
    {
        return Image_Path($value);
    }


}
