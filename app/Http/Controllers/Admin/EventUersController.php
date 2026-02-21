<?php

namespace App\Http\Controllers\Admin;

use App\Models\EventUserActions;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\EventUsers as Model;
use App\Models\Events;
use App\Models\EventUsers;
use App\Models\Setting;
use App\Models\EventUserLogs;
use App\Models\Qr_Code;
use App\Models\EventMessages;
use App\Models\CongratulationMessages;
use App\Models\EventFamily;
use Carbon\Carbon;
use Response;
use PDF;
use Intervention\Image\ImageManagerStatic as Image;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Notifications;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EventUserImport;


class EventUersController extends Controller
{


    public function get_lang()
    {
        $lang = session()->get('admin_lang');

        if($lang == 'en' && $lang != null) {
            return $lang;
        } else {
            return 'ar';
        }
    }


    public function import(Request $request)
    {
       $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv',
            'event_id' => 'required|exists:events,id'
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }   

        $file_path = $request->file('file')->store('temp');
        $saved_path = storage_path('app') . '/' . $file_path;

        // dd($saved_path);

        $data = Excel::toArray([], $saved_path);

        if(! empty($data)) {
            $data = array_slice($data[0],1);
        }

        if($data != null && count($data) > 0) {

            foreach ($data as $index => $row) {

                $check = EventUsers::where('event_id',$request->event_id)->where('mobile',$row[1])->first();

                if($check == null) {

                    EventUsers::create([
                        'event_id'    => $request->event_id,
                        'name'        => $row[0],
                        'mobile'      => $row[1],
                        'users_count' => $row[2],
                        'status' => 'hold'
                    ]);
                }
            }
        }

        // dd($data);
        // Excel::import(new EventUserImport($request->event_id), $data);

        return response()->json([
            'success' => 'imported successfully!', 
        ]);
    }


    public function event_chat_details($event_user_id) {

        $event_user = Model::findOrFail($event_user_id);

        $event_id = $event_user->event_id;
        $mobile   = $event_user->mobile;

        $actions = EventUserActions::where('event_id',$event_id)->where('event_user_id',$event_user_id)->where('mobile',$mobile)->get();

        $varibles = [
            'event_id'      => $event_id,
            'event_user_id' => $event_user_id,
            'mobile'        => $mobile,
            'actions'       => $actions,
            'event_user'    => $event_user
        ];

        return response()->json([
            'varibles' => $varibles, 
        ]);

    }


    // new-send-event-invitation
    public function new_send_event_invitation(Request $request) {

       $validator = Validator::make($request->all(), [ 
        	'event_id' => 'required',
            'users' => 'required|array',
            'users.*.id' => 'required',
            'users.*.users_count' => 'required|numeric', 
            'file_type' => 'required',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }   
        // dd($request->all());

        $ultramsg_token="7ye6ifujyug0u46g"; // Ultramsg.com token
        $instance_id="instance109805"; // Ultramsg.com instance id
        $client = new \UltraMsg\WhatsAppApi($ultramsg_token,$instance_id);

        $priority=0;
        $referenceId="SDK";
        $nocache=true; 

      	$total_qty = 0;

      	$event = Events::where('id',$request->event_id)->firstOrFail();

        $user = $event->user;

        if($request->users != null && ! empty($request->users)) {

          	foreach($request->users as $arr) {

              if(isset($arr['id'])) {

                if(isset($arr['users_count']) && $arr['users_count']) {
                	$total_qty = $total_qty + $arr['users_count'];
                } else {

                  $row = Model::withTrashed()->where('id',$arr['id'])->first();

                  if($row != null) {
                  	 $total_qty = $total_qty + $row->users_count;
                  } else {
                     $total_qty = $total_qty + 1;
                  }
                }
              }

            }

          	//dd($total_qty);

            if($user == null) {
                return response()->json([
                    'errors' => 'لقد حدث خطا ما برجاء المحاوله مره اخري', 
                ]); 
            }

          	foreach($request->users as $arr) {

              if(isset($arr['id'])) {

                $row = Model::withTrashed()->where('id',$arr['id'])->first();

                if($row != null && $row->event != null) {
                    $row->update([
                        "send_time" => now(),
                    ]);
                    if($row->code != null) {
                        $code = $row->code;
                    } else {
                        $code = generateUniqueCode();
                        $row->update(['code' => $code]);
                    }

                  /////////////////

                  $to = $row->mobile;

                  //$image="https://file-example.s3-accelerate.amazonaws.com/images/test.jpg";

                  $day_name   = Carbon::parse($row->event->date)->locale('ar')->translatedFormat('l');

                  $caption = $row->name . PHP_EOL . PHP_EOL .
                        $row->event->title . PHP_EOL . PHP_EOL .
                        " وذلك بمشيئة الله تعالى يوم " . $day_name ." الموافق " . PHP_EOL .
                        $row->event->date . " 📆" . PHP_EOL . PHP_EOL .
                        " وقت الأستقبال ⏱️ " . $row->event->time . " مساءً" . PHP_EOL . PHP_EOL .
                        "📍مكان الحفـل " . $row->event->address . PHP_EOL . PHP_EOL . 
                        "عدد الدعوات " . $row->users_count . PHP_EOL . PHP_EOL .
                        "قبول الدعوة " . PHP_EOL .
                        "الأعتذار عن الدعوة " . PHP_EOL .
                        "موقع المناسبة " . PHP_EOL . PHP_EOL .
                        "اضغط على الرابط." . PHP_EOL .
                        // "يرجي التأكيد أو الاعتذار خلال 24 ساعة حتى لا يتم الغاء الدعوة. قم بضغط على الرابط لمعرفة تفاصيل المناسبة" . PHP_EOL . PHP_EOL .
                        "https://www.mazoominvitations.com/event-login/".$code;

                  if($request->file_type == 'image') {

                    $image = $row->event->file;

                    // $api=$client->sendChatMessage($to,$body);
                    $api = $client->sendImageMessage($to,$image,$caption,$priority,$referenceId,$nocache);

                  } else {

                    $video = $row->event->video;

                    // $api=$client->sendChatMessage($to,$body);
                    $api = $client->sendVideoMessage($to,$video,$caption,$priority,$referenceId,$nocache);

                  }

                  ///////////////////////////////////////////////////////////////////////

                  if(! empty($api) && isset($api['sent']) && $api['sent'] == 'true'  && isset($api['message']) && $api['message'] == 'ok') {

                    // dd('ok');
                    $row->update(['is_new_sent' => 1]);

                    $user->update([
                      'balance' => $user->balance - $row->users_count
                    ]);

                  } else {
                    // dd('not ok',$api);
                    $row->update(['is_new_sent' => 0]);
                  }

                }

              }

            }

          return response()->json([
              'success' => 'تم ارسال الرسائل بنجاح', 
          ]); 


          	/*
            if($user->balance >= $total_qty) {


            } else {
                $msg = ' عفوا رصيدك غير كافي برجاء شحن رصيدك برصيد ' . $total_qty;
                return redirect()->back()->with('error',$msg);
            }
            */

        } else {
            return response()->json([
                'errors' => 'من فضلك اختر عنصر واحد علي الاقل', 
            ]); 
        }

    }



  	// send_event_users
    public function update_user_mobile(Request $request)
    {

       $validator = Validator::make($request->all(), [ 
        	'event_user_id' => 'required|exists:event_users,id',
            'mobile' => 'required',
            'users_count' => 'required|numeric',
            'name' => 'required',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }   
        $request->validate([
            'event_user_id' => 'required',
            'mobile' => 'required|numeric',
        ]);

        $event_user_id = $request->event_user_id;

        $event_user = EventUsers::where('id', $event_user_id)->firstOrFail();

        try {

          $event_user->update(['mobile' => $request->mobile ]);

          if($request->users_count != null) {
            $event_user->update(['users_count' => $request->users_count ]);
          }

          if($request->name != null) {
            $event_user->update(['name' => $request->name ]);
          }

          return response()->json([
              'success' => 'تم التحديث بنجاح', 
          ]);

        } catch(\Exception $e) {
            dd($e->getMessage(), $e->getLine());
        }

    }




  	// delete_selected_event_users
    public function delete_selected_event_users(Request $request) {

       $validator = Validator::make($request->all(), [ 
            'users' => 'required|array',
            'users.*.id' => 'required', 
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        } 
        if($request->users != null && ! empty($request->users)) {

            foreach($request->users as $arr) {

              	if(isset($arr['id'])) {
                	Model::withTrashed()->where('id',$arr['id'])->delete();
                }

            }

            return response()->json([
                'success' => 'تم حذف العناصر المختاره', 
            ]);

        } else {
            return response()->json([
                'success' => 'من فضلك اختر عنصر واحد علي الاقل', 
            ]);
        }

    }



  	// delete_messages
    public function delete_messages(Request $request) {

       $validator = Validator::make($request->all(), [ 
            'messags_ids' => 'required|array',
            'messags_ids.*.id' => 'required', 
            'messags_ids.*.type' => 'required|in:congrate,event_message', 
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        } 
        if($request->messags_ids != null && ! empty($request->messags_ids)) {

            foreach($request->messags_ids as $item) {
                $id = $item['id'];
              	if(array_key_exists("id",$item) && array_key_exists("type",$item)) {

                  	$key = $item['type'];

                  	if($key == 'congrate') {
                        CongratulationMessages::where('id',$id)->delete();
                    } else {
                        EventMessages::where('id',$id)->delete();
                    }

                }



            }

            return response()->json([
                'success' => 'تم حذف العناصر المختاره', 
            ]);

        } else {
            return response()->json([
                'success' => 'من فضلك اختر عنصر واحد علي الاقل', 
            ]);
        }

    }





	// send_event_users
    public function remember_users_to_event(Request $request)
    {
       $validator = Validator::make($request->all(), [
            'sending_type2' => 'required|in:old_send,new_send',
            'message2' => 'required',
            'event_id' => 'required|exists:events,id',
            'users' => 'required|array',
            'users.*.id' => 'required',
          	'file2'  => 'nullable',
            'date' => 'required',
            'time' => 'required',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }   
        $setting = Setting::first(); 

        $event_id = $request->event_id;

        $event = Events::where('id', $event_id)->firstOrFail();

      	$message = $request->message2;

        $url_button = '?q=' . $event->lat . ',' . $event->long;

      	$path = 'images';
      	$filename = '';

        if($request->file('file2') != null && $request->file2 != null) {

            $extension = $request->file('file2')->extension();
            $filename = uniqid() . '.' . $extension;
            $request->file('file2')->move($path, $filename);

            $url_image = asset('images/'.$filename);

        } else {
            $url_image = $event->file;
        }

        /* ***************************************************************************** */

        $ultramsg_token="7ye6ifujyug0u46g"; // Ultramsg.com token
        $instance_id="instance109805"; // Ultramsg.com instance id
        $client = new \UltraMsg\WhatsAppApi($ultramsg_token,$instance_id);

        $priority=0;
        $referenceId="SDK";
        $nocache=true;

        /* ***************************************************************************** */

        try {

            $errors = 0;

            if($request->users != null && ! empty($request->users)) {

                foreach($request->users as $arr) {

                    if(array_key_exists('id', $arr)) {

                        $user_event = Model::withTrashed()->find($arr['id']);

                        if($user_event != null) {

                            $url_button = '?q=' . $user_event?->event?->lat . ',' . $user_event?->event?->long;

                            $user_name = $user_event->name;

                            $mobile = $user_event->mobile;

                            //$to = $code.$mobile;
                            $to = $mobile;
                            $to = str_replace("+","",$to);

                            //$time = $event->time . ' مساءً';
                          	$date = $request->date;
                          	$time = $request->time;

                            if($request->sending_type2 == 'old_send') {

                                $template_name = 'car_msg3';
                                $language = 'ar';

                                $token          = get_whats_setting($event)['token'];
                                $sender_id      = get_whats_setting($event)['sender_id'];
                                $phone_numer_id = get_whats_setting($event)['sender_id'];

                                // $response = SendTemplateV10($to,$template_name,$language,$message,$phone_numer_id,$token);

                                //$url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name.'&param_1='.$user_name.'&param_2='.$message.'&url_button='.$url_button.'&image='.$url_image;
                                // $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name.'&param_1='.$message.'&url_button='.$url_button.'&image='.$url_image.'&url_button='.$url_button;
                                $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name.'&param_1='.$message.'&param_2='.$time.'&param_3='.$date.'&url_button='.$url_button.'&image='.$url_image;


                                $response = SendNewTemplateCodeV1($url);

                                //$response = SendTemplateV10($to,$template_name,$language,$message,$phone_numer_id,$token);

                                if ($response != null && $response->getStatusCode() == 200) {

                                    $body = $response->getBody();
                                    $data = json_decode($body, true);

                                } else {
                                    $user_event->update([
                                        'status' => 'failed-v2',
                                    ]);
                                }

                            } else {

                                $caption = "ضيفتنـا الغاليـة , ننتظـرك يوم ". $date ." في تمــام الساعة "  . $time . "  تشرفينــا لحضور " . $request->message2 . ' 🌺🌺 ';

                                // $caption2 = 'تحرص الشركة على تقديم المساعدة للضيف حتى لا توجه اي صعوبات في دخول المناسبة تم ارسال الكود مره ثانية ,يرجى العلم ان الكود نفس الكود المرسل في السابق وليس كودا جديداً ';
                                // dd($url_image);

                                // $api=$client->sendChatMessage($to,$body);
                                $api = $client->sendImageMessage($to,$url_image,$caption,$priority,$referenceId,$nocache);

                                $api2 = $client->sendLocationMessage($to,$event->address,$event->lat,$event->long,$priority=0,$referenceId="SDK");

                                // $qr_code_row = Qr_Code::where('event_user_id',$arr['id'])->latest()->first();

                                // if($qr_code_row) {
                                //     $image_link = url('qr_code/' . $qr_code_row->qr);
                                //     // $api3 = $client->sendImageMessage($to,$image_link,$caption2,$priority,$referenceId,$nocache);
                                // }

                                if(! empty($api) && isset($api['sent']) && $api['sent'] == 'true'  && isset($api['message']) && $api['message'] == 'ok') {
                                    // dd('ok');
                                    info('error sending');
                                } else {
                                    // dd('not ok',$api);
                                    $errors = $errors + 1;
                                }




                            }

                        } else {
                            $errors = $errors + 1;
                        }

                    } else {
                        $errors = $errors + 1;
                    }

                }

                return response()->json([
                    'success' => 'تم الأرسال بنجاح', 
                ]);
            }

        } catch(\Exception $e) {
            dd($e,$e->getMessage(), $e->getLine());
        }

        dd('error-v2');

    }


    // send_event_users
    public function send_custom_message(Request $request)
    {
       $validator = Validator::make($request->all(), [
            'sending_type' => 'required|in:old_send,new_send',
            'message' => 'required',
            'file'  => 'nullable',
            'event_id' => 'required|exists:events,id',
            'users' => 'required|array',
            'users.*.id' => 'required',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }   
        $setting = Setting::first();
  
        $event_id = $request->event_id;

        $event = Events::where('id', $event_id)->firstOrFail();

      	$path = 'images';
      	$filename = '';

        if($request->file('file') != null && $request->file != null) {

            $extension = $request->file('file')->extension();
            $filename = uniqid() . '.' . $extension;
            $request->file('file')->move($path, $filename);

            $url_image = asset('images/'.$filename);

        } else {
            $url_image = $event->file;
        }

        /* ***************************************************************************** */

        $ultramsg_token="7ye6ifujyug0u46g"; // Ultramsg.com token
        $instance_id="instance109805"; // Ultramsg.com instance id
        $client = new \UltraMsg\WhatsAppApi($ultramsg_token,$instance_id);

        $priority=0;
        $referenceId="SDK";
        $nocache=true;

        /* ***************************************************************************** */

        try {

            $errors = 0;

            if($request->users != null && ! empty($request->users)) {

                foreach($request->users as $arr) {

                    if(array_key_exists('id', $arr)) {

                        $user_event = Model::withTrashed()->find($arr['id']);

                        if($user_event != null) {

                          	$user_name = $user_event->name;

                            $mobile = $user_event->mobile;

                            //$to = $code.$mobile;
                            $to = $mobile;
                            $to = str_replace("+","",$to);

                            if($request->sending_type == 'old_send') {

                                $template_name = 'custom_message';
                                $language = 'ar';

                                $message = $request->message;

                                $token          = get_whats_setting($event)['token'];
                                $sender_id      = get_whats_setting($event)['sender_id'];
                                $phone_numer_id = get_whats_setting($event)['sender_id'];

                                // $response = SendTemplateV10($to,$template_name,$language,$message,$phone_numer_id,$token);

                                $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name.'&param_1='.$user_name.'&param_2='.$message.'&image='.$url_image;

                                $response = SendNewTemplateCodeV1($url);

                              	// dd($response);

                                //$response = SendTemplateV10($to,$template_name,$language,$message,$phone_numer_id,$token);

                                if ($response != null && $response->getStatusCode() == 200) {

                                    $body = $response->getBody();
                                    $data = json_decode($body, true);

                                } else {
                                    $user_event->update([
                                        'status' => 'failed-v2',
                                    ]);
                                }

                            } else {

                                $caption = '- ' . $user_event->name . PHP_EOL . PHP_EOL .
                                // ' - شركة معزوم  لتنظيم الحفلات' . PHP_EOL . PHP_EOL .
                                ' - ' . $request->message;

                                // $api=$client->sendChatMessage($to,$body);
                                $api = $client->sendImageMessage($to,$url_image,$caption,$priority,$referenceId,$nocache);

                                // $api2 = $client->sendContactMessage($to,'96597378181',$priority=0,$referenceId="SDK");

                                if(! empty($api) && isset($api['sent']) && $api['sent'] == 'true'  && isset($api['message']) && $api['message'] == 'ok') {
                                    // dd('ok');
                                } else {
                                    // dd('not ok',$api);
                                    $errors = $errors + 1;
                                }

                            }

                        } else {
                            $errors = $errors + 1;
                        }

                    } else {
                        $errors = $errors + 1;
                    }

                }

                return response()->json([
                    'success' => 'تم الأرسال بنجاح', 
                ]);
            }

        } catch(\Exception $e) {
            dd($e->getMessage(), $e->getLine());
        }

        dd('error-v2');

    }




    public function event_report($id) {

        $event = Events::where('id', $id)->firstOrFail();
        $user_events = Model::withTrashed()->where('event_id',$id)->get();

        $data = [
            'event' => $event,
            'user_events' => $user_events
        ]; 

        return response()->json([
            "pdf_data" => $data
        ]);

        //return view('admin.events.event_report',compact('event','user_events'));
    }


    // save_event_users
    public function save_event_users(Request $request)
    { 
       $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
            'event_users.*.name' => 'required',
            'event_users.*.mobile' => 'required|numeric',
          	'event_users.*.users_count' => 'required|numeric|min:1',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }   

        $event_id = $request->event_id;

        $event = Events::where('id', $event_id)->firstOrFail();

        if($request->event_users != null && ! empty($request->event_users)) {

            foreach ($request->event_users as $arr) {
                if($arr['name'] != null && $arr['mobile'] != null && is_numeric($arr['mobile']) && $arr['users_count'] != null && is_numeric($arr['users_count'])) {

                  $check = Model::withTrashed()->where('event_id',$event_id)->where('mobile',ltrim($arr['mobile'],"+"))->count();

                  if($check == 0) {

                    Model::withTrashed()->create([
                        'event_id' => $event_id,
                        'name' => $arr['name'],
                        'mobile' => ltrim($arr['mobile'],"+"),
                        'users_count' => $arr['users_count'],
                        'status' => 'hold'
                    ]);

                  }

                }
            }

        }


        return response()->json([
            'success' => 'تم الحفظ بنجاح', 
        ]);

    }


    // update_event_users
    public function update_event_users(Request $request)
    {

       $validator = Validator::make($request->all(), [ 
            'old_event_users' => 'required|array',
            'old_event_users.*.id' => 'required|exists:event_users,id',
            'old_event_users.*.name' => 'required',
            'old_event_users.*.mobile' => 'required|numeric',
            'old_event_users.*.users_count' => 'required|numeric|min:0',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }   

        $errors = [];

        foreach ($request->old_event_users as  $item) {
            $id = $item['id'];
            $row = Model::withTrashed()->find($id);

            if($row != null && $item['name'] != null && $item['mobile'] != null && is_numeric($item['mobile']) && $item['users_count'] != null && is_numeric($item['users_count'])) {

                $mobile = ltrim($item['mobile'],"+");

                $count = Model::where('id','!=',$row->id)->where('event_id',$row->event_id)->where('mobile',$mobile)->first();

                if($count != null) {
                    // dd($count,$id,$row->id,$mobile,$item['mobile'],$row,$request->old_event_users);
                    $errors[] = $mobile;
                    // return redirect()->back()->with('error', 'رقم الجوال ' . $mobile . ' موجود من قبل');
                } else {

                    // dd($count);

                    $row->update([
                        'name' => $item['name'],
                        'mobile' => $mobile,
                        'users_count' => $item['users_count'],
                    ]);

                    ////////////////////////////////////////////////////////////////////////////

                    $user_event = $row;
                    $users_count = $item['users_count'];

                    $check_qr_code = Qr_Code::where('event_user_id',$row->id)->latest()->first();

                }
                ////////////////////////////////////////////////////////////////////////////
            }
        }

        if(! empty($errors)) {
            $err_string = implode(", ", $errors);

            return response()->json([
                'errors' => 'رقم الجوال ' . $err_string . ' موجود من قبل', 
            ]);
        } else {

            return response()->json([
                'success' => 'تم التحديث بنجاح', 
            ]); 
        }

    }



  	public function delete_event_users(Request $request) {

       $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
            'users' => 'required|array',
            'users.*.id' => 'required',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }   

        $event_id = $request->event_id;

        $event = Events::where('id', $event_id)->firstOrFail();

      	if($request->users != null && ! empty($request->users)) {

          foreach($request->users as $arr) {

            if(array_key_exists('id', $arr)) {

              $user_event = Model::withTrashed()->find($arr['id']);

              if($user_event != null) {
                $user_event->delete();
              }

            }
          }


          return response()->json([
              'success' => 'تم الحذف بنجاح', 
          ]); 
        }

    }


  	///////////////////////////////////////////////////////////////////////////////////////

  	// save_event_family
    public function save_event_family(Request $request)
    {
       $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
            'event_users' => 'required|array',
            'event_users.*.name' => 'required',
            'event_users.*.mobile' => 'nullable|numeric',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }  

        $event_id = $request->event_id;

        $event = Events::where('id', $event_id)->firstOrFail();

        if($request->event_users != null && ! empty($request->event_users)) {

            foreach ($request->event_users as $arr) {
                if($arr['name'] != null) {

                  EventFamily::create([
                    'event_id' => $event_id,
                    'name' => $arr['name'],
                    'mobile' => isset($arr['mobile']) ? ltrim($arr['mobile'],"+") : null,
                    'scan_qr' => 'no'
                  ]);
                }
            }

        }


                return response()->json([
                    'success' => 'تم الحفظ بنجاح', 
                ]); 

    }


    // update_event_family
    public function update_event_family(Request $request)
    {
       $validator = Validator::make($request->all(), [
            'event_users' => 'required|array',
            'event_users.*.id' => 'required',
            'event_users.*.name' => 'required',
            'event_users.*.mobile' => 'nullable|numeric',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }    

        foreach ($request->event_users as $id => $arr) { 
            $row = EventFamily::find($arr['id']); 
            if($row != null && $arr['name'] != null) { 
                $row->update([
                    'name' => $arr['name'],
                    'mobile' => isset($arr['mobile']) ? ltrim($arr['mobile'],"+") : null,
                ]);
            }
        } 


        return response()->json([
            'success' => 'تم التحديث بنجاح', 
        ]); 

    }



  	public function delete_event_family($id) {

        $user_event = EventFamily::find($id);

        if($user_event != null) {
          $user_event->delete();
        }


        return response()->json([
            'success' => 'تم الحذف بنجاح', 
        ]);

    }


  	public function open_event_family($id) {

        $user_event = EventFamily::findOrFail($id);

        $user_event->update(['scan_qr' => 'yes']);


        return response()->json([
            'success' => 'تم دخول الحفل بنجاح', 
        ]); 

    }

  	///////////////////////////////////////////////////////////////////////////////////////

  	public function event_family_search(Request $request) 
    {
       $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
            'search' => 'sometimes',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }   

        $event_id = $request->event_id;

        $event_users = EventFamily::where('event_id',$event_id)

        ->when($request->name,function($q) use($request) {

          $q->where('name','like','%' . $request->search . '%');

        })->when($request->search,function($q) use($request) {

          $q->where('mobile', $request->search);

        })->get();


        return response()->json([
            'event_users' => $event_users, 
            'event_id' => $event_id, 
        ]);
    }

  	///////////////////////////////////////////////////////////////////////////////////////

  	// send_event_users
    public function send_event_users(Request $request)
    {
       $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
            'users' => 'required|array', 
            'users.*.id' => 'required|exists:event_users,id',
            'users.*.users_count' => 'required|numeric',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $setting = Setting::first();
        $event_id = $request->event_id;

        $event = Events::where('id', $event_id)->firstOrFail();

        $colum_qty = array_column($request->users, 'users_count');
        $total_qty = array_sum($colum_qty);

        $user = $event->user;

      	/*
        if($user->balance < $total_qty) {
            $msg = ' عفوا رصيدك غير كافي برجاء شحن رصيدك برصيد ' . $total_qty;
            return redirect()->back()->with('error',$msg);
        }
        */

        try {

            $errors = 0;

            if($request->users != null && ! empty($request->users)) {

                foreach($request->users as $arr) {

                    if(array_key_exists('id', $arr)) {

                        $user_event = Model::withTrashed()->find($arr['id']);

                        if($user_event != null) {

                          	if(array_key_exists('users_count', $arr)) {
                                $users_count = $arr['users_count'];
                            } else {
								$users_count = $user_event->users_count;
                            }

                            $user_event->update([
                                'status' => 'hold',
                                'users_count' => $users_count,
                                'scan' => null,
                                'scan_at' => null,
                                'get_location' => null,
                              	'message_id' => null,
                              	'is_sent' => null,
                                'sent_from' => null,
                                'is_delivered' => null,
                                'is_read' => null,
                                'qr_sent' => null,
                                'is_accepted' => null,
                                'is_refused' => null,
                                'error_title' => null,
                                'error' => null,
                                'log' => null,
                            ]);

                            $image_path = $event->file;

                            //$code = $user_event->mobile_code->code;
                            //$mobile = substr($user_event->mobile, 1);
                            $mobile = $user_event->mobile;

                            //$to = $code.$mobile;
                            $to = $mobile;
                            $to = str_replace("+","",$to);

                            $template_name = 'wedding_data_v1_ar';
                            $language = 'ar';
                            $image_url = $image_path;
                            $user_name = $user_event->name;


                            $phone_numer_id = $setting->phone_numer_id;
                            $token          = $setting->access_token;

                            // $response = SendTemplateV1($to, $template_name, $language, $image_url, $user_name, $event->title, $phone_numer_id, $token);

                            $sender_id = $setting->sender_id;

                            $param_1   = $user_name;
                            $param_2   = $event->title;
                            $param_3   = Carbon::parse($event->date)->locale('ar')->translatedFormat('l') . ' الموافق ' . $event->date;
                            $param_4   = $event->address;
                            $param_5   = $event->time != null ? $event->time : '07:00 مساء';

                            //$url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name.'&param_1='.$param_1.'&param_2='.$param_2.'&image='.$image_url;
                            $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name.'&param_1='.$param_1.'&param_2='.$param_2.'&param_3='.$param_3.'&param_4='.$param_4.'&param_5='.$param_5.'&image='.$image_url;

                            $response = SendNewTemplateCodeV1($url);

                          	//dd($response,$response->getStatusCode());

                            if ($response != null && $response->getStatusCode() == 200) {

                                $user->update([
                                    'balance' => $user->balance - $users_count
                                ]);

                                // $body = $response->getBody();
                                // $data = json_decode($body, true);

                                $response_data = $response->getBody()->getContents();
                                $data = json_decode($response_data, true);

                                //dd($data);
                                // dd(11,$response_data,json_decode($response_data,true));

                                if(array_key_exists('messages', $data) && count($data['messages']) >= 0 && array_key_exists('id', $data['messages'][0])) {
                                    $message_id = $data['messages'][0]['id'];
                                } else {
                                    $message_id = 0;
                                }

                                $user_event->update([
                                    'is_sent' => 'yes',
                                    'sent_from' => 'dashboard',
                                    'status' => 'sent',
                                    'message_id' => $message_id
                                ]);

                            } else {
                                $user_event->update([
                                    'status' => 'failed',
                                ]);
                            }

                        } else {
                            $errors = $errors + 1;
                        }

                    } else {
                        $errors = $errors + 1;
                    }

                }
 

                return response()->json(['success' => 'تم الأرسال بنجاح']);
            }

        } catch(\Exception $e) {
            dd($e->getMessage(), $e->getLine());
        }

        dd('error-v2');

    }





  	public function event_users_search(Request $request) {

       $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }  
        $event_id = $request->event_id;
        $event_users = EventUsers::where('event_id', $event_id)
            ->when($request->search, function ($q) use ($request) {

                $search = $request->search;

                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%$search%")
                        ->orWhere('mobile', 'like', "%$search%");
                });
            })
            ->paginate(15); // عدد النتائج في الصفحة

        return response()->json([
            'event_users' => $event_users,
            'event_id' => $event_id,
        ]);

    }







    public function destroy($id)
    {
        $Item = Model::withTrashed()->findOrFail($id);
        $Item->delete();

        return response()->json([
            'success' => 'You delete data success', 
        ]);
    }


  	public function event_user_history($id)
    {
        $Item = Model::withTrashed()->findOrFail($id);
        $logs = EventUserLogs::where('event_user_id', $Item->id)
            ->paginate(15); // عدد العناصر في الصفحة

        $logs->getCollection()->transform(function ($item) {
            $item->log = json_decode($item->log, true);
            return $item;
        });


        return response()->json([
            'Item' => $Item, 
            'logs' => $logs, 
        ]);
    }


  	public function send_qr($id)
    {

      	//dd('ok');

      	$setting = Setting::first();

        $user_event = Model::withTrashed()->findOrFail($id);

        $event = $user_event->event;

        /////////////////////////////////////////////////////////////////

      	$user_event->update([ 'is_accepted' => 'yes'  ]);

        $uu_id = $this->unique_uu_id();

        $image_name = $uu_id . '-test-qr.png';

        Qr_Code::create([
          'event_user_id' => $user_event->id,
          'event_id' => $user_event->event_id,
          'qr' => $image_name,
          'uu_id' => $uu_id,
          'counter' => 0
        ]);
/////////////////////////////////////////
  
        // new code
        $this->update_qr($event,$uu_id,$user_event,$image_name);

        $qr_code_path = 'qr_code/' . $image_name; 

        $image_url = asset($qr_code_path);

        //$code = $user_event->mobile_code->code;
        //$mobile = substr($user_event->mobile, 1);
        $mobile = $user_event->mobile;

        //$to = $code.$mobile;
        //$to = $user_event->mobile;
        $to = $mobile;

        $template_name = 'wedding_data_v2_ar';
        $language = 'ar';
        $user_name = $user_event->name;
        $token          = get_whats_setting($event)['token'];
        $sender_id      = get_whats_setting($event)['sender_id'];
        $phone_numer_id = get_whats_setting($event)['sender_id'];

        //$response = SendTemplateV2($to, $template_name, $language, $image_url, $user_name, $phone_numer_id, $token);

        $to = str_replace("+","",$to);

        $url_button = '?q=' . $user_event->event->lat . ',' . $user_event->event->long;

        // $sender_id = $setting->sender_id;
        $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.
        '&phone='.$to.'&template='.$template_name.'&param_1='.$user_event->users_count.
        '&image='.$image_url;

        // $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.
        // '&phone='.$to.'&template='.$template_name.'&param_1='.$user_event->users_count.
        // '&image='.$image_url.'&url_button='.$url_button;
        
        $response = SendNewVTemplateCodeV1($url);
 		// تحويل النص لـ Array
        $responseData = json_decode($response);  

        // التأكد أن الرد ليس فارغاً وأن مصفوفة الرسائل موجودة فعلاً
        if ($responseData && isset($responseData->messages) && !empty($responseData->messages)) {
            $status = $responseData->messages[0]->message_status ?? '';

            if ($status == "accepted") {
                $user_event->update(['qr_sent' => 'yes']);
                return response()->json(['success' => 'تم أرسال QR Scan بنجاح']);
            }
        }  else {

          return response()->json([
              'errors' => 'عفوا فشل أرسال QR Scan ', 
          ]); 
        }

        /////////////////////////////////////////////////////

        ////////////////////////////////////////////////////////////////////




      	//dd($response);
 
    }



  	public function send_new_qr($id)
    {
      	//dd('ok');

      	$setting = Setting::first();

        $user_event = Model::withTrashed()->findOrFail($id);

        $event = $user_event->event;

        ////////////////////////////////////////////////////////////////////

      	$user_event->update([ 'is_accepted' => 'yes'  ]);

        $uu_id = $this->unique_uu_id();

        $image_name = $uu_id . '-test-qr.png';

      	Qr_Code::where('event_user_id',$user_event->id)->delete();

        Qr_Code::create([
          'event_user_id' => $user_event->id,
          'event_id' => $user_event->event_id,
          'qr' => $image_name,
          'uu_id' => $uu_id,
          'counter' => 0
        ]);

        // new code
        $this->update_qr($event,$uu_id,$user_event,$image_name);

        $qr_code_path = 'qr_code/' . $image_name;

        // $bg = 'qr-image-v9.jpg';

        // $link = asset('scan-qr/' . $uu_id);
        // QrCode::size(900)->format('png')->generate($link, $qr_code_path);

        // Image::make($bg)->insert($qr_code_path, 'left', 480, 0)->widen(700)->save($qr_code_path, 100);

        // $destination = public_path($qr_code_path);

        // $new_img = Image::make($destination);

        // $new_img->text($user_event->users_count, 150, 615, function ($font) {
        //   $font->file(public_path('font/OpenSans-Italic.ttf'));
        //   $font->size(40);
        //   $font->color('#eeb534');
        // });

        // $new_img->save($destination);

        $image_url = asset($qr_code_path);

        //$code = $user_event->mobile_code->code;
        //$mobile = substr($user_event->mobile, 1);
        $mobile = $user_event->mobile;

        //$to = $code.$mobile;
        //$to = $user_event->mobile;
        $to = $mobile;

        $template_name = 'wedding_data_v_ar__10';
        $language = 'ar';
        $user_name = $user_event->name;

        $token          = get_whats_setting($event)['token'];
        $sender_id      = get_whats_setting($event)['sender_id'];
        $phone_numer_id = get_whats_setting($event)['sender_id'];

        // $response = SendTemplateV2($to, $template_name, $language, $image_url, $user_name, $phone_numer_id, $token);

      	$to = str_replace("+","",$to);

        //$url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name.'&param_1='.$user_name.'&image='.$image_url;
        $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name.'&image='.$image_url;

        $response = SendNewTemplateCodeV1($url);

      	//dd($response);

        if ($response != null && $response->getStatusCode() == 200) {

          //dd('ok');

          $user_event->update([ 'qr_sent' => 'yes'  ]);


          return response()->json([
              'success' => 'تم أرسال QR Scan  بنجاح', 
          ]); 

        } else {

          return response()->json([
              'errors' => 'عفوا فشل أرسال QR Scan ', 
          ]); 
        }

    }


  	public function accept_user_event($id)
    {
        $user_event = Model::withTrashed()->findOrFail($id);

        $event = Events::find($user_event->event_id);

      	Notifications::create([
          'add_by'         => 'admin',
          'user_id'        => 1,
          'send_to_type'   => 'user',
          'send_to_id'     => $user_event->event->user_id,
          'en_title'       => 'accept event : ' . $user_event->event->title,
          'ar_title'       => 'قبول الدعوه  : ' . $user_event->event->title,
          'en_description' => 'user : ' . $user_event->name . ' accept event : ' . $user_event->event->title,
          'ar_description' => 'المستخدم : ' . $user_event->name . ' قبل الدعوه  : ' . $user_event->event->title,
          'type'           => 'event',
          'item_id'        => $user_event->event->id,
          'user_event_id'  => $user_event != null ? $user_event->id : 0,
          'status'         => 'accept_event',
        ]);

        $user_event->update([ 
            'is_accepted' => 'yes', 
            'scan' => null , 
            'scan_at' => null, 
            'is_refused' => null,
            'status' => 'attend',
            'accept_time' => now(),
        ]);

      	if($event != null && $event->showing_qr == 'yes') {

            $uu_id = $this->unique_uu_id();

            $image_name = $uu_id . '-test-qr.png';

            Qr_Code::create([
              'event_user_id' => $user_event->id,
              'event_id' => $user_event->event_id,
              'qr' => $image_name,
              'uu_id' => $uu_id,
              'counter' => 0
            ]);

            // new code
            $this->update_qr($event,$uu_id,$user_event,$image_name);

            // $param_1 = $user_event->name;

            // $bg = 'qr-image-v9.jpg';

            // $link = asset('scan-qr/' . $uu_id);
            // $qr_code_path = 'qr_code/' . $image_name;
            // QrCode::size(900)->format('png')->generate($link, $qr_code_path);

            // Image::make($bg)->insert($qr_code_path, 'left', 480, 0)->widen(700)->save($qr_code_path, 100);

            // $destination = public_path($qr_code_path);

            // $new_img = Image::make($destination);

            // $new_img->text($user_event->users_count, 150, 615, function ($font) {
            //   $font->file(public_path('font/OpenSans-Italic.ttf'));
            //   $font->size(40);
            //   $font->color('#eeb534');
            // });

            // $new_img->text($user_event->mobile, 190, 680, function ($font) {
            //   $font->file(public_path('font/OpenSans-Italic.ttf'));
            //   $font->size(30);
            //   $font->color('#000');
            //   //$font->align('right'); // Adjust alignment if necessary
            // });

            // $new_img->save($destination);


        }


        return response()->json([
            'success' => 'تم قبول الدعوه', 
        ]);
    }


  	public function refuse_user_event($id)
    {

        $user_event = Model::withTrashed()->findOrFail($id);

        if($user_event && $user_event->event) {

          	Notifications::create([
            	'add_by'         => 'admin',
                'user_id'        => 1,
                'send_to_type'   => 'user',
                'send_to_id'     => $user_event->event->user_id,
                'en_title'       => 'refuse event : ' . $user_event->event->title,
                'ar_title'       => 'رفض الدعوه  : ' . $user_event->event->title,
                'en_description' => 'user : ' . $user_event->name . ' refuse event : ' . $user_event->event->title,
                'ar_description' => 'المستخدم : ' . $user_event->name . ' رفض الدعوه  : ' . $user_event->event->title,
                'type'           => 'event',
                'item_id'        => $user_event->event->id,
                'user_event_id'  => $user_event != null ? $user_event->id : 0,
                'status'         => 'refuse_event',
            ]);

            Qr_Code::where('event_user_id', $user_event->id)->delete();

            $user_event->update([ 'scan' => null , 'scan_at' => null, 'is_refused' => 'yes','is_accepted' => 'no' ,'status' => 'not-attend'  ]);

        }


        return response()->json([
            'success' => 'تم رفض الدعوه', 
        ]);
    }


  	public function qr_is_send($id)
    {
        $user_event = Model::withTrashed()->findOrFail($id);

        $user_event->update([ 'qr_sent' => 'yes' ]);


        return response()->json([
            'success' => 'تم تاكيد الارسال بنجاح', 
        ]);
    }


   	public function is_send_event($id)
    {
        $user_event = Model::withTrashed()->findOrFail($id);

        $user_event->update([ 'is_sent' => 'yes','status' => 'sent' ]);


        return response()->json([
            'success' => 'تم تاكيد الارسال بنجاح', 
        ]);
    }


    public function all_invited_users(Request $request, $id)
    {
        $Item = Events::findOrFail($id);
        $data = EventUsers::where('event_id', $Item->id)
        ->when($request->search, function ($q) use ($request) {

            $search = $request->search;

            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
            });
        })
        ->paginate(15);

        $title = 'كل المدعوين';

        $type = 'all_invited_users';


        return response()->json([
            'Item' => $Item, 
            'data' => $data, 
            'title' => $title, 
            'type' => $type, 
        ]);
    }

    public function excel_all_invited_users(Request $request, $id)
    {
        $Item = Events::findOrFail($id);
        $data = EventUsers::
        where('event_id', $Item->id)
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



    public function event_qr_details(Request $request, $id)
    {
        $Item = Events::findOrFail($id);
        $data = EventUsers::where('event_id', $Item->id)
        ->where('scan', 'yes')
        ->when($request->search, function ($q) use ($request) {

            $search = $request->search;

            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
            });
        })
        ->paginate(15);

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


    public function excel_event_qr_details(Request $request, $id)
    {
        $Item = Events::findOrFail($id);
        $data = EventUsers::where('event_id', $Item->id)
        ->where('scan', 'yes') 
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



    public function confirmed_event_details(Request $request, $id)
    {
        $Item = Events::findOrFail($id);
        $data = EventUsers::where('event_id', $Item->id)
        ->where('status', 'attend')
        ->when($request->search, function ($q) use ($request) {
            $search = $request->search;
            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
            });
        })
        ->paginate(15);

        $title = 'كل المدعوين الذين ينوون الحضور';

        $type = 'confirmed_event_details';


        return response()->json([
            'Item' => $Item, 
            'data' => $data, 
            'title' => $title, 
            'type' => $type, 
        ]);
    }

    public function excel_confirmed_event_details(Request $request, $id)
    {
        $Item = Events::findOrFail($id);
        $data = EventUsers::where('event_id', $Item->id)
        ->where('status', 'attend')
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

    public function confirmed_users_web_chat(Request $request, $id)
    {
        $Item = Events::findOrFail($id);
        $data = EventUserActions::where('event_id', $Item->id)
        ->where('action', 'accept_event')
        ->with("event_user:id,name")
        ->whereHas('event_user', function ($q) use ($request) {
            if ($request->search) {
                $q->where('mobile', 'like', "%{$request->search}%");
            }
        })
        ->paginate(15);

        $title = 'كل المدعوين الذين اكدوا الحضور من الشات الويب';

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



    public function not_attend_event_details(Request $request, $id)
    {
        $Item = Events::findOrFail($id);
        $data = EventUsers::where('event_id', $Item->id)
        ->where('status', 'not-attend')
        ->when($request->search, function ($q) use ($request) {

            $search = $request->search;

            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
            });
        })
        ->paginate(15);

        $title = 'كل المدعوين الذين اعتذرو';


        return response()->json([
            'Item' => $Item, 
            'data' => $data, 
            'title' => $title, 
        ]);
    }

    public function excel_not_attend_event_details(Request $request, $id)
    {
        $Item = Events::findOrFail($id);
        $data = EventUsers::where('event_id', $Item->id)
        ->where('status', 'not-attend')
        ->get();

        $title = 'كل المدعوين الذين اعتذرو';


        return response()->json([
            'Item' => $Item, 
            'data' => $data, 
            'title' => $title, 
        ]);
    }



    public function hold_event_details(Request $request, $id)
    {
        $Item = Events::findOrFail($id);
        $data = EventUsers::where('event_id', $Item->id)
        ->where('status', 'hold')
        ->where('is_new_sent', 0)
        ->whereNull('is_sent')
        ->when($request->search, function ($q) use ($request) {

            $search = $request->search;

            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
            });
        })
        ->paginate(15);

        $title = 'كل المدعوين المنتظرين';

        $type = 'hold';


        return response()->json([
            'Item' => $Item, 
            'data' => $data, 
            'title' => $title, 
            'type' => $type, 
        ]);
    }

    public function excel_hold_event_details(Request $request, $id)
    {
        $Item = Events::findOrFail($id);
        $data = EventUsers::where('event_id', $Item->id)
        ->where('status', 'hold')
        ->where('is_new_sent', 0)
        ->whereNull('is_sent') 
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



  	public function failed_event_details(Request $request, $id)
    {
        $Item = Events::findOrFail($id);

        //$data = EventUsers::where('event_id',$Item->id)->where('status','failed')->get();
        $data = EventUsers::where('event_id', $Item->id)
        ->whereIn('status', ['sent'])
        ->whereNull('is_accepted')
        ->whereNull('is_refused')
        ->where(function ($query) {
            $query->where('is_new_sent', 1)
                ->orWhereNotNull('is_sent');
        })
        ->when($request->search, function ($q) use ($request) {
            $search = $request->search;
            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
            });
        })
        ->paginate(15);

        $title = 'لم يتم تاكيد الحضور';

      	$type = 'failed';
 
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
        ->whereIn('status', ['sent'])
        ->whereNull('is_accepted')
        ->whereNull('is_refused')
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


  	public function non_attendance_event_details(Request $request, $id)
    {
        $Item = Events::findOrFail($id);

        //$data = EventUsers::where('event_id',$Item->id)->where('status','failed')->get();
        $data = EventUsers::where('event_id', $Item->id)
        ->where('status', 'attend')
        ->whereNull('scan')
        ->whereNull('is_refused')
        ->when($request->search, function ($q) use ($request) {
            $search = $request->search;
            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
            });
        })
        ->paginate(15);

        $title = 'عدم الحضور فعليا';

      	$type = 'non_attendance';
 
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



  	public function qr_sent_event_details(Request $request, $id)
    {
        $Item = Events::findOrFail($id);
        $data = EventUsers::where('event_id', $Item->id)
        ->where('qr_sent', 'yes')
        ->when($request->search, function ($q) use ($request) {
            $search = $request->search;
            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
            });
        })
        ->paginate(15);

        $title = 'كل الدعوات (Sent QR)';


 
        return response()->json([
            'Item' => $Item, 
            'data' => $data, 
            'title' => $title,  
        ]); 
    }

  	public function excel_qr_sent_event_details(Request $request, $id)
    {
        $Item = Events::findOrFail($id);
        $data = EventUsers::where('event_id', $Item->id)
        ->where('qr_sent', 'yes')
        ->get();

        $title = 'كل الدعوات (Sent QR)';


 
        return response()->json([
            'Item' => $Item, 
            'data' => $data, 
            'title' => $title,  
        ]); 
    }


  	public function event_messages_search(Request $request) {

       $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
            'type' => 'required',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }   

        $event_id = $request->event_id;

        $Item = Events::findOrFail($event_id);

      	if($request->type == 'congrate_message') {
            $messages = CongratulationMessages::where('event_id', $event_id)
            ->when($request->search, function ($q) use ($request) {

                $search = $request->search;

                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%$search%")
                        ->orWhere('mobile', 'like', "%$search%");
                });
            })
            ->with("reply:id,name,mobile,message,type,message_id")
            ->paginate(15);

			$title = 'رسائل التهنئة';

      		$type = 'congrate_message';

        } else {
            $messages = EventMessages::where('event_id', $event_id)
            ->when($request->search, function ($q) use ($request) {

                $search = $request->search;

                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%$search%")
                        ->orWhere('mobile', 'like', "%$search%");
                });
            })
            ->with("reply:id,name,mobile,message,type,message_id")
            ->paginate(15);

          	$title = 'كل الرسائل';

      		$type = 'event_message';

        }


 
        return response()->json([
            'Item' => $Item, 
            'messages' => $messages, 
            'title' => $title, 
            'type' => $type, 
        ]); 

    }




  	public function congratulations_event_messages_details(Request $request, $id)
    {
        $Item = Events::findOrFail($id);

        $mobiles = Model::withTrashed()->where('event_id',$Item->id)->pluck('mobile')->toArray();

        $mobiles_arr = [];

        foreach($mobiles as $phone) {
            $mobiles_arr[] = ltrim($phone,"+");
        }
        $messages = CongratulationMessages::whereHas('event', function ($event) {
            $event->whereIn('is_open', ['yes', 'current']);
        })
        ->when($mobiles_arr ?? false, function ($q) use ($mobiles_arr) {
            $q->whereIn('mobile', $mobiles_arr);
        })
        ->when($request->search, function ($q) use ($request) {
            $search = $request->search;
            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
            });
        })
        ->paginate(15);

        $title = 'رسائل التهنئة';

      	$type = 'congrate_message';

        return response()->json([
            'Item' => $Item, 
            'messages' => $messages, 
            'title' => $title, 
            'type' => $type, 
        ]);  
    }



    public function event_messages(Request $request, $id)
    {

        $Item = Events::findOrFail($id);

        $mobiles = Model::withTrashed()->where('event_id',$Item->id)->pluck('mobile')->toArray();

        $mobiles_arr = [];

        foreach($mobiles as $phone) {
            $mobiles_arr[] = ltrim($phone,"+");
        }
        $messages = EventMessages::whereHas('event', function ($event) {
            $event->whereIn('is_open', ['yes', 'current']);
        })
        ->when($mobiles_arr ?? false, function ($q) use ($mobiles_arr) {
            $q->whereIn('mobile', $mobiles_arr);
        })
        ->when($request->search, function ($q) use ($request) {
            $search = $request->search;
            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
            });
        })
        ->paginate(15);

        $title = 'كل الرسائل';

      	$type = 'event_message';
  
        return response()->json([
            'Item' => $Item, 
            'messages' => $messages, 
            'title' => $title, 
            'type' => $type, 
        ]);  
    }



  	public function delete_event_messages(Request $request, $id, $type)
    {

      	if($type == 'event_message') {
             $Item = EventMessages::findOrFail($id);
        } else {
             $Item = CongratulationMessages::findOrFail($id);
        }

        $Item->delete();


        return response()->json([
            'success' => 'You delete data success', 
        ]); 
    }



  	public function login_user(Request $request, $id) {

       $validator = Validator::make($request->all(), [
            'users_count' => 'required|numeric',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        
        $Item = Model::withTrashed()->findOrFail($id);

      	$now = Carbon::now();

        // dd($request->all());

        for($i = 1;$i <= $request->users_count;$i++) {
            $Item->update(['scan' => 'yes','scan_at' => $now,'scan_count' => $Item->scan_count + 1]);
        }

        //$Item->update(['scan' => 'yes','scan_at' => $now]);


        return response()->json([
            'success' => 'تم عمل QR Scan  بنجاح', 
        ]);
  	}

    public function send_event_location($id) {

        $user_event = Model::withTrashed()->findOrFail($id);

        $event = $user_event->event;

        $mobile = ltrim($user_event->mobile,"+");

        $setting = Setting::first();

        $token          = get_whats_setting($event)['token'];
        $sender_id      = get_whats_setting($event)['sender_id'];
        $phone_numer_id = get_whats_setting($event)['sender_id'];

        $phone = $mobile;
        // $template_name = 'wedding_data_v7_ar';
        $template_name = 'wedding_data_v15__';
        $param_1 = $user_event->name;

        $url_button = '?q=' . $event->lat . ',' . $event->long;

        $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$phone.'&template='.$template_name.'&url_button='.$url_button;

        $response = SendNewTemplateCodeV1($url);

        if ($response && $response->getStatusCode() == 200) { // 200 OK

            return response()->json([
                'success' => 'تم الارسال بنجاح', 
            ]);
        } else {

          return response()->json([
              'errors' => 'عفوا لقد فشل الارسال ', 
          ]);
        }

  	}


  	private function unique_uu_id()
    {
        $uu_id = random_int(10000, 99999);

        while (Qr_Code::where('uu_id', $uu_id)->exists()) {
            $uu_id = random_int(10000, 99999);
        }


        return $uu_id; 
    }



	public function send_congratulation_message(Request $request)
     {

       $validator = Validator::make($request->all(), [
            'msg1' => 'required',
            'event_users_id' => 'required|exists:event_users,id',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }    


        $event_user_id = $request->event_users_id;

        $user_event = Model::withTrashed()->where('id', $event_user_id)->firstOrFail();


       	CongratulationMessages::create([
          'event_id' => $user_event != null ? $user_event->event_id : 0,
          'event_user_id' => $user_event != null ? $user_event->id : 0,
          'name' => $user_event != null ? $user_event->name : '',
          'mobile' => $user_event->mobile,
          'message' => $request->msg1
        ]);

      	if($user_event != null && $user_event->event) {
      		Notifications::create([
              'add_by'         => 'admin',
              'user_id'        => 1,
              'send_to_type'   => 'user',
              'send_to_id'     => $user_event->event->user_id,
              'en_title'       => 'new congratulation msg to event : ' . $user_event->event->title,
              'ar_title'       => 'تهنئه جديده للدعوه   : ' . $user_event->event->title,
              'en_description' => 'user : ' . $user_event->name . ' send congratulation message : ' . $request->msg1,
              'ar_description' => 'المستخدم : ' . $user_event->name . '  أرسل التهنئة  : ' . $request->msg1,
              'type'           => 'event-msg',
              'item_id'        => $user_event->event->id,
              'user_event_id'  => $user_event != null ? $user_event->id : 0,
              'status'         => 'new_msg',
            ]);
        }

        return response()->json([
            'success' => 'تم ارسال الرساله بنجاح', 
        ]);
    }


    public function send_congratulations($id) {

      $setting = Setting::first();

      $event = Events::where('id', $id)->firstOrFail();

      $EventUsers = EventUsers::where('event_id',$id)->get();

      $arr = [];

      if($EventUsers != null && $EventUsers->count() > 0) {

        foreach($EventUsers as $user_event) {

          $mobile = $user_event->mobile;

          $to = $mobile;

          $template_name = 'wedding_data_v10_ar_new';
          $language = 'ar';


          $token          = get_whats_setting($event)['token'];
          $sender_id      = get_whats_setting($event)['sender_id'];
          $phone_numer_id = get_whats_setting($event)['sender_id'];

          $whatsapp = '201008478014';

          // $response = SendTemplateV5($to,$template_name,$language,$whatsapp,$phone_numer_id,$token);

          $sender_id = $setting->sender_id;

          $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name;

          $response = SendNewTemplateCodeV1($url);

          if (! ($response != null && $response->getStatusCode() == 200)) {

            $arr[] = $user_event->name;

          }
        }

        if(empty($arr)) {

          return response()->json([
              'success' => 'تم ارسال التهنئه بنجاح', 
          ]); 
        } else {

            return response()->json([
                'errors' => 'عفوا لم يتم ارسال تهنئه لبعض المستخدمين', 
            ]); 
        }

      } else {

                return response()->json([
                    'errors' => 'عفوا لا يوجد اي مستخمدمين', 
                ]); 
      }

    }


    // send_congratulation_messages
    public function send_congratulation_messages(Request $request)
    {
       $validator = Validator::make($request->all(), [
            'sending_type' => 'required|in:old_send,new_send',
            'event_id' => 'required|exists:events,id',
            'users' => 'required|array',
            'users.*.id' => 'required',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }  
        $setting = Setting::first(); 

        $event_id = $request->event_id;

        $event = Events::where('id', $event_id)->firstOrFail();

        /* ***************************************************************************** */

        $ultramsg_token="7ye6ifujyug0u46g"; // Ultramsg.com token
        $instance_id="instance109805"; // Ultramsg.com instance id
        $client = new \UltraMsg\WhatsAppApi($ultramsg_token,$instance_id);

        $priority=0;
        $referenceId="SDK";
        $nocache=true;

        /* ***************************************************************************** */

        try {

            $errors = 0;

            if($request->users != null && ! empty($request->users)) {

                foreach($request->users as $arr) {

                    if(array_key_exists('id', $arr)) {

                        $user_event = Model::withTrashed()->find($arr['id']);

                        if($user_event != null) {

                          	$user_name = $user_event->name;

                            $mobile = $user_event->mobile;

                            //$to = $code.$mobile;
                            $to = $mobile;
                            $to = str_replace("+","",$to);

                            if($request->sending_type == 'old_send') {

                                $template_name = 'wedding_data_v10_ar_new';
                                $language = 'ar';

                                $token          = get_whats_setting($event)['token'];
                                $sender_id      = get_whats_setting($event)['sender_id'];
                                $phone_numer_id = get_whats_setting($event)['sender_id'];

                                // $response = SendTemplateV10($to,$template_name,$language,$message,$phone_numer_id,$token);

                                $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name;

                                $response = SendNewTemplateCodeV1($url);

                              	// dd($response);

                                //$response = SendTemplateV10($to,$template_name,$language,$message,$phone_numer_id,$token);

                                if ($response != null && $response->getStatusCode() == 200) {

                                    $body = $response->getBody();
                                    $data = json_decode($body, true);

                                    $user_event->update([
                                        'is_send_congratulation' => 1,
                                    ]);

                                } else {
                                    $user_event->update([
                                        'status' => 'failed-v2',
                                    ]);
                                }

                            } else {

                                $caption = 'حياكم الله ،،' .
                                'اكتمل حفلنا بحضوركم نتمنى لكم ليلة ممتعة🌹';

                                // $api=$client->sendChatMessage($to,$body);
                                $api = $client->sendChatMessage($to,$caption,$priority,$referenceId,$nocache);

                                // $api2 = $client->sendContactMessage($to,'96597378181',$priority=0,$referenceId="SDK");

                                if(! empty($api) && isset($api['sent']) && $api['sent'] == 'true'  && isset($api['message']) && $api['message'] == 'ok') {
                                    // dd('ok');

                                    $user_event->update([
                                        'is_send_congratulation' => 1,
                                    ]);

                                } else {
                                    // dd('not ok',$api);
                                    $errors = $errors + 1;
                                }

                            }

                        } else {
                            $errors = $errors + 1;
                        }

                    } else {
                        $errors = $errors + 1;
                    }

                }


                return response()->json([
                    'success' => 'تم الأرسال بنجاح', 
                ]); 
            }

        } catch(\Exception $e) {
            dd($e->getMessage(), $e->getLine());
        }

        dd('error-v2');

    }



  	public function send_apologize_message(Request $request)
     {

       $validator = Validator::make($request->all(), [
            'msg2' => 'required',
            'user_id' => 'required|exists:event_users,id',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }   

        $event_user_id = $request->user_id;

        $user_event = Model::withTrashed()->where('id', $event_user_id)->firstOrFail();


       	EventMessages::create([
            'event_id' => $user_event != null ? $user_event->event_id : 0,
            'event_user_id' => $user_event != null ? $user_event->id : 0,
            'name' => $user_event != null ? $user_event->name : '',
            'mobile' => $user_event->mobile,
            'message' => $request->msg2
        ]);

      	if($user_event != null && $user_event->event) {
      		Notifications::create([
              'add_by'         => 'admin',
              'user_id'        => 1,
              'send_to_type'   => 'user',
              'send_to_id'     => $user_event->event->user_id,
              'en_title'       => 'new apology msg to event : ' . $user_event->event->title,
              'ar_title'       => 'اعتذار جديد للدعوه   : ' . $user_event->event->title,
              'en_description' => 'user : ' . $user_event->name . ' send apology message : ' . $request->msg2,
              'ar_description' => 'المستخدم : ' . $user_event->name . '  أرسل الأعتذار  : ' . $request->msg2,
              'type'           => 'event-msg',
              'item_id'        => $user_event->event->id,
              'user_event_id'  => $user_event != null ? $user_event->id : 0,
              'status'         => 'new_msg',
            ]);
        }


        return response()->json([
            'success' => 'تم ارسال الرساله بنجاح', 
        ]);
    }





    private function update_qr($event,$uu_id,$user_event,$image_name) {

        $color = $this->hexToRgb($event->color);

        if($event->image != null) {

            $bg = $event->image;
            $image_name = $uu_id . '-test-qr.png';

            $link = asset('scan-qr/' . $uu_id);
            $qr_code_path = 'qr_code/' . $image_name;

            // إنشاء QR كـ صورة مؤقتة
            QrCode::format('png')
            ->size(300)
            ->color($color[0],$color[1],$color[2])
            ->backgroundColor(0, 0, 0, 0) // RGBA => شفاف
            ->generate($link, $qr_code_path);

            // افتح الخلفية
            $background = Image::make($bg);

            // افتح QR
            $qr = Image::make($qr_code_path);

            // احسب الإحداثيات لتوسيط QR في الأسفل
            $x = intval(($background->width() - $qr->width()) / 2); // مركز أفقي
            $y = $background->height() - $qr->height() - 180; // من الأسفل

            // أدرج QR
            //$background->insert($qr, 'top-left', $x, $y - 350);
            $background->insert($qr, 'top-left', $x, $y - 170);

            // احسب مركز الصورة للنص
            $center_x = intval($background->width() / 2);
            $text_y = $y + $qr->height() - 380; // أسفل QR بـ 20px

            // $Arabic = new \ArPHP\I18N\Arabic('Glyphs');
            // $name = $Arabic->utf8Glyphs($user_event->mobile);

          	/*
            $name = $user_event->mobile;

            // أضف النص في وسط الصورة أفقيًا وأسفل QR
            $background->text($name, $center_x, $text_y, function ($font) {
                $font->file(public_path('font/OpenSans.ttf'));
                $font->size(26);
                $font->color('#000');
                $font->align('center');
                $font->valign('top');
            });
            */


            // احسب مركز الصورة للنص
            $text_y2 = $y + $qr->height() + 40; // أسفل QR بـ 20px

            // $Arabic2 = new \ArPHP\I18N\Arabic('Glyphs');
            // $user_count_label = 'عدد الدخول ' . $user_event->users_count . '';
            // $name2 = $Arabic2->utf8Glyphs($user_count_label);

            if($user_event->users_count > 1) {

                $user_count_label = $user_event->users_count;
                $name2 = $user_count_label;

                // أضف النص في وسط الصورة أفقيًا وأسفل QR
                $background->text($name2, $center_x, $text_y2, function ($font) {
                    $font->file(public_path('font/OpenSans.ttf'));
                    $font->size(26);
                    $font->color('#000');
                    $font->align('center');
                    $font->valign('top');
                });

            }


            // حفظ النتيجة
            $background->save(public_path($qr_code_path), 100);

        } else {

            $bg = 'qr-image-v9.jpg';

            $link = asset('scan-qr/' . $uu_id);
            $qr_code_path = 'qr_code/' . $image_name;
            QrCode::size(450)->format('png')->generate($link, $qr_code_path);

            Image::make($bg)->insert($qr_code_path, 'left', 320, 0)->widen(450)->save($qr_code_path, 100);

            $destination = public_path($qr_code_path);

            $new_img = Image::make($destination);

          	if($user_event->users_count > 1) {
              $new_img->text($user_event->users_count, 115, 412, function ($font) {
                $font->file(public_path('font/OpenSans-Italic.ttf'));
                $font->size(25);
                $font->color('#000');
              });
            }

          	/*
            $new_img->text($user_event->mobile, 190, 680, function ($font) {
              $font->file(public_path('font/OpenSans-Italic.ttf'));
              $font->size(30);
              $font->color('#000');
              //$font->align('right'); // Adjust alignment if necessary
            });
            */

            $new_img->save($destination);

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


        return [
          $r, 
          $g, 
          $b,
        ];
    }




}
