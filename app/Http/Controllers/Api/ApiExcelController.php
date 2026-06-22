<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomEvent;
use App\Models\CustomEventFamily;
use App\Models\CustomEventUsers;
use App\Models\CustomMessage;
use App\Models\Events;
use App\Models\EventUserActions;
use App\Models\EventUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiExcelController extends Controller
{

    public function excel_event_users(Request $request) {

        $validator = Validator::make($request->all(), [
          'custom_event_id' => 'required|exists:custom_event,id'
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }  
        $custom_event_id = $request->custom_event_id;

        $event_users = CustomEventUsers::
        where('custom_event_id', $custom_event_id)
        ->where("user_id", auth()->user()->id)
        ->get(); // عدد النتائج في الصفحة

        return response()->json([
            'event_users' => $event_users,
        ]); 
    }

  	public function excel_event_family(Request $request) {

        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id'
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }   
        $event_id = $request->custom_event_id;

        $event_users = CustomEventFamily::
        where('event_id', $event_id)
        ->get(); // عدد النتائج في الصفحة

        return response()->json([
            'event_users' => $event_users,
            'event_id' => $event_id,
        ]); 
    }

    public function excel_event_host_visitor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id', 
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }   
        $Item = CustomEvent::findOrFail($request->custom_event_id);
        $user_status = $Item->user_id == auth()->user()->id;
        if($user_status){ 
            $visitors_count = CustomEventUsers::
            where('custom_event_id',$Item->id)
            ->where(function($query) use($request){ 
                $query->where("user_id", auth()->user()->id)
                ->orWhereNull("user_id");
            })
            ->get(); 
        }
        else{
            $visitors_count = CustomEventUsers::
            where('custom_event_id',$Item->id)
            ->where(function($query) use($request){ 
                $query->where("user_id", auth()->user()->id);
            })
            ->get(); 
        }

        return response()->json([
            'Item' =>  $Item, 
            'visitors_count' =>  $visitors_count, 
        ]); 
    }

    public function excel_event_host_qr(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id', 
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }   
        $Item = CustomEvent::findOrFail($request->custom_event_id);
        $user_status = $Item->user_id == auth()->user()->id;
        if($user_status){  
            $qr_count = CustomEventUsers::
            where('custom_event_id',$Item->id)
            ->where(function($query) use($request){ 
                $query->where("user_id", auth()->user()->id)
                ->orWhereNull("user_id");
            })
            ->where('scan','yes')
            ->get();
        }
        else{
            $qr_count = CustomEventUsers::
            where('custom_event_id',$Item->id)
            ->where(function($query) use($request){ 
                $query->where("user_id", auth()->user()->id);
            })
            ->where('scan','yes')
            ->get(); 
        }

        return response()->json([
            'Item' =>  $Item, 
            'qr_count' =>  $qr_count,
        ]); 
    }

    public function excel_event_host_congrate_msg(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id', 
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }   
        $Item = CustomEvent::findOrFail($request->custom_event_id);
        $user_status = $Item->user_id == auth()->user()->id;
        if($user_status){ 
            $congratulation_msg = CustomMessage::
            where("custom_event_id", $Item->id)
            ->where("type", "congratulation")
            ->whereHas("user", function($query) use($request){ 
                $query->where("user_id", auth()->user()->id)
                ->orWhereNull("user_id");
            })
            ->get();
        }
        else{
            $congratulation_msg = CustomMessage::
            where("custom_event_id", $Item->id)
            ->where("type", "congratulation")
            ->whereHas("user", function($query) use($request){ 
                $query->where("user_id", auth()->user()->id);
            })
            ->get();
        }

        return response()->json([
            'Item' =>  $Item,
            'congratulation_msg' =>  $congratulation_msg, 
        ]); 
    }

    public function excel_event_host_apologize_msg(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id', 
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }   
        $Item = CustomEvent::findOrFail($request->custom_event_id);
        $user_status = $Item->user_id == auth()->user()->id;
        if($user_status){  
            $apologize_msg = CustomMessage::
            where("custom_event_id", $Item->id)
            ->where("type", "apologize")
            ->whereHas("user", function($query) use($request){ 
                $query->where("user_id", auth()->user()->id)
                ->orWhereNull("user_id");
            })
            ->get();
        }
        else{
            $apologize_msg = CustomMessage::
            where("custom_event_id", $Item->id)
            ->where("type", "apologize")
            ->whereHas("user", function($query) use($request){ 
                $query->where("user_id", auth()->user()->id);
            })
            ->get();
        }

        return response()->json([
            'Item' =>  $Item, 
            'apologize_msg' =>  $apologize_msg,
        ]); 
    }

    public function excel_event_host_apologize(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id', 
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }   
        $Item = CustomEvent::findOrFail($request->custom_event_id);
        $user_status = $Item->user_id == auth()->user()->id;
        if($user_status){ 
            $apologize_count = CustomEventUsers::
            where("custom_event_id", $Item->id) 
            ->where(function($query) use($request){ 
                $query->where("user_id", auth()->user()->id)
                ->orWhereNull("user_id");
            })
            ->get();
        }
        else{
            $apologize_count = CustomEventUsers::
            where("custom_event_id", $Item->id) 
            ->where(function($query) use($request){ 
                $query->where("user_id", auth()->user()->id);
            })
            ->get();
        }

        return response()->json([
            'Item' =>  $Item, 
            'apologize_count' =>  $apologize_count, 
        ]); 
    }

    public function excel_event_host_confirm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id', 
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }   
        $Item = CustomEvent::findOrFail($request->custom_event_id);
        $user_status = $Item->user_id == auth()->user()->id;
        if($user_status){
            $confirm_count = CustomEventUsers::
            where("custom_event_id", $Item->id) 
            ->where(function($query) use($request){ 
                $query->where("user_id", auth()->user()->id)
                ->orWhereNull("user_id");
            })
            ->get(); 
        }
        else{
            $confirm_count = CustomEventUsers::
            where("custom_event_id", $Item->id) 
            ->where(function($query) use($request){ 
                $query->where("user_id", auth()->user()->id);
            })
            ->get(); 
        }

        return response()->json([
            'Item' =>  $Item,   
            'confirm_count' =>  $confirm_count,  
        ]); 
    }

    public function excel_qr_count($id){
        $custom_event_users = CustomEventUsers::
        where('custom_event_id',$id)
        ->where("user_id", auth()->user()->id)
        ->where('scan','yes')
        ->get();

        return response()->json([
            "custom_event_users" => $custom_event_users
        ]);
    }
    
    public function excel_confirm_count(Request $request, $id){
    
        $Item = CustomEvent::findOrFail($id);
        $user_events = CustomEventUsers::
        where('custom_event_id', $Item->id)
        ->where("confirm_count", ">", 0) 
        ->where("user_id", auth()->user()->id)
        ->get();

        return response()->json([
            'Item' =>  $Item, 
            'user_events' =>  $user_events,  
        ]); 
    } 
    
    public function excel_apologize_count(Request $request, $id){
        
        $Item = CustomEvent::findOrFail($id);
        $user_events = CustomEventUsers::
        where('custom_event_id', $Item->id)
        ->where("apologize_count", ">", 0) 
        ->where("user_id", auth()->user()->id)
        ->get();

        return response()->json([
            'Item' =>  $Item, 
            'user_events' =>  $user_events,  
        ]); 
    } 

    // EVENT
     
    public function excel_all_invited_users(Request $request, $id)
    {
        $Item = Events::findOrFail($id);
        $data = EventUsers::
        where('event_id', $Item->id)
        ->where("user_id", auth()->user()->id)
        ->get();

        $title = 'كل المدعوين';

        $type = 'all_invited_users';


        return response()->json([
            'Item' => $Item, 
            'data' => $data, 
            'title' => $title, 
            'type' => $type, 
        ]);
    }

    public function excel_event_qr_details(Request $request, $id)
    {
        $Item = Events::findOrFail($id);
        $data = EventUsers::where('event_id', $Item->id)
        ->where('scan', 'yes') 
        ->where("user_id", auth()->user()->id)
        ->get();

        $title = 'كل المدعوين الذين اكدو الحضور (QR)';

        $is_qr_page = 'yes';

        $type = 'qr';


        return response()->json([
            'Item' => $Item, 
            'data' => $data, 
            'title' => $title, 
            'is_qr_page' => $is_qr_page, 
            'type' => $type, 
        ]);
    }

    public function excel_confirmed_event_details(Request $request, $id)
    {
        $Item = Events::findOrFail($id);
        $data = EventUsers::where('event_id', $Item->id)
        ->where('status', 'attend')
        ->where("user_id", auth()->user()->id)
        ->get();

        $title = 'كل المدعوين الذين ينوون الحضور';

        $type = 'confirmed_event_details';


        return response()->json([
            'Item' => $Item, 
            'data' => $data, 
            'title' => $title, 
            'type' => $type, 
        ]);
    }

    public function excel_confirmed_users_web_chat(Request $request, $id)
    {
        $Item = Events::findOrFail($id);
        $data = EventUserActions::where('event_id', $Item->id)
        ->where('action', 'accept_event')
        ->with("event_user:id,name,users_count,is_read,scan,scan_count", "event.user") 
        ->whereHas("event_user", function($query){
            $query->where("user_id", auth()->user()->id);
        })
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "event_id" => $item->event_id,
                "event_user_id" => $item->event_user_id,
                "mobile" => $item->mobile,
                "action" => $item->action,
                "msg" => $item->msg,
                "users_count" => $item->users_count,
                "event_user" => $item->event_user,
                "event" => $item?->event?->title,
                "user_name" => $item?->event?->user?->name,
                "user_id" => $item?->event?->user?->id,
            ];
        });

        $title = 'كل المدعوين الذين اكدوا الحضور من الشات الويب';

        $type = 'confirmed_event_details';


        return response()->json([
            'Item' => $Item, 
            'data' => $data, 
            'title' => $title, 
            'type' => $type, 
        ]);
    }

    public function excel_not_attend_event_details(Request $request, $id)
    {
        $Item = Events::findOrFail($id);
        $data = EventUsers::where('event_id', $Item->id)
        ->where('status', 'not-attend')
        ->where("user_id", auth()->user()->id)
        ->get();

        $title = 'كل المدعوين الذين اعتذرو';


        return response()->json([
            'Item' => $Item, 
            'data' => $data, 
            'title' => $title, 
        ]);
    }

    public function excel_hold_event_details(Request $request, $id)
    {
        $Item = Events::findOrFail($id);
        $data = EventUsers::where('event_id', $Item->id)
        ->where('status', 'hold')
        ->where('is_new_sent', 0)
        ->whereNull('is_sent')
        ->where("user_id", auth()->user()->id)
        ->get();

        $title = 'كل المدعوين المنتظرين';

        $type = 'hold';


        return response()->json([
            'Item' => $Item, 
            'data' => $data, 
            'title' => $title, 
            'type' => $type, 
        ]);
    }

  	public function excel_failed_event_details(Request $request, $id)
    {
        $Item = Events::findOrFail($id);

        //$data = EventUsers::where('event_id',$Item->id)->where('status','failed')->get();
        $data = EventUsers::where('event_id', $Item->id)
        //->whereIn('status', ['sent'])
        ->whereNull('is_accepted')
        ->whereNull('is_refused')
        ->where("user_id", auth()->user()->id)
        ->where(function ($query) {
            $query->where('is_new_sent', 1)
                ->orWhereNotNull('is_sent');
        })
        ->get();

        $title = 'لم يتم تاكيد الحضور';

      	$type = 'failed';
 
        return response()->json([
            'Item' => $Item, 
            'data' => $data, 
            'title' => $title, 
            'type' => $type, 
        ]);
    }

  	public function excel_non_attendance_event_details(Request $request, $id)
    {
        $Item = Events::findOrFail($id);

        //$data = EventUsers::where('event_id',$Item->id)->where('status','failed')->get();
        $data = EventUsers::where('event_id', $Item->id)
        ->where('status', 'attend')
        ->whereNull('scan')
        ->whereNull('is_refused')
        ->where("user_id", auth()->user()->id) 
        ->get();

        $title = 'عدم الحضور فعليا';

      	$type = 'non_attendance';
 
        return response()->json([
            'Item' => $Item, 
            'data' => $data, 
            'title' => $title, 
            'type' => $type, 
        ]); 
    }

  	public function excel_qr_sent_event_details(Request $request, $id)
    {
        $Item = Events::findOrFail($id);
        $data = EventUsers::where('event_id', $Item->id)
        ->where('qr_sent', 'yes')
        ->where("user_id", auth()->user()->id)
        ->get();

        $title = 'كل الدعوات (Sent QR)';


 
        return response()->json([
            'Item' => $Item, 
            'data' => $data, 
            'title' => $title,  
        ]); 
    }
}
