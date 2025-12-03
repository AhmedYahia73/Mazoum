<?php

namespace App\Http\Resources\APiResource;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\OrderItems;


class UserEvents_Data extends JsonResource
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

        $code = $this->mobile_code != null ? $this->mobile_code->code : 0;

        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $code,
            'mobile' => $this->mobile,
            'phone' => str_replace('+','',$code).$this->mobile
        ];


        return $data;
    }
}
