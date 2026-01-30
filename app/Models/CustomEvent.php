<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CustomEvent extends Model
{
    protected $table = 'custom_event';

    public $timestamp = true;

    protected $fillable = [
        'title', 'image', 'code', 'user_id' , 'color' , 'assistant_id' , 'language',
        'address' , 'date' , 'time', 'scan_assistant_id'
    ];

    public function getImageAttribute($value)
    {
        return Image_Path($value);
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
