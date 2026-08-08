<?php
namespace App\Http\Controllers\Api\CustomEvent;

use App\Http\Controllers\Controller;
use App\Models\CustomChat;
use App\Models\CustomEvent as Model;
use App\Models\CustomEventFamily;
use App\Models\CustomEventUsers;
use App\Models\CustomMemory;
use App\Models\EnterUserCustomEvent;
use App\Models\EnterUserEvent;
use App\Models\EventChat;
use App\Models\EventUsers;
use App\Models\Memory;
use App\Models\Negotaition;
use App\Models\Orders;
use App\Models\Payment;
use App\Models\Pricing;
use App\Models\Qr_Code;
use App\Models\User;
use App\Traits\imageTrait;
use Carbon\Carbon;
use chillerlan\QRCode\QRCode as QRCodeRead;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManagerStatic as Image;
use SimpleSoftwareIO\QrCode\Facades\QrCode; 

class PackageController extends Controller
{
    use imageTrait;

    public function view(Request $request){
        $validator = Validator::make($request->all(), [
          'locale' => 'required|in:en,ar'
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $locale = $request->locale;
        $packages = Pricing:: 
        get();

        return response()->json([
            "packages" => $packages
        ]);
    }

    public function custom_template($id){
        $custom_event_user = CustomEventUsers::where("id", $id)
        ->firstOrFail();
        $event = $custom_event_user->event;
        $day_name   = Carbon::parse($event->date)->locale('ar')->translatedFormat('l');
        $invitation_text = 
                        $custom_event_user->name . "\n"
                        . "بارك الله لهما وجمع بينهم في خير .\n"
                        . "وذلك بمشيئة الله تعالى يوم " . $day_name . "\n"
                        . "الموافق " . $event->date . "\n"
                        . "وقت الاستقبال " . $event->time . " مساءًًً\n"
                        . "مكان الحفل " . $event->address . "\n"
                        . "عدد الدعوات " . $custom_event_user->users_count;

        // لطباعة النص كما هو في الـ Terminal أو داخل ملف نصي
        
        return response()->json([
            "invitation_text" => $invitation_text
        ]); 
    }

    public function event_template($id){
        $event_user = EventUsers::where("id", $id)
        ->firstOrFail();
        $event = $event_user->event;
        $day_name   = Carbon::parse($event->date)->locale('ar')->translatedFormat('l');
        $invitation_text = 
                        $event_user->name . "\n"
                        . "بارك الله لهما وجمع بينهم في خير .\n"
                        . "وذلك بمشيئة الله تعالى يوم " . $day_name . "\n"
                        . "الموافق " . $event->date . "\n"
                        . "وقت الاستقبال " . $event->time . " مساءًًً\n"
                        . "مكان الحفل " . $event->address . "\n"
                        . "عدد الدعوات " . $event_user->users_count;

        // لطباعة النص كما هو في الـ Terminal أو داخل ملف نصي
        
        return response()->json([
            "invitation_text" => $invitation_text
        ]); 
    }

    public function custom_details($id){
        $custom_event_user = CustomEventUsers::where("id", $id)
        ->firstOrFail();
        $event = $custom_event_user->event;

        return response()->json([
            "event" => $event
        ]); 
    }

    public function event_details($id){
        $event_user = EventUsers::where("id", $id)
        ->firstOrFail();
        $event = $event_user->event;
        
        return response()->json([
            "event" => $event
        ]); 
    }

    public function negotaition(Request $request){
        $validator = Validator::make($request->all(), [
            'package_id' => 'required|exists:pricing,id',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $negotaition = Negotaition::
        create([
            "pricing_id" => $request->package_id,
            "user_id" => $request->user()->id,
        ]); 

        return response()->json([
            "success" => "You negotaition success"
        ]);
    }

    public function negotations_history(Request $request){
        $validator = Validator::make($request->all(), [
            'lang' => 'required|in:en,ar',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $lang = $request->lang;
        $negotations = Negotaition::
        where("user_id", auth()->user()->id)
        ->with("package")
        ->orderByDesc("id")
        ->get()
        ->map(function($item) use($lang){
            return [
                "id" => $item->id,
                "status" => $item->status,
                "title" => $item?->package?->{$lang . "_title"},
                "package_price" => $item?->package?->price,
                "created_at" => $item->created_at,
            ];
        });

        return response()->json([
            "negotations" => $negotations
        ]);
    }

    public function orders_list(){
        $orders = Orders::
        where("user_id", auth()->user()->id)
        ->with("currency")
        ->where("is_paid", "not_paid")
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "duration" => $item->duration,
                "duration_type" => $item->duration_type,
                "operation_date" => $item->operation_date,
                "order_number" => $item->order_number,
                "payment_type" => $item->payment_type,
                "is_paid" => $item->is_paid,
                "total" => $item->total,
                "type" => $item->type,
                "users_count" => $item->users_count,
                "type" => $item->type,
                "payment_method" => $item->payment_method,
                "payment_type" => $item->payment_type,
                "payment_description" => $item->payment_description,
            ];
        });


        return response()->json([
            "orders" => $orders
        ]);
    }

    public function orders_history(){
        $orders = Orders::
        where("user_id", auth()->user()->id)
        ->with("currency")
        ->where("is_paid", "paid")
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "duration" => $item->duration,
                "duration_type" => $item->duration_type,
                "operation_date" => $item->operation_date,
                "order_number" => $item->order_number,
                "payment_type" => $item->payment_type,
                "is_paid" => $item->is_paid,
                "total" => $item->total,
                "type" => $item->type,
                "users_count" => $item->users_count,
                "type" => $item->type,
                "payment_method" => $item->payment_method,
                "payment_type" => $item->payment_type,
                "payment_description" => $item->payment_description,
            ];
        });

        return response()->json([
            "orders" => $orders
        ]);
    }

    public function payment(Request $request){
        $validator = Validator::make($request->all(), [
          'order_id' => 'required|exists:orders,id',
          "receipt" => 'required',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $receipt = $this->upload($request, "receipt", "payments");
        $order = Orders::
        where("id", $request->order_id)
        ->first();
        Payment::create([
            "user_id" => $request->user()->id, 
            "currency_id" => $order->currency_id, 
            "price" => $order->total,
            "order_id" => $order->id,
            "receipt" => $receipt,
        ]);

        return response()->json([
            "success" => "You pay success"
        ]);
    }

    public function my_custom_events(Request $request){
        $my_custom_events = Model::where("user_id", auth()->user()->id)
        ->orWhereHas("sub_user", function($query){
            $query->where("users.id", auth()->user()->id);
        })
        ->get();

        return response()->json([
            "my_custom_events" => $my_custom_events
        ]);
    }
 
    public function event_users(Request $request, $id)
    {
        $Item = Model::
        where("user_id", auth()->user()->id)
        ->orWhereHas("sub_user", function($query){
            $query->where("users.id", auth()->user()->id);
        })
        ->findOrFail($id);
        // $user_events = CustomEventUsers::
        // where('custom_event_id', $Item->id)
        // ->when($request->search, function ($q) use ($request) { 
        //     $search = $request->search; 
        //     $q->where(function ($sub) use ($search) {
        //         $sub->where('name', 'like', "%$search%")
        //             ->orWhere('mobile', 'like', "%$search%");
        //     });
        // })
        // ->get()
        // ->map(function($item){
        //     return [
        //         "id" => $item->id,
        //         "name" => $item->name,
        //         "mobile" => $item->mobile,
                
        //         "invetations" => $item->users_count,
        //         "send_qr" => $item->send_qr ? $item->users_count : 0,
        //         "attendance" => $item->scan_count,
        //         "waiting" => $item->users_count - $item->scan_count,
        //     ];
        // });
        $perPage = 10;

        $invetations_list = CustomEventUsers::
        where('custom_event_id',$Item->id)
        ->paginate($perPage)
        ->through(function($item) use($Item){
            return [
                "id" => $item->id,
                "name" => $item->name,
                "mobile" => $item->mobile,
                "can_send_qr" => !$item->send_qr || (!$item->resend_qr && $Item->resend_qr),
                "count" => $item->users_count, 
                "scan_status" => $item->users_count > $item->scan_count,
            ];
        }); 

        $send_qr_list = CustomEventUsers::
        where('custom_event_id',$Item->id)
        ->where("send_qr", 1)
        ->paginate($perPage)
        ->through(function($item) use($Item){
            return [
                "id" => $item->id,
                "name" => $item->name,
                "mobile" => $item->mobile,
                "can_send_qr" => !$item->send_qr || (!$item->resend_qr && $Item->resend_qr),
                "count" => $item->users_count,
                "scan_status" => $item->users_count > $item->scan_count,
            ];
        });

        $attendance_list = CustomEventUsers::
        where('custom_event_id',$Item->id)
        ->where("scan_count", ">", 0)
        ->paginate($perPage)
        ->through(function($item) use($Item){
            return [
                "id" => $item->id,
                "name" => $item->name,
                "mobile" => $item->mobile, 
                "can_send_qr" => !$item->send_qr || (!$item->resend_qr && $Item->resend_qr),
                "count" => $item->scan_count,
                "scan_status" => $item->users_count > $item->scan_count,
            ];
        });

        $waiting_list = CustomEventUsers::
        where('custom_event_id',$Item->id)
        ->whereColumn("users_count", ">", "scan_count")
        ->paginate($perPage)
        ->through(function($item) use($Item){
            return [
                "id" => $item->id,
                "name" => $item->name,
                "mobile" => $item->mobile, 
                "can_send_qr" => !$item->send_qr || (!$item->resend_qr && $Item->resend_qr),
                "count" => $item->users_count - $item->scan_count,
                "scan_status" => $item->users_count > $item->scan_count,
            ];
        });
        $invetations = CustomEventUsers::
        where('custom_event_id',$Item->id)
        ->sum('users_count');
        $send_qr = CustomEventUsers::
        where('custom_event_id',$Item->id)
        ->where("send_qr", 1)
        ->sum('users_count');
        $attendance = CustomEventUsers::
        where('custom_event_id',$Item->id)
        ->sum('scan_count');
        $Item->invetations = $invetations;
        $Item->attendance = $attendance;
        $waiting = $invetations - $send_qr;
        $Item->waiting = $waiting;
        $Item->send_qr = $send_qr;

        $invetation_data = $invetations_list->toArray();
        $invetation_data['total_users'] = $invetations;
        $send_qr_data = $send_qr_list->toArray();
        $send_qr_data['total_users'] = $send_qr;
        $attendance_data = $attendance_list->toArray();
        $attendance_data['total_users'] = $attendance;
        $waiting_data = $waiting_list->toArray();
        $waiting_data['total_users'] = $waiting;

        return response()->json([
            'Item' =>  $Item, 
            'invetations' =>  $invetations, 
            'attendance' =>  $attendance, 
            'waiting' =>  $waiting, 
            'send_qr' =>  $send_qr, 

            'invetations_list' =>  $invetation_data, 
            'send_qr_list' =>  $send_qr_data, 
            'attendance_list' =>  $attendance_data, 
            'waiting_list' =>  $waiting_data, 

        ]); 
    }

    public function attend_custom_event(Request $request, $id){
        

        $validator = Validator::make($request->all(), [
            'scan_count' => 'required|numeric',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }  
        $attendance = CustomEventUsers::
        findOrFail($id);
        if($request->scan_count + $attendance->scan_count > $attendance->users_count){
            return response()->json([
                "errors" => "Scan count exceeds the number of invitations"
            ], 400); 
        }
        $user_data = auth()->user();
        $available = $user_data->custom_invetaion - $user_data->send_custom_invetaion;
        if($request->users_count >= $available){
            return response()->json([
                "errors" => "لا تمتلك كل هذا العدد من الدعوات تم ارسال البعض و ليس الكل"
            ], 400);
        } 
        $user_data->send_custom_invetaion += $request->users_count;
        $user_data->save();
        $attendance->update([
            "scan_count" => $request->scan_count + $attendance->scan_count,
            'scan' => 'yes',
            'scan_at' => now(),
        ]);
        EnterUserCustomEvent::create([
            "custom_user_id" => $id,
            "count" => $request->scan_count
        ]);
        
        return response()->json([
            "success" => "You attend success"
        ]);
    }

    public function attend_event(Request $request, $id){
        

        $validator = Validator::make($request->all(), [
            'scan_count' => 'required|numeric',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }  
        $attendance = EventUsers::
        findOrFail($id);
        if($request->scan_count + $attendance->scan_count > $attendance->users_count){
            return response()->json([
                "errors" => "Scan count exceeds the number of invitations"
            ], 400); 
        }
        $user_data = auth()->user();
        $available = $user_data->custom_invetaion - $user_data->send_custom_invetaion;
        if($request->users_count >= $available){
            return response()->json([
                "errors" => "لا تمتلك كل هذا العدد من الدعوات تم ارسال البعض و ليس الكل"
            ], 400);
        } 
        $user_data->send_custom_invetaion += $request->users_count;
        $user_data->save();
        $attendance->update([
            "scan_count" => $request->scan_count + $attendance->scan_count,
            'scan' => 'yes',
            'scan_at' => now(),
        ]);
        EnterUserEvent::create([
            "event_user_id" => $id,
            "count" => $request->scan_count
        ]);
        
        return response()->json([
            "success" => "You attend success"
        ]);
    }

    public function event_open_users(Request $request, $id){
        $enter_event = EnterUserEvent::
        where("event_user_id", $id)
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "count" => $item->count,
                "date" => $item->created_at->format("Y-m-d"),
                "time" => $item->created_at->format("H:i A"),
            ];
        });

        return response()->json([
            "enter_event" => $enter_event
        ]);
    }

    public function custom_open_users(Request $request, $id){
        $enter_event = EnterUserCustomEvent::
        where("custom_user_id", $id)
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "count" => $item->count,
                "date" => $item->created_at->format("Y-m-d"),
                "time" => $item->created_at->format("H:i A"),
            ];
        });

        return response()->json([
            "enter_event" => $enter_event
        ]);
    }
    
     // save_event_users
    public function save_event_users(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id',
            'event_users' => 'required|array',
            'event_users.*.name' => 'required',
          	'event_users.*.users_count' => 'required|numeric|min:1',
          	'event_users.*.mobile' => 'sometimes',
          	'event_users.*.mobile' => 'sometimes',
          	'event_users.*.suit_num' => 'sometimes|numeric',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }   

        $custom_event_id = $request->custom_event_id;

        $event = Model::where('id', $custom_event_id)
        ->where("user_id", auth()->user()->id)      
        ->orWhereHas("sub_user", function($query){
            $query->where("users.id", auth()->user()->id);
        }) 
        ->firstOrFail();

        if($request->event_users != null && ! empty($request->event_users)) {

            foreach ($request->event_users as $arr) {

                if($arr['name'] != null && $arr['users_count'] != null && is_numeric($arr['users_count'])) {

                    $uu_id = uniqid();

                    $row = CustomEventUsers::create([
                        'custom_event_id' => $custom_event_id,
                        'name' => $arr['name'],
                        'users_count' => $arr['users_count'],
                        'mobile' => isset($arr['mobile']) ? $arr['mobile'] : null,
                        'uu_id' => $uu_id,
                        "user_id" => auth()->user()->id,
                        'suit_num' => isset($arr['suit_num']) ? (int) $arr['suit_num'] : 0,
                    ]);

                $this->update_qr($row->event,$row->uu_id, $row, $row->event?->image);
                }
            }

        }

        return response()->json([
            'success' =>  'تم الحفظ بنجاح', 
        ]);  

    }

    public function event_visitors(Request $request, $id)
    { 
        $event_users = CustomEventUsers::
        select("id", "mobile", "name", "scan_count", "users_count", "qr")
        ->where('custom_event_id', $id)
        ->when($request->search, function ($q) use ($request) {

            $search = $request->search;

            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
            });
        })
        ->paginate(15);

        return response()->json([ 
            'event_users' =>  $event_users, 
        ]);  
    }
    
    public function event_visitor_item(Request $request, $id)
    { 
        $event_user = CustomEventUsers::
        select("id", "mobile", "name", "scan_count", "users_count")
        ->where('id', $id)
        ->first();

        return response()->json([ 
            'event_user' =>  $event_user, 
        ]);  
    }

     // update_event_users
    public function update_event_users(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_users' => 'required|array',
            'event_users.*.id' => 'required',
            'event_users.*.name' => 'required',
            'event_users.*.users_count' => 'required|numeric|min:0',
            'event_users.*.mobile' => 'nullable|numeric',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        } 

        if($request->event_users != null && ! empty($request->event_users)) {

            foreach ($request->event_users as $key => $arr) {
                $id = $request->event_users[$key]['id'];
                $row = CustomEventUsers::
                where('id', $id)
                ->whereHas("event", function($query){
                    $query->where("user_id", auth()->user()->id)
                    ->orWhereHas("sub_user", function($query){
                        $query->where("users.id", auth()->user()->id);
                    });
                })
                ->firstOrFail();

                if($row != null && $arr['name'] != null && $arr['users_count'] != null && is_numeric($arr['users_count'])) {

                    $users_count = $arr['users_count'] - $row->users_count;
                    $user = User::
                    where("id", $row->event->user_id)
                    ->first();

                    $uu_id = uniqid();

                  	$row->update([
                        'name' => $arr['name'],
                        'users_count' => $arr['users_count'],
                        'mobile' => isset($arr['mobile']) ? $arr['mobile'] : null,
                        'uu_id' => $uu_id,
                        'suit_num' => isset($arr['suit_num']) ? (int) $arr['suit_num'] : $row->suit_num,
                    ]); 
                }
            }
        }

        return response()->json([
            'success' =>  'تم التحديث بنجاح', 
        ]);  

    }

    public function destroy_user($id)
    {
        $Item = CustomEventUsers::
        whereHas("event", function($query){
            $query->where("user_id", auth()->user()->id)
            ->orWhereHas("sub_user", function($query){
                $query->where("users.id", auth()->user()->id);
            });
        })
        ->findOrFail($id);
        $Item->delete();

        return response()->json([
            'success' =>  'تم حذف البيانات بنجاح', 
        ]); 
    } 
    // ___________________________________________________
     
    public function send_invitations(Request $request, $id)
    {
        $Item = Model::where('id', $id)
        ->where(function ($query) {
            $query->where('user_id', auth()->id())
                ->orWhereHas('sub_user', function ($subQuery) {
                    $subQuery->where('users.id', auth()->id());
                });
        })
        ->firstOrFail();
        $event_users = CustomEventUsers::where('custom_event_id', $id)
        ->when($request->search, function ($q) use ($request) {

            $search = $request->search;

            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
            });
        })
        ->paginate(15);

        return response()->json([
            'Item' =>  $Item, 
            'event_users' =>  $event_users, 
        ]); 
    }
 
    public function new_send_event_invitation(Request $request) {
        $validator = Validator::make($request->all(), [
        	'custom_event_id' => 'required|exists:custom_event,id',
            'users' => 'required|array',
            'users.*' => 'required|exists:custom_event_users,id',
        ]); 
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }


      	$event = Model::where('id',$request->custom_event_id)
        ->where("user_id", auth()->user()->id)
        ->orWhereHas("sub_user", function($query){
            $query->where("users.id", auth()->user()->id);
        })
        ->where('id',$request->custom_event_id)
        ->firstOrFail(); 

        $ultramsg_token="7ye6ifujyug0u46g"; // Ultramsg.com token
        $instance_id="instance109805"; // Ultramsg.com instance id
        $client = new \UltraMsg\WhatsAppApi($ultramsg_token,$instance_id);

        $priority=0;
        $referenceId="SDK";
        $nocache=true;

        if($request->users != null && ! empty($request->users)) {

            $error_count = 0;

          	foreach($request->users as $item) {

              if(isset($item)) {

                $row = CustomEventUsers::
                where('id',$item)
                ->with("event")
                ->first();
                // __________________________________________________________________________________
                if(!$event->resend_qr && $row->send_qr || $event->resend_qr && $row->resend_qr){
                    continue;
                }
                if($row->send_qr ){
                    $row->resend_qr = true;
                    $row->save();
                }
                $users_count = $row->users_count;
                $available = auth()->user()->custom_invetaion - auth()->user()->send_custom_invetaion;
            
                // __________________________________________________________________________________
                $this->update_qr($row->event,$row->uu_id, $row, $row->event?->image);
              
                // __________________________________________________________________________________
                if($row != null && $row->mobile != null && $event != null) {

                  $to = $row->mobile;

                  $image = $row->qr;

                  $day_name   = Carbon::parse($event->date)->locale('ar')->translatedFormat('l');

                  $caption = $row->name . PHP_EOL . PHP_EOL .
                    $event->title . PHP_EOL . PHP_EOL .
                    "وذلك بمشيئة الله يوم " . $day_name ." الموافق" . PHP_EOL . PHP_EOL .
                    $event->date . " 📆" . PHP_EOL . PHP_EOL .
                    "⏱️الساعـة " . $event->time . " مساءًًاً" . PHP_EOL . PHP_EOL .
                    "📍مكان الحفـل " . $event->address ;

                  // $api=$client->sendChatMessage($to,$body);
                  $api = $client->sendImageMessage($to,$image,$caption,$priority,$referenceId,$nocache);

                //   dd($api);

                  if(! empty($api) && isset($api['sent']) && $api['sent'] == 'true'  && isset($api['message']) && $api['message'] == 'ok') {

                    // dd('ok');
                    // $row->update(['is_new_sent' => 1]);

                        $row->send_qr = 1;
                        $row->save();
                  } else {

                    $error_count = $error_count + 1;

                    // dd('not ok',$api);
                    // $row->update(['is_new_sent' => 0]);
                  }

                }

              }

            }

            if($error_count == 0) {
                return response()->json([
                    'success' =>  'تم ارسال الرسائل بنجاح', 
                ]);  
            } else {
                return response()->json([
                    'errors' =>  'عفوا فشل ارسال ' .$error_count . ' أرقام ', 
                ], 400);  
            }

        } else {
            return response()->json([
                'errors' =>  'من فضلك اختر عنصر واحد علي الاقل', 
            ], 400);  
        }

    }
 
    public function share_custom_invitation_watts(Request $request) {
        $validator = Validator::make($request->all(), [
        	'custom_event_id' => 'required|exists:custom_event,id',
            'users_id' => 'required|exists:custom_event_users,id',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        } 
      	$event = Model::where('id',$request->custom_event_id)
        ->where("user_id", auth()->user()->id)
        ->orWhereHas("sub_user", function($query){
            $query->where("users.id", auth()->user()->id);
        })
        ->where('id',$request->custom_event_id)
        ->firstOrFail();
        $event_user = CustomEventUsers::
        where('id',$request->users_id)
        ->first();
        // __________________________________________________________________________________
        if(!$event->resend_qr && $event_user->send_qr || $event->resend_qr && $event_user->resend_qr){
            return response()->json([
                "errors" => "لقد ارسلت الدعوة سابقا"
            ], 400);
        } 
        $event_user->send_qr = true;
        $event_user->save();

        return response()->json([
            "success" => "You send qr success"
        ]);
    }

    public function create_qr(Request $request){
        $validator = Validator::make($request->all(), [
            'custom_event_user_id' => 'required|exists:custom_event_users,id',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
 
        $event_user = CustomEventUsers::
        where('id',$request->custom_event_user_id)
        ->with("event")
        ->first();
        if($event_user->send_qr ){
            $event_user->resend_qr = true;
            $event_user->save();
        }
        $users_count = $event_user->users_count;
        $available = auth()->user()->custom_invetaion - auth()->user()->send_custom_invetaion;
       
        // __________________________________________________________________________________
        $this->update_qr($event_user->event,$event_user->uu_id, $event_user, $event_user->event?->image);
        // __________________________________________________________________________________
     
        // __________________________________________________________________________________
        $event_user->send_qr = 1;
        $event_user->save();
    
        return response()->json([
            "success" => "You create qr success"
        ]);
    }
 
    // _______________________________________________

  	public function event_family_search(Request $request) {

        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id'
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }   
        $event_id = $request->custom_event_id;

        $event_users = CustomEventFamily::where('event_id', $event_id)
            ->when($request->search, function ($q) use ($request) {
                $search = $request->search;

                $q->where(function ($q2) use ($search) {
                    $q2->where('name', 'like', "%$search%")
                    ->orWhere('mobile', $search);
                });
            })
            ->paginate(15); // عدد النتائج في الصفحة

        return response()->json([
            'event_users' => $event_users,
            'event_id' => $event_id,
        ]);


    }
    
    public function save_event_family(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'custom_event_id' => 'required|exists:custom_event,id',
            'event_users' => 'required',
            'event_users.*.id' => 'required|exists:event_users,id',
            'event_users.*.name' => 'required',
            'event_users.*.mobile' => 'nullable|numeric',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $event_id = $request->custom_event_id;

        $event = Model::where('id', $event_id)
        ->where("user_id", auth()->user()->id)
        ->orWhereHas("sub_user", function($query){
            $query->where("users.id", auth()->user()->id);
        })
        ->where('id', $event_id)
        ->firstOrFail();

        if($request->event_users != null && ! empty($request->event_users)) {

            foreach ($request->event_users as $arr) {
                if($arr['name'] != null) {

                  CustomEventFamily::create([
                    'event_id' => $event_id,
                    'name' => $arr['name'],
                    'mobile' => isset($arr['mobile']) ? ltrim($arr['mobile'],"+") : null,
                    'scan_qr' => 'no'
                  ]);
                }
            }

        }

        return response()->json([
            'success' =>  'تم الحفظ بنجاح', 
        ]);  

    }

  	public function open_event_family($id) {

        $user_event = CustomEventFamily::findOrFail($id);

        $user_event->update(['scan_qr' => 'yes']);

        return response()->json([
            'success' =>  'تم دخول الحفل بنجاح', 
        ]);  

    }

  	public function delete_event_family($id) {

        $user_event = CustomEventFamily::find($id);

        if($user_event != null) {
          $user_event->delete();
        }

        return response()->json([
            'success' =>  'تم الحذف بنجاح', 
        ]); 

    }
    //__________________________________________
    

    public function event_report($id)
    {
        $Item = Model::
        where("user_id", auth()->user()->id)
        ->orWhereHas("sub_user", function($query){
            $query->where("users.id", auth()->user()->id);
        })
        ->findOrFail($id);
        $visitors_count = CustomEventUsers::
        where('custom_event_id',$Item->id)
        ->sum('users_count');
        $qr_count = CustomEventUsers::
        where('custom_event_id',$Item->id)
        ->where('scan','yes')
        ->sum('scan_count');
        return response()->json([
            'Item' =>  $Item, 
            'visitors_count' =>  $visitors_count, 
            'qr_count' =>  $qr_count, 
        ]); 
    }

    private function update_qr($event,$uu_id,$user_event,$image_name, $status = false) {

        $color = $this->hexToRgb($event->color);

        $name_qr      = $event->name_qr;
        $number_qr    = $event->number_qr;
        $qr_height    = $event->qr_height;
        $qr_width     = $event->qr_width;
        $qr_x         = $event->qr_x;
        $qr_y         = $event->qr_y;
        $image_height = $event->image_height;
        $image_width  = $event->image_width;
        $text_color   = $event->text_color ?: '#000';

        if (!$status && $event->getRawOriginal('image') != null && file_exists(public_path('images/' . $event->getRawOriginal('image')))) {

            $image_name  = $uu_id . '-test-qr.png';
            $link        = asset('scan-qr/' . $uu_id);
            $qr_tmp_path = public_path('qr_code/tmp_' . $image_name);
            $final_path  = public_path('qr_code/' . $image_name);

            $qr_size = ($qr_width > 0 && $qr_height > 0) ? $qr_width : 300;

            QrCode::format('png')
                ->size($qr_size)
                ->color($color[0], $color[1], $color[2])
                ->backgroundColor(0, 0, 0, 0)
                ->generate($link, $qr_tmp_path);

            $background = Image::make(public_path('images/' . $event->getRawOriginal('image')));

            if ($image_width > 0 && $image_height > 0) {
                $background->resize($image_width, $image_height);
            }

            $qr = Image::make($qr_tmp_path);

            // if ($qr_width > 0 && $qr_height > 0) {
            //     $qr->resize($qr_width, $qr_height);
            // }

            // // origin: bottom-right — qr_x/qr_y = pixels from bottom-right corner
            // if ($qr_x > 0 || $qr_y > 0) {
            //     $x = $background->width()  - $qr->width()  - $qr_x;
            //     $y = $background->height() - $qr->height() - $qr_y;
            // } else {
            //     $x = intval(($background->width()  - $qr->width())  / 2);
            //     $y = intval(($background->height() - $qr->height()) / 2);
            // }

            // $background->insert($qr, 'top-left', $x, $y);
            
            if ($qr_width > 0 && $qr_height > 0) {
                $qr->resize($qr_width, $qr_height);
            }

            // تعديل نقطة البداية (Origin) لتكون Top-Left مباشرة
            if ($qr_x > 0 || $qr_y > 0) {
                $x = $qr_x; 
                $y = $qr_y;
            } else {
                $x = intval(($background->width()  - $qr->width())  / 2);
                $y = intval(($background->height() - $qr->height()) / 2);
            }

            $background->insert($qr, 'top-left', $x, $y);

            $center_x = intval($background->width() / 2);
            $text_y   = $y + $qr->height() + 15;

            if ($event->language == 'ar') {
                $Arabic    = new \ArPHP\I18N\Arabic('Glyphs');
                $font_path = base_path('resources/fonts/DroidArabicKufiRegular.ttf');
                $name      = $Arabic->utf8Glyphs($user_event->name);
                $Arabic2   = new \ArPHP\I18N\Arabic('Glyphs');
                $name2     = $Arabic2->utf8Glyphs('عدد الضيوف ' . $user_event->users_count);
                if($user_event->suit_num && $user_event->suit_num != 0){
                    $Arabic3   = new \ArPHP\I18N\Arabic('Glyphs');
                    $name3     = $Arabic3->utf8Glyphs('رقم الكرسى ' . $user_event->suit_num);
                }
            } else {
                $font_path = public_path('font/LuxuriousRoman-Regular.ttf');
                $name      = $user_event->name;
                $name2     = 'Entered Users ' . $user_event->users_count;
                if($user_event->suit_num && $user_event->suit_num != 0){
                    $name3     = "Suit Num " . $user_event->suit_num;
                }
            }

            if ($name_qr) {
                $background->text($name, $center_x, $text_y, function ($font) use ($font_path, $text_color) {
                    $font->file($font_path);
                    $font->size(20);
                    $font->color($text_color);
                    $font->align('center');
                    $font->valign('top');
                });
                $text_y += 25;
            }

            if ($number_qr && $user_event->users_count > 1) {
                $background->text($name2, $center_x, $text_y, function ($font) use ($font_path, $text_color) {
                    $font->file($font_path);
                    $font->size(20);
                    $font->color($text_color);
                    $font->align('center');
                    $font->valign('top');
                });
                $text_y += 25;
            }

            if (isset($name3)) {
                $background->text($name3, $center_x, $text_y, function ($font) use ($font_path, $text_color) {
                    $font->file($font_path);
                    $font->size(20);
                    $font->color($text_color);
                    $font->align('center');
                    $font->valign('top');
                }); 
            }

            $background->save($final_path, 100);

            if (file_exists($qr_tmp_path)) {
                unlink($qr_tmp_path);
            }

        } 
        else {
            // ==========================================
            // 1. إعدادات المسارات
            // ==========================================
            $bg           = public_path('qr-image-v10.jpg'); // تأكد من اسم صورة الخلفية الفارغة
            $link         = asset('scan-qr/' . $uu_id);
            $qr_tmp_name  = 'tmp_qr_' . time() . '.png';
            $qr_tmp_path  = public_path('qr_code/' . $qr_tmp_name);
            $final_path   = public_path('qr_code/' . $image_name);

            // ==========================================
            // 2. إعدادات الخطوط
            // ==========================================
            // خط العناوين العربية
            $arabic_font = public_path('font/DroidArabicKufiRegular.ttf');
            if (!file_exists($arabic_font)) {
                $arabic_font = base_path('resources/fonts/DroidArabicKufiRegular.ttf');
            }
            
            // الخط الجديد للأرقام والإنجليزية (Times New Roman)
            $number_font = public_path('font/timr45w.ttf'); 
 
            // ==========================================
            // 3. إعدادات الأبعاد والإحداثيات
            // ==========================================
            $qr_size        = 450; // حجم الباركود
            $y_title        = 580; // الارتفاع الخاص باسم المناسبة 
            $y_tickets      = 900 ; // الارتفاع الخاص برقم المقعد وعدد الدعوات
            $x_left_ticket  = 600; // العرض الخاص برقم المقعد 
            $x_right_ticket = 1430; // العرض الخاص بعدد الدعوات 
            $y_mobile       = 1120; // الارتفاع الخاص برقم الموبايل
            $y_datetime     = 1200; // الارتفاع الخاص بالتاريخ والوقت
            $y_qr           = 1270; // الارتفاع الخاص بمكان الباركود

            // ==========================================
            // 4. إنشاء الباركود
            // ==========================================
            QrCode::format('png')
                ->size($qr_size)
                ->color($color[0], $color[1], $color[2])
                ->backgroundColor(0, 0, 0, 0)
                ->generate($link, $qr_tmp_path);

            // ==========================================
            // 5. دمج البيانات على الصورة
            // ==========================================
            $img = Image::make($bg);
            $center_x = intval($img->width() / 2);

            // أ- إضافة عنوان المناسبة (Event Title)
            // نعكس ترتيب الكلمات فقط - FreeType يربط الحروف تلقائياً
            if (!empty(trim($event->name ?? ''))) {
                $words = explode(' ', trim($event->name));
                $words_rev = array_reverse($words);
                $reversed_name = implode(' ', $words_rev);
                $Arabic = new \ArPHP\I18N\Arabic('Glyphs');
                $title_text = $Arabic->utf8Glyphs($reversed_name);
                \Illuminate\Support\Facades\Log::info("update_qr rendering text", ["original" => $event->name, "title_text" => $title_text, "font" => $arabic_font]);

                $img->text($title_text, $center_x, $y_title, function ($font) use ($arabic_font) {
                    $font->file($arabic_font);
                    $font->size(90);
                    $font->color('#fff'); 
                    $font->align('center');
                    $font->valign('middle');
                });
            }

            // ب- إضافة رقم المقعد (في حالة أنه لا يساوي 0)
            if (isset($user_event->suit_num) && $user_event->suit_num != 0) {
                $img->text($user_event->suit_num, $x_left_ticket, $y_tickets, function ($font) use ($number_font) {
                    $font->file($number_font);
                    $font->size(90); 
                    $font->color('#000000');
                    $font->align('center');
                    $font->valign('middle');
                });
            }

            // ج- إضافة عدد الدعوات
            if (isset($user_event->accept_count)) {
                $img->text($user_event->accept_count, $x_right_ticket, $y_tickets, function ($font) use ($number_font) {
                    $font->file($number_font);
                    $font->size(90);
                    $font->color('#000000');
                    $font->align('center');
                    $font->valign('middle');
                });
            }

            // د- إضافة رقم الموبايل
            if (isset($user_event->mobile)) {
                $img->text($user_event->mobile, $center_x, $y_mobile, function ($font) use ($number_font) {
                    $font->file($number_font);
                    $font->size(90);
                    $font->color('#000');
                    $font->align('center');
                    $font->valign('middle');
                });
            }

            // هـ- إضافة التاريخ والوقت
            if (isset($event->date) && isset($event->time)) {
                $datetime = $event->date . ' ' . $event->time;
                $img->text($datetime, $center_x, $y_datetime, function ($font) use ($number_font) {
                    $font->file($number_font);
                    $font->size(50);
                    $font->color('#000000');
                    $font->align('center');
                    $font->valign('middle');
                });
            }

            // و- إضافة صورة الباركود في المنتصف
            $img->insert($qr_tmp_path, 'top', 0, $y_qr);

            // ==========================================
            // 6. حفظ الصورة النهائية وتنظيف الملفات المؤقتة
            // ==========================================
            $img->save($final_path, 100);

            if (file_exists($qr_tmp_path)) {
                unlink($qr_tmp_path);
            }

            return $final_path;
        }
    }

  	private function hexToRgb(string $hex): array
    {
        $hex = str_replace('#', '', $hex);

        if (strlen($hex) === 3) {
            $r = hexdec(str_repeat(substr($hex, 0, 1), 2));
            $g = hexdec(str_repeat(substr($hex, 1, 1), 2));
            $b = hexdec(str_repeat(substr($hex, 2, 1), 2));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }

        return [$r, $g, $b];
    }

    public function my_package(){
        $total = auth()->user()->custom_invetaion;
        $send = auth()->user()->send_custom_invetaion;
        $available = $total - $send;

        return response()->json([
            "total" => $total,
            "send" => $send,
            "available" => $available,
        ]);
    } 
 
    public function eventMemoryScanQrCode(Request $request)
    {
        // 1. التحقق من وجود الملف وأن تكون صورة
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        try {
            // 2. الحصول على المسار المؤقت للصورة المرفوعة
            $imagePath = $request->file('image')->getRealPath();

            // 3. قراءة الـ QR Code مع معالجة الصورة تلقائياً
            $decodedText = $this->decodeQrFromImage($imagePath);

            if (empty($decodedText)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'لم يتم العثور على QR Code واضح في الصورة'
                ], 422);
            }

            $uu_id = explode("/", $decodedText);
            $uu_id = end($uu_id); 
            $qr_code = Qr_Code::
            where('uu_id', $uu_id)
            ->firstOrFail();
   
        
            $event = $qr_code?->event;
            if(!$event){
                return response()->json([
                    "errors" => "QR is expired"
                ], 400);
            } 
            $memories = Memory::
            where("event_user_id", $qr_code->event_user_id)
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
                "event_user_id" => $qr_code->event_user_id,
            ]); 

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'تعذر قراءة الـ QR Code من الصورة: ' . $e->getMessage()
            ], 422);
        }
    } 
 
    public function customEventMemoryScanQrCode(Request $request)
    {
        // 1. التحقق من وجود الملف وأن تكون صورة
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        try {
            // 2. الحصول على المسار المؤقت للصورة المرفوعة
            $imagePath = $request->file('image')->getRealPath();

            // 3. قراءة الـ QR Code مع معالجة الصورة تلقائياً
            $decodedText = $this->decodeQrFromImage($imagePath);

            if (empty($decodedText)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'لم يتم العثور على QR Code واضح في الصورة'
                ], 422);
            }

            $uu_id = explode("/", $decodedText);
            $uu_id = end($uu_id); 
            $qr_code = CustomEventUsers::
            where('uu_id', $uu_id)
            ->firstOrFail();
     
            $memories = CustomMemory::
            where("custom_user_id", $qr_code->id)
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
                "event" => $qr_code->event,
                "event_user_id" => $qr_code->event_user_id,
            ]); 

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'تعذر قراءة الـ QR Code من الصورة: ' . $e->getMessage()
            ], 422);
        }
    } 
 
    public function eventChatScanQrCode(Request $request)
    {
        // 1. التحقق من وجود الملف وأن تكون صورة
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        try {
            // 2. الحصول على المسار المؤقت للصورة المرفوعة
            $imagePath = $request->file('image')->getRealPath();

            // 3. قراءة الـ QR Code مع معالجة الصورة تلقائياً
            $decodedText = $this->decodeQrFromImage($imagePath);

            if (empty($decodedText)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'لم يتم العثور على QR Code واضح في الصورة'
                ], 422);
            }

            $uu_id = explode("/", $decodedText);
            $uu_id = end($uu_id); 
            $qr_code = Qr_Code::
            where('uu_id', $uu_id)
            ->firstOrFail();
   
        
            $event = $qr_code?->event;
            if(!$event){
                return response()->json([
                    "errors" => "QR is expired"
                ], 400);
            } 
 
            $chat = EventChat::
            where("event_user_id", $qr_code->event_user_id)
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
                "event_user_id" => $qr_code->event_user_id,
            ]); 

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'تعذر قراءة الـ QR Code من الصورة: ' . $e->getMessage()
            ], 422);
        }
    } 
 
    public function customEventChatScanQrCode(Request $request)
    {
        // 1. التحقق من وجود الملف وأن تكون صورة
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        try {
            // 2. الحصول على المسار المؤقت للصورة المرفوعة
            $imagePath = $request->file('image')->getRealPath();

            // 3. قراءة الـ QR Code مع معالجة الصورة تلقائياً
            $decodedText = $this->decodeQrFromImage($imagePath);

            if (empty($decodedText)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'لم يتم العثور على QR Code واضح في الصورة'
                ], 422);
            }

            $uu_id = explode("/", $decodedText);
            $uu_id = end($uu_id); 
            $qr_code = CustomEventUsers::
            where('uu_id', $uu_id)
            ->firstOrFail();
             $chat = CustomChat:: 
            where("custom_user_id", $qr_code->id)
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
                "event" => $qr_code->event,
                "event_user_id" => $qr_code->event_user_id,
            ]); 

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'تعذر قراءة الـ QR Code من الصورة: ' . $e->getMessage()
            ], 422);
        }
    } 
    /**
     * محاولة قراءة QR Code من صورة مع معالجة تلقائية للصور ذات الخلفيات المعقدة
     * يجرب عدة استراتيجيات: القراءة المباشرة، ثم مع تدرج رمادي وزيادة التباين،
     * ثم معالجة يدوية بالـ GD مع تحويل الصورة إلى أبيض وأسود
     */
    private function decodeQrFromImage(string $imagePath): string
    {
        // المحاولة 0: استخدام ZBar 
        try {
            // إضافة 2>&1 لالتقاط أخطاء النظام والصلاحيات
            $command = 'zbarimg --quiet --raw ' . escapeshellarg($imagePath) . ' 2>&1';
            $output = shell_exec($command);
            
            // تسجيل المخرجات في ملف laravel.log لمعرفة سبب المشكلة
            Log::info("ZBar Output: " . (string)$output);

            $result = trim((string) $output);
            
            // التأكد من أن النتيجة ليست رسالة خطأ من النظام
            if (!empty($result) && !str_contains(strtolower($result), 'error') && !str_contains(strtolower($result), 'scanned 0')) {
                return $result;
            }
        } catch (\Exception $e) {
            Log::error("ZBar Exception: " . $e->getMessage());
        }
        // المحاولة 1: استخدام مكتبة khanamiryan مباشرة (أقوى في الكشف)
        try {
        // تنفيذ أمر zbarimg لقراءة الصورة
            $command = 'zbarimg --quiet --raw ' . escapeshellarg($imagePath);
            $output = shell_exec($command);
            $result = trim((string) $output);
            
            if (!empty($result)) {
                return $result;
            }
        } catch (\Exception $e) {
            // نكمل للمحاولة التالية
        }
        // المحاولة 1: استخدام مكتبة khanamiryan مباشرة (أقوى في الكشف)
        try {
            $qrcode = new \Zxing\QrReader($imagePath);
            $result = $qrcode->text(['TRY_HARDER' => true]);
            if (!empty($result) && $result !== false) {
                return $result;
            }
        } catch (\Exception $e) {
            // نكمل للمحاولة التالية
        }
        // المحاولة 1: استخدام مكتبة khanamiryan مباشرة (أقوى في الكشف)
        try {
            $qrcode = new \Zxing\QrReader($imagePath);
            $result = $qrcode->text();
            if (!empty($result) && $result !== false) {
                return $result;
            }
        } catch (\Exception $e) {
            // نكمل للمحاولة التالية
        }

        // المحاولة 2: khanamiryan مع معالجة GD (عدة قيم threshold)
        $thresholds = [100, 128, 80, 160, 60];
        $contrastValues = [-80, -100, -50];
        foreach ($contrastValues as $contrast) {
            foreach ($thresholds as $threshold) {
                try {
                    $tmpPath = $this->preprocessImageForQr($imagePath, $threshold, $contrast);
                    $qrcode = new \Zxing\QrReader($tmpPath);
                    $result = $qrcode->text();
                    if (file_exists($tmpPath)) unlink($tmpPath);
                    if (!empty($result) && $result !== false) {
                        return $result;
                    }
                } catch (\Exception $e) {
                    if (isset($tmpPath) && file_exists($tmpPath)) unlink($tmpPath);
                }
            }
        }

        // المحاولة 3: khanamiryan مع تصغير الصورة
        $scales = [0.5, 0.75];
        foreach ($scales as $scale) {
            foreach ([100, 128] as $threshold) {
                try {
                    $tmpPath = $this->preprocessImageForQr($imagePath, $threshold, -80, $scale);
                    $qrcode = new \Zxing\QrReader($tmpPath);
                    $result = $qrcode->text();
                    if (file_exists($tmpPath)) unlink($tmpPath);
                    if (!empty($result) && $result !== false) {
                        return $result;
                    }
                } catch (\Exception $e) {
                    if (isset($tmpPath) && file_exists($tmpPath)) unlink($tmpPath);
                }
            }
        }

        // المحاولة 4: chillerlan مع خيارات المعالجة المدمجة
        try {
            $options = new QROptions([
                'readerUseGd'            => true,
                'readerGrayscale'        => true,
                'readerIncreaseContrast' => true,
            ]);
            $qrcode = new QRCodeRead($options);
            $result = (string) $qrcode->readFromFile($imagePath);
            if (!empty($result)) {
                return $result;
            }
        } catch (\Exception $e) {
            // نكمل
        }

        // المحاولة 5: chillerlan مع عكس الألوان
        try {
            $options = new QROptions([
                'readerUseGd'            => true,
                'readerGrayscale'        => true,
                'readerIncreaseContrast' => true,
                'readerInvertColors'     => true,
            ]);
            $qrcode = new QRCodeRead($options);
            $result = (string) $qrcode->readFromFile($imagePath);
            if (!empty($result)) {
                return $result;
            }
        } catch (\Exception $e) {
            // فشلت كل المحاولات
        }

        throw new \Exception('لم يتم العثور على QR Code واضح في الصورة بعد كل محاولات المعالجة');
    }

    /**
     * تحميل صورة GD من مسار الملف
     */
    private function loadGdImage(string $imagePath)
    {
        $imageInfo = getimagesize($imagePath);
        if ($imageInfo === false) {
            throw new \Exception('ملف غير صالح كصورة');
        }

        $mime = $imageInfo['mime'];
        switch ($mime) {
            case 'image/jpeg':
                $srcImage = imagecreatefromjpeg($imagePath);
                break;
            case 'image/png':
                $srcImage = imagecreatefrompng($imagePath);
                break;
            case 'image/webp':
                $srcImage = imagecreatefromwebp($imagePath);
                break;
            default:
                throw new \Exception('نوع صورة غير مدعوم: ' . $mime);
        }

        if (!$srcImage) {
            throw new \Exception('فشل في تحميل الصورة');
        }

        return $srcImage;
    }

    /**
     * معالجة الصورة بالـ GD وحفظها في ملف مؤقت
     * 1. تصغير (اختياري)
     * 2. تدرج رمادي
     * 3. زيادة التباين
     * 4. تحويل لأبيض وأسود (Threshold)
     * 5. إضافة هامش أبيض (Quiet Zone)
     * يرجع مسار الملف المؤقت
     */
    private function preprocessImageForQr(string $imagePath, int $threshold = 128, int $contrast = -80, float $scale = 1.0): string
    {
        $srcImage = $this->loadGdImage($imagePath);
        $width  = imagesx($srcImage);
        $height = imagesy($srcImage);

        // تصغير الصورة إذا طُلب ذلك
        if ($scale < 1.0) {
            $newW = intval($width * $scale);
            $newH = intval($height * $scale);
            $scaled = imagecreatetruecolor($newW, $newH);
            imagecopyresampled($scaled, $srcImage, 0, 0, 0, 0, $newW, $newH, $width, $height);
            imagedestroy($srcImage);
            $srcImage = $scaled;
            $width = $newW;
            $height = $newH;
        }

        // تحويل لتدرج رمادي
        imagefilter($srcImage, IMG_FILTER_GRAYSCALE);

        // زيادة التباين
        imagefilter($srcImage, IMG_FILTER_CONTRAST, $contrast);

        // تحويل لأبيض وأسود (Threshold)
        $bwImage = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($bwImage, 255, 255, 255);
        $black = imagecolorallocate($bwImage, 0, 0, 0);
        imagefill($bwImage, 0, 0, $white);

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $rgb = imagecolorat($srcImage, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                if ($r < $threshold) {
                    imagesetpixel($bwImage, $x, $y, $black);
                } else {
                    imagesetpixel($bwImage, $x, $y, $white);
                }
            }
        }

        imagedestroy($srcImage);

        // إضافة هامش أبيض (Quiet Zone) حول الصورة
        $margin = 40;
        $newWidth  = $width  + ($margin * 2);
        $newHeight = $height + ($margin * 2);
        $finalImage = imagecreatetruecolor($newWidth, $newHeight);
        $whiteMargin = imagecolorallocate($finalImage, 255, 255, 255);
        imagefill($finalImage, 0, 0, $whiteMargin);
        imagecopy($finalImage, $bwImage, $margin, $margin, 0, 0, $width, $height);
        imagedestroy($bwImage);

        // حفظ الصورة المعالجة في ملف مؤقت
        $tmpPath = sys_get_temp_dir() . '/qr_processed_' . uniqid() . '.png';
        imagepng($finalImage, $tmpPath);
        imagedestroy($finalImage);

        return $tmpPath;
    }
}


