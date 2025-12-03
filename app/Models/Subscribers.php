<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Subscribers extends Model
{

    protected $table = 'subscribers';

    protected $fillable = [ 'email' , 'seen' ];

    public $timestamps = true;

    protected static function boot() {
        parent::boot();
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('id', 'desc');
        });
    }




}
