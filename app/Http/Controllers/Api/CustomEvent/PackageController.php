<?php

namespace App\Http\Controllers\Api\CustomEvent;

use App\Http\Controllers\Controller;
use App\Models\CustomEvent as Model;
use App\Models\CustomEventFamily;
use App\Models\CustomEventUsers;
use App\Models\Pricing;
use App\Models\Orders;
use App\Models\Negotaition;
use App\Models\Payment;
use App\Models\User;
use App\Traits\imageTrait;
use Carbon\Carbon;
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
                "total" => $item->total,
                "type" => $item->type,
                "users_count" => $item->users_count,
                "type" => $item->type,
                "type" => $item->type,
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
                "total" => $item->total,
                "type" => $item->type,
                "users_count" => $item->users_count,
                "type" => $item->type,
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
        $waiting = $invetations - $attendance;
        $Item->waiting = $waiting;
        $Item->send_qr = $send_qr;

        return response()->json([
            'Item' =>  $Item, 
            'invetations' =>  $invetations, 
            'attendance' =>  $attendance, 
            'waiting' =>  $waiting, 
            'send_qr' =>  $send_qr, 

            'invetations_list' =>  $invetations_list, 
            'send_qr_list' =>  $send_qr_list, 
            'attendance_list' =>  $attendance_list, 
            'waiting_list' =>  $waiting_list, 

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
        where('id',$id)
        ->update([
            "scan_count" => $request->scan_count
        ]);

        return response()->json([
            "success" => "You attend success"
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
        ->where('id', $custom_event_id)
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
                    ]); 
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
        select("id", "mobile", "name", "scan_count", "users_count")
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
                    $user->update([
                        "send_custom_invetaion" => $user->send_custom_invetaion + $users_count
                    ]);

                    $uu_id = uniqid();

                  	$row->update([
                        'name' => $arr['name'],
                        'users_count' => $arr['users_count'],
                        'mobile' => isset($arr['mobile']) ? $arr['mobile'] : null,
                        'uu_id' => $uu_id
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
        $Item = Model::
        where("user_id", auth()->user()->id)
        ->orWhereHas("sub_user", function($query){
            $query->where("users.id", auth()->user()->id);
        })
        ->findOrFail($id);
        $event_users = CustomEventUsers::where('custom_event_id', $Item->id)
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

                $row = CustomEventUsers::where('id',$item)->first();
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
                if($users_count >= $available){
                    return response()->json([
                        "errors" => "لا تمتلك كل هذا العدد من الدعوات تم ارسال البعض و ليس الكل"
                    ], 400);
                }
                // __________________________________________________________________________________
                $this->update_qr($row,$row->uu_id);
                // __________________________________________________________________________________
                if(!$row->send_qr){
                    auth()->user()->send_custom_invetaion += $users_count;
                    auth()->user()->save();
                }
                // __________________________________________________________________________________
                if($row != null && $row->mobile != null && $event != null) {

                  $to = $row->mobile;

                  $image = $row->qr;

                  $day_name   = Carbon::parse($event->date)->locale('ar')->translatedFormat('l');

                  $caption = $row->name . PHP_EOL . PHP_EOL .
                    $event->title . PHP_EOL . PHP_EOL .
                    "وذلك بمشيئة الله يوم " . $day_name ." الموافق" . PHP_EOL . PHP_EOL .
                    $event->date . " 📆" . PHP_EOL . PHP_EOL .
                    "⏱️الساعـة " . $event->time . " مساءاً" . PHP_EOL . PHP_EOL .
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
        ->first();
        if($event_user->send_qr ){
            $event_user->resend_qr = true;
            $event_user->save();
        }
        $users_count = $event_user->users_count;
        $available = auth()->user()->custom_invetaion - auth()->user()->send_custom_invetaion;
        if($users_count >= $available){
            return response()->json([
                "errors" => "لا تمتلك كل هذا العدد من الدعوات تم ارسال البعض و ليس الكل"
            ], 400);
        }
        // __________________________________________________________________________________
        $this->update_qr($event_user,$event_user->uu_id);
        // __________________________________________________________________________________
        if(!$event_user->send_qr){
            auth()->user()->send_custom_invetaion += $users_count;
            auth()->user()->save();
        }
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

    private function update_qr($row, $uu_id) {
        $event = $row->event;
        $bg = $event->image;

        // تأكد من وجود المجلد
        $directory = public_path('custom_event_qr_code');
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        $image_name = $uu_id . '-custom-event-qr.png';
        $link = asset('scan-custom-event-qr/' . $uu_id);
        $qr_temp_path = public_path('custom_event_qr_code/temp_qr_' . $image_name);

        // إنشاء QR بخلفية شفافة
        $color = $this->hexToRgb($row->event->color);
        
        QrCode::format('png')
            ->size(140)
            ->color($color[0], $color[1], $color[2])
            ->backgroundColor(0, 0, 0, 0)
            ->generate($link, $qr_temp_path);

        // افتح الخلفية
        $background = Image::make($bg);
        
        // افتح QR
        $qr = Image::make($qr_temp_path);

        // احسب الإحداثيات لتوسيط QR
        $x = intval(($background->width() - $qr->width()) / 2);
        $y = $background->height() - $qr->height() - 280;

        // أدرج QR مرة واحدة بس!
        $background->insert($qr, 'top-left', $x, $y);

        // إعداد النصوص
        if ($event->language == 'ar') {
            $Arabic = new \ArPHP\I18N\Arabic('Glyphs');
            $name = $Arabic->utf8Glyphs($row->name);
            
            $user_count_label = 'عدد الضيوف ' . $row->users_count;
            $Arabic2 = new \ArPHP\I18N\Arabic('Glyphs');
            $name2 = $Arabic2->utf8Glyphs($user_count_label);
            
            $font_path = public_path('font/DroidArabicKufiRegular.ttf');
        } else {
            $name = $row->name;
            $name2 = 'Entered Users ' . $row->users_count;
            $font_path = public_path('font/LuxuriousRoman-Regular.ttf');
        }

        // مركز الصورة للنص
        $center_x = intval($background->width() / 2);
        $text_y = $y + $qr->height() + 15;

        // إضافة اسم الشخص
        $background->text($name, $center_x, $text_y, function ($font) use ($font_path) {
            $font->file($font_path);
            $font->size(20);
            $font->color('#000');
            $font->align('center');
            $font->valign('top');
        });

        // إضافة عدد المستخدمين
        if ($row->users_count > 1) {
            $text_y2 = $text_y + 25;
            $background->text($name2, $center_x, $text_y2, function ($font) use ($font_path) {
                $font->file($font_path);
                $font->size(20);
                $font->color('#000');
                $font->align('center');
                $font->valign('top');
            });
        }

        // حفظ الصورة النهائية
        $final_path = public_path('custom_event_qr_code/' . $image_name);
        
        try {
        
            $background = Image::canvas($background->width(), $background->height())
                        ->insert($background);
            // ⭐ الحل السحري: encode قبل save
            $encoded = $background->encode('png', 100);
            // حفظ الصورة المشفرة
            file_put_contents($final_path, $encoded);
            
            // تحديث قاعدة البيانات
            $row->update([
                'qr' => $image_name
            ]);
            
            // حذف QR المؤقت
            @unlink($qr_temp_path);
            
            // تدمير الصورة من الذاكرة
            $background->destroy();
            
            return true;
        } catch (\Exception $e) {
            Log::error("فشل حفظ QR: " . $e->getMessage());
            return false;
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
}
