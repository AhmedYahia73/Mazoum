<?php

namespace App\Http\Resources\APiResource;

use App\Models\Admin;
use App\Models\EventUsers;
use App\Models\Packages;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class UserItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        if (getallheaders() != null && ! empty(getallheaders()) && array_key_exists('language', getallheaders())) {
            $lang = getallheaders()['language'];
        } else {
            $lang = 'ar';
        }

        $offer = Packages::find($this->offer_id);


        return [
            'id' => $this->id,
            'name'  => $this->name,
            'status'  => $this->status,
            'mobile_code'  => $this->mobile_code,
            'mobile'  => $this->mobile,
            'device_token'  => $this->device_token,
            'offer_id'  => $this->offer_id,
            'start_subscription_date'  => $this->start_subscription_date,
            'duration_type'  => $this->duration_type,
            'subscription_price'  => $this->subscription_price,
            'duration'  => $this->duration,
            'payment_type'  => $this->payment_type,
            'is_paid'  => $this->is_paid,
            'balance'  => $this->balance,
            'full_balance'  => $this->full_balance,
            'password' => $this->password,
            'offer_name' => $offer != null ? $offer->{$lang.'_name'} : '',
            'available' => $this->full_balance - $this->balance,
            'token' => $this->token,
            'currency' => $this->order && $this->order->currency ? CurrencyItemResource::make($this->order->currency) : null
        ];

    }
}
