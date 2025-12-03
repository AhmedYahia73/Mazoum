<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventUsers extends Model
{
    use SoftDeletes;

    protected $table = 'event_users';

    public $timestamp = true;

    protected $fillable = [
        'event_id', 'uu_id', 'message_id', 'name', 'mobile', 'status', 'scan', 'scan_at', 'get_location', 'users_count',
        'is_sent', 'is_delivered', 'qr_sent', 'is_accepted', 'is_refused' , 'log' , 'sent_from' , 'is_read',
        'error_title', 'error','confirmed_at','is_open','is_new_sent','scan_count',
        'is_send_congratulation','code'
    ];

    protected $casts = [
        'scan_at' => 'array',
    ];


    public function setScanAtAttribute($value)
    {
        // لو القيمة فاضية، خليه يحتفظ بالقيمة القديمة كما هي
        if (empty($value)) {
            return;
        }

        $status = $this->scan_at ? json_decode($this->attributes['scan_at'], true) : [];

        $status[] = $value;

        $this->attributes['scan_at'] = json_encode($status);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Generate unique code when creating a new EventUsers record
        static::creating(function ($model) {
            if (empty($model->code)) {
                $model->code = self::generateUniqueCode();
            }
        });
    }

    /**
     * Generate a unique code for EventUsers.
     *
     * @return string
     */
    public static function generateUniqueCode()
    {
        do {
            // Generate a random 6-character alphanumeric code
            $code = strtolower(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public function event()
    {
        return $this->belongsTo('App\Models\Events', 'event_id');
    }

    // public function mobile_code()
    // {
    //     return $this->belongsTo('App\Models\MobileCodes', 'code_id');
    // }
}
