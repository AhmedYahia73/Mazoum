<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewSetting extends Model
{
    use HasFactory;

    protected $fillable = [ 
        'phone_numer_id', 
        'sender_id',
        'country_id',
        'phone_number',
        'status',
    ];

    public function country()
    {
        return $this->belongsTo('App\Models\Country', 'country_id');
    }
}
