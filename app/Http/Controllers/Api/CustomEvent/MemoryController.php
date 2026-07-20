<?php

namespace App\Http\Controllers\Api\CustomEvent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\imageTrait;

use App\Models\Memory;
use App\Models\Qr_Code;
use App\Models\CustomMemory;
use App\Models\CustomEventUsers;
use App\Models\EventUsers;

class MemoryController extends Controller
{
    use imageTrait;

    public function custom_memories($id){
        $custom_event = CustomEventUsers::
        with("event")
        ->findOrFail($id)?->event;

        $memories = CustomMemory::
        where("custom_user_id", $id)
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id, 
                "image_url" => $item->image_url,
                "date" => $item->created_at->format("Y-m-d"),
                "time" => $item->created_at->format("h:i:s A"),
            ];
        });

        return response()->json([
            "memories" => $memories,
            "custom_event" => $custom_event,
        ]);
    }

    public function memories($id){
        $event = Qr_Code::
        where("uu_id", $id)
        ->first()
        ?->event;
        if(!$event){
            return response()->json([
                "errors" => "QR is expired"
            ], 400);
        } 
        $memories = Memory::
        where("event_user_id", $id)
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id, 
                "image_url" => $item->image_url,
                "date" => $item->created_at->format("Y-m-d"),
                "time" => $item->created_at->format("h:i:s A"),
            ];
        });

        return response()->json([
            "memories" => $memories,
            "event" => $event,
        ]);
    }
    
    public function send_custom_memories(Request $request){
        $validator = Validator::make($request->all(), [
            "custom_user_id" => ["required", "exists:custom_event_users,id"],
            "images" => ["required", "array"],
            "images.*" => ["required", "image"],
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $custom_event = CustomEventUsers::
        where("id", $request->custom_user_id)
        ->first(); 
        foreach ($request->images as $item) {
            $image = $this->uploadFile($item, "custom_events/memories");
            $memories = CustomMemory::
            create([
                'custom_event_id' => $custom_event->custom_event_id,
                'custom_user_id' => $request->custom_user_id,
                'image' => $image,
            ]);
        }

        return response()->json([
            "success" => "You add data success",
        ]);
    }

    public function send_memories(Request $request){
        $validator = Validator::make($request->all(), [
            "event_user_id" => ["required", "exists:event_users,id"],
            "images" => ["required", "array"],
            "images.*" => ["required", "image"],
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $event = EventUsers::
        where("id", $request->event_user_id)
        ->first(); 
        foreach ($request->images as $item) {
            $image = $this->uploadFile($item, "events/memories");
            $memories = Memory::
            create([
                'event_id' => $event->event_id,
                'event_user_id' => $request->event_user_id,
                'image' => $image,
            ]);
        }

        return response()->json([
            "success" => "You add data success",
        ]);
    }
}
