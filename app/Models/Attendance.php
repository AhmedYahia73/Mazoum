<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendance';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'from',
        'to',
        'image',
        'by_admin',
        'second_image'
    ];

    protected $casts = [
        'by_admin' => 'boolean',
    ];

    protected $appends = ['from_date', 'from_time', 'to_date', 'to_time', 'image_url', 'second_image_url'];

    public function getImageUrlAttribute()
    {
        if(isset($this->attributes['image'])){
            return url("storage/" . $this->attributes['image']);
        }
    } 
    
    public function getSecondImageUrlAttribute()
    {
        if(isset($this->attributes['image'])){
            return url("storage/" . $this->attributes['image']);
        }
    } 
    
    public function getFromDateAttribute()
    {
        return $this->from ? \Carbon\Carbon::parse($this->from)->format('Y-m-d') : null;
    }

    public function getFromTimeAttribute()
    {
        return $this->from ? \Carbon\Carbon::parse($this->from)->format('H:i') : null;
    }

    public function getToDateAttribute()
    {
        return $this->to ? \Carbon\Carbon::parse($this->to)->format('Y-m-d') : null;
    }

    public function getToTimeAttribute()
    {
        return $this->to ? \Carbon\Carbon::parse($this->to)->format('H:i') : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
