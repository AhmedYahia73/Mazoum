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

        $isObj = is_object($this->resource);

        $code = $isObj
            ? ($this->mobile_code != null ? ($this->mobile_code->code ?? 0) : 0)
            : ($this->resource['code'] ?? ($this->resource['mobile_code']['code'] ?? 0));

        $id = $isObj ? $this->id : ($this->resource['id'] ?? null);
        $name = $isObj ? $this->name : ($this->resource['name'] ?? null);
        $mobile = $isObj ? $this->mobile : ($this->resource['mobile'] ?? null);
        $usersCount = $isObj ? ($this->users_count ?? 0) : ($this->resource['users_count'] ?? 0);
        $scanCount = $isObj ? ($this->scan_count ?? 0) : ($this->resource['scan_count'] ?? 0);
        $acceptCount = $isObj ? ($this->accept_count ?? 0) : ($this->resource['accept_count'] ?? 0);
        $isSent = $isObj ? $this->is_sent : ($this->resource['is_sent'] ?? null);
        $isAccepted = $isObj ? $this->is_accepted : ($this->resource['is_accepted'] ?? null);
        $isRefused = $isObj ? $this->is_refused : ($this->resource['is_refused'] ?? null);
        $isDelivered = $isObj ? $this->is_delivered : ($this->resource['is_delivered'] ?? null);
        $isRead = $isObj ? $this->is_read : ($this->resource['is_read'] ?? null);
        $qrSent = $isObj ? $this->qr_sent : ($this->resource['qr_sent'] ?? null);
        $status = $isObj ? $this->status : ($this->resource['status'] ?? null);

        $data = [
            'id' => $id,
            'name' => $name,
            'code' => $code,
            'mobile' => $mobile,
            'phone' => str_replace('+', '', $code) . $mobile,
            "scan_status" => $usersCount > $scanCount,
            "accept_count" => $acceptCount,
            "is_sent" => $isSent,
            "is_accepted" => $isAccepted,
            "is_refused" => $isRefused,
            "is_delivered" => $isDelivered,
            "is_read" => $isRead,
            "qr_sent" => $qrSent,
            "status" => $status
        ];


        return $data;
    }
}
