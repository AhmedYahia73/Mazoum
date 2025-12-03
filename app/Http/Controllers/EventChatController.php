<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\EventUsers;
use App\Models\EventUserActions;

use App\Models\Orders;
use App\Models\Admin;
use App\Models\Setting;
use App\Models\Logs;
use App\Models\Qr_Code;
use App\Models\EventUserLogs;
use App\Models\Events;
use App\Models\EventMessages;
use App\Models\Parking;
use App\Models\CongratulationMessages;
use App\Models\Notifications;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManagerStatic as Image;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use GuzzleHttp\Client;
use App\Http\Controllers\Controller;

class EventChatController extends Controller
{



    public function event_chat() {


        if (session()->has('event_login')) {

            $session = session('event_login');
            $arr =explode('-',$session);

            $event_id      = $arr[0];
            $event_user_id = $arr[1];
            $mobile        = $arr[2];

            $event = Events::findOrFail($event_id);

            $actions = EventUserActions::where('event_id',$event_id)->where('event_user_id',$event_user_id)->where('mobile',$mobile)->get();
            $event_user = EventUsers::where('id',$event_user_id)->first();

            if($event_user) {

                $varibles = [
                    'event_id'      => $event_id,
                    'event'         => $event,
                    'event_user_id' => $event_user_id,
                    'mobile'        => $mobile,
                    'actions'       => $actions,
                    'event_user'    => $event_user
                ];

                return view('event.show',$varibles);

            } else {
                session()->forget('event_login');
                return redirect('event/login');
            }


        } else {
            return redirect('event/login');
        }

    }



    public function save_event_action(Request $request) {

        $request->validate([
            'event_user_id' => 'required',
          	'action'        => 'required',
            'msg'           => 'required_if:action,save_msg',
        ]);

        $setting = Setting::first();

      	$user_event = EventUsers::where('id',$request->event_user_id)->first();

      	if($user_event != null && $user_event->event) {

          	if($request->action != 'save_msg') {

            	EventUserActions::create([
                   'event_id' => $user_event->event_id,
                   'event_user_id' => $user_event->id,
                   'mobile' => $user_event->mobile,
                   'action' => $request->action,
                   'msg' => null
               ]);

               /////////////////////////////////////// Start Accept Event ///////////////////////////////////////

                if($request->action == 'accept_event') {

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

                    $user_event->update([ 'is_accepted' => 'yes' ,'confirmed_at' => now(),'status' => 'attend' ]);

                    if($event->showing_qr == 'yes') {

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

                        // $qr_code_path = 'qr_code/' . $image_name;
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

                        //     $new_img->text($user_event->mobile, 190, 680, function ($font) {
                        //     $font->file(public_path('font/OpenSans-Italic.ttf'));
                        //     $font->size(30);
                        //     $font->color('#000');
                        //     //$font->align('right'); // Adjust alignment if necessary
                        // });

                        // $new_img->save($destination);

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

                /////////////////////////////////////// End Accept Event ///////////////////////////////////////


                /////////////////////////////////////// Start Refuse Event ///////////////////////////////////////

                elseif($request->action == 'refuse_event') {

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

                /////////////////////////////////////// End Refuse Event ///////////////////////////////////////


                /////////////////////////////////////// Start Resend Qr ///////////////////////////////////////

                elseif($request->action == 'not_received_qr') {

                    EventUserActions::create([
                        'event_id' => $user_event->event_id,
                        'event_user_id' => $user_event->id,
                        'mobile' => $user_event->mobile,
                        'action' => 'resend_qr',
                        'msg' => null
                    ]);

                    $event = Events::find($user_event->event_id);

                    $user_event->update([ 'is_accepted' => 'yes'  ]);

                    if($event != null && $event->showing_qr == 'yes') {

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

                            $image_name = $uu_id . '-test-qr.png';

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

                /////////////////////////////////////// End Resend Qr ///////////////////////////////////////



                /////////////////////////////////////// Start Receive Congratulation ///////////////////////////////////////

                elseif($request->action == 'location_event') {

                    $user_event->update([ 'get_location' => 'yes' ]);

                }

                /////////////////////////////////////// End Receive Congratulation ///////////////////////////////////////


            } else {

                $check_receive_congratulation = EventUserActions::where('event_user_id',$user_event->id)->where('action','yes_receive_congratulation')->first();
                $check_receive_apology = EventUserActions::where('event_user_id',$user_event->id)->where('action','yes_receive_apology')->first();

                if(($check_receive_congratulation != null && $check_receive_apology == null) || ($check_receive_congratulation != null && $check_receive_apology != null && $check_receive_congratulation->id > $check_receive_apology->id)) {

                    EventUserActions::create([
                        'event_id' => $user_event->event_id,
                        'event_user_id' => $user_event->id,
                        'mobile' => $user_event->mobile,
                        'action' => 'yes_receive_congratulation',
                        'msg' => $request->msg,
                    ]);

                    CongratulationMessages::create([
                        'event_id' => $user_event != null ? $user_event->event_id : 0,
                        'event_user_id' => $user_event != null ? $user_event->id : 0,
                        'name' => $user_event != null ? $user_event->name : '',
                        'mobile' => $user_event->mobile,
                        'message' => $request->msg
                    ]);

                    Notifications::create([
                        'add_by'         => 'admin',
                        'user_id'        => 1,
                        'send_to_type'   => 'user',
                        'send_to_id'     => $user_event->event->user_id,
                        'en_title'       => 'new congratulation msg to event : ' . $user_event->event->title,
                        'ar_title'       => 'تهنئه جديده للدعوه   : ' . $user_event->event->title,
                        'en_description' => 'user : ' . $user_event->name . ' send congratulation message : ' . $request->msg,
                        'ar_description' => 'المستخدم : ' . $user_event->name . '  أرسل التهنئة  : ' . $request->msg,
                        'type'           => 'event-msg',
                        'item_id'        => $user_event->event->id,
                        'user_event_id'  => $user_event != null ? $user_event->id : 0,
                        'status'         => 'new_msg',
                    ]);

                }

                if(($check_receive_apology != null && $check_receive_congratulation == null) || ($check_receive_congratulation != null && $check_receive_apology != null && $check_receive_apology->id > $check_receive_congratulation->id)) {

                    EventUserActions::create([
                        'event_id' => $user_event->event_id,
                        'event_user_id' => $user_event->id,
                        'mobile' => $user_event->mobile,
                        'action' => 'yes_receive_apology',
                        'msg' => $request->msg,
                    ]);

                    EventMessages::create([
                        'event_id' => $user_event != null ? $user_event->event_id : 0,
                        'event_user_id' => $user_event != null ? $user_event->id : 0,
                        'name' => $user_event != null ? $user_event->name : '',
                        'mobile' => $user_event->mobile,
                        'message' => $request->msg
                    ]);

                    Notifications::create([
                        'add_by'         => 'admin',
                        'user_id'        => 1,
                        'send_to_type'   => 'user',
                        'send_to_id'     => $user_event->event->user_id,
                        'en_title'       => 'new apology msg to event : ' . $user_event->event->title,
                        'ar_title'       => 'اعتذار جديد للدعوه   : ' . $user_event->event->title,
                        'en_description' => 'user : ' . $user_event->name . ' send apology message : ' . $request->msg,
                        'ar_description' => 'المستخدم : ' . $user_event->name . '  أرسل الأعتذار  : ' . $request->msg,
                        'type'           => 'event-msg',
                        'item_id'        => $user_event->event->id,
                        'user_event_id'  => $user_event != null ? $user_event->id : 0,
                        'status'         => 'new_msg',
                      ]);

                }

            }

            return response()->json([
                'status'  => true,
                'message' => 'success',
                'data'    => null,
            ], 200);

        } else {
        	 return response()->json([
               	'status'  => false,
                'message' => 'Error',
                'data'    => null,
            ], 200);
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
            $background->insert($qr, 'top-left', $x, $y - 420);

            // احسب مركز الصورة للنص
            $center_x = intval($background->width() / 2);
            $text_y = $y + $qr->height() - 380; // أسفل QR بـ 20px

            // $Arabic = new \ArPHP\I18N\Arabic('Glyphs');
            // $name = $Arabic->utf8Glyphs($user_event->mobile);

            $name = $user_event->mobile;

            // أضف النص في وسط الصورة أفقيًا وأسفل QR
            $background->text($name, $center_x, $text_y, function ($font) {
                $font->file(public_path('font/OpenSans.ttf'));
                $font->size(26);
                $font->color('#000');
                $font->align('center');
                $font->valign('top');
            });


            // احسب مركز الصورة للنص
            $text_y2 = $y + $qr->height() - 40; // أسفل QR بـ 20px

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

            // $new_img->text($user_event->mobile, 190, 680, function ($font) {
            //   $font->file(public_path('font/OpenSans-Italic.ttf'));
            //   $font->size(30);
            //   $font->color('#000');
            //   //$font->align('right'); // Adjust alignment if necessary
            // });

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
