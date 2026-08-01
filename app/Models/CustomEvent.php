<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomEvent extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'custom_event';

    public $timestamp = true;

    protected $fillable = [
        'title', 'image', 'code', 'user_id' , 
        'color' , 'assistant_id' , 'language',
        'address' , 'date' , 'time', "send_type",
        "name_qr", "number_qr", "qr_height",
        "qr_width", "qr_x", "qr_y", "lat", "lng",
        'scan_assistant_id', "resend_qr", "name",
        'image_height', 'image_width', 'text_color',
        "video", "pdf", "show_data_pdf", "pdf_bottom", "scan_gender"
    ];
    protected $appends = ["map", "video_url"];
   
    public function getVideoUrlAttribute(){
        if(isset($this->attributes['video'])){
            return Image_Path($this->attributes['video']);
        }
    }

    public function getMapAttribute(){
        if(isset($this->attributes['lat']) && isset($this->attributes['lng'])){
            return "https://www.google.com/maps?q={$this->attributes['lat']},{$this->attributes['lng']}";
        }
    }

    public function getImageAttribute($value)
    {
        return Image_Path($value);
    }

    public function getPdfAttribute($value)
    {
        return Image_Path($value);
    }

    public function sub_user()
    {
        return $this->hasMany('App\Models\User', 'custom_event_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
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
            (auth()->user()->role == 'employee' &&
            $model->assistant_id != auth()->id()) || 
            (auth()->user()->role == 'scan_employee' &&
            $model->scan_assistant_id != auth()->id())) {
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
