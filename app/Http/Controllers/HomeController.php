<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use App\Models\Admin;
use App\Models\Setting;
use App\Models\Logs;
use App\Models\EventUsers;
use App\Models\Qr_Code;
use App\Models\EventUserLogs;
use App\Models\Events;
use App\Models\EventMessages;
use App\Models\Parking;
use App\Models\CongratulationMessages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $setting = Setting::first();

        $setting->update([
         'en_description' => 'good boy'
        ]);

        DB::table('setting')->update([ 'en_description' => 'good boy v5']);

        $data = $request->hub_challenge;
        return $data;
    }



    public function webhook_post(Request $request)
    {

      	info('post');
        info($request->all());

        /*
        Admin::create([
            'name' => 'siko 4',
            'email' => 'siko'.rand().'@test.com',
            'mobile' => '01008479614',
            'status' => 0,
            'password' => bcrypt(123456)
        ]);
        */

      	/*
      	$log = Logs::create([
          'log' => json_encode(getallheaders()),
          'type' => 1111
        ]);
        */

        /*
        $message_id = $data['entry'][0]['changes'][0]['value']['statuses'][0]['id'];
        $status =     $data['entry'][0]['changes'][0]['value']['statuses'][0]['status'];

        Logs::create([
            'message_id' => $message_id,
            'type' => $status
        ]);
        */

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


            /*
            EventUsers::where('message_id', $message_id)->update([
               'status' => $status
            ]);
			*/

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

              	/*
              	$error_title = null;
                $error_details = null;

                if($status == 'failed') {

                    if(array_key_exists("errors", $data['entry'][0]['changes'][0]['value']['statuses'][0]) && array_key_exists("title", $data['entry'][0]['changes'][0]['value']['statuses'][0]['errors'][0]) && array_key_exists("error_data", $data['entry'][0]['changes'][0]['value']['statuses'][0]['errors'][0]) ) {

                        $error_title = $data['entry'][0]['changes'][0]['value']['statuses'][0]['errors'][0]['title'];
                        $error_details = $data['entry'][0]['changes'][0]['value']['statuses'][0]['errors'][0]['error_data']['details'];

                    }
                }
                */

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
                    $bg = 'qr-image-v7.jpg';

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

                    $phone_numer_id = $setting->phone_numer_id;
                    $token = $setting->access_token;

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
                    $phone_numer_id = $setting->phone_numer_id;
                    $token = $setting->access_token;

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


                    $phone_numer_id = $setting->phone_numer_id;
                    $token = $setting->access_token;

                    $response = SendTemplateV4($to, $template_name, $language, $phone_numer_id, $token);

                }

                if($status == 'location' && $event != null) {

                    //$code = $user_event->mobile_code->code;
                    //$mobile = substr($user_event->mobile, 1);
                    $mobile = $user_event->mobile;

                    //$to = $code.$mobile;
                    //$to = $user_event->mobile;
                    $to = $mobile;

                    $template_name = 'wedding_data_v7_ar';
                    $language = 'ar';
                    $user_name = $user_event->name;
                    $location = '?q=' . $event->lat . ',' . $event->long;
                    $phone_numer_id = $setting->phone_numer_id;
                    $token = $setting->access_token;

                    $response = SendTemplateV3($to, $template_name, $language, $user_name, $location, $phone_numer_id, $token);

                }


                if($status == 'date' && $event != null) {

                    $mobile = $user_event->mobile;
                    $to = $mobile;

                    $template_name = 'wedding_data_v9_ar';
                    $language = 'ar';

                    $date = $event->date;
                    $phone_numer_id = $setting->phone_numer_id;
                    $token = $setting->access_token;

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
                    $phone_numer_id = $setting->phone_numer_id;
                    $token = $setting->access_token;

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


                $phone_numer_id = $setting->phone_numer_id;
                $token = $setting->access_token;

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


                $phone_numer_id = $setting->phone_numer_id;
                $token = $setting->access_token;

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


                $phone_numer_id = $setting->phone_numer_id;
                $token = $setting->access_token;

                $whatsapp = '201008478014';

                $response = SendTemplateV5($to, $template_name, $language, $whatsapp, $phone_numer_id, $token);
            }

          	/* ****************************************************************************************** */

            if(array_key_exists("wa_id", $data['entry'][0]['changes'][0]['value']['contacts'][0]) && $status == 'yes-congrato') {

                $mobile = $data['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'];

                $to = $mobile;

                $template_name = 'wedding_data_v4_ar';
                $language = 'ar';


                $phone_numer_id = $setting->phone_numer_id;
                $token = $setting->access_token;

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


                $phone_numer_id = $setting->phone_numer_id;
                $token = $setting->access_token;

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


        $setting = Setting::first();

        //$data = json_encode($request->all());

        $setting->update([
           'en_description' => 'ok2'
        ]);

        DB::table('setting')->update([ 'en_description' => 'ok2']);



    }

    public function webhook_v3(Request $request)
    {


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








}
