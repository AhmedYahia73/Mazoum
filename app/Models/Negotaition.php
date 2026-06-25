<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Negotaition extends Model
{
    use HasFactory;

    protected $fillable = [
        'pricing_id', 
        'user_id',
        'status', // "pending", "inprogress", "approve", "reject"
    ];

    public function package(){
        return $this->belongsTo(Pricing::class, "pricing_id");
    }

    public function user(){
        return $this->belongsTo(User::class, "user_id");
    }
}
