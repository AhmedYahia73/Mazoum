<?php

namespace App\Http\Controllers\Api\CustomEvent;

use App\Http\Controllers\Controller;
use App\Models\CustomEvent;
use App\Models\Events;
use App\Models\MobileCodes;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function view(){
        $users = User::where("user_id", auth()->user()->id)
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "mobile_code" => $item->mobile_code,
                "mobile" => $item->mobile,
                "name" => $item->name, 
                "custom_invetaion" => $item->custom_invetaion,
            ];
        });

        return response()->json([
            "users" => $users
        ]);
    } 
    
    public function custom(){
        $users = User::where("user_id", auth()->user()->id)
        ->whereNotNull("custom_event_id")
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "mobile_code" => $item->mobile_code,
                "mobile" => $item->mobile,
                "name" => $item->name,
                "custom_invetaion" => $item->custom_invetaion,
                "send_custom_invetaion" => $item->send_custom_invetaion,
                "total" => $item->custom_invetaion + $item->send_custom_invetaion,
            ];
        });

        return response()->json([
            "users" => $users
        ]);
    }

    public function event(){
        $users = User::where("user_id", auth()->user()->id)
        ->whereNotNull("event_id")
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "mobile_code" => $item->mobile_code,
                "mobile" => $item->mobile,
                "name" => $item->name, 
                "custom_invetaion" => $item->custom_invetaion,
                "send_custom_invetaion" => $item->send_custom_invetaion,
                "total" => $item->custom_invetaion + $item->send_custom_invetaion,
            ];
        });

        return response()->json([
            "users" => $users
        ]);
    }

    public function lists(){
        $codes = MobileCodes::get(['id','ar_country_name','code']);
        return response()->json([
            "codes" => $codes
        ]);
    }

    public function create(Request $request){
        $validator = Validator::make($request->all(), [
          'mobile_code' => 'required|exists:mobile_codes,id',
          'mobile' => 'required',
          'name' => 'required',
          'custom_invetaion' => 'required|numeric',
          "custom_event_id" => "exists:custom_event,id",
          "event_user_id" => "exists:events,id",
          "password" => "required"
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        if(!$request->custom_event_id && !$request->event_user_id){
            return response()->json([
                "errors" => "custom_event_id or event_id is required"
            ]);

        }
        $user_id = 0;
        if($request->event_user_id){ 
            $user_id = Events::
            where("id", $request->event_user_id)
            ->first()->user_id;
        }
        if($request->custom_event_id){ 
            $user_id = CustomEvent::
            where("id", $request->custom_event_id)
            ->first()->user_id;
        }
        $user = User::
        where("id", auth()->user()->id)
        ->first();
        $available = $user->custom_invetaion - $user->send_custom_invetaion;
        $user_custom_invetaion = $user->custom_invetaion - $request->custom_invetaion;
        if($user_custom_invetaion < 0){
            return response()->json([
                "errors" => "أنت لا تملتلك كل هذه الدعوات"
            ], 400);
        }
        $user->custom_invetaion -=  $user_custom_invetaion;
        $user->balance -= $user_custom_invetaion;
        $user->save();
        User::create([
            "mobile_code" => $request->mobile_code,
            "mobile" => $request->mobile,
            "name" => $request->name,
            "custom_invetaion" => $request->custom_invetaion,
            "user_id" => $user_id,
            "custom_event_id" => $request->custom_event_id ?? null,
            "event_id" => $request->event_user_id ?? null,
            "password" => Hash::make($request->password),
            "balance" => $request->custom_invetaion
        ]);

        return response()->json([
            "success" => "You add data success"
        ]);
    }

    public function update(Request $request, $id){
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
