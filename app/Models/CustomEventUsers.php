<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomEventUsers extends Model
{
    // use SoftDeletes;

    protected $table = 'custom_event_users';

    public $timestamp = true;

    protected $casts = [
        'scan_at' => 'array',
    ];

    protected $fillable = [
        'custom_event_id',
        'uu_id',
        'name' ,
        'mobile',
        'qr' ,
        'scan',
        'scan_at',
        'users_count',
        'scan_count',
        'send_qr',
        'user_id',
        "resend_qr",
    ];

    public function enter()
    {
        return $this->belongsTo('App\Models\EnterUserCustomEvent', 'custom_user_id');
    }

    public function msgs(){
        return $this->hasMany(CustomChat::class, "custom_user_id");
    }


    public function un_read_user_msgs(){
        return $this->hasMany(CustomChat::class, "event_user_id")
        ->where("is_read", false)
        ->where("user_sent", 1);
    }

    public function un_read_vistor_msgs(){
        return $this->hasMany(CustomChat::class, "event_user_id")
        ->where("is_read", false)
        ->where("user_sent", 0);
    }

    public function un_read_msgs(){
        return $this->hasMany(CustomChat::class, "custom_user_id")
        ->where("is_read", false);
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function event()
    {
        return $this->belongsTo('App\Models\CustomEvent', 'custom_event_id');
    }

  	public function getQrAttribute($value)
    {
        return Custom_Image_Path('custom_event_qr_code',$value);
    }


  	public function setScanAtAttribute($value)
    {
        $status = $this->scan_at ? json_decode($this->attributes['scan_at'], true) : [];
        $status[] = $value;
        $this->attributes['scan_at'] = json_encode($status);
    }



}
