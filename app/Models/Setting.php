<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Setting extends Model
{
    protected $table = 'setting';

    public $timestamp = true;

    protected $fillable = [
        'email', 'en_website_name', 'ar_website_name', 'whatsapp', 'mobile', 'en_key_words',
        'ar_key_words', 'en_description', 'ar_description', 'logo','access_token','phone_numer_id',
        'facebook_link','youtube_link', 'sender_id',
        'sa_access_token','sa_phone_numer_id','sa_sender_id',
        'sa_access_token2','sa_phone_numer_id2','sa_sender_id2',
        'kw_access_token2','kw_phone_numer_id2','kw_sender_id2',
    ];

    public function getLogoAttribute($value)
    {
        return Image_Path($value);
    }
}
