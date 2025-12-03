<?php

namespace App\Http\Resources\APiResource;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\OrderItems;
use App\Models\EventUsers;


class UserEventsData_V2 extends JsonResource
{
    public function __construct($resource)
    {
        // Ensure we call the parent constructor
        parent::__construct($resource);
        $this->resource = $resource;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {


        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'address' => $this->address,
            'date' => $this->date,
            'image' => $this->image,
            'all_invited' => (int)(EventUsers::where('event_id',$this->id)->sum('users_count')),
            'invitations_not_sent' => (int)(EventUsers::where('event_id',$this->id)->where('status','hold')->sum('users_count')),

            'confirmed_invitatios' => (int)(EventUsers::where('event_id',$this->id)->where('is_accepted','yes')->sum('users_count')),
            //'confirmed_invitatios' => (int)(EventUsers::where('event_id',$this->id)->where('status','attend')->sum('users_count')),

            'scaned_qr' => (int)(EventUsers::where('event_id',$this->id)->where('scan','yes')->sum('scan_count')),
            'apologized_invitatios' => (int)(EventUsers::where('event_id',$this->id)->where('status','not-attend')->sum('users_count')),
            'failed_invitatios' => (int)(EventUsers::where('event_id',$this->id)->where('status','failed')->sum('users_count')),

            'non_attendance_user' => (int) (EventUsers::where('event_id',$this->id)->where('status','attend')->whereNull('scan')->whereNull('is_refused')->sum('users_count'))

        ];

        return $data;
    }
}
