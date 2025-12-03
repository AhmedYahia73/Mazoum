<?php

namespace App\Http\Resources\APiResource;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\CongratulationMessages;
use Carbon\Carbon;

class CongratulationMessagesResource extends JsonResource
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

        $replay = CongratulationMessages::whereHas('event',function($event) { $event->whereIn('is_open',['yes','current']); })->where('message_id',$this->id)->where('type','replay')->select(['id','name','mobile','message'])->first();

        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'mobile' => $this->mobile,
            'message' => $this->message,
          	'replay'   => $replay,
              'created_at' => Carbon::parse($this->created_at)->format('Y-m-d h:i A')
        ];


        return $data;
    }
}
