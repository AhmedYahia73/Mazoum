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
                'send_to_id'     => $user_event?->event?->user_id,
                'en_title'       => $user_event?->event?->title,
                'ar_title'       => $user_event?->event?->title,
                'en_description' => $user_event->name,
                'ar_description' => $user_event->name,
                'type'           => 'accept_event',
                'item_id'        => $user_event?->event?->id,
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

            $url_button = '?q=' . $user_event?->event?->lat . ',' . $user_event?->event?->long;

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

            $url_button = '?q=' . $user_event?->event?->lat . ',' . $user_event?->event?->long;

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
                'send_to_id'     => $user_event?->event?->user_id,
                'en_title'       => $user_event?->event?->title,
                'ar_title'       => $user_event?->event?->title,
                'en_description' => $user_event->name,
                'ar_description' => $user_event->name,
                'type'           => 'refuse_event',
                'item_id'        => $user_event?->event?->id,
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
              'send_to_id'     => $user_event?->event?->user_id,
              'en_title'       => $user_event?->event?->title,
              'ar_title'       => $user_event?->event?->title,
              'en_description' => $request->msg,
              'ar_description' => $request->msg,
              'type'           => 'event-msg',
              'item_id'        => $user_event?->event?->id,
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
              'send_to_id'     => $user_event?->event?->user_id,
              'en_title'       => $user_event?->event?->title,
              'ar_title'       => $user_event?->event?->title,
              'en_description' => $request->msg,
              'ar_description' => $request->msg,
              'type'           => 'event-msg',
              'item_id'        => $user_event?->event?->id,
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

        $name_qr      = $event->name_qr;
        $number_qr    = $event->number_qr;
        $qr_height    = $event->qr_height;
        $qr_width     = $event->qr_width;
        $qr_x         = $event->qr_x;
        $qr_y         = $event->qr_y;
        $image_height = $event->image_height;
        $image_width  = $event->image_width;
        $text_color   = $event->text_color ?: '#000';

        if ($event->getRawOriginal('image') != null && file_exists(public_path('images/' . $event->getRawOriginal('image')))) {

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
            } else {
                $font_path = public_path('font/LuxuriousRoman-Regular.ttf');
                $name      = $user_event->name;
                $name2     = 'Entered Users ' . $user_event->users_count;
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
            make_qr_transparent(public_path($qr_code_path));
            Image::make($bg)->insert($qr_code_path, 'left', 320, 0)->widen(450)->encode('png')->save($qr_code_path);

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

        return [$r, $g, $b];
    }


}


