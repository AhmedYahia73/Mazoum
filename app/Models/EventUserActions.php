<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EventUserActions extends Model
{
                $this->accept_data($data, $user_event);
    private function accept_data($data, $user_event) {
        try {
            if (isset($data['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['nfm_reply']['response_json'])) {
                $users_count = 0;
                $response_json = json_decode($data['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['nfm_reply']['response_json'], true);
                
                // إحنا هنا بنسحب القيمة بناءً على اسم الحقل في الـ Flow (screen_0___0)
                // لو إنت مغير الاسم في الـ Flow JSON تأكد إنه مطابق هنا
                $flow_id = $response_json['screen_0___0'] ?? '0_0'; 
                
                // بنعمل Extract للرقم من الـ ID (مثلاً لو الـ ID هو 0_3 هياخد رقم 3)
                $count_parts = explode('_', $flow_id);
                $users_count = (int) end($count_parts);
                // الآن تحديث الجزء اللي كان معمول له Comment
                $event_action = EventUserActions::where("event_id", $user_event->event_id)
                    ->where("event_user_id", $user_event->id)
                    ->where('action', 'accept_event')
                    ->first();

                if ($event_action) {
                    // تحديث السجل الموجود
                    $event_action->users_count = $users_count;
                    $event_action->save();
                } else {
                    // إنشاء سجل جديد
                    EventUserActions::create([
                        'event_id' => $user_event->event_id,
                        'event_user_id' => $user_event->id,
                        'mobile' => $user_event->mobile,
                        'action' => 'accept_event',
                        'users_count' => $users_count,
                        'msg' => null
                    ]);
                }
            }
        } catch (\Throwable $th) { 
        }
    }
    protected $table = 'event_users_actions';

    public $timestamp = true;

    protected $fillable = [
         'event_id', 'event_user_id', 'mobile', 'action' , 'msg' , 'users_count'
    ];
    protected $appends = ["new_users_count"];

    public function getNewUsersCountAttribute(){
        if(isset($this->attributes['users_count'])){
            return $this->attributes['users_count'];
        }
        return 0;
    }
 
    public function event()
    {
        return $this->belongsTo('App\Models\Events', 'event_id');
    }

    public function event_user()
    {
        return $this->belongsTo('App\Models\EventUsers', 'event_user_id');
    }
}

