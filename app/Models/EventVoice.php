<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventVoice extends Model
{
    use HasFactory;
    protected $table = 'event_voices';

    public $timestamp = true;

    protected $fillable = [
        "voice",
        "event_user_id",
        "custom_event_user_id",
    ];
    protected $appends = [
        'voice_url',
    ];

    public function getVoiceUrlAttribute()
    {
        return url('storage/' . $this->voice);
    }

    public function event_user()
    {
        return $this->belongsTo('App\Models\EventUsers', 'event_user_id');
    }

    public function custom_event_user()
    {
        return $this->belongsTo('App\Models\CustomEventUsers', 'custom_event_user_id');
    }
}
