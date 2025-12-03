<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pricing extends Model
{
    protected $table = 'pricing';

    public $timestamp = true;

    protected $fillable = [
        'en_title', 'ar_title', 'send_invitation', 'confirm_attendance', 'confirm_apology',
        'reminder_before_invitation', 'party_employee', 'attendance_report_after_invitation',
        'send_congratulations_after_invitation','users_count','price','congratulations_messages'
    ];

}
