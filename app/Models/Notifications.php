<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;



class Notifications extends Model
{

    protected  $table = 'notifications';

    public $timestamp = true;

    protected $fillable = [
        'add_by', 'user_id', 'send_to_type', 'send_to_id', 'en_description','ar_description',
        'url','seen','type','item_id','en_title','ar_title',
        'user_event_id','status'
    ];


    protected static function boot() {
        parent::boot();
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('id', 'asc');
        });
    }


    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }





}
