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
        'country','location','can_replay_messages' , 'is_open' , 'gender',
        'sending_type','phone','invitation_count','reservation_date','package_price','payment_type',
        'is_paid','employee_gender','color','image','video', 'country_code', 'scan_assistant_id',
        'name_qr', 'number_qr', 'qr_height', 'qr_width', 'qr_x', 'qr_y', 'resend_qr',
        'image_height', 'image_width', 'text_color', 'pdf', 'country_id',
        'pdf_bottom', 'show_data_pdf', "phone_setting_id", "name", "scan_gender",
    ];

    public function sub_user(){
        return $this->hasMany('App\Models\User', 'event_id');
    }

    public function getPdfAttribute($value)
    {
        return Image_Path($value);
    }

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
    
    public function employee()
    {
        return $this->belongsTo('App\Models\User', 'assistant_id');
    }


  	protected static function boot() {
        parent::boot();
        static::addGlobalScope(function (Builder $builder) {
            if (
                auth()->check() &&
                auth()->user()->role == 'employee'
            ) {
                $builder->where('assistant_id', auth()->id());
            }
        });
        static::updating(function ($model) {
            if (auth()->check() &&
            ((auth()->user()->role == 'employee' &&
            $model->assistant_id != auth()->id()) || 
            (auth()->user()->role == 'scan_employee' &&
            $model->scan_assistant_id != auth()->id()))) {
                return false;
            }
        });
        static::creating(function ($model) {
            if (auth()->check() &&
            auth()->user()->role == 'employee') {
                $model->assistant_id = auth()->id();
            }
        }); 
        static::deleting(function ($model) { 
            if (
                auth()->check() &&
                ((auth()->user()->role == 'employee' &&
                $model->assistant_id != auth()->id())
                || (
                auth()->user()->role == 'scan_employee' &&
                $model->scan_assistant_id != auth()->id()))
            ) {
                return false;
            }
        });
        //_____________________________
        static::addGlobalScope(function (Builder $builder) {
            if (
                auth()->check() &&
                auth()->user()->role == 'scan_employee'
            ) {
                $builder->where('scan_assistant_id', auth()->id());
            }
        }); 
        static::creating(function ($model) {
            if (auth()->check() &&
            auth()->user()->role == 'scan_employee') {
                $model->scan_assistant_id = auth()->id();
            }
        });
    }


}
