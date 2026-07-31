<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\APiResource\CongratulationMessagesResource;
use App\Http\Resources\APiResource\EventMessagesResource;
use App\Http\Resources\APiResource\UserEvents_Data;
use App\Http\Resources\APiResource\UserEventsData_V2;
use App\Models\CongratulationMessages;
use App\Models\EnterUserEvent;
use App\Models\EventFamily;
use App\Models\EventMessages;
use App\Models\Events;
use App\Models\EventUsers as Model;
use App\Models\EventUsers;
use App\Models\NewSetting;
use App\Models\Notifications;
use App\Models\Qr_Code;
use App\Models\Setting;
use App\Models\User;
use App\Traits\GeneralTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManagerStatic as Image;
use PDF;
use Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class ApiEventUersController extends Controller
{
    use GeneralTrait;

    public $token;
    public $lang;



    public function __construct()
    {
        if (getallheaders() != null && ! empty(getallheaders())) {

            if (array_key_exists('language', getallheaders())) {
                $this->lang = getallheaders()['language'];
            } elseif (array_key_exists('Language', getallheaders())) {
                $this->lang = getallheaders()['Language'];
            } else {
                $this->lang = 'ar';
            }

            if (array_key_exists('token', getallheaders())) {
                $this->token = getallheaders()['token'];
            } elseif (array_key_exists('Token', getallheaders())) {
                $this->token = getallheaders()['Token'];
            } else {
                $this->token = null;
            }

        } else {
            $this->lang = null;
            $this->token = null;
        }
    }



  	public function delete_event_messages($id,$type)
    {

      	if($type == 'event_message') {
             $Item = EventMessages::findOrFail($id);
        } else {
             $Item = CongratulationMessages::findOrFail($id);
        }

        $Item->delete();

        return $this->returnSuccessMessage(trans('home.delete_msg'));

    }



  	public function login_user_using_qr($id) {


      	if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }

        $lang = $this->lang;

        $user = null;

        if ($this->token != null) {
            $user = User::where('token', $this->token)->first();
        }

        if ($user == null) {
            if ($lang == 'en') {
                return $this->returnError('E100', 'user is required');
            } else {
                return $this->returnError('E100', 'المستخدم مطلوب');
            }
        }

         $user_event = Model::where('id', $id)->first();
        
        if ($user_event != null) {

            $now = Carbon::now();

            $user_event->update(['scan' => 'yes','scan_at' => $now]);
            EnterUserEvent::create([
                "event_user_id" => $id,
                "count" => $user_event->users_count
            ]);

            return $this->returnSuccessMessage('تم عمل QR Scan  بنجاح');

        } else {
            if ($lang == 'en') {
                return $this->returnError('404', 'sorry this event is not found');
            } else {
                return $this->returnError('404', 'عفوا هذا الحدث غير موجود مسبقا');
            }
        }


    }



    public function send_qr($id)
    {

      	if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }

        $lang = $this->lang;

        $user = null;

        if ($this->token != null) {
            $user = User::where('token', $this->token)->first();
        }

        if ($user == null) {
            if ($lang == 'en') {
                return $this->returnError('E100', 'user is required');
            } else {
                return $this->returnError('E100', 'المستخدم مطلوب');
            }
        }

      	$setting = Setting::first();

        $user_event = Model::withTrashed()->findOrFail($id);

        $event = $user_event;

        ///////////////////////////////////////////////////////////////

      	$user_event->update([ 'is_accepted' => 'yes'  ]);

        $uu_id = $this->unique_uu_id();
        $bg = 'qr-image-v9.jpg';

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
        $sender_id      = get_whats_setting($event)['sender_id'];
        $phone_numer_id = get_whats_setting($event)['sender_id'];

        //$response = SendTemplateV2($to, $template_name, $language, $image_url, $user_name, $phone_numer_id, $token);

        $to = str_replace("+","",$to);

        $url_button = '?q=' . $user_event?->event?->lat . ',' . $user_event?->event?->long;

        $sender_id = $setting->sender_id;

        $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name.'&param_1='.$user_event->users_count.'&image='.$image_url.'&url_button='.$url_button;

        $response = SendNewTemplateCodeV1($url);

        $template_name = 'wedding_data_v1_ar';
        $title = $user_event?->event?->title;
        $address = $user_event?->event?->address;
        $time = $user_event?->event?->time;
        $date   = Carbon::parse($user_event?->event?->date)->locale('ar')->translatedFormat('l') . ' الموافق ' . $event->date;
        $users_count = $user_event->users_count;
        $response = SendWeddingDataV1ArTemplate($to,$template_name,$language,$user_name,$title,$date,$address,$time,$users_count,$image_url,$phone_numer_id,$token);
      	//dd($response);

        if ($response != null && $response->getStatusCode() == 200) {

          $user_event->update([ 'qr_sent' => 'yes'  ]);

           return $this->returnSuccessMessage('تم أرسال QR Scan  بنجاح');

        } else {
            return $this->returnError('E100', 'عفوا فشل أرسال QR Scan ');
        }

    }



    // save_event_users
    public function save_event_users(Request $request)
    {

        if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }

        $lang = $this->lang;

        $user = null;

        if ($this->token != null) {
            $user = User::where('token', $this->token)->first();
        }

        if ($user == null) {
            if ($lang == 'en') {
                return $this->returnError('E100', 'user is required');
            } else {
                return $this->returnError('E100', 'المستخدم مطلوب');
            }
        }


        /////////////////////////////////////
        $validated_arr = [
            'event_id' => 'required|exists:events,id'
        ];

        $custom_messages = [
            'event_id.required' => 'رقم الحدث مطلوب',
            'event_id.exists' => 'عفوا هذا الحدث غير موجود مسبقا',
        ];

        $i = 0;

        if ($request->event_users != null && ! empty($request->event_users)) {

            $i = 1;

            foreach ($request->event_users as $j => $value) {
                //////
                $validated_arr['event_users.'.$j.'.name'] = 'required';
                $validated_arr['event_users.'.$j.'.mobile'] = 'required';
                $validated_arr['event_users.'.$j.'.users_count'] = 'required|numeric|min:1';

                $custom_messages['event_users.'.$j.'.name.required'] = 'الأسم رقم '.$i.' مطلوب';

                $custom_messages['event_users.'.$j.'.mobile.required'] = 'الموبيل رقم '.$i.' مطلوب';
                $custom_messages['event_users.'.$j.'.mobile.numeric'] = 'الموبيل رقم '.$i.' لابد ان يحتوي علي أرقام';

                $custom_messages['event_users.'.$j.'.users_count.required'] = 'عدد الدعوات '.$i.' مطلوب';
                $custom_messages['event_users.'.$j.'.users_count.numeric'] = 'عدد الدعوات  '.$i.' لابد ان يحتوي علي أرقام';

                $i = $i + 1;
            }
        }


        if ($lang == 'ar') {
            $validator = Validator::make($request->all(), $validated_arr, $custom_messages);
        } else {
            $validator = Validator::make($request->all(), $validated_arr);
        }

        //Send failed response if request is not valid
        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }
        //////////////////////////////////////


        $event_id = $request->event_id;

        $event = Events::where('id', $event_id)
        ->first();
        $errors = 0;


        if($event != null && ($event->user_id == $user->id || $event->user_id == $user->user_id)) {

          	$uu_id = $this->Get_UUID();

            if($request->event_users != null && ! empty($request->event_users)) {

                foreach ($request->event_users as $arr) {

                    if($arr['name'] != null && $arr['mobile'] != null && $arr['users_count'] != null) {

                      	$check_mobile = Model::where('event_id',$event_id)->where(function($q) use($arr) {

                          	$q->where('mobile',$arr['mobile'])->orWhere('mobile',ltrim($arr['mobile'],"+"));

                        })->count();

                      	if($check_mobile == 0) {

                            Model::create([
                              'event_id' => $event_id,
                              'uu_id' => $uu_id,
                              'name' => $arr['name'],
                              'mobile' => ltrim($arr['mobile'],"+"),
                              'users_count' => $arr['users_count'],
                              'status' => 'hold',2,
                              "user_id" => $user->id
                            ]);

                        } else {

                          	$check_row = Model::where('event_id',$event_id)->where(function($q) use($arr) {

                                $q->where('mobile',$arr['mobile'])->orWhere('mobile',ltrim($arr['mobile'],"+"));

                            })->first();

                          	if($check_row != null) {
                            	$check_row->update([ 'users_count' => $arr['users_count'] ]);
                            }

                        	$errors = $errors + 1;
                        }
                    }
                }

            }


          	// if($errors == 0) {

            // 	$data['event'] = $event;
            //     $data['event_users'] = Model::where('uu_id', $uu_id)->get(['id','name','mobile','users_count']);

            //     return $this->returnData('data', $data);


            // } else {
            // 	if ($lang == 'en') {
            //       return $this->returnError('404', 'sorry some mobiles it is not saved because it frequntly');
            //   } else {
            //       return $this->returnError('404', 'عفوا لم يتم حفظ بعض الارقام لانها موجوده مسبقا');
            //   }
            // }

            $data['event'] = $event;
            $data['event_users'] = Model::where('uu_id', $uu_id)->get(['id','name','mobile','users_count']);

            return $this->returnData('data', $data);



        } else {
            if ($lang == 'en') {
                return $this->returnError('404', 'sorry this event is not found');
            } else {
                return $this->returnError('404', 'عفوا هذا الحدث غير موجود مسبقا');
            }
        }
    }



  	// edit_event_user
    public function edit_event_user(Request $request,$event_user_id)
    {

        if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }

        $lang = $this->lang;

        $user = null;

        if ($this->token != null) {
            $user = User::where('token', $this->token)->first();
        }

        if ($user == null) {
            if ($lang == 'en') {
                return $this->returnError('E100', 'user is required');
            } else {
                return $this->returnError('E100', 'المستخدم مطلوب');
            }
        }

        /////////////////////////////////////
        $validated_arr = [
            'name' => 'required',
          	'users_count' => 'required|numeric|min:1',
        ];


        $validator = Validator::make($request->all(), $validated_arr);


        //Send failed response if request is not valid
        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }
        //////////////////////////////////////


        $event_user = Model::where('id',$event_user_id)->first();


        if($event_user != null) {

          	$event_user->update([ 'users_count' => $request->users_count,'name' => $request->name ]);

          	if ($lang == 'en') {
              $msg = 'updated succesfully';
              //return $this->returnSuccessMessage('updated successfully');
            } else {
              $msg = 'تم التحديث بنجاح';
              //return $this->returnSuccessMessage('تم التحديث بنجاح');
            }

          	return $this->returnData('event_user', $event_user);


        } else {
            if ($lang == 'en') {
                return $this->returnError('404', 'sorry this event user is not found');
            } else {
                return $this->returnError('404', 'عفوا هذا المستخدم غير موجود مسبقا');
            }
        }
    }



    // send_event_users
    public function send_event_users(Request $request)
    {

        $ultramsg_token="7ye6ifujyug0u46g"; // Ultramsg.com token
        $instance_id="instance109805"; // Ultramsg.com instance id
        $client = new \UltraMsg\WhatsAppApi($ultramsg_token,$instance_id);

        $priority=0;
        $referenceId="SDK";
        $nocache=true;

        if ($this->lang == null) {
            return response()->json([
                "message" => 'language is required',
            ], 400);  
        }

        $lang = $this->lang;

        $user = null;

        if ($this->token != null) {
            $user = User::where('token', $this->token)->first();
        }

        if ($user == null) {
            if ($lang == 'en') {
                return response()->json([
                    "message" => ' user is required',
                ], 400); 
            } else {
                return response()->json([
                    "message" => 'الستخدم مطلوب',
                ], 400); 
            }
        }

        $setting = Setting::first();

        /////////////////////////////////////
        $validated_arr = [
            'event_id' => 'required|exists:events,id'
        ];

        $custom_messages = [
            'event_id.required' => 'رقم الحدث مطلوب',
            'event_id.exists' => 'عفوا هذا الحدث غير موجود مسبقا',
        ];

        $i = 0;

        if ($request->event_users != null && ! empty($request->event_users)) {

            $i = 1;

            foreach ($request->event_users as $j => $value) {
                //////
                $validated_arr['event_users.'.$j.'.id'] = 'required|exists:event_users,id';
                $validated_arr['event_users.'.$j.'.qty'] = 'required|numeric|min:1';

                $custom_messages['event_users.'.$j.'.id.required'] = 'المستخدم رقم '.$i.' مطلوب';
                $custom_messages['event_users.'.$j.'.id.exists'] = 'عفوا المستخدم رقم '.$i.' غير موجود';

                $custom_messages['event_users.'.$j.'.qty.required'] = 'العدد رقم '.$i.' مطلوب';
                $custom_messages['event_users.'.$j.'.qty.numeric'] = 'العدد رقم '.$i.' لابد ان يحتوي علي أرقام';
                $custom_messages['event_users.'.$j.'.qty.min'] = 'عفوا العدد رقم '.$i.' لابد أن يحتوي علي الأقل واحد';

                $i = $i + 1;
            }
        }

        if ($lang == 'ar') {
            $validator = Validator::make($request->all(), $validated_arr, $custom_messages);
        } else {
            $validator = Validator::make($request->all(), $validated_arr);
        }

        //Send failed response if request is not valid
        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }
        //////////////////////////////////////

        $colum_qty = array_column($request->event_users, 'qty');
        $total_qty = array_sum($colum_qty);

        if($user->balance >= $total_qty) {

            $event_id = $request->event_id;

            $event = Events::where('id', $event_id)->first();

            try {

                if($event != null && ($event->user_id = $user->id || $event->user_id = $user->user_id)) {

                    if($request->event_users != null && ! empty($request->event_users)) {

                        foreach($request->event_users as $event_user_arr) {

                            $user_event = Model::find($event_user_arr['id']);

                            if($user_event != null) {

                                if(array_key_exists('qty', $event_user_arr)) {
                                    $users_count = $event_user_arr['qty'];
                                } else {
                                    $users_count = $user_event->users_count;
                                }

                                $user_event->update([
                                    'status' => 'hold',
                                    'scan' => null,
                                    'get_location' => null,
                                    'users_count' => $event_user_arr['qty']
                                ]);

                                $image_path = $event->file;

                                $to = $user_event->mobile;
                                $template_name = 'wedding_data_v1_ar';
                                $language = 'ar';
                                $image_url = $image_path;
                                $user_name = $user_event->name;


                                $phone_numer_id = $setting->phone_numer_id;
                                // $token = $setting->access_token;

                                //$response = SendTemplateV1($to, $template_name, $language, $image_url, $user_name, $event->title, $phone_numer_id, $token);
                              	// $sender_id = $setting->sender_id;
                                // $param_1   = $user_name;
                                // $param_2   = $event->title;

                                // $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name.'&param_1='.$param_1.'&param_2='.$param_2.'&image='.$image_url;
                                // $response = SendNewTemplateCodeV1($url);

                                $token          = get_whats_setting($event)['token'];
                                $sender_id      = $this->get_phone_id($event->phone_setting_id);
                                $phone_numer_id = $this->get_phone_id($event->phone_setting_id);

                                $param_1   = $user_name;
                                $param_2   = $event->title;
                                $param_3   = Carbon::parse($event->date)->locale('ar')->translatedFormat('l') . ' الموافق ' . $event->date;
                                $param_4   = $event->address;
                                $param_5   = $event->time != null ? $event->time : '07:00 مساءً';
                                $param_6   = $users_count;

                                if($event->sending_type == 'old_send') {

                                    $response = SendWeddingDataV1ArTemplate($to,$template_name,$language,$param_1,$param_2,$param_3,$param_4,$param_5,$param_6,$image_url,$phone_numer_id,$token);

                                    // if($event->country_code == 'kw') {

                                    //     //$url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name.'&param_1='.$param_1.'&param_2='.$param_2.'&image='.$image_url;
                                    //     $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name.'&param_1='.$param_1.'&param_2='.$param_2.'&param_3='.$param_3.'&param_4='.$param_4.'&param_5='.$param_5.'&image='.$image_url;

                                    //     $response = SendNewTemplateCodeV1($url);

                                    // } else {

                                    //     $response = SendWeddingDataV1ArTemplate($to,$template_name,$language,$param_1,$param_2,$param_3,$param_4,$param_5,$param_6,$image_url,$phone_numer_id,$token);
                                    // }

                                    if ($response != null && $response->getStatusCode() == 200) {

                                        $user->update([
                                            'balance' => $user->balance - $event_user_arr['qty']
                                        ]);

                                        // $body = $response->getBody();
                                        // $data = json_decode($body, true);

                                        $response_data = $response->getBody()->getContents();
                                        $data = json_decode($response_data, true);

                                        if(array_key_exists('messages', $data) && count($data['messages']) >= 0 && array_key_exists('id', $data['messages'][0])) {
                                            $message_id = $data['messages'][0]['id'];
                                        } else {
                                            $message_id = 0;
                                        }

                                        $user_event->update([
                                            'is_sent' => 'yes',
                                            'sent_from' => 'mobile',
                                            'status' => 'sent',
                                            'message_id' => $message_id
                                        ]);

                                    } else {
                                        $user_event->update([
                                            'status' => 'failed-v3',
                                        ]);
                                    }

                                } elseif($event->sending_type == 'new_send') {

                                    $row = $user_event;

                                    $day_name   = Carbon::parse($row->event->date)->locale('ar')->translatedFormat('l');
                                    $image = $row->event->file;

                                    $caption = $row->name . PHP_EOL . PHP_EOL .
                                    $row->event->title . PHP_EOL . PHP_EOL .
                                    "وذلك بمشيئة الله يوم " . $day_name ." الموافق" . PHP_EOL . PHP_EOL .
                                    $row->event->date . " 📆" . PHP_EOL . PHP_EOL .
                                    "⏱️الساعـة " . $row->event->time . " مساءًاً" . PHP_EOL . PHP_EOL .
                                    "📍مكان الحفـل " . $row->event->address . PHP_EOL . PHP_EOL .
                                    "لتأكيد الحضـور أو الاعتذار الرجاء الضغط على للينك لإظهار تفاصيل المناسبة وستلام كود الدخول الخاص بكم" . PHP_EOL . PHP_EOL .
                                    "https://mazoom.online/event/login";

                                    // $api=$client->sendChatMessage($to,$body);
                                    $api = $client->sendImageMessage($to,$image,$caption,$priority,$referenceId,$nocache);

                                    if(! empty($api) && isset($api['sent']) && $api['sent'] == 'true'  && isset($api['message']) && $api['message'] == 'ok') {

                                        // dd('ok');
                                        $row->update(['is_new_sent' => 1]);

                                        $user->update([
                                            'balance' => $user->balance - $user_event->users_count
                                        ]);

                                    } else {
                                        // dd('not ok',$api);
                                        $row->update(['is_new_sent' => 0]);
                                    }

                                }

                            }

                        }

                        if ($lang == 'en') {
                            return response()->json([
                                "message" => 'invitations sent successfully',
                            ]);
                        } else {
                            return response()->json([
                                "message" => 'تم أرسال الدعوات بنجاح',
                            ]);
                        }
                    }

                } else {
                    if ($lang == 'en') {
                        return response()->json([
                            "message" => ' sorry this event is not found',
                        ], 404);
                    } else {
                        return response()->json([
                            "message" => ' عفوا رصيدك غير كافي برجاء شحن رصيدك برصيد ',
                        ], 404);
                    }
                }

            } catch(\Exception $e) {
                dd($e->getMessage(), $e->getLine());
            }

        } else {
            if ($lang == 'en') {
                return response()->json([
                    "message" => 'sorry your balance is not enough please charge your balance with at least ' . $total_qty,
                ], 400);
            } else {
                return response()->json([
                    "message" => ' عفوا رصيدك غير كافي برجاء شحن رصيدك برصيد ' . $total_qty,
                ], 400);
            }
        }


    }



    // send_reminder_invitations
    public function send_reminder_invitations(Request $request)
    {

        if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }

        $lang = $this->lang;

        $user = null;

        if ($this->token != null) {
            $user = User::where('token', $this->token)->first();
        }

        if ($user == null) {
            if ($lang == 'en') {
                return $this->returnError('E100', 'user is required');
            } else {
                return $this->returnError('E100', 'المستخدم مطلوب');
            }
        }

        $setting = Setting::first();

        /////////////////////////////////////
        $validated_arr = [
            'event_id' => 'required|exists:events,id',
            'image' => 'required|image',
        ];

        $custom_messages = [
            'event_id.required' => 'رقم الحدث مطلوب',
            'event_id.exists' => 'عفوا هذا الحدث غير موجود مسبقا',
        ];

        // $i = 0;

        // if ($request->event_users != null && ! empty($request->event_users)) {

        //     $i = 1;

        //     foreach ($request->event_users as $j => $value) {
        //         //////
        //         $validated_arr['event_users.'.$j.'.id'] = 'required|exists:event_users,id';

        //         $custom_messages['event_users.'.$j.'.id.required'] = 'المستخدم رقم '.$i.' مطلوب';
        //         $custom_messages['event_users.'.$j.'.id.exists'] = 'عفوا المستخدم رقم '.$i.' غير موجود';

        //         $i = $i + 1;
        //     }
        // }


        if ($lang == 'ar') {
            $validator = Validator::make($request->all(), $validated_arr, $custom_messages);
        } else {
            $validator = Validator::make($request->all(), $validated_arr);
        }

        //Send failed response if request is not valid
        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }
        //////////////////////////////////////

        $event_id = $request->event_id;
        $user_events_data = Model::where('event_id', $event_id)->where('status', 'attend')->get();

        $event = Events::where('id', $event_id)->where('user_id', $user->id)->first();

        try {

            if($event != null) {

              	$event->update([ 'sent_remember' => 'yes' ]);

                if($user_events_data != null && $user_events_data->count() > 0) {

                    $image_path = $event->file;

                    $path = 'images';
                    $filename = '';

                    if($request->file('image') != null) {

                        $extension = $request->file('image')->extension();
                        $filename = uniqid() . '.' . $extension;
                        $request->file('image')->move($path, $filename);
                    }

                    $image_path = asset('images/'.$filename);

                  	$event_title = $event->title;

                    foreach($user_events_data as $user_event) {

                        $to = $user_event->mobile;
                        $template_name = 'wedding_data_v8_ar';
                        $language = 'ar';
                        $image_url = $image_path;
                        $user_name = $user_event->name;

                        $phone_numer_id = $setting->phone_numer_id;

                        // $token = $setting->access_token;
						//$response = SendTemplateV6($to,$template_name,$language,$image_url,$user_name,$event_title,$phone_numer_id,$token);

                      	// $sender_id = $setting->sender_id;
                        // $param_1   = $user_name;
                        // $param_2   = $event->title;

                        // $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name.'&param_1='.$param_1.'&param_2='.$param_2.'&image='.$image_url;
                        // $response = SendNewTemplateCodeV1($url);

                        $token          = get_whats_setting($event)['token'];
                        $sender_id      = get_whats_setting($event)['sender_id'];
                        $phone_numer_id = get_whats_setting($event)['sender_id'];

                        $param_1   = $user_name;
                        $param_2   = $event->title;
                        $param_3   = Carbon::parse($event->date)->locale('ar')->translatedFormat('l') . ' الموافق ' . $event->date;
                        $param_4   = $event->address;
                        $param_5   = $event->time != null ? $event->time : '07:00 مساءً';

                        //$url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name.'&param_1='.$param_1.'&param_2='.$param_2.'&image='.$image_url;
                        $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name.'&param_1='.$param_1.'&param_2='.$param_2.'&param_3='.$param_3.'&param_4='.$param_4.'&param_5='.$param_5.'&image='.$image_url;

                        $response = SendNewTemplateCodeV1($url);

                        $template_name = 'wedding_data_v1_ar';
                        $title = $user_event?->event?->title;
                        $address = $user_event?->event?->address;
                        $time = $user_event?->event?->time;
                        $date   = Carbon::parse($event->date)->locale('ar')->translatedFormat('l') . ' الموافق ' . $event->date;
                        $users_count = $user_event->users_count;
                        $response = SendWeddingDataV1ArTemplate($to,$template_name,$language,$user_name,$title,$date,$address,$time,$users_count,$image_url,$phone_numer_id,$token);
                        if ($response != null && $response->getStatusCode() == 200) {

                            // $user->update([
                            //     'balance' => $user->balance - 1
                            // ]);

                            $body = $response->getBody();
                            $data = json_decode($body, true);

                            if(array_key_exists('messages', $data) && count($data['messages']) >= 0 && array_key_exists('id', $data['messages'][0])) {
                                $message_id = $data['messages'][0]['id'];
                            } else {
                                $message_id = 0;
                            }

                        }

                    }

                    if ($lang == 'en') {
                        return $this->returnSuccessMessage('reminder invitations sent successfully');
                    } else {
                        return $this->returnSuccessMessage('تم أرسال تذكر الدعوات  بنجاح');
                    }
                } else {
                    if ($lang == 'en') {
                        return $this->returnError('404', 'sorry this event is not have any confirmed users');
                    } else {
                        return $this->returnError('404', 'عفوا هذا الحدث غير لا يحتوي علي اي دعوات مقبولة');
                    }
                }

            } else {
                if ($lang == 'en') {
                    return $this->returnError('404', 'sorry this event is not found');
                } else {
                    return $this->returnError('404', 'عفوا هذا الحدث غير موجود مسبقا');
                }
            }

        } catch(\Exception $e) {
            dd($e->getMessage(), $e->getLine());
        }


    }



  	// send_event_users
    public function send_custom_message(Request $request)
    {


        if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }

        $lang = $this->lang;

        $user = null;

        if ($this->token != null) {
            $user = User::where('token', $this->token)->first();
        }

        if ($user == null) {
            if ($lang == 'en') {
                return $this->returnError('E100', 'user is required');
            } else {
                return $this->returnError('E100', 'المستخدم مطلوب');
            }
        }

        $setting = Setting::first();


        $validated_arr = [
            'event_id' => 'required|exists:events,id',
          	'message_type' => 'required|in:congratulation_msg,apologize_msg',
            'message_id' => 'required',
            'message' => 'required',
        ];

      	$validator = Validator::make($request->all(), $validated_arr);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }
        //////////////////////////////////////

        $event_id = $request->event_id;

        $event = Events::where('id', $event_id)->where('user_id', $user->id)->first();

        try {

          if($event != null) {

            if($event->can_replay_messages == 'yes') {

                if($request->message_type == 'congratulation_msg') {

                    $cong_message = CongratulationMessages::where('event_id',$event->id)->where('message_id',$request->message_id)->where('type','replay')->first();

                    if($cong_message != null) {

                      if ($lang == 'en') {
                          return $this->returnError('E100', 'sorry this not allowed to send any messages');
                      } else {
                          return $this->returnError('E100', ' غير مسموح بارسال اي رسائل اخري ');
                      }
                  	}

                } else {

                    $apologize_message = EventMessages::where('event_id',$event->id)->where('message_id',$request->message_id)->where('type','replay')->first();

                    if($apologize_message != null) {

                      if ($lang == 'en') {
                          return $this->returnError('E100', 'sorry this not allowed to send any messages');
                      } else {
                          return $this->returnError('E100', ' غير مسموح بارسال اي رسائل اخري ');
                      }
                    }
                }

              	if($request->message_type == 'congratulation_msg') {

                  $itemRow = CongratulationMessages::find($request->message_id);

                } else {

                  $itemRow = EventMessages::find($request->message_id);

                }


                $mobile = $itemRow != null ? $itemRow->mobile : $user->mobile;

                //$to = $code.$mobile;
                $to = $mobile;
                $to = str_replace("+","",$to);

                $template_name = 'car_msg5';
                $language = 'ar';

                $message = $request->message;


                $token          = get_whats_setting($event)['token'];
                $sender_id      = get_whats_setting($event)['sender_id'];
                $phone_numer_id = get_whats_setting($event)['sender_id'];

                // $response = SendTemplateV10($to,$template_name,$language,$message,$phone_numer_id,$token);

                $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name.'&param_1='.$message;

                $response = SendNewTemplateCodeV1($url);

                //$response = SendTemplateV10($to,$template_name,$language,$message,$phone_numer_id,$token);

                if ($response != null && $response->getStatusCode() == 200) {

                    $body = $response->getBody();
                    $data = json_decode($body, true);


                    if($request->message_type == 'congratulation_msg') {

                        $cong_message = CongratulationMessages::where('event_id',$event->id)->where('message_id',$request->message_id)->where('type','replay')->first();

                        if($cong_message == null) {

                          CongratulationMessages::create([
                            'event_id' => $event->id,
                            'message_id' => $request->message_id,
                            'type' => 'replay',
                            'name' => $user->name,
                            'mobile' => $user->mobile,
                            'message' => $request->message
                          ]);
                      }

                    } else {

                      $apologize_message = EventMessages::where('event_id',$event->id)->where('message_id',$request->message_id)->where('type','replay')->first();

                      if($apologize_message == null) {

                          EventMessages::create([
                            'event_id' => $event->id,
                            'message_id' => $request->message_id,
                            'type' => 'replay',
                            'name' => $user->name,
                            'mobile' => $user->mobile,
                            'message' => $request->message
                          ]);

                      }
                    }


                    /*
                    if ($lang == 'en') {
                    return $this->returnSuccessMessage('message sent successfully');
                    } else {
                    return $this->returnSuccessMessage('تم ارسال الرساله بنجاح');
                    }
                    */

                    return $this->event_details($event->id);

                } else {
                    if ($lang == 'en') {
                        return $this->returnError('E100', 'sorry failed send any messages');
                    } else {
                        return $this->returnError('E100', ' عفوا فشل ارسال الرساله  ');
                    }
                }

            } else {
                if ($lang == 'en') {
                    return $this->returnError('E100', 'sorry you are not allowed to replay on this message');
                  } else {
                    return $this->returnError('E100', 'عفوا غير مسموح بالرد علي الرسائل');
                  }
            }

          } else {
             // user not found
             if ($lang == 'en') {
               return $this->returnError('E100', 'sorry this event user not found');
             } else {
               return $this->returnError('E100', 'عفوا هذا المستخدم غير موجود');
             }
          }


        } catch(\Exception $e) {
           dd($e->getMessage(), $e->getLine());
             if ($lang == 'en') {
               return $this->returnError('E100', 'some thing went wrong please try again');
             } else {
               return $this->returnError('E100', 'لقد حدث خطا ما برجاء المحاوله مره اخري');
             }
        }

        //dd('error-v2');

    }



  	public function event_details($id)
    {

        if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }

        $lang = $this->lang;

        $user = null;

        if ($this->token != null) {
            $user = User::where('token', $this->token)->first();
        }

        if ($user == null) {
            if ($lang == 'en') {
                return $this->returnError('E100', 'user is required');
            } else {
                return $this->returnError('E100', 'المستخدم مطلوب');
            }
        }

        $Item = Events::where('id', $id)->where(function ($query) use ($user) {
              $query->where('user_id', $user->id)->orWhere('assistant_id',$user->id);
        })->select([
            'id','title', 'file as image', 'lat', 'long', 'address', 'showing_qr', 'first_name' , 'last_name' , 'date' , 'have_reminder','can_replay_messages' ,'sent_remember'
        ])->first(); 
        $user_id = $user->user_id ? $user->id : null;

        if ($Item != null) {

            $EventUsers = EventUsers::
            where('event_id', $Item->id); 
            $user_id ? $EventUsers->where("user_id", $user_id): 
            $EventUsers->where(function($query) use($user_id){
                $query->whereNull("user_id")
                ->orWhere("user_id", $user_id);
            });
            $EventUsers = $EventUsers->get()
            ->map(function($item){
                return [
                    "id" => $item->id,
                    "name" => $item->name,
                    "mobile" => $item->mobile,
                    "users_count" => $item->users_count,
                    "scan_at" => $item->scan_at,
                    "confirmed_at" => $item->confirmed_at,
                    "scan_status" => $item->users_count > $item->scan_count,
                ];
            });
            $user_events = UserEvents_Data::collection($EventUsers);

            $all_invited_users = EventUsers::where('event_id',$Item->id); 
            $user_id ? $all_invited_users->where("user_id", $user_id): 
            $all_invited_users->where(function($query) use($user_id){
                $query->whereNull("user_id")
                ->orWhere("user_id", $user_id);
            });
            $all_invited_users = $all_invited_users->get()
            ->map(function($item){
                return [
                    "id" => $item->id,
                    "name" => $item->name,
                    "mobile" => $item->mobile,
                    "users_count" => $item->users_count,
                    "scan_at" => $item->scan_at,
                    "confirmed_at" => $item->confirmed_at,
                    "scan_status" => $item->users_count > $item->scan_count,
                ];
            });;
            $invitations_not_sent_users = EventUsers::
            where('event_id',$Item->id)
            ->where('status','hold'); 
            $user_id ? $invitations_not_sent_users->where("user_id", $user_id): 
            $invitations_not_sent_users->where(function($query) use($user_id){
                $query->whereNull("user_id")
                ->orWhere("user_id", $user_id);
            });
            $invitations_not_sent_users = $invitations_not_sent_users->get()
            ->map(function($item){
                return [
                    "id" => $item->id,
                    "name" => $item->name,
                    "mobile" => $item->mobile,
                    "users_count" => $item->users_count,
                    "scan_at" => $item->scan_at,
                    "confirmed_at" => $item->confirmed_at,
                    "scan_status" => $item->users_count > $item->scan_count,
                ];
            });

            //$confirmed_invitatios_users = EventUsers::where('event_id',$Item->id)->where('status','attend')->get(['id','name','mobile','users_count','scan_at','confirmed_at'])

            $confirmed_invitatios_users = EventUsers::
            where('event_id',$Item->id)
            ->where('is_accepted','yes'); 
            $user_id ? $confirmed_invitatios_users->where("user_id", $user_id): 
            $confirmed_invitatios_users->where(function($query) use($user_id){
                $query->whereNull("user_id")
                ->orWhere("user_id", $user_id);
            });
            $confirmed_invitatios_users = $confirmed_invitatios_users->get()
            ->map(function($item){
                return [
                    "id" => $item->id,
                    "name" => $item->name,
                    "mobile" => $item->mobile,
                    "users_count" => $item->users_count,
                    "scan_at" => $item->scan_at,
                    "confirmed_at" => $item->confirmed_at,
                    "scan_status" => $item->users_count > $item->scan_count,
                ];
            });

            $scaned_qr_users = EventUsers::
            where('event_id',$Item->id)
            ->where('scan','yes'); 
            $user_id ? $scaned_qr_users->where("user_id", $user_id): 
            $scaned_qr_users->where(function($query) use($user_id){
                $query->whereNull("user_id")
                ->orWhere("user_id", $user_id);
            });
            $scaned_qr_users = $scaned_qr_users->get()
            ->map(function($item){
                return [
                    "id" => $item->id,
                    "name" => $item->name,
                    "mobile" => $item->mobile,
                    "users_count" => $item->users_count,
                    "scan_at" => $item->scan_at,
                    "confirmed_at" => $item->confirmed_at,
                    "scan_status" => $item->users_count > $item->scan_count,
                ];
            });
            $apologized_invitatios_users = EventUsers::
            where('event_id',$Item->id)
            ->where('status','not-attend'); 
            $user_id ? $apologized_invitatios_users->where("user_id", $user_id): 
            $apologized_invitatios_users->where(function($query) use($user_id){
                $query->whereNull("user_id")
                ->orWhere("user_id", $user_id);
            });
            $apologized_invitatios_users = $apologized_invitatios_users->get()
            ->map(function($item){
                return [
                    "id" => $item->id,
                    "name" => $item->name,
                    "mobile" => $item->mobile,
                    "users_count" => $item->users_count,
                    "scan_at" => $item->scan_at,
                    "confirmed_at" => $item->confirmed_at,
                    "scan_status" => $item->users_count > $item->scan_count,
                ];
            });
            $failed_invitatios_users = EventUsers::
            where('event_id',$Item->id)
            ->whereIn('status',['hold','sent'])
            ->whereNull('is_accepted')
            ->whereNull('is_refused'); 
            $user_id ? $failed_invitatios_users->where("user_id", $user_id): 
            $failed_invitatios_users->where(function($query) use($user_id){
                $query->whereNull("user_id")
                ->orWhere("user_id", $user_id);
            });
            $failed_invitatios_users = $failed_invitatios_users->get()
            ->map(function($item){
                return [
                    "id" => $item->id,
                    "name" => $item->name,
                    "mobile" => $item->mobile,
                    "users_count" => $item->users_count,
                    "scan_at" => $item->scan_at,
                    "confirmed_at" => $item->confirmed_at,
                    "scan_status" => $item->users_count > $item->scan_count,
                ];
            });

            $enterd_events = EventFamily::where('event_id',$Item->id)->get(['id','name','mobile','scan_qr']);

            // $confirmed_without_attend = EventUsers::where('event_id',$Item->id)->where('is_accepted','yes')->where('scan','!=','yes')->get(['id','name','mobile','users_count','scan_at','confirmed_at']);

            $non_attendance_users   = EventUsers::
            where('event_id',$Item->id)
            ->where('status','attend')
            ->whereNull('scan')
            ->whereNull('is_refused'); 
            $user_id ? $non_attendance_users->where("user_id", $user_id): 
            $non_attendance_users->where(function($query) use($user_id){
                $query->whereNull("user_id")
                ->orWhere("user_id", $user_id);
            });
           $non_attendance_users = $non_attendance_users->get()
            ->map(function($item){
                return [
                    "id" => $item->id,
                    "name" => $item->name,
                    "mobile" => $item->mobile,
                    "users_count" => $item->users_count,
                    "scan_at" => $item->scan_at,
                    "confirmed_at" => $item->confirmed_at,
                    "scan_status" => $item->users_count > $item->scan_count,
                ];
            });


            $arr1 = [
                'title_en' => 'all_invited_users',
                'title_ar' => '',
                'count' => $all_invited_users->sum('users_count'),
                'users' => $all_invited_users
            ];

            $arr2 = [
                'title_en' => 'invitations_not_sent_users',
                'title_ar' => '',
                'count' => $invitations_not_sent_users->sum('users_count'),
                'users' => $invitations_not_sent_users
            ];

            $arr3 = [
                'title_en' => 'confirmed_invitatios_users',
                'title_ar' => '',
              	'count' => $confirmed_invitatios_users->sum('users_count'),
                'users' => $confirmed_invitatios_users
            ];

            $arr4 = [
                'title_en' => 'scaned_qr_users',
                'title_ar' => '',
              	'count' => $scaned_qr_users->sum('scan_count'),
                'users' => $scaned_qr_users
            ];

            $arr5 = [
                'title_en' => 'apologized_invitatios_users',
                'title_ar' => '',
                'count' => $apologized_invitatios_users->sum('users_count'),
                'users' => $apologized_invitatios_users
            ];

            $arr6 = [
                'title_en' => 'failed_invitatios_users',
                'title_ar' => '',
                'count' => $failed_invitatios_users->sum('users_count'),
                'users' => $failed_invitatios_users
            ];

            $arr7 = [
                'title_en' => 'enterd_events',
                'title_ar' => '',
                'count' => $enterd_events->count(),
                'users' => $enterd_events
            ];

            // $arr8 = [
            //     'title_en' => 'confirmed_without_attend',
            //     'title_ar' => '',
            //     'count' => $confirmed_without_attend->sum('users_count'),
            //     'users' => $confirmed_without_attend
            // ];

            $arr9 = [
                'title_en' => 'non_attendance_users',
                'title_ar' => '',
                'count' => $non_attendance_users->sum('users_count'),
                'users' => $non_attendance_users
            ];


            $event_details[] = $arr1;
            $event_details[] = $arr2;
            $event_details[] = $arr3;
            $event_details[] = $arr4;
            $event_details[] = $arr5;
            $event_details[] = $arr6;
            $event_details[] = $arr7;
            // $event_details[] = $arr8;
            $event_details[] = $arr9;

          	$mobiles = EventUsers::where('event_id',$Item->id)->pluck('mobile')->toArray();

          	$mobiles_arr = [];

          	foreach($mobiles as $phone) {
            	$mobiles_arr[] = ltrim($phone,"+");
            }

          	$event_messages = EventMessages::whereHas('event',function($event) { $event->where('is_open','yes'); })->whereIn('mobile',$mobiles_arr)->get(['id','name','mobile','message','created_at']);

            $event_congratulations_messages = CongratulationMessages::whereHas('event',function($event) { $event->where('is_open','yes'); })->whereIn('mobile',$mobiles_arr)->get(['id','name','mobile','message','created_at']);

            $data['event'] = $Item;
            $data['event_details'] = $event_details;
            $data['event_users'] = $user_events;
          	$data['event_messages'] = EventMessagesResource::collection($event_messages);

          	$data['event_congratulations_messages'] = CongratulationMessagesResource::collection($event_congratulations_messages);

            return $this->returnData('data', $data);

        } else {
            if ($lang == 'en') {
                return $this->returnError('404', 'sorry this event is not found');
            } else {
                return $this->returnError('404', 'عفوا هذا الحدث غير موجود مسبقا');
            }
        }
    }





    public function delete_user_event($user_event_id)
    {

        if ($this->lang == null) {
            return $this->returnError('E300', 'language is required');
        }

        $lang = $this->lang;

        $user = null;

        if ($this->token != null) {
            $user = User::where('token', $this->token)->first();
        }

        if ($user == null) {
            if ($lang == 'en') {
                return $this->returnError('E100', 'user is required');
            } else {
                return $this->returnError('E100', 'المستخدم مطلوب');
            }
        }


        $user_event = Model::where('id', $user_event_id)->first();

        if ($user_event != null) {

            $event = Events::where('id', $user_event->event_id)->where('user_id', $user->id)->first();

            if($event != null) {

                $user_event->delete();

                if ($lang == 'en') {
                    return $this->returnSuccessMessage('event user is deleted successfully');
                } else {
                    return $this->returnSuccessMessage('تم حذف مستخدم الحدث بنجاح');
                }

            } else {
                if ($lang == 'en') {
                    return $this->returnError('404', 'sorry this event is not found');
                } else {
                    return $this->returnError('404', 'عفوا هذا الحدث غير موجود مسبقا');
                }
            }

        } else {
            if ($lang == 'en') {
                return $this->returnError('404', 'sorry this event is not found');
            } else {
                return $this->returnError('404', 'عفوا هذا الحدث غير موجود مسبقا');
            }
        }
    }


    public function destroy($id)
    {
        $Item = Model::findOrFail($id);
        $Item->delete();
        return redirect()->back()->with('error', trans('home.delete_msg'));
    }



  	public function Get_UUID() {

        $uu_id = random_int(1000000, 9999999);

        while(Model::where('uu_id', $uu_id)->exists()) {

            $uu_id = random_int(1000000, 9999999);
        }

        return $uu_id;
    }



  	private function unique_uu_id()
    {
        $uu_id = random_int(10000, 99999);

        while (Qr_Code::where('uu_id', $uu_id)->exists()) {
            $uu_id = random_int(10000, 99999);
        }

        return $uu_id;
    }



    private function update_qr($event,$uu_id,$user_event,$image_name) {

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

        if($event->getRawOriginal('image') != null && file_exists(public_path('images/' . $event->getRawOriginal('image')))) {

            $image_name   = $uu_id . '-test-qr.png';
            $link         = asset('scan-qr/' . $uu_id);
            $qr_dir       = public_path('qr_code');
            $qr_code_path = $qr_dir . '/' . $image_name;

            if (!file_exists($qr_dir)) {
                mkdir($qr_dir, 0777, true);
            }

            $qr_size = ($qr_width > 0 && $qr_height > 0) ? $qr_width : 300;

            generate_qr_png($link, $qr_code_path, $qr_size, $color);

            $background = Image::make(public_path('images/' . $event->getRawOriginal('image')));

            if ($image_width > 0 && $image_height > 0) {
                $background->resize($image_width, $image_height);
            }

            $qr = Image::make($qr_code_path);

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
                    $Arabic3   = new \ArPHP\I18N\Arabic('Glyphs');
                    $name3     = $Arabic3->utf8Glyphs('رقم الكرسى ' . $user_event->suit_num);
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

            $background->save($qr_code_path, 100);

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

            if (!file_exists(public_path('qr_code'))) {
                mkdir(public_path('qr_code'), 0777, true);
            }

            // ==========================================
            // 2. إعدادات الخطوط
            // ==========================================
            $arabic_font = public_path('font/Amiri.ttf'); 
            $number_font = public_path('font/timr45w.ttf'); 
 
            // ==========================================
            // 3. إعدادات الأبعاد والإحداثيات
            // ==========================================
            $qr_size        = 450;
            $y_title        = 580; 
            $y_tickets      = 900 ; 
            $x_left_ticket  = 600; 
            $x_right_ticket = 1430; 
            $y_mobile       = 1120; 
            $y_datetime     = 1200; 
            $y_qr           = 1270; 

            // ==========================================
            // 4. إنشاء الباركود
            // ==========================================
            QrCode::format('png')
                ->size($qr_size)
                ->color($color[0], $color[1], $color[2])
                ->backgroundColor(255, 255, 255, 0)
                ->margin(1)
                ->generate($link, $qr_tmp_path);

            // ==========================================
            // 5. دمج البيانات على الصورة
            // ==========================================
            $img = Image::make($bg);
            $center_x = intval($img->width() / 2);

            // أ- إضافة عنوان المناسبة (Event Title)
            if (isset($event->name)) {
                $title_text = $event->name;
                
                // تصحيح: تطبيق التعديل العربي فقط إذا كانت اللغة عربية
                if (isset($event->language) && $event->language == 'ar') {
                    $Arabic = new \ArPHP\I18N\Arabic('Glyphs');
                    $title_text = $Arabic->utf8Glyphs($title_text);
                }

                $img->text($title_text, $center_x, $y_title, function ($font) use ($arabic_font) {
                    $font->file($arabic_font);
                    $font->size(90);
                    $font->color('#fff'); 
                    $font->align('center');
                    $font->valign('middle');
                });
            }

            // ب- إضافة رقم المقعد
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
