<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;


class Events extends Model
{
    use SoftDeletes;

    protected $table = 'events';

    public $timestamp = true;

    protected $fillable = [
        'title', 'file', 'lat', 'long', 'address', 'showing_qr', 'add_by', 'user_id',
        'first_name' , 'last_name' , 'date' , 'time', 'assistant_id','have_reminder' , 'sent_remember',
        'country','location','can_replay_messages' , 'is_open' , 'gender','enable_resend_again',
        'sending_type','phone','invitation_count','reservation_date','package_price','payment_type',
        'is_paid','employee_gender','color','image','video',
        'country_code'
    ];

    public function getFileAttribute($value)
    {
        return Image_Path($value);
    }

    public function getImageAttribute($value)
    {
        return $value != null ? Image_Path($value) : null;
    }

    public function getVideoAttribute($value)
    {
        return $value != null ? Image_Path($value) : null;
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }


  	protected static function boot() {
        parent::boot();
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('date', 'asc');
        });
    }


}
