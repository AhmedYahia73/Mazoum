<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Desgins extends Model
{
    protected $table = 'desgins';

    public $timestamp = true;

    protected $fillable = [
        'en_name','ar_name', 'file', 'type'
    ];

    public function getFileAttribute($value)
    {
        return Image_Path($value);
    }

}
