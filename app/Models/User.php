<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
        "name", "mobile", "device_token", "token" , "status" , "mobile_code",
        "offer_id" , "balance" ,"full_balance" , "user_type" , "password", "email" , "pass",
        "order_id","start_subscription_date","duration_type","duration",
        "payment_type","employee_gender","is_paid","subscription_price",
        "user_id", "custom_invetaion", "send_custom_invetaion", "custom_event_id", 
    ];

  	protected $hidden = [
        'password'
    ];

    protected $appends = ['role'];

    public function sub_users(){
        return $this->hasMany(User::class, "user_id");
    }

    public function getRoleAttribute(){
        $type = $this->getAttribute('user_type');
        if($type == "employee"){
            return "employee";
        }
        elseif($type == "scan_employee"){
            return "scan_employee";
        }
        else{
            return "user";
        }
    }

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

  	protected static function boot() {
        parent::boot(); 
        static::addGlobalScope(function (Builder $builder) {
            if (
                auth()->check() &&
                auth()->user()->role == 'employee'
            ) {
                $builder->where('user_type', "!=", "employee");
            }
        });
        static::updating(function ($model) {
            if (
                auth()->check() &&
                ((auth()->user()->role == 'employee' &&
                $model->user_type == "employee") ||
                auth()->user()->role == 'scan_employee')
            ) {
                return false;
            }
        });
        static::creating(function ($model) {
            if (auth()->check() &&
            auth()->user()->role == 'employee' && is_null($model->user_type)){
                $model->user_type = "scan_employee";
            }
        });
        static::deleting(function ($model) { 
            if (
                auth()->check() &&
                ((auth()->user()->role == 'employee' &&
                $model->user_type != "scan_employee") ||
                auth()->user()->role == 'scan_employee')
            ) {
                return false;
            }
        });
    }

}
