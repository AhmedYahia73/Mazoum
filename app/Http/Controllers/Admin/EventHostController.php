<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CongratulationMessages;
use App\Models\EventMessages;
use App\Models\Events;
use App\Models\CustomEvent;
use App\Models\CustomEventUsers;
use App\Models\EventUsers;
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

        $event_id = $request->event_id;
        $event_host = User::
        where("id", $request->user_id) 
        ->first();
        $user_id = $event_host->user_id ? $event_host->id : null;
        $event_user = EventUsers::
        where('event_id',$event_id);
        $user_id ? $event_user->where("user_id", $user_id): 
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
        $event_host = User::
        where("id", $request->user_id) 
        ->first();
        $user_id = $event_host->user_id ? $event_host->id : null;
        $qr = EventUsers::
        where('event_id',$event_id) 
        ->where('scan','yes');
        $user_id ? $qr->where("user_id", $user_id): 
        $qr->where(function($query) use($user_id){
            $query->whereNull("user_id")
            ->orWhere("user_id", $user_id);
        });
        $qr = $qr->sum('scan_count');
        $confirm_attend = EventUsers::
        where('event_id', $event_id) ;
        $user_id ? $confirm_attend->where("user_id", $user_id):
        $confirm_attend->where(function($query) use($user_id){
            $query->whereNull("user_id")
            ->orWhere("user_id", $user_id);
        });
        $confirm_attend = $confirm_attend->sum('accept_count');
        $apologize = EventUsers::
        where('event_id',$event_id)
        ->where('status','not-attend');
        $user_id ? $apologize->where("user_id", $user_id):
        $apologize->where(function($query) use($user_id){
            $query->whereNull("user_id")
            ->orWhere("user_id", $user_id);
        });
        $apologize = $apologize->sum('users_count'); 
        $send_Qr = EventUsers::
        where('event_id', $event_id)
        ->where('qr_sent','yes') ;
        $user_id ? $send_Qr->where("user_id", $user_id):
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
        $user_id ? $confirm_web_users->where("user_id", $user_id):
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
        $user_id ? $waiting->where("user_id", $user_id):
        $waiting->where(function($query) use($user_id){
            $query->whereNull("user_id")
            ->orWhere("user_id", $user_id);
        });
        $waiting = $waiting->sum('users_count');
        $invitees = EventUsers::
        where('event_id',$event_id);
        $user_id ? $invitees->where("user_id", $user_id):
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
        $user_id ? $not_confirm->where("user_id", $user_id):
        $not_confirm->where(function($query) use($user_id){
            $query->whereNull("user_id")
            ->orWhere("user_id", $user_id);
        });
        $not_confirm = $not_confirm->sum('users_count');

        return response()->json([ 
            "invitees" => $invitees,
            "waiting" => $waiting,
            "confirm_web_users" => $confirm_web_users,
            "not_attend" => $not_attend, 
            "qr" => $qr,
            "confirm_attend" => $confirm_attend,
            "apologize" => $apologize,
            "not_confirm" => $not_confirm,
            "send_Qr" => $send_Qr, 
        ]);
    }

    public function index(Request $request, $id)
    {
        $event = Events::
        findOrFail($id);
        $user_id = $event->user_id;
        $query = User::
        where("user_id", $user_id)
        ->orWhereHas("sub_users", function($query) use($user_id){
            $query->where("users.id", $user_id);
        });

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

    public function custom_index(Request $request, $id)
    {
        $event = CustomEvent::
        findOrFail($id);
        $user_id = $event->user_id;
        $query = User::
        where("user_id", $user_id)
        ->orWhereHas("sub_users", function($query) use($user_id){
            $query->where("users.id", $user_id);
        });

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
          'mobile_code' => 'required|exists:mobile_codes,id',
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
        $user_custom_invetaion = $user->custom_invetaion - $request->custom_invetaion > 0 ? $request->custom_invetaion - $request->custom_invetaion : 0;
        $user->custom_invetaion -=  $user_custom_invetaion;
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
            "balance" => $request->custom_invetaion
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
        ->where("user_id", auth()->user()->id)
        ->update([
            "password" => Hash::make($request->password)
        ]);

        return response()->json([
            "success" => "You update data success"
        ]);
    } 
}
