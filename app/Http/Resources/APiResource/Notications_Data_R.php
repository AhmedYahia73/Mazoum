<?php

namespace App\Http\Resources\APiResource;

use App\Models\Admin;
use App\Models\EventUsers;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class Notications_Data_R extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        if (array_key_exists('language', getallheaders())) {
          $lang = getallheaders()['language'];
        } elseif (array_key_exists('Language', getallheaders())) {
          $lang = getallheaders()['Language'];
        } else {
          $lang = 'ar';
        }

        $user_event = EventUsers::where('id',$this->user_event_id)->select(['id','name','mobile'])->first();

        return [
            'id' => $this->id,
            'title' => $this->{$lang.'_title'},
            'description' => $this->{$lang.'_description'},
            'seen' => $this->seen,
          	'event_id' => $this->item_id,
            'user_event' => $user_event,
            'type' => $this->type,
            'status' => $this->status,
          	'date' => Carbon::parse($this->created_at)->format('Y-m-d'),
          	'created_at' => Carbon::parse($this->created_at)->format('Y-m-d h:i A'),
        ];

    }
}
