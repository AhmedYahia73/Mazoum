<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "description",
        "type",
        "icon",
        "status",
    ];

    protected $appends = ["icon_url"];

    public function getIconUrlAttribute(){
        if(isset($this->attributes['icon'])){
            return url("storage/" . $this->attributes['icon']);
        }
    }
}
