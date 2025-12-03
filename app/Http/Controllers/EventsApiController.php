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
use App\Models\Notifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManagerStatic as Image;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use GuzzleHttp\Client;

class EventsApiController extends Controller
{


    public function accept_event(Request $request)
    {
        info('accept event');
        info($request->all());

        $setting = Setting::first();

        $mobile = ltrim($request->phone,"+");

        $user_event = EventUsers::whereHas('event',function($event) { $event->whereIn('is_open',['yes','current']); })->where('mobile',$mobile)->orderBy('id','desc')->first();

        if($user_event && $user_event->event) {

            $event = Events::find($user_event->event_id);

            $token     = get_whats_setting($event)['token'];
            $sender_id = get_whats_setting($event)['sender_id'];


          	/*
          	$phone_numer_id = '746157308570599';
            $sender_id      = '746157308570599';
            $token          = 'EABIy7zT1dfYBO304MlaYIQZBalGto0d1oPSCKHXEosSCsaLIdxE6QgftNNSLuhG37zirzBTMpK8HprkTRtlLyQZB1evrzBItZBW8y8LgZAYQ1pd6y64GtnMmKUZCjlY0QAZBhvu0VErD7fPzO8iz0cg0OrZBC8ovZA1F5ZCLzWa85nwaL1jWP8WYaa8yI1Ffkmvsy0QHjRrU5bSMJLS8b9bt7ZA2c0Ys8WYvlTMufprZCQ5ZCiAGTqGfzO9LcVY8S9CdpuY1PZBD1phEneQZDZD';
			*/

            $phone = $mobile;

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

            // $template_name4 = 'car_msg4';

            // $url4 = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$phone.'&template='.$template_name4;

            // $response4 = SendNewTemplateCodeV1($url4);

            // if ($response4 && $response4->getStatusCode() == 200) { // 200 OK

            //     // $response_data2 = $response2->getBody()->getContents();

            //     // info($response_data2);

            //     //dd($response_data,json_decode($response_data,true));
            // }

            /* ******************************************************************************************************************************************* */

            $template_name = 'wedding_data_v2_ar';

            $user_event->update([ 'is_accepted' => 'yes' ,'confirmed_at' => now(),'status' => 'attend' ]);

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

                // $param_1 = $user_event->name;
                // $url_image = 'https://6gphones.ae/mazoom/public/logo/mazoom.png?2106217949';

                // $bg = 'qr-image-v9.jpg';

                // $link = asset('scan-qr/' . $uu_id);
                // QrCode::size(900)->format('png')->generate($link, $qr_code_path);

                // Image::make($bg)->insert($qr_code_path, 'left', 480, 0)->widen(700)->save($qr_code_path, 100);

                // $destination = public_path($qr_code_path);

                // $new_img = Image::make($destination);

                // $new_img->text($user_event->users_count, 150, 615, function ($font) {
                //     $font->file(public_path('font/OpenSans-Italic.ttf'));
                //     $font->size(40);
                //     $font->color('#eeb534');
                // });

              	// $new_img->text($user_event->mobile, 190, 680, function ($font) {
                //     $font->file(public_path('font/OpenSans-Italic.ttf'));
                //     $font->size(30);
                //     $font->color('#000');
                //     //$font->align('right'); // Adjust alignment if necessary
                // });

                // $new_img->save($destination);

                $url_image = asset($qr_code_path);

                //$url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$phone.'&template='.$template_name.'&param_1='.$user_event->users_count.'&image='.$url_image.'&url_button='.$url_button;
                $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$phone.'&template='.$template_name.'&param_1='.$user_event->users_count.'&image='.$url_image;

                $response = SendNewTemplateCodeV1($url);

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

          	sleep(5);

            /* ******************************************************************************************************************************************* */

            $template_name3 = 'mazoom_qr2';

            $url3 = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$phone.'&template='.$template_name3;


            $response3 = SendNewTemplateCodeV1($url3);

          	//info($response3);
          	//info($response3->getBody()->getContents());

            if ($response3 && $response3->getStatusCode() == 200) { // 200 OK

                // $response_data2 = $response2->getBody()->getContents();

                // info($response_data2);

                //dd($response_data,json_decode($response_data,true));
            }


            /* ******************************************************************************************************************************************* */

            sleep(4);

            $template_name2 = 'send_congratulation_ar_new';

            $url2 = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$phone.'&template='.$template_name2;

            $response2 = SendNewTemplateCodeV1($url2);

            if ($response2 && $response2->getStatusCode() == 200) { // 200 OK

                // $response_data2 = $response2->getBody()->getContents();

                // info($response_data2);

                //dd($response_data,json_decode($response_data,true));
            }


        }

    }



  	public function resend_qr_code(Request $request)
    {
        info('resend_qr_code');
        info($request->all());

        $setting = Setting::first();

        $mobile = ltrim($request->phone,"+");

        $user_event = EventUsers::whereHas('event',function($event) { $event->whereIn('is_open',['yes','current']); })->where('mobile',$mobile)->orderBy('id','desc')->first();

        if($user_event) {

            $event = Events::find($user_event->event_id);

            $token     = get_whats_setting($event)['token'];
            $sender_id = get_whats_setting($event)['sender_id'];

            $phone = $mobile;
            $template_name = 'wedding_data_v2_ar';

            $user_event->update([ 'is_accepted' => 'yes'  ]);

            $url_button = '?q=' . $user_event->event->lat . ',' . $user_event->event->long;

              if($event != null && $event->showing_qr == 'yes') {

                $param_1 = $user_event->name;

                // $url_image = 'https://6gphones.ae/mazoom/public/logo/mazoom.png?2106217949';

              	$check_Qr_Code = Qr_Code::where('event_id',$user_event->event_id)->where('event_user_id',$user_event->id)->first();

                if($check_Qr_Code) {

                  $uu_id = $check_Qr_Code->uu_id;

                  $image_name = $uu_id . '-test-qr.png';

                  $link = asset('scan-qr/' . $uu_id);
                  $qr_code_path = 'qr_code/' . $image_name;

                } else {

                   $uu_id = $this->unique_uu_id();

                    $image_name = $uu_id . '-test-qr.png';

                  	$check_Qr_Code = Qr_Code::create([
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

                    // Image::make($bg)->insert($qr_code_path,  'left', 480, 0)->widen(700)->save($qr_code_path, 100);

                    // $destination = public_path($qr_code_path);

                    // $new_img = Image::make($destination);

                    // $new_img->text($user_event->users_count, 150, 615, function ($font) {
                    //     $font->file(public_path('font/OpenSans-Italic.ttf'));
                    //     $font->size(40);
                    //     $font->color('#eeb534');
                    // });

                    // $new_img->save($destination);

                }


                $url_image = asset($qr_code_path);

                $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$phone.'&template='.$template_name.'&param_1='.$user_event->users_count.'&image='.$url_image.'&url_button='.$url_button;

                $response = SendNewTemplateCodeV1($url);

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

        }

    }



    public function refuse_event(Request $request)
    {
        // info('refuse event');
        // info($request->all());

        $setting = Setting::first();

        $mobile = ltrim($request->phone,"+");

        $user_event = EventUsers::whereHas('event',function($event) { $event->whereIn('is_open',['yes','current']); })->where('mobile',$mobile)->orderBy('id','desc')->first();

        if($user_event && $user_event->event) {

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

            $token     = get_whats_setting($event)['token'];
            $sender_id = get_whats_setting($event)['sender_id'];

            $phone = $mobile;
            $template_name = 'wedding_data_v3_ar';

            $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$phone.'&template='.$template_name;

            $response = SendNewTemplateCodeV1($url);

            if ($response && $response->getStatusCode() == 200) { // 200 OK

                // $response_data = $response->getBody()->getContents();

                // info($response_data);

                //dd($response_data,json_decode($response_data,true));
            }

        }
    }



    public function save_congratulation_msg(Request $request)
    {
        info('congratulation msg');

        info($request->all());

        $setting = Setting::first();

        $mobile = ltrim($request->phone,"+");

        $user_event = EventUsers::whereHas('event',function($event) { $event->whereIn('is_open',['yes','current']); })->where('mobile',$mobile)->orderBy('id','desc')->first();

        CongratulationMessages::create([
          'event_id' => $user_event != null ? $user_event->event_id : 0,
          'event_user_id' => $user_event != null ? $user_event->id : 0,
          'name' => $user_event != null ? $user_event->name : '',
          'mobile' => $mobile,
          'message' => $request->msg
        ]);

      	if($user_event != null && $user_event->event) {
      		Notifications::create([
              'add_by'         => 'event_user',
              'user_id'        => $user_event != null ? $user_event->id : 0,
              'send_to_type'   => 'user',
              'send_to_id'     => $user_event->event->user_id,
              'en_title'       => $user_event->event->title,
              'ar_title'       => $user_event->event->title,
              'en_description' => $request->msg,
              'ar_description' => $request->msg,
              'type'           => 'event-msg',
              'item_id'        => $user_event->event->id,
              'user_event_id'  => $user_event != null ? $user_event->id : 0,
              'status'         => 'new_msg',
            ]);
        }


    }



    public function save_apology_msg(Request $request)
    {
        info('apology msg');

        info($request->all());

        $setting = Setting::first();

        $mobile = ltrim($request->phone,"+");

        $user_event = EventUsers::whereHas('event',function($event) { $event->whereIn('is_open',['yes','current']); })->where('mobile',$mobile)->orderBy('id','desc')->first();

        EventMessages::create([
            'event_id' => $user_event != null ? $user_event->event_id : 0,
            'event_user_id' => $user_event != null ? $user_event->id : 0,
            'name' => $user_event != null ? $user_event->name : '',
            'mobile' => $mobile,
            'message' => $request->msg
        ]);

      	if($user_event != null && $user_event->event) {
      		Notifications::create([
              'add_by'         => 'event_user',
              'user_id'        => $user_event != null ? $user_event->id : 0,
              'send_to_type'   => 'user',
              'send_to_id'     => $user_event->event->user_id,
              'en_title'       => $user_event->event->title,
              'ar_title'       => $user_event->event->title,
              'en_description' => $request->msg,
              'ar_description' => $request->msg,
              'type'           => 'event-msg',
              'item_id'        => $user_event->event->id,
              'user_event_id'  => $user_event != null ? $user_event->id : 0,
              'status'         => 'new_msg',
            ]);
        }

    }



    public function location_event(Request $request)
    {
        // info('location event');
        // info($request->all());

        $setting = Setting::first();

        $mobile = ltrim($request->phone,"+");

        $user_event = EventUsers::whereHas('event',function($event) { $event->whereIn('is_open',['yes','current']); })->where('mobile',$mobile)->orderBy('id','desc')->first();

        if($user_event) {

            $event = Events::find($user_event->event_id);

            if($event) {

                $user_event->update([ 'get_location' => 'yes' ]);

                $token     = get_whats_setting($event)['token'];
                $sender_id = get_whats_setting($event)['sender_id'];

                $phone = $mobile;
                // $template_name = 'wedding_data_v7_ar';
                $template_name = 'wedding_data_v15__';
                $param_1 = $user_event->name;

                $url_button = '?q=' . $event->lat . ',' . $event->long;

                $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$phone.'&template='.$template_name.'&url_button='.$url_button;

                $response = SendNewTemplateCodeV1($url);

                if ($response && $response->getStatusCode() == 200) { // 200 OK

                    // $response_data = $response->getBody()->getContents();

                    // info($response_data);

                    //dd($response_data,json_decode($response_data,true));
                }

            }

        }
    }


    public function event_date(Request $request)
    {
        // info('event date');
        // info($request->all());

        $setting = Setting::first();

        $mobile = ltrim($request->phone,"+");

        $user_event = EventUsers::whereHas('event',function($event) { $event->whereIn('is_open',['yes','current']); })->where('mobile',$mobile)->orderBy('id','desc')->first();

        if($user_event) {

            $event = Events::find($user_event->event_id);

            if($event) {

                $token     = get_whats_setting($event)['token'];
                $sender_id = get_whats_setting($event)['sender_id'];

                $phone = $mobile;
                $template_name = 'wedding_data_v9_ar';

                $param_1 = $event->date;

                $url = 'https://api.karzoun.app/CloudApi.php?token='.$token.'&sender_id='.$sender_id.'&phone='.$phone.'&template='.$template_name.'&param_1='.$param_1;

                $response = SendNewTemplateCodeV1($url);

                if ($response && $response->getStatusCode() == 200) { // 200 OK

                    // $response_data = $response->getBody()->getContents();

                    // info($response_data);

                    //dd($response_data,json_decode($response_data,true));
                }


            }
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
