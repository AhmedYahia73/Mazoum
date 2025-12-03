<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Orders extends Model
{
    protected $table = 'orders';

    public $timestamp = true;

    protected $fillable = [
        'order_number', 'user_id', 'type', 'offer_id', 'total',
        'operation_date' , 'users_count' , 'currency_id',
        'start_subscription_date' , 'duration_type' , 'duration',
        'payment_type','employee_gender','is_paid'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }

    public function offer()
    {
        return $this->belongsTo('App\Models\Packages','offer_id');
    }

    public function currency() {
        return $this->belongsTo('App\Models\Currency','currency_id');
    }
}
