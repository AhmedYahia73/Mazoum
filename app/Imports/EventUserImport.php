<?php

namespace App\Imports;

use App\Models\EventUsers;
use App\Models\Product;
use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;

class EventUserImport implements ToModel
{
    public $event_id;

    public function __construct($event_id) {
        $this->event_id = $event_id;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $check = EventUsers::where('event_id',$this->event_id)->where('mobile',$row[1])->first();

        if($check == null) {

            return new EventUsers([
                'event_id'    => $this->event_id,
                'name'        => $row[0],
                'mobile'      => $row[1],
                'users_count' => $row[2],
                'status' => 'hold'
            ]);
        }
    }
}
