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
                        'is_accepted' => 'yes' ,
                        'confirmed_at' => now(),
                        'status' => 'attend',
                    ]);

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
                            //$link = asset('mobile-scan-qr/' . $uu_id);
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
                        'send_to_id'     => $user_event?->event?->user_id,
                        'en_title'       => 'new congratulation msg to event : ' . $user_event?->event?->title,
                        'ar_title'       => 'تهنئه جديده للدعوه   : ' . $user_event?->event?->title,
                        'en_description' => 'user : ' . $user_event->name . ' send congratulation message : ' . $request->msg,
                        'ar_description' => 'المستخدم : ' . $user_event->name . '  أرسل التهنئة  : ' . $request->msg,
                        'type'           => 'event-msg',
                        'item_id'        => $user_event?->event?->id,
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
                        'send_to_id'     => $user_event?->event?->user_id,
                        'en_title'       => 'new apology msg to event : ' . $user_event?->event?->title,
                        'ar_title'       => 'اعتذار جديد للدعوه   : ' . $user_event?->event?->title,
                        'en_description' => 'user : ' . $user_event->name . ' send apology message : ' . $request->msg,
                        'ar_description' => 'المستخدم : ' . $user_event->name . '  أرسل الأعتذار  : ' . $request->msg,
                        'type'           => 'event-msg',
                        'item_id'        => $user_event?->event?->id,
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

        $name_qr      = $event->name_qr;
        $number_qr    = $event->number_qr;
        $qr_height    = $event->qr_height;
        $qr_width     = $event->qr_width;
        $qr_x         = $event->qr_x;
        $qr_y         = $event->qr_y;
        $image_height = $event->image_height;
        $image_width  = $event->image_width;
        $text_color   = $event->text_color ?: '#000';

        if ($event->getRawOriginal('image') != null) {

            $image_name  = $uu_id . '-test-qr.png';
            $link        = asset('scan-qr/' . $uu_id);
            $qr_tmp_path = public_path('qr_code/tmp_' . $image_name);
            $final_path  = public_path('qr_code/' . $image_name);

            $qr_size = ($qr_width > 0 && $qr_height > 0) ? $qr_width : 300;

            generate_qr_png($link, $qr_tmp_path, $qr_size, $color);

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
                $font_path = base_path('resources/fonts/DroidArabicKufiRegular.ttf');
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

        }
        else {
            // ==========================================
            // 1. إعدادات المسارات
            // ==========================================
            $bg           = public_path('qr-image-v10.jpg');
            $link         = asset('scan-qr/' . $uu_id);
            $qr_tmp_name  = 'tmp_qr_' . time() . '.png';
            $qr_tmp_path  = public_path('qr_code/' . $qr_tmp_name);
            $final_path   = public_path('qr_code/' . $image_name);

            // ==========================================
            // 2. إعدادات الخطوط
            // ==========================================
            $arabic_font = public_path('font/DroidArabicKufiRegular.ttf');
            if (!file_exists($arabic_font)) {
                $arabic_font = base_path('resources/fonts/DroidArabicKufiRegular.ttf');
            }
            $number_font = public_path('font/timr45w.ttf');
            if (!file_exists($number_font)) {
                $number_font = $arabic_font;
            }

            // ==========================================
            // 3. إعدادات الأبعاد والإحداثيات
            // ==========================================
            $rr = 600;
            $qr_size        = 450;
            $y_title        = 580; 
            $y_tickets      = 900; 
            $x_left_ticket  = 600; 
            $x_right_ticket = 1430; 
            $y_mobile       = 1120;
            $y_datetime     = 1200; 
            $y_qr           = 1270; 

            // ==========================================
            // 4. إنشاء الباركود
            // ==========================================
            // إضافة fallback أمني لمصفوفة الألوان لتجنب أخطاء Missing Index
            QrCode::format('png')
                ->size($qr_size)
                ->color($color[0] ?? 0, $color[1] ?? 0, $color[2] ?? 0)
                ->backgroundColor(255, 255, 255, 0)
                ->margin(1)
                ->generate($link, $qr_tmp_path);

            // ==========================================
            // 5. دمج البيانات على الصورة
            // ==========================================
            $img = Image::make($bg);
            $center_x = intval($img->width() / 2);

            // أ- إضافة عنوان المناسبة (Event Title)
            // نعكس ترتيب الكلمات فقط - FreeType يربط الحروف تلقائياً
            if (!empty(trim($event->name ?? ''))) {
                $words = explode(' ', trim($event->name));
                $words_rev = array_reverse($words);
                $reversed_name = implode(' ', $words_rev);
                $Arabic = new \ArPHP\I18N\Arabic('Glyphs');
                $title_text = $Arabic->utf8Glyphs($reversed_name);

                $img->text($title_text, $center_x, $y_title, function ($font) use ($arabic_font) {
                    $font->file($arabic_font);
                    $font->size(90);
                    $font->color('#ffffff'); 
                    $font->align('center');
                    $font->valign('middle');
                });
            }

            // ب- إضافة رقم المقعد (في حالة أنه لا يساوي 0)
            if (!empty($user_event->suit_num) && $user_event->suit_num != 0) {
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
            if (!empty($user_event->mobile)) {
                $img->text($user_event->mobile, $center_x, $y_mobile, function ($font) use ($number_font) {
                    $font->file($number_font);
                    $font->size(90);
                    $font->color('#000000');
                    $font->align('center');
                    $font->valign('middle');
                });
            }

            // هـ- إضافة التاريخ والوقت
            if (!empty($event->date) && !empty($event->time)) {
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


}




