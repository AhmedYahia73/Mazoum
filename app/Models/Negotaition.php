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
    ];
}
