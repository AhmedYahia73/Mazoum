<?php

namespace App\Http\Controllers\Api\CustomEvent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Events\ChatEvent;
use App\Events\CustomChatEvent;
use App\Traits\imageTrait;

use App\Models\CustomEventUsers;
use App\Models\CustomChat;
use App\Models\EventUsers;
use App\Models\EventChat;

class ChatController extends Controller
{
    use imageTrait;

    public function custom_users(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'type' => ["required", "in:user,visitor"], 
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $users = CustomEventUsers::
        where("custom_event_id", $id)
        ->whereHas("event", function($query){
            $query->where("user_id", auth()->user()->id);
        })
        ->withCount("un_read_" . $request->type . "_msgs")
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "name" => $item->name,
                "mobile" => $item->mobile,
                "un_read_msgs_count" => $item->un_read_user_msgs_count ?? $item->un_read_vistor_msgs_count,
            ];
        });
        $users = $users->sortByDesc("un_read_msgs_count")->values();

        return response()->json([
            "users" => $users
        ]);
    }

    public function custom_msgs(Request $request, $id){
        $custom_event_user = CustomEventUsers::
        with("event")
        ->findOrFail($id);
        $custom_event = $custom_event_user?->event;
        $chat = CustomChat:: 
        where("custom_user_id", $id)
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "msg" => $item->msg,
                "image" => !empty($item->image) ? url("storage/", $item->image) : null,
                "is_read" => $item->is_read,
                "user_sent" => $item->user_sent,
                "date" => $item->created_at->format("Y-m-d"),
                "time" => $item->created_at->format("h:i:s A"),
            ];
        });

        return response()->json([
            "chat" => $chat,
            "custom_event" => $custom_event,
        ]);
    }

    public function custom_msg_read(Request $request, $custom_user_id){ 
        $chat = CustomChat::
        where("user_id", $request->user()->id)
        ->where("custom_user_id", $custom_user_id)
        ->where("user_sent", true)
        ->update([
            "is_read" => true
        ]);

        return response()->json([
            "success" => "You read msg success"
        ]);
    }

    public function custom_msg_vistor_read(Request $request, $custom_user_id){ 
        $chat = CustomChat:: 
        where("custom_user_id", $custom_user_id)
        ->where("user_sent", false)
        ->update([
            "is_read" => true
        ]);

        return response()->json([
            "success" => "You read msg success"
        ]);
    }

    public function user_send_custom_msg(Request $request){ 
        $validator = Validator::make($request->all(), [
            'msg' => ["sometimes"], 
            'image' => ["sometimes"],
            'custom_user_id' => ["required", "exists:custom_event_users,id"], 
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        if(!$request->msg && !$request->image){
            return response()->json([
                "errors" => "you must enter msg or image"
            ], 400);
        }

        $custom_event_id = CustomEventUsers::
        where("id", $request->custom_user_id)
        ->first()->custom_event_id; 
        $msg = $request->msg ?? null;
        $image = $request->image ?? null;
        if( $request->image){
            $image = $this->upload($request, "image", "custom_chat");
        }
        $custom_chat = CustomChat::create([
            'msg' => $msg,
            'image' => $image,
            'user_id' => $request->user()->id,
            'custom_user_id' => $request->custom_user_id,
            'custom_event_id' => $custom_event_id,
            'user_sent' => true,
            'is_read' => false,
        ]); 
        CustomChatEvent::dispatch($custom_chat);

        return response()->json([
            "success" => "You send msg success"
        ]);
    }

    public function event_user_send_custom_msg(Request $request){ 
        $validator = Validator::make($request->all(), [
            'msg' => ["sometimes"], 
            'image' => ["sometimes"],
            'custom_user_id' => ["required", "exists:custom_event_users,id"], 
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        if(!$request->msg && !$request->image){
            return response()->json([
                "errors" => "you must enter msg or image"
            ], 400);
        }

        $custom_event_user = CustomEventUsers::
        where("id", $request->custom_user_id)
        ->first();
        $custom_event_id = $custom_event_user->custom_event_id;
        $user_id = $custom_event_user->event?->user_id;
        $chat_imges_count = CustomChat::
        where("custom_user_id", $request->custom_user_id)
        ->whereNotNull("image")
        ->where("user_sent", false)
        ->count();
        if($chat_imges_count > 1){
            return response()->json([
                "errors" => "you have limit images 2"
            ], 400);
        }
        $msg = $request->msg ?? null;
        $image = $request->image ?? null;
        if( $request->image){
            $image = $this->upload($request, "image", "custom_chat");
        }
        CustomChat::create([
            'msg' => $msg,
            'image' => $image,
            'user_id' => $user_id,
            'custom_user_id' => $request->custom_user_id,
            'custom_event_id' => $custom_event_id,
            'user_sent' => false,
            'is_read' => false,
        ]);

        return response()->json([
            "success" => "You send msg success"
        ]);
    }
    // _________________________________________________
    
    public function event_users(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'type' => ["required", "in:user,visitor"], 
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $users = EventUsers::
        where("event_id", $id)
        ->whereHas("event", function($query){
            $query->where("user_id", auth()->user()->id);
        })
        ->withCount("un_read_" . $request->type . "_msgs")
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "name" => $item->name,
                "mobile" => $item->code . $item->mobile,
                "un_read_msgs_count" => $item->un_read_user_msgs_count ?? $item->un_read_visitor_msgs_count,
            ];
        });
        $users = $users->sortByDesc("un_read_msgs_count")->values();

        return response()->json([
            "users" => $users
        ]);
    }

    public function event_msgs(Request $request, $id){
        $event_user = EventUsers::
        with("event")
        ->findOrFail($id);
        $event = $event_user?->event;
        $chat = EventChat::
        where("event_user_id", $event_user->id)
        ->where("event_user_id", $id)
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "msg" => $item->msg,
                "image" => !empty($item->image) ? url("storage/", $item->image) : null,
                "is_read" => $item->is_read,
                "user_sent" => $item->user_sent,
                "date" => $item->created_at->format("Y-m-d"),
                "time" => $item->created_at->format("h:i:s A"),
            ];
        });

        return response()->json([
            "chat" => $chat,
            "event" => $event,
        ]);
    }

    public function event_msg_read(Request $request, $event_user_id){ 
        $chat = EventChat::
        where("user_id", $request->user()->id)
        ->where("event_user_id", $event_user_id)
        ->where("user_sent", true)
        ->update([
            "is_read" => true
        ]);

        return response()->json([
            "success" => "You read msg success"
        ]);
    }

    public function event_msg_vistor_read(Request $request, $event_user_id){ 
        $chat = EventChat:: 
        where("event_user_id", $event_user_id)
        ->where("user_sent", false)
        ->update([
            "is_read" => true
        ]);

        return response()->json([
            "success" => "You read msg success"
        ]);
    }

    public function user_send_event_msg(Request $request){ 
        $validator = Validator::make($request->all(), [
            'msg' => ["sometimes"], 
            'image' => ["sometimes"],
            'event_user_id' => ["required", "exists:event_users,id"], 
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        if(!$request->msg && !$request->image){
            return response()->json([
                "errors" => "you must enter msg or image"
            ], 400);
        }

        $event_id = EventUsers::
        where("id", $request->event_user_id)
        ->first()->event_id; 
        $msg = $request->msg ?? null;
        $image = $request->image ?? null;
        if( $request->image){
            $image = $this->upload($request, "image", "event_chat");
        }
        $chat = EventChat::create([
            'msg' => $msg,
            'image' => $image,
            'user_id' => $request->user()->id,
            'event_user_id' => $request->event_user_id,
            // 'event_id' => $event_id,
            'user_sent' => true,
            'is_read' => false,
        ]);
        ChatEvent::dispatch($chat);

        return response()->json([
            "success" => "You send msg success"
        ]);
    }

    public function event_user_send_event_msg(Request $request){ 
        $validator = Validator::make($request->all(), [
            'msg' => ["sometimes"], 
            'image' => ["sometimes"],
            'event_user_id' => ["required", "exists:event_users,id"], 
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        if(!$request->msg && !$request->image){
            return response()->json([
                "errors" => "you must enter msg or image"
            ], 400);
        }

        $event_user = EventUsers::
        where("id", $request->event_user_id)
        ->first();
        $event_id = $event_user->event_id;
        $user_id = $event_user->event?->user_id;
        $chat_imges_count = EventChat::
        where("event_user_id", $request->event_user_id)
        ->where("user_sent", false)
        ->whereNotNull("image")
        ->count();
        if($chat_imges_count > 1){
            return response()->json([
                "errors" => "you have limit images 2"
            ], 400);
        }
        $msg = $request->msg ?? null;
        $image = $request->image ?? null;
        if( $request->image){
            $image = $this->upload($request, "image", "custom_chat");
        }
        $chat = EventChat::create([
            'msg' => $msg,
            'image' => $image,
            'user_id' => $user_id,
            'event_user_id' => $request->event_user_id,
            // 'event_id' => $event_id,
            'user_sent' => false,
            'is_read' => false,
        ]);
        ChatEvent::dispatch($chat);

        return response()->json([
            "success" => "You send msg success"
        ]);
    }
    
}
