<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\CongratulationMessages;
use App\Models\EventMessages;
use App\Models\Events;
use App\Models\EventUserActions;
use App\Models\EventUserLogs;
use App\Models\EventUsers;
use App\Models\Logs;
use App\Models\Notifications;
use App\Models\Orders;
use App\Models\Parking;
use App\Models\Qr_Code;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManagerStatic as Image;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class HomeController extends Controller
{



    public function test()
    {
        return 'test';
    }


  	public function webhook_v1(Request $request)
    {

      	info('webhook get is working');

        $setting = Setting::first();

        $setting->update([
         'en_description' => 'good boy'
        ]);

        DB::table('setting')->update([ 'en_description' => 'good boy v5']);

        $data = $request->hub_challenge;
        return $data;
    }



  	public function new_webhook_post(Request $request)
    {
        info('WEBHOOK POST RECEIVED');
        info($request->all());

        $setting = Setting::first();

        $data = $request->all();

        $log = Logs::create([
            'log' => json_encode($data),
            'type' => gettype($data)
        ]);

        $language = 'ar';

        if($data != null && gettype($data) == 'array' && array_key_exists("entry", $data) && count($data['entry']) >= 0 &&
           array_key_exists("changes", $data['entry'][0]) && count($data['entry'][0]['changes']) >= 0 &&
           array_key_exists("value", $data['entry'][0]['changes'][0]) && array_key_exists("statuses", $data['entry'][0]['changes'][0]['value']) &&
           count($data['entry'][0]['changes'][0]['value']['statuses']) >= 0 && array_key_exists("id", $data['entry'][0]['changes'][0]['value']['statuses'][0]) &&
           array_key_exists("status", $data['entry'][0]['changes'][0]['value']['statuses'][0])) {

            $message_id = $data['entry'][0]['changes'][0]['value']['statuses'][0]['id'];
            $status =     $data['entry'][0]['changes'][0]['value']['statuses'][0]['status'];

             $check_user_event = EventUsers::where('message_id', $message_id)->first();

            $log->update([
              	'event_user_id' => $check_user_event != null ? $check_user_event->id : 0,
                'event_id' => $check_user_event != null ? $check_user_event->event_id : 0,
                'message_id' => $message_id
            ]);

          	$error_title = null;
          	$error_details = null;

          	if($status == 'failed') {

                if(array_key_exists("errors", $data['entry'][0]['changes'][0]['value']['statuses'][0]) && array_key_exists("title", $data['entry'][0]['changes'][0]['value']['statuses'][0]['errors'][0]) && array_key_exists("error_data", $data['entry'][0]['changes'][0]['value']['statuses'][0]['errors'][0]) ) {

                  	$error_title = $data['entry'][0]['changes'][0]['value']['statuses'][0]['errors'][0]['title'];
          			    $error_details = $data['entry'][0]['changes'][0]['value']['statuses'][0]['errors'][0]['error_data']['details'];

                }
            }

          	$events_users = EventUsers::where('message_id', $message_id)->get();

          	foreach($events_users as $user_event) {

            	$user_event->update([ 'status' => $status, 'log' => json_encode($data) ]);

               	EventUserLogs::create([
                	'log' => json_encode($data),
                  	'event_id' => $user_event->event_id,
                    'event_user_id' => $user_event->id,
                    'message_id' => $message_id,
                  	'status' => $status,
                  	'error_title' => $error_title,
                    'error_details' => $error_details,
                ]);

              	if($status == 'delivered') {
                	$user_event->update([ 'is_delivered' => 'yes'  ]);
                }

              	if($status == 'read') {
                	$user_event->update([ 'is_read' => 'yes'  ]);
                }
            }


            Parking::where('message_id', $message_id)->update([
               'status' => $status
            ]);

        } elseif($data != null && gettype($data) == 'array' && array_key_exists("entry", $data) && count($data['entry']) >= 0 &&
           array_key_exists("changes", $data['entry'][0]) && count($data['entry'][0]['changes']) >= 0 &&
           array_key_exists("value", $data['entry'][0]['changes'][0]) && array_key_exists("messages", $data['entry'][0]['changes'][0]['value']) &&
           count($data['entry'][0]['changes'][0]['value']['messages']) >= 0 && array_key_exists("context", $data['entry'][0]['changes'][0]['value']['messages'][0]) &&
           array_key_exists("id", $data['entry'][0]['changes'][0]['value']['messages'][0]['context']) && array_key_exists("button", $data['entry'][0]['changes'][0]['value']['messages'][0]) &&
           array_key_exists("payload", $data['entry'][0]['changes'][0]['value']['messages'][0]['button'])) {


            $message_id = $data['entry'][0]['changes'][0]['value']['messages'][0]['context']['id'];
            $status =    $data['entry'][0]['changes'][0]['value']['messages'][0]['button']['payload'];

            $log->update([ 'message_id' => $message_id ]);

            $user_event = EventUsers::where('message_id', $message_id)->first();

            if($user_event != null) {

                $user_event->update([
                	'log' => json_encode($data)
                ]);


                EventUserLogs::create([
                	'log' => json_encode($data),
                  	'event_id' => $user_event->event_id,
                    'event_user_id' => $user_event->id,
                    'message_id' => $message_id,
                    'status' => $status,
                ]);

                $event = Events::find($user_event->event_id);

                if($status == 'delivered') {
                	$user_event->update([ 'is_delivered' => 'yes'  ]);
                }

                if($status == 'attend' && $event != null && $event->showing_qr == 'yes') {

                    $user_event->update([ 'is_accepted' => 'yes'  ]);

                
                    /////////////////////////////////////////////////////////

                    $phone = $user_event->mobile;

                    Notifications::create([
                      'add_by'         => 'event_user',
                        'user_id'        => $user_event != null ? $user_event->id : 0,
                        'send_to_type'   => 'user',
                        'send_to_id'     => $user_event->event->user_id,
                        'en_title'       => $user_event->event->title,
                        'ar_title'       => $user_event->event->title,
                        'en_description' => $user_event->name,
                        'ar_description' => $user_event->name,
                        'type'           => 'accept_event',
                        'item_id'        => $user_event->event->id,
                        'user_event_id'  => $user_event != null ? $user_event->id : 0,
                        'status'         => 'accept_event',
                    ]);

                    /* ******************************************************************************************************************************************* */

                    $template_name6 = 'flow_'.$user_event->users_count;

                    $token          = get_whats_setting($event)['token'];
                    $sender_id      = get_whats_setting($event)['sender_id'];
                    $phone_numer_id = get_whats_setting($event)['sender_id'];

                    $to = $phone;
                    $language = 'ar';

                    $func = 'SendArFlowV' . $user_event->users_count . 'Template';
                    $response6 = $func($to,$template_name6,$language,$phone_numer_id,$token);

                    //info($response3);
                    //info($response3->getBody()->getContents());

                    if ($response6 && $response6->getStatusCode() == 200) { // 200 OK

                        // $response_data2 = $response2->getBody()->getContents();
                        // info($response_data2);
                        //dd($response_data,json_decode($response_data,true));
                    }

                } elseif($status == 'attend' && $event != null && $event->showing_qr != 'yes') {


                  	$user_event->update([ 'is_accepted' => 'yes'  ]);

                    $mobile = $user_event->mobile;

                    $to = $mobile;

                    $language = 'ar';
                    $user_name = $user_event->name;

                    $token          = get_whats_setting($event)['token'];
                    $sender_id      = get_whats_setting($event)['sender_id'];
                    $phone_numer_id = get_whats_setting($event)['sender_id'];

                  	$template_name2 = 'send_congratulation_ar_new';

                    // $response2 = SendTemplateV9($to,$template_name2,$language,$phone_numer_id,$token);
                    $response2 = SendCongratulationArNewTemplate($to,$template_name2,$language,$phone_numer_id,$token);

                }


                if($status == 'not-attend' && $event != null) {

                  Notifications::create([
                    'add_by'         => 'event_user',
                      'user_id'        => $user_event != null ? $user_event->id : 0,
                      'send_to_type'   => 'user',
                      'send_to_id'     => $user_event->event->user_id,
                      'en_title'       => $user_event->event->title,
                      'ar_title'       => $user_event->event->title,
                      'en_description' => $user_event->name,
                      'ar_description' => $user_event->name,
                      'type'           => 'refuse_event',
                      'item_id'        => $user_event->event->id,
                      'user_event_id'  => $user_event != null ? $user_event->id : 0,
                      'status'         => 'refuse_event',
                  ]);

                  Qr_Code::where('event_user_id', $user_event->id)->delete();

                  $user_event->update([ 'scan' => null , 'scan_at' => null, 'is_refused' => 'yes','is_accepted' => 'no' ,'status' => 'not-attend'  ]);

                  $event = Events::find($user_event->event_id);

                  $token          = get_whats_setting($event)['token'];
                  $sender_id      = get_whats_setting($event)['sender_id'];
                  $phone_numer_id = get_whats_setting($event)['sender_id'];


                  $mobile = $user_event->mobile;
                  $to = $mobile;

                  $phone = $mobile;
                  $template_name = 'wedding_data_v3_ar';

                  // $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$phone.'&template='.$template_name;
                  // $response = SendNewTemplateCodeV1($url);

                  $response = SendApologizedTemplate($to,$template_name,$language,$phone_numer_id,$token);

                  if ($response && $response->getStatusCode() == 200) { // 200 OK

                      // $response_data = $response->getBody()->getContents();

                      // info($response_data);

                      //dd($response_data,json_decode($response_data,true));
                  }

                    // Qr_Code::where('event_user_id', $user_event->id)->delete();

                    // $user_event->update([ 'scan' => null , 'scan_at' => null, 'is_refused' => 'yes','is_accepted' => 'no'  ]);

                    // $mobile = $user_event->mobile;

                    // $to = $mobile;

                    // $template_name = 'wedding_data_v3_ar';
                    // $language = 'ar';


                    // $phone_numer_id = $setting->sa_phone_numer_id;
                    // $token = $setting->sa_access_token;

                    // $response = SendTemplateV4($to, $template_name, $language, $phone_numer_id, $token);

                }

                if($status == 'location' && $event != null) {


                    $mobile = $user_event->mobile;

                    $to = $mobile;

                    $template_name = 'wedding_data_v7_ar';
                    $language = 'ar';
                    $user_name = $user_event->name;
                    $location = '?q=' . $event->lat . ',' . $event->long;

                    $token          = get_whats_setting($event)['token'];
                    $sender_id      = get_whats_setting($event)['sender_id'];
                    $phone_numer_id = get_whats_setting($event)['sender_id'];

                    // $response = SendTemplateV3($to, $template_name, $language, $user_name, $location, $phone_numer_id, $token);
                    $response = SendWeddingDataV7ATemplate($to, $template_name, $language, $user_name, $location, $phone_numer_id, $token);

                }


                if($status == 'date' && $event != null) {

                    $mobile = $user_event->mobile;
                    $to = $mobile;

                    $template_name = 'wedding_data_v9_ar';
                    $language = 'ar';

                    $date = $event->date;

                    $token          = get_whats_setting($event)['token'];
                    $sender_id      = get_whats_setting($event)['sender_id'];
                    $phone_numer_id = get_whats_setting($event)['sender_id'];

                    // $response = SendTemplateV7($to, $template_name, $language, $date, $phone_numer_id, $token);
                    $response = SendWeddingDataV9ArTemplate($to, $template_name, $language, $date, $phone_numer_id, $token);

                }

                ////////////////////////////////////////////////////////////////
                if($status != 'location') {
                    $user_event->update([
                       'status' => $status
                    ]);
                } else {
                    $user_event->update([
                       'get_location' => 'yes'
                    ]);
                }
                ////////////////////////////////////////////////////////////////

            }

            /* ****************************************************************************************** */

            if(array_key_exists("wa_id", $data['entry'][0]['changes'][0]['value']['contacts'][0]) && $status == 'no') {

                $mobile = $data['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'];

                $to = $mobile;

                $template_name = 'wedding_data_v2_ar';
                $language = 'ar';

        		$user_event = EventUsers::whereHas('event',function($event) { $event->whereIn('is_open',['yes','current']); })->where('mobile',$mobile)->orderBy('id','desc')->first();

                $event = $user_event->event;

                $token          = get_whats_setting($event)['token'];
                $sender_id      = get_whats_setting($event)['sender_id'];
                $phone_numer_id = get_whats_setting($event)['sender_id'];

                $whatsapp = '201008478014';

                //$response = SendTemplateV5($to,$template_name,$language,$whatsapp,$phone_numer_id,$token);
                // $response = SendTemplateV8($to, $template_name, $language, $phone_numer_id, $token);

				$Qr_Code = Qr_Code::where('event_user_id',$user_event->id)->first();

              	if($Qr_Code) {

                  $image_name = $Qr_Code->uu_id . '-test-qr.png';

                  $qr_code_path = 'qr_code/' . $image_name;

                  $url_image = asset($qr_code_path);

                  $response = SendWeddingDataV2ArTemplate($to,$template_name,$language,$user_event->users_count,$url_image,$phone_numer_id,$token);

                }

              }


          	/* ****************************************************************************************** */

            if(array_key_exists("wa_id", $data['entry'][0]['changes'][0]['value']['contacts'][0]) && ($status == 'no-congrato' || $status == 'no-apologize')) {

                $mobile = $data['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'];

                $to = $mobile;

                $template_name = 'wedding_data_v11_ar_';
                $language = 'ar';

                $user_event = EventUsers::whereHas('event',function($event) { $event->whereIn('is_open',['yes','current']); })->where('mobile',$mobile)->orderBy('id','desc')->first();

                $event = $user_event->event;

                $token          = get_whats_setting($event)['token'];
                $sender_id      = get_whats_setting($event)['sender_id'];
                $phone_numer_id = get_whats_setting($event)['sender_id'];

                $whatsapp = '201008478014';

                //$response = SendTemplateV5($to,$template_name,$language,$whatsapp,$phone_numer_id,$token);
                // $response = SendTemplateV8($to, $template_name, $language, $phone_numer_id, $token);

                $response = SendMessageTemplate($to, $template_name, $language, $phone_numer_id, $token);

              }


            /* ****************************************************************************************** */

            if(array_key_exists("wa_id", $data['entry'][0]['changes'][0]['value']['contacts'][0]) && $status == 'yes') {

                $mobile = $data['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'];

                $to = $mobile;

                $template_name = 'wedding_data_v11_ar_';
                $language = 'ar';

                $user_event = EventUsers::whereHas('event',function($event) { $event->whereIn('is_open',['yes','current']); })->where('mobile',$mobile)->orderBy('id','desc')->first();

                $event = $user_event->event;

                $token          = get_whats_setting($event)['token'];
                $sender_id      = get_whats_setting($event)['sender_id'];
                $phone_numer_id = get_whats_setting($event)['sender_id'];

                $whatsapp = '201008478014';

                // $response = SendTemplateV5($to, $template_name, $language, $whatsapp, $phone_numer_id, $token);
                $response = SendMessageTemplate($to, $template_name, $language, $phone_numer_id, $token);

              }

          	/* ****************************************************************************************** */

            if(array_key_exists("wa_id", $data['entry'][0]['changes'][0]['value']['contacts'][0]) && ($status == 'yes-congrato' || $status == 'yes-apologize')) {

                $mobile = $data['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'];

                $to = $mobile;

                $template_name = 'wedding_data_v12_ar';
                $language = 'ar';

                $user_event = EventUsers::whereHas('event',function($event) { $event->whereIn('is_open',['yes','current']); })->where('mobile',$mobile)->orderBy('id','desc')->first();

                $event = $user_event->event;

                $token          = get_whats_setting($event)['token'];
                $sender_id      = get_whats_setting($event)['sender_id'];
                $phone_numer_id = get_whats_setting($event)['sender_id'];

                $whatsapp = '201008478014';

                // $response = SendTemplateV5($to, $template_name, $language, $whatsapp, $phone_numer_id, $token);
                $response = SendMessageTemplate($to, $template_name, $language, $phone_numer_id, $token);

            }


            if(array_key_exists("wa_id", $data['entry'][0]['changes'][0]['value']['contacts'][0]) && (in_array($status, [1,2,3,4,5,6,7,8,9]) || in_array($status, ['1','2','3','4','5','6','7','8','9']))) {

                $mobile = $data['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'];

                $event_user = EventUsers::where(function($q) use($mobile) {

                    $q->where('mobile', $mobile)->orWhere('mobile', '+'.$mobile);

                })->orderBy('id', 'desc')->first();

                if($event_user != null) {

                    $user_event = $event_user;

                    $event = $event_user->event;

                    $phone = $mobile;
 

                    ////////////////////////////////////////////////////////////////////////

                    $template_name = 'wedding_data_v2_ar';

                    $user_event->update([ 'is_accepted' => 'yes' ,'confirmed_at' => now(),'status' => 'attend', 'accept_count' => (int)$status ]);

                    $event_action = EventUserActions::where('event_id', $user_event->event_id)
                        ->where('event_user_id', $user_event->id)
                        ->where('action', 'accept_event')
                        ->first();
                    if ($event_action) {
                        $event_action->users_count += (int)$status;
                        $event_action->save();
                    } else {
                        EventUserActions::create([
                            'event_id'      => $user_event->event_id,
                            'event_user_id' => $user_event->id,
                            'mobile'        => $user_event->mobile,
                            'action'        => 'accept_event',
                            'users_count'   => (int)$status,
                            'msg'           => null,
                        ]);
                    }

                    Notifications::create([
                        'add_by'         => 'event_user',
                        'user_id'        => $user_event->id,
                        'send_to_type'   => 'user',
                        'send_to_id'     => $user_event->event->user_id,
                        'en_title'       => $user_event->event->title,
                        'ar_title'       => $user_event->event->title,
                        'en_description' => $user_event->name,
                        'ar_description' => $user_event->name,
                        'type'           => 'accept_event',
                        'item_id'        => $user_event->event->id,
                        'user_event_id'  => $user_event->id,
                        'status'         => 'accept_event',
                    ]);

                    $url_button = '?q=' . $user_event->event->lat . ',' . $user_event->event->long;

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

                        $qr_code_path = 'qr_code/' . $image_name;

                        $url_image = asset($qr_code_path);

                        //$url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$phone.'&template='.$template_name.'&param_1='.$user_event->users_count.'&image='.$url_image.'&url_button='.$url_button;

                        // $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$phone.'&template='.$template_name.'&param_1='.$user_event->users_count.'&image='.$url_image;
                        // $response = SendNewTemplateCodeV1($url);

                        $token          = get_whats_setting($event)['token'];
                        $sender_id      = get_whats_setting($event)['sender_id'];
                        $phone_numer_id = get_whats_setting($event)['sender_id'];

                        $to = $phone;
                        $language = 'ar';

                        $response = SendWeddingDataV2ArTemplate($to,$template_name,$language,$user_event->users_count,$url_image,$phone_numer_id,$token);

                        if ($response && $response->getStatusCode() == 200) { // 200 OK

                            // $response_data = $response->getBody()->getContents();
                            // info($response_data);
                            //dd($response_data,json_decode($response_data,true));

                            $user_event->update([ 'qr_sent' => 'yes'  ]);

                            EventUserLogs::create([
                              'log' => "تم ارسال ال QR Code",
                              'event_id' => $user_event->event_id,
                              'event_user_id' => $user_event->id,
                              'message_id' => $user_event->message_id,
                              'status' => 'attend',
                              'error_title' => null,
                              'error_details' => null,
                            ]);
                        }

                    }

                    sleep(3);

                    //$template_name3 = 'mazoom_qr2';

                    // $url3 = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$phone.'&template='.$template_name3;
                    // $response3 = SendNewTemplateCodeV1($url3);

                    // $token          = get_whats_setting($event)['token'];
                    // $sender_id      = get_whats_setting($event)['sender_id'];
                    // $phone_numer_id = get_whats_setting($event)['sender_id'];

                    // $to = $phone;
                    // $language = 'ar';

                    //$response3 = SendConfirmationTemplate($to,$template_name3,$language,$phone_numer_id,$token);

                    //info($response3);
                    //info($response3->getBody()->getContents());

                    //if ($response3 && $response3->getStatusCode() == 200) { // 200 OK

                        // $response_data2 = $response2->getBody()->getContents();

                        // info($response_data2);

                        //dd($response_data,json_decode($response_data,true));
                    //}

                    sleep(4);

                    $token          = get_whats_setting($event)['token'];
                    $sender_id      = get_whats_setting($event)['sender_id'];
                    $phone_numer_id = get_whats_setting($event)['sender_id'];


                    $template_name2 = 'send_congratulation_ar_new';

                    // $url2 = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$phone.'&template='.$template_name2;
                    // $response2 = SendNewTemplateCodeV1($url2);

                    $response2 = SendCongratulationArNewTemplate($to,$template_name2,$language,$phone_numer_id,$token);

                    if ($response2 && $response2->getStatusCode() == 200) { // 200 OK

                        // $response_data2 = $response2->getBody()->getContents();
                        // info($response_data2);
                        //dd($response_data,json_decode($response_data,true));
                    }

                }

                // $to = $mobile;

                // $template_name = 'wedding_data_v12_ar';
                // $language = 'ar';


                // $phone_numer_id = $setting->sa_phone_numer_id;
                // $token = $setting->sa_access_token;

                // $whatsapp = '201008478014';

                // // $response = SendTemplateV5($to, $template_name, $language, $whatsapp, $phone_numer_id, $token);
                // $response = SendMessageTemplate($to, $template_name, $language, $phone_numer_id, $token);

            }


            /* ****************************************************************************************** */



        } elseif($data != null && gettype($data) == 'array' && array_key_exists("entry", $data) &&
           array_key_exists("changes", $data['entry'][0]) &&
           array_key_exists("value", $data['entry'][0]['changes'][0]) &&
           array_key_exists("messages", $data['entry'][0]['changes'][0]['value']) &&
           isset($data['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['type']) &&
           $data['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['type'] === 'nfm_reply') {

            // WhatsApp Flow reply — wedding_data90 template
            $msg        = $data['entry'][0]['changes'][0]['value']['messages'][0];
            $mobile     = $msg['from'];
            $context_id = $msg['context']['id'] ?? null;

            $response_json = json_decode($msg['interactive']['nfm_reply']['response_json'] ?? '{}', true);
            // القيمة بتيجي زي "0_1" — نأخذ الرقم بعد الـ underscore الأخير
            $raw_value   = $response_json['screen_0___0'] ?? null;
            $users_count = $raw_value ? (int) explode('_', $raw_value)[1] : null;

            info('FLOW REPLY', ['mobile' => $mobile, 'users_count' => $users_count, 'context_id' => $context_id]);
            
            Log::info('users_count:', $users_count);
            if ($users_count && $mobile) {

                $user_event = EventUsers::where(function($q) use ($mobile) {
                    $q->where('mobile', $mobile)->orWhere('mobile', '+' . $mobile);
                })->when($context_id, function($q) use ($context_id) {
                    $q->orWhere('message_id', $context_id);
                })->orderBy('id', 'desc')->first();

                if ($user_event) {

                    $event = $user_event->event;

                    $user_event->update([
                        'users_count'  => $users_count,
                        'accept_count' => $users_count,
                        'is_accepted'  => 'yes',
                        'confirmed_at' => now(),
                        'status'       => 'attend',
                    ]);

                    $event_action = EventUserActions::where('event_id', $user_event->event_id)
                        ->where('event_user_id', $user_event->id)
                        ->where('action', 'accept_event')
                        ->first();

                    if ($event_action) {
                        $event_action->users_count = $users_count;
                        $event_action->save();
                    } else {
                        EventUserActions::create([
                            'event_id'      => $user_event->event_id,
                            'event_user_id' => $user_event->id,
                            'mobile'        => $user_event->mobile,
                            'action'        => 'accept_event',
                            'users_count'   => $users_count,
                            'msg'           => null,
                        ]);
                    }

                    Notifications::create([
                        'add_by'         => 'event_user',
                        'user_id'        => $user_event->id,
                        'send_to_type'   => 'user',
                        'send_to_id'     => $user_event->event->user_id,
                        'en_title'       => $user_event->event->title,
                        'ar_title'       => $user_event->event->title,
                        'en_description' => $user_event->name,
                        'ar_description' => $user_event->name,
                        'type'           => 'accept_event',
                        'item_id'        => $user_event->event->id,
                        'user_event_id'  => $user_event->id,
                        'status'         => 'accept_event',
                    ]);

                    $token          = get_whats_setting($event)['token'];
                    $phone_numer_id = get_whats_setting($event)['sender_id'];
                    $to             = $mobile;
                    $language       = 'ar';

                    if ($event->showing_qr == 'yes') {

                        $uu_id      = $this->unique_uu_id();
                        $image_name = $uu_id . '-test-qr.png';

                        $qr_row = Qr_Code::where('event_user_id', $user_event->id)->first();
                        if ($qr_row) {
                            $qr_row->update(['qr' => $image_name]);
                        } else {
                            Qr_Code::create([
                                'event_user_id' => $user_event->id,
                                'event_id'      => $user_event->event_id,
                                'qr'            => $image_name,
                                'uu_id'         => $uu_id,
                                'counter'       => 0,
                            ]);
                        }

                        $this->update_qr($event, $uu_id, $user_event, $image_name);

                        $qr_code_path  = 'qr_code/' . $image_name;
                        $url_image     = asset($qr_code_path);
                        $template_name = 'wedding_data90';

                        $response = SendWeddingDataV2ArTemplate($to, $template_name, $language, $users_count, $url_image, $phone_numer_id, $token);

                        if ($response && $response->getStatusCode() == 200) {
                            $user_event->update(['qr_sent' => 'yes']);
                            EventUserLogs::create([
                                'log'           => 'تم ارسال ال QR Code',
                                'event_id'      => $user_event->event_id,
                                'event_user_id' => $user_event->id,
                                'message_id'    => $user_event->message_id,
                                'status'        => 'attend',
                                'error_title'   => null,
                                'error_details' => null,
                            ]);
                        }

                    } else {

                        $template_name = 'send_congratulation_ar_new';
                        SendCongratulationArNewTemplate($to, $template_name, $language, $phone_numer_id, $token);

                    }
                }
            }

        } elseif($data != null && gettype($data) == 'array' && array_key_exists("entry", $data) && count($data['entry']) >= 0 &&
           array_key_exists("changes", $data['entry'][0]) && count($data['entry'][0]['changes']) >= 0 &&
           array_key_exists("value", $data['entry'][0]['changes'][0]) && array_key_exists("messages", $data['entry'][0]['changes'][0]['value']) &&
           count($data['entry'][0]['changes'][0]['value']['messages']) >= 0) {


            if(array_key_exists("text", $data['entry'][0]['changes'][0]['value']['messages'][0])
             && array_key_exists("from", $data['entry'][0]['changes'][0]['value']['messages'][0])) {

                $mobile = $data['entry'][0]['changes'][0]['value']['messages'][0]['from'];
                $txt_msg = $data['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'];


                $to = $mobile;

                $template_name = 'wedding_data_v4_ar';
                $language = 'ar';

                $user_event = EventUsers::whereHas('event',function($event) { $event->whereIn('is_open',['yes','current']); })->where('mobile',$mobile)->orderBy('id','desc')->first();

                $event = $user_event->event;

                $token          = get_whats_setting($event)['token'];
                $sender_id      = get_whats_setting($event)['sender_id'];
                $phone_numer_id = get_whats_setting($event)['sender_id'];

                $whatsapp = '201008478014';

                // $response = SendTemplateV5($to, $template_name, $language, $whatsapp, $phone_numer_id, $token);
                $response = SendMessageTemplate($to, $template_name, $language, $phone_numer_id, $token);

                if ($response != null && $response->getStatusCode() == 200) {

                    $event_user = EventUsers::where(function($q) use($mobile) {

                    	$q->where('mobile', $mobile)->orWhere('mobile', '+'.$mobile);

                    })->orderBy('id', 'desc')->first();

                  	if($event_user != null && $event_user->status == 'attend') {

                      	CongratulationMessages::create([
                          'event_id' => $event_user != null ? $event_user->event_id : 0,
                          'event_user_id' => $event_user != null ? $event_user->id : 0,
                          'name' => $event_user != null ? $event_user->name : '',
                          'mobile' => $mobile,
                          'message' => $txt_msg
                        ]);

                    } else {

                      EventMessages::create([
                        'event_id' => $event_user != null ? $event_user->event_id : 0,
                        'event_user_id' => $event_user != null ? $event_user->id : 0,
                        'name' => $event_user != null ? $event_user->name : '',
                        'mobile' => $mobile,
                        'message' => $txt_msg
                      ]);

                    }

                }


            }


        }

        return response()->json(['status' => 'ok'], 200);
    }





	/*
    public function webhook_v1(Request $request)
    {

        $setting = Setting::first();

        $setting->update([
         'en_description' => 'good boy'
        ]);

        DB::table('setting')->update([ 'en_description' => 'good boy v5']);

        $data = $request->hub_challenge;
        return $data;
    }
    */


  	/*
  	public function new_webhook_v1(Request $request)
    {

        $hub1 = $request->query('hub');
        $hub2 = $request->hub_challenge;

      	$data = $hub1 != null ? $hub1 : $hub2;

        return response($data, 200);


        $verifyToken = env('WHATSAPP_VERIFY_TOKEN');


        $mode      = $hub['mode'] ?? null;
        $token     = $hub['verify_token'] ?? null;
        $challenge = $hub['challenge'] ?? null;

      	//dd($mode,$token,$challenge);

        if ($mode === 'subscribe' && $token === $verifyToken) {
            return response($challenge, 200);
        }



        return response('Forbidden', 403);
    }
	*/


  	// للـ verification من Meta
    public function new_webhook_v1(Request $request)
    {

      	info('new_webhook_v1');

        $mode = $request->input('hub_mode');
        $token = $request->input('hub_verify_token');
        $challenge = $request->input('hub_challenge');

        // ضع token خاص بك
        $verifyToken = 123456789;
        $verifyToken = env('WHATSAPP_VERIFY_TOKEN');

        if ($mode === 'subscribe' && $token === $verifyToken) {
            Log::info('Webhook verified successfully');
            return response($challenge, 200)
                ->header('Content-Type', 'text/plain');
        }

        return response('Forbidden', 403);
    }






    public function webhook_post(Request $request)
    {

      	info('post webhook_post');
        info($request->all());


        $setting = Setting::first();

        $data = $request->all();

        $log = Logs::create([
            'log' => json_encode($data),
            'type' => gettype($data)
        ]);


        if($data != null && gettype($data) == 'array' && array_key_exists("entry", $data) && count($data['entry']) >= 0 &&
           array_key_exists("changes", $data['entry'][0]) && count($data['entry'][0]['changes']) >= 0 &&
           array_key_exists("value", $data['entry'][0]['changes'][0]) && array_key_exists("statuses", $data['entry'][0]['changes'][0]['value']) &&
           count($data['entry'][0]['changes'][0]['value']['statuses']) >= 0 && array_key_exists("id", $data['entry'][0]['changes'][0]['value']['statuses'][0]) &&
           array_key_exists("status", $data['entry'][0]['changes'][0]['value']['statuses'][0])) {

            $message_id = $data['entry'][0]['changes'][0]['value']['statuses'][0]['id'];
            $status =     $data['entry'][0]['changes'][0]['value']['statuses'][0]['status'];

             $check_user_event = EventUsers::where('message_id', $message_id)->first();

            $log->update([
              	'event_user_id' => $check_user_event != null ? $check_user_event->id : 0,
                'event_id' => $check_user_event != null ? $check_user_event->event_id : 0,
                'message_id' => $message_id
            ]);

          	$error_title = null;
          	$error_details = null;

          	if($status == 'failed') {

                if(array_key_exists("errors", $data['entry'][0]['changes'][0]['value']['statuses'][0]) && array_key_exists("title", $data['entry'][0]['changes'][0]['value']['statuses'][0]['errors'][0]) && array_key_exists("error_data", $data['entry'][0]['changes'][0]['value']['statuses'][0]['errors'][0]) ) {

                  	$error_title = $data['entry'][0]['changes'][0]['value']['statuses'][0]['errors'][0]['title'];
          			$error_details = $data['entry'][0]['changes'][0]['value']['statuses'][0]['errors'][0]['error_data']['details'];

                }
            }

          	$events_users = EventUsers::where('message_id', $message_id)->get();

          	foreach($events_users as $user_event) {

            	$user_event->update([ 'status' => $status, 'log' => json_encode($data) ]);

               	EventUserLogs::create([
                	'log' => json_encode($data),
                  	'event_id' => $user_event->event_id,
                    'event_user_id' => $user_event->id,
                    'message_id' => $message_id,
                  	'status' => $status,
                  	'error_title' => $error_title,
                    'error_details' => $error_details,
                ]);

              	if($status == 'delivered') {
                	$user_event->update([ 'is_delivered' => 'yes'  ]);
                }

              	if($status == 'read') {
                	$user_event->update([ 'is_read' => 'yes'  ]);
                }
            }


            Parking::where('message_id', $message_id)->update([
               'status' => $status
            ]);

        } elseif($data != null && gettype($data) == 'array' && array_key_exists("entry", $data) && count($data['entry']) >= 0 &&
           array_key_exists("changes", $data['entry'][0]) && count($data['entry'][0]['changes']) >= 0 &&
           array_key_exists("value", $data['entry'][0]['changes'][0]) && array_key_exists("messages", $data['entry'][0]['changes'][0]['value']) &&
           count($data['entry'][0]['changes'][0]['value']['messages']) >= 0 && array_key_exists("context", $data['entry'][0]['changes'][0]['value']['messages'][0]) &&
           array_key_exists("id", $data['entry'][0]['changes'][0]['value']['messages'][0]['context']) && array_key_exists("button", $data['entry'][0]['changes'][0]['value']['messages'][0]) &&
           array_key_exists("payload", $data['entry'][0]['changes'][0]['value']['messages'][0]['button'])) {


            $message_id = $data['entry'][0]['changes'][0]['value']['messages'][0]['context']['id'];
            $status =    $data['entry'][0]['changes'][0]['value']['messages'][0]['button']['payload'];

            $log->update([ 'message_id' => $message_id ]);

            $user_event = EventUsers::where('message_id', $message_id)->first();

            if($user_event != null) {

                $user_event->update([
                	'log' => json_encode($data)
                ]);


                EventUserLogs::create([
                	'log' => json_encode($data),
                  	'event_id' => $user_event->event_id,
                    'event_user_id' => $user_event->id,
                    'message_id' => $message_id,
                    'status' => $status,
                ]);

                $event = Events::find($user_event->event_id);

                if($status == 'delivered') {
                	$user_event->update([ 'is_delivered' => 'yes'  ]);
                }

                if($status == 'attend' && $event != null && $event->showing_qr == 'yes') {

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

                    $link = asset('scan-qr/' . $uu_id);
                    $qr_code_path = 'qr_code/' . $image_name;
                    QrCode::size(900)->format('png')->generate($link, $qr_code_path);

                    Image::make($bg)->insert($qr_code_path, 'right', 60, 500)->widen(700)->save($qr_code_path, 100);

                    $destination = public_path($qr_code_path);

                    $new_img = Image::make($destination);

                    $new_img->text($user_event->users_count, 170, 645, function ($font) {
                        $font->file(public_path('font/OpenSans-Italic.ttf'));
                        $font->size(40);
                        $font->color('#fff');
                    });

                    $new_img->save($destination);

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

                    $response = SendTemplateV2($to, $template_name, $language, $image_url, $user_name, $phone_numer_id, $token);

                  	if ($response != null && $response->getStatusCode() == 200) {

                      $user_event->update([ 'qr_sent' => 'yes'  ]);

                      EventUserLogs::create([
                          'log' => "تم ارسال ال QR Code",
                          'event_id' => $user_event->event_id,
                          'event_user_id' => $user_event->id,
                          'message_id' => $message_id,
                          'status' => $status,
                          'error_title' => null,
                          'error_details' => null,
                      ]);
                    }

                  	$template_name2 = 'send_congratulation_ar_new';

                    $response2 = SendTemplateV9($to,$template_name2,$language,$phone_numer_id,$token);

                } elseif($status == 'attend' && $event != null && $event->showing_qr != 'yes') {

                  	$user_event->update([ 'is_accepted' => 'yes'  ]);

                    $mobile = $user_event->mobile;

                    $to = $mobile;

                    $language = 'ar';
                    $user_name = $user_event->name;

                    $token          = get_whats_setting($event)['token'];
                    $sender_id      = get_whats_setting($event)['sender_id'];
                    $phone_numer_id = get_whats_setting($event)['sender_id'];

                  	$template_name2 = 'send_congratulation_ar_new';

                    $response2 = SendTemplateV9($to,$template_name2,$language,$phone_numer_id,$token);

                }


                if($status == 'not-attend' && $event != null) {

                    Qr_Code::where('event_user_id', $user_event->id)->delete();

                    $user_event->update([ 'scan' => null , 'scan_at' => null, 'is_refused' => 'yes','is_accepted' => 'no'  ]);

                    $mobile = $user_event->mobile;

                    $to = $mobile;

                    $template_name = 'wedding_data_v3_ar';
                    $language = 'ar';


                    // $phone_numer_id = $setting->sa_phone_numer_id;
                    // $token = $setting->sa_access_token;

                    $token          = get_whats_setting($event)['token'];
                    $sender_id      = get_whats_setting($event)['sender_id'];
                    $phone_numer_id = get_whats_setting($event)['sender_id'];

                    $response = SendTemplateV4($to, $template_name, $language, $phone_numer_id, $token);

                }

                if($status == 'location' && $event != null) {

                    $mobile = $user_event->mobile;

                    $to = $mobile;

                    $template_name = 'wedding_data_v7_ar';
                    $language = 'ar';
                    $user_name = $user_event->name;
                    $location = '?q=' . $event->lat . ',' . $event->long;

                    $token          = get_whats_setting($event)['token'];
                    $sender_id      = get_whats_setting($event)['sender_id'];
                    $phone_numer_id = get_whats_setting($event)['sender_id'];

                    $response = SendTemplateV3($to, $template_name, $language, $user_name, $location, $phone_numer_id, $token);

                }


                if($status == 'date' && $event != null) {

                    $mobile = $user_event->mobile;
                    $to = $mobile;

                    $template_name = 'wedding_data_v9_ar';
                    $language = 'ar';

                    $date = $event->date;

                    $token          = get_whats_setting($event)['token'];
                    $sender_id      = get_whats_setting($event)['sender_id'];
                    $phone_numer_id = get_whats_setting($event)['sender_id'];

                    $response = SendTemplateV7($to, $template_name, $language, $date, $phone_numer_id, $token);

                }

                ////////////////////////////////////////////////////////////////
                if($status != 'location') {
                    $user_event->update([
                       'status' => $status
                    ]);
                } else {
                    $user_event->update([
                       'get_location' => 'yes'
                    ]);
                }
                ////////////////////////////////////////////////////////////////

            }

            /* ************************************************************ */

            $parking = Parking::where('message_id', $message_id)->first();

            if($parking != null) {

                if($status == 'send-car') {

                    $mobile = $parking->mobile;
                    $to = $mobile;

                    $template_name = 'car_msg2';
                    $language = 'ar';

                    $phone_numer_id = $setting->sa_phone_numer_id;
                    $token = $setting->sa_access_token;

                    $response = SendCarTemplateV2($to, $template_name, $language, $phone_numer_id, $token);

                    if ($response != null && $response->getStatusCode() == 200) {
                        $parking->update(['parking_status' => 'leaving']);
                    }
                }


            }

            /* ****************************************************************************************** */

            if(array_key_exists("wa_id", $data['entry'][0]['changes'][0]['value']['contacts'][0]) && $status == 'no') {

                $mobile = $data['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'];

                $to = $mobile;

                $template_name = 'wedding_danewta_v10_ar_new';
                $language = 'ar';


                // $phone_numer_id = $setting->sa_phone_numer_id;
                // $token = $setting->sa_access_token;



                $whatsapp = '201008478014';

                //$response = SendTemplateV5($to,$template_name,$language,$whatsapp,$phone_numer_id,$token);
                $response = SendTemplateV8($to, $template_name, $language, $phone_numer_id, $token);
            }


          	/* ****************************************************************************************** */

            if(array_key_exists("wa_id", $data['entry'][0]['changes'][0]['value']['contacts'][0]) && $status == 'no-congrato') {

                $mobile = $data['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'];

                $to = $mobile;

                $template_name = 'wedding_danewta_v10_ar_new';
                $language = 'ar';


                $phone_numer_id = $setting->sa_phone_numer_id;
                $token = $setting->sa_access_token;

                $whatsapp = '201008478014';

                //$response = SendTemplateV5($to,$template_name,$language,$whatsapp,$phone_numer_id,$token);
                $response = SendTemplateV8($to, $template_name, $language, $phone_numer_id, $token);
            }


            /* ****************************************************************************************** */

            if(array_key_exists("wa_id", $data['entry'][0]['changes'][0]['value']['contacts'][0]) && $status == 'yes') {

                $mobile = $data['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'];

                $to = $mobile;

                $template_name = 'wedding_data_v4_ar';
                $language = 'ar';


                // $phone_numer_id = $setting->sa_phone_numer_id;
                // $token = $setting->sa_access_token;

                $user_event = EventUsers::whereHas('event',function($event) { $event->whereIn('is_open',['yes','current']); })->where('mobile',$mobile)->orderBy('id','desc')->first();

                $event = $user_event->event;

                $token          = get_whats_setting($event)['token'];
                $sender_id      = get_whats_setting($event)['sender_id'];
                $phone_numer_id = get_whats_setting($event)['sender_id'];

                $whatsapp = '201008478014';

                $response = SendTemplateV5($to, $template_name, $language, $whatsapp, $phone_numer_id, $token);
            }

          	/* ****************************************************************************************** */

            if(array_key_exists("wa_id", $data['entry'][0]['changes'][0]['value']['contacts'][0]) && $status == 'yes-congrato') {

                $mobile = $data['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'];

                $to = $mobile;

                $template_name = 'wedding_data_v4_ar';
                $language = 'ar';


                // $phone_numer_id = $setting->sa_phone_numer_id;
                // $token = $setting->sa_access_token;

                $user_event = EventUsers::whereHas('event',function($event) { $event->whereIn('is_open',['yes','current']); })->where('mobile',$mobile)->orderBy('id','desc')->first();

                $event = $user_event->event;

                $token          = get_whats_setting($event)['token'];
                $sender_id      = get_whats_setting($event)['sender_id'];
                $phone_numer_id = get_whats_setting($event)['sender_id'];

                $whatsapp = '201008478014';

                $response = SendTemplateV5($to, $template_name, $language, $whatsapp, $phone_numer_id, $token);
            }


            /* ****************************************************************************************** */



        } elseif($data != null && gettype($data) == 'array' && array_key_exists("entry", $data) && count($data['entry']) >= 0 &&
           array_key_exists("changes", $data['entry'][0]) && count($data['entry'][0]['changes']) >= 0 &&
           array_key_exists("value", $data['entry'][0]['changes'][0]) && array_key_exists("messages", $data['entry'][0]['changes'][0]['value']) &&
           count($data['entry'][0]['changes'][0]['value']['messages']) >= 0) {


            if(array_key_exists("text", $data['entry'][0]['changes'][0]['value']['messages'][0])
             && array_key_exists("from", $data['entry'][0]['changes'][0]['value']['messages'][0])) {

                $mobile = $data['entry'][0]['changes'][0]['value']['messages'][0]['from'];
                $txt_msg = $data['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'];


                $to = $mobile;

                $template_name = 'wedding_data_v5_ar';
                $language = 'ar';


                // $phone_numer_id = $setting->sa_phone_numer_id;
                // $token = $setting->sa_access_token;

                $user_event = EventUsers::whereHas('event',function($event) { $event->whereIn('is_open',['yes','current']); })->where('mobile',$mobile)->orderBy('id','desc')->first();

                $event = $user_event->event;

                $token          = get_whats_setting($event)['token'];
                $sender_id      = get_whats_setting($event)['sender_id'];
                $phone_numer_id = get_whats_setting($event)['sender_id'];

                $whatsapp = '201008478014';

                $response = SendTemplateV5($to, $template_name, $language, $whatsapp, $phone_numer_id, $token);

                if ($response != null && $response->getStatusCode() == 200) {

                    $event_user = EventUsers::where(function($q) use($mobile) {

                    	$q->where('mobile', $mobile)->orWhere('mobile', '+'.$mobile);

                    })->orderBy('id', 'desc')->first();

                  	if($event_user != null && $event_user->status == 'attend') {

                      	CongratulationMessages::create([
                          'event_id' => $event_user != null ? $event_user->event_id : 0,
                          'event_user_id' => $event_user != null ? $event_user->id : 0,
                          'name' => $event_user != null ? $event_user->name : '',
                          'mobile' => $mobile,
                          'message' => $txt_msg
                        ]);

                    } else {

                      EventMessages::create([
                        'event_id' => $event_user != null ? $event_user->event_id : 0,
                        'event_user_id' => $event_user != null ? $event_user->id : 0,
                        'name' => $event_user != null ? $event_user->name : '',
                        'mobile' => $mobile,
                        'message' => $txt_msg
                      ]);

                    }

                }


            }


        }





    }










    public function webhook_v2(Request $request)
    {

		info('webhook_v2 is working');


        $setting = Setting::first();

        //$data = json_encode($request->all());

        $setting->update([
           'en_description' => 'ok2'
        ]);

        DB::table('setting')->update([ 'en_description' => 'ok2']);



    }

    public function webhook_v3(Request $request)
    {

		info('webhook_v3 is working');

        $setting = Setting::first();

        //$data = json_encode($request->all());

        $setting->update([
           'en_description' => 'ok3'
        ]);

        DB::table('setting')->update([ 'en_description' => 'ok3']);

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

        if ($event->getRawOriginal('file') != null && file_exists(public_path('images/' . $event->getRawOriginal('file')))) {

            $image_name  = $uu_id . '-test-qr.png';
            $link        = asset('scan-qr/' . $uu_id);
            $qr_dir      = public_path('qr_code');
            $qr_tmp_path = $qr_dir . '/tmp_' . $image_name;
            $final_path  = $qr_dir . '/' . $image_name;

            if (!file_exists($qr_dir)) {
                mkdir($qr_dir, 0777, true);
            }

            $qr_size = ($qr_width > 0 && $qr_height > 0) ? $qr_width : 300;

            generate_qr_png($link, $qr_tmp_path, $qr_size, $color);

            $background = Image::make(public_path('images/' . $event->getRawOriginal('file')));

            if ($image_width > 0 && $image_height > 0) {
                $background->resize($image_width, $image_height);
            }

            $qr = Image::make($qr_tmp_path);

            if ($qr_width > 0 && $qr_height > 0) {
                $qr->resize($qr_width, $qr_height);
            }

            // origin: bottom-right — qr_x/qr_y = pixels from bottom-right corner
            if ($qr_x > 0 || $qr_y > 0) {
                $x = $background->width()  - $qr->width()  - $qr_x;
                $y = $background->height() - $qr->height() - $qr_y;
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
                $name2     = $Arabic2->utf8Glyphs('عدد الضيوف ' . $user_event->accept_count);
            } else {
                $font_path = public_path('font/LuxuriousRoman-Regular.ttf');
                $name      = $user_event->name;
                $name2     = 'Entered Users ' . $user_event->accept_count;
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

            if ($number_qr && $user_event->accept_count > 1) {
                $background->text($name2, $center_x, $text_y, function ($font) use ($font_path, $text_color) {
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

            QrCode::size(450)->format('png')->generate($link, $qr_code_path);
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


    private function accept_data($data, $user_event) {
        try {
            if (isset($data['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['nfm_reply']['response_json'])) {
                $users_count = 0;
                $response_json = json_decode($data['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['nfm_reply']['response_json'], true);
                
                // إحنا هنا بنسحب القيمة بناءً على اسم الحقل في الـ Flow (screen_0___0)
                // لو إنت مغير الاسم في الـ Flow JSON تأكد إنه مطابق هنا
                $flow_id = $response_json['screen_0___0'] ?? '0_0'; 
                
                // بنعمل Extract للرقم من الـ ID (مثلاً لو الـ ID هو 0_3 هياخد رقم 3)
                $count_parts = explode('_', $flow_id);
                $users_count = (int) end($count_parts);
                // الآن تحديث الجزء اللي كان معمول له Comment
                $event_action = EventUserActions::where("event_id", $user_event->event_id)
                    ->where("event_user_id", $user_event->id)
                    ->where('action', 'accept_event')
                    ->first();

                if ($event_action) {
                    // تحديث السجل الموجود
                    $event_action->users_count = $users_count;
                    $event_action->save();
                } else {
                    // إنشاء سجل جديد
                    EventUserActions::create([
                        'event_id' => $user_event->event_id,
                        'event_user_id' => $user_event->id,
                        'mobile' => $user_event->mobile,
                        'action' => 'accept_event',
                        'users_count' => $users_count,
                        'msg' => null
                    ]);
                }
            }
        } catch (\Throwable $th) { 
        }
    }

}
