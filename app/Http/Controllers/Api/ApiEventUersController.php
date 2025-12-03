<?php

namespace App\Http\Controllers\Api;

use App\Models\EventFamily;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\EventUsers as Model;
use App\Models\Events;
use App\Models\EventUsers;
use App\Models\Setting;
use App\Models\User;
use App\Models\EventMessages;
use App\Models\CongratulationMessages;
use App\Http\Resources\APiResource\CongratulationMessagesResource;
use App\Http\Resources\APiResource\EventMessagesResource;
use App\Http\Resources\APiResource\UserEvents_Data;
use App\Http\Resources\APiResource\UserEventsData_V2;
use App\Traits\GeneralTrait;
use Response;
use Illuminate\Support\Facades\Validator;
use App\Models\Qr_Code;
use Carbon\Carbon;
use PDF;
use Intervention\Image\ImageManagerStatic as Image;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Notifications;


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

        $url_button = '?q=' . $user_event->event->lat . ',' . $user_event->event->long;

        $sender_id = $setting->sender_id;

        $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name.'&param_1='.$user_event->users_count.'&image='.$image_url.'&url_button='.$url_button;

        $response = SendNewTemplateCodeV1($url);

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

        $event = Events::where('id', $event_id)->where('user_id', $user->id)->first();
        $errors = 0;


        if($event != null) {

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
                              'status' => 'hold'
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

            $event = Events::where('id', $event_id)->where('user_id', $user->id)->first();

            try {

                if($event != null) {

                    if($request->event_users != null && ! empty($request->event_users)) {

                        foreach($request->event_users as $event_user_arr) {

                            $user_event = Model::find($event_user_arr['id']);

                            if($user_event != null) {

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
                                $sender_id      = get_whats_setting($event)['sender_id'];
                                $phone_numer_id = get_whats_setting($event)['sender_id'];

                                $param_1   = $user_name;
                                $param_2   = $event->title;
                                $param_3   = Carbon::parse($event->date)->locale('ar')->translatedFormat('l') . ' الموافق ' . $event->date;
                                $param_4   = $event->address;
                                $param_5   = $event->time != null ? $event->time : '07:00 مساء';

                                if($event->sending_type == 'old_send') {

                                    //$url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name.'&param_1='.$param_1.'&param_2='.$param_2.'&image='.$image_url;
                                    $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name.'&param_1='.$param_1.'&param_2='.$param_2.'&param_3='.$param_3.'&param_4='.$param_4.'&param_5='.$param_5.'&image='.$image_url;

                                    $response = SendNewTemplateCodeV1($url);

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
                                    "⏱️الساعـة " . $row->event->time . " مساءاً" . PHP_EOL . PHP_EOL .
                                    "📍مكان الحفـل " . $row->event->address . PHP_EOL . PHP_EOL .
                                    "لتأكيد الحضـور أو الاعتذار الرجاء الضغط على للينك لإظهار تفاصيل المناسبة وستلام كود الدخول الخاص بكم" . PHP_EOL . PHP_EOL .
                                    "https://mazoom-kw.com/event/login";

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
                            return $this->returnSuccessMessage('invitations sent successfully');
                        } else {

                            return $this->returnSuccessMessage('تم أرسال الدعوات بنجاح');
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

        } else {
            if ($lang == 'en') {
                return $this->returnError('E100', 'sorry your balance is not enough please charge your balance with at least ' . $total_qty);
            } else {
                return $this->returnError('E100', ' عفوا رصيدك غير كافي برجاء شحن رصيدك برصيد ' . $total_qty);
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
                        $param_5   = $event->time != null ? $event->time : '07:00 مساء';

                        //$url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name.'&param_1='.$param_1.'&param_2='.$param_2.'&image='.$image_url;
                        $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$to.'&template='.$template_name.'&param_1='.$param_1.'&param_2='.$param_2.'&param_3='.$param_3.'&param_4='.$param_4.'&param_5='.$param_5.'&image='.$image_url;

                        $response = SendNewTemplateCodeV1($url);

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

        if ($Item != null) {

            $EventUsers = EventUsers::where('event_id', $Item->id)->get(['id','name','mobile','users_count','scan_at','confirmed_at']);
            $user_events = UserEvents_Data::collection($EventUsers);

            $all_invited_users = EventUsers::where('event_id',$Item->id)->get(['id','name','mobile','users_count','scan_at','confirmed_at']);
            $invitations_not_sent_users = EventUsers::where('event_id',$Item->id)->where('status','hold')->get(['id','name','mobile','users_count','scan_at','confirmed_at']);

            //$confirmed_invitatios_users = EventUsers::where('event_id',$Item->id)->where('status','attend')->get(['id','name','mobile','users_count','scan_at','confirmed_at']);
            $confirmed_invitatios_users = EventUsers::where('event_id',$Item->id)->where('is_accepted','yes')->get(['id','name','mobile','users_count','scan_at','confirmed_at']);

            $scaned_qr_users = EventUsers::where('event_id',$Item->id)->where('scan','yes')->get(['id','name','mobile','users_count','scan_at','confirmed_at','scan_count']);
            $apologized_invitatios_users = EventUsers::where('event_id',$Item->id)->where('status','not-attend')->get(['id','name','mobile','users_count','scan_at','confirmed_at']);
            $failed_invitatios_users = EventUsers::where('event_id',$Item->id)->whereIn('status',['hold','sent'])->whereNull('is_accepted')->whereNull('is_refused')->get(['id','name','mobile','users_count','scan_at','confirmed_at']);

            $enterd_events = EventFamily::where('event_id',$Item->id)->get(['id','name','mobile','scan_qr']);

            // $confirmed_without_attend = EventUsers::where('event_id',$Item->id)->where('is_accepted','yes')->where('scan','!=','yes')->get(['id','name','mobile','users_count','scan_at','confirmed_at']);

            $non_attendance_users   = EventUsers::where('event_id',$Item->id)->where('status','attend')->whereNull('scan')->whereNull('is_refused')->get(['id','name','mobile','users_count','scan_count','scan_at','confirmed_at']);


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

        return [$r, $g, $b];
    }




}
