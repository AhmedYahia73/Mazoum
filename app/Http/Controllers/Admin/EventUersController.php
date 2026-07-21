<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\EventUserImport;
use App\Jobs\SendEventPdfJob;
use App\Events\WattsChat as WattsChatEvent;
use App\Models\CongratulationMessages;
use App\Models\EnterUserEvent;
use App\Models\EventFamily;
use App\Models\EventMessages;
use App\Models\Events;
use App\Models\EventUserActions;
use App\Models\EventUserLogs;
use App\Models\EventUsers as Model;
use App\Models\EventUsers;
use App\Models\NewSetting;
use App\Models\Notifications;
use App\Models\Qr_Code;
use App\Models\Setting;
use App\Models\User;
use App\Models\WattsChat as WattsChatModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManagerStatic as Image;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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

    public function import_users(Request $request)
    {
       $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv',
            'event_id' => 'required|exists:events,id',
            'user_id' => 'required|exists:users,id',
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
                        'status' => 'hold',
                        'user_id' => $request->user_id
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
                        "send_type" => "link",
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
                        " وذلك بمشيئة الله تعالى يوم " . $day_name ." الموافق 📆 " .
                        $row->event->date  . PHP_EOL . PHP_EOL .
                        " وقت الاستقبال ⏱️ " . $row->event->time . " مساءًً" . PHP_EOL . PHP_EOL .
                        "📍مكان الحفـل " . $row->event->address . PHP_EOL . PHP_EOL . 
                        "عدد الدعوات " . $row->users_count . PHP_EOL . PHP_EOL .
                        "فضلاً الدخول على الرابط والضغط على (قبـول الدعـوة) لتأكيد الحضور، أو اختيار (الاعتذار) في حال عـدم التمكن من الحضور." . PHP_EOL .
                        // "يرجي التأكيد أو الاعتذار خلال 24 ساعة حتى لا يتم الغاء الدعوة. قم بضغط على الرابط لمعرفة تفاصيل المناسبة" . PHP_EOL . PHP_EOL .
                        "https://www.mazoominvitations.com/event-login/".$code;

                  if($request->file_type == 'image') {

                    $image = $row->event->file;
                    $caption .= "?type=image"; 
                    // $api=$client->sendChatMessage($to,$body);
                    $api = $client->sendImageMessage($to,$image,$caption,$priority,$referenceId,$nocache);

                  } 
                  elseif($request->file_type == 'pdf'){
                    $document = $row->event->pdf;
                    $caption .= "?type=pdf";
                    SendEventPdfJob::dispatch($row->id, $row->event->id, $ultramsg_token, $instance_id, $row->event->pdf_bottom);
		            // $api = $client->sendDocumentMessage($to,"invetation",$document,$caption,$priority,$referenceId,$nocache);
                  }
                  else {

                    $caption .= "?type=video";
                    $video = $row->event->video;

                    // $api=$client->sendChatMessage($to,$body);
                    $api = $client->sendVideoMessage($to,$video,$caption,$priority,$referenceId,$nocache);

                  }

                  ///////////////////////////////////////////////////////////////////////

                  if(! empty($api) && isset($api['sent']) && $api['sent'] == 'true'  && isset($api['message']) && $api['message'] == 'ok') {

                    // dd('ok');
                    $row->update([
                        'is_new_sent' => 1, 
                        'is_sent' => "yes", 
                        'is_delivered' => "yes", 
                    ]);

                    $user->update([
                      'balance' => $user->balance - $row->users_count,
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
                	Model::withTrashed()->where('id',$arr['id'])->forceDelete();
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
            'phone_setting_id' => 'required|exists:new_settings,id',
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

                            $url_button = '?q=' . $user_event->event->lat . ',' . $user_event->event->long;

                            $user_name = $user_event->name;

                            $mobile = $user_event->mobile;

                            //$to = $code.$mobile;
                            $to = $mobile;
                            $to = str_replace("+","",$to);

                            //$time = $event->time . ' مساءًً';
                          	$date = $request->date;
                          	$time = $request->time;

                            $template_name = 'car_msg3_';

                            if($request->sending_type2 == 'old_send') {

                                $language = 'ar';

                                $token          = get_whats_setting($event)['token'];
                                $sender_id      = $this->get_phone_id($request->phone_setting_id);
                                $phone_numer_id = $this->get_phone_id($request->phone_setting_id);

                                $param_1 = $message;
                                $param_2 = $time;
                                $param_3 = $date;

                                $response = SendCarMsgTemplate($to,$template_name,$language,$url_image,$param_1,$param_2,$param_3,$phone_numer_id,$token);

                                // if($event->country_code == 'kw') {

                                //   //$url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name.'&param_1='.$user_name.'&param_2='.$message.'&url_button='.$url_button.'&image='.$url_image;
                                //   // $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name.'&param_1='.$message.'&url_button='.$url_button.'&image='.$url_image.'&url_button='.$url_button;
                                //   $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name.'&param_1='.$message.'&param_2='.$time.'&param_3='.$date.'&url_button='.$url_button.'&image='.$url_image;

                                //   $response = SendNewTemplateCodeV1($url);

                                // } else {

                                //   $param_1 = $user_name;
                                //   $param_2 = $time;
                                //   $param_3 = $date;

                                //   $response = SendCarMsgTemplate($to,$template_name,$language,$url_image,$param_1,$param_2,$param_3,$phone_numer_id,$token);
                                // }

                                //$response = SendTemplateV10($to,$template_name,$language,$message,$phone_numer_id,$token);

                                if ($response != null && $response->getStatusCode() == 200) {

                                    $user_event->update([
                                        'remember' => 1,
                                    ]);
                                    $body = $response->getBody();
                                    $data = json_decode($body, true);
                                    $message = WattsChatModel::create([
                                        'phone'        => $to,
                                        'name'         => "Admin",
                                        'message'      => $template_name,
                                        'is_sent_by_me'=> true,
                                        'message_id'   => 0,
                                        'from'         => "Admin",
                                        "template_name" => $template_name,
                                        "event_user_id" => $user_event->id,
                                        "event_id" => $event->id,
                                        "phone_numer_id" => $phone_numer_id,
                                    ]);

                                } else {
                                    $user_event->update([
                                        'status' => 'failed-v2',
                                    ]);
                                }

                            } else {

                                $caption = "ضيفتنـا الغاليـة , ننتظـرك يوم ". $date ." في تمــام الساعة "  . $time . "  تشرفينــا لحضور " . $request->message2 . ' 🌺🌺 ';

                                // $caption2 = 'تحرص الشركة على تقديم المساعدة للضيف حتى لا توجه اي صعوبات في دخول المناسبة تم ارسال الكود مره ثانية ,يرجى العلم ان الكود نفس الكود المرسل في السابق وليس كودا جديداً ';

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
                                    $user_event->update([
                                        'remember' => 1,
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
            dd($e,$e->getMessage(), $e->getLine());
        }

        dd('error-v2'); 

    }



    // // send_event_users
    // public function send_custom_message(Request $request)
    // {
    //     $setting = Setting::first();

    //     $request->validate([
    //         'sending_type' => 'required|in:old_send,new_send',
    //         'message' => 'required',
    //         'file'  => 'nullable|image',
    //         'event_id' => 'required|exists:events,id',
    //         'users' => 'required',
    //     ]);

    //     $event_id = $request->event_id;

    //     $event = Events::where('id', $event_id)->firstOrFail();

    //   	$path = 'images';
    //   	$filename = '';

    //     if($request->file('file') != null && $request->file != null) {

    //         $extension = $request->file('file')->extension();
    //         $filename = uniqid() . '.' . $extension;
    //         $request->file('file')->move($path, $filename);

    //         $url_image = asset('images/'.$filename);

    //     } else {
    //         $url_image = $event->file;
    //     }

    //     /* ***************************************************************************** */

    //     $ultramsg_token="7ye6ifujyug0u46g"; // Ultramsg.com token
    //     $instance_id="instance109805"; // Ultramsg.com instance id
    //     $client = new \UltraMsg\WhatsAppApi($ultramsg_token,$instance_id);

    //     $priority=0;
    //     $referenceId="SDK";
    //     $nocache=true;

    //     /* ***************************************************************************** */

    //     try {

    //         $errors = 0;

    //         if($request->users != null && ! empty($request->users)) {

    //             foreach($request->users as $arr) {

    //                 if(array_key_exists('id', $arr)) {

    //                     $user_event = Model::withTrashed()->find($arr['id']);

    //                     if($user_event != null) {

    //                       	$user_name = $user_event->name;

    //                         $mobile = $user_event->mobile;

    //                         //$to = $code.$mobile;
    //                         $to = $mobile;
    //                         $to = str_replace("+","",$to);

    //                         if($request->sending_type == 'old_send') {


                                // if($event->country_code == 'kw') {

                                //   $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name.'&param_1='.$user_name.'&param_2='.$message.'&image='.$url_image;

                                //   $response = SendNewTemplateCodeV1($url);

                                // } else {
                                //   //dd($user_name,$message);
                                //   $response = SendCustomMessageTemplate($to,$template_name,$language,$user_name,$message,$number,$phone_numer_id,$token);
                                // }


                              	// dd($response);

    //                             //$response = SendTemplateV10($to,$template_name,$language,$message,$phone_numer_id,$token);

    //                             if ($response != null && $response->getStatusCode() == 200) {

    //                                 $body = $response->getBody();
    //                                 $data = json_decode($body, true);

    //                             } else {
    //                                 $user_event->update([
    //                                     'status' => 'failed-v2',
    //                                 ]);
    //                             }

    //                         } else {

    //                             $caption = $user_event->name . PHP_EOL . $request->message;

    //                             // $api=$client->sendChatMessage($to,$body);
    //                             $api = $client->sendImageMessage($to,$url_image,$caption,$priority,$referenceId,$nocache);

    //                             // $api2 = $client->sendContactMessage($to,'96597378181',$priority=0,$referenceId="SDK");

    //                             if(! empty($api) && isset($api['sent']) && $api['sent'] == 'true'  && isset($api['message']) && $api['message'] == 'ok') {
    //                                 // dd('ok');
    //                             } else {
    //                                 // dd('not ok',$api);
    //                                 $errors = $errors + 1;
                    
    // // send_event_users
    public function send_custom_message(Request $request)
    {
       $validator = Validator::make($request->all(), [
            'sending_type' => 'required|in:old_send,new_send',
            'message' => 'required',
            'file'  => 'nullable',
            'event_id' => 'required|exists:events,id',
            'users' => 'required|array',
            'users.*.id' => 'required',
            'phone_setting_id' => 'required|exists:new_settings,id',
            "type" => "in:image,pdf,video"
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
            
            if($request->type == "pdf"){
                $url_image = $event->pdf;
            }
            elseif($request->type == "video"){
                $url_image = $event->video;
            }
            else{
                $url_image = $event->file;
            }
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
                                $sender_id      = $this->get_phone_id($request->phone_setting_id);
                                $phone_numer_id = $this->get_phone_id($request->phone_setting_id);

                                // $response = SendTemplateV10($to,$template_name,$language,$message,$phone_numer_id,$token);

                                // $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name.'&param_1='.$user_name.'&param_2='.$message.'&image='.$url_image;

                                // $response = SendNewTemplateCodeV1($url);
 
                                $type = "image";
                                if($request->type == "pdf"){
                                    $template_name = 'pdf_message';
                                    $type = "document";
                                }
                                elseif($request->type == "video"){
                                    $template_name = 'vide_message';
                                    $type = "video";
                                }
                                else{
                                    $template_name = 'custom_message';
                                    $type = "image";
                                }
                                $language = 'ar';

                                $message = $request->message;

                                $token          = get_whats_setting($event)['token'];
                                $sender_id      = $this->get_phone_id($request->phone_setting_id);
                                $phone_numer_id = $this->get_phone_id($request->phone_setting_id);

                                $number = '966593907079';

                                $param1 = $user_event->name;
                                $param2 = $message;
                                $image_url = $url_image;

                                // $response = SendCustomMessageTemplate($to,$template_name,$language,$user_name,$message,$number,$phone_numer_id,$token);
                                $response = SendCustomMessageV2ArTemplate($to,$template_name,$language,$param1,$param2,$image_url,$phone_numer_id,$token, $type);
                              	// dd($response);

                                //$response = SendTemplateV10($to,$template_name,$language,$message,$phone_numer_id,$token);

                                if ($response != null && $response->getStatusCode() == 200) {

                                    $message = WattsChatModel::create([
                                        'phone'        => $to,
                                        'name'         => "Admin",
                                        'message'      => $template_name,
                                        'is_sent_by_me'=> true,
                                        'message_id'   => 0,
                                        'from'         => "Admin",
                                        "template_name" => $template_name,
                                        "event_user_id" => $user_event->id,
                                        "event_id" => $event->id,
                                        "phone_numer_id" => $phone_numer_id,
                                    ]);
                                    $body = $response->getBody();
                                    $data = json_decode($body, true);

                                } else {
                                    $user_event->update([
                                        'status' => 'failed-v2',
                                        "send_type" => "meta",
                                    ]);
                                }

                            } else {

                                $caption = '' . $user_event->name . PHP_EOL . PHP_EOL .
                                ' ' . $request->message;
 
                                // $api=$client->sendChatMessage($to,$body);
                                if($request->type == "pdf"){ 
                                    $api = $client->sendDocumentMessage($to, "msg_pdf", $url_image,$caption,$priority,$referenceId,$nocache);
                                }
                                elseif($request->type == "video"){ 
                                    $api = $client->sendVideoMessage($to, $url_image,$caption,$priority,$referenceId,$nocache);
                                }
                                else{
                                    $api = $client->sendImageMessage($to,$url_image,$caption,$priority,$referenceId,$nocache);
                                }

                                // $api2 = $client->sendContactMessage($to,'96597378181',$priority=0,$referenceId="SDK");

                                if(! empty($api) && isset($api['sent']) && $api['sent'] == 'true'  && isset($api['message']) && $api['message'] == 'ok') {
                                    
                                    $user_event->update([ 
                                        "send_type" => "link",
                                    ]);
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
            'event_users.*.suit_num' => 'numeric',
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
                        'status' => 'hold', 
                        'suit_num' => isset($arr['suit_num']) ? $arr['suit_num'] : 0,
                    ]);

                  }

                }
            }

        }


        return response()->json([
            'success' => 'تم الحفظ بنجاح', 
        ]);

    }


    // user_save_event_users
    public function user_save_event_users(Request $request)
    { 
       $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
            'user_id' => 'required|exists:users,id',
            'event_users.*.name' => 'required',
            'event_users.*.mobile' => 'required|numeric',
          	'event_users.*.users_count' => 'required|numeric|min:1',
            'event_users.*.suit_num' => 'numeric',
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
                        'status' => 'hold',
                        "user_id" => $request->user_id,
                        "suit_num" => isset($arr['suit_num']) ? $arr['suit_num'] : 0,
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
            'old_event_users.*.suit_num' => 'numeric',
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
                        'suit_num' => isset($item['suit_num']) ? $item['suit_num'] : 0,
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

    public function event_family($id) {

        $event = Events::where('id', $id)->firstOrFail();

        $event_users = EventFamily::where('event_id',$id)->get();

        return response()->json([
            'event_users' => $event_users, 
            'event_id' => $id, 
        ]);

    }

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
                    'scan_qr' => 'no',
                    "user_id" => $event->user_id ?? null,
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
            'phone_setting_id' => 'required|exists:new_settings,id',
            'type' => 'in:image,video,pdf'
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }


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

                            // get or create QR record
                            $qr_record = Qr_Code::where('event_user_id', $user_event->id)->latest()->first();
                            if ($qr_record) {
                                $uu_id      = $qr_record->uu_id;
                                $image_name = $qr_record->qr;
                            } else {
                                $uu_id      = $this->unique_uu_id();
                                $image_name = $uu_id . '-test-qr.png';
                                Qr_Code::create([
                                    'event_user_id' => $user_event->id,
                                    'event_id'      => $user_event->event_id,
                                    'qr'            => $image_name,
                                    'uu_id'         => $uu_id,
                                    'counter'       => 0,
                                ]);
                            }

                            // $this->update_qr($event, $uu_id, $user_event, $image_name);

                            // $qr_code_path = 'qr_code/' . $uu_id . '-test-qr.png';
                            // $image_url    = asset($qr_code_path);
                            // //$code = $user_event->mobile_code->code;
                            // //$mobile = substr($user_event->mobile, 1);
                            // $mobile = $user_event->mobile;

                            // //$to = $code.$mobile;
                            // $to = $mobile;
                            // $to = str_replace("+","",$to);

                            // $template_name = 'wedding_data_v1_ar';
                            // $language = 'ar';
                            // $user_name = $user_event->name;

                            // $token          = get_whats_setting($event)['token'];
                            // $sender_id      = $this->get_phone_id($request->phone_setting_id);
                            // $phone_numer_id = $this->get_phone_id($request->phone_setting_id);

                          	//dd($token,$sender_id,$phone_numer_id);
                            // $response = SendTemplateV1($to, $template_name, $language, $image_url, $user_name, $event->title, $phone_numer_id, $token);


                            //$code = $user_event->mobile_code->code;
                            //$mobile = substr($user_event->mobile, 1);
                            $mobile = $user_event->mobile;

                            //$to = $code.$mobile;
                            $to = $mobile;
                            $to = str_replace("+","",$to);

                            if($request->type && $request->type == "video"){
                                $template_name = 'wedding__video';
                                $image_path = $event->video;
                                $header_type = 'video';
                                $send_buttons = false;
                            }
                            elseif($request->type && $request->type == "pdf"){
                                $template_name = 'wedding__pdf';
                                $image_path = $event->pdf;
                                $header_type = 'document';
                                $send_buttons = false;
                            } 
                            else{
                                $template_name = 'wedding_data_v1_ar';
                                $image_path = $event->file;
                                $header_type = 'image';
                                $send_buttons = true;
                            }
                            $language = 'ar';
                            $image_url = $image_path;
                            $user_name = $user_event->name;

                            $token          = get_whats_setting($event)['token']; 
                            $phone_numer_id = $this->get_phone_id($request->phone_setting_id);

                            $param_1   = $user_name;
                            $param_2   = $event->title;
                            $param_3   = Carbon::parse($event->date)->locale('ar')->translatedFormat('l') . ' الموافق ' . $event->date;
                            $param_4   = $event->address;
                            $param_5   = $event->time != null ? $event->time .' مساءً ' : '07:00 مساءً';
							$param_6   = $users_count > 10 ? 10 : $users_count;
                            $phone_number = $this->get_phone_number($request->phone_setting_id);
                            
                            $user_event->update([
                                "phone_number" => $phone_number
                            ]);
                          	/*
                          	$phone_numer_id = '746157308570599';
                            $sender_id      = '746157308570599';
                            $token          = 'EABIy7zT1dfYBO304MlaYIQZBalGto0d1oPSCKHXEosSCsaLIdxE6QgftNNSLuhG37zirzBTMpK8HprkTRtlLyQZB1evrzBItZBW8y8LgZAYQ1pd6y64GtnMmKUZCjlY0QAZBhvu0VErD7fPzO8iz0cg0OrZBC8ovZA1F5ZCLzWa85nwaL1jWP8WYaa8yI1Ffkmvsy0QHjRrU5bSMJLS8b9bt7ZA2c0Ys8WYvlTMufprZCQ5ZCiAGTqGfzO9LcVY8S9CdpuY1PZBD1phEneQZDZD';
                          	*/
 
                            $response = SendWeddingDataV1ArTemplate($to,$template_name,$language,$param_1,$param_2,$param_3,$param_4,$param_5,$param_6,$image_url,$phone_numer_id,$token, $header_type);

                            // if($event->country_code == 'kw') {

                            //   //$url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name.'&param_1='.$param_1.'&param_2='.$param_2.'&image='.$image_url;
                            //   //$url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name.'&param_1='.$param_1.'&param_2='.$param_2.'&param_3='.$param_3.'&param_4='.$param_4.'&param_5='.$param_5.'&image='.$image_url;
                            //   $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name.'&param_1='.$param_1.'&param_2='.$param_2.'&param_3='.$param_3.'&param_4='.$param_4.'&param_5='.$param_5.'&param_6='.$param_6.'&image='.$image_url;

                            //   $response = SendNewTemplateCodeV1($url);

                            // } else {

                            //   $response = SendWeddingDataV1ArTemplate($to,$template_name,$language,$param_1,$param_2,$param_3,$param_4,$param_5,$param_6,$image_url,$phone_numer_id,$token);

                            // }

                          	//dd($response,$response->getStatusCode());
                            //dd($response->getBody());
                            if ($response != null && $response->getStatusCode() == 200) {
                                $message = WattsChatModel::create([
                                    'phone'        => $to,
                                    'name'         => "Admin",
                                    'message'      => $template_name,
                                    'is_sent_by_me'=> true,
                                    'message_id'   => 0,
                                    'from'         => "Admin",
                                    "template_name" => $template_name,
                                    "event_user_id" => $user_event->id,
                                    "event_id" => $event->id,
                                    "phone_numer_id" => $phone_numer_id,
                                ]);
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
                                    'message_id' => $message_id,
                                    "send_type" => "meta"
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
                
                return response()->json(['success', 'تم الأرسال بنجاح']);
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
        // delete_selected_event_users
        $Item = Model::withTrashed()->findOrFail($id);

        // الحذف النهائي من قاعدة البيانات
        $Item->forceDelete(); 

        // حذف البيانات المتعلقة به
        EventUserActions::where("event_user_id", $id)->delete();

        return response()->json([
            'success' => 'Data permanently deleted successfully', 
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


  	public function send_qr(Request $request, $id)
    {

       $validator = Validator::make($request->all(), [
            'phone_setting_id' => 'required|exists:new_settings,id',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
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

        $template_name = 'wedding_data_v2_ar';
        $language = 'ar';
        $user_name = $user_event->name;

        $token          = get_whats_setting($event)['token'];
        $sender_id      = $this->get_phone_id($request->phone_setting_id);
        $phone_numer_id = $this->get_phone_id($request->phone_setting_id);

        //$response = SendTemplateV2($to, $template_name, $language, $image_url, $user_name, $phone_numer_id, $token);

        $to = str_replace("+","",$to);

        $url_button = '?q=' . $user_event->event->lat . ',' . $user_event->event->long;

        // $sender_id = $setting->sender_id;

        if($user_event->send_type == "meta"){
            $response = SendWeddingDataV2ArTemplate($to,$template_name,$language,$user_event->accept_count,$image_url,$phone_numer_id,$token);
            if ($response != null && (is_array($response) || $response->getStatusCode() == 200)) {
                $message = WattsChatModel::create([
                    'phone'        => $to,
                    'name'         => "Admin",
                    'message'      => $template_name,
                    'is_sent_by_me'=> true,
                    'message_id'   => 0,
                    'from'         => "Admin",
                    "template_name" => $template_name,
                    "event_user_id" => $user_event->id,
                    "event_id" => $event->id,
                    "phone_numer_id" => $phone_numer_id,
                ]);
            }
        }
        else{

            $ultramsg_token="7ye6ifujyug0u46g"; // Ultramsg.com token
            $instance_id="instance109805"; // Ultramsg.com instance id
            $client = new \UltraMsg\WhatsAppApi($ultramsg_token,$instance_id);  
  
            $text = "باركود الدخـول الخـاص بـك, فضـلاً تأكد من حفـظ الصـورة في هاتفك لإبرازهــا عند دخـول المناسبة.
                    عدد الضيـوف  : ( " . $user_event->accept_count . " ) 🌺";
            // $api=$client->sendChatMessage($to,$body);
            $api2 = $client->sendImageMessage($mobile, $image_url, $text, 0, "SDK");
            $response = ["success"];
        }

        // if($event->country_code == 'kw') {

        //   $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name.'&param_1='.$user_event->users_count.'&image='.$image_url;
        //   $response = SendNewTemplateCodeV1($url);

        // } else {

        //   $response = SendWeddingDataV2ArTemplate($to,$template_name,$language,$user_event->users_count,$image_url,$phone_numer_id,$token);

        // }


      	//dd($response);

        if ($response != null && (is_array($response) || $response->getStatusCode() == 200)) {

          $user_event->update([ 'qr_sent' => 'yes'  ]);

           return response()->json(['status' => 'success', 'message' => 'تم أرسال QR Scan  بنجاح']);

        } else {
        	return response()->json(['status' => 'error', 'message' => 'عفوا فشل أرسال QR Scan ']);
        }  
 
    }



  	public function send_new_qr(Request $request, $id)
    {

       $validator = Validator::make($request->all(), [
            'phone_setting_id' => 'required|exists:new_settings,id',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
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
        $sender_id      = $this->get_phone_id($request->phone_setting_id);
        $phone_numer_id = $this->get_phone_id($request->phone_setting_id);

        //$response = SendTemplateV2($to, $template_name, $language, $image_url, $user_name, $phone_numer_id, $token);

        $to = str_replace("+","",$to);

        $url_button = '?q=' . $user_event->event->lat . ',' . $user_event->event->long;

        // $sender_id = $setting->sender_id;

        $response = SendWeddingDataV2ArTemplate($to,$template_name,$language,$user_event->accept_count,$image_url,$phone_numer_id,$token);

        // if($event->country_code == 'kw') {

        //   $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name.'&param_1='.$user_event->users_count.'&image='.$image_url;
        //   $response = SendNewTemplateCodeV1($url);

        // } else {

        //   $response = SendWeddingDataV2ArTemplate($to,$template_name,$language,$user_event->users_count,$image_url,$phone_numer_id,$token);

        // }


      	//dd($response);

        if ($response != null && $response->getStatusCode() == 200) {
            $message = WattsChatModel::create([
                'phone'        => $to,
                'name'         => "Admin",
                'message'      => $template_name,
                'is_sent_by_me'=> true,
                'message_id'   => 0,
                'from'         => "Admin",
                "template_name" => $template_name,
                "event_user_id" => $user_event->id,
                "event_id" => $event->id,
                "phone_numer_id" => $phone_numer_id,
            ]);
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


  	public function accept_user_event(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'users_count'   => ['required', "numeric"],
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }   
        
        $user_event = Model::withTrashed()->findOrFail($id);
        $user_event->update([
            'accept_count' => $request->users_count + $user_event->accept_count,
        ]);
        $event = Events::find($user_event->event_id);


        $event_action = EventUserActions::
        where("event_id", $user_event->event_id)
        ->where("event_user_id", $id)
        ->where('action', 'accept_event')
        ->first(); 
        if($event_action){
            $event_action->users_count += $request->users_count;
            $event_action->save();
        }
        else{
            EventUserActions::create([
                'event_id' => $user_event->event_id,
                'event_user_id' => $user_event->id,
                'mobile' => $user_event->mobile,
                'action' => 'accept_event',
                'users_count' => $request->users_count,
                'msg' => null
            ]);
        } 
      	Notifications::create([
          'add_by'         => 'admin',
          'user_id'        => 1,
          'send_to_type'   => 'user',
          'send_to_id'     => $user_event?->event?->user_id,
          'en_title'       => 'accept event : ' . $user_event?->event?->title,
          'ar_title'       => 'قبول الدعوه  : ' . $user_event?->event?->title,
          'en_description' => 'user : ' . $user_event->name . ' accept event : ' . $user_event?->event?->title,
          'ar_description' => 'المستخدم : ' . $user_event->name . ' قبل الدعوه  : ' . $user_event?->event?->title,
          'type'           => 'event',
          'item_id'        => $user_event?->event?->id,
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
            'is_sent' => "yes",
            'is_delivered' => "yes",
            'qr_sent' => "yes",
            'is_read' => "yes",
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
        Notifications::create([
            'add_by'         => 'admin',
            'user_id'        => 1,
            'send_to_type'   => 'user',
            'send_to_id'     => $user_event?->event?->user_id,
            'en_title'       => 'refuse event : ' . $user_event?->event?->title,
            'ar_title'       => 'رفض الدعوه  : ' . $user_event?->event?->title,
            'en_description' => 'user : ' . $user_event->name . ' refuse event : ' . $user_event?->event?->title,
            'ar_description' => 'المستخدم : ' . $user_event->name . ' رفض الدعوه  : ' . $user_event?->event?->title,
            'type'           => 'event',
            'item_id'        => $user_event?->event?->id,
            'user_event_id'  => $user_event != null ? $user_event->id : 0,
            'status'         => 'refuse_event',
        ]);

        Qr_Code::where('event_user_id', $user_event->id)->delete();
        $user_event->update([ 'scan' => null , 'scan_at' => null, 'is_refused' => 'yes','is_accepted' => 'no' ,'status' => 'not-attend'  ]);

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
        ->where('accept_count', ">", 0) 
        ->when($request->search, function ($q) use ($request) {
            $search = $request->search;
            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
            });
        })
        ->paginate(15) 
        ->withQueryString() 
        ->through(function($item) { 
            return [
                "id" => $item->id,
                "users_count" => $item->users_count, 
                'event_id' => $item->event_id,
                'uu_id' => $item->uu_id,
                'message_id' => $item->message_id,
                'name' => $item->name,
                'mobile' => $item->mobile,
                'status' => $item->status,
                'scan' => $item->scan,
                'scan_at' => $item->scan_at,
                'get_location' => $item->get_location,
                'is_sent' => $item->is_sent,
                'is_delivered' => $item->is_delivered,
                'qr_sent' => $item->qr_sent,
                'is_accepted' => $item->is_accepted,
                'is_refused' => $item->is_refused,
                'log' => $item->log,
                'sent_from' => $item->sent_from,
                'is_read' => $item->is_read,
                'error_title' => $item->error_title,
                'error' => $item->error,
                'confirmed_at' => $item->confirmed_at,
                'is_open' => $item->is_open,
                'is_new_sent' => $item->is_new_sent,
                'scan_count' => $item->scan_count,
                'is_send_congratulation' => $item->is_send_congratulation,
                'code' => $item->code,
                "send_time" => $item->send_time,
                "phone_number" => $item->phone_number,
                "accept_time" => $item->accept_time,
                "accept_count" => $item->accept_count,
                "remember" => $item->remember,
            ];
        });

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
        if($request->search){
            $data = EventUsers::where('event_id', $Item->id)
            ->where("send_type", "link")
            ->where('qr_sent','yes') 
            ->where("accept_count", ">", 0)
            ->with("event:id,title")
            ->where('mobile', 'like', "%{$request->search}%")
            ->paginate(15);
        }
        else{
            $data = EventUsers::where('event_id', $Item->id)
            ->where("send_type", "link")
            ->where('qr_sent','yes') 
            ->where("accept_count", ">", 0)
            ->with("event:id,title")
            ->paginate(15);
        }

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
        if($request->search){
            $data = EventUsers::
            where('event_id', $Item->id)
            ->where("accept_count", 0) 
            ->where('status', "!=", 'not-attend')
            ->where(function($query) { 
                $query->where('is_new_sent', "!=", 0)
                ->orWhere('status', "!=", 'hold')
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
        }
        else{
            $data = EventUsers::
            where('event_id', $Item->id)
            ->where('status', "!=", 'not-attend')
            ->where("accept_count", 0) 
            ->where(function($query) { 
                $query->where('is_new_sent', "!=", 0)
                ->orWhere('status', "!=", 'hold')
                ->orWhereNotNull('is_sent'); 
            })
            ->paginate(15);
        }

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
        //->whereIn('status', ['sent'])
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

        $data = EventUsers::where('event_id', $Item->id)
            ->where('status', 'attend')
            ->whereNull('scan')
            ->whereNull('is_refused')
            /* ده الجزء السحري: بنقارن مجموع الـ users_count بالـ scan_count مباشرة في الداتابيز */
            // ->whereRaw('(SELECT COALESCE(SUM(users_count), 0) FROM event_users_actions WHERE event_users_actions.event_user_id = event_users.id) > scan_count')
            // ->when($request->search, function ($q) use ($request) {
            //     $search = $request->search;
            //     $q->where(function ($sub) use ($search) {
            //         $sub->where('name', 'like', "%$search%")
            //             ->orWhere('mobile', 'like', "%$search%");
            //     });
            // })
            ->paginate(15)
            ->withQueryString()
            ->through(function($item) {
                // الحسابات دي هتفضل زي ما هي عشان تتبعت في الـ JSON
                // $user_count = $item->event_action ? $item->event_action->sum("users_count") : 0;
                $final_count = $item->users_count - $item->scan_count;

                return [
                    "id" => $item->id,
                    "users_count" => $final_count, 
                    'event_id' => $item->event_id,
                    'uu_id' => $item->uu_id,
                    'message_id' => $item->message_id,
                    'name' => $item->name,
                    'mobile' => $item->mobile,
                    'status' => $item->status,
                    'scan' => $item->scan,
                    'scan_at' => $item->scan_at,
                    'get_location' => $item->get_location,
                    'is_sent' => $item->is_sent,
                    'is_delivered' => $item->is_delivered,
                    'qr_sent' => $item->qr_sent,
                    'is_accepted' => $item->is_accepted,
                    'is_refused' => $item->is_refused,
                    'log' => $item->log,
                    'sent_from' => $item->sent_from,
                    'is_read' => $item->is_read,
                    'error_title' => $item->error_title,
                    'error' => $item->error,
                    'confirmed_at' => $item->confirmed_at,
                    'is_open' => $item->is_open,
                    'is_new_sent' => $item->is_new_sent,
                    'scan_count' => $item->scan_count,
                    'accept_count' => $item->accept_count,
                    'is_send_congratulation' => $item->is_send_congratulation,
                    'code' => $item->code,
                    "send_time" => $item->send_time,
                    "accept_time" => $item->accept_time,
                    "remember" => $item->remember,
                ];
            });

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
        $data = EventUsers::
        where('event_id',$Item->id)
        ->where('qr_sent','yes')
        ->where("accept_count", ">", 0)
        ->with("enter")
        //->where('qr_sent', 'yes')
        ->when($request->search, function ($q) use ($request) {
            $search = $request->search;
            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
            });
        })
        // ->where(function($query) {
        //     $query->whereRaw('(SELECT SUM(users_count) FROM event_users_actions WHERE event_users_actions.event_user_id = event_users.id) > 0');
        // })
        ->paginate(15)
        ->withQueryString() 
        ->through(function($item) { 
            return [
                "id" => $item->id,
                "users_count" => $item->accept_count, 
                'event_id' => $item->event_id,
                'uu_id' => $item->uu_id,
                'message_id' => $item->message_id,
                'name' => $item->name,
                'mobile' => $item->mobile,
                'status' => $item->status,
                'scan' => $item->scan,
                'scan_at' => $item->scan_at,
                'get_location' => $item->get_location,
                'accept_count' => $item->accept_count,
                'is_sent' => $item->is_sent,
                'is_delivered' => $item->is_delivered,
                'qr_sent' => $item->qr_sent,
                'is_accepted' => $item->is_accepted,
                'is_refused' => $item->is_refused,
                'log' => $item->log,
                'sent_from' => $item->sent_from,
                'is_read' => $item->is_read,
                'error_title' => $item->error_title,
                'error' => $item->error,
                'confirmed_at' => $item->confirmed_at,
                'is_open' => $item->is_open,
                'is_new_sent' => $item->is_new_sent,
                'scan_count' => $item->scan_count,
                'is_send_congratulation' => $item->is_send_congratulation,
                'code' => $item->code,
                "send_time" => $item->send_time,
                "accept_time" => $item->accept_time,
                "user_enrance" => $item->enter
                ->map(function($element){
                    return [
                        "count" => $element->count,
                        "date" => $element->created_at->format("Y-m-d"),
                        "time" => $element->created_at->format("h:i A"),
                    ];
                }),
                "remember" => $item->remember,
            ];
        });

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
        $messages = CongratulationMessages::
        with("reply")
        ->where("event_id", $id)
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
 
        $messages = EventMessages::
        where("event_id", $id)
        ->when($request->search, function ($q) use ($request) {
            $search = $request->search;
            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
            });
        })
        ->with("reply")
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
        EnterUserEvent::create([
            "event_user_id" => $id,
            "count" => $request->users_count
        ]);


        //$Item->update(['scan' => 'yes','scan_at' => $now]);


        return response()->json([
            'success' => 'تم عمل QR Scan  بنجاح', 
        ]);
  	}

    public function send_event_location(Request $request, $id) {

       $validator = Validator::make($request->all(), [
            'phone_setting_id' => 'required|exists:new_settings,id',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        } 
        $user_event = Model::withTrashed()->findOrFail($id);

        $event = $user_event->event;

        $mobile = ltrim($user_event->mobile,"+");

        $setting = Setting::first();

        $token          = get_whats_setting($event)['token'];
        $sender_id      = $this->get_phone_id($request->phone_setting_id);
        $phone_numer_id = $this->get_phone_id($request->phone_setting_id);

        if($user_event->send_type == "meta"){ 
            $phone = $mobile;
            // $template_name = 'wedding_data_v7_ar';
            $template_name = 'wedding_data_v15';
            $param_1 = $user_event->name;
            $language = 'ar';
            $url_button = '?q=' . $event->lat . ',' . $event->long;

            // $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$phone.'&template='.$template_name.'&url_button='.$url_button;

            // $response = SendNewTemplateCodeV1($url);
            
            $response = SendWeddingDataV15Template($mobile,$template_name,$language,$url_button,$phone_numer_id,$token);       

            if ((is_array($response) && $response[0] == "success") || ($response && $response->getStatusCode() == 200)) { 
                $message = WattsChatModel::create([
                    'phone'        => $mobile,
                    'name'         => "Admin",
                    'message'      => $template_name,
                    'is_sent_by_me'=> true,
                    'message_id'   => 0,
                    'from'         => "Admin",
                    "template_name" => $template_name,
                    "event_user_id" => $user_event->id,
                    "event_id" => $event->id,
                    "phone_numer_id" => $phone_numer_id,
                ]);
            }
        }
        else{
            $ultramsg_token="7ye6ifujyug0u46g"; // Ultramsg.com token
            $instance_id="instance109805"; // Ultramsg.com instance id
            $client = new \UltraMsg\WhatsAppApi($ultramsg_token,$instance_id);  
  
            // $api=$client->sendChatMessage($to,$body);
            $api2 = $client->sendLocationMessage($mobile,$event->address,$event->lat,$event->long,$priority=0,$referenceId="SDK");
            $response = ["success"];
        }

        if ((is_array($response) && $response[0] == "success") || ($response && $response->getStatusCode() == 200)) { // 200 OK

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
              'send_to_id'     => $user_event?->event?->user_id,
              'en_title'       => 'new congratulation msg to event : ' . $user_event?->event?->title,
              'ar_title'       => 'تهنئه جديده للدعوه   : ' . $user_event?->event?->title,
              'en_description' => 'user : ' . $user_event->name . ' send congratulation message : ' . $request->msg1,
              'ar_description' => 'المستخدم : ' . $user_event->name . '  أرسل التهنئة  : ' . $request->msg1,
              'type'           => 'event-msg',
              'item_id'        => $user_event?->event?->id,
              'user_event_id'  => $user_event != null ? $user_event->id : 0,
              'status'         => 'new_msg',
            ]);
        }

        return response()->json([
            'success' => 'تم ارسال الرساله بنجاح', 
        ]);
    }


    public function send_congratulations(Request $request, $id) {

       $validator = Validator::make($request->all(), [
            'phone_setting_id' => 'required|exists:new_settings,id',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
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
          $sender_id      = $this->get_phone_id($request->phone_setting_id);
          $phone_numer_id = $this->get_phone_id($request->phone_setting_id);

          $whatsapp = '201008478014';

          // $response = SendTemplateV5($to,$template_name,$language,$whatsapp,$phone_numer_id,$token);

          $sender_id = $setting->sender_id;

          $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name;

          $response = SendNewTemplateCodeV1($url);

          if (! ($response != null && $response->getStatusCode() == 200)) {

            $message = WattsChatModel::create([
                'phone'        => $to,
                'name'         => "Admin",
                'message'      => $template_name,
                'is_sent_by_me'=> true,
                'message_id'   => 0,
                'from'         => "Admin",
                "template_name" => $template_name,
                "event_user_id" => $user_event->id,
                "event_id" => $event->id,
                "phone_numer_id" => $phone_numer_id,
            ]);
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
            'phone_setting_id' => 'required|exists:new_settings,id',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }  
        $setting = Setting::first(); 

        $event_id = $request->event_id;

        $event = Events::withTrashed()
        ->where('id', $event_id)->firstOrFail();

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
                                $sender_id      = $this->get_phone_id($request->phone_setting_id);
                                $phone_numer_id = $this->get_phone_id($request->phone_setting_id);

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
                                    $message = WattsChatModel::create([
                                        'phone'        => $to,
                                        'name'         => "Admin",
                                        'message'      => $template_name,
                                        'is_sent_by_me'=> true,
                                        'message_id'   => 0,
                                        'from'         => "Admin",
                                        "template_name" => $template_name,
                                        "event_user_id" => $user_event->id,
                                        "event_id" => $event->id,
                                        "phone_numer_id" => $phone_numer_id,
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
        $user_event->accept_count = 0;
        $user_event->is_refused = 'yes';
        $user_event->save();


       	EventMessages::create([
            'event_id' => $user_event != null ? $user_event->event_id : 0,
            'event_user_id' => $user_event != null ? $user_event->id : 0,
            'name' => $user_event != null ? $user_event->name : '',
            'mobile' => $user_event->mobile,
            'message' => $request->msg2
        ]);
        EventUserActions::
        where('event_user_id', $event_user_id)
        ->where('action','accept_event')
        ->delete(); 

      	if($user_event != null && $user_event->event) {
      		Notifications::create([
              'add_by'         => 'admin',
              'user_id'        => 1,
              'send_to_type'   => 'user',
              'send_to_id'     => $user_event?->event?->user_id,
              'en_title'       => 'new apology msg to event : ' . $user_event?->event?->title,
              'ar_title'       => 'اعتذار جديد للدعوه   : ' . $user_event?->event?->title,
              'en_description' => 'user : ' . $user_event->name . ' send apology message : ' . $request->msg2,
              'ar_description' => 'المستخدم : ' . $user_event->name . '  أرسل الأعتذار  : ' . $request->msg2,
              'type'           => 'event-msg',
              'item_id'        => $user_event?->event?->id,
              'user_event_id'  => $user_event != null ? $user_event->id : 0,
              'status'         => 'new_msg',
            ]);
        }


        return response()->json([
            'success' => 'تم ارسال الرساله بنجاح', 
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
                $font_path = public_path('font/DroidArabicKufiRegular.ttf');
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

        } else {

            $bg           = 'qr-image-v9.jpg';
            $link         = asset('scan-qr/' . $uu_id);
            $qr_code_path = 'qr_code/' . $image_name;

            QrCode::format('png')
            ->size(450)
            ->color($color[0], $color[1], $color[2])
            ->backgroundColor(255, 255, 255) // تم التعديل هنا للون الأبيض ليطابق خلفية الكارت
            ->generate($link, $qr_code_path);
            
            // يمكنك الاستغناء عن دالة الشفافية اليدوية إذا لم تعد بحاجة لها
            // make_qr_transparent(public_path($qr_code_path)); 
            
            Image::make($bg)->insert($qr_code_path, 'left', 320, 0)->widen(450)->save($qr_code_path, 100);

            $destination = public_path($qr_code_path);
            $new_img     = Image::make($destination);

            if ($user_event->accept_count > 1) {
                $new_img->text($user_event->accept_count, 115, 412, function ($font) {
                    $font->file(public_path('font/OpenSans-Italic.ttf'));
                    $font->size(25);
                    $font->color('#000');
                });
            }

            $new_img->save($destination);
            return $destination;
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

    public function scan_data(Request $request){ 
       $validator = Validator::make($request->all(), [
            'qr_id' => 'required|exists:qr_code,uu_id',
        ]); 
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        } 
        // qr_id
        $qr_code = Qr_Code::
        where("uu_id",$request->qr_id)
        ->firstOrFail();
        $Item = EventUsers::where('id', $qr_code->event_user_id)
        ->with("event")->first(); 
 
        return response()->json([
            "id" => $Item?->id,
            "event_id" => $Item?->event_id,
            "user_name" => $Item?->name,
            "user_mobile" => $Item?->mobile, 
            "event_name" => $Item?->event?->title,
            "scan_count" => $Item?->scan_count,
            "users_count" => $Item?->users_count,
            "available" => $Item?->users_count - $Item?->scan_count,
            "status" => $Item?->is_refused == 'yes' ? "refused" : ($Item?->accept_count < 1 ? "not_confirmed" : "accepted"),
        ]);
    }

    public function scan_qr(Request $request){
       $validator = Validator::make($request->all(), [
            'qr_id' => 'required|exists:qr_code,uu_id',
            "users_count" => 'required|numeric',
        ]); 
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }  
        // qr_id
        
        $qr_code = Qr_Code::
        where("uu_id",$request->qr_id)
        ->first();
        $Item = EventUsers::
        where('id', $qr_code->event_user_id)
        ->with(['event' => function($query) {
            $query->withoutGlobalScopes(); 
        }])
        ->first();
        $user_data = User::
        where("id", $Item->user_id)
        ->first();
        if(!$user_data){
            $user_data = User::
            where("id", $Item?->event?->user_id)
            ->first();
        }  
        $available = $user_data->custom_invetaion - $user_data->send_custom_invetaion;
        if($request->users_count >= $available){
            return response()->json([
                "errors" => "لا تمتلك كل هذا العدد من الدعوات تم ارسال البعض و ليس الكل"
            ], 400);
        }
        if(!$Item || $Item?->users_count < $Item?->scan_count + $request->users_count || $Item?->is_refused == 'yes' || $Item?->accept_count < 1) {
            return response()->json([
                'errors' => 'عفوا هذا QR غير متاح', 
            ],400);

        }
        $user_data->send_custom_invetaion += $request->users_count;
        $user_data->save();
        EnterUserEvent::create([
            "event_user_id" => $Item->id,
            "count" => $request->users_count
        ]);
         

      	$now = Carbon::now(); 

        $Item->update(['scan' => 'yes',
        'scan_at' => $now,
        'scan_count' => $request->users_count + $Item->scan_count]);
        EnterUserEvent::create([
            "event_user_id" => $Item->id,
            "count" => $request->users_count
        ]);

 
        return response()->json([
            'success' => 'تم عمل QR Scan  بنجاح', 
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

    public function faild_users(Request $request, $id){ 
        $Item = Events::findOrFail($id);
        $data = EventUsers::
        where('event_id',$id)
        ->where('status','failed') 
        ->when($request->search, function ($q) use ($request) {
            $search = $request->search;
            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
            });
        }) 
        ->paginate(15)
        ->withQueryString() 
        ->through(function($item) { 
            return [
                "id" => $item->id,
                "users_count" => $item->users_count, 
                'event_id' => $item->event_id,
                'uu_id' => $item->uu_id,
                'message_id' => $item->message_id,
                'name' => $item->name,
                'mobile' => $item->mobile,
                'status' => $item->status,
                'scan' => $item->scan,
                'scan_at' => $item->scan_at,
                'get_location' => $item->get_location,
                'accept_count' => $item->accept_count,
                'is_sent' => $item->is_sent,
                'is_delivered' => $item->is_delivered,
                'qr_sent' => $item->qr_sent,
                'is_accepted' => $item->is_accepted,
                'is_refused' => $item->is_refused,
                'log' => $item->log,
                'sent_from' => $item->sent_from,
                'is_read' => $item->is_read,
                'error_title' => $item->error_title,
                'error' => $item->error,
                'confirmed_at' => $item->confirmed_at,
                'is_open' => $item->is_open,
                'is_new_sent' => $item->is_new_sent,
                'scan_count' => $item->scan_count,
                'is_send_congratulation' => $item->is_send_congratulation,
                'code' => $item->code,
                "send_time" => $item->send_time,
                "accept_time" => $item->accept_time,
                "remember" => $item->remember,
            ];
        });

        $title = 'كل الدعوات (Faild Send)';


 
        return response()->json([
            'Item' => $Item, 
            'data' => $data, 
            'title' => $title,  
        ]); 
    }

    public function is_remember(Request $request, $id){ 
        $Item = Events::findOrFail($id);
        $data = EventUsers::
        where('event_id',$id)
        ->where('remember',1) 
        ->when($request->search, function ($q) use ($request) {
            $search = $request->search;
            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
            });
        }) 
        ->paginate(15)
        ->withQueryString() 
        ->through(function($item) { 
            return [
                "id" => $item->id,
                "users_count" => $item->users_count, 
                'event_id' => $item->event_id,
                'uu_id' => $item->uu_id,
                'message_id' => $item->message_id,
                'name' => $item->name,
                'mobile' => $item->mobile,
                'status' => $item->status,
                'scan' => $item->scan,
                'scan_at' => $item->scan_at,
                'get_location' => $item->get_location,
                'accept_count' => $item->accept_count,
                'is_sent' => $item->is_sent,
                'is_delivered' => $item->is_delivered,
                'qr_sent' => $item->qr_sent,
                'is_accepted' => $item->is_accepted,
                'is_refused' => $item->is_refused,
                'log' => $item->log,
                'sent_from' => $item->sent_from,
                'is_read' => $item->is_read,
                'error_title' => $item->error_title,
                'error' => $item->error,
                'confirmed_at' => $item->confirmed_at,
                'is_open' => $item->is_open,
                'is_new_sent' => $item->is_new_sent,
                'scan_count' => $item->scan_count,
                'is_send_congratulation' => $item->is_send_congratulation,
                'code' => $item->code,
                "send_time" => $item->send_time,
                "accept_time" => $item->accept_time,
                "remember" => $item->remember,
            ];
        });

        $title = 'كل الدعوات (Faild Send)';


 
        return response()->json([
            'Item' => $Item, 
            'data' => $data, 
            'title' => $title,  
        ]); 
    }

    public function excel_is_remember(Request $request, $id){ 
        $Item = Events::findOrFail($id);
        $data = EventUsers::
        where('event_id',$id)
        ->where('remember',1) 
        ->when($request->search, function ($q) use ($request) {
            $search = $request->search;
            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
            });
        }) 
        ->get() 
        ->map(function($item) { 
            return [
                "id" => $item->id,
                "users_count" => $item->users_count, 
                'event_id' => $item->event_id,
                'uu_id' => $item->uu_id,
                'message_id' => $item->message_id,
                'name' => $item->name,
                'mobile' => $item->mobile,
                'status' => $item->status,
                'scan' => $item->scan,
                'scan_at' => $item->scan_at,
                'get_location' => $item->get_location,
                'accept_count' => $item->accept_count,
                'is_sent' => $item->is_sent,
                'is_delivered' => $item->is_delivered,
                'qr_sent' => $item->qr_sent,
                'is_accepted' => $item->is_accepted,
                'is_refused' => $item->is_refused,
                'log' => $item->log,
                'sent_from' => $item->sent_from,
                'is_read' => $item->is_read,
                'error_title' => $item->error_title,
                'error' => $item->error,
                'confirmed_at' => $item->confirmed_at,
                'is_open' => $item->is_open,
                'is_new_sent' => $item->is_new_sent,
                'scan_count' => $item->scan_count,
                'is_send_congratulation' => $item->is_send_congratulation,
                'code' => $item->code,
                "send_time" => $item->send_time,
                "accept_time" => $item->accept_time,
                "remember" => $item->remember,
            ];
        });

        $title = 'كل الدعوات (Faild Send)';


 
        return response()->json([
            'Item' => $Item, 
            'data' => $data, 
            'title' => $title,  
        ]); 
    }

    public function send_invite_utility_msg(Request $request){
       $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
            'users' => 'required|array',
            'users.*.id' => 'required|exists:event_users,id',
            'users.*.count' => 'required|numeric',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $users_ids = array_column($request->users, "id");
        $event = Events::
        findOrFail($request->event_id);
        $users = Model::
        whereIn("id", $users_ids)
        ->get();
        // الاسم ,عدد الدعوات
        try{
            $msg = [];
            foreach ($users as $key => $user) {

                $user_name   = $user->name;
                $event_title   = $event->title;
                $event_day   = Carbon::parse($event->date)->locale('ar')->translatedFormat('l') . ' الموافق ' . $event->date;
                $event_address   = $event->address;
                $event_time   = $event->time != null ? $event->time .' مساءً ' : '07:00 مساءً';
                $users_count = $user->users_count;
                $setting = Setting::first();

                $param1 = $user_name . " " .
                $event_title . " " .
                "و ذلك بمشيئة الله تعالى يوم " . $event_day .  
                "مكان الحفل " . $event_address . " " .
                "الاستقبال⏱️ " . $event_time . " " .
                "خدمة عملاء معزوم " . " " .
                "966599272904 - 96597378181";
                $param2 = $user->users_count; 

                $qr_record = Qr_Code::where('event_user_id', $user->id)->latest()->first();
                if ($qr_record) {
                    $uu_id      = $qr_record->uu_id;
                    $image_name = $qr_record->qr;
                } else {
                    $uu_id      = $this->unique_uu_id();
                    $image_name = $uu_id . '-test-qr.png';
                    Qr_Code::create([
                        'event_user_id' => $user->id,
                        'event_id'      => $user->event_id,
                        'qr'            => $image_name,
                        'uu_id'         => $uu_id,
                        'counter'       => 0,
                    ]);
                }
                $user->update([
                    'status' => 'hold',
                    'users_count' => $request->users[$key]['count'],
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
                
                $mobile = $user->mobile; 
                $to = $mobile;
                $to = str_replace("+","",$to);
                if($event->country_code == "sa"){
                    $template_name = 'wedding__masj';
                }
                else{ 
                    $template_name = 'wedding__failed';
                }
                $image_url = $event->file;
                $header_type = 'image'; 
                $language = 'ar'; 

                

                $token          = get_whats_v2_setting($event)['token']; 
                $phone_numer_id = get_whats_v2_setting($event)['sender_id'];


                $response = SendWeddingUtilityV1ArTemplate($to,$template_name,$language,$param1,$param2,$image_url,$phone_numer_id,$token, $header_type);
                if ($response != null && $response->getStatusCode() == 200) {
                    $user->update([
                        'balance' => $user->balance - $users_count
                    ]);

                    // $body = $response->getBody();
                    // $data = json_decode($body, true);

                    $response_data = $response->getBody()->getContents();
                    $data = json_decode($response_data, true);

                    $message = WattsChatModel::create([
                        'phone'        => $to,
                        'name'         => "Admin",
                        'message'      => $template_name,
                        'is_sent_by_me'=> true,
                        'message_id'   => 0,
                        'from'         => "Admin",
                        "template_name" => $template_name,
                        "event_user_id" => $user->id,
                        "event_id" => $event->id,
                        "phone_numer_id" => $phone_numer_id,
                    ]);
                    //dd($data);
                    // dd(11,$response_data,json_decode($response_data,true));

                    if(array_key_exists('messages', $data) && count($data['messages']) >= 0 && array_key_exists('id', $data['messages'][0])) {
                        $message_id = $data['messages'][0]['id'];
                    } else {
                        $message_id = 0;
                    }

                    $user->update([
                        'is_sent' => 'yes',
                        'sent_from' => 'dashboard',
                        'status' => 'sent',
                        'message_id' => $message_id,
                        "send_type" => "meta"
                    ]);

                } else {
                    $user->update([
                        'status' => 'failed',
                    ]);
                    $msg[] = $user;
                }
            }
            if(count($msg) > 0){

                return response()->json([
                    'errors'=> 'عدد الدعوات الفاشلة ' . count($msg),
                    'user' => $msg
                ]);
            }
            return response()->json([
                'success'=> 'تم الأرسال بنجاح',
            ]);
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            // لو فيسبوك رجع خطأ (زي 400)، الكود مش هيقفل 500، بل هيدخل هنا ويطبع تفاصيل الخطأ
            $errorResponse = $e->getResponse();
            $errorBody = json_decode($errorResponse->getBody()->getContents(), true);
            
            dd([
                'error_from_facebook' => $errorBody,
                'status_code' => $errorResponse->getStatusCode()
            ]);
        }
    }
  

    public function sendMessageFashalTemplate (Request $request)
    {
        // 1. التحقق من البيانات القادمة من الموقع
        $validator = Validator::make($request->all(), [
            'user_event_id'   => 'required|array',
            'user_event_id.*' => 'required|exists:event_users,id',
            'phone_setting_id' => 'required|exists:new_settings,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        } 

        $messageText = "... الرجـاء إرسال كلمة ( *معزوم* ) لإستقبال الدعوة الخاصة بكم من قبل الشركة .";

        $event_users = EventUsers::whereIn("id", $request->user_event_id)->get();
        $settings = Setting::first();
        $access_token = $settings?->access_token;
        $phone_numer_id = $this->get_phone_id($request->phone_setting_id);
        $language = 'ar';
        $template_name = "message__fashal";
        $from = $this->get_phone_number($request->phone_setting_id);

        // مصفوفة لتجميع الرسائل التي تم حفظها بنجاح لتجنب خطأ الـ Undefined variable
        $savedMessages = [];

        foreach ($event_users as $item) {
            $customerPhone = $item->mobile;
            
            // 2. إرسال الرسالة إلى Meta WhatsApp API
            $response = Http::withToken($access_token)
                ->post('https://graph.facebook.com/v19.0/' . $phone_numer_id . '/messages', [
                    'messaging_product' => 'whatsapp',
                    'recipient_type'    => 'individual',
                    'to'                => $customerPhone,
                    'type'              => 'template',
                    'template'          => [
                        'name'     => $template_name,
                        'language' => [
                            'code' => $language
                        ],
                        'components' => []
                    ],
                ]);

            // 3. التعامل مع الرد من Meta
            if ($response->successful()) {
                $messageId = $response->json()['messages'][0]['id'] ?? 'sent_' . uniqid();

                // حفظ الرسالة في قاعدة البيانات
                $message = WattsChatModel::create([
                    'phone'         => $customerPhone,
                    'name'          => 'Admin', 
                    'message'       => $messageText,
                    'is_sent_by_me' => true,    
                    'message_id'    => $messageId,
                    "from"          => $from,
                    "event_user_id" => $item->id,
                    "event_id"      => $item->event_id,
                ]);

                // إطلاق الـ Event للـ Real-time
                WattsChatEvent::dispatch($message);

                // إضافة الرسالة للمصفوفة
                $savedMessages[] = $message;
            }
        }

        // إرجاع رد يضمن عدم حدوث خطأ حتى لو كانت المصفوفة فارغة
        return response()->json([
            'status' => 'success', 
            'count'  => count($savedMessages), 
        ], 200);
    }

    public function sendWedingMsg(Request $request)
    {
        // 1. التحقق من البيانات القادمة من الموقع
        $validator = Validator::make($request->all(), [
            'user_event_id'   => 'required|array',
            'user_event_id.*' => 'required|exists:event_users,id',
            'phone_setting_id' => 'required|exists:new_settings,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        } 

        $messageText = "... الرجـاء إرسال كلمة ( *معزوم* ) لإستقبال الدعوة الخاصة بكم من قبل الشركة .";

        $event_users = EventUsers::
        whereIn("id", $request->user_event_id)
        ->with("event")
        ->get();
        $settings = Setting::first();
        $access_token = $settings?->access_token;
        $phone_numer_id = $this->get_phone_id($request->phone_setting_id);
        $language = 'ar';
        $template_name = "wedding__masj_1";
        $from = $this->get_phone_number($request->phone_setting_id);
        $header_type = "image";
        // مصفوفة لتجميع الرسائل التي تم حفظها بنجاح لتجنب خطأ الـ Undefined variable
        $savedMessages = [];
        $event = $event_users[0]?->event;
        $param_2 = $event->title;
        $image_url = $event->file;
        foreach ($event_users as $item) {
            $customerPhone = $item->mobile;
            $param_1 = $item->name;
            // 2. إرسال الرسالة إلى Meta WhatsApp API
            $response = Http::withToken($access_token)
                ->post('https://graph.facebook.com/v19.0/' . $phone_numer_id . '/messages', [
                    'messaging_product' => 'whatsapp',
                    'recipient_type'    => 'individual',
                    'to'                => $customerPhone,
                    'type'              => 'template',
                    'template'          => [
                        'name'     => $template_name,
                        'language' => [
                            'code' => $language
                        ], 
                    'components' => [
                        [
                            'type' => 'header',
                            'parameters' => [
                                [
                                    'type' => $header_type,
                                    $header_type => [
                                        'link' => $image_url,
                                    ],
                                ]
                            ],
                        ],
                        [
                            'type' => 'body',
                            'parameters' => [
                                [
                                    'type' => 'text',
                                    'text' => $param_1
                                ],
                                [
                                    'type' => 'text',
                                    'text' => $param_2
                                ],
                            ],
                        ],
                        [
                            'type' => 'button',
                            'sub_type' => 'quick_reply',
                            'index' => '0',
                            'parameters' => [
                                [
                                    'type' => 'PAYLOAD',
                                    'payload' => 'attend'
                                ]
                            ],
                        ],
                        [
                            'type' => 'button',
                            'sub_type' => 'quick_reply',
                            'index' => '1',
                            'parameters' => [
                                [
                                    'type' => 'payload',
                                    'payload' => 'not-attend'
                                ]
                            ],
                        ],
                        [
                            'type' => 'button',
                            'sub_type' => 'quick_reply',
                            'index' => '2',
                            'parameters' => [
                                [
                                    'type' => 'payload',
                                    'payload' => 'event_details'
                                ]
                            ],
                        ]
                    ],
                ]
            ]);

            // 3. التعامل مع الرد من Meta
            if ($response->successful()) {
                $messageId = $response->json()['messages'][0]['id'] ?? 'sent_' . uniqid();

                // حفظ الرسالة في قاعدة البيانات
                $message = WattsChatModel::create([
                    'phone'         => $customerPhone,
                    'name'          => 'Admin', 
                    'message'       => "wedding__masj_1",
                    'is_sent_by_me' => true,    
                    'message_id'    => $messageId,
                    "from"          => $from,
                    "event_user_id" => $item->id,
                    "event_id"      => $item->event_id,
                ]);

                // إطلاق الـ Event للـ Real-time
                WattsChatEvent::dispatch($message);

                // إضافة الرسالة للمصفوفة
                $savedMessages[] = $message;
            }
        }

        // إرجاع رد يضمن عدم حدوث خطأ حتى لو كانت المصفوفة فارغة
        return response()->json([
            'status' => 'success', 
            'count'  => count($savedMessages), 
        ], 200);
    }

    public function sendEventDetailsMsg(Request $request)
    {
        // 1. التحقق من البيانات القادمة من الموقع
        $validator = Validator::make($request->all(), [
            'user_event_id'   => 'required|array',
            'user_event_id.*' => 'required|exists:event_users,id',
            'phone_setting_id' => 'required|exists:new_settings,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        } 

        $messageText = "... الرجـاء إرسال كلمة ( *معزوم* ) لإستقبال الدعوة الخاصة بكم من قبل الشركة .";

        $event_users = EventUsers::
        whereIn("id", $request->user_event_id)
        ->with("event")
        ->get();
        $settings = Setting::first();
        $access_token = $settings?->access_token;
        $phone_numer_id = $this->get_phone_id($request->phone_setting_id);
        $language = 'ar';
        $template_name = "wedding___details";
        $from = $this->get_phone_number($request->phone_setting_id);
        $header_type = "image";
        // مصفوفة لتجميع الرسائل التي تم حفظها بنجاح لتجنب خطأ الـ Undefined variable
        $savedMessages = [];
        $event = $event_users[0]?->event;
        $param_1 = $event->address;
        $date = Carbon::parse($event->date)->locale('ar');
        $param_2 = $event->date;
        $param_3 = $date->translatedFormat('l');
        
        $param_4 = Carbon::createFromFormat('H:i', $event->time)->format('g:i A');

        // تحويل AM/PM إلى (صباحاً / مساءً)
        $param_4 = str_replace(['AM', 'PM'], ['صباحاً', 'مساءً'], $param_4);
        $image_url = $event->file;
        $mapUrl = "https://www.google.com/maps?q={$event->lat},{$event->long}";
        foreach ($event_users as $item) {
            $customerPhone = $item->mobile;
            
            // ملاحظة: تأكد من ترتيب المتغيرات الخاصة بالـ Body بشكل صحيح
            // $param_1 هنا يمثل اسم المدعو
            $guestName = $item->name; 

            $response = SendEventDetailsArTemplate($template_name,$language,$param_1,$param_2,$param_3,$param_4, $mapUrl, $phone_numer_id, $access_token, $customerPhone);
            // 3. التعامل مع الرد والتنقيح (Debugging)
            if ($response->successful()) {
                $messageId = $response->json()['messages'][0]['id'] ?? 'sent_' . uniqid();

                $message = WattsChatModel::create([
                    'phone'         => $customerPhone,
                    'name'          => 'Admin', 
                    'message'       => "wedding__masj_1",
                    'is_sent_by_me' => true,    
                    'message_id'    => $messageId,
                    "from"          => $from,
                    "event_user_id" => $item->id,
                    "event_id"      => $item->event_id,
                ]);

                WattsChatEvent::dispatch($message);
                $savedMessages[] = $message;
            } else {
                // 🔴 ارجاع تفاصيل الخطأ القادم من ميتا مباشرة لمعرفة السبب
                return response()->json([
                    'status' => 'error_from_meta',
                    'meta_response' => $response->json(),
                    'http_code' => $response->status()
                ], 400);
            }
        }
        return response()->json([
            'success' => 'You send message success',
        ]);
    }

    private function get_phone_id($id){
        $data = NewSetting::
        where("id", $id)
        ->first();
        if(empty($data)){
            return Setting::first()?->phone_numer_id ?? null;
        }
        return $data->phone_numer_id;
    }

    private function get_phone_number($id){
        $data = NewSetting::
        where("id", $id)
        ->first();
        
        return $data->phone_number;
    }
}


