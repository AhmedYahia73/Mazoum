<?php

namespace App\Http\Resources\APiResource;

use App\Models\Admin;
use App\Models\EventUsers;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class CurrencyItemResource extends JsonResource
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

        return [
            'id' => $this->id,
            'title' => $this->{$lang.'_name'},
        ];

    }
}
