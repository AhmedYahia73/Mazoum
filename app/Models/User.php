<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;


    protected $fillable = [
        'name', 'mobile', 'device_token', 'token' , 'status' , 'mobile_code',
        'offer_id' , 'balance' ,'full_balance' , 'user_type' , 'password', 'email' , 'pass',
        'order_id','start_subscription_date','duration_type','duration',
        'payment_type','employee_gender','is_paid','subscription_price'
    ];

  	protected $hidden = [
        'password'
    ];

    public function orders() {
        return $this->hasMany('App\Models\Orders','user_id');
    }

    public function offer()
    {
        return $this->belongsTo('App\Models\Packages','offer_id');
    }

    public function order()
    {
        return $this->belongsTo('App\Models\Orders','order_id');
    }

    public function code() {
        return $this->belongsTo('App\Models\MobileCodes','mobile_code');
    }


    public function toArray()
    {
        $array = parent::toArray();

        if (getallheaders() != null && ! empty(getallheaders()) && array_key_exists('language', getallheaders())) {
            $lang = getallheaders()['language'];
        } else {
            $lang = 'ar';
        }

        if (! array_key_exists('offer_name', $array)) {
            $offer = Packages::find($this->offer_id);
            $array['offer_name'] = $offer != null ? $offer->{$lang.'_name'} : '';
        }

        if (! array_key_exists('available', $array)) {
            $array['available'] = $this->full_balance - $this->balance;
        }

        return $array;
    }

}
