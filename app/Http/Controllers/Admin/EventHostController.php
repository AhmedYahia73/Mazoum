<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CongratulationMessages;
use App\Models\CustomEvent;
use App\Models\CustomEventUsers;
use App\Models\EventMessages;
use App\Models\Events;
use App\Models\EventUsers;
use App\Models\EventVoice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EventHostController extends Controller
{
    public function custom_users(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'custom_event_id' => 'required|exists:custom_event,id',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $custom_event_id = $request->custom_event_id;
        $event_host = User::
        where("id", $request->user_id) 
        ->first();
        $user_id = $event_host->user_id ? $event_host->id : null;
        $custom_event_user = CustomEventUsers::
        where('custom_event_id',$custom_event_id);
        $user_id ? $custom_event_user->where("user_id", $user_id): 
        $custom_event_user->where(function($query) use($user_id){
            $query->whereNull("user_id")
            ->orWhere("user_id", $user_id);
        });
        $custom_event_user = $custom_event_user->get();
   
        
        // $ =  $confirm_attend - $qr;  
        return response()->json([
            "custom_event_user" => $custom_event_user
        ]);
    }
    
    public function users(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'event_id' => 'required|exists:events,id',
            'type' => 'required|in:all,qr,confirm,apologize,send_Qr,not_attend,confirm_web_users,waiting,invitees,not_confirm',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $event = Events::
        where("id", $request->event_id)
        ->first();
        $user_status = $event->user_id == $request->user_id;
        $event_id = $request->event_id;
        $event_host = User::
        where("id", $request->user_id) 
        ->first();
        $user_id = $request->user_id;
        $event_user = EventUsers::
        where('event_id',$event_id);
        !$user_status ? $event_user->where("user_id", $user_id): 
        $event_user->where(function($query) use($user_id){
            $query->whereNull("user_id")
            ->orWhere("user_id", $user_id);
        });

        if($request->type == "all"){
            $event_user = $event_user 
            ->paginate(15);
        }
        elseif($request->type == "qr"){
            $event_user = $event_user
            ->where('scan', 'yes')
            ->paginate(15);
        }
        elseif($request->type == "confirm"){
            $event_user = $event_user
            ->where('accept_count', ">", 0) 
            ->paginate(15);
        } 
        elseif($request->type == "apologize"){
            $event_user = $event_user
            ->where('status','not-attend')
            ->paginate(15);
        } 
        elseif($request->type == "send_Qr"){
            $event_user = $event_user
            ->where('qr_sent','yes')
            ->where("accept_count", ">", 0)
            ->paginate(15);
        } 
        elseif($request->type == "confirm_web_users"){
            $event_user = $event_user
            ->where("send_type", "link")
            ->where('qr_sent','yes') 
            ->where("accept_count", ">", 0)
            ->paginate(15);
        }  
        elseif($request->type == "waiting"){
            $event_user = $event_user
            ->where('status', 'hold')
            ->where('is_new_sent', 0)
            ->whereNull('is_sent')
            ->paginate(15);
        }
        elseif($request->type == "invitees"){
            $event_user = $event_user 
            ->paginate(15);
        }  
        elseif($request->type == "not_confirm"){
            $event_user = $event_user
            ->where("accept_count", 0) 
            ->where('status', "!=", 'not-attend')
            ->paginate(15);
        }  
        elseif($request->type == "not_attend"){
            $event_user = $event_user
            ->where('accept_count', ">", 0)
            ->whereColumn("scan_count", "<", "accept_count")
            ->paginate(15)
            ->withQueryString() 
            ->through(function($item) {
                $item->not_attend = $item->scan_count - $item->accept_count;
                return $item;
            });
        }    
        
        // $ =  $confirm_attend - $qr;  
        return response()->json([
            "event_user" => $event_user
        ]);
    }
    
    public function excel_users(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'event_id' => 'required|exists:events,id',
            'type' => 'required|in:all,qr,confirm,apologize,send_Qr,not_attend,confirm_web_users,waiting,invitees,not_confirm',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $event = Events::
        where("id", $request->event_id)
        ->first();
        $user_status = $event->user_id == $request->user_id;
        $event_id = $request->event_id;
        $event_host = User::
        where("id", $request->user_id) 
        ->first();
        $user_id = $request->user_id;
        $event_user = EventUsers::
        where('event_id',$event_id);
        !$user_status ? $event_user->where("user_id", $user_id): 
        $event_user->where(function($query) use($user_id){
            $query->whereNull("user_id")
            ->orWhere("user_id", $user_id);
        });

        if($request->type == "all"){
            $event_user = $event_user 
            ->get();
        }
        elseif($request->type == "qr"){
            $event_user = $event_user
            ->where('scan', 'yes')
            ->get();
        }
        elseif($request->type == "confirm"){
            $event_user = $event_user
            ->where('accept_count', ">", 0) 
            ->get();
        } 
        elseif($request->type == "apologize"){
            $event_user = $event_user
            ->where('status','not-attend')
            ->get();
        } 
        elseif($request->type == "send_Qr"){
            $event_user = $event_user
            ->where('qr_sent','yes')
            ->where("accept_count", ">", 0)
            ->get();
        } 
        elseif($request->type == "confirm_web_users"){
            $event_user = $event_user
            ->where("send_type", "link")
            ->where('qr_sent','yes') 
            ->where("accept_count", ">", 0)
            ->get();
        }  
        elseif($request->type == "waiting"){
            $event_user = $event_user
            ->where('status', 'hold')
            ->where('is_new_sent', 0)
            ->whereNull('is_sent')
            ->get();
        }
        elseif($request->type == "invitees"){
            $event_user = $event_user 
            ->get();
        }  
        elseif($request->type == "not_confirm"){
            $event_user = $event_user
            ->where("accept_count", 0) 
            ->where('status', "!=", 'not-attend')
            ->get();
        }  
        elseif($request->type == "not_attend"){
            $event_user = $event_user
            ->where('accept_count', ">", 0)
            ->whereColumn("scan_count", "<", "accept_count")
            ->get()
            ->map(function($item) {
                $item->not_attend = $item->scan_count - $item->accept_count;
                return $item;
            });
        }    
        
        // $ =  $confirm_attend - $qr;  
        return response()->json([
            "event_user" => $event_user
        ]);
    }

    public function report(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'event_id' => 'required|exists:events,id',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $event_id = $request->event_id;
        $event = Events::
        where("id", $request->event_id)
        ->first();
        $user_status = $event->user_id == $request->user_id;
        $event_host = User::
        where("id", $request->user_id) 
        ->first(); 
        $user_id = $request->user_id;
        $qr = EventUsers::
        where('event_id',$event_id) 
        ->where('scan','yes');
        !$user_status ? $qr->where("user_id", $user_id): 
        $qr->where(function($query) use($user_id){
            $query->whereNull("user_id")
            ->orWhere("user_id", $user_id);
        });
        $qr = $qr->sum('scan_count');
        $confirm_attend = EventUsers::
        where('event_id', $event_id) ;
        !$user_status ? $confirm_attend->where("user_id", $user_id):
        $confirm_attend->where(function($query) use($user_id){
            $query->whereNull("user_id")
            ->orWhere("user_id", $user_id);
        });
        $confirm_attend = $confirm_attend->sum('accept_count');
        $apologize = EventUsers::
        where('event_id',$event_id)
        ->where('status','not-attend');
        !$user_status ? $apologize->where("user_id", $user_id):
        $apologize->where(function($query) use($user_id){
            $query->whereNull("user_id")
            ->orWhere("user_id", $user_id);
        });
        $apologize = $apologize->sum('users_count'); 
        $send_Qr = EventUsers::
        where('event_id', $event_id)
        ->where('qr_sent','yes') ;
        !$user_status ? $send_Qr->where("user_id", $user_id):
        $send_Qr->where(function($query) use($user_id){
            $query->whereNull("user_id")
            ->orWhere("user_id", $user_id);
        });
        $send_Qr = $send_Qr->sum('accept_count');  
        
        $not_attend =  $confirm_attend - $qr;
        $confirm_web_users = EventUsers::
        where('event_id', $event_id)
        ->where("send_type", "link")
        ->where('qr_sent','yes') ;
        !$user_status ? $confirm_web_users->where("user_id", $user_id):
        $confirm_web_users->where(function($query) use($user_id){
            $query->whereNull("user_id")
            ->orWhere("user_id", $user_id);
        });
        $confirm_web_users = $confirm_web_users->sum('accept_count');
        $waiting = EventUsers::
        where('event_id',$event_id)
        ->where('status','hold')
        ->where('is_new_sent',0)
        ->whereNull('is_sent');
        !$user_status ? $waiting->where("user_id", $user_id):
        $waiting->where(function($query) use($user_id){
            $query->whereNull("user_id")
            ->orWhere("user_id", $user_id);
        });
        $waiting = $waiting->sum('users_count');
        $invitees = EventUsers::
        where('event_id',$event_id);
        !$user_status ? $invitees->where("user_id", $user_id):
        $invitees->where(function($query) use($user_id){
            $query->whereNull("user_id")
            ->orWhere("user_id", $user_id);
        });
        $invitees = $invitees->sum('users_count');
        $not_confirm = EventUsers::
        where('event_id', $event_id)
        ->where("accept_count", 0) 
        ->where('status', "!=", 'not-attend')
        ->where(function($query) { 
            $query->where('is_new_sent', "!=", 0)
            ->orWhere('status', "!=", 'hold')
            ->orWhereNotNull('is_sent'); 
        });
        !$user_status ? $not_confirm->where("user_id", $user_id):
        $not_confirm->where(function($query) use($user_id){
            $query->whereNull("user_id")
            ->orWhere("user_id", $user_id);
        });
        $not_confirm = $not_confirm->sum('users_count');

        $ids = EventUsers::
        where('event_id',$event_id);
        !$user_status ? $ids->where("user_id", $user_id):
        $ids->where(function($query) use($user_id){
            $query->whereNull("user_id")
            ->orWhere("user_id", $user_id);
        });
        $ids = $ids->pluck('id')->toArray();
        $congratulation_voice = EventVoice::
        where('event_user_id', $ids)
        ->count();
        return response()->json([ 
            "invitees" => $invitees,
            "waiting" => $waiting,
            "confirm_web_users" => $confirm_web_users,
            "not_attend" => $not_attend, 
            "qr" => $qr,
            "congratulation_voice" => $congratulation_voice,
            "confirm_attend" => $confirm_attend,
            "apologize" => $apologize,
            "not_confirm" => $not_confirm,
            "send_Qr" => $send_Qr, 
        ]);
    }

    
    public function voice_msgs(Request $request, $id)
    { 
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id', 
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $s = $request->search;

        $user_events = EventVoice::whereHas("event_user", function($query) use ($id, $request, $s) {
            // فلترة بحسب رقم الفعالية
            $query->where("event_id", $id)
            ->where("user_id", $request->user_id);

            // ⭐ البحث باسم المستخدم أو الهاتفك داخل العلاقة
            if ($s) {
                $query->where(function ($q) use ($s) {
                    $q->where('name', 'like', "%{$s}%")
                    ->orWhere('mobile', 'like', "%{$s}%");
                });
            }
        })->with('event_user:id,name,mobile');

        // ⭐ الترقيم الصفحي
        $user_events = $user_events->paginate(20);

        return response()->json([
            "status" => true,
            "user_events" => $user_events
        ]);
    }
    
    public function index(Request $request, $id)
    {
        $event = Events::
        findOrFail($id);
        $user_id = $event->user_id;
        $query = User::
        where(function($query) use($id){
            $query->where("event_id", $id)
            ->orWhereHas("event", function($q) use($id){
                $q->where("events.id", $id);
            });
        });

        // Search
        if ($request->search) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                ->orWhere('mobile', 'like', "%$search%");
            });
        }
        $Item = $query->paginate(15)
        ->through(function ($item) {
            // حساب القيمة يدوياً لأن toArray لم يتم تنفيذها بعد
            $calculated_available = $item->custom_invetaion - $item->send_custom_invetaion;
            
            // تعيين القيم الجديدة
            $item->balance = $calculated_available; 
            $item->available = $item->custom_invetaion;
            
            return $item;
        });// عدد العناصر في الصفحة

        return response()->json([
            'Item' => $Item,
        ]); 
    } 

    public function custom_index(Request $request, $id)
    {
        $event = CustomEvent::
        findOrFail($id);
        $user_id = $event->user_id;
        $query = User::
        where("user_id", $event->user_id)
        ->orWhere("id", $event->user_id);

        // Search
        if ($request->search) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                ->orWhere('mobile', 'like', "%$search%");
            });
        }
 
        $Item = $query->paginate(15); // عدد العناصر في الصفحة

        return response()->json([
            'Item' => $Item,
        ]); 
    }  

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
          'mobile_code' => 'required',
          'mobile' => 'required',
          'name' => 'required',
          'custom_invetaion' => 'required|numeric',
          "custom_event_id" => "exists:custom_event,id",
          "event_id" => "exists:events,id",
          "password" => "required"
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        if(!$request->custom_event_id && !$request->event_id){
            return response()->json([
                "errors" => "custom_event_id or event_id is required"
            ]);

        }
        $user_id = 0;
        if($request->event_id){ 
            $user_id = Events::
            where("id", $request->event_id)
            ->first()->user_id;
        }
        if($request->custom_event_id){ 
            $user_id = CustomEvent::
            where("id", $request->custom_event_id)
            ->first()->user_id;
        }
        $user = User::
        where("id", $user_id)
        ->first();
        $available = $user->custom_invetaion - $user->send_custom_invetaion;
        $user_custom_invetaion = $user->custom_invetaion - $request->custom_invetaion > 0 ? $user->custom_invetaion - $request->custom_invetaion : 0;
        $user->custom_invetaion = $user_custom_invetaion;
        $user->balance -= $request->custom_invetaion;
        $user->save();
        User::create([
            "mobile_code" => $request->mobile_code,
            "mobile" => $request->mobile,
            "name" => $request->name,
            "custom_invetaion" => $request->custom_invetaion,
            "user_id" => $user->id,
            "custom_event_id" => $request->custom_event_id ?? null,
            "event_id" => $request->event_id ?? null,
            "password" => Hash::make($request->password),
            "pass" => $request->password,
            "balance" => $request->custom_invetaion,
            "user_type" => "user",
        ]);

        return response()->json([
            "success" => "You add data success"
        ]);
    }

    public function show($id)
    {
        $Item = User::findOrFail($id);
        return response()->json([
            'Item' =>  $Item, 
        ]);  
    }
    
    public function edit($id)
    {
        $Item = User::findOrFail($id);
        return response()->json([
            'Item' =>  $Item, 
        ]);  
    }
    
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            "password" => "required"
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
  
        User::
        where("id", $id)
        ->update([
            "password" => Hash::make($request->password),
            "pass" => $request->password,
        ]);

        return response()->json([
            "success" => "You update data success"
        ]);
    }

    public function destroy($id){
        
        CustomEventUsers::
        where("user_id", $id)
        ->delete();
        EventUsers::
        where("user_id", $id)
        ->delete();
        $user = User::where("id", $id)
        ->first();
        $parent_user = User::
        where("id", $user->user_id)
        ->first(); 
        if($parent_user){ 
            $balance = $user->custom_invetaion - $user->send_custom_invetaion;
            $parent_user->custom_invetaion += $balance;
            $parent_user->balance += $balance;
            $parent_user->save();
        }
        $user->delete();

        return response()->json([
            "success" => "You delete data success"
        ]);
    }
}
