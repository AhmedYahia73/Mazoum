<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Memory;
use App\Models\CustomMemory;

class MemoryController extends Controller
{
    public function event_memory(Request $request){
        $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $memories = Memory::
        where("event_id", $request->event_id)
        ->with("user")
        ->paginate(20)
        ->through(function($item){
            return [
                "id" => $item->id,
                "image" => $item->image_url,
                "name" => $item?->user?->name,
                "mobile" => $item?->user?->mobile,
            ];
        });

        return response()->json([
            "memories" => $memories
        ]);
    }
    
    public function event_user_memory(Request $request){
        $validator = Validator::make($request->all(), [
            'event_user_id' => 'required|exists:event_users,id',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $memories = Memory::
        where("event_user_id", $request->event_user_id)
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "image" => $item->image_url, 
            ];
        });

        return response()->json([
            "memories" => $memories
        ]);
    }
    
    public function custom_event_memory(Request $request){
        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $memories = CustomMemory::
        where("custom_event_id", $request->custom_event_id)
        ->with("user")
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "image" => $item->image_url,
                "name" => $item?->user?->name,
                "mobile" => $item?->user?->mobile,
            ];
        });

        return response()->json([
            "memories" => $memories
        ]);
    }
    
    public function custom_user_memory(Request $request){
        $validator = Validator::make($request->all(), [
            'custom_user_id' => 'required|exists:custom_event_users,id',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $memories = CustomMemory::
        where("custom_user_id", $request->custom_user_id)
        ->with("user")
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "image" => $item->image_url, 
            ];
        });

        return response()->json([
            "memories" => $memories
        ]);
    }
}
