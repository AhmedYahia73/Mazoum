<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Memory extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'event_user_id',
        'image',
    ];
    protected $appends = ["image_url"];

    public function getImageUrlAttribute(){
        if(isset($this->attributes['image'])){
            return url("storage/" . $this->attributes['image']);
        }
        return null;
    }

    public function user(){
        return $this->belongsTo(EventUsers::class, "event_user_id");
    }
}
