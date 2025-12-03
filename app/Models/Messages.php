<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Messages extends Model
{
    protected $table = 'messages';

    protected $fillable = [

        'first_name','last_name','email','mobile','message','seen',
    ];

    public $timestamps = true;

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('id', 'desc');
        });
    }




}
